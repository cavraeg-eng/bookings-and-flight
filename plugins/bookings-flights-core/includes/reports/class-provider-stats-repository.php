<?php

namespace BAF\Core\Reports;

use BAF\Core\Migrations\Provider_Stats_Table;

defined( 'ABSPATH' ) || exit;

final class Provider_Stats_Repository {

	public function record( array $data ): bool|\WP_Error {
		global $wpdb;

		$values = array(
			'snapshot_uuid' => wp_generate_uuid4(),
			'recorded_at'   => current_time( 'mysql', true ),
			'provider'      => sanitize_key( (string) ( $data['provider'] ?? '' ) ),
			'status'        => $this->sanitize_status( (string) ( $data['status'] ?? '' ) ),
			'metric_key'    => sanitize_key( (string) ( $data['metric_key'] ?? '' ) ),
			'message'       => substr( sanitize_text_field( (string) ( $data['message'] ?? '' ) ), 0, 255 ),
			'source'        => sanitize_key( (string) ( $data['source'] ?? 'core' ) ),
		);
		$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' );

		if ( isset( $data['metric_value'] ) ) {
			$values['metric_value'] = (float) $data['metric_value'];
			$formats[]              = '%f';
		}

		$inserted = $wpdb->insert( Provider_Stats_Table::table_name(), $values, $formats );

		if ( false === $inserted ) {
			return new \WP_Error( 'baf_provider_stats_log_failed', __( 'Provider stats could not be recorded.', 'bookings-flights-core' ) );
		}

		return true;
	}

	public function latest( int $limit = 10 ): array {
		global $wpdb;

		$limit = min( 25, max( 1, absint( $limit ) ) );

		return (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT provider, status, metric_key, metric_value, message, source, recorded_at FROM " . Provider_Stats_Table::table_name() . ' ORDER BY recorded_at DESC, id DESC LIMIT %d',
				$limit
			),
			ARRAY_A
		);
	}

	private function sanitize_status( string $status ): string {
		return in_array( $status, array( 'success', 'deferred', 'failed' ), true ) ? $status : 'deferred';
	}
}