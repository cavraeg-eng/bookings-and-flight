<?php
/**
 * Template Name: Contact
 *
 * @package Bookings_and_Flights_Static
 */

get_header();

$support_email = '';

if ( class_exists( '\\BAF\\Core\\Settings\\Settings_Manager' ) ) {
	$general       = \BAF\Core\Settings\Settings_Manager::get_general();
	$support_email = sanitize_email( (string) ( $general['support_email'] ?? '' ) );
}

if ( '' === $support_email ) {
	$option_email = bookings_and_flights_option( 'contact_email', '' );
	$support_email = sanitize_email( is_scalar( $option_email ) ? (string) $option_email : '' );
}
?>

<main id="main-content" class="site-main partner-review-page">
	<section class="partner-review-hero" aria-labelledby="contact-title">
		<div class="partner-review-hero__inner">
			<p class="partner-review-hero__eyebrow"><?php esc_html_e( 'Contact', 'bookings_and_flights' ); ?></p>
			<h1 id="contact-title" class="partner-review-hero__title"><?php esc_html_e( 'Site support and partner-review contact', 'bookings_and_flights' ); ?></h1>
			<p class="partner-review-hero__lede"><?php esc_html_e( 'Use this page for questions about the Bookings and Flights website, editorial content, affiliate disclosures, and partner search placement.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<section class="partner-review-section" aria-labelledby="contact-options-title">
		<div class="partner-review-section__inner partner-review-split">
			<div>
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Support path', 'bookings_and_flights' ); ?></p>
				<h2 id="contact-options-title"><?php esc_html_e( 'How to reach the site owner', 'bookings_and_flights' ); ?></h2>
			</div>
			<div class="partner-review-prose">
				<?php if ( '' !== $support_email && is_email( $support_email ) ) : ?>
					<p>
						<?php esc_html_e( 'For website questions, affiliate disclosures, content corrections, and partner-review verification, email:', 'bookings_and_flights' ); ?>
						<a href="mailto:<?php echo esc_attr( $support_email ); ?>"><?php echo esc_html( $support_email ); ?></a>
					</p>
				<?php else : ?>
					<p><?php esc_html_e( 'A public support email has not been configured yet. Add the same contact email used in affiliate-network profiles under the Bookings and Flights settings before submitting partner reviews.', 'bookings_and_flights' ); ?></p>
				<?php endif; ?>
				<p><?php esc_html_e( 'For completed reservations, use the confirmation email and support channel from the travel provider. Bookings and Flights does not process payments, refunds, changes, or cancellations for partner bookings.', 'bookings_and_flights' ); ?></p>
			</div>
		</div>
	</section>

	<section class="partner-review-section partner-review-section--muted" aria-labelledby="contact-review-title">
		<div class="partner-review-section__inner">
			<div class="partner-review-section__intro">
				<p class="partner-review-section__eyebrow"><?php esc_html_e( 'Review information', 'bookings_and_flights' ); ?></p>
				<h2 id="contact-review-title"><?php esc_html_e( 'Affiliate and provider review details', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Bookings and Flights is a travel-planning website that uses disclosed partner tools for search and booking handoff. Organic content, WordPress-owned guide pages, and visible legal pages should remain public for affiliate-program review.', 'bookings_and_flights' ); ?></p>
			</div>
			<ul class="partner-review-list">
				<li><?php esc_html_e( 'Public legal pages: Privacy Policy and Terms & Conditions.', 'bookings_and_flights' ); ?></li>
				<li><?php esc_html_e( 'Public disclosure: partner links and widgets may earn commissions.', 'bookings_and_flights' ); ?></li>
				<li><?php esc_html_e( 'Traffic approach: organic travel discovery content and destination planning pages.', 'bookings_and_flights' ); ?></li>
				<li><?php esc_html_e( 'Provider boundary: live availability, booking, payment, and reservation support stay with the selected provider.', 'bookings_and_flights' ); ?></li>
			</ul>
		</div>
	</section>
</main>

<?php get_footer(); ?>
