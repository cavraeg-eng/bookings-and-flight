<?php
/**
 * Hotels search page.
 *
 * @package Bookings_and_Flights_Static
 */

$get_text = static function ( string $key ): string {
	if ( ! isset( $_GET[ $key ] ) || ! is_scalar( $_GET[ $key ] ) ) {
		return '';
	}

	return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
};

$normalize_date = static function ( string $value ): string {
	$value = trim( $value );

	return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
};

$stay_focus_options = array(
	'central'     => __( 'Central neighborhoods', 'bookings_and_flights' ),
	'family'      => __( 'Family stays', 'bookings_and_flights' ),
	'work_trip'    => __( 'Work trip', 'bookings_and_flights' ),
	'pool_spa'     => __( 'Pool or spa', 'bookings_and_flights' ),
	'budget'       => __( 'Budget-friendly', 'bookings_and_flights' ),
	'near_transit' => __( 'Near transit', 'bookings_and_flights' ),
);

$destination = trim( $get_text( 'travel_destination' ) );
$destination = substr( $destination, 0, 80 );
$check_in    = $normalize_date( $get_text( 'check_in' ) );
$check_out   = $normalize_date( $get_text( 'check_out' ) );
$surface     = sanitize_key( $get_text( 'baf_surface' ) );
$stay_focus  = sanitize_key( $get_text( 'stay_focus' ) );
$stay_focus  = isset( $stay_focus_options[ $stay_focus ] ) ? $stay_focus : '';
$has_guests_intent = isset( $_GET['guests'] ) && is_scalar( $_GET['guests'] );
$has_rooms_intent  = isset( $_GET['rooms'] ) && is_scalar( $_GET['rooms'] );
$guests             = $has_guests_intent ? absint( wp_unslash( $_GET['guests'] ) ) : 2;
$guests             = min( 12, max( 1, $guests ) );
$rooms              = $has_rooms_intent ? absint( wp_unslash( $_GET['rooms'] ) ) : 1;
$rooms              = min( 6, max( 1, $rooms ) );

$has_intent = '' !== $destination || '' !== $check_in || '' !== $check_out || $has_guests_intent || $has_rooms_intent || '' !== $stay_focus;

