<?php
/**
 * Route archive template.
 *
 * @package Bookings_and_Flights_Static
 */

$route_css = get_template_directory() . '/assets/css/route-surface.css';
if ( file_exists( $route_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-route-surface',
		get_template_directory_uri() . '/assets/css/route-surface.css',
		array( 'bookings_and_flights-footer' ),
		filemtime( $route_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'route-surface-page';

		return $classes;
	}
);

global $wp_query;

$origin_filter = '';
if ( function_exists( 'bookings_and_flights_route_origin_filter' ) ) {
	$origin_filter = bookings_and_flights_route_origin_filter();
}

$archive_query = null;
if ( '' !== $origin_filter ) {
	$archive_query = new WP_Query(
		array(
			'post_type'      => 'route',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'no_found_rows'  => true,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'   => 'baf_origin_airport',
					'value' => $origin_filter,
				),
				array(
					'key'   => 'baf_origin',
					'value' => $origin_filter,
				),
			),
		)
	);
}

$routes_query = $archive_query instanceof WP_Query ? $archive_query : $wp_query;

get_header();
?>

<main id="main-content" class="route-archive">
	<section class="route-hero" aria-labelledby="route-archive-title">
		<div class="route-hero__inner">
			<p class="route-hero__eyebrow"><?php esc_html_e( 'Flight routes', 'bookings_and_flights' ); ?></p>
			<h1 id="route-archive-title" class="route-hero__title">
				<?php
				if ( '' !== $origin_filter ) {
					echo esc_html(
						sprintf(
							/* translators: %s: origin airport or city code. */
							__( 'Flights from %s', 'bookings_and_flights' ),
							$origin_filter
						)
					);
				} else {
					esc_html_e( 'Route guides built for provider handoff', 'bookings_and_flights' );
				}
				?>
			</h1>
			<p class="route-hero__lede"><?php esc_html_e( 'Browse indexable WordPress route guides, then continue into Travelpayouts-controlled flight search for provider options, filters, booking, payment, and support.', 'bookings_and_flights' ); ?></p>
			<div class="route-hero__actions">
				<a class="route-button" href="<?php echo esc_url( home_url( '/flights/' ) ); ?>"><?php esc_html_e( 'Open flight search', 'bookings_and_flights' ); ?></a>
				<a class="route-button route-button--secondary" href="<?php echo esc_url( home_url( '/#deals' ) ); ?>"><?php esc_html_e( 'Browse planning prompts', 'bookings_and_flights' ); ?></a>
			</div>
		</div>
	</section>

	<section class="route-content route-content--archive-modules" aria-labelledby="route-archive-modules-title">
		<div class="route-content__inner route-content__inner--stack">
			<div class="route-section-heading">
				<p class="route-section-heading__eyebrow"><?php esc_html_e( 'SEO route modules', 'bookings_and_flights' ); ?></p>
				<h2 id="route-archive-modules-title"><?php esc_html_e( 'Flight pages that hand off at the right moment', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Route archives stay indexable and editorial. Live fares, low-price calendars, result filters, booking, payment, changes, and support remain inside Travelpayouts or the partner provider.', 'bookings_and_flights' ); ?></p>
			</div>

			<div class="route-module-grid route-module-grid--archive">
				<article class="route-module">
					<span class="route-module__label"><?php esc_html_e( 'Search', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'White Label flight entry', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Route guides link into the approved provider search surface instead of storing or replaying fare results in WordPress.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="route-module">
					<span class="route-module__label"><?php esc_html_e( 'Calendar', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Flexible-date discovery', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Low-price calendar modules belong on route detail pages with clear disclosure and provider-owned availability.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="route-module">
					<span class="route-module__label"><?php esc_html_e( 'Destination', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Hotels and activity follow-up', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Route pages connect flights to destination guides, hotel handoffs, and activity planning prompts without claiming local supplier inventory.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="route-module">
					<span class="route-module__label"><?php esc_html_e( 'Alerts', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Price alert intent', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Alert signup captures local traveler intent only; provider fare monitoring and booking execution stay outside WordPress.', 'bookings_and_flights' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="route-index" aria-labelledby="route-index-title">
		<div class="route-index__inner">
			<div class="route-section-heading">
				<p class="route-section-heading__eyebrow"><?php esc_html_e( 'Indexable route content', 'bookings_and_flights' ); ?></p>
				<h2 id="route-index-title"><?php esc_html_e( 'Origin and destination route pages', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Each route guide stores editorial context in WordPress while provider flight results remain inside the approved Travelpayouts handoff.', 'bookings_and_flights' ); ?></p>
			</div>

			<?php if ( $routes_query->have_posts() ) : ?>
				<div class="route-grid">
					<?php
					while ( $routes_query->have_posts() ) :
						$routes_query->the_post();
						get_template_part(
							'template-parts/route-card',
							null,
							array(
								'post_id' => get_the_ID(),
							)
						);
					endwhile;
					?>
				</div>
			<?php else : ?>
				<div class="route-empty" role="status">
					<h2><?php esc_html_e( 'No published route guides yet', 'bookings_and_flights' ); ?></h2>
					<p><?php esc_html_e( 'Publish route posts with origin and destination airport meta to populate this archive. Until then, use the Flights handoff for provider-owned search.', 'bookings_and_flights' ); ?></p>
					<a class="route-button" href="<?php echo esc_url( home_url( '/flights/' ) ); ?>"><?php esc_html_e( 'Open flight search', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>
</main>

<?php
get_footer();
