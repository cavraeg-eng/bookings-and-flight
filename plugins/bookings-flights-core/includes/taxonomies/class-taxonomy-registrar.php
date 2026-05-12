<?php
/**
 * Custom taxonomy registration.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Taxonomies;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Taxonomy_Registrar {

	public const TRAVEL_REGION   = 'travel_region';
	public const TRAVEL_STYLE    = 'travel_style';
	public const TRAVEL_VERTICAL = 'travel_vertical';
	public const TRAVEL_SEASON   = 'travel_season';

	public static function register(): void {
		foreach ( self::definitions() as $taxonomy => $definition ) {
			register_taxonomy(
				$taxonomy,
				$definition['post_types'],
				apply_filters( 'baf_core_taxonomy_args', self::build_args( $taxonomy, $definition ), $taxonomy )
			);

			foreach ( $definition['post_types'] as $post_type ) {
				register_taxonomy_for_object_type( $taxonomy, $post_type );
			}
		}
	}

	public static function keys(): array {
		return array_keys( self::definitions() );
	}

	private static function definitions(): array {
		return array(
			self::TRAVEL_REGION   => array(
				'singular'    => __( 'Travel Region', 'bookings-flights-core' ),
				'plural'      => __( 'Travel Regions', 'bookings-flights-core' ),
				'description' => __( 'Continent, country, region, and destination grouping.', 'bookings-flights-core' ),
				'slug'        => 'travel-regions',
				'hierarchical' => true,
				'post_types'  => array( Post_Type_Registrar::DESTINATION, Post_Type_Registrar::ROUTE, Post_Type_Registrar::TRAVEL_DEAL ),
			),
			self::TRAVEL_STYLE    => array(
				'singular'    => __( 'Travel Style', 'bookings-flights-core' ),
				'plural'      => __( 'Travel Styles', 'bookings-flights-core' ),
				'description' => __( 'Budget, family, luxury, beach, business, adventure, and culture styles.', 'bookings-flights-core' ),
				'slug'        => 'travel-styles',
				'hierarchical' => false,
				'post_types'  => array( Post_Type_Registrar::DESTINATION, Post_Type_Registrar::ROUTE, Post_Type_Registrar::TRAVEL_DEAL, Post_Type_Registrar::TRIP_PLAN ),
			),
			self::TRAVEL_VERTICAL => array(
				'singular'    => __( 'Travel Vertical', 'bookings-flights-core' ),
				'plural'      => __( 'Travel Verticals', 'bookings-flights-core' ),
				'description' => __( 'Flights, hotels, cars, activities, insurance, eSIM, transfers, and partner verticals.', 'bookings-flights-core' ),
				'slug'        => 'travel-verticals',
				'hierarchical' => true,
				'post_types'  => array( Post_Type_Registrar::ROUTE, Post_Type_Registrar::TRAVEL_DEAL, Post_Type_Registrar::TRAVEL_PARTNER ),
			),
			self::TRAVEL_SEASON   => array(
				'singular'    => __( 'Travel Season', 'bookings-flights-core' ),
				'plural'      => __( 'Travel Seasons', 'bookings-flights-core' ),
				'description' => __( 'Month, season, holiday, or timing intent.', 'bookings-flights-core' ),
				'slug'        => 'travel-seasons',
				'hierarchical' => false,
				'post_types'  => array( Post_Type_Registrar::DESTINATION, Post_Type_Registrar::ROUTE, Post_Type_Registrar::TRAVEL_DEAL, Post_Type_Registrar::TRIP_PLAN, Post_Type_Registrar::TRAVEL_ALERT ),
			),
		);
	}

	private static function build_args( string $taxonomy, array $definition ): array {
		return array(
			'labels'             => self::labels( $definition['singular'], $definition['plural'] ),
			'description'        => $definition['description'],
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => $definition['hierarchical'],
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'capabilities'       => Capability_Manager::content_taxonomy_capabilities(),
			'rewrite'            => array(
				'slug'         => $definition['slug'],
				'hierarchical' => true === $definition['hierarchical'],
			),
			'query_var'          => $taxonomy,
		);
	}

	private static function labels( string $singular, string $plural ): array {
		return array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'search_items'               => sprintf( __( 'Search %s', 'bookings-flights-core' ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s', 'bookings-flights-core' ), $plural ),
			'all_items'                  => sprintf( __( 'All %s', 'bookings-flights-core' ), $plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'bookings-flights-core' ), $singular ),
			'view_item'                  => sprintf( __( 'View %s', 'bookings-flights-core' ), $singular ),
			'update_item'                => sprintf( __( 'Update %s', 'bookings-flights-core' ), $singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'bookings-flights-core' ), $singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'bookings-flights-core' ), $singular ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'bookings-flights-core' ), strtolower( $plural ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'bookings-flights-core' ), strtolower( $plural ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'bookings-flights-core' ), strtolower( $plural ) ),
			'not_found'                  => sprintf( __( 'No %s found.', 'bookings-flights-core' ), strtolower( $plural ) ),
			'back_to_items'              => sprintf( __( 'Back to %s', 'bookings-flights-core' ), $plural ),
		);
	}
}