<?php
/**
 * Single travel deal template.
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

$deal_css = get_template_directory() . '/assets/css/deal-surface.css';
if ( file_exists( $deal_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-deal-surface',
		get_template_directory_uri() . '/assets/css/deal-surface.css',
		array( 'bookings_and_flights-search-surface' ),
		filemtime( $deal_css )
	);
}

$continuity_css = get_template_directory() . '/assets/css/white-label-continuity.css';
if ( file_exists( $continuity_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-white-label-continuity',
		get_template_directory_uri() . '/assets/css/white-label-continuity.css',
		array( 'bookings_and_flights-deal-surface' ),
		filemtime( $continuity_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'deal-surface-page';
		$classes[] = 'search-surface-page';

		return $classes;
	}
);

add_filter( 'bookings_and_flights_has_official_travelpayouts_output', '__return_true' );

get_header();
?>

<main id="main-content" class="deal-single">
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
			return function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( $value ) : '';
		};

		$origin                 = $get_text_meta( 'baf_origin' );
		$destination            = $get_text_meta( 'baf_destination' );
		$origin_airport         = $normalize_code( $get_text_meta( 'baf_origin_airport' ) );
		$destination_airport    = $normalize_code( $get_text_meta( 'baf_destination_airport' ) );
		$departure_window       = $get_text_meta( 'baf_departure_window' );
		$return_window          = $get_text_meta( 'baf_return_window' );
		$budget_min             = (float) get_post_meta( $post_id, 'baf_budget_min', true );
		$budget_max             = (float) get_post_meta( $post_id, 'baf_budget_max', true );
		$seasonal_context       = $get_textarea_meta( 'baf_deal_seasonal_context' );
		$weekend_ideas          = $get_textarea_meta( 'baf_deal_weekend_ideas' );
		$theme_notes            = $get_textarea_meta( 'baf_deal_theme_notes' );
		$activity_notes         = $get_textarea_meta( 'baf_deal_activity_notes' );
		$partner_notes          = $get_textarea_meta( 'baf_deal_partner_notes' );
		$source_note            = $get_textarea_meta( 'baf_deal_source_note' );
		$deal_label             = function_exists( 'bookings_and_flights_deal_label_for_post' ) ? bookings_and_flights_deal_label_for_post( $post_id ) : wp_strip_all_tags( get_the_title() );
		$destination_label      = '' !== $destination ? $destination : $destination_airport;
		$archive_url            = function_exists( 'bookings_and_flights_public_deal_archive_url' ) ? bookings_and_flights_public_deal_archive_url() : home_url( '/travel-deals/' );
		$route_archive_url      = function_exists( 'bookings_and_flights_public_route_archive_url' ) ? bookings_and_flights_public_route_archive_url( $origin_airport ) : home_url( '/routes/' );
		$budget_label           = '';

		if ( $budget_min > 0 && $budget_max > 0 ) {
			$budget_label = sprintf(
				/* translators: 1: minimum budget, 2: maximum budget. */
				__( 'Editorial budget context: $%1$s to $%2$s', 'bookings_and_flights' ),
				number_format_i18n( $budget_min ),
				number_format_i18n( $budget_max )
			);
		} elseif ( $budget_min > 0 || $budget_max > 0 ) {
			$budget_label = sprintf(
				/* translators: %s: budget amount. */
				__( 'Editorial budget context: around $%s', 'bookings_and_flights' ),
				number_format_i18n( max( $budget_min, $budget_max ) )
			);
		}

		$flight_url_args = array_filter(
			array(
				'origin'      => $origin_airport,
				'destination' => $destination_airport,
				'baf_surface' => 'deal_single',
			)
		);
		$flight_url      = add_query_arg( $flight_url_args, home_url( '/flights/' ) );
		$hotel_url       = add_query_arg(
			array_filter(
				array(
					'travel_destination' => $destination_label,
					'stay_focus'         => 'deal_destination',
					'baf_surface'        => 'deal_single',
				)
			),
			home_url( '/hotels/' )
		);
		$activity_url    = add_query_arg(
			array_filter(
				array(
					'travel_destination' => $destination_label,
					'baf_surface'        => 'deal_single',
				)
			),
			home_url( '/#explore' )
		);

		$details = array_filter(
			array(
				array(
					'label' => __( 'Deal brief', 'bookings_and_flights' ),
					'value' => $deal_label,
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
				array(
					'label' => __( 'Budget note', 'bookings_and_flights' ),
					'value' => $budget_label,
				),
			),
			static function ( array $detail ): bool {
				return '' !== (string) $detail['value'];
			}
		);

		$term_labels = array();
		$tax_query   = array();
		foreach ( array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' ) as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_labels[] = sanitize_text_field( $term->name );
				}
			}

			$term_ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
				continue;
			}

			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', $term_ids ),
			);
		}

		$related_args = array(
			'post_type'      => 'travel_deal',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'no_found_rows'  => true,
		);
		if ( ! empty( $tax_query ) ) {
			$related_args['tax_query'] = array_merge( array( 'relation' => 'OR' ), $tax_query );
		} else {
			$related_args['post__in'] = array( 0 );
		}
		$related_deals = new WP_Query( $related_args );

		$route_meta_query = array();
		if ( '' !== $origin_airport ) {
			$route_meta_query[] = array(
				'key'   => 'baf_origin_airport',
				'value' => $origin_airport,
			);
		}
		if ( '' !== $destination_airport ) {
			$route_meta_query[] = array(
				'key'   => 'baf_destination_airport',
				'value' => $destination_airport,
			);
		}

		$route_args = array(
			'post_type'      => 'route',
			'post_status'    => 'publish',
			'posts_per_page' => 2,
			'no_found_rows'  => true,
		);
		if ( ! empty( $route_meta_query ) ) {
			$route_args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $route_meta_query );
		} else {
			$route_args['post__in'] = array( 0 );
		}
		$matching_routes = new WP_Query( $route_args );

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

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'deal-single__article' ); ?>>
			<section class="deal-hero" aria-labelledby="deal-title">
				<div class="deal-hero__inner">
					<p class="deal-hero__eyebrow"><?php esc_html_e( 'Travel deal brief', 'bookings_and_flights' ); ?></p>
					<h1 id="deal-title" class="deal-hero__title"><?php echo esc_html( get_the_title() ); ?></h1>
					<p class="deal-hero__lede">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: deal label. */
								__( 'Review %s with editable editorial context before opening approved provider search.', 'bookings_and_flights' ),
								$deal_label
							)
						);
						?>
					</p>
					<div class="deal-hero__actions">
						<a class="deal-button" href="<?php echo esc_url( $flight_url ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
						<a class="deal-button deal-button--secondary" href="#deal-partner-cards"><?php esc_html_e( 'Review partner cards', 'bookings_and_flights' ); ?></a>
						<a class="deal-button deal-button--secondary" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Browse deal ideas', 'bookings_and_flights' ); ?></a>
					</div>
				</div>
			</section>

			<section class="deal-section" aria-label="<?php esc_attr_e( 'Deal planning details', 'bookings_and_flights' ); ?>">
				<div class="deal-section__inner">
					<div class="deal-panel deal-panel--summary">
						<h2><?php esc_html_e( 'Brief summary', 'bookings_and_flights' ); ?></h2>
						<?php if ( ! empty( $term_labels ) ) : ?>
							<ul class="deal-tax-list" aria-label="<?php esc_attr_e( 'Deal taxonomy labels', 'bookings_and_flights' ); ?>">
								<?php foreach ( array_slice( array_unique( $term_labels ), 0, 6 ) as $term_label ) : ?>
									<li><?php echo esc_html( $term_label ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<dl class="deal-facts">
							<?php foreach ( $details as $detail ) : ?>
								<div class="deal-facts__item">
									<dt><?php echo esc_html( $detail['label'] ); ?></dt>
									<dd><?php echo esc_html( $detail['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>

					<div class="deal-panel deal-panel--editorial">
						<h2><?php esc_html_e( 'Safe editorial rules', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'Use this page for sourced or cautious travel-planning context. Do not claim live fares, room availability, inventory, booking terms, or urgency unless an approved provider surface shows it to the traveler.', 'bookings_and_flights' ); ?></p>
						<ul class="deal-guidance">
							<li><?php esc_html_e( 'Keep seasonal, weekend, style, activity, and source notes editable in WordPress.', 'bookings_and_flights' ); ?></li>
							<li><?php esc_html_e( 'Use budget fields as editorial context, not as verified current prices.', 'bookings_and_flights' ); ?></li>
							<li><?php esc_html_e( 'Route monetized actions through approved placement registry output or existing handoff pages.', 'bookings_and_flights' ); ?></li>
						</ul>
					</div>

					<div class="deal-entry">
						<div class="deal-entry__content">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</section>

			<?php
			get_template_part(
				'template-parts/deal-editorial-modules',
				null,
				array(
					'deal_label'       => $deal_label,
					'destination_label' => $destination_label,
					'seasonal_context' => $seasonal_context,
					'weekend_ideas'    => $weekend_ideas,
					'theme_notes'      => $theme_notes,
					'activity_notes'   => $activity_notes,
					'partner_notes'    => $partner_notes,
					'source_note'      => $source_note,
					'flight_url'       => $flight_url,
					'hotel_url'        => $hotel_url,
					'activity_url'     => $activity_url,
					'archive_url'      => $archive_url,
				)
			);
			?>

			<div id="deal-provider-search" class="deal-search">
				<?php
				get_template_part(
					'template-parts/white-label-continuity',
					null,
					array(
						'title' => __( 'Provider search stays connected to this brief', 'bookings_and_flights' ),
						'copy'  => __( 'This travel deal page remains a WordPress-owned editorial brief. The White Label module below is Travelpayouts-controlled for live search, provider filters, booking, payment, changes, and support.', 'bookings_and_flights' ),
						'links' => array(
							array(
								'label' => __( 'Deal brief', 'bookings_and_flights' ),
								'url'   => '#deal-title',
							),
							array(
								'label' => __( 'Flights', 'bookings_and_flights' ),
								'url'   => $flight_url,
							),
							array(
								'label' => __( 'All deals', 'bookings_and_flights' ),
								'url'   => $archive_url,
							),
							array(
								'label' => __( 'Routes', 'bookings_and_flights' ),
								'url'   => $route_archive_url,
							),
						),
					)
				);

				get_template_part(
					'template-parts/travel-search-placement',
					null,
					array(
						'placement'        => 'flights_white_label_search',
						'surface'          => 'deal',
						'channel'          => 'deal_single',
						'slug'             => 'deal_' . $post_id,
						'class'            => 'search-placement--deal',
						'eyebrow'          => __( 'Travelpayouts White Label', 'bookings_and_flights' ),
						'title'            => __( 'Search from this deal brief', 'bookings_and_flights' ),
						'description'      => __( 'Open the approved White Label search module for provider-owned flight results. Booking, payment, changes, support, and result filters stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ),
						'origin'           => $origin_airport,
						'destination'      => $destination_airport,
						'details'          => $details,
						'fallback_message' => __( 'Deal search is configured through the Travelpayouts placement registry. If it is unavailable, use the flight handoff link or check provider settings.', 'bookings_and_flights' ),
					)
				);
				?>
			</div>

			<section class="deal-section deal-section--related" aria-label="<?php esc_attr_e( 'Related deal, route, and destination links', 'bookings_and_flights' ); ?>">
				<div class="deal-section__inner deal-section__inner--split">
					<div class="deal-panel deal-panel--related">
						<h2><?php esc_html_e( 'Related deal ideas', 'bookings_and_flights' ); ?></h2>
						<?php if ( $related_deals->have_posts() ) : ?>
							<div class="deal-related-list">
								<?php
								while ( $related_deals->have_posts() ) :
									$related_deals->the_post();
									?>
									<a class="deal-related-link" href="<?php echo esc_url( get_permalink() ); ?>">
										<?php echo esc_html( get_the_title() ); ?>
										<span><?php esc_html_e( 'Open related brief', 'bookings_and_flights' ); ?></span>
									</a>
									<?php
								endwhile;
								?>
							</div>
							<?php wp_reset_postdata(); ?>
						<?php else : ?>
							<p><?php esc_html_e( 'Publish more travel deal posts with shared region, style, season, or vertical terms to populate related brief links.', 'bookings_and_flights' ); ?></p>
							<a class="deal-button deal-button--secondary" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Browse all deal ideas', 'bookings_and_flights' ); ?></a>
						<?php endif; ?>
					</div>

					<div class="deal-panel deal-panel--routes">
						<h2><?php esc_html_e( 'Route and destination follow-up', 'bookings_and_flights' ); ?></h2>
						<?php if ( $matching_routes->have_posts() || $destination_guides->have_posts() ) : ?>
							<div class="deal-related-list">
								<?php
								while ( $matching_routes->have_posts() ) :
									$matching_routes->the_post();
									?>
									<a class="deal-related-link" href="<?php echo esc_url( get_permalink() ); ?>">
										<?php echo esc_html( get_the_title() ); ?>
										<span><?php esc_html_e( 'Open matching route', 'bookings_and_flights' ); ?></span>
									</a>
									<?php
								endwhile;
								wp_reset_postdata();

								while ( $destination_guides->have_posts() ) :
									$destination_guides->the_post();
									?>
									<a class="deal-related-link" href="<?php echo esc_url( get_permalink() ); ?>">
										<?php echo esc_html( get_the_title() ); ?>
										<span><?php esc_html_e( 'Open destination guide', 'bookings_and_flights' ); ?></span>
									</a>
									<?php
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						<?php else : ?>
							<p><?php esc_html_e( 'Matching route and destination links will appear when posts share airport or destination metadata with this brief.', 'bookings_and_flights' ); ?></p>
						<?php endif; ?>

						<div class="deal-panel__actions">
							<a class="deal-button" href="<?php echo esc_url( $hotel_url ); ?>"><?php esc_html_e( 'Open hotel handoff', 'bookings_and_flights' ); ?></a>
							<a class="deal-button deal-button--secondary" href="<?php echo esc_url( $route_archive_url ); ?>"><?php esc_html_e( 'Browse matching routes', 'bookings_and_flights' ); ?></a>
						</div>
					</div>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
