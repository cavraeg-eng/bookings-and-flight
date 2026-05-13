<?php
/**
 * Travel taxonomy archive template.
 *
 * @package Bookings_and_Flights_Static
 */

$taxonomy_css = get_template_directory() . '/assets/css/taxonomy-surface.css';
if ( file_exists( $taxonomy_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-taxonomy-surface',
		get_template_directory_uri() . '/assets/css/taxonomy-surface.css',
		array( 'bookings_and_flights-footer' ),
		filemtime( $taxonomy_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'taxonomy-surface-page';

		return $classes;
	}
);

$term = get_queried_object();

if ( ! $term instanceof WP_Term ) {
	get_header();
	?>
	<main id="main-content" class="taxonomy-surface">
		<section class="taxonomy-empty" role="status">
			<h1><?php esc_html_e( 'Travel topic unavailable', 'bookings_and_flights' ); ?></h1>
			<p><?php esc_html_e( 'This travel topic could not be loaded. Return to the destination, route, or deal archives to continue browsing.', 'bookings_and_flights' ); ?></p>
		</section>
	</main>
	<?php
	get_footer();
	return;
}

$taxonomy_config = array(
	'travel_region'   => array(
		'label'       => __( 'Region', 'bookings_and_flights' ),
		'post_types'  => array( 'destination', 'route', 'travel_deal' ),
		'description' => __( 'Explore destination guides, route pages, and deal briefs grouped by this region.', 'bookings_and_flights' ),
	),
	'travel_style'    => array(
		'label'       => __( 'Style', 'bookings_and_flights' ),
		'post_types'  => array( 'destination', 'route', 'travel_deal' ),
		'description' => __( 'Compare editorial travel ideas for this style while provider search owns live availability and booking.', 'bookings_and_flights' ),
	),
	'travel_vertical' => array(
		'label'       => __( 'Travel vertical', 'bookings_and_flights' ),
		'post_types'  => array( 'route', 'travel_deal' ),
		'description' => __( 'Browse public route and deal content for this partner vertical without exposing private partner records.', 'bookings_and_flights' ),
	),
	'travel_season'   => array(
		'label'       => __( 'Season', 'bookings_and_flights' ),
		'post_types'  => array( 'destination', 'route', 'travel_deal' ),
		'description' => __( 'Plan seasonal destinations, routes, and deal ideas with cautious editorial context before provider handoff.', 'bookings_and_flights' ),
	),
);

$taxonomy     = sanitize_key( $term->taxonomy );
$config       = $taxonomy_config[ $taxonomy ] ?? array(
	'label'       => __( 'Travel topic', 'bookings_and_flights' ),
	'post_types'  => array( 'destination', 'route', 'travel_deal' ),
	'description' => __( 'Browse public travel content assigned to this topic.', 'bookings_and_flights' ),
);
$term_name    = sanitize_text_field( $term->name );
$term_slug    = sanitize_title( $term->slug );
$term_summary = '' !== trim( (string) $term->description ) ? wp_trim_words( wp_strip_all_tags( $term->description ), 32, '' ) : $config['description'];
$paged        = max( 1, absint( get_query_var( 'paged' ) ) );
$term_link    = get_term_link( $term );
$term_link    = is_wp_error( $term_link ) ? home_url( '/' ) : $term_link;

$content_query = new WP_Query(
	array(
		'post_type'           => $config['post_types'],
		'post_status'         => 'publish',
		'posts_per_page'      => 12,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => false,
		'tax_query'           => array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => array( absint( $term->term_id ) ),
			),
		),
	)
);

$type_labels = array(
	'destination' => __( 'Destination guide', 'bookings_and_flights' ),
	'route'       => __( 'Route guide', 'bookings_and_flights' ),
	'travel_deal' => __( 'Travel deal brief', 'bookings_and_flights' ),
);

$archive_links = array(
	array(
		'label' => __( 'Destinations', 'bookings_and_flights' ),
		'copy'  => __( 'Public destination guides assigned to this topic can link onward to hotel and activity handoffs.', 'bookings_and_flights' ),
		'url'   => home_url( '/destinations/' ),
	),
	array(
		'label' => __( 'Routes', 'bookings_and_flights' ),
		'copy'  => __( 'Route guides keep flight context editorial until travelers open approved provider search.', 'bookings_and_flights' ),
		'url'   => home_url( '/routes/' ),
	),
	array(
		'label' => __( 'Deals', 'bookings_and_flights' ),
		'copy'  => __( 'Deal briefs group seasonal and themed ideas without fake pricing, inventory, or urgency.', 'bookings_and_flights' ),
		'url'   => home_url( '/travel-deals/' ),
	),
	array(
		'label' => __( 'Planner', 'bookings_and_flights' ),
		'copy'  => __( 'The AI planner entry remains a local placeholder until a later phase activates provider-safe generation.', 'bookings_and_flights' ),
		'url'   => home_url( '/#trip-planner' ),
	),
);

