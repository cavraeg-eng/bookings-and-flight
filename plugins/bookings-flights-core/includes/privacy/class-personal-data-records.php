<?php
/**
 * Shared personal-data query and formatting helpers.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Privacy;

use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Services\Saved_Trip_Service;

defined( 'ABSPATH' ) || exit;

final class Personal_Data_Records {

	public static function erase_posts( array $query_args, int $page_size, string $state_key = '', int $page = 1 ): array {
		$retained_ids = '' === $state_key ? array() : self::retained_ids( $state_key );

		if ( $page <= 1 && '' !== $state_key ) {
			delete_transient( $state_key );
			$retained_ids = array();
		}

		$query_args['fields']                 = 'ids';
		$query_args['no_found_rows']          = true;
		$query_args['cache_results']          = false;
		$query_args['update_post_meta_cache'] = false;
		$query_args['update_post_term_cache'] = false;

		if ( ! empty( $retained_ids ) ) {
			$excluded_ids               = array_map( 'absint', (array) ( $query_args['post__not_in'] ?? array() ) );
			$query_args['post__not_in'] = array_values( array_unique( array_merge( $excluded_ids, $retained_ids ) ) );
		}

		$query          = new \WP_Query( $query_args );
		$ids            = array_map( 'absint', $query->posts );
		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( $ids as $post_id ) {
			$deleted = wp_delete_post( $post_id, true );

			if ( $deleted instanceof \WP_Post ) {
				$items_removed = true;
				continue;
			}

			$items_retained = true;
			$retained_ids[] = $post_id;
			$messages[]     = sprintf(
				/* translators: %d: post ID. */
				__( 'Bookings and Flights record %d could not be erased.', 'bookings-flights-core' ),
				$post_id
			);
		}

		self::store_retained_ids( $state_key, $retained_ids, count( $ids ) < $page_size );

		return self::erase_response( $items_removed, $items_retained, $messages, count( $ids ) < $page_size );
	}

	public static function erasure_state_key( string $scope, string $email_address ): string {
		return 'baf_privacy_erase_' . sanitize_key( $scope ) . '_' . md5( strtolower( $email_address ) );
	}

	public static function saved_trip_ids( int $user_id, int $page, int $per_page ): array {
		$query = new \WP_Query( self::saved_trip_query_args( $user_id, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	public static function saved_trip_query_args( int $user_id, int $page, int $per_page ): array {
		return array(
			'author'                 => $user_id,
			'fields'                 => 'ids',
			'meta_query'             => array(
				array(
					'key'   => Saved_Trip_Service::META_STATUS,
					'value' => 'active',
				),
			),
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'paged'                  => max( 1, $page ),
			'post_status'            => array( 'private', 'draft' ),
			'post_type'              => Post_Type_Registrar::TRIP_PLAN,
			'posts_per_page'         => $per_page,
			'no_found_rows'          => true,
			'cache_results'          => false,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		);
	}

	public static function travel_alert_ids( string $email, int $page, int $per_page ): array {
		$query = new \WP_Query( self::travel_alert_query_args( $email, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	public static function travel_alert_query_args( string $email, int $page, int $per_page ): array {
		return array(
			'fields'                 => 'ids',
			'meta_query'             => array(
				array(
					'key'   => 'baf_alert_email',
					'value' => $email,
				),
			),
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'paged'                  => max( 1, $page ),
			'post_status'            => 'any',
			'post_type'              => Post_Type_Registrar::TRAVEL_ALERT,
			'posts_per_page'         => $per_page,
			'no_found_rows'          => true,
			'cache_results'          => false,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		);
	}

	public static function ai_trip_plan_ids( int $user_id, int $page, int $per_page ): array {
		$query = new \WP_Query( self::ai_trip_plan_query_args( $user_id, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	public static function ai_trip_plan_query_args( int $user_id, int $page, int $per_page ): array {
		return array(
			'author'                 => $user_id,
			'fields'                 => 'ids',
			'meta_query'             => array(
				array(
					'key'     => 'baf_ai_source_session_id',
					'compare' => 'EXISTS',
				),
			),
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'paged'                  => max( 1, $page ),
			'post_status'            => array( 'draft', 'private' ),
			'post_type'              => Post_Type_Registrar::TRIP_PLAN,
			'posts_per_page'         => $per_page,
			'no_found_rows'          => true,
			'cache_results'          => false,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		);
	}

	public static function saved_trip_links( int $post_id, string $origin, string $destination, string $depart_date, string $return_date ): array {
		$origin_code      = self::iata_code( $origin );
		$destination_code = self::iata_code( $destination );

		return array(
			'flights' => esc_url_raw(
				add_query_arg(
					array_filter(
						array(
							'baf_surface'        => 'saved_trips',
							'depart_date'        => $depart_date,
							'destination'        => $destination_code,
							'origin'             => $origin_code,
							'return_date'        => $return_date,
							'travel_destination' => '' === $destination_code ? $destination : '',
							'travel_origin'      => '' === $origin_code ? $origin : '',
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

	public static function alert_resume_link( string $origin, string $destination ): string {
		return esc_url_raw(
			add_query_arg(
				array_filter(
					array(
						'travel_focus' => 'price_alert',
						'origin'       => self::iata_code( $origin ),
						'destination'  => self::iata_code( $destination ),
					)
				),
				home_url( '/flights/' )
			)
		);
	}

	public static function owned_trip_plan_link( int $post_id, int $user_id ): string {
		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || Post_Type_Registrar::TRIP_PLAN !== $post->post_type || (int) $post->post_author !== $user_id ) {
			return '';
		}

		return self::edit_post_link( $post_id );
	}

	public static function edit_post_link( int $post_id ): string {
		$url = get_edit_post_link( $post_id, 'raw' );

		return is_string( $url ) ? esc_url_raw( $url ) : '';
	}

	public static function data_rows( array $rows ): array {
		$data = array();

		foreach ( $rows as $name => $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}

			$value = trim( sanitize_textarea_field( (string) $value ) );

			if ( '' === $value ) {
				continue;
			}

			$data[] = array(
				'name'  => sanitize_text_field( (string) $name ),
				'value' => $value,
			);
		}

		return $data;
	}

	public static function meta_text( int $post_id, string $meta_key ): string {
		return sanitize_text_field( (string) get_post_meta( $post_id, $meta_key, true ) );
	}

	public static function user_id_for_email( string $email_address ): int {
		$email = self::valid_email( $email_address );

		if ( '' === $email ) {
			return 0;
		}

		$user = get_user_by( 'email', $email );

		return $user instanceof \WP_User ? (int) $user->ID : 0;
	}

	public static function valid_email( string $email_address ): string {
		$email = sanitize_email( $email_address );

		return '' !== $email && is_email( $email ) ? $email : '';
	}

	public static function ai_sessions_table_exists(): bool {
		global $wpdb;

		$table = AI_Sessions_Table::table_name();

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
	}

	public static function export_response( array $items = array(), bool $done = true ): array {
		return array(
			'data' => $items,
			'done' => $done,
		);
	}

	public static function erase_response( bool $removed = false, bool $retained = false, array $messages = array(), bool $done = true ): array {
		return array(
			'items_removed'  => $removed,
			'items_retained' => $retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	private static function retained_ids( string $state_key ): array {
		$value = get_transient( $state_key );

		if ( ! is_array( $value ) ) {
			return array();
		}

		return array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
	}

	private static function store_retained_ids( string $state_key, array $retained_ids, bool $done ): void {
		if ( '' === $state_key ) {
			return;
		}

		$retained_ids = array_values( array_unique( array_filter( array_map( 'absint', $retained_ids ) ) ) );

		if ( $done || empty( $retained_ids ) ) {
			delete_transient( $state_key );
			return;
		}

		set_transient( $state_key, $retained_ids, HOUR_IN_SECONDS );
	}

	private static function iata_code( string $value ): string {
		$value = preg_replace( '/[^A-Z]/', '', strtoupper( trim( $value ) ) );
		$value = is_string( $value ) ? $value : '';

		return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
	}
}
