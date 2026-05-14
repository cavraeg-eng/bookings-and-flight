<?php
/**
 * Travelpayouts SubID reporting map.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Subid_Map_Service {

	public function entries(): array {
		$placements = $this->placements();
		$entries    = array();

		foreach ( self::definitions() as $definition ) {
			$placement_key = sanitize_key( (string) $definition['placement_key'] );
			$placement     = $placements[ $placement_key ] ?? $this->pseudo_placement( $definition );
			$context       = array(
				'surface' => sanitize_key( (string) $definition['surface'] ),
				'channel' => sanitize_key( (string) $definition['channel'] ),
				'slug'    => sanitize_key( (string) $definition['slug'] ),
			);

			$entries[] = array(
				'surface'          => $context['surface'],
				'channel'          => $context['channel'],
				'slug'             => $context['slug'],
				'placement_key'    => $placement_key,
				'placement_name'   => sanitize_text_field( (string) ( $placement['name'] ?? $definition['label'] ) ),
				'vertical'         => sanitize_key( (string) ( $placement['vertical'] ?? $definition['vertical'] ) ),
				'widget_family'    => sanitize_key( (string) ( $placement['widget_family'] ?? $definition['widget_family'] ) ),
				'status'           => sanitize_key( (string) ( $placement['status'] ?? 'mapped' ) ),
				'configured'       => true === (bool) ( $placement['configured'] ?? false ),
				'example_subid'    => Travelpayouts_Widget_Subid_Service::build( $placement, $context ),
				'reporting_source' => __( 'Travelpayouts Performance reports are the source of truth for partner clicks, searches, bookings, conversion, and earnings by SubID.', 'bookings-flights-core' ),
				'local_scope'      => __( 'WordPress-local analytics are privacy-aware operational signals only; they do not represent provider revenue or completed bookings.', 'bookings-flights-core' ),
			);
		}

		return $entries;
	}

	private function placements(): array {
		$service    = new Travelpayouts_Widget_Registry_Service();
		$placements = $service->all( false );

		if ( is_wp_error( $placements ) ) {
			return array();
		}

		$indexed = array();

		foreach ( (array) $placements as $placement ) {
			if ( ! is_array( $placement ) || empty( $placement['key'] ) ) {
				continue;
			}

			$indexed[ sanitize_key( (string) $placement['key'] ) ] = $placement;
		}

		return $indexed;
	}

	private function pseudo_placement( array $definition ): array {
		return array(
			'key'           => sanitize_key( (string) $definition['placement_key'] ),
			'name'          => sanitize_text_field( (string) $definition['label'] ),
			'vertical'      => sanitize_key( (string) $definition['vertical'] ),
			'widget_family' => sanitize_key( (string) $definition['widget_family'] ),
			'status'        => 'mapped',
			'configured'    => false,
		);
	}

	private static function definitions(): array {
		return array(
			array(
				'label'         => __( 'Homepage flight search', 'bookings-flights-core' ),
				'placement_key' => 'flights_white_label_search',
				'vertical'      => 'flights',
				'widget_family' => 'white_label_search',
				'surface'       => 'home',
				'channel'       => 'homepage',
				'slug'          => 'flight_search',
			),
			array(
				'label'         => __( 'Homepage hotel search', 'bookings-flights-core' ),
				'placement_key' => 'hotels_partner_search',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_search',
				'surface'       => 'home',
				'channel'       => 'homepage',
				'slug'          => 'hotel_search',
			),
			array(
				'label'         => __( 'Flights search page', 'bookings-flights-core' ),
				'placement_key' => 'flights_white_label_search',
				'vertical'      => 'flights',
				'widget_family' => 'white_label_search',
				'surface'       => 'flights',
				'channel'       => 'search_page',
				'slug'          => 'flight_search',
			),
			array(
				'label'         => __( 'Flights low-price calendar', 'bookings-flights-core' ),
				'placement_key' => 'flights_low_price_calendar',
				'vertical'      => 'flights',
				'widget_family' => 'low_price_calendar',
				'surface'       => 'flights',
				'channel'       => 'search_page',
				'slug'          => 'flight_search_calendar',
			),
			array(
				'label'         => __( 'Flights popular routes', 'bookings-flights-core' ),
				'placement_key' => 'flights_popular_routes',
				'vertical'      => 'flights',
				'widget_family' => 'popular_routes',
				'surface'       => 'flights',
				'channel'       => 'search_page',
				'slug'          => 'flight_search_popular_routes',
			),
			array(
				'label'         => __( 'Flights route map', 'bookings-flights-core' ),
				'placement_key' => 'flights_route_map',
				'vertical'      => 'flights',
				'widget_family' => 'route_map',
				'surface'       => 'flights',
				'channel'       => 'search_page',
				'slug'          => 'flight_search_route_map',
			),
			array(
				'label'         => __( 'Hotels search page', 'bookings-flights-core' ),
				'placement_key' => 'hotels_partner_search',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_search',
				'surface'       => 'hotels',
				'channel'       => 'search_page',
				'slug'          => 'hotel_search',
			),
			array(
				'label'         => __( 'Hotels map handoff', 'bookings-flights-core' ),
				'placement_key' => 'hotels_map_handoff',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_map',
				'surface'       => 'hotels',
				'channel'       => 'search_page',
				'slug'          => 'hotels_landing_hotel_map',
			),
			array(
				'label'         => __( 'Hotels listing handoff', 'bookings-flights-core' ),
				'placement_key' => 'hotels_listing_handoff',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_listing',
				'surface'       => 'hotels',
				'channel'       => 'search_page',
				'slug'          => 'hotels_landing_hotel_listings',
			),
			array(
				'label'         => __( 'Destination hotel guide handoff', 'bookings-flights-core' ),
				'placement_key' => 'hotels_partner_search',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_search',
				'surface'       => 'hotels',
				'channel'       => 'destination_single',
				'slug'          => 'destination_guide',
			),
			array(
				'label'         => __( 'Route flight guide handoff', 'bookings-flights-core' ),
				'placement_key' => 'flights_white_label_search',
				'vertical'      => 'flights',
				'widget_family' => 'white_label_search',
				'surface'       => 'route',
				'channel'       => 'route_single',
				'slug'          => 'route_guide',
			),
			array(
				'label'         => __( 'AI planner recommendation', 'bookings-flights-core' ),
				'placement_key' => 'ai_planner_recommendation',
				'vertical'      => 'ai',
				'widget_family' => 'planner_recommendation',
				'surface'       => 'trip_planner',
				'channel'       => 'ai_planner',
				'slug'          => 'trip_planner_recommendations',
			),
			array(
				'label'         => __( 'Saved trip flight resume', 'bookings-flights-core' ),
				'placement_key' => 'flights_white_label_search',
				'vertical'      => 'flights',
				'widget_family' => 'white_label_search',
				'surface'       => 'saved_trip',
				'channel'       => 'saved_trips',
				'slug'          => 'saved_trip_resume',
			),
			array(
				'label'         => __( 'Saved trip hotel resume', 'bookings-flights-core' ),
				'placement_key' => 'hotels_partner_search',
				'vertical'      => 'hotels',
				'widget_family' => 'hotel_search',
				'surface'       => 'saved_trip',
				'channel'       => 'saved_trips',
				'slug'          => 'saved_trip_resume',
			),
			array(
				'label'         => __( 'Flight alert resume', 'bookings-flights-core' ),
				'placement_key' => 'flights_white_label_search',
				'vertical'      => 'flights',
				'widget_family' => 'white_label_search',
				'surface'       => 'alert',
				'channel'       => 'travel_alert',
				'slug'          => 'price_alert_resume',
			),
		);
	}
}
