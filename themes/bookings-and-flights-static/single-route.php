<?php
/**
 * Single route template.
 *
 * @package Bookings_and_Flights_Static
 */

$search_css = get_template_directory() . '/assets/css/search-surface.css';
if ( file_exists( $search_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-search-surface',
		get_template_directory_uri() . '/assets/css/search-surface.css',
		array( 'bookings_and_flights-footer' ),
		filemtime( $search_css )
	);
}

$route_css = get_template_directory() . '/assets/css/route-surface.css';
if ( file_exists( $route_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-route-surface',
		get_template_directory_uri() . '/assets/css/route-surface.css',
		array( 'bookings_and_flights-search-surface' ),
		filemtime( $route_css )
	);
}

$continuity_css = get_template_directory() . '/assets/css/white-label-continuity.css';
if ( file_exists( $continuity_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-white-label-continuity',
		get_template_directory_uri() . '/assets/css/white-label-continuity.css',
		array( 'bookings_and_flights-route-surface' ),
		filemtime( $continuity_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'route-surface-page';
		$classes[] = 'search-surface-page';

		return $classes;
	}
);

add_filter( 'bookings_and_flights_has_official_travelpayouts_output', '__return_true' );

get_header();
?>

<main id="main-content" class="route-single">
	<?php
	while ( have_posts() ) :
		the_post();

		$post_id = get_the_ID();
		$get_meta = static function ( string $key ) use ( $post_id ): string {
			return sanitize_text_field( (string) get_post_meta( $post_id, $key, true ) );
		};
		$normalize_code = static function ( string $value ): string {
			$value = strtoupper( preg_replace( '/[^A-Z0-9]/', '', $value ) );

			return substr( $value, 0, 10 );
		};

		$origin              = $get_meta( 'baf_origin' );
		$destination         = $get_meta( 'baf_destination' );
		$origin_airport      = $normalize_code( $get_meta( 'baf_origin_airport' ) );
		$destination_airport = $normalize_code( $get_meta( 'baf_destination_airport' ) );
		$departure_window    = $get_meta( 'baf_departure_window' );
		$return_window       = $get_meta( 'baf_return_window' );
		$route_label_parts   = array_filter(
			array(
				'' !== $origin_airport ? $origin_airport : $origin,
				'' !== $destination_airport ? $destination_airport : $destination,
			)
		);
		$route_label = 2 === count( $route_label_parts ) ? implode( ' to ', $route_label_parts ) : get_the_title();

		$flight_url_args = array(
			'baf_surface' => 'route_single',
		);

		if ( '' !== $origin_airport ) {
			$flight_url_args['origin'] = $origin_airport;
		}

		if ( '' !== $destination_airport ) {
			$flight_url_args['destination'] = $destination_airport;
		}

		$flight_url = add_query_arg( $flight_url_args, home_url( '/flights/' ) );
		$alert_url  = add_query_arg(
			array_merge(
				$flight_url_args,
				array(
					'travel_focus' => 'price_alert',
				)
			),
			home_url( '/flights/' )
		);
		$route_archive_url = get_post_type_archive_link( 'route' );
		if ( ! is_string( $route_archive_url ) || '' === $route_archive_url ) {
			$route_archive_url = home_url( '/routes/' );
		}

		$details = array_filter(
			array(
				array(
					'label' => __( 'Route', 'bookings_and_flights' ),
					'value' => $route_label,
				),
				array(
					'label' => __( 'Origin', 'bookings_and_flights' ),
					'value' => '' !== $origin ? $origin : $origin_airport,
				),
				array(
					'label' => __( 'Destination', 'bookings_and_flights' ),
					'value' => '' !== $destination ? $destination : $destination_airport,
				),
				array(
					'label' => __( 'Departure window', 'bookings_and_flights' ),
					'value' => $departure_window,
				),
				array(
					'label' => __( 'Return window', 'bookings_and_flights' ),
					'value' => $return_window,
				),
			),
			static function ( array $detail ): bool {
				return '' !== (string) $detail['value'];
			}
		);
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'route-single__article' ); ?>>
			<section class="route-hero" aria-labelledby="route-title">
				<div class="route-hero__inner">
					<p class="route-hero__eyebrow"><?php esc_html_e( 'Flight route guide', 'bookings_and_flights' ); ?></p>
					<h1 id="route-title" class="route-hero__title"><?php the_title(); ?></h1>
					<p class="route-hero__lede">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: route label. */
								__( 'Plan %s with WordPress-owned editorial context and Travelpayouts-controlled flight search handoff.', 'bookings_and_flights' ),
								$route_label
							)
						);
						?>
					</p>
					<div class="route-hero__actions">
						<a class="route-button" href="<?php echo esc_url( $flight_url ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
						<a class="route-button route-button--secondary" href="<?php echo esc_url( $route_archive_url ); ?>"><?php esc_html_e( 'Browse routes', 'bookings_and_flights' ); ?></a>
					</div>
				</div>
			</section>

			<section class="route-content" aria-label="<?php esc_attr_e( 'Route planning details', 'bookings_and_flights' ); ?>">
				<div class="route-content__inner">
					<div class="route-panel route-panel--summary">
						<h2><?php esc_html_e( 'Route summary', 'bookings_and_flights' ); ?></h2>
						<dl class="route-facts">
							<?php foreach ( $details as $detail ) : ?>
								<div class="route-facts__item">
									<dt><?php echo esc_html( $detail['label'] ); ?></dt>
									<dd><?php echo esc_html( $detail['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>

					<div class="route-panel route-panel--editorial">
						<h2><?php esc_html_e( 'Airport and timing notes', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'Use this route page for evergreen planning context. Confirm exact airports, baggage rules, direct service, nearby airports, flexible-date calendars, and fare conditions inside the Travelpayouts provider surface.', 'bookings_and_flights' ); ?></p>
						<ul class="route-guidance">
							<li><?php esc_html_e( 'Keep airport codes and local airport names editable in WordPress meta.', 'bookings_and_flights' ); ?></li>
							<li><?php esc_html_e( 'Use seasons or date windows as editorial guidance, not stored provider inventory.', 'bookings_and_flights' ); ?></li>
							<li><?php esc_html_e( 'Link related routes by shared origin, destination, region, or travel style to strengthen internal discovery.', 'bookings_and_flights' ); ?></li>
						</ul>
					</div>

					<div class="route-entry">
						<div class="route-entry__content">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</section>

			<div id="route-provider-search" class="route-search">
				<?php
				get_template_part(
					'template-parts/white-label-continuity',
					null,
					array(
						'title' => __( 'Route search stays connected to this guide', 'bookings_and_flights' ),
						'copy'  => sprintf(
							/* translators: %s: route label. */
							__( 'This %s guide remains a WordPress-owned SEO and planning page. The White Label module below is Travelpayouts-controlled for live search, provider filters, booking, payment, changes, and support.', 'bookings_and_flights' ),
							$route_label
						),
						'links' => array(
							array(
								'label' => __( 'Route guide', 'bookings_and_flights' ),
								'url'   => '#route-title',
							),
							array(
								'label' => __( 'Flights', 'bookings_and_flights' ),
								'url'   => $flight_url,
							),
							array(
								'label' => __( 'All routes', 'bookings_and_flights' ),
								'url'   => $route_archive_url,
							),
							array(
								'label' => __( 'Home', 'bookings_and_flights' ),
								'url'   => home_url( '/' ),
							),
						),
					)
				);
				?>

				<?php
				get_template_part(
					'template-parts/travel-search-placement',
					null,
					array(
						'placement'        => 'flights_white_label_search',
						'surface'          => 'route',
						'channel'          => 'route_single',
						'slug'             => 'route_' . $post_id,
						'class'            => 'search-placement--route',
						'eyebrow'          => __( 'Travelpayouts White Label', 'bookings_and_flights' ),
						'title'            => __( 'Search this route', 'bookings_and_flights' ),
						'description'      => __( 'Open the approved White Label search module for provider-owned flight results. Booking, payment, changes, support, and result filters stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ),
						'origin'           => $origin_airport,
						'destination'      => $destination_airport,
						'details'          => $details,
						'fallback_message' => __( 'Route flight search is configured through the Travelpayouts placement registry. If it is unavailable, use the flight handoff link or check provider settings.', 'bookings_and_flights' ),
					)
				);
				?>
			</div>

			<div class="route-search route-search--discovery">
				<?php
				get_template_part(
					'template-parts/flight-discovery-widgets',
					null,
					array(
						'surface'     => 'route',
						'channel'     => 'route_single',
						'slug'        => 'route_' . $post_id,
						'origin'      => $origin_airport,
						'destination' => $destination_airport,
						'details'     => $details,
					)
				);
				?>
			</div>

			<section class="route-content route-content--after-search" aria-label="<?php esc_attr_e( 'Route alerts and related routes', 'bookings_and_flights' ); ?>">
				<div class="route-content__inner route-content__inner--split">
					<?php if ( shortcode_exists( 'baf_flight_alert_signup' ) ) : ?>
						<?php
						echo do_shortcode(
							sprintf(
								'[baf_flight_alert_signup origin="%1$s" destination="%2$s" depart_date="%3$s" return_date="%4$s" travelers="1" cabin="economy" surface="route_single" route_id="%5$d" context="route"]',
								esc_attr( $origin_airport ),
								esc_attr( $destination_airport ),
								esc_attr( $departure_window ),
								esc_attr( $return_window ),
								$post_id
							)
						);
						?>
					<?php else : ?>
						<div class="route-panel route-panel--alert" aria-labelledby="route-after-search-title">
							<p class="route-panel__eyebrow"><?php esc_html_e( 'Alerts', 'bookings_and_flights' ); ?></p>
							<h2 id="route-after-search-title"><?php esc_html_e( 'Watch this route later', 'bookings_and_flights' ); ?></h2>
							<p><?php esc_html_e( 'Alert capture is unavailable until the Bookings and Flights Core alert workflow is active. Use the approved flight handoff without treating WordPress as the live fare owner.', 'bookings_and_flights' ); ?></p>
							<a class="route-button" href="<?php echo esc_url( $alert_url ); ?>"><?php esc_html_e( 'Open alert handoff', 'bookings_and_flights' ); ?></a>
						</div>
					<?php endif; ?>

					<div class="route-panel route-panel--related">
						<h2><?php esc_html_e( 'Related routes', 'bookings_and_flights' ); ?></h2>
						<?php
						$meta_query = array();
						if ( '' !== $origin_airport ) {
							$meta_query[] = array(
								'key'   => 'baf_origin_airport',
								'value' => $origin_airport,
							);
						}
						if ( '' !== $destination_airport ) {
							$meta_query[] = array(
								'key'   => 'baf_destination_airport',
								'value' => $destination_airport,
							);
						}

						$related_args = array(
							'post_type'      => 'route',
							'post_status'    => 'publish',
							'posts_per_page' => 3,
							'post__not_in'   => array( $post_id ),
							'no_found_rows'  => true,
						);

						if ( ! empty( $meta_query ) ) {
							$related_args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $meta_query );
						}

						$related_routes = new WP_Query( $related_args );
						?>
						<?php if ( $related_routes->have_posts() ) : ?>
							<div class="route-related-list">
								<?php
								while ( $related_routes->have_posts() ) :
									$related_routes->the_post();
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
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Publish more route posts with shared origin, destination, region, or travel-style metadata to populate related route links.', 'bookings_and_flights' ); ?></p>
							<a class="route-button route-button--secondary" href="<?php echo esc_url( $route_archive_url ); ?>"><?php esc_html_e( 'Browse all routes', 'bookings_and_flights' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
