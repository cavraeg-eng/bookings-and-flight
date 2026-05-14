<?php
/**
 * Admin reports page.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Admin;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Reports\Reporting_Service;

defined( 'ABSPATH' ) || exit;

final class Reports_Page {

	public static function render(): void {
		if ( ! current_user_can( Capability_Manager::VIEW_REPORTS ) ) {
			wp_die( esc_html__( 'You do not have permission to access Bookings and Flights reports.', 'bookings-flights-core' ) );
		}

		$days      = self::reports_days();
		$service   = new Reporting_Service();
		$dashboard = $service->dashboard( $days );

		if ( true === self::should_export_reports() ) {
			self::send_reports_export( $service->export_rows( $days ), $days );
		}
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Bookings and Flights Reports', 'bookings-flights-core' ); ?></h1>
			<p class="baf-admin__intro"><?php echo esc_html__( 'Review privacy-aware WordPress-local reporting for affiliate clicks, AI sessions, provider status, and content performance. Raw IP addresses, user agents, prompts, provider secrets, and private customer data are not shown.', 'bookings-flights-core' ); ?></p>

			<form method="get" class="baf-report-filters">
				<input type="hidden" name="page" value="<?php echo esc_attr( Admin_Manager::REPORTS_SLUG ); ?>" />
				<label for="baf_report_days"><?php echo esc_html__( 'Date range', 'bookings-flights-core' ); ?></label>
				<select id="baf_report_days" name="days">
					<?php foreach ( array( 7, 30, 90, 365 ) as $option ) : ?>
						<option value="<?php echo esc_attr( (string) $option ); ?>" <?php selected( $days, $option ); ?>>
							<?php
							printf(
								/* translators: %d: number of days. */
								esc_html__( 'Last %d days', 'bookings-flights-core' ),
								(int) $option
							);
							?>
						</option>
					<?php endforeach; ?>
				</select>
				<?php submit_button( __( 'Apply', 'bookings-flights-core' ), 'secondary', '', false ); ?>
				<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => Admin_Manager::REPORTS_SLUG, 'days' => $days, 'baf_export' => 'csv' ), admin_url( 'admin.php' ) ), 'baf_export_reports' ) ); ?>"><?php echo esc_html__( 'Export CSV', 'bookings-flights-core' ); ?></a>
			</form>

			<div class="baf-status-grid baf-report-summary" role="status" aria-label="<?php echo esc_attr__( 'Bookings and Flights report summary', 'bookings-flights-core' ); ?>">
				<?php self::render_metric_card( __( 'Affiliate clicks', 'bookings-flights-core' ), (int) $dashboard['clicks']['total'], __( 'Signed handoffs recorded locally.', 'bookings-flights-core' ) ); ?>
				<?php self::render_metric_card( __( 'AI sessions', 'bookings-flights-core' ), (int) $dashboard['ai_sessions']['total'], __( 'Generation runs logged without raw prompts.', 'bookings-flights-core' ) ); ?>
				<?php self::render_metric_card( __( 'Provider snapshots', 'bookings-flights-core' ), count( $dashboard['provider_stats'] ), __( 'Latest local provider status records.', 'bookings-flights-core' ) ); ?>
			</div>

			<div class="baf-notice baf-notice--warning" role="status">
				<strong><?php echo esc_html__( 'Revenue and conversion signals:', 'bookings-flights-core' ); ?></strong>
				<?php echo esc_html( (string) $dashboard['conversion']['message'] ); ?>
			</div>

			<?php
			self::render_click_reports( (array) $dashboard['clicks'] );
			self::render_subid_reports( (array) $dashboard['subid_map'], (array) $dashboard['clicks']['by_subid'] );
			self::render_ai_reports( (array) $dashboard['ai_sessions'] );
			self::render_provider_reports( (array) $dashboard['provider_stats'] );
			self::render_content_reports( (array) $dashboard['content'] );
			?>
		</div>
		<?php
	}

	private static function render_metric_card( string $title, int $value, string $description ): void {
		?>
		<section class="baf-status-card baf-report-card" role="status" aria-label="<?php echo esc_attr( $title ); ?>">
			<div class="baf-status-card__header">
				<h2><?php echo esc_html( $title ); ?></h2>
				<span class="baf-report-card__value"><?php echo esc_html( number_format_i18n( $value ) ); ?></span>
			</div>
			<p><?php echo esc_html( $description ); ?></p>
		</section>
		<?php
	}

	private static function render_click_reports( array $clicks ): void {
		?>
		<div class="baf-report-grid">
			<?php self::render_count_table( __( 'Clicks by provider', 'bookings-flights-core' ), (array) $clicks['by_provider'], __( 'Provider', 'bookings-flights-core' ), __( 'Clicks', 'bookings-flights-core' ) ); ?>
			<section class="baf-report-panel">
				<h2><?php echo esc_html__( 'Top clicked content', 'bookings-flights-core' ); ?></h2>
				<?php if ( empty( $clicks['top_content'] ) ) : ?>
					<p class="description"><?php echo esc_html__( 'No click records are available for this date range.', 'bookings-flights-core' ); ?></p>
				<?php else : ?>
					<table class="widefat striped">
						<thead>
							<tr>
								<th scope="col"><?php echo esc_html__( 'Content', 'bookings-flights-core' ); ?></th>
								<th scope="col"><?php echo esc_html__( 'Type', 'bookings-flights-core' ); ?></th>
								<th scope="col"><?php echo esc_html__( 'Clicks', 'bookings-flights-core' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $clicks['top_content'] as $row ) : ?>
								<tr>
									<td><?php echo esc_html( (string) $row['title'] ); ?></td>
									<td><?php echo esc_html( (string) $row['post_type'] ); ?></td>
									<td><?php echo esc_html( number_format_i18n( (int) $row['clicks'] ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</section>
		</div>
		<?php
	}

	private static function render_subid_reports( array $subid_map, array $observed_subids ): void {
		?>
		<section class="baf-report-panel">
			<h2><?php echo esc_html__( 'Travelpayouts SubID reporting map', 'bookings-flights-core' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'These readable SubIDs map major Bookings and Flights surfaces to Travelpayouts reporting. They contain placement context only; never names, emails, IP addresses, raw prompts, private trip details, or per-user identifiers.', 'bookings-flights-core' ); ?></p>
			<?php if ( empty( $subid_map ) ) : ?>
				<p class="description"><?php echo esc_html__( 'No SubID map entries are available yet.', 'bookings-flights-core' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__( 'Surface', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Placement', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Example SubID', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Status', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Source of truth', 'bookings-flights-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $subid_map as $row ) : ?>
							<tr>
								<td><?php echo esc_html( (string) $row['surface'] ); ?></td>
								<td>
									<strong><?php echo esc_html( (string) $row['placement_key'] ); ?></strong><br />
									<span class="description"><?php echo esc_html( (string) $row['placement_name'] ); ?></span>
								</td>
								<td><code><?php echo esc_html( (string) $row['example_subid'] ); ?></code></td>
								<td><?php echo esc_html( true === (bool) $row['configured'] ? (string) $row['status'] : __( 'mapped only', 'bookings-flights-core' ) ); ?></td>
								<td><?php echo esc_html( (string) $row['reporting_source'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</section>

		<section class="baf-report-panel">
			<h2><?php echo esc_html__( 'Observed local SubID clicks', 'bookings-flights-core' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Local click records are privacy-aware operational signals from signed handoff URLs. Missing rows mean unavailable local records for this date range, not zero Travelpayouts revenue or conversions.', 'bookings-flights-core' ); ?></p>
			<?php if ( empty( $observed_subids ) ) : ?>
				<p class="description"><?php echo esc_html__( 'No local SubID click records are available for this date range.', 'bookings-flights-core' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__( 'SubID', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Provider', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Target host', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Local clicks', 'bookings-flights-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $observed_subids as $row ) : ?>
							<tr>
								<td><code><?php echo esc_html( (string) $row['subid'] ); ?></code></td>
								<td><?php echo esc_html( (string) $row['provider'] ); ?></td>
								<td><?php echo esc_html( (string) $row['target_host'] ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $row['count'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</section>
		<?php
	}

	private static function render_ai_reports( array $sessions ): void {
		?>
		<div class="baf-report-grid">
			<?php self::render_count_table( __( 'AI sessions by status', 'bookings-flights-core' ), (array) $sessions['by_status'], __( 'Status', 'bookings-flights-core' ), __( 'Sessions', 'bookings-flights-core' ) ); ?>
			<?php self::render_count_table( __( 'AI sessions by mode', 'bookings-flights-core' ), (array) $sessions['by_mode'], __( 'Mode', 'bookings-flights-core' ), __( 'Sessions', 'bookings-flights-core' ) ); ?>
		</div>
		<?php
	}

	private static function render_provider_reports( array $stats ): void {
		?>
		<section class="baf-report-panel">
			<h2><?php echo esc_html__( 'Provider status snapshots', 'bookings-flights-core' ); ?></h2>
			<?php if ( empty( $stats ) ) : ?>
				<p class="description"><?php echo esc_html__( 'No provider status snapshots have been recorded yet. Run the provider stats cron job to populate this report.', 'bookings-flights-core' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__( 'Provider', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Status', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Metric', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Recorded', 'bookings-flights-core' ); ?></th>
							<th scope="col"><?php echo esc_html__( 'Message', 'bookings-flights-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $stats as $row ) : ?>
							<tr>
								<td><?php echo esc_html( (string) $row['provider'] ); ?></td>
								<td><?php echo esc_html( (string) $row['status'] ); ?></td>
								<td><?php echo esc_html( (string) $row['metric_key'] ); ?>: <?php echo esc_html( self::format_metric_value( $row['metric_value'] ?? null ) ); ?></td>
								<td><?php echo esc_html( self::format_datetime( (string) $row['recorded_at'] ) ); ?></td>
								<td><?php echo esc_html( (string) $row['message'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</section>
		<?php
	}

	private static function render_content_reports( array $rows ): void {
		?>
		<section class="baf-report-panel">
			<h2><?php echo esc_html__( 'Content performance inventory', 'bookings-flights-core' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th scope="col"><?php echo esc_html__( 'Post type', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Published', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Draft', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Pending', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Private', 'bookings-flights-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( (string) $row['post_type'] ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (int) $row['publish'] ) ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (int) $row['draft'] ) ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (int) $row['pending'] ) ); ?></td>
							<td><?php echo esc_html( number_format_i18n( (int) $row['private'] ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</section>
		<?php
	}

	private static function render_count_table( string $title, array $rows, string $label_header, string $count_header ): void {
		?>
		<section class="baf-report-panel">
			<h2><?php echo esc_html( $title ); ?></h2>
			<?php if ( empty( $rows ) ) : ?>
				<p class="description"><?php echo esc_html__( 'No records are available for this date range.', 'bookings-flights-core' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html( $label_header ); ?></th>
							<th scope="col"><?php echo esc_html( $count_header ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td><?php echo esc_html( '' !== (string) $row['label'] ? (string) $row['label'] : __( 'Unknown', 'bookings-flights-core' ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $row['count'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</section>
		<?php
	}

	private static function reports_days(): int {
		if ( ! isset( $_GET['days'] ) ) {
			return 30;
		}

		$days = absint( wp_unslash( $_GET['days'] ) );

		return in_array( $days, array( 7, 30, 90, 365 ), true ) ? $days : 30;
	}

	private static function should_export_reports(): bool {
		return isset( $_GET['baf_export'], $_GET['_wpnonce'] ) && 'csv' === sanitize_key( wp_unslash( $_GET['baf_export'] ) ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'baf_export_reports' );
	}

	private static function send_reports_export( array $rows, int $days ): void {
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=baf-reports-' . absint( $days ) . '-days.csv' );

		$output = fopen( 'php://output', 'w' );

		if ( false !== $output ) {
			foreach ( $rows as $row ) {
				fputcsv( $output, array_map( 'sanitize_text_field', $row ) );
			}

			fclose( $output );
		}

		exit;
	}

	private static function format_datetime( string $datetime ): string {
		$timestamp = strtotime( $datetime );

		if ( false === $timestamp ) {
			return '';
		}

		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
	}

	private static function format_metric_value( mixed $value ): string {
		if ( null === $value || '' === $value ) {
			return __( 'Unavailable', 'bookings-flights-core' );
		}

		if ( is_numeric( $value ) ) {
			return (string) (float) $value;
		}

		return sanitize_text_field( (string) $value );
	}
}
