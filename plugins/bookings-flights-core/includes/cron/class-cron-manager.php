<?php
/**
 * Cron registration and scheduling.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Cron;

use BAF\Core\Jobs\Job_Runner;

defined( 'ABSPATH' ) || exit;

final class Cron_Manager {

	public const HOOK_REFRESH_OFFERS = 'baf_refresh_cached_offers';
	public const HOOK_PROCESS_ALERTS = 'baf_process_travel_alerts';
	public const HOOK_SYNC_STATS     = 'baf_sync_provider_stats';
	public const HOOK_CLEANUP        = 'baf_cleanup_job_records';

	public const RECURRENCE_TEN_MINUTES = 'baf_ten_minutes';

	public static function bootstrap(): void {
		add_filter( 'cron_schedules', array( self::class, 'add_schedules' ) );
		add_action( self::HOOK_REFRESH_OFFERS, array( Job_Runner::class, 'refresh_cached_offers' ) );
		add_action( self::HOOK_PROCESS_ALERTS, array( Job_Runner::class, 'process_travel_alerts' ) );
		add_action( self::HOOK_SYNC_STATS, array( Job_Runner::class, 'sync_provider_stats' ) );
		add_action( self::HOOK_CLEANUP, array( Job_Runner::class, 'cleanup_job_records' ) );
	}

	public static function activate(): void {
		self::schedule_events();
	}

	public static function schedule_events(): void {
		self::schedule_event( self::HOOK_REFRESH_OFFERS, self::RECURRENCE_TEN_MINUTES, 5 * MINUTE_IN_SECONDS );
		self::schedule_event( self::HOOK_PROCESS_ALERTS, 'hourly', 10 * MINUTE_IN_SECONDS );
		self::schedule_event( self::HOOK_SYNC_STATS, 'hourly', 20 * MINUTE_IN_SECONDS );
		self::schedule_event( self::HOOK_CLEANUP, 'daily', HOUR_IN_SECONDS );
	}

	public static function unschedule_events(): void {
		foreach ( self::hooks() as $hook ) {
			wp_clear_scheduled_hook( $hook );
		}
	}

	public static function add_schedules( array $schedules ): array {
		if ( ! isset( $schedules[ self::RECURRENCE_TEN_MINUTES ] ) ) {
			$schedules[ self::RECURRENCE_TEN_MINUTES ] = array(
				'interval' => 10 * MINUTE_IN_SECONDS,
				'display'  => __( 'Every 10 minutes', 'bookings-flights-core' ),
			);
		}

		return $schedules;
	}

	public static function hooks(): array {
		return array(
			self::HOOK_REFRESH_OFFERS,
			self::HOOK_PROCESS_ALERTS,
			self::HOOK_SYNC_STATS,
			self::HOOK_CLEANUP,
		);
	}

	public static function event_definitions(): array {
		return array(
			self::HOOK_REFRESH_OFFERS => array(
				'label'       => __( 'Refresh cached offers', 'bookings-flights-core' ),
				'recurrence'  => self::RECURRENCE_TEN_MINUTES,
				'description' => __( 'Prepares cached offer refresh work without making provider calls during page render.', 'bookings-flights-core' ),
			),
			self::HOOK_PROCESS_ALERTS => array(
				'label'       => __( 'Process travel alerts', 'bookings-flights-core' ),
				'recurrence'  => 'hourly',
				'description' => __( 'Checks queued travel alert work and records safe deferrals until alert workflows are enabled.', 'bookings-flights-core' ),
			),
			self::HOOK_SYNC_STATS     => array(
				'label'       => __( 'Sync provider stats', 'bookings-flights-core' ),
				'recurrence'  => 'hourly',
				'description' => __( 'Maintains provider status telemetry for future reports without exposing credentials.', 'bookings-flights-core' ),
			),
			self::HOOK_CLEANUP        => array(
				'label'       => __( 'Clean up job records', 'bookings-flights-core' ),
				'recurrence'  => 'daily',
				'description' => __( 'Removes old successful job records and stale locks.', 'bookings-flights-core' ),
			),
		);
	}

	private static function schedule_event( string $hook, string $recurrence, int $offset ): void {
		if ( false !== wp_next_scheduled( $hook ) ) {
			return;
		}

		wp_schedule_event( time() + $offset, $recurrence, $hook );
	}
}