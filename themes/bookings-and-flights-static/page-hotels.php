<?php
/**
 * Hotels search page.
 *
 * @package Bookings_and_Flights_Static
 */

$destination = isset( $_GET['travel_destination'] ) ? sanitize_text_field( wp_unslash( $_GET['travel_destination'] ) ) : '';
$check_in    = isset( $_GET['check_in'] ) ? sanitize_text_field( wp_unslash( $_GET['check_in'] ) ) : '';
$check_out   = isset( $_GET['check_out'] ) ? sanitize_text_field( wp_unslash( $_GET['check_out'] ) ) : '';
$guests      = isset( $_GET['guests'] ) ? absint( wp_unslash( $_GET['guests'] ) ) : 0;
$surface     = isset( $_GET['baf_surface'] ) ? sanitize_key( wp_unslash( $_GET['baf_surface'] ) ) : '';

if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $check_in ) ) {
	$check_in = '';
}

if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $check_out ) ) {
	$check_out = '';
}

$details = array();
if ( '' !== $destination ) {
	$details[] = array(
		'label' => __( 'Destination', 'bookings_and_flights' ),
		'value' => $destination,
	);
}

if ( '' !== $check_in ) {
	$details[] = array(
		'label' => __( 'Check in', 'bookings_and_flights' ),
		'value' => $check_in,
	);
}

if ( '' !== $check_out ) {
	$details[] = array(
		'label' => __( 'Check out', 'bookings_and_flights' ),
		'value' => $check_out,
	);
}

if ( $guests > 0 ) {
	$details[] = array(
		'label' => __( 'Guests', 'bookings_and_flights' ),
		'value' => sprintf(
			/* translators: %d: number of hotel guests. */
			_n( '%d guest', '%d guests', $guests, 'bookings_and_flights' ),
			$guests
		),
	);
}

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'search-surface-page';

		return $classes;
	}
);

get_header();
?>

<main id="main-content" class="search-page search-page--hotels">
	<section class="search-page__hero" aria-labelledby="hotel-search-title">
		<div class="search-page__hero-inner">
			<p class="search-page__eyebrow"><?php esc_html_e( 'Hotels', 'bookings_and_flights' ); ?></p>
			<h1 id="hotel-search-title" class="search-page__title"><?php esc_html_e( 'Hotel search through approved partner placement', 'bookings_and_flights' ); ?></h1>
			<p class="search-page__lede"><?php esc_html_e( 'Continue into the configured Trip.com or Travelpayouts Hotels partner surface inside the WordPress page shell. Availability, reservation actions, changes, and support stay with the provider.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<div class="search-page__content">
		<?php
		get_template_part(
			'template-parts/travel-search-placement',
			null,
			array(
				'placement'        => 'hotels_partner_search',
				'surface'          => 'hotels',
				'channel'          => 'home' === $surface ? 'homepage' : 'search_page',
				'slug'             => 'hotel_search',
				'class'            => 'search-placement--hotels',
				'eyebrow'          => __( 'Trip.com partner surface', 'bookings_and_flights' ),
				'title'            => __( 'Search hotels', 'bookings_and_flights' ),
				'description'      => __( 'Use the partner search controls below or open the sponsored handoff when the embedded surface is blocked by the browser.', 'bookings_and_flights' ),
				'details'          => $details,
				'fallback_message' => __( 'Hotel search is configured through the Travelpayouts placement registry. If it is unavailable, check provider consent or placement settings.', 'bookings_and_flights' ),
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
