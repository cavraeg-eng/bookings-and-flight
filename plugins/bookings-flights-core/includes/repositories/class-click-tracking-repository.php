<?php
/**
 * Affiliate click tracking repository.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Repositories;

use BAF\Core\Migrations\Clicks_Table;

defined( 'ABSPATH' ) || exit;

final class Click_Tracking_Repository {

	public function record( array $event ): bool|\WP_Error {
		global $wpdb;

		$target_url = esc_url_raw( (string) ( $event['target_url'] ?? '' ) );
		$target_host = (string) wp_parse_url( $target_url, PHP_URL_HOST );

		$inserted = $wpdb->insert(
			Clicks_Table::table_name(),
			array(
				'event_uuid'      => wp_generate_uuid4(),
				'occurred_at'     => current_time( 'mysql', true ),
				'post_id'         => absint( $event['post_id'] ?? 0 ),
				'provider'        => sanitize_key( (string) ( $event['provider'] ?? '' ) ),
				'subid'           => $this->sanitize_subid( (string) ( $event['subid'] ?? '' ) ),
				'target_host'     => sanitize_text_field( $target_host ),
				'user_id'         => get_current_user_id(),
				'ip_hash'         => $this->hash_value( $this->server_value( 'REMOTE_ADDR' ) ),
				'user_agent_hash' => $this->hash_value( $this->server_value( 'HTTP_USER_AGENT' ) ),
				'referer_hash'    => $this->hash_value( $this->server_value( 'HTTP_REFERER' ) ),
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			return new \WP_Error( 'baf_click_log_failed', __( 'Affiliate click could not be recorded.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		do_action( 'baf_after_affiliate_click_logged', $wpdb->insert_id, $event );

		return true;
	}

	private function sanitize_subid( string $subid ): string {
		$subid = strtolower( $subid );
		$subid = preg_replace( '/[^a-z0-9_]+/', '_', $subid );
		$subid = trim( (string) $subid, '_' );

		return substr( $subid, 0, 191 );
	}

	private function server_value( string $key ): string {
		if ( ! isset( $_SERVER[ $key ] ) ) {
			return '';
		}

		$value = wp_unslash( $_SERVER[ $key ] );

		if ( is_array( $value ) ) {
			return '';
		}

		return sanitize_text_field( (string) $value );
	}

	private function hash_value( string $value ): string {
		if ( '' === $value ) {
			return '';
		}

		return hash_hmac( 'sha256', $value, wp_salt( 'auth' ) );
	}
}