<?php
/**
 * Shared Travelpayouts placement shell.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'placement'        => '',
		'surface'          => '',
		'channel'          => 'search_page',
		'slug'             => '',
		'class'            => '',
		'eyebrow'          => '',
		'title'            => '',
		'description'      => '',
		'origin'           => '',
		'destination'      => '',
		'details'          => array(),
		'fallback_message' => __( 'Travel search is being configured. Please use the partner handoff link on this page or check back shortly.', 'bookings_and_flights' ),
		'support_note'     => __( 'Sponsored partner search may earn a commission. Bookings and Flights does not complete reservations; booking, payment, changes, and support stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ),
	)
);

$placement = sanitize_key( (string) $args['placement'] );
$surface   = sanitize_key( (string) $args['surface'] );
$channel   = sanitize_key( (string) $args['channel'] );
$slug      = sanitize_key( (string) $args['slug'] );
$class     = sanitize_html_class( (string) $args['class'] );
$details   = is_array( $args['details'] ) ? $args['details'] : array();
$origin      = strtoupper( trim( (string) $args['origin'] ) );
$origin      = preg_match( '/^[A-Z]{3}$/', $origin ) ? $origin : '';
$destination = strtoupper( trim( (string) $args['destination'] ) );
$destination = preg_match( '/^[A-Z]{3}$/', $destination ) ? $destination : '';
$title_id    = sanitize_html_class( implode( '-', array_filter( array( $surface, $placement, $slug, 'placement-title' ) ) ) );

if ( '' === $title_id ) {
	$title_id = wp_unique_id( 'search-placement-title-' );
}
?>

<section class="<?php echo esc_attr( trim( 'search-placement ' . $class ) ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div class="search-placement__header">
		<?php if ( '' !== (string) $args['eyebrow'] ) : ?>
			<p class="search-placement__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== (string) $args['title'] ) : ?>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" class="search-placement__title"><?php echo esc_html( (string) $args['title'] ); ?></h2>
		<?php endif; ?>

		<?php if ( '' !== (string) $args['description'] ) : ?>
			<p class="search-placement__description"><?php echo esc_html( (string) $args['description'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $details ) ) : ?>
		<dl class="search-placement__intent">
			<?php foreach ( $details as $detail ) : ?>
				<?php
				$label = isset( $detail['label'] ) ? (string) $detail['label'] : '';
				$value = isset( $detail['value'] ) ? (string) $detail['value'] : '';
				if ( '' === $label || '' === $value ) {
					continue;
				}
				?>
				<div class="search-placement__intent-item">
					<dt><?php echo esc_html( $label ); ?></dt>
					<dd><?php echo esc_html( $value ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>
	<?php endif; ?>

	<div class="search-placement__widget">
		<?php
		if ( shortcode_exists( 'baf_travelpayouts_widget' ) && '' !== $placement ) {
			$shortcode_attributes = array(
				'placement' => $placement,
				'surface'   => $surface,
				'channel'   => $channel,
				'slug'      => $slug,
			);

			if ( '' !== $origin ) {
				$shortcode_attributes['origin'] = $origin;
			}

			if ( '' !== $destination ) {
				$shortcode_attributes['destination'] = $destination;
			}

			$shortcode_parts = array( 'baf_travelpayouts_widget' );
			foreach ( $shortcode_attributes as $attribute_name => $attribute_value ) {
				$shortcode_parts[] = sprintf( '%1$s="%2$s"', sanitize_key( $attribute_name ), esc_attr( $attribute_value ) );
			}

			echo do_shortcode( '[' . implode( ' ', $shortcode_parts ) . ']' );
		} else {
			?>
			<div class="search-placement__fallback" role="status">
				<?php echo esc_html( (string) $args['fallback_message'] ); ?>
			</div>
			<?php
		}
		?>
	</div>

	<?php if ( '' !== (string) $args['support_note'] ) : ?>
		<p class="search-placement__support"><?php echo esc_html( (string) $args['support_note'] ); ?></p>
	<?php endif; ?>
</section>
