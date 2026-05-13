<?php
/**
 * Personal data export and erasure hooks.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Privacy;

use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Services\Saved_Trip_Service;

defined( 'ABSPATH' ) || exit;

final class Personal_Data_Manager {

	private const EXPORT_PAGE_SIZE = 50;
	private const ERASE_PAGE_SIZE  = 25;

	public static function bootstrap(): void {
		add_filter( 'wp_privacy_personal_data_exporters', array( self::class, 'register_exporters' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( self::class, 'register_erasers' ) );
	}

	public static function register_exporters( array $exporters ): array {
		$exporters['baf-saved-trips'] = array(
			'exporter_friendly_name' => __( 'Bookings and Flights saved trips', 'bookings-flights-core' ),
			'callback'               => array( self::class, 'export_saved_trips' ),
		);
		$exporters['baf-travel-alerts'] = array(
			'exporter_friendly_name' => __( 'Bookings and Flights travel alerts', 'bookings-flights-core' ),
			'callback'               => array( self::class, 'export_travel_alerts' ),
		);
		$exporters['baf-ai-trip-plans'] = array(
			'exporter_friendly_name' => __( 'Bookings and Flights AI trip plans', 'bookings-flights-core' ),
			'callback'               => array( self::class, 'export_ai_trip_plans' ),
		);
		$exporters['baf-ai-sessions'] = array(
			'exporter_friendly_name' => __( 'Bookings and Flights AI session logs', 'bookings-flights-core' ),
			'callback'               => array( self::class, 'export_ai_sessions' ),
		);

		return $exporters;
	}

	public static function register_erasers( array $erasers ): array {
		$erasers['baf-saved-trips'] = array(
			'eraser_friendly_name' => __( 'Bookings and Flights saved trips', 'bookings-flights-core' ),
			'callback'             => array( self::class, 'erase_saved_trips' ),
		);
		$erasers['baf-travel-alerts'] = array(
			'eraser_friendly_name' => __( 'Bookings and Flights travel alerts', 'bookings-flights-core' ),
			'callback'             => array( self::class, 'erase_travel_alerts' ),
		);
		$erasers['baf-ai-trip-plans'] = array(
			'eraser_friendly_name' => __( 'Bookings and Flights AI trip plans', 'bookings-flights-core' ),
			'callback'             => array( self::class, 'erase_ai_trip_plans' ),
		);
		$erasers['baf-ai-sessions'] = array(
			'eraser_friendly_name' => __( 'Bookings and Flights AI session logs', 'bookings-flights-core' ),
			'callback'             => array( self::class, 'erase_ai_sessions' ),
		);

		return $erasers;
	}

	public static function export_saved_trips( string $email_address, int $page = 1 ): array {
		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return self::export_response();
		}

		$ids   = self::saved_trip_ids( $user_id, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$destination = self::meta_text( $post_id, 'baf_destination' );
			$origin      = self::meta_text( $post_id, 'baf_origin' );
			$depart_date = self::meta_text( $post_id, 'baf_departure_window' );
			$return_date = self::meta_text( $post_id, 'baf_return_window' );
			$links       = self::saved_trip_links( $post_id, $origin, $destination, $depart_date, $return_date );

			$items[] = array(
				'group_id'    => 'baf_saved_trips',
				'group_label' => __( 'Bookings and Flights Saved Trips', 'bookings-flights-core' ),
				'item_id'     => 'baf-saved-trip-' . $post_id,
				'data'        => self::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )             => get_the_title( $post_id ),
						__( 'Origin', 'bookings-flights-core' )            => $origin,
						__( 'Destination', 'bookings-flights-core' )       => $destination,
						__( 'Departure date', 'bookings-flights-core' )    => $depart_date,
						__( 'Return date', 'bookings-flights-core' )       => $return_date,
						__( 'Travelers', 'bookings-flights-core' )         => self::meta_text( $post_id, Saved_Trip_Service::META_TRAVELERS ),
						__( 'Travel style', 'bookings-flights-core' )      => self::meta_text( $post_id, 'baf_travel_style' ),
						__( 'Planning note', 'bookings-flights-core' )     => get_post_field( 'post_content', $post_id ),
						__( 'Saved at', 'bookings-flights-core' )          => self::meta_text( $post_id, Saved_Trip_Service::META_SAVED_AT ),
						__( 'Flight resume link', 'bookings-flights-core' ) => $links['flights'],
						__( 'Hotel resume link', 'bookings-flights-core' ) => $links['hotels'],
						__( 'Planner resume link', 'bookings-flights-core' ) => $links['planner'],
					)
				),
			);
		}

		return self::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_travel_alerts( string $email_address, int $page = 1 ): array {
		$email = self::valid_email( $email_address );

		if ( '' === $email ) {
			return self::export_response();
		}

		$ids   = self::travel_alert_ids( $email, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$origin      = self::meta_text( $post_id, 'baf_origin_airport' );
			$destination = self::meta_text( $post_id, 'baf_destination_airport' );

			$items[] = array(
				'group_id'    => 'baf_travel_alerts',
				'group_label' => __( 'Bookings and Flights Travel Alerts', 'bookings-flights-core' ),
				'item_id'     => 'baf-travel-alert-' . $post_id,
				'data'        => self::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )             => get_the_title( $post_id ),
						__( 'Email', 'bookings-flights-core' )             => self::meta_text( $post_id, 'baf_alert_email' ),
						__( 'Route', 'bookings-flights-core' )             => self::meta_text( $post_id, 'baf_alert_route' ),
						__( 'Origin airport', 'bookings-flights-core' )    => $origin,
						__( 'Destination airport', 'bookings-flights-core' ) => $destination,
						__( 'Departure window', 'bookings-flights-core' )  => self::meta_text( $post_id, 'baf_departure_window' ),
						__( 'Return window', 'bookings-flights-core' )     => self::meta_text( $post_id, 'baf_return_window' ),
						__( 'Frequency', 'bookings-flights-core' )         => self::meta_text( $post_id, 'baf_alert_frequency' ),
						__( 'Travelers', 'bookings-flights-core' )         => self::meta_text( $post_id, 'baf_alert_travelers' ),
						__( 'Cabin', 'bookings-flights-core' )             => self::meta_text( $post_id, 'baf_alert_cabin' ),
						__( 'Status', 'bookings-flights-core' )            => self::meta_text( $post_id, 'baf_alert_status' ),
						__( 'Email status', 'bookings-flights-core' )      => self::meta_text( $post_id, 'baf_alert_email_status' ),
						__( 'Consent timestamp', 'bookings-flights-core' ) => self::meta_text( $post_id, 'baf_alert_consent_at' ),
						__( 'Last email attempt', 'bookings-flights-core' ) => self::meta_text( $post_id, 'baf_alert_email_last_attempt_at' ),
						__( 'Resume flight search', 'bookings-flights-core' ) => self::alert_resume_link( $origin, $destination ),
					)
				),
			);
		}

		return self::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_ai_trip_plans( string $email_address, int $page = 1 ): array {
		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return self::export_response();
		}

		$ids   = self::ai_trip_plan_ids( $user_id, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$items[] = array(
				'group_id'    => 'baf_ai_trip_plans',
				'group_label' => __( 'Bookings and Flights AI Trip Plans', 'bookings-flights-core' ),
				'item_id'     => 'baf-ai-trip-plan-' . $post_id,
				'data'        => self::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )          => get_the_title( $post_id ),
						__( 'Summary', 'bookings-flights-core' )        => get_post_field( 'post_excerpt', $post_id ),
						__( 'Origin', 'bookings-flights-core' )         => self::meta_text( $post_id, 'baf_origin' ),
						__( 'Destination', 'bookings-flights-core' )    => self::meta_text( $post_id, 'baf_destination' ),
						__( 'Departure date', 'bookings-flights-core' ) => self::meta_text( $post_id, 'baf_departure_window' ),
						__( 'Return date', 'bookings-flights-core' )    => self::meta_text( $post_id, 'baf_return_window' ),
						__( 'Travel style', 'bookings-flights-core' )   => self::meta_text( $post_id, 'baf_travel_style' ),
						__( 'AI run ID', 'bookings-flights-core' )      => self::meta_text( $post_id, 'baf_ai_source_session_id' ),
						__( 'Local draft link', 'bookings-flights-core' ) => self::edit_post_link( $post_id ),
					)
				),
			);
		}

		return self::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_ai_sessions( string $email_address, int $page = 1 ): array {
		global $wpdb;

		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 || ! self::ai_sessions_table_exists() ) {
			return self::export_response();
		}

		$table  = AI_Sessions_Table::table_name();
		$offset = max( 0, ( $page - 1 ) * self::EXPORT_PAGE_SIZE );
		$rows   = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, run_uuid, created_at, finished_at, provider, mode, status, prompt_version, source_post_id, output_summary, error_code FROM {$table} WHERE user_id = %d ORDER BY id ASC LIMIT %d OFFSET %d",
				$user_id,
				self::EXPORT_PAGE_SIZE,
				$offset
			),
			ARRAY_A
		);
		$items  = array();

		foreach ( (array) $rows as $row ) {
			$source_post_id = absint( $row['source_post_id'] ?? 0 );
			$source_link    = self::owned_trip_plan_link( $source_post_id, $user_id );

			$items[] = array(
				'group_id'    => 'baf_ai_sessions',
				'group_label' => __( 'Bookings and Flights AI Session Logs', 'bookings-flights-core' ),
				'item_id'     => 'baf-ai-session-' . absint( $row['id'] ?? 0 ),
				'data'        => self::data_rows(
					array(
						__( 'Run ID', 'bookings-flights-core' )         => $row['run_uuid'] ?? '',
						__( 'Created at UTC', 'bookings-flights-core' ) => $row['created_at'] ?? '',
						__( 'Finished at UTC', 'bookings-flights-core' ) => $row['finished_at'] ?? '',
						__( 'Provider', 'bookings-flights-core' )       => $row['provider'] ?? '',
						__( 'Mode', 'bookings-flights-core' )           => $row['mode'] ?? '',
						__( 'Status', 'bookings-flights-core' )         => $row['status'] ?? '',
						__( 'Prompt version', 'bookings-flights-core' ) => $row['prompt_version'] ?? '',
						__( 'Output summary', 'bookings-flights-core' ) => $row['output_summary'] ?? '',
						__( 'Error code', 'bookings-flights-core' )     => $row['error_code'] ?? '',
						__( 'Source trip plan link', 'bookings-flights-core' ) => $source_link,
					)
				),
			);
		}

		return self::export_response( $items, count( (array) $rows ) < self::EXPORT_PAGE_SIZE );
	}

	public static function erase_saved_trips( string $email_address, int $page = 1 ): array {
		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return self::erase_response();
		}

		return self::erase_posts( self::saved_trip_query_args( $user_id, 1, self::ERASE_PAGE_SIZE ) );
	}

	public static function erase_travel_alerts( string $email_address, int $page = 1 ): array {
		$email = self::valid_email( $email_address );

		if ( '' === $email ) {
			return self::erase_response();
		}

		return self::erase_posts( self::travel_alert_query_args( $email, 1, self::ERASE_PAGE_SIZE ) );
	}

	public static function erase_ai_trip_plans( string $email_address, int $page = 1 ): array {
		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return self::erase_response();
		}

		return self::erase_posts( self::ai_trip_plan_query_args( $user_id, 1, self::ERASE_PAGE_SIZE ) );
	}

	public static function erase_ai_sessions( string $email_address, int $page = 1 ): array {
		global $wpdb;

		$user_id = self::user_id_for_email( $email_address );

		if ( $user_id <= 0 || ! self::ai_sessions_table_exists() ) {
			return self::erase_response();
		}

		$table = AI_Sessions_Table::table_name();
		$ids   = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE user_id = %d ORDER BY id ASC LIMIT %d",
				$user_id,
				self::ERASE_PAGE_SIZE
			)
		);

		if ( empty( $ids ) ) {
			return self::erase_response();
		}

		$ids          = array_map( 'absint', $ids );
		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$updated      = $wpdb->query(
			$wpdb->prepare(
				"UPDATE {$table} SET run_uuid = '', user_id = 0, source_post_id = 0, request_hash = '', output_hash = '', output_summary = '', error_message = '' WHERE id IN ({$placeholders})",
				...$ids
			)
		);

		if ( false === $updated ) {
			return self::erase_response(
				false,
				true,
				array( __( 'Some Bookings and Flights AI session logs could not be anonymized.', 'bookings-flights-core' ) ),
				true
			);
		}

		return self::erase_response( true, false, array(), count( $ids ) < self::ERASE_PAGE_SIZE );
	}

	private static function erase_posts( array $query_args ): array {
		$query_args['fields']                 = 'ids';
		$query_args['no_found_rows']          = true;
		$query_args['cache_results']          = false;
		$query_args['update_post_meta_cache'] = false;
		$query_args['update_post_term_cache'] = false;

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
			$messages[]     = sprintf(
				/* translators: %d: post ID. */
				__( 'Bookings and Flights record %d could not be erased.', 'bookings-flights-core' ),
				$post_id
			);
		}

		return self::erase_response( $items_removed, $items_retained, $messages, count( $ids ) < self::ERASE_PAGE_SIZE || $items_retained );
	}

	private static function saved_trip_ids( int $user_id, int $page, int $per_page ): array {
		$query = new \WP_Query( self::saved_trip_query_args( $user_id, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	private static function saved_trip_query_args( int $user_id, int $page, int $per_page ): array {
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

	private static function travel_alert_ids( string $email, int $page, int $per_page ): array {
		$query = new \WP_Query( self::travel_alert_query_args( $email, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	private static function travel_alert_query_args( string $email, int $page, int $per_page ): array {
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

	private static function ai_trip_plan_ids( int $user_id, int $page, int $per_page ): array {
		$query = new \WP_Query( self::ai_trip_plan_query_args( $user_id, $page, $per_page ) );

		return array_map( 'absint', $query->posts );
	}

	private static function ai_trip_plan_query_args( int $user_id, int $page, int $per_page ): array {
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
			'post_status'            => array( 'draft', 'private', 'pending', 'publish' ),
			'post_type'              => Post_Type_Registrar::TRIP_PLAN,
			'posts_per_page'         => $per_page,
			'no_found_rows'          => true,
			'cache_results'          => false,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		);
	}

	private static function saved_trip_links( int $post_id, string $origin, string $destination, string $depart_date, string $return_date ): array {
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

	private static function alert_resume_link( string $origin, string $destination ): string {
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

	private static function owned_trip_plan_link( int $post_id, int $user_id ): string {
		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post || Post_Type_Registrar::TRIP_PLAN !== $post->post_type || (int) $post->post_author !== $user_id ) {
			return '';
		}

		return self::edit_post_link( $post_id );
	}

	private static function edit_post_link( int $post_id ): string {
		$url = get_edit_post_link( $post_id, 'raw' );

		return is_string( $url ) ? esc_url_raw( $url ) : '';
	}

	private static function data_rows( array $rows ): array {
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

	private static function meta_text( int $post_id, string $meta_key ): string {
		return sanitize_text_field( (string) get_post_meta( $post_id, $meta_key, true ) );
	}

	private static function iata_code( string $value ): string {
		$value = preg_replace( '/[^A-Z]/', '', strtoupper( trim( $value ) ) );
		$value = is_string( $value ) ? $value : '';

		return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
	}

	private static function user_id_for_email( string $email_address ): int {
		$email = self::valid_email( $email_address );

		if ( '' === $email ) {
			return 0;
		}

		$user = get_user_by( 'email', $email );

		return $user instanceof \WP_User ? (int) $user->ID : 0;
	}

	private static function valid_email( string $email_address ): string {
		$email = sanitize_email( $email_address );

		return '' !== $email && is_email( $email ) ? $email : '';
	}

	private static function ai_sessions_table_exists(): bool {
		global $wpdb;

		$table = AI_Sessions_Table::table_name();

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
	}

	private static function export_response( array $items = array(), bool $done = true ): array {
		return array(
			'data' => $items,
			'done' => $done,
		);
	}

	private static function erase_response( bool $removed = false, bool $retained = false, array $messages = array(), bool $done = true ): array {
		return array(
			'items_removed'  => $removed,
			'items_retained' => $retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}
}
