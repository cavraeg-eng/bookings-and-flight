<?php
/**
 * Destination planning module grid and handoff cards.
 *
 * @package Bookings_and_Flights_Static
 */

$modules           = isset( $args['modules'] ) && is_array( $args['modules'] ) ? $args['modules'] : array();
$flight_search_url = isset( $args['flight_search_url'] ) ? esc_url( (string) $args['flight_search_url'] ) : '';
$hotel_search_url  = isset( $args['hotel_search_url'] ) ? esc_url( (string) $args['hotel_search_url'] ) : '';
$planner_url       = isset( $args['planner_url'] ) ? esc_url( (string) $args['planner_url'] ) : '';
?>

<section class="hotel-guide-section destination-guide-section" aria-labelledby="destination-guide-modules-title">
	<div class="hotel-guide-section__inner">
		<div class="hotel-guide-heading">
			<p class="hotel-guide-heading__eyebrow"><?php esc_html_e( 'Destination content engine', 'bookings_and_flights' ); ?></p>
			<h2 id="destination-guide-modules-title"><?php esc_html_e( 'Timing, activities, and trip angles', 'bookings_and_flights' ); ?></h2>
			<p><?php esc_html_e( 'These modules turn destination posts into richer SEO pages while keeping provider availability and monetized execution outside WordPress.', 'bookings_and_flights' ); ?></p>
		</div>

		<?php if ( ! empty( $modules ) ) : ?>
			<div class="destination-guide-module-grid">
				<?php foreach ( $modules as $module ) : ?>
					<section class="hotel-guide-module destination-guide-module" aria-label="<?php echo esc_attr( (string) ( $module['label'] ?? '' ) ); ?>">
						<span class="hotel-guide-module__label"><?php echo esc_html( (string) ( $module['label'] ?? '' ) ); ?></span>
						<h3><?php echo esc_html( (string) ( $module['title'] ?? '' ) ); ?></h3>
						<p><?php echo esc_html( (string) ( $module['copy'] ?? '' ) ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="destination-guide-link-grid" aria-label="<?php esc_attr_e( 'Destination planning handoffs', 'bookings_and_flights' ); ?>">
			<a class="destination-guide-link-card" href="<?php echo esc_url( $flight_search_url ); ?>">
				<span><?php esc_html_e( 'Flights', 'bookings_and_flights' ); ?></span>
				<strong><?php esc_html_e( 'Open provider flight search', 'bookings_and_flights' ); ?></strong>
				<small><?php esc_html_e( 'Live results, filters, booking, payment, and support stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ); ?></small>
			</a>
			<a class="destination-guide-link-card" href="<?php echo esc_url( $hotel_search_url ); ?>">
				<span><?php esc_html_e( 'Hotels', 'bookings_and_flights' ); ?></span>
				<strong><?php esc_html_e( 'Open provider hotel search', 'bookings_and_flights' ); ?></strong>
				<small><?php esc_html_e( 'Use the editorial guide here, then confirm current availability and policies with the provider.', 'bookings_and_flights' ); ?></small>
			</a>
			<a class="destination-guide-link-card" href="<?php echo esc_url( $planner_url ); ?>">
				<span><?php esc_html_e( 'AI planner', 'bookings_and_flights' ); ?></span>
				<strong><?php esc_html_e( 'Review planner placeholder', 'bookings_and_flights' ); ?></strong>
				<small><?php esc_html_e( 'AI planning remains a later-phase, consent-gated workflow and does not auto-publish or book.', 'bookings_and_flights' ); ?></small>
			</a>
		</div>
	</div>
</section>
