<?php
/**
 * Starter Travelpayouts widget placement definitions and migrations.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Widget_Starter_Placements {

	public const SCHEMA_VERSION = '1.0.2';

	public static function definitions(): array {
		$settings         = Settings_Manager::get_travelpayouts();
		$white_label_id   = sanitize_text_field( (string) $settings['white_label_widget_id'] );
		$hotel_widget_url = esc_url_raw( (string) $settings['hotel_widget_script_url'] );

		return array(
			array(
				'key'             => 'flights_white_label_search',
				'name'            => 'Flights White Label search',
				'vertical'        => 'flights',
				'context'         => 'flights',
				'widget_family'   => 'white_label_search',
				'render_mode'     => 'dashboard_script',
				'embed'           => array(
					'source'         => 'travelpayouts_white_label',
					'mode'           => 'dashboard_script',
					'reference'      => $white_label_id,
					'approved_hosts' => array( 'tpwgts.com' ),
				),
				'status'          => '' === $white_label_id ? 'draft' : 'active',
				'public_surfaces' => array( 'home', 'flights', 'route' ),
				'notes'           => 'Seeded from the existing White Label Widget ID setting.',
			),
			array(
				'key'             => 'flights_low_price_calendar',
				'name'            => 'Flights low-price calendar',
				'vertical'        => 'flights',
				'context'         => 'flights_discovery',
				'widget_family'   => 'low_price_calendar',
				'render_mode'     => 'official_shortcode',
				'embed'           => array(
					'source'          => 'official_plugin',
					'mode'            => 'official_shortcode',
					'reference'       => 'tp_calendar_widget',
					'shortcode_attrs' => array(
						'responsive' => 'true',
					),
				),
				'status'          => 'active',
				'public_surfaces' => array( 'flights', 'route' ),
				'frame'           => array(
					'desktop_min_height' => 460,
					'tablet_min_height'  => 460,
					'mobile_min_height'  => 560,
				),
				'notes'           => 'Official Travelpayouts calendar shortcode for route-level flight discovery.',
			),
			array(
				'key'             => 'flights_popular_routes',
				'name'            => 'Flights popular routes',
				'vertical'        => 'flights',
				'context'         => 'flights_discovery',
				'widget_family'   => 'popular_routes',
				'render_mode'     => 'official_shortcode',
				'embed'           => array(
					'source'          => 'official_plugin',
					'mode'            => 'official_shortcode',
					'reference'       => 'tp_popular_routes_widget',
					'shortcode_attrs' => array(
						'responsive' => 'true',
					),
				),
				'status'          => 'active',
				'public_surfaces' => array( 'flights', 'route' ),
				'frame'           => array(
					'desktop_min_height' => 380,
					'tablet_min_height'  => 420,
					'mobile_min_height'  => 520,
				),
				'notes'           => 'Official Travelpayouts popular routes shortcode for flight discovery.',
			),
			array(
				'key'             => 'flights_route_map',
				'name'            => 'Flights route map',
				'vertical'        => 'flights',
				'context'         => 'flights_discovery',
				'widget_family'   => 'route_map',
				'render_mode'     => 'official_shortcode',
				'embed'           => array(
					'source'          => 'official_plugin',
					'mode'            => 'official_shortcode',
					'reference'       => 'tp_map_widget',
					'shortcode_attrs' => array(
						'width'  => '100%',
						'height' => '420',
					),
				),
				'status'          => 'active',
				'public_surfaces' => array( 'flights', 'route' ),
				'frame'           => array(
					'desktop_min_height' => 460,
					'tablet_min_height'  => 460,
					'mobile_min_height'  => 520,
				),
				'notes'           => 'Official Travelpayouts map shortcode for origin-based flight discovery.',
			),
			array(
				'key'             => 'hotels_partner_search',
				'name'            => 'Hotels partner search',
				'vertical'        => 'hotels',
				'context'         => 'hotels',
				'widget_family'   => 'hotel_search',
				'render_mode'     => self::is_iframe_url( $hotel_widget_url ) ? 'iframe' : 'dashboard_script',
				'embed'           => array(
					'source'         => 'partner_program',
					'mode'           => self::is_iframe_url( $hotel_widget_url ) ? 'iframe' : 'dashboard_script',
					'url'            => $hotel_widget_url,
					'approved_hosts' => array( 'tp.media', 'tpwgt.com', 'tpwgts.com', 'travelpayouts.com', 'trip.com' ),
				),
				'status'          => '' === $hotel_widget_url ? 'draft' : 'active',
				'public_surfaces' => array( 'home', 'hotels' ),
				'fallback'        => array(
					'url'   => $hotel_widget_url,
					'label' => 'Open hotel search',
				),
				'notes'           => 'Seeded from the existing Hotels & Accommodation widget setting.',
			),
		);
	}

	public static function migrate_registry( array $registry, mixed $stored ): array {
		$stored_version = is_array( $stored ) ? (string) ( $stored['schema_version'] ?? '' ) : '';

		if ( '' === $stored_version || version_compare( $stored_version, '1.0.1', '<' ) ) {
			$registry = self::migrate_flights_route_surface( $registry );
		}

		if ( '' === $stored_version || version_compare( $stored_version, '1.0.2', '<' ) ) {
			$registry = self::migrate_discovery_placements( $registry );
		}

		return $registry;
	}

	private static function migrate_flights_route_surface( array $registry ): array {
		if ( empty( $registry['placements']['flights_white_label_search'] ) || ! is_array( $registry['placements']['flights_white_label_search'] ) ) {
			return $registry;
		}

		$placement = $registry['placements']['flights_white_label_search'];
		$surfaces  = self::sanitize_key_list( $placement['public_surfaces'] ?? array() );

		if ( in_array( 'route', $surfaces, true ) ) {
			return $registry;
		}

		$now = self::timestamp();

		$surfaces[] = 'route';

		$placement['public_surfaces'] = $surfaces;
		$placement['updated_at']      = $now;
		$registry['updated_at']       = $now;

		$registry['placements']['flights_white_label_search'] = $placement;

		return $registry;
	}

	private static function migrate_discovery_placements( array $registry ): array {
		$changed        = false;
		$discovery_keys = array( 'flights_low_price_calendar', 'flights_popular_routes', 'flights_route_map' );

		foreach ( self::definitions() as $placement ) {
			$key = (string) ( $placement['key'] ?? '' );

			if ( ! in_array( $key, $discovery_keys, true ) || isset( $registry['placements'][ $key ] ) ) {
				continue;
			}

			$sanitized = Travelpayouts_Widget_Registry_Service::sanitize_placement( $placement, array(), false );

			if ( is_wp_error( $sanitized ) ) {
				continue;
			}

			$registry['placements'][ $key ] = $sanitized;
			$changed                       = true;
		}

		if ( true === $changed ) {
			$registry['updated_at'] = self::timestamp();
		}

		return $registry;
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

	private static function is_iframe_url( string $value ): bool {
		$path = strtolower( (string) wp_parse_url( $value, PHP_URL_PATH ) );
		$host = strtolower( (string) wp_parse_url( $value, PHP_URL_HOST ) );

		return ( 'trip.com' === $host || str_ends_with( $host, '.trip.com' ) ) && str_starts_with( $path, '/partners/ad/' );
	}

	private static function timestamp(): string {
		return wp_date( DATE_ATOM, time(), new \DateTimeZone( 'UTC' ) );
	}
}
