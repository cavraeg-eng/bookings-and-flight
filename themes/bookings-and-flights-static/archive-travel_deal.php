<?php
/**
 * Travel deal archive template.
 *
 * @package Bookings_and_Flights_Static
 */

$deal_css = get_template_directory() . '/assets/css/deal-surface.css';
if ( file_exists( $deal_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-deal-surface',
		get_template_directory_uri() . '/assets/css/deal-surface.css',
		array( 'bookings_and_flights-footer' ),
		filemtime( $deal_css )
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'deal-surface-page';
		$classes[] = 'deal-surface-archive';

		return $classes;
	}
);

$archive_url = function_exists( 'bookings_and_flights_public_deal_archive_url' ) ? bookings_and_flights_public_deal_archive_url() : home_url( '/travel-deals/' );

get_header();
?>

<main id="main-content" class="deal-archive">
	<section class="deal-hero" aria-labelledby="deal-archive-title">
		<div class="deal-hero__inner">
			<p class="deal-hero__eyebrow"><?php esc_html_e( 'Travel deal ideas', 'bookings_and_flights' ); ?></p>
			<h1 id="deal-archive-title" class="deal-hero__title"><?php esc_html_e( 'Seasonal and editorial trip briefs', 'bookings_and_flights' ); ?></h1>
			<p class="deal-hero__lede"><?php esc_html_e( 'Browse editable WordPress deal briefs for seasonal trips, weekend ideas, budget, family, luxury, beach, business, and activity planning. Live prices, availability, booking, payment, changes, and support stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ); ?></p>
			<div class="deal-hero__actions">
				<a class="deal-button" href="<?php echo esc_url( home_url( '/flights/' ) ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
				<a class="deal-button deal-button--secondary" href="<?php echo esc_url( home_url( '/destinations/' ) ); ?>"><?php esc_html_e( 'Browse destinations', 'bookings_and_flights' ); ?></a>
			</div>
		</div>
	</section>

	<section class="deal-section deal-section--intro" aria-labelledby="deal-editorial-title">
		<div class="deal-section__inner deal-section__inner--stack">
			<div class="deal-heading">
				<p class="deal-heading__eyebrow"><?php esc_html_e( 'Editorial guardrails', 'bookings_and_flights' ); ?></p>
				<h2 id="deal-editorial-title"><?php esc_html_e( 'Helpful trip ideas without fake urgency', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Deal content should help visitors decide what to compare next. Use cautious copy, visible disclosures, and approved partner handoffs instead of unverified fare claims.', 'bookings_and_flights' ); ?></p>
			</div>

			<div class="deal-module-grid deal-module-grid--compact">
				<article class="deal-module">
					<span class="deal-module__label"><?php esc_html_e( 'Seasonal', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Trips by timing', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Use month, holiday, weather, and crowd context as editorial guidance before provider search.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="deal-module">
					<span class="deal-module__label"><?php esc_html_e( 'Weekend', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Short-stay ideas', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Frame quick trips, flexible-day windows, and nearby alternatives without claiming current inventory.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="deal-module">
					<span class="deal-module__label"><?php esc_html_e( 'Themes', 'bookings_and_flights' ); ?></span>
					<h3><?php esc_html_e( 'Budget, family, luxury, beach, business', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Let travel-style taxonomy organize editorial ideas while providers own final terms.', 'bookings_and_flights' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="deal-section" aria-labelledby="deal-list-title">
		<div class="deal-section__inner">
			<div class="deal-heading">
				<p class="deal-heading__eyebrow"><?php esc_html_e( 'Published briefs', 'bookings_and_flights' ); ?></p>
				<h2 id="deal-list-title"><?php esc_html_e( 'Editable travel deal ideas', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Each brief keeps copy, taxonomy labels, budget context, and partner-card guidance editable in WordPress.', 'bookings_and_flights' ); ?></p>
			</div>

			<?php if ( have_posts() ) : ?>
				<div class="deal-listing">
					<?php
					while ( have_posts() ) :
						the_post();
						$post_id             = get_the_ID();
						$destination_label   = function_exists( 'bookings_and_flights_destination_label_for_post' ) ? bookings_and_flights_destination_label_for_post( $post_id ) : sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
						$origin_airport      = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_origin_airport', true ) ) : '';
						$destination_airport = function_exists( 'bookings_and_flights_normalize_route_code' ) ? bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_destination_airport', true ) ) : '';
						$budget_min          = (float) get_post_meta( $post_id, 'baf_budget_min', true );
						$budget_max          = (float) get_post_meta( $post_id, 'baf_budget_max', true );
						$budget_label        = '';
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

						$term_labels = array();
						foreach ( array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' ) as $taxonomy ) {
							$terms = get_the_terms( $post_id, $taxonomy );
							if ( ! is_array( $terms ) ) {
								continue;
							}

							foreach ( $terms as $term ) {
								$term_labels[] = sanitize_text_field( $term->name );
							}
						}

						$flight_url = add_query_arg(
							array_filter(
								array(
									'origin'      => $origin_airport,
									'destination' => $destination_airport,
									'baf_surface' => 'deal_archive',
								)
							),
							home_url( '/flights/' )
						);
						?>
						<article class="deal-card">
							<p class="deal-card__eyebrow"><?php esc_html_e( 'Editorial brief', 'bookings_and_flights' ); ?></p>
							<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<p>
								<?php
								echo esc_html(
									'' !== get_the_excerpt() ? get_the_excerpt() : sprintf(
										/* translators: %s: destination label. */
										__( 'Review seasonal, weekend, travel-style, activity, and partner handoff context for %s.', 'bookings_and_flights' ),
										'' !== $destination_label ? $destination_label : __( 'this trip idea', 'bookings_and_flights' )
									)
								);
								?>
							</p>
							<?php if ( ! empty( $term_labels ) ) : ?>
								<ul class="deal-tax-list" aria-label="<?php esc_attr_e( 'Deal taxonomy labels', 'bookings_and_flights' ); ?>">
									<?php foreach ( array_slice( array_unique( $term_labels ), 0, 5 ) as $term_label ) : ?>
										<li><?php echo esc_html( $term_label ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<dl class="deal-card__facts">
								<?php if ( '' !== $destination_label ) : ?>
									<div>
										<dt><?php esc_html_e( 'Destination', 'bookings_and_flights' ); ?></dt>
										<dd><?php echo esc_html( $destination_label ); ?></dd>
									</div>
								<?php endif; ?>
								<?php if ( '' !== $budget_label ) : ?>
									<div>
										<dt><?php esc_html_e( 'Budget note', 'bookings_and_flights' ); ?></dt>
										<dd><?php echo esc_html( $budget_label ); ?></dd>
									</div>
								<?php endif; ?>
							</dl>
							<div class="deal-card__actions">
								<a class="deal-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Open deal brief', 'bookings_and_flights' ); ?></a>
								<a class="deal-text-link" href="<?php echo esc_url( $flight_url ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="deal-empty" role="status">
					<h2><?php esc_html_e( 'No travel deal ideas yet', 'bookings_and_flights' ); ?></h2>
					<p><?php esc_html_e( 'Publish travel deal posts to build editable seasonal and editorial trip briefs. Until then, use Flights or Destinations for approved provider handoff paths.', 'bookings_and_flights' ); ?></p>
					<a class="deal-button" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Refresh deal archive', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
