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
		$get_text_meta = static function ( string $key ) use ( $post_id ): string {
			return sanitize_text_field( (string) get_post_meta( $post_id, $key, true ) );
		};
		$get_textarea_meta = static function ( string $key ) use ( $post_id ): string {
			return sanitize_textarea_field( (string) get_post_meta( $post_id, $key, true ) );
		};
		$normalize_code = static function ( string $value ): string {
			return bookings_and_flights_normalize_route_code( $value );
		};

		$origin                  = $get_text_meta( 'baf_origin' );
		$destination             = $get_text_meta( 'baf_destination' );
		$origin_airport          = $normalize_code( $get_text_meta( 'baf_origin_airport' ) );
		$destination_airport     = $normalize_code( $get_text_meta( 'baf_destination_airport' ) );
		$departure_window        = $get_text_meta( 'baf_departure_window' );
		$return_window           = $get_text_meta( 'baf_return_window' );
		$route_travel_time       = $get_textarea_meta( 'baf_route_travel_time' );
		$route_airport_notes     = $get_textarea_meta( 'baf_route_airport_notes' );
		$route_flexible_dates    = $get_textarea_meta( 'baf_route_flexible_dates' );
		$route_destination_notes = $get_textarea_meta( 'baf_route_destination_notes' );
		$route_label_parts       = array_filter(
			array(
				'' !== $origin_airport ? $origin_airport : $origin,
				'' !== $destination_airport ? $destination_airport : $destination,
			)
		);
		$route_label             = 2 === count( $route_label_parts ) ? implode( ' to ', $route_label_parts ) : get_the_title();
		$destination_label       = '' !== $destination ? $destination : $destination_airport;

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
		$hotel_url  = add_query_arg(
			array_filter(
				array(
					'travel_destination' => $destination_label,
					'stay_focus'         => 'route_destination',
					'baf_surface'        => 'route_single',
				)
			),
			home_url( '/hotels/' )
		);
		$activity_url = add_query_arg(
			array_filter(
				array(
					'travel_destination' => $destination_label,
					'baf_surface'        => 'route_single',
				)
			),
			home_url( '/#explore' )
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

		$related_meta_query = array();
		if ( '' !== $origin_airport ) {
			$related_meta_query[] = array(
				'key'   => 'baf_origin_airport',
				'value' => $origin_airport,
			);
		}
		if ( '' !== $destination_airport ) {
			$related_meta_query[] = array(
				'key'   => 'baf_destination_airport',
				'value' => $destination_airport,
			);
		}

		$related_route_tax_query = array();
		foreach ( array( 'travel_region', 'travel_style', 'travel_season', 'travel_vertical' ) as $related_taxonomy ) {
			$term_ids = wp_get_post_terms( $post_id, $related_taxonomy, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
				continue;
			}

			$related_route_tax_query[] = array(
				'taxonomy' => $related_taxonomy,
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', $term_ids ),
			);
		}

		$related_args = array(
			'post_type'      => 'route',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'no_found_rows'  => true,
		);
		if ( ! empty( $related_meta_query ) ) {
			$related_args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $related_meta_query );
		} elseif ( ! empty( $related_route_tax_query ) ) {
			$related_args['tax_query'] = array_merge( array( 'relation' => 'OR' ), $related_route_tax_query );
		} else {
			$related_args['post__in'] = array( 0 );
		}
		$related_routes = new WP_Query( $related_args );

		$destination_meta_query = array();
		if ( '' !== $destination_airport ) {
			$destination_meta_query[] = array(
				'key'   => 'baf_destination_airport',
				'value' => $destination_airport,
			);
		}
		if ( '' !== $destination ) {
			$destination_meta_query[] = array(
				'key'   => 'baf_destination',
				'value' => $destination,
			);
		}

		$destination_args = array(
			'post_type'      => 'destination',
			'post_status'    => 'publish',
			'posts_per_page' => 2,
			'no_found_rows'  => true,
		);
		if ( ! empty( $destination_meta_query ) ) {
			$destination_args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $destination_meta_query );
		} else {
			$destination_args['post__in'] = array( 0 );
		}
		$destination_guides = new WP_Query( $destination_args );
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
						<a class="route-button route-button--secondary" href="#route-alerts"><?php esc_html_e( 'Watch route', 'bookings_and_flights' ); ?></a>
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

			<?php
			get_template_part(
				'template-parts/route-planning-modules',
				null,
				array(
					'route_label'         => $route_label,
					'origin_airport'      => $origin_airport,
					'destination_airport' => $destination_airport,
					'destination_label'   => $destination_label,
					'travel_time'         => $route_travel_time,
					'airport_notes'       => $route_airport_notes,
					'flexible_dates'      => $route_flexible_dates,
					'destination_notes'   => $route_destination_notes,
					'flight_url'          => $flight_url,
					'hotel_url'           => $hotel_url,
					'activity_url'        => $activity_url,
					'calendar_url'        => '#route-discovery-widgets',
					'alert_url'           => '#route-alerts',
				)
			);
			?>

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

			<div id="route-discovery-widgets" class="route-search route-search--discovery">
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

			<section id="route-alerts" class="route-content route-content--after-search" aria-label="<?php esc_attr_e( 'Route alerts and related routes', 'bookings_and_flights' ); ?>">
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

					<div class="route-panel route-panel--destination">
						<h2><?php esc_html_e( 'Destination hotels and activities', 'bookings_and_flights' ); ?></h2>
						<p>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: destination label. */
									__( 'Use %s as the arrival context for hotels and activities, then continue into approved handoff surfaces for current availability and terms.', 'bookings_and_flights' ),
									'' !== $destination_label ? $destination_label : __( 'the destination', 'bookings_and_flights' )
								)
							);
							?>
						</p>
						<?php if ( $destination_guides->have_posts() ) : ?>
							<div class="route-related-list">
								<?php
								while ( $destination_guides->have_posts() ) :
									$destination_guides->the_post();
									?>
									<a class="route-related-link" href="<?php echo esc_url( get_permalink() ); ?>">
										<?php echo esc_html( get_the_title() ); ?>
										<span><?php esc_html_e( 'Open destination guide', 'bookings_and_flights' ); ?></span>
									</a>
									<?php
								endwhile;
								?>
							</div>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Destination guide links will appear here when matching destination posts are published.', 'bookings_and_flights' ); ?></p>
						<?php endif; ?>

						<div class="route-panel__actions">
							<a class="route-button" href="<?php echo esc_url( $hotel_url ); ?>"><?php esc_html_e( 'Open hotel handoff', 'bookings_and_flights' ); ?></a>
							<a class="route-button route-button--secondary" href="<?php echo esc_url( $activity_url ); ?>"><?php esc_html_e( 'Explore activities', 'bookings_and_flights' ); ?></a>
						</div>
					</div>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