$hotels_css = get_template_directory() . '/assets/css/hotels-surface.css';
if ( file_exists( $hotels_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-hotels-surface',
		get_template_directory_uri() . '/assets/css/hotels-surface.css',
		array( 'bookings_and_flights-search-surface' ),
		filemtime( $hotels_css )
	);
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

if ( $has_guests_intent ) {
	$details[] = array(
		'label' => __( 'Guests', 'bookings_and_flights' ),
		'value' => sprintf(
			/* translators: %d: number of hotel guests. */
			_n( '%d guest', '%d guests', $guests, 'bookings_and_flights' ),
			$guests
		),
	);
}

if ( $has_rooms_intent ) {
	$details[] = array(
		'label' => __( 'Rooms', 'bookings_and_flights' ),
		'value' => sprintf(
			/* translators: %d: number of hotel rooms. */
			_n( '%d room', '%d rooms', $rooms, 'bookings_and_flights' ),
			$rooms
		),
	);
}

if ( '' !== $stay_focus ) {
	$details[] = array(
		'label' => __( 'Stay focus', 'bookings_and_flights' ),
		'value' => $stay_focus_options[ $stay_focus ],
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
			<h1 id="hotel-search-title" class="search-page__title"><?php esc_html_e( 'Hotel search with partner handoff', 'bookings_and_flights' ); ?></h1>
			<p class="search-page__lede"><?php esc_html_e( 'Shape the city, dates, rooms, and stay style inside the Bookings and Flights shell, then continue into the configured Trip.com or Travelpayouts Hotels partner surface for live availability and booking.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>

	<div class="search-page__content">
		<section class="hotel-intent" aria-labelledby="hotel-intent-title">
			<div class="hotel-intent__content">
				<p class="hotel-intent__eyebrow"><?php esc_html_e( 'Hotel intent', 'bookings_and_flights' ); ?></p>
				<h2 id="hotel-intent-title" class="hotel-intent__title"><?php esc_html_e( 'Set the stay shape before opening provider search', 'bookings_and_flights' ); ?></h2>
				<p class="hotel-intent__copy"><?php esc_html_e( 'These fields keep your hotel search intent visible on this page. Final live availability, taxes, fees, policies, room choices, payment, changes, and support are confirmed with the partner provider.', 'bookings_and_flights' ); ?></p>
			</div>

			<form class="hotel-intent__form" action="<?php echo esc_url( home_url( '/hotels/' ) ); ?>" method="get" data-baf-placement-key="hotels_partner_search">
				<div class="hotel-intent__grid">
					<label class="hotel-intent__field hotel-intent__field--destination">
						<span><?php esc_html_e( 'Destination', 'bookings_and_flights' ); ?></span>
						<input type="text" name="travel_destination" value="<?php echo esc_attr( $destination ); ?>" placeholder="<?php esc_attr_e( 'Miami Beach', 'bookings_and_flights' ); ?>" autocomplete="address-level2" maxlength="80">
					</label>
					<label class="hotel-intent__field">
						<span><?php esc_html_e( 'Check in', 'bookings_and_flights' ); ?></span>
						<input type="date" name="check_in" value="<?php echo esc_attr( $check_in ); ?>">
					</label>
					<label class="hotel-intent__field">
						<span><?php esc_html_e( 'Check out', 'bookings_and_flights' ); ?></span>
						<input type="date" name="check_out" value="<?php echo esc_attr( $check_out ); ?>">
					</label>
					<label class="hotel-intent__field">
						<span><?php esc_html_e( 'Guests', 'bookings_and_flights' ); ?></span>
						<input type="number" name="guests" value="<?php echo esc_attr( (string) $guests ); ?>" min="1" max="12" inputmode="numeric">
					</label>
					<label class="hotel-intent__field">
						<span><?php esc_html_e( 'Rooms', 'bookings_and_flights' ); ?></span>
						<input type="number" name="rooms" value="<?php echo esc_attr( (string) $rooms ); ?>" min="1" max="6" inputmode="numeric">
					</label>
					<label class="hotel-intent__field hotel-intent__field--focus">
						<span><?php esc_html_e( 'Stay focus', 'bookings_and_flights' ); ?></span>
						<select name="stay_focus">
							<option value=""><?php esc_html_e( 'Choose in partner search', 'bookings_and_flights' ); ?></option>
							<?php foreach ( $stay_focus_options as $focus_key => $focus_label ) : ?>
								<option value="<?php echo esc_attr( $focus_key ); ?>" <?php selected( $stay_focus, $focus_key ); ?>><?php echo esc_html( $focus_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>

				<input type="hidden" name="baf_surface" value="hotels_landing">
				<button class="hotel-intent__submit" type="submit"><?php esc_html_e( 'Update hotel intent', 'bookings_and_flights' ); ?></button>
				<p class="hotel-intent__helper"><?php esc_html_e( 'Intent only: use the embedded partner search or sponsored handoff to confirm live rates, room availability, taxes, policies, booking, payment, changes, and support.', 'bookings_and_flights' ); ?></p>
			</form>

			<div class="hotel-intent__provider" aria-labelledby="hotel-provider-options-title">
				<h3 id="hotel-provider-options-title"><?php esc_html_e( 'Provider-controlled hotel choices', 'bookings_and_flights' ); ?></h3>
				<p><?php esc_html_e( 'WordPress keeps the editorial shell and your intent summary. Set live filters inside the partner surface when those controls are available there.', 'bookings_and_flights' ); ?></p>
				<ul class="hotel-intent__provider-list">
					<li>
						<span><?php esc_html_e( 'Live rates and taxes', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Room type and policies', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Map, neighborhood, and amenity filters', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Provider', 'bookings_and_flights' ); ?></strong>
					</li>
					<li>
						<span><?php esc_html_e( 'Booking, payment, changes, and support', 'bookings_and_flights' ); ?></span>
						<strong><?php esc_html_e( 'Provider', 'bookings_and_flights' ); ?></strong>
					</li>
				</ul>
			</div>
		</section>
	</div>

	<div id="hotels-provider-search" class="search-page__content search-page__content--provider">
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
				'eyebrow'          => __( 'Hotels partner surface', 'bookings_and_flights' ),
				'title'            => __( 'Open live hotel search', 'bookings_and_flights' ),
				'description'      => $has_intent ? __( 'Your hotel intent is summarized below. Use the embedded partner controls or open the sponsored handoff to confirm live availability and booking details.', 'bookings_and_flights' ) : __( 'Use the embedded partner controls or open the sponsored handoff to search live hotel availability. WordPress does not store or rank live room inventory.', 'bookings_and_flights' ),
				'details'          => $details,
				'fallback_message' => __( 'Hotel search is configured through the Travelpayouts placement registry. If it is unavailable, check provider consent or placement settings.', 'bookings_and_flights' ),
				'support_note'     => __( 'Sponsored hotel search may earn a commission. Bookings and Flights keeps the planning shell visible; live rooms, rates, taxes, booking, payment, changes, and support stay with Trip.com, Travelpayouts, or the partner provider.', 'bookings_and_flights' ),
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
