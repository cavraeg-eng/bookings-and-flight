<?php
/**
 * Member-owned saved trip intent service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Saved_Trip_Service {

	public const META_CONTEXT   = 'baf_saved_trip_context';
	public const META_SAVED_AT  = 'baf_saved_trip_saved_at';
	public const META_STATUS    = 'baf_saved_trip_status';
	public const META_TRAVELERS = 'baf_saved_trip_travelers';
	public const META_USER_ID   = 'baf_saved_trip_user_id';

	private const SCHEMA_VERSION = 'saved_trip_intent_v1';
	private const MAX_PER_PAGE   = 20;
	private const PLACEMENTS     = array( 'flights_white_label_search', 'hotels_partner_search' );
	private const TRAVEL_STYLES  = array( 'balanced', 'food_culture', 'family', 'budget', 'luxury', 'outdoors' );

	private Travelpayouts_Widget_Registry_Service $placements;

	public function __construct( ?Travelpayouts_Widget_Registry_Service $placements = null ) {
		$this->placements = $placements ?? new Travelpayouts_Widget_Registry_Service();
	}

	public function list_for_current_user( int $page = 1, int $per_page = 10 ): array|\WP_Error {
		$user_id = $this->require_member();

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$page     = max( 1, $page );
		$per_page = min( self::MAX_PER_PAGE, max( 1, $per_page ) );
		$query    = new \WP_Query(
			array(
				'author'         => $user_id,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => self::META_STATUS,
						'value' => 'active',
					),
				),
				'order'          => 'DESC',
				'orderby'        => 'modified',
				'paged'          => $page,
				'post_status'    => array( 'private', 'draft' ),
				'post_type'      => Post_Type_Registrar::TRIP_PLAN,
				'posts_per_page' => $per_page,
			)
		);

		$items = array();

		foreach ( $query->posts as $post_id ) {
			$item = $this->response_for_post( (int) $post_id );

			if ( is_array( $item ) ) {
				$items[] = $item;
			}
		}

		return array(
			'items'       => $items,
			'page'        => $page,
			'per_page'    => $per_page,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
		);
	}

	public function get_for_current_user( int $post_id ): array|\WP_Error {
		$guard = $this->require_owned_trip( $post_id );

		if ( is_wp_error( $guard ) ) {
			return $guard;
		}

		$item = $this->response_for_post( $post_id );

		return is_array( $item ) ? $item : $this->not_found_error();
	}

	public function save_for_current_user( array $request, int $post_id = 0 ): array|\WP_Error {
		$user_id = $this->require_member();

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$normalized = $this->normalize_request( $request );

		if ( is_wp_error( $normalized ) ) {
			return $normalized;
		}

		$existing = null;

		if ( $post_id > 0 ) {
			$guard = $this->require_owned_trip( $post_id );

			if ( is_wp_error( $guard ) ) {
				return $guard;
			}

			$existing = get_post( $post_id );
		}

		$post_data = array(
			'post_author'  => $user_id,
			'post_content' => $normalized['note'],
			'post_excerpt' => $normalized['summary'],
			'post_status'  => 'private',
			'post_title'   => $normalized['title'],
			'post_type'    => Post_Type_Registrar::TRIP_PLAN,
		);

		if ( $existing instanceof \WP_Post ) {
			$post_data['ID'] = $existing->ID;
			$saved_post_id   = wp_update_post( $post_data, true );
		} else {
			$saved_post_id = wp_insert_post( $post_data, true );
		}

		if ( is_wp_error( $saved_post_id ) || (int) $saved_post_id <= 0 ) {
			return new \WP_Error( 'baf_saved_trip_save_failed', __( 'The saved trip intent could not be stored.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		$this->update_meta( (int) $saved_post_id, $normalized, $user_id );

		return $this->get_for_current_user( (int) $saved_post_id );
	}

	public function update_for_current_user( array $request, int $post_id ): array|\WP_Error {
		$guard = $this->require_owned_trip( $post_id );

		if ( is_wp_error( $guard ) ) {
			return $guard;
		}

		$current = $this->response_for_post( $post_id );

		if ( ! is_array( $current ) ) {
			return $this->not_found_error();
		}

		$context = is_array( $current['context'] ?? null ) ? $current['context'] : array();
		$merged  = array(
			'departure_date'        => $current['departure_date'] ?? '',
			'destination'           => $current['destination'] ?? '',
			'local_storage_consent' => $request['local_storage_consent'] ?? false,
			'note'                  => $current['note'] ?? '',
			'origin'                => $current['origin'] ?? '',
			'placement_key'         => $context['placement_key'] ?? 'flights_white_label_search',
			'return_date'           => $current['return_date'] ?? '',
			'travel_style'          => $current['travel_style'] ?? 'balanced',
			'travelers'             => $current['travelers'] ?? 2,
		);

		foreach ( $merged as $key => $value ) {
			if ( 'local_storage_consent' !== $key && array_key_exists( $key, $request ) ) {
				$merged[ $key ] = $request[ $key ];
			}
		}

		return $this->save_for_current_user( $merged, $post_id );
	}

	public function delete_for_current_user( int $post_id ): array|\WP_Error {
		$guard = $this->require_owned_trip( $post_id );

		if ( is_wp_error( $guard ) ) {
			return $guard;
		}

		$deleted = wp_delete_post( $post_id, true );

		if ( ! $deleted instanceof \WP_Post ) {
			return new \WP_Error( 'baf_saved_trip_delete_failed', __( 'The saved trip intent could not be deleted.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		return array(
			'deleted' => true,
			'id'      => $post_id,
		);
	}

	public function resume_payload_for_current_user( int $post_id ): array {
		$item = $this->get_for_current_user( $post_id );

		if ( is_wp_error( $item ) ) {
			return array();
		}

		return array(
			'id'             => $item['id'],
			'destination'    => $item['destination'],
			'origin'         => $item['origin'],
			'departure_date' => $item['departure_date'],
			'return_date'    => $item['return_date'],
			'travelers'      => $item['travelers'],
			'travel_style'   => $item['travel_style'],
			'note'           => $item['note'],
			'planner_prompt' => $this->planner_prompt( $item ),
		);
	}

	private function normalize_request( array $request ): array|\WP_Error {
		$destination = substr( sanitize_text_field( $this->string_value( $request['destination'] ?? '' ) ), 0, 120 );

		if ( '' === $destination ) {
			return new \WP_Error( 'baf_saved_trip_destination_required', __( 'Add a destination before saving this trip intent.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		if ( true !== $this->boolean_value( $request['local_storage_consent'] ?? false ) ) {
			return new \WP_Error( 'baf_saved_trip_consent_required', __( 'Confirm local saved-trip storage before saving this trip intent.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$origin       = substr( sanitize_text_field( $this->string_value( $request['origin'] ?? '' ) ), 0, 120 );
		$depart_date  = $this->normalize_date( $this->string_value( $request['departure_date'] ?? '' ) );
		$return_date  = $this->normalize_date( $this->string_value( $request['return_date'] ?? '' ) );
		$travelers    = min( 12, max( 1, absint( $this->string_value( $request['travelers'] ?? 2 ) ) ) );
		$travel_style = $this->allowed_value( $this->string_value( $request['travel_style'] ?? 'balanced' ), self::TRAVEL_STYLES, 'balanced' );
		$note         = substr( sanitize_textarea_field( $this->string_value( $request['note'] ?? '' ) ), 0, 500 );
		$placement    = $this->allowed_value( $this->string_value( $request['placement_key'] ?? 'flights_white_label_search' ), self::PLACEMENTS, 'flights_white_label_search' );

		if ( '' !== $depart_date && '' !== $return_date && strtotime( $return_date ) < strtotime( $depart_date ) ) {
			return new \WP_Error( 'baf_saved_trip_dates_invalid', __( 'The return date must be after the departure date.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$context = $this->placement_context( $placement, $destination );

		if ( is_wp_error( $context ) ) {
			return $context;
		}

		$title = sprintf(
			/* translators: %s: destination name. */
			__( 'Saved trip: %s', 'bookings-flights-core' ),
			$destination
		);

		$summary = sprintf(
			/* translators: 1: destination, 2: traveler count. */
			_n( 'Local trip intent for %1$s with %2$d traveler.', 'Local trip intent for %1$s with %2$d travelers.', $travelers, 'bookings-flights-core' ),
			$destination,
			$travelers
		);

		return array(
			'context'        => $context,
			'departure_date' => $depart_date,
			'destination'    => $destination,
			'note'           => $note,
			'origin'         => $origin,
			'return_date'    => $return_date,
			'summary'        => $summary,
			'title'          => $title,
			'travel_style'   => $travel_style,
			'travelers'      => $travelers,
		);
	}

	private function placement_context( string $placement_key, string $destination ): array|\WP_Error {
		$placement = $this->placements->get( $placement_key, false );

		if ( is_wp_error( $placement ) ) {
			return new \WP_Error( 'baf_saved_trip_placement_invalid', __( 'Choose an approved local travel handoff path before saving.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$subid = Travelpayouts_Widget_Subid_Service::build(
			$placement,
			array(
				'placement' => $placement_key,
				'slug'      => sanitize_title( $destination ),
				'surface'   => 'saved_trips',
			)
		);

		return array(
			'blocked_actions'  => array( 'book', 'pay', 'claim_price_or_availability', 'store_partner_booking', 'execute_provider_search' ),
			'configured'       => (bool) ( $placement['configured'] ?? false ),
			'placement_key'    => sanitize_key( (string) ( $placement['key'] ?? $placement_key ) ),
			'placement_name'   => sanitize_text_field( (string) ( $placement['name'] ?? '' ) ),
			'provider_action'  => 'not_executed',
			'schema_version'   => self::SCHEMA_VERSION,
			'source_surface'   => 'saved_trips',
			'suggested_subid'  => sanitize_key( $subid ),
			'vertical'         => sanitize_key( (string) ( $placement['vertical'] ?? 'travel' ) ),
			'widget_family'    => sanitize_key( (string) ( $placement['widget_family'] ?? 'partner_link_card' ) ),
		);
	}

	private function update_meta( int $post_id, array $normalized, int $user_id ): void {
		update_post_meta( $post_id, 'baf_destination', $normalized['destination'] );
		update_post_meta( $post_id, 'baf_origin', $normalized['origin'] );
		update_post_meta( $post_id, 'baf_departure_window', $normalized['departure_date'] );
		update_post_meta( $post_id, 'baf_return_window', $normalized['return_date'] );
		update_post_meta( $post_id, 'baf_travel_style', $normalized['travel_style'] );
		update_post_meta( $post_id, self::META_CONTEXT, wp_json_encode( $normalized['context'] ) );
		update_post_meta( $post_id, self::META_SAVED_AT, wp_date( DATE_ATOM ) );
		update_post_meta( $post_id, self::META_STATUS, 'active' );
		update_post_meta( $post_id, self::META_TRAVELERS, (string) $normalized['travelers'] );
		update_post_meta( $post_id, self::META_USER_ID, (string) $user_id );
	}

	private function response_for_post( int $post_id ): ?array {
		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || Post_Type_Registrar::TRIP_PLAN !== $post->post_type ) {
			return null;
		}

		$destination = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
		$origin      = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_origin', true ) );
		$depart_date = $this->normalize_date( (string) get_post_meta( $post_id, 'baf_departure_window', true ) );
		$return_date = $this->normalize_date( (string) get_post_meta( $post_id, 'baf_return_window', true ) );
		$style       = $this->allowed_value( (string) get_post_meta( $post_id, 'baf_travel_style', true ), self::TRAVEL_STYLES, 'balanced' );
		$travelers   = min( 12, max( 1, absint( get_post_meta( $post_id, self::META_TRAVELERS, true ) ) ) );
		$context     = $this->stored_context( $post_id );

		return array(
			'id'             => $post_id,
			'title'          => sanitize_text_field( $post->post_title ),
			'destination'    => $destination,
			'origin'         => $origin,
			'departure_date' => $depart_date,
			'return_date'    => $return_date,
			'travelers'      => $travelers,
			'travel_style'   => $style,
			'note'           => sanitize_textarea_field( $post->post_content ),
			'context'        => $context,
			'links'          => $this->resume_links( $post_id, $origin, $destination, $depart_date, $return_date ),
			'modified_at'    => mysql2date( DATE_ATOM, $post->post_modified_gmt, false ),
			'saved_at'       => sanitize_text_field( (string) get_post_meta( $post_id, self::META_SAVED_AT, true ) ),
		);
	}

	private function stored_context( int $post_id ): array {
		$stored = get_post_meta( $post_id, self::META_CONTEXT, true );
		$decoded = json_decode( (string) $stored, true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return array(
			'blocked_actions' => array_values( array_filter( (array) ( $decoded['blocked_actions'] ?? array() ), 'is_string' ) ),
			'configured'      => (bool) ( $decoded['configured'] ?? false ),
			'placement_key'   => sanitize_key( (string) ( $decoded['placement_key'] ?? '' ) ),
			'placement_name'  => sanitize_text_field( (string) ( $decoded['placement_name'] ?? '' ) ),
			'provider_action' => sanitize_key( (string) ( $decoded['provider_action'] ?? 'not_executed' ) ),
			'schema_version'  => sanitize_key( (string) ( $decoded['schema_version'] ?? '' ) ),
			'source_surface'  => sanitize_key( (string) ( $decoded['source_surface'] ?? '' ) ),
			'suggested_subid' => sanitize_key( (string) ( $decoded['suggested_subid'] ?? '' ) ),
			'vertical'        => sanitize_key( (string) ( $decoded['vertical'] ?? '' ) ),
			'widget_family'   => sanitize_key( (string) ( $decoded['widget_family'] ?? '' ) ),
		);
	}

	private function resume_links( int $post_id, string $origin, string $destination, string $depart_date, string $return_date ): array {
		return array(
			'flights' => esc_url_raw(
				add_query_arg(
					array_filter(
						array(
							'baf_surface' => 'saved_trips',
							'depart_date' => $depart_date,
							'destination' => $destination,
							'origin'      => $origin,
							'return_date' => $return_date,
						)
					),
					home_url( '/flights/' )
				)
			),
			'hotels'  => esc_url_raw(
				add_query_arg(
					array_filter(
						array(
							'baf_surface'        => 'saved_trips',
							'check_in'           => $depart_date,
							'check_out'          => $return_date,
							'travel_destination' => $destination,
						)
					),
					home_url( '/hotels/' )
				)
			),
			'planner' => esc_url_raw( add_query_arg( 'resume_trip', $post_id, home_url( '/trip-planner/' ) ) ),
		);
	}

	private function require_owned_trip( int $post_id ): true|\WP_Error {
		$user_id = $this->require_member();

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || Post_Type_Registrar::TRIP_PLAN !== $post->post_type || (int) $post->post_author !== $user_id ) {
			return $this->not_found_error();
		}

		if ( 'active' !== (string) get_post_meta( $post_id, self::META_STATUS, true ) ) {
			return $this->not_found_error();
		}

		return true;
	}

	private function require_member(): int|\WP_Error {
		if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
			return new \WP_Error(
				'baf_saved_trip_login_required',
				__( 'Sign in before saving trip intent locally.', 'bookings-flights-core' ),
				array(
					'login_url' => wp_login_url( home_url( '/saved-trips/' ) ),
					'status'    => rest_authorization_required_code(),
				)
			);
		}

		return get_current_user_id();
	}

	private function not_found_error(): \WP_Error {
		return new \WP_Error( 'baf_saved_trip_not_found', __( 'Saved trip intent was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
	}

	private function normalize_date( string $value ): string {
		$value = trim( sanitize_text_field( $value ) );

		if ( 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return '';
		}

		$parts = array_map( 'absint', explode( '-', $value ) );

		return 3 === count( $parts ) && checkdate( $parts[1], $parts[2], $parts[0] ) ? $value : '';
	}

	private function allowed_value( string $value, array $allowed, string $fallback ): string {
		$value = sanitize_key( $value );

		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	private function planner_prompt( array $item ): string {
		$parts = array_filter(
			array(
				'Continue planning a trip to ' . (string) ( $item['destination'] ?? '' ),
				! empty( $item['origin'] ) ? 'from ' . (string) $item['origin'] : '',
				! empty( $item['departure_date'] ) ? 'departing ' . (string) $item['departure_date'] : '',
				! empty( $item['return_date'] ) ? 'returning ' . (string) $item['return_date'] : '',
			)
		);

		return sanitize_text_field( implode( ' ', $parts ) );
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
}
