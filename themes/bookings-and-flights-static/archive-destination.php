<?php
/**
 * Destination guide archive.
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

$destination_css = get_template_directory() . '/assets/css/destination-surface.css';
if ( file_exists( $destination_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-destination-surface',
		get_template_directory_uri() . '/assets/css/destination-surface.css',
		array( 'bookings_and_flights-hotel-guide' ),
		filemtime( $destination_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'hotel-guide-page';
		$classes[] = 'destination-guide-page';

		return $classes;
	}
);

get_header();
?>

<main id="main-content" class="hotel-guide hotel-guide--archive destination-guide destination-guide--archive">
	<section class="hotel-guide-hero" aria-labelledby="destination-archive-title">
		<div class="hotel-guide-hero__inner">
			<p class="hotel-guide-hero__eyebrow"><?php esc_html_e( 'Destination guides', 'bookings_and_flights' ); ?></p>
			<h1 id="destination-archive-title" class="hotel-guide-hero__title"><?php esc_html_e( 'Editable travel guides before provider handoff', 'bookings_and_flights' ); ?></h1>
			<p class="hotel-guide-hero__lede"><?php esc_html_e( 'Browse WordPress-owned destination pages for timing, activities, routes, hotel neighborhoods, and trip-planning context. Live availability, prices, maps, booking, payment, changes, and support stay inside Travelpayouts or the partner provider.', 'bookings_and_flights' ); ?></p>
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
				<h2 id="destination-guide-list-title"><?php esc_html_e( 'Published destination guides', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Each guide keeps destination copy, taxonomy labels, and planning modules editable in WordPress while monetized utility links route through approved partner handoffs.', 'bookings_and_flights' ); ?></p>
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
						$region_terms      = get_the_terms( $post_id, 'travel_region' );
						$style_terms       = get_the_terms( $post_id, 'travel_style' );
						$term_labels       = array();
						foreach ( array( $region_terms, $style_terms ) as $term_group ) {
							if ( is_array( $term_group ) ) {
								foreach ( $term_group as $term ) {
									$term_labels[] = sanitize_text_field( $term->name );
								}
							}
						}
						$hotel_url         = add_query_arg(
							array(
								'travel_destination' => $destination_label,
								'baf_surface'        => 'destination_archive',
							),
							home_url( '/hotels/' )
						);
						?>
						<article class="hotel-guide-listing-card">
							<p class="hotel-guide-listing-card__eyebrow"><?php esc_html_e( 'Destination guide', 'bookings_and_flights' ); ?></p>
							<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<p>
								<?php
								echo esc_html(
									'' !== get_the_excerpt() ? get_the_excerpt() : ( '' !== $summary ? wp_trim_words( $summary, 26, '' ) : sprintf(
										/* translators: %s: destination name. */
										__( 'Plan %s timing, routes, hotel neighborhoods, and activity ideas before opening provider-owned search.', 'bookings_and_flights' ),
										$destination_label
									) )
								);
								?>
							</p>
							<?php if ( ! empty( $term_labels ) ) : ?>
								<ul class="destination-guide-tax-list destination-guide-tax-list--card" aria-label="<?php esc_attr_e( 'Guide taxonomy labels', 'bookings_and_flights' ); ?>">
									<?php foreach ( array_slice( array_unique( $term_labels ), 0, 4 ) as $term_label ) : ?>
										<li><?php echo esc_html( $term_label ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
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
								<a class="hotel-guide-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Open destination guide', 'bookings_and_flights' ); ?></a>
								<a class="hotel-guide-text-link" href="<?php echo esc_url( $hotel_url ); ?>"><?php esc_html_e( 'Open partner search', 'bookings_and_flights' ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="hotel-guide-empty" role="status">
					<h2><?php esc_html_e( 'No destination guides yet', 'bookings_and_flights' ); ?></h2>
					<p><?php esc_html_e( 'Publish destination posts to build editable travel guides. Until then, use the Hotels page for partner-owned hotel search.', 'bookings_and_flights' ); ?></p>
					<a class="hotel-guide-button" href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'Open hotel search', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
