<?php
/**
 * Single destination hotel guide template.
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

$hotel_css = get_template_directory() . '/assets/css/hotel-guide.css';
if ( file_exists( $hotel_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-hotel-guide',
		get_template_directory_uri() . '/assets/css/hotel-guide.css',
		array( 'bookings_and_flights-search-surface' ),
		filemtime( $hotel_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'search-surface-page';
		$classes[] = 'hotel-guide-page';

		return $classes;
	}
);

get_header();
?>

<main id="main-content" class="hotel-guide hotel-guide--single">
	<?php
	while ( have_posts() ) :
		the_post();

		$post_id = get_the_ID();
		$get_meta = static function ( string $key ) use ( $post_id ): string {
			return trim( sanitize_textarea_field( (string) get_post_meta( $post_id, $key, true ) ) );
		};
		$destination_label = function_exists( 'bookings_and_flights_destination_label_for_post' ) ? bookings_and_flights_destination_label_for_post( $post_id ) : wp_strip_all_tags( get_the_title( $post_id ) );
		$airport_code      = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_destination_airport', true ) ) : '';
		$travel_style      = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_travel_style', true ) );
		$summary           = $get_meta( 'baf_hotel_guide_summary' );
		$neighborhoods     = $get_meta( 'baf_hotel_neighborhoods' );
		$best_for          = $get_meta( 'baf_hotel_best_for' );
		$family_notes      = $get_meta( 'baf_hotel_family_notes' );
		$luxury_notes      = $get_meta( 'baf_hotel_luxury_notes' );
		$budget_notes      = $get_meta( 'baf_hotel_budget_notes' );
		$landmark_notes    = $get_meta( 'baf_hotel_landmark_notes' );
		$hotel_search_url  = add_query_arg(
			array(
				'travel_destination' => $destination_label,
				'stay_focus'         => '' !== $travel_style ? sanitize_key( $travel_style ) : 'central',
				'baf_surface'        => 'destination',
			),
			home_url( '/hotels/' )
		);
		$flight_search_url = '' !== $airport_code ? add_query_arg(
			array(
				'destination' => $airport_code,
				'baf_surface' => 'destination',
			),
			home_url( '/flights/' )
		) : home_url( '/flights/' );
		$destinations_url  = get_post_type_archive_link( 'destination' );
		$destinations_url  = is_string( $destinations_url ) && '' !== $destinations_url ? $destinations_url : home_url( '/destinations/' );

		$guide_modules = array(
			array(
				'label' => __( 'Where to stay', 'bookings_and_flights' ),
				'title' => __( 'Neighborhood guidance', 'bookings_and_flights' ),
				'copy'  => '' !== $neighborhoods ? $neighborhoods : sprintf(
					/* translators: %s: destination name. */
					__( 'Use this %s guide to compare walkability, transit access, arrival timing, and nearby trip plans before opening partner hotel search.', 'bookings_and_flights' ),
					$destination_label
				),
			),
			array(
				'label' => __( 'Best-fit lens', 'bookings_and_flights' ),
				'title' => __( 'Best-fit editorial lens', 'bookings_and_flights' ),
				'copy'  => '' !== $best_for ? $best_for : __( 'Match the stay to the traveler first: location, comfort, group size, accessibility needs, and cancellation flexibility are editorial planning criteria, not live result filters.', 'bookings_and_flights' ),
			),
			array(
				'label' => __( 'Landmarks', 'bookings_and_flights' ),
				'title' => __( 'Budget stays near landmarks', 'bookings_and_flights' ),
				'copy'  => '' !== $landmark_notes ? $landmark_notes : __( 'Use landmark proximity as planning context, then confirm exact hotel address, map position, fees, and policies inside the provider-owned search surface.', 'bookings_and_flights' ),
			),
		);
		$stay_cards = array(
			array(
				'title' => __( 'Family stay lens', 'bookings_and_flights' ),
				'copy'  => '' !== $family_notes ? $family_notes : __( 'Look for room setup, transit ease, elevator access, breakfast needs, and late-arrival plans before confirming live family options with the provider.', 'bookings_and_flights' ),
			),
			array(
				'title' => __( 'Luxury stay lens', 'bookings_and_flights' ),
				'copy'  => '' !== $luxury_notes ? $luxury_notes : __( 'Use this guide to frame preferred service level, neighborhood, wellness, dining, and view priorities without claiming live premium inventory.', 'bookings_and_flights' ),
			),
			array(
				'title' => __( 'Budget stay lens', 'bookings_and_flights' ),
				'copy'  => '' !== $budget_notes ? $budget_notes : __( 'Plan around transit, review quality, room size, and total trip cost, then confirm current rates and taxes in the partner search flow.', 'bookings_and_flights' ),
			),
		);

		$related_meta_query = array();
		if ( '' !== $destination_label ) {
			$related_meta_query[] = array(
				'key'   => 'baf_destination',
				'value' => $destination_label,
			);
		}
		if ( '' !== $airport_code ) {
			$related_meta_query[] = array(
				'key'   => 'baf_destination_airport',
				'value' => $airport_code,
			);
		}

		$related_routes_args = array(
			'post_type'      => 'route',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
		);
		if ( ! empty( $related_meta_query ) ) {
			$related_routes_args['meta_query'] = array_merge( array( 'relation' => 'OR' ), $related_meta_query );
		}
		$related_routes = new WP_Query( $related_routes_args );

		$details = array_filter(
			array(
				array(
					'label' => __( 'Destination', 'bookings_and_flights' ),
					'value' => $destination_label,
				),
				array(
					'label' => __( 'Airport', 'bookings_and_flights' ),
					'value' => $airport_code,
				),
				array(
					'label' => __( 'Guide type', 'bookings_and_flights' ),
					'value' => __( 'City hotel guide', 'bookings_and_flights' ),
				),
			),
			static function ( array $detail ): bool {
				return '' !== (string) $detail['value'];
			}
		);
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'hotel-guide__article' ); ?>>
			<section class="hotel-guide-hero" aria-labelledby="hotel-guide-title">
				<div class="hotel-guide-hero__inner">
					<p class="hotel-guide-hero__eyebrow"><?php esc_html_e( 'City hotel guide', 'bookings_and_flights' ); ?></p>
					<h1 id="hotel-guide-title" class="hotel-guide-hero__title">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: destination name. */
								__( 'Where to stay in %s', 'bookings_and_flights' ),
								$destination_label
							)
						);
						?>
					</h1>
					<p class="hotel-guide-hero__lede">
						<?php
						echo esc_html(
							'' !== $summary ? $summary : sprintf(
								/* translators: %s: destination name. */
								__( 'Use this editable %s hotel guide for neighborhood and stay-type planning, then open the Travelpayouts partner surface for current availability and booking terms.', 'bookings_and_flights' ),
								$destination_label
							)
						);
						?>
					</p>
					<div class="hotel-guide-hero__actions">
						<a class="hotel-guide-button" href="<?php echo esc_url( $hotel_search_url ); ?>"><?php esc_html_e( 'Open hotel search', 'bookings_and_flights' ); ?></a>
						<a class="hotel-guide-button hotel-guide-button--secondary" href="<?php echo esc_url( $destinations_url ); ?>"><?php esc_html_e( 'Browse city guides', 'bookings_and_flights' ); ?></a>
					</div>
				</div>
			</section>

			<section class="hotel-guide-section" aria-label="<?php esc_attr_e( 'Hotel guide editorial notes', 'bookings_and_flights' ); ?>">
				<div class="hotel-guide-section__inner hotel-guide-section__inner--split">
					<div class="hotel-guide-panel hotel-guide-panel--entry">
						<h2><?php esc_html_e( 'City stay brief', 'bookings_and_flights' ); ?></h2>
						<div class="hotel-guide-entry">
							<?php the_content(); ?>
						</div>
					</div>

					<div class="hotel-guide-panel hotel-guide-panel--disclosure">
						<h2><?php esc_html_e( 'Search boundary', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'These modules are editorial planning guidance. Current room availability, rates, taxes, policies, map filters, booking terms, payment, changes, and support stay with Travelpayouts, Trip.com, or the partner provider.', 'bookings_and_flights' ); ?></p>
						<a class="hotel-guide-text-link" href="<?php echo esc_url( $flight_search_url ); ?>"><?php esc_html_e( 'Pair with flight search', 'bookings_and_flights' ); ?></a>
					</div>
				</div>
			</section>

			<section class="hotel-guide-section" aria-labelledby="hotel-guide-modules-title">
				<div class="hotel-guide-section__inner">
					<div class="hotel-guide-heading">
						<p class="hotel-guide-heading__eyebrow"><?php esc_html_e( 'Editable modules', 'bookings_and_flights' ); ?></p>
						<h2 id="hotel-guide-modules-title"><?php esc_html_e( 'Hotel planning modules', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'Use these editorial lenses to choose a stay path before opening provider-controlled hotel search.', 'bookings_and_flights' ); ?></p>
					</div>

					<div class="hotel-guide-module-grid">
						<?php foreach ( $guide_modules as $module ) : ?>
							<section class="hotel-guide-module" aria-label="<?php echo esc_attr( $module['label'] ); ?>">
								<span class="hotel-guide-module__label"><?php echo esc_html( $module['label'] ); ?></span>
								<h3><?php echo esc_html( $module['title'] ); ?></h3>
								<p><?php echo esc_html( $module['copy'] ); ?></p>
							</section>
						<?php endforeach; ?>
					</div>
				</div>
			</section>

			<section class="hotel-guide-section hotel-guide-section--stay-types" aria-labelledby="hotel-guide-stay-types-title">
				<div class="hotel-guide-section__inner">
					<div class="hotel-guide-heading">
						<p class="hotel-guide-heading__eyebrow"><?php esc_html_e( 'Stay types', 'bookings_and_flights' ); ?></p>
						<h2 id="hotel-guide-stay-types-title"><?php esc_html_e( 'Family, luxury, and budget angles', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'These are content modules, not provider-result filters. Refine actual hotels inside the partner surface when those controls are available.', 'bookings_and_flights' ); ?></p>
					</div>

					<div class="hotel-guide-card-grid">
						<?php foreach ( $stay_cards as $card ) : ?>
							<article class="hotel-guide-card">
								<h3><?php echo esc_html( $card['title'] ); ?></h3>
								<p><?php echo esc_html( $card['copy'] ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>

			<?php
			get_template_part(
				'template-parts/hotel-discovery-placements',
				null,
				array(
					'title'       => sprintf(
						/* translators: %s: destination name. */
						__( '%s hotel map and listing handoffs', 'bookings_and_flights' ),
						$destination_label
					),
					'description' => __( 'Use the editorial guide to frame the stay, then open provider-owned map and listing tools to confirm current hotel options and booking terms.', 'bookings_and_flights' ),
					'channel'     => 'destination_single',
					'slug_prefix' => 'destination_' . $post_id,
					'class'       => 'hotel-partner-placements--destination',
					'details'     => $details,
				)
			);
			?>

			<div id="destination-hotel-search" class="hotel-guide-provider">
				<?php
				get_template_part(
					'template-parts/travel-search-placement',
					null,
					array(
						'placement'        => 'hotels_partner_search',
						'surface'          => 'hotels',
						'channel'          => 'destination_single',
						'slug'             => 'destination_' . $post_id,
						'class'            => 'search-placement--hotel-guide',
						'eyebrow'          => __( 'Hotels partner surface', 'bookings_and_flights' ),
						'title'            => __( 'Open partner search for this city', 'bookings_and_flights' ),
						'description'      => __( 'Continue from the editorial city guide into the configured partner surface for current hotel availability, map controls, room choices, taxes, policies, booking terms, payment, changes, and support.', 'bookings_and_flights' ),
						'details'          => $details,
						'fallback_message' => __( 'Hotel search is configured through the Travelpayouts placement registry. If it is unavailable, use the hotel handoff link or check provider settings.', 'bookings_and_flights' ),
						'support_note'     => __( 'Sponsored hotel search may earn a commission. Bookings and Flights keeps this city guide editable in WordPress; current hotel search and reservations stay with Travelpayouts, Trip.com, or the partner provider.', 'bookings_and_flights' ),
					)
				);
				?>
			</div>

			<section class="hotel-guide-section" aria-labelledby="hotel-guide-related-title">
				<div class="hotel-guide-section__inner hotel-guide-section__inner--split">
					<div class="hotel-guide-panel hotel-guide-panel--related">
						<h2 id="hotel-guide-related-title"><?php esc_html_e( 'Related route links', 'bookings_and_flights' ); ?></h2>
						<?php if ( $related_routes->have_posts() ) : ?>
							<div class="hotel-guide-related-list">
								<?php
								while ( $related_routes->have_posts() ) :
									$related_routes->the_post();
									?>
									<a class="hotel-guide-related-link" href="<?php echo esc_url( get_permalink() ); ?>">
										<span><?php echo esc_html( get_the_title() ); ?></span>
										<small><?php esc_html_e( 'Open route guide', 'bookings_and_flights' ); ?></small>
									</a>
								<?php endwhile; ?>
							</div>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Related route guides will appear here when matching route posts are published.', 'bookings_and_flights' ); ?></p>
							<a class="hotel-guide-text-link" href="<?php echo esc_url( home_url( '/routes/' ) ); ?>"><?php esc_html_e( 'Browse all routes', 'bookings_and_flights' ); ?></a>
						<?php endif; ?>
					</div>

					<div class="hotel-guide-panel hotel-guide-panel--next">
						<h2><?php esc_html_e( 'Continue planning', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'Move between the WordPress city guide, flight route pages, and the approved hotel partner search without changing the booking-owner boundary.', 'bookings_and_flights' ); ?></p>
						<div class="hotel-guide-panel__actions">
							<a class="hotel-guide-button" href="<?php echo esc_url( $hotel_search_url ); ?>"><?php esc_html_e( 'Open hotel handoff', 'bookings_and_flights' ); ?></a>
							<a class="hotel-guide-button hotel-guide-button--secondary" href="<?php echo esc_url( $flight_search_url ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
						</div>
					</div>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
