<?php
/**
 * AI session logging repository.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Repositories;

use BAF\Core\Migrations\AI_Sessions_Table;

defined( 'ABSPATH' ) || exit;

final class AI_Session_Repository {

	public function start( array $context ): array|\WP_Error {
		global $wpdb;

		$run_uuid = wp_generate_uuid4();
		$inserted = $wpdb->insert(
			AI_Sessions_Table::table_name(),
			array(
				'run_uuid'       => $run_uuid,
				'created_at'     => current_time( 'mysql', true ),
				'user_id'        => get_current_user_id(),
				'provider'       => sanitize_key( (string) ( $context['provider'] ?? '' ) ),
				'mode'           => sanitize_key( (string) ( $context['mode'] ?? '' ) ),
				'status'         => 'running',
				'prompt_version' => sanitize_key( (string) ( $context['prompt_version'] ?? '' ) ),
				'source_post_id' => absint( $context['source_post_id'] ?? 0 ),
				'request_hash'   => $this->hash_payload( (array) ( $context['request'] ?? array() ) ),
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s' )
		);

		if ( false === $inserted ) {
			return new \WP_Error( 'baf_ai_log_failed', __( 'AI generation could not be logged.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		return array(
			'id'       => (int) $wpdb->insert_id,
			'run_uuid' => $run_uuid,
		);
	}

	public function finish( int $session_id, string $status, array $data = array() ): void {
		global $wpdb;

		$wpdb->update(
			AI_Sessions_Table::table_name(),
			array(
				'finished_at'    => current_time( 'mysql', true ),
				'status'         => $this->sanitize_status( $status ),
				'output_hash'    => $this->hash_payload( (array) ( $data['output'] ?? array() ) ),
				'output_summary' => substr( sanitize_text_field( (string) ( $data['summary'] ?? '' ) ), 0, 255 ),
				'error_code'     => sanitize_key( (string) ( $data['error_code'] ?? '' ) ),
				'error_message'  => substr( sanitize_text_field( (string) ( $data['error_message'] ?? '' ) ), 0, 255 ),
			),
			array( 'id' => $session_id ),
			array( '%s', '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);
	}

	private function sanitize_status( string $status ): string {
		return in_array( $status, array( 'success', 'failed' ), true ) ? $status : 'failed';
	}

	private function hash_payload( array $payload ): string {
		$encoded = wp_json_encode( $payload );

		return false === $encoded ? '' : hash_hmac( 'sha256', $encoded, wp_salt( 'auth' ) );
	}
}