<?php
/**
 * Travelpayouts widget placement admin screen.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Admin;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Services\Travelpayouts_Widget_Registry_Service;

defined( 'ABSPATH' ) || exit;

final class Widget_Placements_Page {

	private const SAVE_ACTION          = 'baf_save_widget_placement';
	private const DELETE_ACTION        = 'baf_delete_widget_placement';
	private const NOTICE_TRANSIENT_KEY = 'baf_widget_placement_notice_';

	private const VERTICALS = array(
		'flights'    => 'Flights',
		'hotels'     => 'Hotels',
		'cars'       => 'Cars',
		'activities' => 'Activities',
		'packages'   => 'Packages',
		'route'      => 'Route',
		'destination' => 'Destination',
		'deal'       => 'Deal',
		'ai'         => 'AI',
		'saved_trip' => 'Saved trip',
	);

	private const WIDGET_FAMILIES = array(
		'white_label_search'  => 'White Label search',
		'white_label_results' => 'White Label results',
		'flight_form'         => 'Flight form',
		'popular_routes'      => 'Popular routes',
		'low_price_calendar'  => 'Low-price calendar',
		'route_map'           => 'Route map',
		'hotel_search'        => 'Hotel search',
		'hotel_map'           => 'Hotel map',
		'partner_link_card'   => 'Partner link card',
	);

	private const RENDER_MODES = array(
		'official_shortcode' => 'Official shortcode',
		'dashboard_script'   => 'Dashboard script',
		'iframe'             => 'Iframe',
		'handoff_link'       => 'Handoff link',
		'disabled'           => 'Disabled',
	);

	private const EMBED_SOURCES = array(
		'official_plugin'           => 'Official plugin',
		'travelpayouts_dashboard'   => 'Travelpayouts dashboard',
		'travelpayouts_white_label' => 'Travelpayouts White Label',
		'partner_program'           => 'Partner program',
		'none'                      => 'None',
	);

	private const STATUSES = array(
		'draft'    => 'Draft',
		'active'   => 'Active',
		'disabled' => 'Disabled',
		'archived' => 'Archived',
	);

	public static function bootstrap(): void {
		add_action( 'admin_post_' . self::SAVE_ACTION, array( self::class, 'handle_save' ) );
		add_action( 'admin_post_' . self::DELETE_ACTION, array( self::class, 'handle_delete' ) );
	}

	public static function menu_capability(): string {
		if ( current_user_can( Capability_Manager::MANAGE_AFFILIATES ) ) {
			return Capability_Manager::MANAGE_AFFILIATES;
		}

		if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
			return Capability_Manager::MANAGE_SETTINGS;
		}

		return '';
	}

	public static function can_manage(): bool {
		return current_user_can( Capability_Manager::MANAGE_AFFILIATES ) || current_user_can( Capability_Manager::MANAGE_SETTINGS );
	}

	public static function render(): void {
		if ( ! self::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to manage Travelpayouts widget placements.', 'bookings-flights-core' ) );
		}

		$service    = new Travelpayouts_Widget_Registry_Service();
		$placements = $service->all( true );

		if ( is_wp_error( $placements ) ) {
			self::render_error_page( $placements );
			return;
		}

		$editing_key = self::requested_placement_key();
		$editing     = Widget_Placement_Form::empty_placement();

		if ( '' !== $editing_key ) {
			$editing = $service->get( $editing_key, true );

			if ( is_wp_error( $editing ) ) {
				$editing = Widget_Placement_Form::empty_placement();
				self::set_notice( 'error', __( 'That widget placement could not be found. A new placement form is shown instead.', 'bookings-flights-core' ) );
			}
		}

		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Travelpayouts Widget Placements', 'bookings-flights-core' ); ?></h1>
			<p class="baf-admin__intro"><?php echo esc_html__( 'Manage the approved Travelpayouts widget, White Label, iframe, and handoff placements that WordPress can render later. Raw embed values are shown only on this capability-gated screen.', 'bookings-flights-core' ); ?></p>
			<?php self::render_notice(); ?>
			<?php self::render_summary( $placements ); ?>
			<?php self::render_table( $placements ); ?>
			<?php Widget_Placement_Form::render( $editing, self::SAVE_ACTION ); ?>
		</div>
		<?php
	}

	public static function handle_save(): void {
		if ( ! self::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to manage Travelpayouts widget placements.', 'bookings-flights-core' ) );
		}

		check_admin_referer( self::SAVE_ACTION );

		$service   = new Travelpayouts_Widget_Registry_Service();
		$placement = Widget_Placement_Form::posted_placement();
		$saved     = $service->save_placement( $placement );

		if ( is_wp_error( $saved ) ) {
			self::set_notice( 'error', $saved->get_error_message() );
			self::redirect( sanitize_key( (string) ( $placement['key'] ?? '' ) ) );
		}

		self::set_notice( 'success', __( 'Widget placement saved.', 'bookings-flights-core' ) );
		self::redirect( (string) $saved['key'] );
	}

	public static function handle_delete(): void {
		if ( ! self::can_manage() ) {
			wp_die( esc_html__( 'You do not have permission to manage Travelpayouts widget placements.', 'bookings-flights-core' ) );
		}

		$key = sanitize_key( (string) wp_unslash( $_POST['placement_key'] ?? '' ) );

		check_admin_referer( self::DELETE_ACTION . '_' . $key );

		$service = new Travelpayouts_Widget_Registry_Service();
		$deleted = $service->delete_placement( $key );

		if ( is_wp_error( $deleted ) ) {
			self::set_notice( 'error', $deleted->get_error_message() );
			self::redirect( $key );
		}

		self::set_notice( 'success', __( 'Widget placement deleted.', 'bookings-flights-core' ) );
		self::redirect();
	}

	private static function render_error_page( \WP_Error $error ): void {
		?>
		<div class="wrap baf-admin">
			<h1><?php echo esc_html__( 'Travelpayouts Widget Placements', 'bookings-flights-core' ); ?></h1>
			<div class="notice notice-error">
				<p><?php echo esc_html( $error->get_error_message() ); ?></p>
			</div>
		</div>
		<?php
	}

	private static function render_summary( array $placements ): void {
		$active     = 0;
		$disabled   = 0;
		$configured = 0;

		foreach ( $placements as $placement ) {
			if ( ! is_array( $placement ) ) {
				continue;
			}

			$status = (string) ( $placement['status'] ?? '' );

			if ( 'active' === $status ) {
				++$active;
			}

			if ( 'disabled' === $status ) {
				++$disabled;
			}

			if ( self::placement_is_configured( $placement ) ) {
				++$configured;
			}
		}

		?>
		<div class="baf-status-grid">
			<?php
			self::render_stat_card( __( 'Total placements', 'bookings-flights-core' ), count( $placements ), __( 'Approved placement records in the registry.', 'bookings-flights-core' ), true );
			self::render_stat_card( __( 'Active', 'bookings-flights-core' ), $active, __( 'Active placements can be used by trusted renderers.', 'bookings-flights-core' ), $active > 0 );
			self::render_stat_card( __( 'Configured', 'bookings-flights-core' ), $configured, __( 'Placements with an approved embed reference, URL, or handoff.', 'bookings-flights-core' ), $configured > 0 );
			self::render_stat_card( __( 'Disabled', 'bookings-flights-core' ), $disabled, __( 'Disabled placements stay stored but should not render publicly.', 'bookings-flights-core' ), 0 === $disabled );
			?>
		</div>
		<?php
	}

	private static function render_stat_card( string $title, int $value, string $message, bool $success ): void {
		?>
		<section class="baf-status-card baf-status-card--<?php echo true === $success ? 'success' : 'warning'; ?>" role="status">
			<div class="baf-status-card__header">
				<h2><?php echo esc_html( $title ); ?></h2>
				<span class="baf-report-card__value"><?php echo esc_html( number_format_i18n( $value ) ); ?></span>
			</div>
			<p><?php echo esc_html( $message ); ?></p>
		</section>
		<?php
	}

	private static function render_table( array $placements ): void {
		?>
		<h2><?php echo esc_html__( 'Placement registry', 'bookings-flights-core' ); ?></h2>
		<?php if ( empty( $placements ) ) : ?>
			<div class="baf-notice baf-notice--warning" role="status">
				<p><?php echo esc_html__( 'No widget placements exist yet. Create the first approved placement below before rendering Travelpayouts surfaces.', 'bookings-flights-core' ); ?></p>
			</div>
			<?php return; ?>
		<?php endif; ?>

		<div class="baf-admin-table-wrap">
			<table class="widefat striped baf-placements-table">
				<caption class="screen-reader-text"><?php echo esc_html__( 'Travelpayouts widget placement registry', 'bookings-flights-core' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php echo esc_html__( 'Placement', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Vertical', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Render mode', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Status', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Surfaces', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Updated', 'bookings-flights-core' ); ?></th>
						<th scope="col"><?php echo esc_html__( 'Actions', 'bookings-flights-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $placements as $placement ) : ?>
						<?php self::render_table_row( $placement ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private static function render_table_row( array $placement ): void {
		$key        = (string) $placement['key'];
		$configured = self::placement_is_configured( $placement );
		$status     = (string) $placement['status'];
		$edit_url   = add_query_arg(
			array(
				'page'      => Admin_Manager::WIDGET_PLACEMENTS_SLUG,
				'placement' => $key,
			),
			admin_url( 'admin.php' )
		);
		?>
		<tr>
			<th scope="row">
				<strong><?php echo esc_html( (string) $placement['name'] ); ?></strong>
				<code><?php echo esc_html( $key ); ?></code>
				<?php if ( ! $configured ) : ?>
					<p class="description"><?php echo esc_html__( 'Missing approved embed configuration.', 'bookings-flights-core' ); ?></p>
				<?php endif; ?>
			</th>
			<td data-label="<?php echo esc_attr__( 'Vertical', 'bookings-flights-core' ); ?>"><?php echo esc_html( self::choice_label( self::VERTICALS, (string) $placement['vertical'] ) ); ?></td>
			<td data-label="<?php echo esc_attr__( 'Render mode', 'bookings-flights-core' ); ?>"><?php echo esc_html( self::choice_label( self::RENDER_MODES, (string) $placement['render_mode'] ) ); ?></td>
			<td data-label="<?php echo esc_attr__( 'Status', 'bookings-flights-core' ); ?>">
				<span class="baf-placement-status baf-placement-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( self::status_label( $placement ) ); ?></span>
			</td>
			<td data-label="<?php echo esc_attr__( 'Surfaces', 'bookings-flights-core' ); ?>"><?php echo esc_html( implode( ', ', (array) $placement['public_surfaces'] ) ); ?></td>
			<td data-label="<?php echo esc_attr__( 'Updated', 'bookings-flights-core' ); ?>"><?php echo esc_html( self::format_timestamp( (string) $placement['updated_at'] ) ); ?></td>
			<td data-label="<?php echo esc_attr__( 'Actions', 'bookings-flights-core' ); ?>" class="baf-placement-actions">
				<a class="button button-small" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html__( 'Edit', 'bookings-flights-core' ); ?></a>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="<?php echo esc_attr( self::DELETE_ACTION ); ?>" />
					<input type="hidden" name="placement_key" value="<?php echo esc_attr( $key ); ?>" />
					<?php wp_nonce_field( self::DELETE_ACTION . '_' . $key ); ?>
					<button type="submit" class="button button-small" onclick="return confirm('<?php echo esc_js( __( 'Delete this placement?', 'bookings-flights-core' ) ); ?>');"><?php echo esc_html__( 'Delete', 'bookings-flights-core' ); ?></button>
				</form>
			</td>
		</tr>
		<?php
	}

	private static function requested_placement_key(): string {
		return sanitize_key( (string) wp_unslash( $_GET['placement'] ?? '' ) );
	}

	private static function placement_is_configured( array $placement ): bool {
		$embed = (array) ( $placement['embed'] ?? array() );

		return '' !== (string) ( $embed['reference'] ?? '' ) || '' !== (string) ( $embed['url'] ?? '' );
	}

	private static function status_label( array $placement ): string {
		if ( ! self::placement_is_configured( $placement ) && 'active' !== (string) $placement['status'] ) {
			return __( 'Missing configuration', 'bookings-flights-core' );
		}

		return self::choice_label( self::STATUSES, (string) $placement['status'] );
	}

	private static function choice_label( array $choices, string $value ): string {
		return (string) ( $choices[ $value ] ?? $value );
	}

	private static function format_timestamp( string $timestamp ): string {
		$time = strtotime( $timestamp );

		if ( false === $time ) {
			return __( 'Unknown', 'bookings-flights-core' );
		}

		return wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time );
	}

	private static function render_notice(): void {
		$notice = get_transient( self::NOTICE_TRANSIENT_KEY . get_current_user_id() );

		if ( ! is_array( $notice ) ) {
			return;
		}

		delete_transient( self::NOTICE_TRANSIENT_KEY . get_current_user_id() );

		$type = 'error' === (string) ( $notice['type'] ?? '' ) ? 'error' : 'success';
		?>
		<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
			<p><?php echo esc_html( (string) ( $notice['message'] ?? '' ) ); ?></p>
		</div>
		<?php
	}

	private static function set_notice( string $type, string $message ): void {
		set_transient(
			self::NOTICE_TRANSIENT_KEY . get_current_user_id(),
			array(
				'type'    => 'error' === $type ? 'error' : 'success',
				'message' => sanitize_text_field( $message ),
			),
			MINUTE_IN_SECONDS
		);
	}

	private static function redirect( string $placement_key = '' ): never {
		$args = array( 'page' => Admin_Manager::WIDGET_PLACEMENTS_SLUG );

		if ( '' !== $placement_key ) {
			$args['placement'] = $placement_key;
		}

		wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
		exit;
	}
}
