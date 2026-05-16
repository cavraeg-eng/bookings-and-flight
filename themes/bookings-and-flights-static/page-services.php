<?php
/**
 * Template Name: Services
 *
 * @package Bookings_and_Flights_Static
 */

get_header();
?>

<main id="main-content" class="site-main partner-review-page">
	<section class="partner-review-hero" aria-labelledby="services-title">
		<div class="partner-review-hero__inner">
			<p class="partner-review-hero__eyebrow"><?php esc_html_e( 'Services', 'bookings_and_flights' ); ?></p>
			<h1 id="services-title" class="partner-review-hero__title"><?php esc_html_e( 'Travel search help before the booking handoff', 'bookings_and_flights' ); ?></h1>
			<p class="partner-review-hero__lede"><?php esc_html_e( 'Bookings and Flights helps travelers shape a trip, compare live partner search tools, and continue to the travel site only when they are ready to book.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<section class="partner-review-section" aria-labelledby="services-search-title">
		<div class="partner-review-section__inner">
			<div class="partner-review-section__intro">
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Search tools', 'bookings_and_flights' ); ?></p>
				<h2 id="services-search-title"><?php esc_html_e( 'Core ways to plan and compare', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Each service keeps the trip context visible on Bookings and Flights while partner providers handle live availability, checkout, payment, changes, and reservation support.', 'bookings_and_flights' ); ?></p>
			</div>

			<div class="partner-review-grid">
				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Flight search', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Enter route, dates, travelers, and cabin details, then compare live flight results through the Travelpayouts-powered search area.', 'bookings_and_flights' ); ?></p>
					<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/flights/' ) ); ?>"><?php esc_html_e( 'Search flights', 'bookings_and_flights' ); ?></a></p>
				</article>

				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Hotel comparison', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Choose the city, dates, guests, and rooms, then use the partner hotel surface for current rooms, maps, amenities, and policies.', 'bookings_and_flights' ); ?></p>
					<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/hotels/' ) ); ?>"><?php esc_html_e( 'Compare hotels', 'bookings_and_flights' ); ?></a></p>
				</article>

				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Trip planning', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Build a practical trip brief before searching, then keep flight, hotel, route, and alert actions tied to the planning context.', 'bookings_and_flights' ); ?></p>
					<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/trip-planner/' ) ); ?>"><?php esc_html_e( 'Plan a trip', 'bookings_and_flights' ); ?></a></p>
				</article>
			</div>
		</div>
	</section>

	<section class="partner-review-section partner-review-section--muted" aria-labelledby="services-boundary-title">
		<div class="partner-review-section__inner partner-review-split">
			<div>
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Partner boundary', 'bookings_and_flights' ); ?></p>
				<h2 id="services-boundary-title"><?php esc_html_e( 'What happens after you choose a provider', 'bookings_and_flights' ); ?></h2>
			</div>
			<div class="partner-review-prose">
				<p><?php esc_html_e( 'Bookings and Flights owns the planning pages, search context, disclosures, saved-trip tools, and local alert intent capture.', 'bookings_and_flights' ); ?></p>
				<p><?php esc_html_e( 'The selected travel provider owns final prices, inventory, booking checkout, payments, refunds, changes, cancellations, and reservation support.', 'bookings_and_flights' ); ?></p>
				<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact site support', 'bookings_and_flights' ); ?></a></p>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
