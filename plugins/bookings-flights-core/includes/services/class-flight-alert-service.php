<?php
/**
 * Local flight alert intent lifecycle service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Flight_Alert_Service {

	public const STATUS_REQUESTED    = 'requested';
	public const STATUS_ACTIVE       = 'active';
	public const STATUS_EMAIL_FAILED = 'email_failed';

	public const EMAIL_STATUS_PENDING = 'pending';
	public const EMAIL_STATUS_SENT    = 'sent';
	public const EMAIL_STATUS_FAILED  = 'failed';
	public const EMAIL_STATUS_SKIPPED = 'skipped';

	private const MAX_ALERTS_PER_EMAIL = 10;
	private const MAX_PROCESS_LIMIT    = 50;

	/**
	 * Save a local alert intent, updating an existing email/route intent when present.
	 *
	 * @param array $data Normalized alert data.
	 * @return array|\WP_Error
	 */
	public function save( array $data ): array|\WP_Error {
		if ( ! post_type_exists( Post_Type_Registrar::TRAVEL_ALERT ) ) {
			return new \WP_Error( 'baf_alert_unavailable', __( 'Alert records are unavailable.', 'bookings-flights-core' ) );
		}

		$email       = sanitize_email( (string) ( $data['email'] ?? '' ) );
		$origin      = $this->normalize_iata( (string) ( $data['origin'] ?? '' ) );
		$destination = $this->normalize_iata( (string) ( $data['destination'] ?? '' ) );

		if ( '' === $email || ! is_email( $email ) || '' === $origin || '' === $destination ) {
			return new \WP_Error( 'baf_alert_invalid', __( 'Alert email and route are required.', 'bookings-flights-core' ) );
		}

		$route_key   = $origin . '-' . $destination;
		$existing_id = $this->find_existing_alert_id( $email, $route_key );

		if ( $existing_id <= 0 && $this->count_active_alerts_for_email( $email ) >= self::MAX_ALERTS_PER_EMAIL ) {
			return new \WP_Error( 'baf_alert_limit_reached', __( 'This email has reached the active alert limit.', 'bookings-flights-core' ) );
		}

		$user_id  = absint( $data['user_id'] ?? 0 );
		$alert_id = $existing_id;
		$mode     = $existing_id > 0 ? 'updated' : 'created';

		if ( $alert_id <= 0 ) {
			$alert_id = wp_insert_post(
				array(
					'post_type'   => Post_Type_Registrar::TRAVEL_ALERT,
					'post_status' => 'private',
					'post_title'  => sprintf(
						/* translators: %s: route code pair. */
						__( 'Flight alert intent: %s', 'bookings-flights-core' ),
						$route_key
					),
					'post_author' => $user_id,
				),
				true
			);

			if ( is_wp_error( $alert_id ) || $alert_id <= 0 ) {
				return new \WP_Error( 'baf_alert_unavailable', __( 'Alert intent could not be saved.', 'bookings-flights-core' ) );
			}
		} else {
			wp_update_post(
				array(
					'ID'         => $alert_id,
					'post_title' => sprintf(
						/* translators: %s: route code pair. */
						__( 'Flight alert intent: %s', 'bookings-flights-core' ),
						$route_key
					),
				)
			);
		}

		$status       = $this->status_for_save( $alert_id );
		$email_status = self::STATUS_ACTIVE === $status ? self::EMAIL_STATUS_SENT : self::EMAIL_STATUS_PENDING;
		$now          = wp_date( DATE_ATOM );
		$meta         = array(
			'baf_origin_airport'              => $origin,
			'baf_destination_airport'         => $destination,
			'baf_departure_window'            => $this->normalize_date( (string) ( $data['depart_date'] ?? '' ) ),
			'baf_return_window'               => $this->normalize_date( (string) ( $data['return_date'] ?? '' ) ),
			'baf_alert_route'                 => $route_key,
			'baf_alert_frequency'             => $this->allowed_value( (string) ( $data['frequency'] ?? '' ), array( 'daily', 'weekly', 'monthly' ), 'weekly' ),
			'baf_alert_email'                 => $email,
			'baf_alert_user_id'               => (string) $user_id,
			'baf_alert_route_post_id'         => (string) absint( $data['route_id'] ?? 0 ),
			'baf_alert_travelers'             => (string) min( 9, max( 1, absint( $data['travelers'] ?? 1 ) ) ),
			'baf_alert_cabin'                 => $this->allowed_value( (string) ( $data['cabin'] ?? '' ), array( 'economy', 'premium_economy', 'business', 'first' ), 'economy' ),
			'baf_alert_surface'               => sanitize_key( (string) ( $data['surface'] ?? '' ) ),
			'baf_alert_source_url'            => esc_url_raw( (string) ( $data['source_url'] ?? '' ) ),
			'baf_alert_consent_at'            => (string) get_post_meta( $alert_id, 'baf_alert_consent_at', true ),
			'baf_alert_status'                => $status,
			'baf_alert_email_status'          => $email_status,
			'baf_alert_updated_at'            => $now,
		);

		if ( '' === $meta['baf_alert_consent_at'] ) {
			$meta['baf_alert_consent_at'] = $now;
		}

		$this->write_meta( $alert_id, $meta );

		return array(
			'id'     => $alert_id,
			'mode'   => $mode,
			'status' => $status,
		);
	}

	public function process_pending_alerts( int $limit = 20 ): array {
		$limit = min( self::MAX_PROCESS_LIMIT, max( 1, $limit ) );
		$ids   = $this->pending_alert_ids( $limit );

		$sent    = 0;
		$failed  = 0;
		$skipped = 0;

		foreach ( $ids as $alert_id ) {
			$result = $this->send_followup_email( (int) $alert_id );

			if ( true === $result ) {
				++$sent;
				continue;
			}

			if ( is_wp_error( $result ) && 'baf_alert_email_skipped' === $result->get_error_code() ) {
				++$skipped;
				continue;
			}

			++$failed;
		}

		$status = $failed > 0 ? 'deferred' : 'success';

		return array(
			'status'  => $status,
			'message' => $failed > 0 ? __( 'Travel alert queue processed with deferred email follow-up.', 'bookings-flights-core' ) : __( 'Travel alert queue processed.', 'bookings-flights-core' ),
			'data'    => array(
				'eligible_alerts' => (string) count( $ids ),
				'emails_sent'     => (string) $sent,
				'emails_failed'   => (string) $failed,
				'alerts_skipped'  => (string) $skipped,
			),
		);
	}

	public function delete_by_token( int $alert_id, string $token ): bool|\WP_Error {
		if ( ! $this->is_valid_delete_token( $alert_id, $token ) ) {
			return new \WP_Error( 'baf_alert_delete_invalid', __( 'Alert delete link is invalid.', 'bookings-flights-core' ) );
		}

		$deleted = wp_delete_post( $alert_id, true );

		return null !== $deleted ? true : new \WP_Error( 'baf_alert_delete_failed', __( 'Alert could not be deleted.', 'bookings-flights-core' ) );
	}

	public function is_valid_delete_token( int $alert_id, string $token ): bool {
		if ( $alert_id <= 0 || Post_Type_Registrar::TRAVEL_ALERT !== get_post_type( $alert_id ) ) {
			return false;
		}

		$expected = $this->delete_token( $alert_id );
		$token    = preg_replace( '/[^a-f0-9]/', '', strtolower( $token ) );
		$token    = is_string( $token ) ? $token : '';

		return '' !== $expected && '' !== $token && hash_equals( $expected, $token );
	}

	private function send_followup_email( int $alert_id ): bool|\WP_Error {
		if ( self::STATUS_REQUESTED !== (string) get_post_meta( $alert_id, 'baf_alert_status', true ) ) {
			return new \WP_Error( 'baf_alert_email_skipped', __( 'Alert is not pending email follow-up.', 'bookings-flights-core' ) );
		}

		$email       = sanitize_email( (string) get_post_meta( $alert_id, 'baf_alert_email', true ) );
		$route_key   = sanitize_text_field( (string) get_post_meta( $alert_id, 'baf_alert_route', true ) );
		$origin      = $this->normalize_iata( (string) get_post_meta( $alert_id, 'baf_origin_airport', true ) );
		$destination = $this->normalize_iata( (string) get_post_meta( $alert_id, 'baf_destination_airport', true ) );

		update_post_meta( $alert_id, 'baf_alert_email_last_attempt_at', wp_date( DATE_ATOM ) );

		if ( '' === $email || ! is_email( $email ) || '' === $route_key || '' === $origin || '' === $destination ) {
			update_post_meta( $alert_id, 'baf_alert_status', self::STATUS_EMAIL_FAILED );
			update_post_meta( $alert_id, 'baf_alert_email_status', self::EMAIL_STATUS_SKIPPED );

			return new \WP_Error( 'baf_alert_email_skipped', __( 'Alert is missing safe email or route data.', 'bookings-flights-core' ) );
		}

		$subject = sprintf(
			/* translators: %s: route code pair. */
			__( 'Your %s price alert intent is saved', 'bookings-flights-core' ),
			$route_key
		);
		$message = $this->email_message( $alert_id, $origin, $destination, $route_key );
		$sent    = wp_mail( $email, $subject, $message );

		if ( true !== $sent ) {
			update_post_meta( $alert_id, 'baf_alert_email_status', self::EMAIL_STATUS_FAILED );

			return new \WP_Error( 'baf_alert_email_failed', __( 'Alert follow-up email could not be sent.', 'bookings-flights-core' ) );
		}

		update_post_meta( $alert_id, 'baf_alert_status', self::STATUS_ACTIVE );
		update_post_meta( $alert_id, 'baf_alert_email_status', self::EMAIL_STATUS_SENT );
		update_post_meta( $alert_id, 'baf_alert_email_sent_at', wp_date( DATE_ATOM ) );

		return true;
	}

	private function email_message( int $alert_id, string $origin, string $destination, string $route_key ): string {
		$frequency = $this->frequency_label( (string) get_post_meta( $alert_id, 'baf_alert_frequency', true ) );
		$flight_url = add_query_arg(
			array(
				'origin'       => $origin,
				'destination'  => $destination,
				'travel_focus' => 'price_alert',
			),
			home_url( '/flights/' )
		);
		$delete_url = $this->delete_confirmation_url( $alert_id );

		return implode(
			"\n\n",
			array(
				sprintf(
					/* translators: %s: route code pair. */
					__( 'Your Bookings and Flights alert intent for %s is saved locally.', 'bookings-flights-core' ),
					$route_key
				),
				sprintf(
					/* translators: %s: alert frequency label. */
					__( 'Frequency preference: %s. This stores your local follow-up preference only; Travelpayouts or the partner provider still controls live fares, filters, booking, payment, changes, and support.', 'bookings-flights-core' ),
					$frequency
				),
				__( 'Before buying, open the provider search and confirm the current live price and booking details.', 'bookings-flights-core' ),
				sprintf(
					/* translators: %s: URL. */
					__( 'Open the flight search: %s', 'bookings-flights-core' ),
					esc_url_raw( $flight_url )
				),
				sprintf(
					/* translators: %s: URL. */
					__( 'Delete this local alert intent: %s', 'bookings-flights-core' ),
					esc_url_raw( $delete_url )
				),
			)
		);
	}

	public function delete_confirmation_url( int $alert_id ): string {
		return add_query_arg(
			array(
				'baf_alert_status' => 'confirm_delete',
				'baf_alert_id'     => $alert_id,
				'baf_alert_token'  => $this->delete_token( $alert_id ),
			),
			home_url( '/flights/' )
		);
	}

	private function delete_token( int $alert_id ): string {
		$email      = sanitize_email( (string) get_post_meta( $alert_id, 'baf_alert_email', true ) );
		$route_key  = sanitize_text_field( (string) get_post_meta( $alert_id, 'baf_alert_route', true ) );
		$consent_at = sanitize_text_field( (string) get_post_meta( $alert_id, 'baf_alert_consent_at', true ) );

		if ( '' === $email || '' === $route_key || '' === $consent_at ) {
			return '';
		}

		return hash_hmac( 'sha256', $alert_id . '|' . strtolower( $email ) . '|' . $route_key . '|' . $consent_at, wp_salt( 'auth' ) );
	}

	private function pending_alert_ids( int $limit ): array {
		$query = new \WP_Query(
			array(
				'post_type'              => Post_Type_Registrar::TRAVEL_ALERT,
				'post_status'            => array( 'private', 'publish' ),
				'posts_per_page'         => $limit,
				'fields'                 => 'ids',
				'orderby'                => 'date',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					array(
						'key'   => 'baf_alert_status',
						'value' => self::STATUS_REQUESTED,
					),
				),
			)
		);

		return array_map( 'absint', $query->posts );
	}

	private function find_existing_alert_id( string $email, string $route_key ): int {
		$query = new \WP_Query(
			array(
				'post_type'              => Post_Type_Registrar::TRAVEL_ALERT,
				'post_status'            => array( 'private', 'publish' ),
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					'relation' => 'AND',
					array(
						'key'   => 'baf_alert_email',
						'value' => $email,
					),
					array(
						'key'   => 'baf_alert_route',
						'value' => $route_key,
					),
					array(
						'key'     => 'baf_alert_status',
						'value'   => array( self::STATUS_REQUESTED, self::STATUS_ACTIVE, self::STATUS_EMAIL_FAILED ),
						'compare' => 'IN',
					),
				),
			)
		);

		return empty( $query->posts ) ? 0 : absint( $query->posts[0] );
	}

	private function count_active_alerts_for_email( string $email ): int {
		$query = new \WP_Query(
			array(
				'post_type'              => Post_Type_Registrar::TRAVEL_ALERT,
				'post_status'            => array( 'private', 'publish' ),
				'posts_per_page'         => self::MAX_ALERTS_PER_EMAIL + 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array(
					'relation' => 'AND',
					array(
						'key'   => 'baf_alert_email',
						'value' => $email,
					),
					array(
						'key'     => 'baf_alert_status',
						'value'   => array( self::STATUS_REQUESTED, self::STATUS_ACTIVE, self::STATUS_EMAIL_FAILED ),
						'compare' => 'IN',
					),
				),
			)
		);

		return count( $query->posts );
	}

	private function status_for_save( int $alert_id ): string {
		$status = sanitize_key( (string) get_post_meta( $alert_id, 'baf_alert_status', true ) );

		if ( self::STATUS_ACTIVE === $status ) {
			return self::STATUS_ACTIVE;
		}

		return self::STATUS_REQUESTED;
	}

	private function write_meta( int $alert_id, array $meta ): void {
		foreach ( $meta as $meta_key => $meta_value ) {
			if ( '' === $meta_value ) {
				delete_post_meta( $alert_id, $meta_key );
				continue;
			}

			update_post_meta( $alert_id, $meta_key, $meta_value );
		}
	}

	private function normalize_iata( string $value ): string {
		$value = preg_replace( '/[^A-Z]/', '', strtoupper( $value ) );
		$value = is_string( $value ) ? $value : '';

		return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
	}

	private function normalize_date( string $value ): string {
		$value = trim( $value );

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches ) ) {
			return '';
		}

		return wp_checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1], $value ) ? $value : '';
	}

	private function allowed_value( string $value, array $allowed, string $fallback ): string {
		$value = sanitize_key( $value );

		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	private function frequency_label( string $frequency ): string {
		$labels = array(
			'daily'   => __( 'Daily', 'bookings-flights-core' ),
			'weekly'  => __( 'Weekly', 'bookings-flights-core' ),
			'monthly' => __( 'Monthly', 'bookings-flights-core' ),
		);

		return $labels[ sanitize_key( $frequency ) ] ?? $labels['weekly'];
	}
}
