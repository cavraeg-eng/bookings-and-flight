<?php
/**
 * Flights search page.
 *
 * @package Bookings_and_Flights_Static
 */

$get_text = static function ( string $key ): string {
	if ( ! isset( $_GET[ $key ] ) || ! is_scalar( $_GET[ $key ] ) ) {
		return '';
	}

	return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
};

$normalize_iata = static function ( string $value ): string {
	$value = strtoupper( trim( $value ) );

	return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
};

$normalize_date = static function ( string $value ): string {
	$value = trim( $value );

	return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
};

$cabin_options = array(
	'economy'         => __( 'Economy', 'bookings_and_flights' ),
	'premium_economy' => __( 'Premium economy', 'bookings_and_flights' ),
	'business'        => __( 'Business', 'bookings_and_flights' ),
	'first'           => __( 'First', 'bookings_and_flights' ),
);

$origin      = $normalize_iata( $get_text( 'origin' ) );
$destination = $normalize_iata( $get_text( 'destination' ) );
$depart_date = $normalize_date( $get_text( 'depart_date' ) );
$return_date = $normalize_date( $get_text( 'return_date' ) );
$surface     = sanitize_key( $get_text( 'baf_surface' ) );
$travelers   = isset( $_GET['travelers'] ) && is_scalar( $_GET['travelers'] ) ? absint( wp_unslash( $_GET['travelers'] ) ) : 1;
$travelers   = min( 9, max( 1, $travelers ) );
$cabin       = sanitize_key( $get_text( 'cabin' ) );
$cabin       = isset( $cabin_options[ $cabin ] ) ? $cabin : 'economy';
$travel_mode  = sanitize_key( $get_text( 'travel_mode' ) );
$travel_focus = sanitize_key( $get_text( 'travel_focus' ) );

$has_traveler_intent = isset( $_GET['travelers'] ) || isset( $_GET['cabin'] );
$has_intent          = '' !== $origin || '' !== $destination || '' !== $depart_date || '' !== $return_date || $has_traveler_intent || '' !== $travel_mode || '' !== $travel_focus;