$handoff_links = array(
	array(
		'label' => __( 'Open flight handoff', 'bookings_and_flights' ),
		'url'   => add_query_arg(
			array(
				'travel_focus' => $term_slug,
				'baf_surface'  => 'taxonomy_archive',
			),
			home_url( '/flights/' )
		),
	),
	array(
		'label' => __( 'Open hotel handoff', 'bookings_and_flights' ),
		'url'   => add_query_arg(
			array(
				'travel_destination' => $term_name,
				'stay_focus'         => $term_slug,
				'baf_surface'        => 'taxonomy_archive',
			),
			home_url( '/hotels/' )
		),
	),
);

$card_context = static function ( int $post_id ) use ( $term_slug ): array {
	$post_type    = get_post_type( $post_id );
	$destination  = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
	$origin_code  = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_origin_airport', true ) ) : '';
	$dest_code    = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_destination_airport', true ) ) : '';
	$primary_url  = get_permalink( $post_id );
	$primary_text = __( 'Open guide', 'bookings_and_flights' );
	$handoff_url  = home_url( '/flights/' );
	$handoff_text = __( 'Open flight handoff', 'bookings_and_flights' );

	if ( 'destination' === $post_type ) {
		$primary_text = __( 'Open destination guide', 'bookings_and_flights' );
		$handoff_text = __( 'Open hotel handoff', 'bookings_and_flights' );
		$handoff_url  = add_query_arg(
			array_filter(
				array(
					'travel_destination' => '' !== $destination ? $destination : get_the_title( $post_id ),
					'stay_focus'         => $term_slug,
					'baf_surface'        => 'taxonomy_archive',
				)
			),
			home_url( '/hotels/' )
		);
	} elseif ( 'route' === $post_type ) {
		$primary_text = __( 'Open route guide', 'bookings_and_flights' );
		$handoff_url  = add_query_arg(
			array_filter(
				array(
					'origin'      => $origin_code,
					'destination' => $dest_code,
					'baf_surface' => 'taxonomy_archive',
				)
			),
			home_url( '/flights/' )
		);
	} elseif ( 'travel_deal' === $post_type ) {
		$primary_text = __( 'Open deal brief', 'bookings_and_flights' );
		$handoff_text = __( 'Open provider handoff', 'bookings_and_flights' );
		$handoff_url  = add_query_arg(
			array_filter(
				array(
					'origin'      => $origin_code,
					'destination' => $dest_code,
					'baf_surface' => 'taxonomy_archive',
				)
			),
			home_url( '/flights/' )
		);
	}

	return array(
		'primary_text' => $primary_text,
		'primary_url'  => $primary_url,
		'handoff_text' => $handoff_text,
		'handoff_url'  => $handoff_url,
	);
};

get_header();
?>

