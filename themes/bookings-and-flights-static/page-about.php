<?php
/**
 * Template Name: About
 *
 * @package Bookings_and_Flights_Static
 */

get_header();
?>

<main id="main-content" class="site-main partner-review-page">
	<section class="partner-review-hero" aria-labelledby="about-title">
		<div class="partner-review-hero__inner">
			<p class="partner-review-hero__eyebrow"><?php esc_html_e( 'About Bookings and Flights', 'bookings_and_flights' ); ?></p>
			<h1 id="about-title" class="partner-review-hero__title"><?php esc_html_e( 'Travel planning content with transparent partner handoffs', 'bookings_and_flights' ); ?></h1>
			<p class="partner-review-hero__lede"><?php esc_html_e( 'Bookings and Flights helps travelers research routes, compare travel ideas, plan itineraries, and continue to trusted partner providers when they are ready to book.', 'bookings_and_flights' ); ?></p>
			<p class="partner-review-verification"><?php esc_html_e( 'Travelpayouts project verification: Hello Travelpayouts for bookingsandflights.com.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<section class="partner-review-section" aria-labelledby="about-editorial-title">
		<div class="partner-review-section__inner">
			<div class="partner-review-section__intro">
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Editorial purpose', 'bookings_and_flights' ); ?></p>
				<h2 id="about-editorial-title"><?php esc_html_e( 'Useful travel context before booking', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'The site is built around traveler questions: where to go, when to travel, how routes compare, what neighborhoods or airports to understand, and which partner surface should handle live availability.', 'bookings_and_flights' ); ?></p>
			</div>
			<div class="partner-review-grid">
				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Original planning guides', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Destination, route, and deal pages are intended to be edited in WordPress with practical planning notes, timing guidance, neighborhood context, and trip ideas.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Clear provider boundary', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Travelpayouts and partner providers control live inventory, prices, booking, payment, changes, cancellations, and reservation support after a handoff.', 'bookings_and_flights' ); ?></p>
				</article>
				<article class="partner-review-card">
					<h3><?php esc_html_e( 'Transparent monetization', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Sponsored widgets and partner links are disclosed. Bookings and Flights may earn a commission when travelers use eligible partner links.', 'bookings_and_flights' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<section class="partner-review-section partner-review-section--muted" aria-labelledby="about-trust-title">
		<div class="partner-review-section__inner partner-review-split">
			<div>
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Trust and support', 'bookings_and_flights' ); ?></p>
				<h2 id="about-trust-title"><?php esc_html_e( 'What we handle, and what partners handle', 'bookings_and_flights' ); ?></h2>
			</div>
			<div class="partner-review-prose">
				<p><?php esc_html_e( 'Bookings and Flights owns the website experience, planning content, saved-trip workflow, affiliate disclosure, and local support for site questions.', 'bookings_and_flights' ); ?></p>
				<p><?php esc_html_e( 'Completed reservations remain with the partner provider shown during the handoff. Travelers should use the confirmation email and provider support channel for booking changes, payments, refunds, or cancellation questions.', 'bookings_and_flights' ); ?></p>
				<p><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Bookings and Flights', 'bookings_and_flights' ); ?></a></p>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