$continuity_css = get_template_directory() . '/assets/css/white-label-continuity.css';
if ( file_exists( $continuity_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-white-label-continuity',
		get_template_directory_uri() . '/assets/css/white-label-continuity.css',
		array( 'bookings_and_flights-search-surface' ),
		filemtime( $continuity_css )
	);
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

if ( $has_traveler_intent ) {
	$details[] = array(
		'label' => __( 'Travelers', 'bookings_and_flights' ),
		'value' => sprintf(
			/* translators: 1: number of travelers, 2: cabin label. */
			_n( '%1$d traveler, %2$s', '%1$d travelers, %2$s', $travelers, 'bookings_and_flights' ),
			$travelers,
			$cabin_options[ $cabin ]
		),
	);
}

if ( in_array( $travel_mode, array( 'explore', 'planner' ), true ) ) {
	$details[] = array(
		'label' => __( 'Planning mode', 'bookings_and_flights' ),
		'value' => 'explore' === $travel_mode ? __( 'Explore anywhere', 'bookings_and_flights' ) : __( 'Trip planning handoff', 'bookings_and_flights' ),
	);
}

if ( in_array( $travel_focus, array( 'deal_dates', 'flexible_city', 'price_alert' ), true ) ) {
	$focus_labels = array(
		'deal_dates'    => __( 'Flexible travel dates', 'bookings_and_flights' ),
		'flexible_city' => __( 'Flexible destination idea', 'bookings_and_flights' ),
		'price_alert'   => __( 'Price alert interest', 'bookings_and_flights' ),
	);
	$details[] = array(
		'label' => __( 'Focus', 'bookings_and_flights' ),
		'value' => $focus_labels[ $travel_focus ],
	);
}

if ( '' !== $origin || '' !== $destination ) {
	add_filter( 'bookings_and_flights_has_official_travelpayouts_output', '__return_true' );
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
			<h1 id="flight-search-title" class="search-page__title"><?php esc_html_e( 'Flight search with Travelpayouts handoff', 'bookings_and_flights' ); ?></h1>
			<p class="search-page__lede"><?php esc_html_e( 'Start with your route, dates, and traveler intent in the Bookings and Flights shell, then continue into the approved Travelpayouts White Label module for provider-owned flight results.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<div class="search-page__content">
		<section class="flight-intent" aria-labelledby="flight-intent-title">
			<div class="flight-intent__content">
				<p class="flight-intent__eyebrow"><?php esc_html_e( 'Flight intent', 'bookings_and_flights' ); ?></p>
				<h2 id="flight-intent-title" class="flight-intent__title"><?php esc_html_e( 'Set the trip shape before opening provider search', 'bookings_and_flights' ); ?></h2>
				<p class="flight-intent__copy"><?php esc_html_e( 'These fields keep your search intent visible on this page. Final route, travelers, cabin, flexible dates, and filter choices are confirmed inside Travelpayouts after handoff.', 'bookings_and_flights' ); ?></p>
			</div>

			<form class="flight-intent__form" action="<?php echo esc_url( home_url( '/flights/' ) ); ?>" method="get" data-baf-placement-key="flights_white_label_search">
				<div class="flight-intent__grid">
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'From', 'bookings_and_flights' ); ?></span>
						<input type="text" name="origin" value="<?php echo esc_attr( $origin ); ?>" placeholder="<?php esc_attr_e( 'NYC', 'bookings_and_flights' ); ?>" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="<?php esc_attr_e( 'Use a 3-letter airport or city code', 'bookings_and_flights' ); ?>">
					</label>
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'To', 'bookings_and_flights' ); ?></span>
						<input type="text" name="destination" value="<?php echo esc_attr( $destination ); ?>" placeholder="<?php esc_attr_e( 'LAX', 'bookings_and_flights' ); ?>" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="<?php esc_attr_e( 'Use a 3-letter airport or city code', 'bookings_and_flights' ); ?>">
					</label>
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'Depart', 'bookings_and_flights' ); ?></span>
						<input type="date" name="depart_date" value="<?php echo esc_attr( $depart_date ); ?>">
					</label>
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'Return', 'bookings_and_flights' ); ?></span>
						<input type="date" name="return_date" value="<?php echo esc_attr( $return_date ); ?>">
					</label>
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'Travelers', 'bookings_and_flights' ); ?></span>
						<input type="number" name="travelers" value="<?php echo esc_attr( (string) $travelers ); ?>" min="1" max="9" inputmode="numeric">
					</label>
					<label class="flight-intent__field">
						<span><?php esc_html_e( 'Cabin', 'bookings_and_flights' ); ?></span>
						<select name="cabin">
							<?php foreach ( $cabin_options as $cabin_key => $cabin_label ) : ?>
								<option value="<?php echo esc_attr( $cabin_key ); ?>" <?php selected( $cabin, $cabin_key ); ?>><?php echo esc_html( $cabin_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>

				<input type="hidden" name="baf_surface" value="flights_landing">
				<button class="flight-intent__submit" type="submit"><?php esc_html_e( 'Update flight intent', 'bookings_and_flights' ); ?></button>
				<p class="flight-intent__helper"><?php esc_html_e( 'Intent only: Travelpayouts controls provider availability, result filters, booking, changes, payment, and reservation support.', 'bookings_and_flights' ); ?></p>
			</form>

			<div class="flight-intent__provider" aria-labelledby="flight-provider-options-title">
				<h3 id="flight-provider-options-title"><?php esc_html_e( 'Provider-controlled options', 'bookings_and_flights' ); ?></h3>
				<p><?php esc_html_e( 'These choices are not applied by WordPress. Set them inside the Travelpayouts White Label module when the provider search opens.', 'bookings_and_flights' ); ?></p>
				<ul class="flight-intent__provider-list">
					<li>
						<span><?php esc_html_e( 'Direct-only flights', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Set in provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Nearby airports', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Set in provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Flexible-date calendar', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Set in provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Airline, baggage, and time filters', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Set in provider', 'bookings_and_flights' ); ?></strong>
					</li>
				</ul>
			</div>
		</section>

		<?php if ( shortcode_exists( 'baf_flight_alert_signup' ) ) : ?>
			<?php
			echo do_shortcode(
				sprintf(
					'[baf_flight_alert_signup origin="%1$s" destination="%2$s" depart_date="%3$s" return_date="%4$s" travelers="%5$d" cabin="%6$s" surface="flights" context="flights"]',
					esc_attr( $origin ),
					esc_attr( $destination ),
					esc_attr( $depart_date ),
					esc_attr( $return_date ),
					$travelers,
					esc_attr( $cabin )
				)
			);
			?>
		<?php else : ?>
			<section class="flight-intent" aria-label="<?php esc_attr_e( 'Price alert status', 'bookings_and_flights' ); ?>">
				<p class="flight-intent__helper"><?php esc_html_e( 'Price alert capture is unavailable until the Bookings and Flights Core alert workflow is active. Use the provider search to confirm live fares.', 'bookings_and_flights' ); ?></p>
			</section>
		<?php endif; ?>
	</div>

	<div id="flights-provider-search" class="search-page__content search-page__content--provider">
		<?php
		get_template_part(
			'template-parts/white-label-continuity',
			null,
			array(
				'title' => __( 'Provider search stays in the Bookings and Flights shell', 'bookings_and_flights' ),
				'copy'  => __( 'The embedded Travelpayouts Widget-type White Label keeps the site header, navigation, footer, and affiliate disclosure visible while Travelpayouts controls live results, filters, booking, payment, changes, and support.', 'bookings_and_flights' ),
				'links' => array(
					array(
						'label' => __( 'Home', 'bookings_and_flights' ),
						'url'   => home_url( '/' ),
					),
					array(
						'label' => __( 'Flights', 'bookings_and_flights' ),
						'url'   => home_url( '/flights/' ),
					),
					array(
						'label' => __( 'Route guides', 'bookings_and_flights' ),
						'url'   => get_post_type_archive_link( 'route' ) ?: home_url( '/routes/' ),
					),
				),
			)
		);
		?>

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
				'description'      => __( 'Continue in the approved White Label search and result module. Adjust route, dates, travelers, cabin, flexible-date, direct-only, and nearby-airport choices inside the provider-owned controls as needed.', 'bookings_and_flights' ),
				'origin'           => $origin,
				'destination'      => $destination,
				'details'          => $details,
				'fallback_message' => __( 'Flight search is configured through the Travelpayouts placement registry. If it is unavailable, check provider consent or placement settings.', 'bookings_and_flights' ),
			)
		);
		?>
	</div>

	<div class="search-page__content search-page__content--discovery">
		<?php
		get_template_part(
			'template-parts/flight-discovery-widgets',
			null,
			array(
				'surface'     => 'flights',
				'channel'     => 'home' === $surface ? 'homepage' : 'search_page',
				'slug'        => 'flight_search',
				'origin'      => $origin,
				'destination' => $destination,
				'details'     => $details,
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
