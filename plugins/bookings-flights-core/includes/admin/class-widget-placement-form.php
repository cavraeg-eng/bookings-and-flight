<?php
/**
 * Travelpayouts widget placement form helper.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Admin;

defined( 'ABSPATH' ) || exit;

final class Widget_Placement_Form {

	private const VERTICALS = array(
		'flights'     => 'Flights',
		'hotels'      => 'Hotels',
		'cars'        => 'Cars',
		'activities'  => 'Activities',
		'packages'    => 'Packages',
		'route'       => 'Route',
		'destination' => 'Destination',
		'deal'        => 'Deal',
		'ai'          => 'AI',
		'saved_trip'  => 'Saved trip',
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

	public static function render( array $placement, string $action ): void {
		$is_existing = '' !== (string) $placement['key'];
		?>
		<h2><?php echo true === $is_existing ? esc_html__( 'Edit placement', 'bookings-flights-core' ) : esc_html__( 'Create placement', 'bookings-flights-core' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="baf-settings-form baf-placement-form">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>" />
			<?php wp_nonce_field( $action ); ?>
			<table class="form-table" role="presentation">
				<tbody>
					<?php self::text( 'key', __( 'Placement key', 'bookings-flights-core' ), $placement['key'], __( 'Lowercase identifier used by future shortcodes and blocks.', 'bookings-flights-core' ), $is_existing ); ?>
					<?php self::text( 'name', __( 'Name', 'bookings-flights-core' ), $placement['name'], __( 'Internal admin label.', 'bookings-flights-core' ) ); ?>
					<?php self::select( 'vertical', __( 'Vertical', 'bookings-flights-core' ), $placement['vertical'], self::VERTICALS ); ?>
					<?php self::text( 'context', __( 'Context', 'bookings-flights-core' ), $placement['context'], __( 'Use values such as home, flights, hotels, route, or destination.', 'bookings-flights-core' ) ); ?>
					<?php self::select( 'widget_family', __( 'Widget family', 'bookings-flights-core' ), $placement['widget_family'], self::WIDGET_FAMILIES ); ?>
					<?php self::select( 'status', __( 'Status', 'bookings-flights-core' ), $placement['status'], self::STATUSES ); ?>
					<?php self::select( 'render_mode', __( 'Render mode', 'bookings-flights-core' ), $placement['render_mode'], self::RENDER_MODES ); ?>
					<?php self::select( 'embed_source', __( 'Embed source', 'bookings-flights-core' ), $placement['embed']['source'], self::EMBED_SOURCES ); ?>
					<?php self::textarea( 'embed_value', __( 'Embed reference or URL', 'bookings-flights-core' ), self::embed_value( $placement ), __( 'Paste the approved widget script URL, Trip.com partner iframe URL, handoff URL, or White Label reference. Raw pasted snippets are reduced before storage.', 'bookings-flights-core' ), 4 ); ?>
					<?php self::text( 'approved_hosts', __( 'Approved hosts', 'bookings-flights-core' ), implode( ', ', (array) $placement['embed']['approved_hosts'] ), __( 'Comma-separated host allowlist for this placement.', 'bookings-flights-core' ) ); ?>
					<?php self::text( 'subid_pattern', __( 'SubID pattern', 'bookings-flights-core' ), $placement['subid_pattern'], __( 'Use lowercase tokens such as {channel}_{surface}_{vertical}_{slug}_{placement}.', 'bookings-flights-core' ) ); ?>
					<?php self::text( 'public_surfaces', __( 'Public surfaces', 'bookings-flights-core' ), implode( ', ', (array) $placement['public_surfaces'] ), __( 'Comma-separated surfaces where this placement may appear.', 'bookings-flights-core' ) ); ?>
					<?php self::checkbox( 'consent_required', __( 'Consent required', 'bookings-flights-core' ), $placement['consent_required'], __( 'Require provider request consent before public renderers use this placement.', 'bookings-flights-core' ) ); ?>
					<?php self::checkbox( 'disclosure_required', __( 'Disclosure required', 'bookings-flights-core' ), $placement['disclosure']['required'], __( 'Show sponsored/affiliate disclosure near public output.', 'bookings-flights-core' ) ); ?>
					<?php self::text( 'disclosure_copy', __( 'Disclosure copy', 'bookings-flights-core' ), $placement['disclosure']['copy'] ); ?>
					<?php self::number( 'desktop_min_height', __( 'Desktop min height', 'bookings-flights-core' ), $placement['frame']['desktop_min_height'] ); ?>
					<?php self::number( 'tablet_min_height', __( 'Tablet min height', 'bookings-flights-core' ), $placement['frame']['tablet_min_height'] ); ?>
					<?php self::number( 'mobile_min_height', __( 'Mobile min height', 'bookings-flights-core' ), $placement['frame']['mobile_min_height'] ); ?>
					<?php self::text( 'fallback_url', __( 'Fallback URL', 'bookings-flights-core' ), $placement['fallback']['url'], __( 'Optional sponsored handoff URL if the embed cannot render.', 'bookings-flights-core' ) ); ?>
					<?php self::text( 'fallback_label', __( 'Fallback label', 'bookings-flights-core' ), $placement['fallback']['label'] ); ?>
					<?php self::textarea( 'notes', __( 'Safe admin notes', 'bookings-flights-core' ), $placement['notes'], __( 'Do not store API keys, customer data, private prompts, or credentials here.', 'bookings-flights-core' ), 3 ); ?>
				</tbody>
			</table>
			<?php submit_button( true === $is_existing ? __( 'Update placement', 'bookings-flights-core' ) : __( 'Create placement', 'bookings-flights-core' ) ); ?>
		</form>
		<?php
	}

	public static function posted_placement(): array {
		$posted = wp_unslash( $_POST['placement'] ?? array() );
		$posted = is_array( $posted ) ? $posted : array();
		$mode   = sanitize_key( (string) ( $posted['render_mode'] ?? '' ) );
		$source = sanitize_key( (string) ( $posted['embed_source'] ?? '' ) );

		return array(
			'key'             => sanitize_key( (string) ( $posted['key'] ?? '' ) ),
			'name'            => sanitize_text_field( (string) ( $posted['name'] ?? '' ) ),
			'vertical'        => sanitize_key( (string) ( $posted['vertical'] ?? '' ) ),
			'context'         => sanitize_key( (string) ( $posted['context'] ?? '' ) ),
			'widget_family'   => sanitize_key( (string) ( $posted['widget_family'] ?? '' ) ),
			'render_mode'     => $mode,
			'embed'           => array(
				'source'         => $source,
				'mode'           => $mode,
				'reference'      => self::embed_reference_from_post( $posted, $mode, $source ),
				'url'            => self::embed_url_from_post( $posted, $mode, $source ),
				'approved_hosts' => self::comma_list( (string) ( $posted['approved_hosts'] ?? '' ) ),
			),
			'status'          => sanitize_key( (string) ( $posted['status'] ?? '' ) ),
			'subid_pattern'   => sanitize_text_field( (string) ( $posted['subid_pattern'] ?? '' ) ),
			'public_surfaces' => self::comma_list( (string) ( $posted['public_surfaces'] ?? '' ) ),
			'consent_required' => ! empty( $posted['consent_required'] ),
			'disclosure'      => array(
				'required' => ! empty( $posted['disclosure_required'] ),
				'copy'     => sanitize_text_field( (string) ( $posted['disclosure_copy'] ?? '' ) ),
			),
			'frame'           => array(
				'desktop_min_height' => absint( $posted['desktop_min_height'] ?? 520 ),
				'tablet_min_height'  => absint( $posted['tablet_min_height'] ?? 520 ),
				'mobile_min_height'  => absint( $posted['mobile_min_height'] ?? 640 ),
			),
			'fallback'        => array(
				'url'   => esc_url_raw( (string) ( $posted['fallback_url'] ?? '' ) ),
				'label' => sanitize_text_field( (string) ( $posted['fallback_label'] ?? '' ) ),
			),
			'notes'           => sanitize_textarea_field( (string) ( $posted['notes'] ?? '' ) ),
		);
	}

	public static function empty_placement(): array {
		return array(
			'key'             => '',
			'name'            => '',
			'vertical'        => 'flights',
			'context'         => 'global',
			'widget_family'   => 'partner_link_card',
			'render_mode'     => 'disabled',
			'embed'           => array(
				'source'         => 'none',
				'mode'           => 'disabled',
				'reference'      => '',
				'url'            => '',
				'approved_hosts' => array(),
			),
			'status'          => 'draft',
			'subid_pattern'   => '{channel}_{surface}_{vertical}_{slug}_{placement}',
			'public_surfaces' => array(),
			'consent_required' => true,
			'disclosure'      => array(
				'required' => true,
				'copy'     => __( 'Sponsored travel search. Booking is completed with the partner provider.', 'bookings-flights-core' ),
			),
			'frame'           => array(
				'desktop_min_height' => 520,
				'tablet_min_height'  => 520,
				'mobile_min_height'  => 640,
			),
			'fallback'        => array(
				'url'   => '',
				'label' => '',
			),
			'notes'           => '',
		);
	}

	private static function text( string $key, string $label, mixed $value, string $description = '', bool $readonly = false ): void {
		self::row( $key, $label, sprintf( '<input type="text" id="%1$s" name="placement[%2$s]" value="%3$s" class="regular-text" %4$s />', esc_attr( self::field_id( $key ) ), esc_attr( $key ), esc_attr( (string) $value ), true === $readonly ? 'readonly' : '' ), $description );
	}

	private static function number( string $key, string $label, mixed $value ): void {
		self::row( $key, $label, sprintf( '<input type="number" min="0" step="1" id="%1$s" name="placement[%2$s]" value="%3$s" class="small-text" />', esc_attr( self::field_id( $key ) ), esc_attr( $key ), esc_attr( (string) $value ) ), __( 'Pixels.', 'bookings-flights-core' ) );
	}

	private static function textarea( string $key, string $label, mixed $value, string $description, int $rows ): void {
		self::row( $key, $label, sprintf( '<textarea id="%1$s" name="placement[%2$s]" class="large-text code" rows="%3$d">%4$s</textarea>', esc_attr( self::field_id( $key ) ), esc_attr( $key ), $rows, esc_textarea( (string) $value ) ), $description );
	}

	private static function select( string $key, string $label, mixed $value, array $choices ): void {
		$html = sprintf( '<select id="%1$s" name="placement[%2$s]">', esc_attr( self::field_id( $key ) ), esc_attr( $key ) );

		foreach ( $choices as $choice_value => $choice_label ) {
			$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( (string) $choice_value ), selected( (string) $value, (string) $choice_value, false ), esc_html( (string) $choice_label ) );
		}

		self::row( $key, $label, $html . '</select>' );
	}

	private static function checkbox( string $key, string $label, mixed $checked, string $description ): void {
		$html = sprintf( '<label for="%1$s"><input type="checkbox" id="%1$s" name="placement[%2$s]" value="1" %3$s /> %4$s</label>', esc_attr( self::field_id( $key ) ), esc_attr( $key ), checked( true, (bool) $checked, false ), esc_html( $description ) );
		self::row( $key, $label, $html );
	}

	private static function row( string $key, string $label, string $input_html, string $description = '' ): void {
		?>
		<tr>
			<th scope="row"><label for="<?php echo esc_attr( self::field_id( $key ) ); ?>"><?php echo esc_html( $label ); ?></label></th>
			<td>
				<?php echo $input_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by escaped field renderers. ?>
				<?php if ( '' !== $description ) : ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	private static function embed_reference_from_post( array $posted, string $mode, string $source ): string {
		if ( 'official_shortcode' === $mode || 'travelpayouts_white_label' === $source ) {
			return sanitize_text_field( (string) ( $posted['embed_value'] ?? '' ) );
		}

		return '';
	}

	private static function embed_url_from_post( array $posted, string $mode, string $source ): string {
		if ( 'travelpayouts_white_label' !== $source && in_array( $mode, array( 'dashboard_script', 'iframe', 'handoff_link' ), true ) ) {
			return (string) ( $posted['embed_value'] ?? '' );
		}

		return '';
	}

	private static function embed_value( array $placement ): string {
		$embed = (array) ( $placement['embed'] ?? array() );

		if ( '' !== (string) ( $embed['reference'] ?? '' ) ) {
			return (string) $embed['reference'];
		}

		return (string) ( $embed['url'] ?? '' );
	}

	private static function comma_list( string $value ): array {
		$values = array_map( 'trim', explode( ',', $value ) );
		$values = array_filter( $values, static fn( string $item ): bool => '' !== $item );

		return array_values( $values );
	}

	private static function field_id( string $key ): string {
		return 'baf_widget_placement_' . sanitize_key( $key );
	}
}
