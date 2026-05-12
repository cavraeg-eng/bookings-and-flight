<?php
/**
 * Background job status repository.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Jobs;

defined( 'ABSPATH' ) || exit;

final class Job_Repository {

	private const OPTION = 'baf_job_status';
	private const MAX_RECORDS = 40;
	private const MAX_ATTEMPTS = 3;

	public function all(): array {
		$records = get_option( self::OPTION, array() );

		return is_array( $records ) ? $records : array();
	}

	public function get( string $job_key ): array {
		$records = $this->all();

		return is_array( $records[ $job_key ] ?? null ) ? $records[ $job_key ] : array();
	}

	public function start( string $job_key ): bool|\WP_Error {
		$records = $this->all();
		$record  = is_array( $records[ $job_key ] ?? null ) ? $records[ $job_key ] : array();

		if ( $this->is_locked( $record ) ) {
			return new \WP_Error( 'baf_job_locked', __( 'This background job is already running.', 'bookings-flights-core' ) );
		}

		$attempts = absint( $record['attempts'] ?? 0 );

		if ( 'failed' === (string) ( $record['status'] ?? '' ) && $attempts >= self::MAX_ATTEMPTS && ! $this->retry_window_elapsed( $record ) ) {
			return new \WP_Error( 'baf_job_retry_limited', __( 'This background job reached the retry limit for the current retry window.', 'bookings-flights-core' ) );
		}

		if ( $this->retry_window_elapsed( $record ) ) {
			$attempts = 0;
		}

		$record['status']     = 'running';
		$record['started_at'] = current_time( 'mysql', true );
		$record['locked_at']  = time();
		$record['message']    = __( 'Job started.', 'bookings-flights-core' );
		$record['attempts']   = $attempts + 1;

		$records[ $job_key ] = $record;
		$this->save( $records );

		return true;
	}

	public function finish( string $job_key, string $status, string $message, array $data = array() ): void {
		$records = $this->all();
		$record  = is_array( $records[ $job_key ] ?? null ) ? $records[ $job_key ] : array();

		unset( $record['locked_at'] );

		$record['status']      = $this->sanitize_status( $status );
		$record['finished_at'] = current_time( 'mysql', true );
		$record['message']     = sanitize_text_field( $message );
		$record['data']        = $this->sanitize_data( $data );

		if ( 'failed' === $record['status'] ) {
			$record['last_failed_at'] = $record['finished_at'];
		} else {
			$record['attempts'] = 0;
		}

		$records[ $job_key ] = $record;
		$this->save( $records );
	}

	public function cleanup(): int {
		$records = $this->all();
		$cutoff  = time() - ( 30 * DAY_IN_SECONDS );
		$removed = 0;

		foreach ( $records as $job_key => $record ) {
			if ( ! is_array( $record ) ) {
				unset( $records[ $job_key ] );
				++$removed;
				continue;
			}

			if ( $this->is_stale_lock( $record ) ) {
				unset( $records[ $job_key ]['locked_at'] );
				$records[ $job_key ]['status']  = 'failed';
				$records[ $job_key ]['message'] = __( 'A stale job lock was cleared.', 'bookings-flights-core' );
			}

			$finished_at = strtotime( (string) ( $record['finished_at'] ?? '' ) );

			if ( 'success' === (string) ( $record['status'] ?? '' ) && false !== $finished_at && $finished_at < $cutoff ) {
				unset( $records[ $job_key ] );
				++$removed;
			}
		}

		$this->save( $records );

		return $removed;
	}

	private function is_locked( array $record ): bool {
		return isset( $record['locked_at'] ) && ! $this->is_stale_lock( $record );
	}

	private function is_stale_lock( array $record ): bool {
		$locked_at = absint( $record['locked_at'] ?? 0 );

		return $locked_at > 0 && $locked_at < time() - ( 15 * MINUTE_IN_SECONDS );
	}

	private function retry_window_elapsed( array $record ): bool {
		$last_failed_at = strtotime( (string) ( $record['last_failed_at'] ?? '' ) );

		return false !== $last_failed_at && $last_failed_at < time() - HOUR_IN_SECONDS;
	}

	private function save( array $records ): void {
		update_option( self::OPTION, array_slice( $records, -self::MAX_RECORDS, null, true ), false );
	}

	private function sanitize_status( string $status ): string {
		return in_array( $status, array( 'success', 'failed', 'deferred', 'running' ), true ) ? $status : 'failed';
	}

	private function sanitize_data( array $data ): array {
		return map_deep( $data, 'sanitize_text_field' );
	}
}