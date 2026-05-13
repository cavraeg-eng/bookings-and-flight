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
		'details'          => array(),
		'fallback_message' => __( 'Travel search is being configured. Please use the partner handoff link on this page or check back shortly.', 'bookings_and_flights' ),
	)
);

$placement = sanitize_key( (string) $args['placement'] );
$surface   = sanitize_key( (string) $args['surface'] );
$channel   = sanitize_key( (string) $args['channel'] );
$slug      = sanitize_key( (string) $args['slug'] );
$class     = sanitize_html_class( (string) $args['class'] );
$details   = is_array( $args['details'] ) ? $args['details'] : array();
?>

<section class="<?php echo esc_attr( trim( 'search-placement ' . $class ) ); ?>" aria-labelledby="<?php echo esc_attr( $surface . '-placement-title' ); ?>">
	<div class="search-placement__header">
		<?php if ( '' !== (string) $args['eyebrow'] ) : ?>
			<p class="search-placement__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== (string) $args['title'] ) : ?>
			<h2 id="<?php echo esc_attr( $surface . '-placement-title' ); ?>" class="search-placement__title"><?php echo esc_html( (string) $args['title'] ); ?></h2>
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
			echo do_shortcode(
				sprintf(
					'[baf_travelpayouts_widget placement="%1$s" surface="%2$s" channel="%3$s" slug="%4$s"]',
					esc_attr( $placement ),
					esc_attr( $surface ),
					esc_attr( $channel ),
					esc_attr( $slug )
				)
			);
		} else {
			?>
			<div class="search-placement__fallback" role="status">
				<?php echo esc_html( (string) $args['fallback_message'] ); ?>
			</div>
			<?php
		}
		?>
	</div>
</section>
