<?php
/**
 * Destination city hotel guides archive.
 *
 * @package Bookings_and_Flights_Static
 */

$hotel_css = get_template_directory() . '/assets/css/hotel-guide.css';
if ( file_exists( $hotel_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-hotel-guide',
		get_template_directory_uri() . '/assets/css/hotel-guide.css',
		array( 'bookings_and_flights-footer' ),
		filemtime( $hotel_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'hotel-guide-page';

		return $classes;
	}
);

get_header();
?>

<main id="main-content" class="hotel-guide hotel-guide--archive">
	<section class="hotel-guide-hero" aria-labelledby="destination-archive-title">
		<div class="hotel-guide-hero__inner">
			<p class="hotel-guide-hero__eyebrow"><?php esc_html_e( 'City hotel guides', 'bookings_and_flights' ); ?></p>
			<h1 id="destination-archive-title" class="hotel-guide-hero__title"><?php esc_html_e( 'Where to stay before partner search', 'bookings_and_flights' ); ?></h1>
			<p class="hotel-guide-hero__lede"><?php esc_html_e( 'Browse editable WordPress city guides for neighborhoods, stay types, landmarks, and related routes. Live hotel availability and booking stay inside Travelpayouts or the partner provider.', 'bookings_and_flights' ); ?></p>
			<div class="hotel-guide-hero__actions">
				<a class="hotel-guide-button" href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'Open hotel search', 'bookings_and_flights' ); ?></a>
				<a class="hotel-guide-button hotel-guide-button--secondary" href="<?php echo esc_url( home_url( '/routes/' ) ); ?>"><?php esc_html_e( 'Browse routes', 'bookings_and_flights' ); ?></a>
			</div>
		</div>
	</section>

	<section class="hotel-guide-section" aria-labelledby="destination-guide-list-title">
		<div class="hotel-guide-section__inner">
			<div class="hotel-guide-heading">
				<p class="hotel-guide-heading__eyebrow"><?php esc_html_e( 'Editorial index', 'bookings_and_flights' ); ?></p>
				<h2 id="destination-guide-list-title"><?php esc_html_e( 'Published city hotel guides', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Each guide keeps hotel planning content editable in WordPress and routes monetized search through approved partner placements.', 'bookings_and_flights' ); ?></p>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="hotel-guide-listing">
					<?php
					while ( have_posts() ) :
						the_post();
						$post_id           = get_the_ID();
						$destination_label = function_exists( 'bookings_and_flights_destination_label_for_post' ) ? bookings_and_flights_destination_label_for_post( $post_id ) : wp_strip_all_tags( get_the_title( $post_id ) );
						$summary           = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_hotel_guide_summary', true ) );
						$airport_code      = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_destination_airport', true ) ) : '';
						$hotel_url         = add_query_arg(
							array(
								'travel_destination' => $destination_label,
								'baf_surface'        => 'destination_archive',
							),
							home_url( '/hotels/' )
						);
						?>
						<article class="hotel-guide-listing-card">
							<p class="hotel-guide-listing-card__eyebrow"><?php esc_html_e( 'City guide', 'bookings_and_flights' ); ?></p>
							<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<p>
								<?php
								echo esc_html(
									'' !== $summary ? wp_trim_words( $summary, 26, '' ) : sprintf(
										/* translators: %s: destination name. */
										__( 'Plan %s hotel neighborhoods and stay types before opening provider-owned search.', 'bookings_and_flights' ),
										$destination_label
									)
								);
								?>
							</p>
							<dl class="hotel-guide-listing-card__facts">
								<div>
									<dt><?php esc_html_e( 'Destination', 'bookings_and_flights' ); ?></dt>
									<dd><?php echo esc_html( $destination_label ); ?></dd>
								</div>
								<?php if ( '' !== $airport_code ) : ?>
									<div>
										<dt><?php esc_html_e( 'Airport', 'bookings_and_flights' ); ?></dt>
										<dd><?php echo esc_html( $airport_code ); ?></dd>
									</div>
								<?php endif; ?>
							</dl>
							<div class="hotel-guide-listing-card__actions">
								<a class="hotel-guide-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Open city guide', 'bookings_and_flights' ); ?></a>
								<a class="hotel-guide-text-link" href="<?php echo esc_url( $hotel_url ); ?>"><?php esc_html_e( 'Search hotels', 'bookings_and_flights' ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="hotel-guide-empty" role="status">
					<h2><?php esc_html_e( 'No city hotel guides yet', 'bookings_and_flights' ); ?></h2>
					<p><?php esc_html_e( 'Publish destination posts to build editable city hotel guides. Until then, use the Hotels page for partner-owned hotel search.', 'bookings_and_flights' ); ?></p>
					<a class="hotel-guide-button" href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'Open hotel search', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
