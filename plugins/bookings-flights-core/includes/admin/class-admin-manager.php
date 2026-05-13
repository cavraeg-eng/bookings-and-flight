<?php
/**
 * Admin pages and assets.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Admin;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Cron\Cron_Manager;
use BAF\Core\Jobs\Job_Repository;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Admin_Manager {

	public const DASHBOARD_SLUG          = 'baf-dashboard';
	public const SETTINGS_SLUG           = 'baf-settings';
	public const INTEGRATIONS_SLUG       = 'baf-integrations';
	public const WIDGET_PLACEMENTS_SLUG  = 'baf-widget-placements';
	public const JOBS_SLUG               = 'baf-jobs';
	public const REPORTS_SLUG            = 'baf-reports';
	public const ASSET_HANDLE            = 'baf-admin';

	private const JOB_HOOK_MAP = array(
		Cron_Manager::HOOK_REFRESH_OFFERS => 'refresh_cached_offers',
		Cron_Manager::HOOK_PROCESS_ALERTS => 'process_travel_alerts',
		Cron_Manager::HOOK_SYNC_STATS     => 'sync_provider_stats',
		Cron_Manager::HOOK_CLEANUP        => 'cleanup_job_records',
	);

	private static array $screen_hooks = array();

	public static function bootstrap(): void {
		Widget_Placements_Page::bootstrap();
		add_action( 'admin_menu', array( self::class, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	public static function register_menu(): void {
		$dashboard_capability = self::dashboard_menu_capability();
		$dashboard_callback   = Capability_Manager::MANAGE_SETTINGS === $dashboard_capability ? array( self::class, 'render_dashboard' ) : array( Widget_Placements_Page::class, 'render' );

		$dashboard_hook = add_menu_page(
			__( 'Bookings and Flights', 'bookings-flights-core' ),
			__( 'Bookings & Flights', 'bookings-flights-core' ),
			$dashboard_capability,
			self::DASHBOARD_SLUG,
			$dashboard_callback,
			'dashicons-airplane',
			56
		);

		$settings_hook = add_submenu_page(
			self::DASHBOARD_SLUG,
			__( 'Settings', 'bookings-flights-core' ),
			__( 'Settings', 'bookings-flights-core' ),
			Capability_Manager::MANAGE_SETTINGS,
			self::SETTINGS_SLUG,
			array( self::class, 'render_settings' )
		);

		$integrations_hook = add_submenu_page(
			self::DASHBOARD_SLUG,
			__( 'Integrations', 'bookings-flights-core' ),
			__( 'Integrations', 'bookings-flights-core' ),
			Capability_Manager::MANAGE_SETTINGS,
			self::INTEGRATIONS_SLUG,
			array( self::class, 'render_integrations' )
		);

		$placements_capability = Widget_Placements_Page::menu_capability();
		$placements_hook       = '';

		if ( '' !== $placements_capability ) {
			$placements_hook = add_submenu_page(
				self::DASHBOARD_SLUG,
				__( 'Widget Placements', 'bookings-flights-core' ),
				__( 'Widget Placements', 'bookings-flights-core' ),
				$placements_capability,
				self::WIDGET_PLACEMENTS_SLUG,
				array( Widget_Placements_Page::class, 'render' )
			);
		}

		$jobs_hook = add_submenu_page(
			self::DASHBOARD_SLUG,
			__( 'Background Jobs', 'bookings-flights-core' ),
			__( 'Background Jobs', 'bookings-flights-core' ),
			Capability_Manager::MANAGE_SETTINGS,
			self::JOBS_SLUG,
			array( self::class, 'render_jobs' )
		);

		$reports_hook = add_submenu_page(
			self::DASHBOARD_SLUG,
			__( 'Reports', 'bookings-flights-core' ),
			__( 'Reports', 'bookings-flights-core' ),
			Capability_Manager::VIEW_REPORTS,
			self::REPORTS_SLUG,
			array( Reports_Page::class, 'render' )
		);

		self::$screen_hooks = array_filter( array( $dashboard_hook, $settings_hook, $integrations_hook, $placements_hook, $jobs_hook, $reports_hook ) );
	}

	private static function dashboard_menu_capability(): string {
		if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			return Capability_Manager::MANAGE_SETTINGS;
		}

		$placements_capability = Widget_Placements_Page::menu_capability();

		return '' === $placements_capability ? Capability_Manager::MANAGE_SETTINGS : $placements_capability;
	}

	public static function enqueue_assets( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, self::$screen_hooks, true ) ) {
			return;
		}

		wp_enqueue_style(
			self::ASSET_HANDLE,
			BAF_CORE_URL . 'assets/css/admin.css',
			array(),
			BAF_CORE_VERSION
		);
	}

	public static function render_dashboard(): void {
		if ( ! current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			wp_die( esc_html__( 'You do not have permission to access Bookings and Flights settings.', 'bookings-flights-core' ) );
		}

		$general       = Settings_Manager::get_general();
		$consent       = Settings_Manager::get_consent();
		$travelpayouts = Settings_Manager::get_travelpayouts();
		$ai            = Settings_Manager::get_ai();
		$bridge_status = self::affiliate_bridge_status();
		$ai_mode       = is_scalar( $ai['mode'] ?? '' ) ? (string) $ai['mode'] : '';
		$ai_provider   = is_scalar( $ai['provider'] ?? '' ) ? (string) $ai['provider'] : '';
		$ai_api_key    = is_scalar( $ai['api_key'] ?? '' ) ? (string) $ai['api_key'] : '';
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Bookings and Flights', 'bookings-flights-core' ); ?></h1>
			<p class="baf-admin__intro"><?php echo esc_html__( 'Manage the WordPress-native travel discovery, AI planning, SEO, and affiliate conversion foundation.', 'bookings-flights-core' ); ?></p>
			<nav class="baf-admin__actions" aria-label="<?php echo esc_attr__( 'Bookings and Flights admin shortcuts', 'bookings-flights-core' ); ?>">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ) ); ?>"><?php echo esc_html__( 'Review settings', 'bookings-flights-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::INTEGRATIONS_SLUG ) ); ?>"><?php echo esc_html__( 'Check integrations', 'bookings-flights-core' ); ?></a>
				<?php if ( Widget_Placements_Page::can_manage() ) : ?>
					<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::WIDGET_PLACEMENTS_SLUG ) ); ?>"><?php echo esc_html__( 'Manage widget placements', 'bookings-flights-core' ); ?></a>
				<?php endif; ?>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::JOBS_SLUG ) ); ?>"><?php echo esc_html__( 'View background jobs', 'bookings-flights-core' ); ?></a>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::REPORTS_SLUG ) ); ?>"><?php echo esc_html__( 'Open reports', 'bookings-flights-core' ); ?></a>
			</nav>

			<div class="baf-status-grid">
				<?php
				self::render_status_card(
					__( 'Core settings', 'bookings-flights-core' ),
					'' !== (string) $general['brand_name'],
					__( 'Brand basics are configured.', 'bookings-flights-core' ),
					__( 'Add a brand name before publishing customer-facing surfaces.', 'bookings-flights-core' )
				);
				self::render_status_card(
					__( 'Consent gates', 'bookings-flights-core' ),
					true === (bool) $consent['allow_provider_requests'] || true === (bool) $consent['allow_external_ai'],
					__( 'At least one external data consent gate is enabled.', 'bookings-flights-core' ),
					__( 'External provider and AI consent gates are currently disabled.', 'bookings-flights-core' )
				);
				self::render_status_card(
					__( 'Travelpayouts core adapter', 'bookings-flights-core' ),
					'' !== (string) $travelpayouts['marker'] && '' !== (string) $travelpayouts['api_token'],
					__( 'Core Travelpayouts credentials are configured.', 'bookings-flights-core' ),
					__( 'Core Travelpayouts credentials are missing or incomplete.', 'bookings-flights-core' )
				);
				self::render_status_card(
					__( 'Affiliate bridge', 'bookings-flights-core' ),
					true === $bridge_status['configured'],
					$bridge_status['message'],
					$bridge_status['message']
				);
				self::render_status_card(
					__( 'AI mode', 'bookings-flights-core' ),
					'demo' === $ai_mode || ( '' !== $ai_provider && '' !== $ai_api_key ),
					'demo' === $ai_mode ? __( 'Demo mode is available without live credentials.', 'bookings-flights-core' ) : __( 'Live AI provider settings are configured.', 'bookings-flights-core' ),
					__( 'Live AI mode needs a provider and API key.', 'bookings-flights-core' )
				);
				?>
			</div>
		</div>
		<?php
	}

	public static function render_settings(): void {
		self::render_settings_form(
			__( 'Bookings and Flights Settings', 'bookings-flights-core' ),
			Settings_Manager::core_group(),
			self::SETTINGS_SLUG,
			__( 'Save settings', 'bookings-flights-core' )
		);
	}

	public static function render_integrations(): void {
		if ( ! current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			wp_die( esc_html__( 'You do not have permission to access Bookings and Flights integrations.', 'bookings-flights-core' ) );
		}

		$bridge_status = self::affiliate_bridge_status();
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Bookings and Flights Integrations', 'bookings-flights-core' ); ?></h1>
			<?php settings_errors(); ?>

			<div class="baf-notice baf-notice--<?php echo true === $bridge_status['configured'] ? 'success' : 'warning'; ?>" role="status">
				<strong><?php echo esc_html__( 'Affiliate bridge:', 'bookings-flights-core' ); ?></strong>
				<?php echo esc_html( $bridge_status['message'] ); ?>
				<?php if ( true === $bridge_status['has_page'] ) : ?>
					<a href="<?php echo esc_url( admin_url( 'options-general.php?page=baf-affiliate-bridge' ) ); ?>"><?php echo esc_html__( 'Open bridge settings', 'bookings-flights-core' ); ?></a>
				<?php endif; ?>
			</div>

			<form method="post" action="options.php" class="baf-settings-form">
				<?php
				settings_fields( Settings_Manager::integrations_group() );
				do_settings_sections( self::INTEGRATIONS_SLUG );
				submit_button( __( 'Save integrations', 'bookings-flights-core' ) );
				?>
			</form>
		</div>
		<?php
	}

	public static function render_jobs(): void {
		if ( ! current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			wp_die( esc_html__( 'You do not have permission to access Bookings and Flights background jobs.', 'bookings-flights-core' ) );
		}

		$repository  = new Job_Repository();
		$records     = $repository->all();
		$definitions = Cron_Manager::event_definitions();
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Bookings and Flights Background Jobs', 'bookings-flights-core' ); ?></h1>
			<p class="baf-admin__intro"><?php echo esc_html__( 'Monitor scheduled automation for cached offers, travel alerts, provider stats, retries, and cleanup. Provider calls are deferred until consent and integrations are ready.', 'bookings-flights-core' ); ?></p>

			<div class="baf-admin-table-wrap">
				<table class="widefat striped baf-jobs-table">
					<caption class="screen-reader-text"><?php echo esc_html__( 'Bookings and Flights scheduled background job status', 'bookings-flights-core' ); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__( 'Job', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Schedule', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Next run', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Last status', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Message', 'bookings-flights-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $definitions as $hook => $definition ) : ?>
							<?php $record = self::job_record_for_hook( $records, (string) $hook ); ?>
							<tr>
								<th scope="row">
									<strong><?php echo esc_html( (string) $definition['label'] ); ?></strong>
									<p class="description"><?php echo esc_html( (string) $definition['description'] ); ?></p>
									<code><?php echo esc_html( (string) $hook ); ?></code>
								</th>
								<td data-label="<?php echo esc_attr__( 'Schedule', 'bookings-flights-core' ); ?>"><?php echo esc_html( (string) $definition['recurrence'] ); ?></td>
								<td data-label="<?php echo esc_attr__( 'Next run', 'bookings-flights-core' ); ?>"><?php echo esc_html( self::format_next_run( (string) $hook ) ); ?></td>
								<td data-label="<?php echo esc_attr__( 'Last status', 'bookings-flights-core' ); ?>"><?php echo esc_html( self::format_job_status( $record ) ); ?></td>
								<td data-label="<?php echo esc_attr__( 'Message', 'bookings-flights-core' ); ?>"><?php echo esc_html( (string) ( $record['message'] ?? __( 'No runs recorded yet.', 'bookings-flights-core' ) ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	private static function render_settings_form( string $title, string $group, string $page, string $button_label ): void {
		if ( ! current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			wp_die( esc_html__( 'You do not have permission to access Bookings and Flights settings.', 'bookings-flights-core' ) );
		}
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php settings_errors(); ?>
			<form method="post" action="options.php" class="baf-settings-form">
				<?php
				settings_fields( $group );
				do_settings_sections( $page );
				submit_button( $button_label );
				?>
			</form>
		</div>
		<?php
	}

	private static function render_status_card( string $title, bool $is_success, string $success_message, string $warning_message ): void {
		$status = true === $is_success ? __( 'Ready', 'bookings-flights-core' ) : __( 'Needs attention', 'bookings-flights-core' );
		?>
		<section class="baf-status-card baf-status-card--<?php echo true === $is_success ? 'success' : 'warning'; ?>" role="status" aria-label="<?php echo esc_attr( $title . ': ' . $status ); ?>">
			<div class="baf-status-card__header">
				<h2><?php echo esc_html( $title ); ?></h2>
				<span class="baf-status-card__badge"><?php echo esc_html( $status ); ?></span>
			</div>
			<p><?php echo esc_html( true === $is_success ? $success_message : $warning_message ); ?></p>
		</section>
		<?php
	}

	private static function format_next_run( string $hook ): string {
		$timestamp = wp_next_scheduled( $hook );

		if ( false === $timestamp ) {
			return __( 'Not scheduled', 'bookings-flights-core' );
		}

		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}

	private static function format_job_status( array $record ): string {
		$status = sanitize_key( (string) ( $record['status'] ?? '' ) );

		if ( '' === $status ) {
			return __( 'Pending', 'bookings-flights-core' );
		}

		if ( empty( $record['finished_at'] ) ) {
			return $status;
		}

		$finished_at = strtotime( (string) $record['finished_at'] );

		if ( false === $finished_at ) {
			return $status;
		}

		return sprintf(
			/* translators: 1: job status, 2: last run date. */
			__( '%1$s at %2$s', 'bookings-flights-core' ),
			$status,
			wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $finished_at )
		);
	}

	private static function job_record_for_hook( array $records, string $hook ): array {
		$job_key = (string) ( self::JOB_HOOK_MAP[ $hook ] ?? $hook );

		return is_array( $records[ $job_key ] ?? null ) ? $records[ $job_key ] : array();
	}

	private static function affiliate_bridge_status(): array {
		if ( ! defined( 'BAF_OPT_SUPPLIER_CREDS' ) ) {
			return array(
				'configured' => false,
				'has_page'   => false,
				'message'    => __( 'Affiliate bridge plugin is not active.', 'bookings-flights-core' ),
			);
		}

		$credentials = (array) get_option( BAF_OPT_SUPPLIER_CREDS, array() );
		$configured  = false;

		foreach ( $credentials as $supplier_credentials ) {
			if ( ! is_array( $supplier_credentials ) ) {
				continue;
			}

			$filled = array_filter(
				$supplier_credentials,
				static function ( $value ): bool {
					return '' !== trim( (string) $value );
				}
			);

			if ( ! empty( $filled ) ) {
				$configured = true;
				break;
			}
		}

		return array(
			'configured' => $configured,
			'has_page'   => true,
			'message'    => true === $configured ? __( 'At least one affiliate bridge supplier has saved credentials.', 'bookings-flights-core' ) : __( 'No affiliate bridge supplier credentials are configured yet.', 'bookings-flights-core' ),
		);
	}
}
