<?php
/**
 * Personal data export and erasure hooks.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Privacy;

use BAF\Core\Migrations\AI_Sessions_Table;
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
		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return Personal_Data_Records::export_response();
		}

		$ids   = Personal_Data_Records::saved_trip_ids( $user_id, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$destination = Personal_Data_Records::meta_text( $post_id, 'baf_destination' );
			$origin      = Personal_Data_Records::meta_text( $post_id, 'baf_origin' );
			$depart_date = Personal_Data_Records::meta_text( $post_id, 'baf_departure_window' );
			$return_date = Personal_Data_Records::meta_text( $post_id, 'baf_return_window' );
			$links       = Personal_Data_Records::saved_trip_links( $post_id, $origin, $destination, $depart_date, $return_date );

			$items[] = array(
				'group_id'    => 'baf_saved_trips',
				'group_label' => __( 'Bookings and Flights Saved Trips', 'bookings-flights-core' ),
				'item_id'     => 'baf-saved-trip-' . $post_id,
				'data'        => Personal_Data_Records::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )             => get_the_title( $post_id ),
						__( 'Origin', 'bookings-flights-core' )            => $origin,
						__( 'Destination', 'bookings-flights-core' )       => $destination,
						__( 'Departure date', 'bookings-flights-core' )    => $depart_date,
						__( 'Return date', 'bookings-flights-core' )       => $return_date,
						__( 'Travelers', 'bookings-flights-core' )         => Personal_Data_Records::meta_text( $post_id, Saved_Trip_Service::META_TRAVELERS ),
						__( 'Travel style', 'bookings-flights-core' )      => Personal_Data_Records::meta_text( $post_id, 'baf_travel_style' ),
						__( 'Planning note', 'bookings-flights-core' )     => get_post_field( 'post_content', $post_id ),
						__( 'Saved at', 'bookings-flights-core' )          => Personal_Data_Records::meta_text( $post_id, Saved_Trip_Service::META_SAVED_AT ),
						__( 'Flight resume link', 'bookings-flights-core' ) => $links['flights'],
						__( 'Hotel resume link', 'bookings-flights-core' ) => $links['hotels'],
						__( 'Planner resume link', 'bookings-flights-core' ) => $links['planner'],
					)
				),
			);
		}

		return Personal_Data_Records::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_travel_alerts( string $email_address, int $page = 1 ): array {
		$email = Personal_Data_Records::valid_email( $email_address );

		if ( '' === $email ) {
			return Personal_Data_Records::export_response();
		}

		$ids   = Personal_Data_Records::travel_alert_ids( $email, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$origin      = Personal_Data_Records::meta_text( $post_id, 'baf_origin_airport' );
			$destination = Personal_Data_Records::meta_text( $post_id, 'baf_destination_airport' );

			$items[] = array(
				'group_id'    => 'baf_travel_alerts',
				'group_label' => __( 'Bookings and Flights Travel Alerts', 'bookings-flights-core' ),
				'item_id'     => 'baf-travel-alert-' . $post_id,
				'data'        => Personal_Data_Records::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )             => get_the_title( $post_id ),
						__( 'Email', 'bookings-flights-core' )             => Personal_Data_Records::meta_text( $post_id, 'baf_alert_email' ),
						__( 'Route', 'bookings-flights-core' )             => Personal_Data_Records::meta_text( $post_id, 'baf_alert_route' ),
						__( 'Origin airport', 'bookings-flights-core' )    => $origin,
						__( 'Destination airport', 'bookings-flights-core' ) => $destination,
						__( 'Departure window', 'bookings-flights-core' )  => Personal_Data_Records::meta_text( $post_id, 'baf_departure_window' ),
						__( 'Return window', 'bookings-flights-core' )     => Personal_Data_Records::meta_text( $post_id, 'baf_return_window' ),
						__( 'Frequency', 'bookings-flights-core' )         => Personal_Data_Records::meta_text( $post_id, 'baf_alert_frequency' ),
						__( 'Travelers', 'bookings-flights-core' )         => Personal_Data_Records::meta_text( $post_id, 'baf_alert_travelers' ),
						__( 'Cabin', 'bookings-flights-core' )             => Personal_Data_Records::meta_text( $post_id, 'baf_alert_cabin' ),
						__( 'Status', 'bookings-flights-core' )            => Personal_Data_Records::meta_text( $post_id, 'baf_alert_status' ),
						__( 'Email status', 'bookings-flights-core' )      => Personal_Data_Records::meta_text( $post_id, 'baf_alert_email_status' ),
						__( 'Consent timestamp', 'bookings-flights-core' ) => Personal_Data_Records::meta_text( $post_id, 'baf_alert_consent_at' ),
						__( 'Last email attempt', 'bookings-flights-core' ) => Personal_Data_Records::meta_text( $post_id, 'baf_alert_email_last_attempt_at' ),
						__( 'Resume flight search', 'bookings-flights-core' ) => Personal_Data_Records::alert_resume_link( $origin, $destination ),
					)
				),
			);
		}

		return Personal_Data_Records::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_ai_trip_plans( string $email_address, int $page = 1 ): array {
		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return Personal_Data_Records::export_response();
		}

		$ids   = Personal_Data_Records::ai_trip_plan_ids( $user_id, $page, self::EXPORT_PAGE_SIZE );
		$items = array();

		foreach ( $ids as $post_id ) {
			$items[] = array(
				'group_id'    => 'baf_ai_trip_plans',
				'group_label' => __( 'Bookings and Flights AI Trip Plans', 'bookings-flights-core' ),
				'item_id'     => 'baf-ai-trip-plan-' . $post_id,
				'data'        => Personal_Data_Records::data_rows(
					array(
						__( 'Title', 'bookings-flights-core' )          => get_the_title( $post_id ),
						__( 'Summary', 'bookings-flights-core' )        => get_post_field( 'post_excerpt', $post_id ),
						__( 'Origin', 'bookings-flights-core' )         => Personal_Data_Records::meta_text( $post_id, 'baf_origin' ),
						__( 'Destination', 'bookings-flights-core' )    => Personal_Data_Records::meta_text( $post_id, 'baf_destination' ),
						__( 'Departure date', 'bookings-flights-core' ) => Personal_Data_Records::meta_text( $post_id, 'baf_departure_window' ),
						__( 'Return date', 'bookings-flights-core' )    => Personal_Data_Records::meta_text( $post_id, 'baf_return_window' ),
						__( 'Travel style', 'bookings-flights-core' )   => Personal_Data_Records::meta_text( $post_id, 'baf_travel_style' ),
						__( 'AI run ID', 'bookings-flights-core' )      => Personal_Data_Records::meta_text( $post_id, 'baf_ai_source_session_id' ),
						__( 'Local draft link', 'bookings-flights-core' ) => Personal_Data_Records::edit_post_link( $post_id ),
					)
				),
			);
		}

		return Personal_Data_Records::export_response( $items, count( $ids ) < self::EXPORT_PAGE_SIZE );
	}

	public static function export_ai_sessions( string $email_address, int $page = 1 ): array {
		global $wpdb;

		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 || ! Personal_Data_Records::ai_sessions_table_exists() ) {
			return Personal_Data_Records::export_response();
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
			$source_link    = Personal_Data_Records::owned_trip_plan_link( $source_post_id, $user_id );

			$items[] = array(
				'group_id'    => 'baf_ai_sessions',
				'group_label' => __( 'Bookings and Flights AI Session Logs', 'bookings-flights-core' ),
				'item_id'     => 'baf-ai-session-' . absint( $row['id'] ?? 0 ),
				'data'        => Personal_Data_Records::data_rows(
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

		return Personal_Data_Records::export_response( $items, count( (array) $rows ) < self::EXPORT_PAGE_SIZE );
	}

	public static function erase_saved_trips( string $email_address, int $page = 1 ): array {
		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return Personal_Data_Records::erase_response();
		}

		return Personal_Data_Records::erase_posts( Personal_Data_Records::saved_trip_query_args( $user_id, 1, self::ERASE_PAGE_SIZE ), self::ERASE_PAGE_SIZE );
	}

	public static function erase_travel_alerts( string $email_address, int $page = 1 ): array {
		$email = Personal_Data_Records::valid_email( $email_address );

		if ( '' === $email ) {
			return Personal_Data_Records::erase_response();
		}

		return Personal_Data_Records::erase_posts( Personal_Data_Records::travel_alert_query_args( $email, 1, self::ERASE_PAGE_SIZE ), self::ERASE_PAGE_SIZE );
	}

	public static function erase_ai_trip_plans( string $email_address, int $page = 1 ): array {
		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 ) {
			return Personal_Data_Records::erase_response();
		}

		return Personal_Data_Records::erase_posts( Personal_Data_Records::ai_trip_plan_query_args( $user_id, 1, self::ERASE_PAGE_SIZE ), self::ERASE_PAGE_SIZE );
	}

	public static function erase_ai_sessions( string $email_address, int $page = 1 ): array {
		global $wpdb;

		$user_id = Personal_Data_Records::user_id_for_email( $email_address );

		if ( $user_id <= 0 || ! Personal_Data_Records::ai_sessions_table_exists() ) {
			return Personal_Data_Records::erase_response();
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
			return Personal_Data_Records::erase_response();
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
			return Personal_Data_Records::erase_response(
				false,
				true,
				array( __( 'Some Bookings and Flights AI session logs could not be anonymized.', 'bookings-flights-core' ) ),
				true
			);
		}

		return Personal_Data_Records::erase_response( true, false, array(), count( $ids ) < self::ERASE_PAGE_SIZE );
	}

}
