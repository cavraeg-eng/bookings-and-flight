<?php

namespace BAF\Core\Reports;

use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Migrations\Clicks_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Reporting_Repository {

	private Provider_Stats_Repository $provider_stats;

	public function __construct( ?Provider_Stats_Repository $provider_stats = null ) {
		$this->provider_stats = $provider_stats ?? new Provider_Stats_Repository();
	}

	public function click_summary( int $days ): array {
		global $wpdb;

		$table  = Clicks_Table::table_name();
		$cutoff = $this->cutoff_datetime( $days );

		if ( ! $this->table_exists( $table ) ) {
			return array(
				'total'       => 0,
				'by_provider' => array(),
				'by_subid'    => array(),
				'top_content' => array(),
			);
		}

		$total       = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE occurred_at >= %s", $cutoff ) );
		$by_provider = (array) $wpdb->get_results( $wpdb->prepare( "SELECT provider, COUNT(*) AS clicks FROM {$table} WHERE occurred_at >= %s GROUP BY provider ORDER BY clicks DESC LIMIT 10", $cutoff ), ARRAY_A );
		$by_subid    = (array) $wpdb->get_results( $wpdb->prepare( "SELECT subid, provider, target_host, COUNT(*) AS clicks FROM {$table} WHERE occurred_at >= %s AND subid <> '' GROUP BY subid, provider, target_host ORDER BY clicks DESC LIMIT 25", $cutoff ), ARRAY_A );
		$top_content = (array) $wpdb->get_results( $wpdb->prepare( "SELECT post_id, COUNT(*) AS clicks FROM {$table} WHERE occurred_at >= %s AND post_id > 0 GROUP BY post_id ORDER BY clicks DESC LIMIT 10", $cutoff ), ARRAY_A );

		return array(
			'total'       => $total,
			'by_provider' => array_map( array( $this, 'sanitize_count_row' ), $by_provider ),
			'by_subid'    => array_map( array( $this, 'sanitize_subid_row' ), $by_subid ),
			'top_content' => $this->hydrate_top_content( $top_content ),
		);
	}

	public function ai_session_summary( int $days ): array {
		global $wpdb;

		$table  = AI_Sessions_Table::table_name();
		$cutoff = $this->cutoff_datetime( $days );

		if ( ! $this->table_exists( $table ) ) {
			return array(
				'total'     => 0,
				'by_status' => array(),
				'by_mode'   => array(),
			);
		}

		return array(
			'total'     => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s", $cutoff ) ),
			'by_status' => array_map( array( $this, 'sanitize_count_row' ), (array) $wpdb->get_results( $wpdb->prepare( "SELECT status AS provider, COUNT(*) AS clicks FROM {$table} WHERE created_at >= %s GROUP BY status ORDER BY clicks DESC LIMIT 10", $cutoff ), ARRAY_A ) ),
			'by_mode'   => array_map( array( $this, 'sanitize_count_row' ), (array) $wpdb->get_results( $wpdb->prepare( "SELECT mode AS provider, COUNT(*) AS clicks FROM {$table} WHERE created_at >= %s GROUP BY mode ORDER BY clicks DESC LIMIT 10", $cutoff ), ARRAY_A ) ),
		);
	}

	public function provider_stats(): array {
		return $this->provider_stats->latest( 10 );
	}

	public function content_summary(): array {
		$rows = array();

		foreach ( Post_Type_Registrar::keys() as $post_type ) {
			$counts = wp_count_posts( $post_type );

			$rows[] = array(
				'post_type' => $post_type,
				'publish'   => absint( $counts->publish ?? 0 ),
				'draft'     => absint( $counts->draft ?? 0 ),
				'pending'   => absint( $counts->pending ?? 0 ),
				'private'   => absint( $counts->private ?? 0 ),
			);
		}

		return $rows;
	}

	private function hydrate_top_content( array $rows ): array {
		$items = array();

		foreach ( $rows as $row ) {
			$post_id = absint( $row['post_id'] ?? 0 );
			$post    = get_post( $post_id );

			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			$items[] = array(
				'post_id'   => $post_id,
				'title'     => get_the_title( $post ),
				'post_type' => $post->post_type,
				'clicks'    => absint( $row['clicks'] ?? 0 ),
			);
		}

		return $items;
	}

	private function sanitize_count_row( array $row ): array {
		return array(
			'label' => sanitize_text_field( (string) ( $row['provider'] ?? '' ) ),
			'count' => absint( $row['clicks'] ?? 0 ),
		);
	}

	private function sanitize_subid_row( array $row ): array {
		return array(
			'subid'       => substr( preg_replace( '/[^a-z0-9_]+/', '_', strtolower( (string) ( $row['subid'] ?? '' ) ) ), 0, 191 ),
			'provider'    => sanitize_key( (string) ( $row['provider'] ?? '' ) ),
			'target_host' => sanitize_text_field( (string) ( $row['target_host'] ?? '' ) ),
			'count'       => absint( $row['clicks'] ?? 0 ),
		);
	}

	private function cutoff_datetime( int $days ): string {
		$timestamp = current_time( 'timestamp', true ) - ( max( 1, $days ) * DAY_IN_SECONDS );

		return wp_date( 'Y-m-d H:i:s', $timestamp, new \DateTimeZone( 'UTC' ) );
	}

	private function table_exists( string $table_name ): bool {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}
}
