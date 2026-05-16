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

$normalize_label = static function ( string $value ): string {
	return substr( sanitize_text_field( $value ), 0, 120 );
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

$compact_flight_search = strtoupper( preg_replace( '/[^A-Z0-9]/', '', $get_text( 'flightSearch' ) ) );
$compact_origin        = '';
$compact_destination   = '';
$compact_depart_date   = '';
$compact_return_date   = '';

$compact_date = static function ( string $day, string $month, int $year ): string {
	$date = DateTimeImmutable::createFromFormat( '!Y-m-d', sprintf( '%04d-%02d-%02d', $year, absint( $month ), absint( $day ) ), wp_timezone() );

	if ( ! $date instanceof DateTimeImmutable ) {
		return '';
	}

	return $date->format( 'Y-m-d' );
};

if ( preg_match( '/^([A-Z]{3})(\d{2})(\d{2})([A-Z]{3})(?:(\d{2})(\d{2}))?/', $compact_flight_search, $compact_matches ) ) {
	$compact_origin      = $normalize_iata( $compact_matches[1] );
	$compact_destination = $normalize_iata( $compact_matches[4] );
	$current_year        = absint( wp_date( 'Y' ) );
	$today               = wp_date( 'Y-m-d' );
	$compact_depart_date = $compact_date( $compact_matches[2], $compact_matches[3], $current_year );

	if ( '' !== $compact_depart_date && $compact_depart_date < $today ) {
		$compact_depart_date = $compact_date( $compact_matches[2], $compact_matches[3], $current_year + 1 );
	}

	if ( isset( $compact_matches[5], $compact_matches[6] ) ) {
		$compact_return_date = $compact_date( $compact_matches[5], $compact_matches[6], $current_year );

		if ( '' !== $compact_depart_date && '' !== $compact_return_date && $compact_return_date < $compact_depart_date ) {
			$compact_return_date = $compact_date( $compact_matches[5], $compact_matches[6], $current_year + 1 );
		}
	}
}

$origin            = $normalize_iata( $get_text( 'origin' ) );
$destination       = $normalize_iata( $get_text( 'destination' ) );
$origin            = '' !== $origin ? $origin : $compact_origin;
$destination       = '' !== $destination ? $destination : $compact_destination;
$origin_label      = $normalize_label( $get_text( 'travel_origin' ) );
$destination_label = $normalize_label( $get_text( 'travel_destination' ) );
$depart_date       = $normalize_date( $get_text( 'depart_date' ) );
$return_date       = $normalize_date( $get_text( 'return_date' ) );
$depart_date       = '' !== $depart_date ? $depart_date : $compact_depart_date;
$return_date       = '' !== $return_date ? $return_date : $compact_return_date;
$surface           = sanitize_key( $get_text( 'baf_surface' ) );
$travelers         = isset( $_GET['travelers'] ) && is_scalar( $_GET['travelers'] ) ? absint( wp_unslash( $_GET['travelers'] ) ) : 1;
$travelers         = min( 9, max( 1, $travelers ) );
$cabin             = sanitize_key( $get_text( 'cabin' ) );
$cabin             = isset( $cabin_options[ $cabin ] ) ? $cabin : 'economy';
$travel_mode       = sanitize_key( $get_text( 'travel_mode' ) );
$travel_focus      = sanitize_key( $get_text( 'travel_focus' ) );

$has_traveler_intent = isset( $_GET['travelers'] ) || isset( $_GET['cabin'] );
$has_intent          = '' !== $origin || '' !== $destination || '' !== $origin_label || '' !== $destination_label
	|| '' !== $depart_date || '' !== $return_date || $has_traveler_intent || '' !== $travel_mode || '' !== $travel_focus
	|| '' !== $compact_flight_search;

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
if ( '' !== $origin || '' !== $destination || '' !== $origin_label || '' !== $destination_label ) {
	$route_origin      = '' !== $origin ? $origin : $origin_label;
	$route_destination = '' !== $destination ? $destination : $destination_label;
	$route             = trim( $route_origin . ( '' !== $route_origin && '' !== $route_destination ? ' to ' : '' ) . $route_destination );
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

if ( in_array( $travel_focus, array( 'deal_dates', 'flexible_city', 'long_weekend', 'price_alert', 'school_break' ), true ) ) {
	$focus_labels = array(
		'deal_dates'    => __( 'Flexible travel dates', 'bookings_and_flights' ),
		'flexible_city' => __( 'Flexible destination idea', 'bookings_and_flights' ),
		'long_weekend'  => __( 'Long weekend window', 'bookings_and_flights' ),
		'price_alert'   => __( 'Price alert interest', 'bookings_and_flights' ),
		'school_break'  => __( 'School-break planning', 'bookings_and_flights' ),
	);
	$details[] = array(
		'label' => __( 'Focus', 'bookings_and_flights' ),
		'value' => $focus_labels[ $travel_focus ],
	);
}

if ( '' !== $origin || '' !== $destination ) {
	add_filter( 'bookings_and_flights_has_official_travelpayouts_output', '__return_true' );
}

$render_provider_search = static function () use ( $details, $destination, $has_intent, $origin, $surface ): void {
	$continuity_links = array(
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
	);

	if ( $has_intent ) {
		$continuity_links[] = array(
			'label' => __( 'Refine search', 'bookings_and_flights' ),
			'url'   => '#flight-intent-title',
		);
	}

	get_template_part(
		'template-parts/white-label-continuity',
		null,
		array(
			'title' => __( 'Search here, book with the travel site', 'bookings_and_flights' ),
			'copy'  => __( 'Use the live flight results below to compare fares and filters without losing this page. When you choose a fare, checkout and support happen with that booking site.', 'bookings_and_flights' ),
			'links' => $continuity_links,
		)
	);

	get_template_part(
		'template-parts/travel-search-placement',
		null,
		array(
			'placement'        => 'flights_white_label_search',
			'surface'          => 'flights',
			'channel'          => 'home' === $surface ? 'homepage' : 'search_page',
			'slug'             => 'flight_search',
			'class'            => $has_intent ? 'search-placement--flights search-placement--has-intent search-placement--priority' : 'search-placement--flights',
			'eyebrow'          => __( 'Travelpayouts White Label', 'bookings_and_flights' ),
			'title'            => $has_intent ? __( 'Your flight search is ready', 'bookings_and_flights' ) : __( 'Search flights', 'bookings_and_flights' ),
			'description'      => $has_intent ? __( 'Your route is already filled in. Use the live results below to compare fares, adjust filters, and pick the booking site that fits.', 'bookings_and_flights' ) : __( 'Start with a route, then compare live fares and filters in the results area below. Choose a fare only when you are ready to open the booking site.', 'bookings_and_flights' ),
			'origin'           => $origin,
			'destination'      => $destination,
			'details'          => $details,
			'fallback_message' => __( 'Flight search is configured through the Travelpayouts placement registry. If it is unavailable, check provider consent or placement settings.', 'bookings_and_flights' ),
		)
	);
};

add_filter(
	'body_class',
	static function ( array $classes ) use ( $has_intent ): array {
		$classes[] = 'search-surface-page';

		if ( $has_intent ) {
			$classes[] = 'search-surface-page--provider-first';
		}

		return $classes;
	}
);

get_header();
?>

<main id="main-content" class="search-page search-page--flights">
	<section class="search-page__hero" aria-labelledby="flight-search-title">
		<div class="search-page__hero-inner">
			<p class="search-page__eyebrow"><?php esc_html_e( 'Flights', 'bookings_and_flights' ); ?></p>
			<h1 id="flight-search-title" class="search-page__title"><?php echo esc_html( $has_intent ? __( 'Your flight search is ready', 'bookings_and_flights' ) : __( 'Find a flight and keep moving', 'bookings_and_flights' ) ); ?></h1>
			<p class="search-page__lede"><?php echo esc_html( $has_intent ? __( 'Live results are ready below. Compare fares, adjust filters, and open the booking site only when a flight looks right.', 'bookings_and_flights' ) : __( 'Enter your route, dates, and travelers. We will open live flight results on this page and send you to the booking site only when you choose a fare.', 'bookings_and_flights' ) ); ?></p>
		</div>
	</section>

	<?php if ( $has_intent ) : ?>
		<div id="flights-provider-search" class="search-page__content search-page__content--provider search-page__content--provider-first">
			<?php $render_provider_search(); ?>
		</div>
	<?php endif; ?>

	<div class="search-page__content">
		<section class="flight-intent" aria-labelledby="flight-intent-title">
			<div class="flight-intent__content">
				<p class="flight-intent__eyebrow"><?php esc_html_e( 'Start your flight search', 'bookings_and_flights' ); ?></p>
				<h2 id="flight-intent-title" class="flight-intent__title"><?php echo esc_html( $has_intent ? __( 'Refine this flight search', 'bookings_and_flights' ) : __( 'Search flights without losing your place', 'bookings_and_flights' ) ); ?></h2>
				<p class="flight-intent__copy"><?php echo esc_html( $has_intent ? __( 'Change the route or dates here, then run the search again to refresh the live results.', 'bookings_and_flights' ) : __( 'Tell us where and when you want to go. Your results will open below, and this page will keep your route summary visible while you compare options.', 'bookings_and_flights' ) ); ?></p>
			</div>

			<form class="flight-intent__form" action="<?php echo esc_url( home_url( '/flights/#flights-provider-search' ) ); ?>" method="get" data-baf-placement-key="flights_white_label_search">
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
				<button class="flight-intent__submit" type="submit"><?php esc_html_e( 'Search flights', 'bookings_and_flights' ); ?></button>
				<p class="flight-intent__helper"><?php esc_html_e( 'After you search, use the live results below to compare fares. Final booking, payment, changes, and support happen with the travel site you choose.', 'bookings_and_flights' ); ?></p>
			</form>

			<div class="flight-intent__provider" aria-labelledby="flight-provider-options-title">
				<h3 id="flight-provider-options-title"><?php esc_html_e( 'What happens after you search', 'bookings_and_flights' ); ?></h3>
				<p><?php esc_html_e( 'Your route opens in the live results area below. From there, narrow the list, compare the fare details, and continue only when the flight feels right.', 'bookings_and_flights' ); ?></p>
				<ol class="flight-intent__provider-list">
					<li>
						<strong><?php esc_html_e( '1', 'bookings_and_flights' ); ?></strong>
						<span><?php esc_html_e( 'Compare live fares', 'bookings_and_flights' ); ?></span>
						<p><?php esc_html_e( 'See current prices, schedules, and seat availability in one results area.', 'bookings_and_flights' ); ?></p>
					</li>
					<li>
						<strong><?php esc_html_e( '2', 'bookings_and_flights' ); ?></strong>
						<span><?php esc_html_e( 'Fine-tune the trip', 'bookings_and_flights' ); ?></span>
						<p><?php esc_html_e( 'Filter by stops, nearby airports, flexible dates, baggage, airlines, and times.', 'bookings_and_flights' ); ?></p>
					</li>
					<li>
						<strong><?php esc_html_e( '3', 'bookings_and_flights' ); ?></strong>
						<span><?php esc_html_e( 'Choose where to book', 'bookings_and_flights' ); ?></span>
						<p><?php esc_html_e( 'When a fare works, Buy opens the travel site that will handle checkout and support.', 'bookings_and_flights' ); ?></p>
					</li>
				</ol>
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

	<?php if ( ! $has_intent ) : ?>
		<div id="flights-provider-search" class="search-page__content search-page__content--provider search-page__content--provider-empty">
			<section class="flight-results-empty" aria-labelledby="flight-results-empty-title">
				<p class="flight-results-empty__eyebrow"><?php esc_html_e( 'Live results', 'bookings_and_flights' ); ?></p>
				<h2 id="flight-results-empty-title"><?php esc_html_e( 'Your results will appear here after you search', 'bookings_and_flights' ); ?></h2>
				<p><?php esc_html_e( 'Enter both airports, dates, and travelers in the flight form above. We load the partner results only after the trip details are clear, so the search area does not open blank.', 'bookings_and_flights' ); ?></p>
				<a class="flight-results-empty__link" href="#flight-intent-title"><?php esc_html_e( 'Start flight search', 'bookings_and_flights' ); ?></a>
			</section>
		</div>
	<?php endif; ?>

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
