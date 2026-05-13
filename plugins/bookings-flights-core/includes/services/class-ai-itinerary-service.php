<?php
/**
 * AI itinerary generation service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\AI\Itinerary_Schema;
use BAF\Core\AI\Provider_Factory;
use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Repositories\AI_Session_Repository;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class AI_Itinerary_Service {

	private const PROMPT_VERSION = 'itinerary_v1';

	private AI_Session_Repository $sessions;

	public function __construct( ?AI_Session_Repository $sessions = null ) {
		$this->sessions = $sessions ?? new AI_Session_Repository();
	}

	public function generate( array $request ): array|\WP_Error {
		$normalized = $this->normalize_request( $request );
		$settings   = Settings_Manager::get_ai();

		if ( true === $normalized['save'] && ! current_user_can( Capability_Manager::EDIT_CONTENT ) ) {
			return $this->save_forbidden_error();
		}

		if ( 'live' === sanitize_key( $this->string_value( $settings['mode'] ?? '' ) ) && true !== $normalized['external_ai_consent'] ) {
			return new \WP_Error( 'baf_ai_request_consent_required', __( 'Confirm external AI consent before sending planner details to a live provider.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$provider = Provider_Factory::make();

		if ( is_wp_error( $provider ) ) {
			return $provider;
		}

		$session    = $this->sessions->start(
			array(
				'provider'       => $provider->provider_id(),
				'mode'           => $provider->mode(),
				'prompt_version' => self::PROMPT_VERSION,
				'source_post_id' => $normalized['source_post_id'],
				'request'        => $normalized,
			)
		);

		if ( is_wp_error( $session ) ) {
			return $session;
		}

		$output = $provider->generate_itinerary( $normalized );

		if ( is_wp_error( $output ) ) {
			$this->finish_error( (int) $session['id'], $output );

			return $output;
		}

		$itinerary = Itinerary_Schema::validate( $output );

		if ( is_wp_error( $itinerary ) ) {
			$this->finish_error( (int) $session['id'], $itinerary );

			return $itinerary;
		}

		$saved_post_id = 0;

		if ( true === $normalized['save'] ) {
			$saved_post_id = $this->save_draft_trip_plan( $itinerary, $normalized, (string) $session['run_uuid'] );

			if ( is_wp_error( $saved_post_id ) ) {
				$this->finish_error( (int) $session['id'], $saved_post_id );

				return $saved_post_id;
			}
		}

		$this->sessions->finish(
			(int) $session['id'],
			'success',
			array(
				'output'  => $itinerary,
				'summary' => $itinerary['title'],
			)
		);

		return array(
			'run_id'       => (string) $session['run_uuid'],
			'mode'         => $provider->mode(),
			'provider'     => $provider->provider_id(),
			'trip_brief'   => $this->trip_brief( $normalized, $provider->mode() ),
			'itinerary'    => $itinerary,
			'trip_plan_id' => $saved_post_id,
			'saved_status' => $saved_post_id > 0 ? 'draft' : '',
			'trip_plan'    => $this->trip_plan_response( $saved_post_id ),
		);
	}

	private function normalize_request( array $request ): array {
		return array(
			'destination'         => substr( sanitize_text_field( $this->string_value( $request['destination'] ?? '' ) ), 0, 120 ),
			'origin'              => substr( sanitize_text_field( $this->string_value( $request['origin'] ?? '' ) ), 0, 120 ),
			'prompt'              => substr( sanitize_textarea_field( $this->string_value( $request['prompt'] ?? '' ) ), 0, 1200 ),
			'departure_date'      => $this->normalize_date( $this->string_value( $request['departure_date'] ?? '' ) ),
			'return_date'         => $this->normalize_date( $this->string_value( $request['return_date'] ?? '' ) ),
			'days'                => max( 1, min( 21, absint( $this->string_value( $request['days'] ?? 3 ) ) ) ),
			'travelers'           => max( 1, min( 12, absint( $this->string_value( $request['travelers'] ?? 2 ) ) ) ),
			'travel_style'        => substr( sanitize_text_field( $this->string_value( $request['travel_style'] ?? '' ) ), 0, 80 ),
			'budget'              => substr( sanitize_text_field( $this->string_value( $request['budget'] ?? '' ) ), 0, 80 ),
			'preferences'         => substr( sanitize_textarea_field( $this->string_value( $request['preferences'] ?? '' ) ), 0, 1200 ),
			'source_post_id'      => absint( $this->string_value( $request['source_post_id'] ?? 0 ) ),
			'save'                => $this->boolean_value( $request['save'] ?? false ),
			'external_ai_consent' => $this->boolean_value( $request['external_ai_consent'] ?? false ),
		);
	}

	private function normalize_date( string $value ): string {
		$value = trim( sanitize_text_field( $value ) );

		if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return '';
		}

		$parts = array_map( 'absint', explode( '-', $value ) );

		return 3 === count( $parts ) && checkdate( $parts[1], $parts[2], $parts[0] ) ? $value : '';
	}

	private function trip_brief( array $request, string $mode ): array {
		return array(
			'destination'        => $request['destination'],
			'origin'             => $request['origin'],
			'departure_date'     => $request['departure_date'],
			'return_date'        => $request['return_date'],
			'days'               => $request['days'],
			'travelers'          => $request['travelers'],
			'travel_style'       => $request['travel_style'],
			'budget'             => $request['budget'],
			'mode'               => sanitize_key( $mode ),
			'external_data_sent' => 'live' === sanitize_key( $mode ),
		);
	}

	private function save_draft_trip_plan( array $itinerary, array $request, string $run_uuid ): int|\WP_Error {
		if ( ! current_user_can( Capability_Manager::EDIT_CONTENT ) ) {
			return $this->save_forbidden_error();
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => Post_Type_Registrar::TRIP_PLAN,
				'post_status'  => 'draft',
				'post_title'   => $itinerary['title'],
				'post_content' => $this->draft_content( $itinerary ),
				'post_excerpt' => $itinerary['summary'],
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error( 'baf_ai_save_failed', __( 'The itinerary was generated but could not be saved as a draft trip plan.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		update_post_meta( $post_id, 'baf_destination', $request['destination'] );
		update_post_meta( $post_id, 'baf_origin', $request['origin'] );
		update_post_meta( $post_id, 'baf_departure_window', $request['departure_date'] );
		update_post_meta( $post_id, 'baf_return_window', $request['return_date'] );
		update_post_meta( $post_id, 'baf_travel_style', $request['travel_style'] );
		update_post_meta( $post_id, 'baf_ai_source_session_id', $run_uuid );
		update_post_meta( $post_id, 'baf_ai_opportunity_schema', $itinerary['opportunity_schema'] );
		update_post_meta( $post_id, 'baf_itinerary_json', wp_json_encode( $itinerary ) );

		return (int) $post_id;
	}

	private function draft_content( array $itinerary ): string {
		$blocks = array();

		if ( '' !== (string) $itinerary['summary'] ) {
			$blocks[] = '<p>' . esc_html( (string) $itinerary['summary'] ) . '</p>';
		}

		foreach ( $itinerary['days'] as $day ) {
			$heading = sprintf(
				/* translators: 1: itinerary day number, 2: itinerary day title. */
				__( 'Day %1$d: %2$s', 'bookings-flights-core' ),
				absint( $day['day'] ?? 0 ),
				(string) ( $day['title'] ?? __( 'Untitled day', 'bookings-flights-core' ) )
			);

			$blocks[] = '<h2>' . esc_html( $heading ) . '</h2>';

			if ( '' !== (string) ( $day['summary'] ?? '' ) ) {
				$blocks[] = '<p>' . esc_html( (string) $day['summary'] ) . '</p>';
			}

			$items = array();

			foreach ( $day['activities'] ?? array() as $activity ) {
				$parts = array_filter(
					array(
						ucfirst( str_replace( '_', ' ', sanitize_key( (string) ( $activity['time_of_day'] ?? 'flexible' ) ) ) ),
						(string) ( $activity['title'] ?? '' ),
						(string) ( $activity['location'] ?? '' ),
						(string) ( $activity['description'] ?? '' ),
					)
				);

				if ( empty( $parts ) ) {
					continue;
				}

				$items[] = '<li>' . esc_html( implode( ' - ', $parts ) ) . '</li>';
			}

			if ( ! empty( $items ) ) {
				$blocks[] = '<ul>' . implode( '', $items ) . '</ul>';
			}
		}

		if ( ! empty( $itinerary['affiliate_opportunities'] ) ) {
			$blocks[] = '<h2>' . esc_html__( 'Recommendation-only handoffs', 'bookings-flights-core' ) . '</h2>';
			$items    = array();

			foreach ( $itinerary['affiliate_opportunities'] as $opportunity ) {
				$items[] = '<li>' . esc_html(
					sprintf(
						'%1$s - %2$s - %3$s - SubID: %4$s - %5$s',
						(string) $opportunity['label'],
						(string) $opportunity['vertical'],
						(string) $opportunity['status'],
						(string) $opportunity['suggested_subid'],
						(string) $opportunity['limitations']
					)
				) . '</li>';
			}

			$blocks[] = '<ul>' . implode( '', $items ) . '</ul>';
		}

		if ( '' !== (string) $itinerary['booking_notes'] ) {
			$blocks[] = '<h2>' . esc_html__( 'Review notes', 'bookings-flights-core' ) . '</h2>';
			$blocks[] = '<p>' . esc_html( (string) $itinerary['booking_notes'] ) . '</p>';
		}

		if ( '' !== (string) $itinerary['disclaimer'] ) {
			$blocks[] = '<p><strong>' . esc_html__( 'Disclosure:', 'bookings-flights-core' ) . '</strong> ' . esc_html( (string) $itinerary['disclaimer'] ) . '</p>';
		}

		return wp_kses_post( implode( "\n\n", $blocks ) );
	}

	private function trip_plan_response( int $post_id ): array {
		if ( $post_id <= 0 ) {
			return array();
		}

		$edit_url = current_user_can( 'edit_post', $post_id ) ? get_edit_post_link( $post_id, 'raw' ) : '';

		return array(
			'id'       => $post_id,
			'status'   => 'draft',
			'edit_url' => is_string( $edit_url ) ? esc_url_raw( $edit_url ) : '',
		);
	}

	private function save_forbidden_error(): \WP_Error {
		return new \WP_Error( 'baf_ai_save_forbidden', __( 'You are not allowed to save AI-generated trip plans.', 'bookings-flights-core' ), array( 'status' => rest_authorization_required_code() ) );
	}

	private function boolean_value( mixed $value ): bool {
		if ( is_bool( $value ) || is_scalar( $value ) ) {
			return rest_sanitize_boolean( $value );
		}

		return false;
	}

	private function string_value( mixed $value ): string {
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( is_int( $value ) || is_float( $value ) || is_bool( $value ) ) {
			return (string) $value;
		}

		if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			return (string) $value;
		}

		return '';
	}

	private function finish_error( int $session_id, \WP_Error $error ): void {
		$this->sessions->finish(
			$session_id,
			'failed',
			array(
				'error_code'    => $error->get_error_code(),
				'error_message' => $error->get_error_message(),
			)
		);
	}
}
