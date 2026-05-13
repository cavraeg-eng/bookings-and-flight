<?php
/**
 * Travelpayouts widget placement registry service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Capabilities\Capability_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Widget_Registry_Service {

	public const OPTION_NAME = 'baf_travelpayouts_widget_registry';

	private const SCHEMA_VERSION = Travelpayouts_Widget_Starter_Placements::SCHEMA_VERSION;

	private const DEFAULT_SUBID_PATTERN = '{channel}_{surface}_{vertical}_{slug}_{placement}';

	private const VERTICALS = array( 'flights', 'hotels', 'cars', 'activities', 'packages', 'route', 'destination', 'deal', 'ai', 'saved_trip' );

	private const WIDGET_FAMILIES = array( 'white_label_search', 'white_label_results', 'flight_form', 'popular_routes', 'low_price_calendar', 'route_map', 'hotel_search', 'hotel_map', 'partner_link_card' );

	private const RENDER_MODES = array( 'official_shortcode', 'dashboard_script', 'iframe', 'handoff_link', 'disabled' );

	private const EMBED_SOURCES = array( 'official_plugin', 'travelpayouts_dashboard', 'travelpayouts_white_label', 'partner_program', 'none' );

	private const STATUSES = array( 'draft', 'active', 'disabled', 'archived' );

	public static function maybe_install(): void {
		$stored = get_option( self::OPTION_NAME, false );

		if ( false === $stored ) {
			add_option( self::OPTION_NAME, self::default_registry(), '', false );
			return;
		}

		$normalized = self::sanitize_registry( $stored, true );
		$normalized = Travelpayouts_Widget_Starter_Placements::migrate_registry( $normalized, $stored );

		if ( $normalized !== $stored ) {
			update_option( self::OPTION_NAME, $normalized, false );
		}
	}

	public function all( bool $include_private = false ): array|\WP_Error {
		$placements = self::get_registry()['placements'];

		if ( true === $include_private ) {
			if ( ! self::current_user_can_manage() ) {
				return new \WP_Error( 'baf_widget_registry_forbidden', __( 'You are not allowed to view private Travelpayouts widget placement data.', 'bookings-flights-core' ), array( 'status' => 403 ) );
			}

			return $placements;
		}

		return array_map( array( self::class, 'public_placement' ), $placements );
	}

	public function get( string $key, bool $include_private = false ): array|\WP_Error {
		$key        = sanitize_key( $key );
		$placements = self::get_registry()['placements'];

		if ( '' === $key || ! isset( $placements[ $key ] ) ) {
			return new \WP_Error( 'baf_widget_placement_not_found', __( 'Travelpayouts widget placement was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		if ( true === $include_private ) {
			if ( ! self::current_user_can_manage() ) {
				return new \WP_Error( 'baf_widget_registry_forbidden', __( 'You are not allowed to view private Travelpayouts widget placement data.', 'bookings-flights-core' ), array( 'status' => 403 ) );
			}

			return $placements[ $key ];
		}

		return self::public_placement( $placements[ $key ] );
	}

	public function get_for_rendering( string $key ): array|\WP_Error {
		$key        = sanitize_key( $key );
		$placements = self::get_registry()['placements'];

		if ( '' === $key || ! isset( $placements[ $key ] ) ) {
			return new \WP_Error( 'baf_widget_placement_not_found', __( 'Travelpayouts widget placement was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		if ( 'active' !== $placements[ $key ]['status'] || ! self::embed_is_configured( (array) $placements[ $key ]['embed'] ) ) {
			return new \WP_Error( 'baf_widget_placement_not_renderable', __( 'Travelpayouts widget placement is not configured for rendering.', 'bookings-flights-core' ), array( 'status' => 409 ) );
		}

		return $placements[ $key ];
	}

	public function save_placement( array $placement ): array|\WP_Error {
		if ( ! self::current_user_can_manage() ) {
			return new \WP_Error( 'baf_widget_registry_forbidden', __( 'You are not allowed to manage Travelpayouts widget placements.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$registry  = self::get_registry();
		$key       = sanitize_key( (string) ( $placement['key'] ?? '' ) );
		$existing  = '' !== $key && isset( $registry['placements'][ $key ] ) ? $registry['placements'][ $key ] : array();
		$sanitized = self::sanitize_placement( $placement, $existing );

		if ( is_wp_error( $sanitized ) ) {
			return $sanitized;
		}

		$registry['placements'][ $sanitized['key'] ] = $sanitized;
		$registry['updated_at']                      = self::timestamp();

		update_option( self::OPTION_NAME, $registry, false );

		do_action( 'baf_travelpayouts_widget_registry_saved', $sanitized['key'], $sanitized );

		return $sanitized;
	}

	public function delete_placement( string $key ): bool|\WP_Error {
		if ( ! self::current_user_can_manage() ) {
			return new \WP_Error( 'baf_widget_registry_forbidden', __( 'You are not allowed to manage Travelpayouts widget placements.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$key      = sanitize_key( $key );
		$registry = self::get_registry();

		if ( '' === $key || ! isset( $registry['placements'][ $key ] ) ) {
			return new \WP_Error( 'baf_widget_placement_not_found', __( 'Travelpayouts widget placement was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		unset( $registry['placements'][ $key ] );
		$registry['updated_at'] = self::timestamp();

		update_option( self::OPTION_NAME, $registry, false );

		do_action( 'baf_travelpayouts_widget_registry_deleted', $key );

		return true;
	}

	public static function sanitize_registry( mixed $value, bool $preserve_invalid = false ): array {
		$value      = is_array( $value ) ? $value : array();
		$placements = array();

		foreach ( (array) ( $value['placements'] ?? array() ) as $placement_key => $placement ) {
			if ( ! is_array( $placement ) ) {
				if ( true === $preserve_invalid ) {
					$placements[ $placement_key ] = $placement;
				}

				continue;
			}

			$sanitized = self::sanitize_placement( $placement, array(), false );

			if ( is_wp_error( $sanitized ) ) {
				if ( true === $preserve_invalid ) {
					$placements[ $placement_key ] = $placement;
				}

				continue;
			}

			$placements[ $sanitized['key'] ] = $sanitized;
		}

		return array(
			'schema_version' => self::SCHEMA_VERSION,
			'placements'     => $placements,
			'updated_at'     => sanitize_text_field( (string) ( $value['updated_at'] ?? self::timestamp() ) ),
		);
	}

	public static function sanitize_placement( array $placement, array $existing = array(), bool $touch_updated_at = true ): array|\WP_Error {
		$key = sanitize_key( (string) ( $placement['key'] ?? $existing['key'] ?? '' ) );

		if ( '' === $key ) {
			return new \WP_Error( 'baf_widget_placement_key_required', __( 'Travelpayouts widget placement key is required.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$name = sanitize_text_field( (string) ( $placement['name'] ?? $existing['name'] ?? '' ) );

		if ( '' === $name ) {
			return new \WP_Error( 'baf_widget_placement_name_required', __( 'Travelpayouts widget placement name is required.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$render_mode = self::sanitize_choice( (string) ( $placement['render_mode'] ?? $existing['render_mode'] ?? '' ), self::RENDER_MODES, 'disabled' );
		$embed_input = (array) ( $placement['embed'] ?? $existing['embed'] ?? array() );

		if ( empty( $embed_input['mode'] ) ) {
			$embed_input['mode'] = $render_mode;
		}

		$embed  = self::sanitize_embed( $embed_input );
		$status = self::sanitize_choice( (string) ( $placement['status'] ?? $existing['status'] ?? 'draft' ), self::STATUSES, 'draft' );

		if ( 'active' === $status && 'official_shortcode' === (string) ( $embed['mode'] ?? '' ) && ! self::embed_is_configured( $embed ) ) {
			$status = 'draft';
		}

		if ( 'active' === $status && ! self::embed_is_configured( $embed ) ) {
			return new \WP_Error( 'baf_widget_placement_embed_required', __( 'Active Travelpayouts widget placements require an approved embed reference or URL.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		$now = self::timestamp();
		$updated_at = true === $touch_updated_at
			? $now
			: sanitize_text_field( (string) ( $existing['updated_at'] ?? $placement['updated_at'] ?? $now ) );

		return array(
			'key'             => $key,
			'name'            => $name,
			'vertical'        => self::sanitize_choice( (string) ( $placement['vertical'] ?? $existing['vertical'] ?? '' ), self::VERTICALS, 'flights' ),
			'context'         => sanitize_key( (string) ( $placement['context'] ?? $existing['context'] ?? 'global' ) ),
			'widget_family'   => self::sanitize_choice( (string) ( $placement['widget_family'] ?? $existing['widget_family'] ?? '' ), self::WIDGET_FAMILIES, 'partner_link_card' ),
			'render_mode'     => $render_mode,
			'embed'           => $embed,
			'status'          => $status,
			'subid_pattern'   => self::sanitize_subid_pattern( (string) ( $placement['subid_pattern'] ?? $existing['subid_pattern'] ?? self::DEFAULT_SUBID_PATTERN ) ),
			'public_surfaces' => self::sanitize_key_list( $placement['public_surfaces'] ?? $existing['public_surfaces'] ?? array() ),
			'consent_required' => self::truthy( $placement['consent_required'] ?? $existing['consent_required'] ?? true ),
			'disclosure'      => array(
				'required' => self::truthy( $placement['disclosure']['required'] ?? $existing['disclosure']['required'] ?? true ),
				'copy'     => sanitize_text_field( (string) ( $placement['disclosure']['copy'] ?? $existing['disclosure']['copy'] ?? 'Sponsored travel search. Booking is completed with the partner provider.' ) ),
			),
			'frame'           => self::sanitize_frame( (array) ( $placement['frame'] ?? $existing['frame'] ?? array() ) ),
			'fallback'        => self::sanitize_fallback( (array) ( $placement['fallback'] ?? $existing['fallback'] ?? array() ) ),
			'notes'           => sanitize_textarea_field( (string) ( $placement['notes'] ?? $existing['notes'] ?? '' ) ),
			'created_at'      => sanitize_text_field( (string) ( $existing['created_at'] ?? $placement['created_at'] ?? $now ) ),
			'updated_at'      => $updated_at,
		);
	}

	public static function public_placement( array $placement ): array {
		$configured = self::embed_is_configured( (array) ( $placement['embed'] ?? array() ) );

		unset( $placement['embed']['reference'], $placement['embed']['url'], $placement['notes'] );

		$placement['configured'] = $configured;

		return $placement;
	}

	private static function get_registry(): array {
		$stored = get_option( self::OPTION_NAME, false );

		return self::sanitize_registry( false === $stored ? self::default_registry() : $stored );
	}

	private static function default_registry(): array {
		$placements = array();

		foreach ( Travelpayouts_Widget_Starter_Placements::definitions() as $placement ) {
			$sanitized = self::sanitize_placement( $placement );

			if ( is_wp_error( $sanitized ) ) {
				continue;
			}

			$placements[ $sanitized['key'] ] = $sanitized;
		}

		return array(
			'schema_version' => self::SCHEMA_VERSION,
			'placements'     => $placements,
			'updated_at'     => self::timestamp(),
		);
	}

	private static function sanitize_embed( array $embed ): array {
		$mode = self::sanitize_choice( (string) ( $embed['mode'] ?? '' ), self::RENDER_MODES, 'disabled' );

		return array(
			'source'          => self::sanitize_choice( (string) ( $embed['source'] ?? '' ), self::EMBED_SOURCES, 'none' ),
			'mode'            => $mode,
			'reference'       => self::sanitize_embed_reference( (string) ( $embed['reference'] ?? '' ) ),
			'url'             => self::sanitize_embed_url( (string) ( $embed['url'] ?? '' ), $mode ),
			'approved_hosts'  => self::sanitize_host_list( $embed['approved_hosts'] ?? array() ),
			'shortcode_attrs' => self::sanitize_shortcode_attrs( $embed['shortcode_attrs'] ?? array() ),
		);
	}

	private static function sanitize_embed_reference( string $value ): string {
		$value = trim( html_entity_decode( $value, ENT_QUOTES ) );

		if ( preg_match( '/wl[_-]?id\s*[=:]\s*[\'"]?([A-Za-z0-9_-]+)/i', $value, $matches ) ) {
			$value = (string) $matches[1];
		}

		return substr( preg_replace( '/[^A-Za-z0-9_\\-\\[\\]]+/', '', $value ), 0, 191 );
	}

	private static function sanitize_embed_url( string $value, string $mode ): string {
		$value = trim( html_entity_decode( $value, ENT_QUOTES ) );

		if ( '' === $value || in_array( $mode, array( 'official_shortcode', 'disabled' ), true ) ) {
			return '';
		}

		if ( preg_match( '/(?:src|href)\s*=\s*[\'"]([^\'"]+)[\'"]/i', $value, $matches ) ) {
			$value = (string) $matches[1];
		} elseif ( preg_match( '/(?:src|href)\s*=\s*([^>\s]+)/i', $value, $matches ) ) {
			$value = trim( (string) $matches[1], '\'"' );
		}

		if ( str_starts_with( $value, '//' ) ) {
			$value = 'https:' . $value;
		}

		$url    = esc_url_raw( $value );
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		$host   = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$path   = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );

		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) || ! self::is_allowed_embed_host( $host ) ) {
			return '';
		}

		if ( 'dashboard_script' === $mode && ! self::is_allowed_widget_script_path( $path ) ) {
			return '';
		}

		if ( 'iframe' === $mode && ! self::is_allowed_iframe_path( $host, $path ) ) {
			return '';
		}

		return $url;
	}

	private static function sanitize_shortcode_attrs( mixed $attributes ): array {
		$attributes = is_array( $attributes ) ? $attributes : array();
		$allowed    = array( 'origin', 'destination', 'responsive', 'width', 'height', 'limit', 'title' );
		$sanitized  = array();

		foreach ( $attributes as $key => $value ) {
			$key = sanitize_key( (string) $key );

			if ( ! in_array( $key, $allowed, true ) || ! is_scalar( $value ) ) {
				continue;
			}

			$value = sanitize_text_field( (string) $value );

			if ( '' !== $value ) {
				$sanitized[ $key ] = substr( $value, 0, 120 );
			}
		}

		return $sanitized;
	}

	private static function sanitize_fallback( array $fallback ): array {
		return array(
			'url'   => self::sanitize_embed_url( (string) ( $fallback['url'] ?? '' ), 'handoff_link' ),
			'label' => sanitize_text_field( (string) ( $fallback['label'] ?? '' ) ),
		);
	}

	private static function sanitize_frame( array $frame ): array {
		return array(
			'desktop_min_height' => max( 0, absint( $frame['desktop_min_height'] ?? 520 ) ),
			'tablet_min_height'  => max( 0, absint( $frame['tablet_min_height'] ?? 520 ) ),
			'mobile_min_height'  => max( 0, absint( $frame['mobile_min_height'] ?? 640 ) ),
			'lazy_load'          => self::truthy( $frame['lazy_load'] ?? true ),
			'timeout_ms'         => min( 30000, max( 1000, absint( $frame['timeout_ms'] ?? 8000 ) ) ),
		);
	}

	private static function sanitize_subid_pattern( string $value ): string {
		$value = strtolower( trim( $value ) );

		if ( '' === $value ) {
			return self::DEFAULT_SUBID_PATTERN;
		}

		$value = preg_replace( '/[^a-z0-9_{}]+/', '_', $value );
		$value = preg_replace( '/_+/', '_', (string) $value );
		$value = trim( (string) $value, '_' );

		return substr( '' === $value ? self::DEFAULT_SUBID_PATTERN : $value, 0, 191 );
	}

	private static function sanitize_key_list( mixed $values ): array {
		$values = is_array( $values ) ? $values : array( $values );
		$values = array_filter(
			array_map(
				static fn( $value ): string => sanitize_key( (string) $value ),
				$values
			)
		);

		return array_values( array_unique( $values ) );
	}

	private static function sanitize_host_list( mixed $values ): array {
		$values = is_array( $values ) ? $values : array( $values );
		$values = array_filter(
			array_map(
				static function ( $value ): string {
					$value = strtolower( trim( (string) $value ) );

					return preg_replace( '/[^a-z0-9.\\-]+/', '', $value );
				},
				$values
			)
		);

		return array_values( array_unique( $values ) );
	}

	private static function sanitize_choice( string $value, array $allowed, string $fallback ): string {
		$value = sanitize_key( $value );

		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	private static function embed_is_configured( array $embed ): bool {
		$mode = (string) ( $embed['mode'] ?? '' );

		if ( in_array( $mode, array( 'disabled', '' ), true ) ) {
			return false;
		}

		if ( 'official_shortcode' === $mode ) {
			$reference = sanitize_key( (string) ( $embed['reference'] ?? '' ) );

			return '' !== $reference && shortcode_exists( $reference );
		}

		return '' !== (string) ( $embed['reference'] ?? '' ) || '' !== (string) ( $embed['url'] ?? '' );
	}

	private static function is_allowed_embed_host( string $host ): bool {
		if ( in_array( $host, array( 'tp.media', 'tpwgt.com', 'tpwgts.com', 'travelpayouts.com', 'www.travelpayouts.com', 'trip.com', 'www.trip.com' ), true ) ) {
			return true;
		}

		return str_ends_with( $host, '.tp.media' )
			|| str_ends_with( $host, '.tpwgt.com' )
			|| str_ends_with( $host, '.tpwgts.com' )
			|| str_ends_with( $host, '.travelpayouts.com' )
			|| str_ends_with( $host, '.trip.com' );
	}

	private static function is_allowed_widget_script_path( string $path ): bool {
		return str_ends_with( $path, '.js' )
			|| str_starts_with( $path, '/content' )
			|| str_starts_with( $path, '/wl_web/' );
	}

	private static function is_allowed_iframe_path( string $host, string $path ): bool {
		if ( 'trip.com' === $host || str_ends_with( $host, '.trip.com' ) ) {
			return str_starts_with( $path, '/partners/ad/' );
		}

		if ( ! self::is_allowed_embed_host( $host ) || '' === $path || self::is_allowed_widget_script_path( $path ) ) {
			return false;
		}

		return str_starts_with( $path, '/ad/' )
			|| str_starts_with( $path, '/hotel/' )
			|| str_starts_with( $path, '/hotels/' )
			|| str_starts_with( $path, '/iframe/' )
			|| str_starts_with( $path, '/iframes/' )
			|| str_starts_with( $path, '/partners/' )
			|| str_starts_with( $path, '/search/' )
			|| str_starts_with( $path, '/widget/' )
			|| str_starts_with( $path, '/widgets/' );
	}

	private static function is_iframe_url( string $value ): bool {
		$path = strtolower( (string) wp_parse_url( $value, PHP_URL_PATH ) );
		$host = strtolower( (string) wp_parse_url( $value, PHP_URL_HOST ) );

		return ( 'trip.com' === $host || str_ends_with( $host, '.trip.com' ) ) && str_starts_with( $path, '/partners/ad/' );
	}

	private static function current_user_can_manage(): bool {
		return current_user_can( Capability_Manager::MANAGE_AFFILIATES ) || current_user_can( Capability_Manager::MANAGE_SETTINGS );
	}

	private static function truthy( mixed $value ): bool {
		return in_array( $value, array( true, 1, '1', 'yes', 'on' ), true );
	}

	private static function timestamp(): string {
		return wp_date( DATE_ATOM, time(), new \DateTimeZone( 'UTC' ) );
	}
}
