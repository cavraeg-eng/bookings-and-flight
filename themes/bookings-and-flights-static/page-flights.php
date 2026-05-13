<?php
/**
 * Flights search page.
 *
 * @package Bookings_and_Flights_Static
 */

$origin      = isset( $_GET['origin'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['origin'] ) ) ) : '';
$destination = isset( $_GET['destination'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['destination'] ) ) ) : '';
$depart_date = isset( $_GET['depart_date'] ) ? sanitize_text_field( wp_unslash( $_GET['depart_date'] ) ) : '';
$return_date = isset( $_GET['return_date'] ) ? sanitize_text_field( wp_unslash( $_GET['return_date'] ) ) : '';
$surface     = isset( $_GET['baf_surface'] ) ? sanitize_key( wp_unslash( $_GET['baf_surface'] ) ) : '';

if ( ! preg_match( '/^[A-Z]{3}$/', $origin ) ) {
	$origin = '';
}

if ( ! preg_match( '/^[A-Z]{3}$/', $destination ) ) {
	$destination = '';
}

if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $depart_date ) ) {
	$depart_date = '';
}

if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $return_date ) ) {
	$return_date = '';
}

$details = array();
if ( '' !== $origin || '' !== $destination ) {
	$route = trim( $origin . ( '' !== $origin && '' !== $destination ? ' to ' : '' ) . $destination );
	$details[] = array(
		'label' => __( 'Route', 'bookings_and_flights' ),
		'value' => $route,
	);
}

if ( '' !== $depart_date ) {
	$details[] = array(
		'label' => __( 'Depart', 'bookings_and_flights' ),
		'value' => $depart_date,
	);
}

if ( '' !== $return_date ) {
	$details[] = array(
		'label' => __( 'Return', 'bookings_and_flights' ),
		'value' => $return_date,
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

<main id="main-content" class="search-page search-page--flights">
	<section class="search-page__hero" aria-labelledby="flight-search-title">
		<div class="search-page__hero-inner">
			<p class="search-page__eyebrow"><?php esc_html_e( 'Flights', 'bookings_and_flights' ); ?></p>
			<h1 id="flight-search-title" class="search-page__title"><?php esc_html_e( 'Flight search inside the Bookings and Flights shell', 'bookings_and_flights' ); ?></h1>
			<p class="search-page__lede"><?php esc_html_e( 'Search and compare provider-owned flight results through the approved Travelpayouts White Label placement. Reservation actions, changes, and support stay with the provider.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<div class="search-page__content">
		<?php
		get_template_part(
			'template-parts/travel-search-placement',
			null,
			array(
				'placement'        => 'flights_white_label_search',
				'surface'          => 'flights',
				'channel'          => 'home' === $surface ? 'homepage' : 'search_page',
				'slug'             => 'flight_search',
				'class'            => 'search-placement--flights',
				'eyebrow'          => __( 'Travelpayouts White Label', 'bookings_and_flights' ),
				'title'            => __( 'Search flights', 'bookings_and_flights' ),
				'description'      => __( 'Your request opens the approved White Label search and result module. Adjust route, dates, and travelers inside the provider-owned search controls as needed.', 'bookings_and_flights' ),
				'details'          => $details,
				'fallback_message' => __( 'Flight search is configured through the Travelpayouts placement registry. If it is unavailable, check provider consent or placement settings.', 'bookings_and_flights' ),
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
