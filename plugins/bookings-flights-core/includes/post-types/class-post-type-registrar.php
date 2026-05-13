<?php
/**
 * Custom post type and post meta registration.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Post_Types;

use BAF\Core\Capabilities\Capability_Manager;

defined( 'ABSPATH' ) || exit;

final class Post_Type_Registrar {

	public const DESTINATION     = 'destination';
	public const ROUTE           = 'route';
	public const TRAVEL_DEAL     = 'travel_deal';
	public const TRIP_PLAN       = 'trip_plan';
	public const TRAVEL_PARTNER  = 'travel_partner';
	public const TRAVEL_ALERT    = 'travel_alert';

	public static function register(): void {
		foreach ( self::definitions() as $post_type => $definition ) {
			register_post_type(
				$post_type,
				apply_filters( 'baf_core_post_type_args', self::build_args( $post_type, $definition ), $post_type )
			);
		}

		self::register_meta();
	}

	public static function keys(): array {
		return array_keys( self::definitions() );
	}

	private static function definitions(): array {
		return array(
			self::DESTINATION    => array(
				'singular'      => __( 'Destination', 'bookings-flights-core' ),
				'plural'        => __( 'Destinations', 'bookings-flights-core' ),
				'description'   => __( 'City, country, and region travel guides.', 'bookings-flights-core' ),
				'public'        => true,
				'archive'       => 'destinations',
				'slug'          => 'destinations',
				'menu_icon'     => 'dashicons-location-alt',
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
				'taxonomies'    => array( 'travel_region', 'travel_style', 'travel_season' ),
				'capabilities'  => Capability_Manager::content_post_type_capabilities(),
			),
			self::ROUTE          => array(
				'singular'      => __( 'Route', 'bookings-flights-core' ),
				'plural'        => __( 'Routes', 'bookings-flights-core' ),
				'description'   => __( 'Origin-destination flight and trip route pages.', 'bookings-flights-core' ),
				'public'        => true,
				'archive'       => 'routes',
				'slug'          => 'routes',
				'menu_icon'     => 'dashicons-airplane',
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
				'taxonomies'    => array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' ),
				'capabilities'  => Capability_Manager::content_post_type_capabilities(),
			),
			self::TRAVEL_DEAL    => array(
				'singular'      => __( 'Travel Deal', 'bookings-flights-core' ),
				'plural'        => __( 'Travel Deals', 'bookings-flights-core' ),
				'description'   => __( 'Editorial or cached travel deal posts.', 'bookings-flights-core' ),
				'public'        => true,
				'archive'       => 'travel-deals',
				'slug'          => 'travel-deals',
				'menu_icon'     => 'dashicons-tickets-alt',
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions' ),
				'taxonomies'    => array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' ),
				'capabilities'  => Capability_Manager::content_post_type_capabilities(),
			),
			self::TRIP_PLAN      => array(
				'singular'      => __( 'Trip Plan', 'bookings-flights-core' ),
				'plural'        => __( 'Trip Plans', 'bookings-flights-core' ),
				'description'   => __( 'AI-generated and saved itinerary records.', 'bookings-flights-core' ),
				'public'        => false,
				'archive'       => false,
				'slug'          => false,
				'menu_icon'     => 'dashicons-clipboard',
				'supports'      => array( 'title', 'editor', 'custom-fields', 'revisions' ),
				'taxonomies'    => array( 'travel_style', 'travel_season' ),
				'capabilities'  => Capability_Manager::content_post_type_capabilities(),
			),
			self::TRAVEL_PARTNER => array(
				'singular'      => __( 'Travel Partner', 'bookings-flights-core' ),
				'plural'        => __( 'Travel Partners', 'bookings-flights-core' ),
				'description'   => __( 'Travelpayouts and provider partner records.', 'bookings-flights-core' ),
				'public'        => false,
				'archive'       => false,
				'slug'          => false,
				'menu_icon'     => 'dashicons-groups',
				'supports'      => array( 'title', 'editor', 'custom-fields' ),
				'taxonomies'    => array( 'travel_vertical' ),
				'capabilities'  => Capability_Manager::affiliate_post_type_capabilities(),
			),
			self::TRAVEL_ALERT   => array(
				'singular'      => __( 'Travel Alert', 'bookings-flights-core' ),
				'plural'        => __( 'Travel Alerts', 'bookings-flights-core' ),
				'description'   => __( 'Price alert landing records and editorial alert pages.', 'bookings-flights-core' ),
				'public'        => false,
				'archive'       => false,
				'slug'          => false,
				'menu_icon'     => 'dashicons-megaphone',
				'supports'      => array( 'title', 'custom-fields' ),
				'taxonomies'    => array( 'travel_season' ),
				'capabilities'  => Capability_Manager::alert_post_type_capabilities(),
			),
		);
	}

	private static function build_args( string $post_type, array $definition ): array {
		$is_public = true === $definition['public'];

		return array(
			'labels'              => self::labels( $definition['singular'], $definition['plural'] ),
			'description'         => $definition['description'],
			'public'              => $is_public,
			'hierarchical'        => false,
			'exclude_from_search' => ! $is_public,
			'publicly_queryable'  => $is_public,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => $is_public,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'menu_position'       => 26,
			'menu_icon'           => $definition['menu_icon'],
			'capabilities'        => $definition['capabilities'],
			'map_meta_cap'        => false,
			'supports'            => $definition['supports'],
			'taxonomies'          => $definition['taxonomies'],
			'has_archive'         => $definition['archive'],
			'rewrite'             => false === $definition['slug'] ? false : array( 'slug' => $definition['slug'] ),
			'query_var'           => $post_type,
		);
	}

	private static function labels( string $singular, string $plural ): array {
		return array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new_item'          => sprintf( __( 'Add New %s', 'bookings-flights-core' ), $singular ),
			'edit_item'             => sprintf( __( 'Edit %s', 'bookings-flights-core' ), $singular ),
			'new_item'              => sprintf( __( 'New %s', 'bookings-flights-core' ), $singular ),
			'view_item'             => sprintf( __( 'View %s', 'bookings-flights-core' ), $singular ),
			'search_items'          => sprintf( __( 'Search %s', 'bookings-flights-core' ), $plural ),
			'not_found'             => sprintf( __( 'No %s found.', 'bookings-flights-core' ), strtolower( $plural ) ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'bookings-flights-core' ), strtolower( $plural ) ),
			'all_items'             => sprintf( __( 'All %s', 'bookings-flights-core' ), $plural ),
			'archives'              => sprintf( __( '%s Archives', 'bookings-flights-core' ), $singular ),
			'attributes'            => sprintf( __( '%s Attributes', 'bookings-flights-core' ), $singular ),
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'bookings-flights-core' ), strtolower( $singular ) ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'bookings-flights-core' ), strtolower( $singular ) ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'bookings-flights-core' ), strtolower( $plural ) ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'bookings-flights-core' ), $plural ),
			'items_list'            => sprintf( __( '%s list', 'bookings-flights-core' ), $plural ),
		);
	}

	private static function register_meta(): void {
		foreach ( self::meta_definitions() as $meta_key => $definition ) {
			foreach ( $definition['post_types'] as $post_type ) {
				register_post_meta(
					$post_type,
					$meta_key,
					array(
						'type'              => $definition['type'],
						'description'       => $definition['description'],
						'single'            => true,
						'sanitize_callback' => $definition['sanitize_callback'],
						'auth_callback'     => array( __CLASS__, 'can_edit_meta' ),
						'show_in_rest'      => false,
					)
				);
			}
		}
	}

	private static function meta_definitions(): array {
		return array(
			'baf_origin'                 => self::meta_definition( __( 'Origin city or place.', 'bookings-flights-core' ), array( self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ) ),
			'baf_destination'            => self::meta_definition( __( 'Destination city or place.', 'bookings-flights-core' ), array( self::DESTINATION, self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ) ),
			'baf_origin_airport'         => self::meta_definition( __( 'Origin airport code.', 'bookings-flights-core' ), array( self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_code' ) ),
			'baf_destination_airport'    => self::meta_definition( __( 'Destination airport code.', 'bookings-flights-core' ), array( self::DESTINATION, self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_code' ) ),
			'baf_destination_best_time'  => self::meta_definition( __( 'Editable best-time-to-visit notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_destination_facts'      => self::meta_definition( __( 'Editable destination fact-sheet notes.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_destination_activities' => self::meta_definition( __( 'Editable activity and attraction planning notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_destination_seasonal'   => self::meta_definition( __( 'Editable seasonal trip-planning notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_departure_window'       => self::meta_definition( __( 'Preferred departure date window.', 'bookings-flights-core' ), array( self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ) ),
			'baf_return_window'          => self::meta_definition( __( 'Preferred return date window.', 'bookings-flights-core' ), array( self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ) ),
			'baf_route_travel_time'       => self::meta_definition( __( 'Editable route travel-time context.', 'bookings-flights-core' ), array( self::ROUTE ), 'sanitize_textarea_field' ),
			'baf_route_airport_notes'     => self::meta_definition( __( 'Editable route airport and connection notes.', 'bookings-flights-core' ), array( self::ROUTE ), 'sanitize_textarea_field' ),
			'baf_route_flexible_dates'    => self::meta_definition( __( 'Editable flexible-date guidance for a route.', 'bookings-flights-core' ), array( self::ROUTE ), 'sanitize_textarea_field' ),
			'baf_route_destination_notes' => self::meta_definition( __( 'Editable destination hotel and activity notes for a route.', 'bookings-flights-core' ), array( self::ROUTE ), 'sanitize_textarea_field' ),
			'baf_deal_seasonal_context'  => self::meta_definition( __( 'Editable seasonal context for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_deal_weekend_ideas'      => self::meta_definition( __( 'Editable weekend trip ideas for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_deal_theme_notes'        => self::meta_definition( __( 'Editable travel-style notes for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_deal_activity_notes'     => self::meta_definition( __( 'Editable activity planning notes for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_deal_partner_notes'      => self::meta_definition( __( 'Editable partner handoff notes for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_deal_source_note'        => self::meta_definition( __( 'Editable sourcing or claim-safety note for a travel deal.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL ), 'sanitize_textarea_field' ),
			'baf_budget_min'             => self::meta_definition( __( 'Minimum travel budget.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_non_negative_number' ), 'number' ),
			'baf_budget_max'             => self::meta_definition( __( 'Maximum travel budget.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL, self::TRIP_PLAN, self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_non_negative_number' ), 'number' ),
			'baf_travel_style'           => self::meta_definition( __( 'Primary travel style.', 'bookings-flights-core' ), array( self::DESTINATION, self::ROUTE, self::TRAVEL_DEAL, self::TRIP_PLAN ) ),
			'baf_hotel_guide_summary'    => self::meta_definition( __( 'Hotel guide summary for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_neighborhoods'    => self::meta_definition( __( 'Editorial neighborhood guidance for hotels in a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_best_for'         => self::meta_definition( __( 'Best-fit editorial hotel guidance for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_family_notes'     => self::meta_definition( __( 'Family hotel editorial notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_luxury_notes'     => self::meta_definition( __( 'Luxury hotel editorial notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_budget_notes'     => self::meta_definition( __( 'Budget hotel editorial notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_hotel_landmark_notes'   => self::meta_definition( __( 'Landmark-area hotel editorial notes for a destination.', 'bookings-flights-core' ), array( self::DESTINATION ), 'sanitize_textarea_field' ),
			'baf_affiliate_vertical'     => self::meta_definition( __( 'Affiliate vertical classification.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL, self::TRAVEL_PARTNER ) ),
			'baf_provider_ids'           => self::meta_definition( __( 'Associated provider identifiers.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL, self::TRAVEL_PARTNER ), array( __CLASS__, 'sanitize_text_list' ), 'array' ),
			'baf_subid_template'         => self::meta_definition( __( 'Affiliate SubID template.', 'bookings-flights-core' ), array( self::TRAVEL_DEAL, self::TRAVEL_PARTNER ) ),
			'baf_ai_source_session_id'   => self::meta_definition( __( 'Source AI session identifier.', 'bookings-flights-core' ), array( self::TRIP_PLAN ) ),
			'baf_itinerary_json'         => self::meta_definition( __( 'Stored itinerary JSON payload.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), array( __CLASS__, 'sanitize_json_text' ) ),
			'baf_alert_route'            => self::meta_definition( __( 'Alert route identifier.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ) ),
			'baf_alert_frequency'        => self::meta_definition( __( 'Alert frequency.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ) ),
			'baf_alert_email'            => self::meta_definition( __( 'Alert contact email.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), 'sanitize_email' ),
			'baf_alert_user_id'          => self::meta_definition( __( 'Alert requester user ID when logged in.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_non_negative_integer' ), 'integer' ),
			'baf_alert_route_post_id'    => self::meta_definition( __( 'Source route post ID.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_non_negative_integer' ), 'integer' ),
			'baf_alert_travelers'        => self::meta_definition( __( 'Requested traveler count.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), array( __CLASS__, 'sanitize_non_negative_integer' ), 'integer' ),
			'baf_alert_cabin'            => self::meta_definition( __( 'Requested cabin class.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), 'sanitize_key' ),
			'baf_alert_surface'          => self::meta_definition( __( 'Alert source surface.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), 'sanitize_key' ),
			'baf_alert_source_url'       => self::meta_definition( __( 'Alert source URL.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), 'esc_url_raw' ),
			'baf_alert_consent_at'       => self::meta_definition( __( 'Alert consent timestamp.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ) ),
			'baf_alert_status'           => self::meta_definition( __( 'Local alert workflow status.', 'bookings-flights-core' ), array( self::TRAVEL_ALERT ), 'sanitize_key' ),
			'baf_partner_apply_url'      => self::meta_definition( __( 'Partner application URL.', 'bookings-flights-core' ), array( self::TRAVEL_PARTNER ), 'esc_url_raw' ),
			'baf_partner_status'         => self::meta_definition( __( 'Partner approval or integration status.', 'bookings-flights-core' ), array( self::TRAVEL_PARTNER ) ),
			'baf_ai_opportunity_schema'  => self::meta_definition( __( 'AI opportunity schema version.', 'bookings-flights-core' ), array( self::TRIP_PLAN ) ),
			'baf_ai_handoff_intents'     => self::meta_definition( __( 'Approved local AI handoff intent records.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), array( __CLASS__, 'sanitize_json_text' ) ),
			'baf_saved_trip_context'     => self::meta_definition( __( 'Local saved trip placement and resume context.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), array( __CLASS__, 'sanitize_json_text' ) ),
			'baf_saved_trip_saved_at'    => self::meta_definition( __( 'Saved trip intent timestamp.', 'bookings-flights-core' ), array( self::TRIP_PLAN ) ),
			'baf_saved_trip_status'      => self::meta_definition( __( 'Saved trip workflow status.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), 'sanitize_key' ),
			'baf_saved_trip_travelers'   => self::meta_definition( __( 'Saved trip traveler count.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), array( __CLASS__, 'sanitize_non_negative_integer' ), 'integer' ),
			'baf_saved_trip_user_id'     => self::meta_definition( __( 'Saved trip owner user ID.', 'bookings-flights-core' ), array( self::TRIP_PLAN ), array( __CLASS__, 'sanitize_non_negative_integer' ), 'integer' ),
		);
	}

	private static function meta_definition( string $description, array $post_types, callable|string $sanitize_callback = 'sanitize_text_field', string $type = 'string' ): array {
		return array(
			'description'       => $description,
			'post_types'        => $post_types,
			'sanitize_callback' => $sanitize_callback,
			'type'              => $type,
		);
	}

	public static function can_edit_meta( mixed $allowed, string $meta_key, int $post_id, int $user_id ): bool {
		if ( $post_id <= 0 ) {
			return user_can( $user_id, Capability_Manager::EDIT_CONTENT );
		}

		return user_can( $user_id, 'edit_post', $post_id );
	}

	public static function sanitize_code( mixed $value ): string {
		$normalized = preg_replace( '/[^A-Z0-9]/', '', strtoupper( sanitize_text_field( (string) $value ) ) );

		return is_string( $normalized ) ? substr( $normalized, 0, 10 ) : '';
	}

	public static function sanitize_non_negative_number( mixed $value ): float {
		return max( 0, (float) $value );
	}

	public static function sanitize_non_negative_integer( mixed $value ): int {
		return max( 0, absint( $value ) );
	}

	public static function sanitize_text_list( mixed $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'sanitize_text_field', $value ) ) );
	}

	public static function sanitize_json_text( mixed $value ): string {
		if ( is_array( $value ) || is_object( $value ) ) {
			$encoded = wp_json_encode( map_deep( (array) $value, 'sanitize_text_field' ) );

			return false === $encoded ? '' : $encoded;
		}

		return sanitize_textarea_field( (string) $value );
	}
}
