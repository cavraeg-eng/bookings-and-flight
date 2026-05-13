<?php
/**
 * White Label brand continuity note.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'eyebrow' => __( 'White Label continuity', 'bookings_and_flights' ),
		'title'   => '',
		'copy'    => '',
		'links'   => array(),
	)
);

$links    = is_array( $args['links'] ) ? $args['links'] : array();
$title_id = wp_unique_id( 'white-label-continuity-title-' );
$has_title = '' !== (string) $args['title'];
?>

<section
	class="white-label-continuity"
	<?php if ( $has_title ) : ?>
		aria-labelledby="<?php echo esc_attr( $title_id ); ?>"
	<?php else : ?>
		aria-label="<?php echo esc_attr( (string) $args['eyebrow'] ); ?>"
	<?php endif; ?>
>
	<div class="white-label-continuity__content">
		<p class="white-label-continuity__eyebrow"><?php echo esc_html( (string) $args['eyebrow'] ); ?></p>

		<?php if ( $has_title ) : ?>
			<h2 id="<?php echo esc_attr( $title_id ); ?>" class="white-label-continuity__title"><?php echo esc_html( (string) $args['title'] ); ?></h2>
		<?php endif; ?>

		<?php if ( '' !== (string) $args['copy'] ) : ?>
			<p class="white-label-continuity__copy"><?php echo esc_html( (string) $args['copy'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $links ) ) : ?>
		<nav class="white-label-continuity__nav" aria-label="<?php esc_attr_e( 'White Label continuity links', 'bookings_and_flights' ); ?>">
			<ul class="white-label-continuity__links">
				<?php foreach ( $links as $link ) : ?>
					<?php
					$label = isset( $link['label'] ) ? (string) $link['label'] : '';
					$url   = isset( $link['url'] ) ? (string) $link['url'] : '';
					if ( '' === $label || '' === $url ) {
						continue;
					}
					?>
					<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>
</section>
