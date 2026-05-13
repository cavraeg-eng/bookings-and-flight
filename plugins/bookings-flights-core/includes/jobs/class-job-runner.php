<?php
/**
 * Background job handlers.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Jobs;

use BAF\Core\Reports\Provider_Stats_Repository;
use BAF\Core\Services\Flight_Alert_Service;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Job_Runner {

	public static function refresh_cached_offers(): void {
		self::run(
			'refresh_cached_offers',
			static function (): array {
				$consent = Settings_Manager::get_consent();

				if ( true !== (bool) $consent['allow_provider_requests'] ) {
					return array(
						'status'  => 'deferred',
						'message' => __( 'Provider request consent is disabled, so cached offers were not refreshed.', 'bookings-flights-core' ),
						'data'    => array( 'reason' => 'missing_provider_consent' ),
					);
				}

				$query = new \WP_Query(
					array(
						'post_type'              => Post_Type_Registrar::TRAVEL_DEAL,
						'post_status'            => 'publish',
						'posts_per_page'         => 20,
						'fields'                 => 'ids',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					)
				);

				return array(
					'status'  => 'success',
					'message' => __( 'Cached offer refresh queue checked.', 'bookings-flights-core' ),
					'data'    => array( 'eligible_deals' => (string) count( $query->posts ) ),
				);
			}
		);
	}

	public static function process_travel_alerts(): void {
		self::run(
			'process_travel_alerts',
			static function (): array {
				return ( new Flight_Alert_Service() )->process_pending_alerts( 20 );
			}
		);
	}

	public static function sync_provider_stats(): void {
		self::run(
			'sync_provider_stats',
			static function (): array {
				$settings = Settings_Manager::get_travelpayouts();
				$consent  = Settings_Manager::get_consent();
				$status   = '' !== sanitize_text_field( (string) $settings['marker'] ) ? 'success' : 'deferred';
				$message  = 'success' === $status ? __( 'Provider stats status checked.', 'bookings-flights-core' ) : __( 'Provider stats sync deferred because Travelpayouts marker is missing.', 'bookings-flights-core' );

				$recorded = ( new Provider_Stats_Repository() )->record(
					array(
						'provider'     => 'travelpayouts',
						'status'       => $status,
						'metric_key'   => 'configuration_ready',
						'metric_value' => 'success' === $status ? 1 : 0,
						'message'      => $message,
						'source'       => true === (bool) $consent['allow_provider_requests'] ? 'local_consent_ready' : 'local_consent_disabled',
					)
				);

				if ( is_wp_error( $recorded ) ) {
					return array(
						'status'  => 'failed',
						'message' => __( 'Provider stats could not be recorded.', 'bookings-flights-core' ),
						'data'    => array( 'provider' => 'travelpayouts' ),
					);
				}

				return array(
					'status'  => $status,
					'message' => $message,
					'data'    => array( 'travelpayouts_configured' => 'success' === $status ? 'yes' : 'no' ),
				);
			}
		);
	}

	public static function cleanup_job_records(): void {
		self::run(
			'cleanup_job_records',
			static function ( Job_Repository $repository ): array {
				$removed = $repository->cleanup();

				return array(
					'status'  => 'success',
					'message' => __( 'Background job records cleaned up.', 'bookings-flights-core' ),
					'data'    => array( 'removed_records' => (string) $removed ),
				);
			}
		);
	}

	private static function run( string $job_key, callable $callback ): void {
		$repository = new Job_Repository();
		$started    = $repository->start( $job_key );

		if ( is_wp_error( $started ) ) {
			return;
		}

		try {
			$result = $callback( $repository );
			$repository->finish( $job_key, (string) $result['status'], (string) $result['message'], (array) ( $result['data'] ?? array() ) );
		} catch ( \Throwable $exception ) {
			$repository->finish( $job_key, 'failed', __( 'Background job failed safely.', 'bookings-flights-core' ), array( 'error_code' => self::safe_error_code( $exception ) ) );
		}
	}

	private static function safe_error_code( \Throwable $exception ): string {
		$code = $exception->getCode();

		if ( is_int( $code ) || is_string( $code ) ) {
			return sanitize_key( (string) $code );
		}

		return '';
	}
}
