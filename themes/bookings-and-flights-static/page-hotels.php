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

$hotel_guide_css = get_template_directory() . '/assets/css/hotel-guide.css';
if ( file_exists( $hotel_guide_css ) ) {
	wp_enqueue_style(
		'bookings_and_flights-hotel-guide',
		get_template_directory_uri() . '/assets/css/hotel-guide.css',
		array( 'bookings_and_flights-hotels-surface' ),
		filemtime( $hotel_guide_css )
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
		$classes[] = 'hotel-guide-page';

		return $classes;
	}
);

$city_guides = new WP_Query(
	array(
		'post_type'      => 'destination',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'no_found_rows'  => true,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);

$destinations_url = get_post_type_archive_link( 'destination' );
$destinations_url = is_string( $destinations_url ) && '' !== $destinations_url ? $destinations_url : home_url( '/destinations/' );

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

	<section class="hotel-guide-teaser" aria-labelledby="hotel-guide-teaser-title">
		<div class="hotel-guide-teaser__inner">
			<div class="hotel-guide-teaser__header">
				<p class="hotel-guide-teaser__eyebrow"><?php esc_html_e( 'City hotel guides', 'bookings_and_flights' ); ?></p>
				<h2 id="hotel-guide-teaser-title" class="hotel-guide-teaser__title"><?php esc_html_e( 'Editorial stay guides before live search', 'bookings_and_flights' ); ?></h2>
				<p class="hotel-guide-teaser__copy"><?php esc_html_e( 'Use destination guides for neighborhoods, landmarks, family stays, luxury stays, and budget planning. Provider search handles live rates, map filters, booking, payment, changes, and support.', 'bookings_and_flights' ); ?></p>
			</div>

			<?php if ( $city_guides->have_posts() ) : ?>
				<div class="hotel-guide-listing">
					<?php
					while ( $city_guides->have_posts() ) :
						$city_guides->the_post();
						$guide_id          = get_the_ID();
						$guide_destination = function_exists( 'bookings_and_flights_destination_label_for_post' ) ? bookings_and_flights_destination_label_for_post( $guide_id ) : wp_strip_all_tags( get_the_title( $guide_id ) );
						$guide_summary     = sanitize_text_field( (string) get_post_meta( $guide_id, 'baf_hotel_guide_summary', true ) );
						$guide_hotel_url   = add_query_arg(
							array(
								'travel_destination' => $guide_destination,
								'baf_surface'        => 'hotels_landing',
							),
							home_url( '/hotels/' )
						);
						?>
						<article class="hotel-guide-listing-card">
							<p class="hotel-guide-listing-card__eyebrow"><?php esc_html_e( 'Editable guide', 'bookings_and_flights' ); ?></p>
							<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<p>
								<?php
								echo esc_html(
									'' !== $guide_summary ? wp_trim_words( $guide_summary, 24, '' ) : sprintf(
										/* translators: %s: destination name. */
										__( 'Review %s stay guidance, then continue into the approved hotel partner surface.', 'bookings_and_flights' ),
										$guide_destination
									)
								);
								?>
							</p>
							<div class="hotel-guide-listing-card__actions">
								<a class="hotel-guide-text-link" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Open city guide', 'bookings_and_flights' ); ?></a>
								<a class="hotel-guide-text-link" href="<?php echo esc_url( $guide_hotel_url ); ?>"><?php esc_html_e( 'Search this city', 'bookings_and_flights' ); ?></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="hotel-guide-empty" role="status">
					<h3><?php esc_html_e( 'City guide slots are ready', 'bookings_and_flights' ); ?></h3>
					<p><?php esc_html_e( 'Published destination posts will appear here as editable hotel guide cards. The hotel search handoff remains available now.', 'bookings_and_flights' ); ?></p>
					<a class="hotel-guide-button" href="<?php echo esc_url( $destinations_url ); ?>"><?php esc_html_e( 'Browse city guides', 'bookings_and_flights' ); ?></a>
				</div>
			<?php endif; ?>

			<div class="hotel-guide-teaser__actions">
				<a class="hotel-guide-button hotel-guide-button--secondary" href="<?php echo esc_url( $destinations_url ); ?>"><?php esc_html_e( 'View all city guides', 'bookings_and_flights' ); ?></a>
			</div>
		</div>
	</section>

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
