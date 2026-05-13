<?php
/**
 * Template Name: Home
 *
 * @package Bookings_and_Flights_Static
 */

$flight_search_url = home_url( '/flights/' );
$hotel_search_url  = home_url( '/hotels/' );
$hero_image_id     = absint( bookings_and_flights_field( 'hero_image_id', 0 ) );
$fallback_image    = get_template_directory_uri() . '/assets/images/home-hero-beach.jpg';

get_header();
?>

<main id="main-content" class="home-main">
	<section class="home-hero" aria-labelledby="home-hero-title">
		<div class="home-hero__media" aria-hidden="true">
			<?php
			if ( $hero_image_id ) :
				echo bookings_and_flights_image(
					$hero_image_id,
					'full',
					array(
						'class'   => 'home-hero__image',
						'loading' => 'eager',
					)
				);
			else :
				?>
				<img class="home-hero__image" src="<?php echo esc_url( $fallback_image ); ?>" alt="" loading="eager" decoding="async">
			<?php endif; ?>
			<div class="home-hero__overlay"></div>
		</div>

		<div class="home-hero__container">
			<div class="home-hero__copy">
				<h1 id="home-hero-title" class="home-hero__title">Flights and hotels for the trip you actually want</h1>
				<p class="home-hero__lede">Start with a clear travel intent, then continue into Travelpayouts-powered flight search or the approved hotel partner surface. Booking and payment happen with the provider.</p>
			</div>

			<section class="home-search" aria-labelledby="home-search-title">
				<header class="home-search__header">
					<h2 id="home-search-title" class="home-search__title">Search flights and stays</h2>
					<p class="home-search__summary">Partner-powered travel search with a visible handoff before booking.</p>
				</header>

				<div class="home-search__forms">
					<form class="home-search__form" action="<?php echo esc_url( $flight_search_url ); ?>" method="get" data-baf-placement-key="flights_white_label_search">
						<div class="home-search__form-head">
							<span class="home-search__vertical">Flights</span>
							<span class="home-search__placement">Travelpayouts White Label</span>
						</div>

						<div class="home-search__grid home-search__grid--flights">
							<label class="home-search__field">
								<span>From</span>
								<input type="text" name="origin" placeholder="NYC" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="Use a 3-letter airport or city code">
							</label>
							<label class="home-search__field">
								<span>To</span>
								<input type="text" name="destination" placeholder="TYO" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="Use a 3-letter airport or city code">
							</label>
							<label class="home-search__field">
								<span>Depart</span>
								<input type="date" name="depart_date">
							</label>
							<label class="home-search__field">
								<span>Return</span>
								<input type="date" name="return_date">
							</label>
						</div>

						<input type="hidden" name="baf_surface" value="home">
						<button class="home-search__submit" type="submit">Search flights</button>
					</form>

					<form class="home-search__form" action="<?php echo esc_url( $hotel_search_url ); ?>" method="get" data-baf-placement-key="hotels_partner_search">
						<div class="home-search__form-head">
							<span class="home-search__vertical">Hotels</span>
							<span class="home-search__placement">Trip.com partner surface</span>
						</div>

						<div class="home-search__grid home-search__grid--hotels">
							<label class="home-search__field home-search__field--wide">
								<span>Destination</span>
								<input type="text" name="travel_destination" placeholder="Lisbon" autocomplete="off">
							</label>
							<label class="home-search__field">
								<span>Check in</span>
								<input type="date" name="check_in">
							</label>
							<label class="home-search__field">
								<span>Check out</span>
								<input type="date" name="check_out">
							</label>
							<label class="home-search__field">
								<span>Guests</span>
								<select name="guests">
									<option value="1">1 guest</option>
									<option value="2" selected>2 guests</option>
									<option value="3">3 guests</option>
									<option value="4">4 guests</option>
								</select>
							</label>
						</div>

						<input type="hidden" name="baf_surface" value="home">
						<button class="home-search__submit" type="submit">Search hotels</button>
					</form>
				</div>

				<p class="home-search__disclosure">Bookings and Flights may earn a commission from partner searches. Live availability, booking, payment, changes, and support are handled by Travelpayouts or the partner provider.</p>
			</section>
		</div>
	</section>

	<section class="home-entrypoints" aria-label="Travel planning entry points">
		<div class="home-entrypoints__container">
			<a id="explore" class="home-entrypoint" href="<?php echo esc_url( add_query_arg( 'travel_mode', 'explore', $flight_search_url ) ); ?>">
				<span class="home-entrypoint__label">Explore</span>
				<span class="home-entrypoint__text">Start with flexible destinations and continue into flight search.</span>
			</a>
			<a id="deals" class="home-entrypoint" href="<?php echo esc_url( add_query_arg( 'travel_focus', 'deal_dates', $flight_search_url ) ); ?>">
				<span class="home-entrypoint__label">Deals</span>
				<span class="home-entrypoint__text">Look for travel dates and routes before opening provider results.</span>
			</a>
			<a id="trip-planner" class="home-entrypoint" href="<?php echo esc_url( add_query_arg( 'travel_mode', 'planner', $flight_search_url ) ); ?>">
				<span class="home-entrypoint__label">Trip Planner</span>
				<span class="home-entrypoint__text">Shape an itinerary idea, then choose the flight or hotel path.</span>
			</a>
			<a id="saved-trips" class="home-entrypoint" href="<?php echo esc_url( add_query_arg( 'travel_mode', 'saved', $hotel_search_url ) ); ?>">
				<span class="home-entrypoint__label">Saved Trips</span>
				<span class="home-entrypoint__text">Return to a trip intent and refresh provider-owned search results.</span>
			</a>
		</div>
	</section>
</main>

<?php
get_footer();