<main id="main-content" class="taxonomy-surface">
	<section class="taxonomy-hero" aria-labelledby="taxonomy-title">
		<div class="taxonomy-hero__inner">
			<p class="taxonomy-hero__eyebrow"><?php echo esc_html( $config['label'] ); ?></p>
			<h1 id="taxonomy-title" class="taxonomy-hero__title">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: taxonomy term name. */
						__( '%s travel ideas', 'bookings_and_flights' ),
						$term_name
					)
				);
				?>
			</h1>
			<p class="taxonomy-hero__lede"><?php echo esc_html( $term_summary ); ?></p>
			<div class="taxonomy-hero__actions">
				<?php foreach ( $handoff_links as $handoff_link ) : ?>
					<a class="taxonomy-button" href="<?php echo esc_url( $handoff_link['url'] ); ?>"><?php echo esc_html( $handoff_link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="taxonomy-section taxonomy-section--rules" aria-labelledby="taxonomy-rules-title">
		<div class="taxonomy-section__inner taxonomy-section__inner--stack">
			<div class="taxonomy-heading">
				<p class="taxonomy-heading__eyebrow"><?php esc_html_e( 'Internal linking rules', 'bookings_and_flights' ); ?></p>
				<h2 id="taxonomy-rules-title"><?php esc_html_e( 'Public content only, provider handoff at the edge', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'These topic archives list published destinations, routes, and deal briefs assigned to the term. Private trip plans, travel alerts, and partner records stay out of public taxonomy pages.', 'bookings_and_flights' ); ?></p>
			</div>

			<div class="taxonomy-rule-grid">
				<?php foreach ( $archive_links as $archive_link ) : ?>
					<a class="taxonomy-rule-card" href="<?php echo esc_url( $archive_link['url'] ); ?>">
						<strong><?php echo esc_html( $archive_link['label'] ); ?></strong>
						<span><?php echo esc_html( $archive_link['copy'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="taxonomy-section" aria-labelledby="taxonomy-list-title">
		<div class="taxonomy-section__inner">
			<div class="taxonomy-heading">
				<p class="taxonomy-heading__eyebrow"><?php esc_html_e( 'Topic archive', 'bookings_and_flights' ); ?></p>
				<h2 id="taxonomy-list-title"><?php esc_html_e( 'Published travel content', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Results are bounded, paginated, and limited to public WordPress content types that belong on discovery surfaces.', 'bookings_and_flights' ); ?></p>
			</div>

			<?php if ( $content_query->have_posts() ) : ?>
				<div class="taxonomy-card-grid">
					<?php
					while ( $content_query->have_posts() ) :
						$content_query->the_post();
						$post_id      = get_the_ID();
						$post_type    = get_post_type( $post_id );
						$type_label   = $type_labels[ $post_type ] ?? __( 'Travel content', 'bookings_and_flights' );
						$excerpt      = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( get_the_content( null, false, $post_id ) ), 28, '' );
						$card_links   = $card_context( $post_id );
						$term_labels  = array();
						$card_taxonomies = array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' );
						foreach ( $card_taxonomies as $card_taxonomy ) {
							$card_terms = get_the_terms( $post_id, $card_taxonomy );
							if ( ! is_array( $card_terms ) ) {
								continue;
							}

							foreach ( $card_terms as $card_term ) {
								$term_labels[] = sanitize_text_field( $card_term->name );
							}
						}
						?>
						<article class="taxonomy-card">
							<p class="taxonomy-card__eyebrow"><?php echo esc_html( $type_label ); ?></p>
							<h3><a href="<?php echo esc_url( $card_links['primary_url'] ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<?php if ( '' !== $excerpt ) : ?>
								<p><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>
							<?php if ( ! empty( $term_labels ) ) : ?>
								<ul class="taxonomy-chip-list" aria-label="<?php esc_attr_e( 'Related taxonomy labels', 'bookings_and_flights' ); ?>">
									<?php foreach ( array_slice( array_unique( $term_labels ), 0, 5 ) as $term_label ) : ?>
										<li><?php echo esc_html( $term_label ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<div class="taxonomy-card__actions">
								<a class="taxonomy-text-link" href="<?php echo esc_url( $card_links['primary_url'] ); ?>"><?php echo esc_html( $card_links['primary_text'] ); ?></a>
								<a class="taxonomy-text-link" href="<?php echo esc_url( $card_links['handoff_url'] ); ?>"><?php echo esc_html( $card_links['handoff_text'] ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>

				<?php
				$pagination = paginate_links(
					array(
						'total'     => max( 1, (int) $content_query->max_num_pages ),
						'current'   => $paged,
						'prev_text' => __( 'Previous', 'bookings_and_flights' ),
						'next_text' => __( 'Next', 'bookings_and_flights' ),
					)
				);
				if ( is_string( $pagination ) && '' !== $pagination ) :
					?>
					<nav class="taxonomy-pagination" aria-label="<?php esc_attr_e( 'Travel topic pages', 'bookings_and_flights' ); ?>">
						<?php echo wp_kses_post( $pagination ); ?>
					</nav>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="taxonomy-empty" role="status">
					<h2><?php esc_html_e( 'No public travel content for this topic yet', 'bookings_and_flights' ); ?></h2>
					<p><?php esc_html_e( 'Publish destination, route, or travel deal posts with this term to populate the archive. Private trip plans, alerts, and partner records remain hidden from public topic pages.', 'bookings_and_flights' ); ?></p>
					<a class="taxonomy-button" href="<?php echo esc_url( $term_link ); ?>"><?php esc_html_e( 'Refresh topic archive', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
