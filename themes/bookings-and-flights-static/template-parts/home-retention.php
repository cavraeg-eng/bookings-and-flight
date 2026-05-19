<?php
/**
 * Home retention callouts.
 *
 * @package Bookings_and_Flights_Static
 */

$price_alert_url = isset( $args['price_alert_url'] ) ? (string) $args['price_alert_url'] : '';
$planner_url     = isset( $args['planner_url'] ) ? (string) $args['planner_url'] : '';
?>

<div class="home-retention" aria-labelledby="home-retention-title" data-reveal>
	<div class="home-module__header">
		<p class="home-kicker home-kicker--dark"><?php esc_html_e( 'Want to keep shaping it?', 'bookings_and_flights' ); ?></p>
		<h3 id="home-retention-title"><?php esc_html_e( 'Save the idea, refresh live options later', 'bookings_and_flights' ); ?></h3>
	</div>

	<div class="home-retention__grid">
		<a id="price-alerts" class="home-retention-card" href="<?php echo esc_url( $price_alert_url ); ?>">
			<span class="home-retention-card__label"><?php esc_html_e( 'Flight timing', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__title"><?php esc_html_e( 'Come back when dates shift', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__text"><?php esc_html_e( 'Use live search now, then revisit the route as plans, prices, or travelers change.', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__action"><?php esc_html_e( 'Check flights', 'bookings_and_flights' ); ?></span>
		</a>

		<a id="ai-planner-entry" class="home-retention-card home-retention-card--light" href="<?php echo esc_url( $planner_url ); ?>">
			<span class="home-retention-card__label"><?php esc_html_e( 'Trip planner', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__title"><?php esc_html_e( 'Turn a maybe into a plan', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__text"><?php esc_html_e( 'Use the planner when you know the destination but not the days, pace, or stay area.', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__prompt"><?php esc_html_e( 'Try: four days in Lisbon near transit', 'bookings_and_flights' ); ?></span>
			<span class="home-retention-card__action"><?php esc_html_e( 'Open planner', 'bookings_and_flights' ); ?></span>
		</a>
	</div>
</div>
