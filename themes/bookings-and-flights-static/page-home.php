<?php
/**
 * Template Name: Home
 *
 * @package Bookings_and_Flights_Static
 */

$flight_search_url = home_url( '/flights/' );
$hotel_search_url  = home_url( '/hotels/' );
$planner_anchor    = home_url( '/trip-planner/' );
$saved_trips_url   = home_url( '/saved-trips/' );
$hero_image_id     = absint( bookings_and_flights_field( 'hero_image_id', 0 ) );
$fallback_image    = get_template_directory_uri() . '/assets/images/home-hero-beach.jpg';
$trending_routes   = array(
	array(
		'label'       => 'Route idea',
		'title'       => 'New York to Tokyo',
		'description' => 'Start with a long-haul city pairing, then compare dates inside the Travelpayouts flight surface.',
		'meta'        => 'City break and culture',
		'origin'      => 'NYC',
		'destination' => 'TYO',
	),
	array(
		'label'       => 'Route idea',
		'title'       => 'Los Angeles to Honolulu',
		'description' => 'Open a warm-weather route search without using placeholder estimates.',
		'meta'        => 'Beach and family travel',
		'origin'      => 'LAX',
		'destination' => 'HNL',
	),
	array(
		'label'       => 'Route idea',
		'title'       => 'Miami to Lisbon',
		'description' => 'Use this as a transatlantic starting point and refine travelers, dates, and filters with the provider.',
		'meta'        => 'Europe gateway',
		'origin'      => 'MIA',
		'destination' => 'LIS',
	),
);
$explore_ideas     = array(
	array(
		'title'       => 'Food-first weekends',
		'description' => 'Pick a city known for restaurants, markets, and walkable neighborhoods before opening flight search.',
		'destination' => 'MEX',
	),
	array(
		'title'       => 'Rail-friendly Europe',
		'description' => 'Start with a flight gateway, then shape the rest of the itinerary outside the provider search module.',
		'destination' => 'LIS',
	),
	array(
		'title'       => 'Island reset',
		'description' => 'Use the provider handoff to compare travel dates after choosing a warm-weather direction.',
		'destination' => 'HNL',
	),
);
$flex_months       = array(
	array(
		'title'       => 'Shoulder-season city trip',
		'description' => 'Flexible timing can reveal better-fit options, but details must be checked in the provider search.',
		'focus'       => 'flexible_city',
	),
	array(
		'title'       => 'Long weekend window',
		'description' => 'Start broad, then narrow dates after the provider-owned results load.',
		'focus'       => 'long_weekend',
	),
	array(
		'title'       => 'School-break planning',
		'description' => 'Compare routes early without displaying unverified estimates on the homepage.',
		'focus'       => 'school_break',
	),
);
$hotel_cities      = array(
	array(
		'title'       => 'Lisbon stays',
		'description' => 'Neighborhood-first hotel discovery with the final availability handled by the partner.',
		'destination' => 'Lisbon',
	),
	array(
		'title'       => 'Tokyo stays',
		'description' => 'Use the hotel surface to compare districts, dates, and traveler counts.',
		'destination' => 'Tokyo',
	),
	array(
		'title'       => 'Mexico City stays',
		'description' => 'Start with a city idea, then continue into the approved hotel partner search.',
		'destination' => 'Mexico City',
	),
);

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

				<p class="home-search__disclosure">Bookings and Flights may earn a commission from partner searches. Availability, booking, payment, changes, and support are handled by Travelpayouts or the partner provider.</p>
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
			<a id="trip-planner" class="home-entrypoint" href="<?php echo esc_url( $planner_anchor ); ?>">
				<span class="home-entrypoint__label">Trip Planner</span>
				<span class="home-entrypoint__text">Shape an itinerary idea, then choose the flight or hotel path.</span>
			</a>
			<a id="saved-trips" class="home-entrypoint" href="<?php echo esc_url( $saved_trips_url ); ?>">
				<span class="home-entrypoint__label">Saved Trips</span>
				<span class="home-entrypoint__text">Return to a trip intent and refresh provider-owned search results.</span>
			</a>
		</div>
	</section>

	<section class="home-entrypoints" aria-labelledby="home-trust-title">
		<h2 id="home-trust-title" class="sr-only"><?php esc_html_e( 'Travel search trust and disclosure', 'bookings_and_flights' ); ?></h2>
		<div class="home-entrypoints__container">
			<div class="home-entrypoint">
				<span class="home-entrypoint__label"><?php esc_html_e( 'Affiliate disclosure', 'bookings_and_flights' ); ?></span>
				<span class="home-entrypoint__text"><?php esc_html_e( 'Bookings and Flights may earn a commission when you use sponsored Travelpayouts or partner links.', 'bookings_and_flights' ); ?></span>
			</div>
			<div class="home-entrypoint">
				<span class="home-entrypoint__label"><?php esc_html_e( 'Partner checkout', 'bookings_and_flights' ); ?></span>
				<span class="home-entrypoint__text"><?php esc_html_e( 'Availability, booking, payment, changes, and reservation support happen on the provider site.', 'bookings_and_flights' ); ?></span>
			</div>
			<a class="home-entrypoint" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<span class="home-entrypoint__label"><?php esc_html_e( 'Support', 'bookings_and_flights' ); ?></span>
				<span class="home-entrypoint__text"><?php esc_html_e( 'Contact us for site questions. For a completed booking, use the partner confirmation and support channel.', 'bookings_and_flights' ); ?></span>
			</a>
			<a class="home-entrypoint" href="<?php echo esc_url( home_url( '/#explore' ) ); ?>">
				<span class="home-entrypoint__label"><?php esc_html_e( 'Destination index', 'bookings_and_flights' ); ?></span>
				<span class="home-entrypoint__text"><?php esc_html_e( 'Browse destination and route ideas before opening the provider-owned search surface.', 'bookings_and_flights' ); ?></span>
			</a>
		</div>
	</section>

	<section class="home-discovery" aria-labelledby="home-discovery-title">
		<div class="home-discovery__container">
			<div class="home-discovery__intro">
				<p class="home-discovery__eyebrow">Travel inspiration</p>
				<h2 id="home-discovery-title" class="home-discovery__title">Browse ideas before opening partner search</h2>
				<p class="home-discovery__summary">These modules are editorial starting points. Provider pages handle current availability and reservation details.</p>
			</div>

			<div class="home-module home-module--routes" aria-labelledby="home-routes-title">
				<div class="home-module__header">
					<h3 id="home-routes-title">Trending route starters</h3>
					<p>Route ideas link into the approved flight placement without homepage estimates.</p>
				</div>
				<div class="home-card-grid home-card-grid--three">
					<?php foreach ( $trending_routes as $route ) : ?>
						<a class="home-discovery-card" href="<?php echo esc_url( add_query_arg( array( 'origin' => $route['origin'], 'destination' => $route['destination'], 'baf_surface' => 'home' ), $flight_search_url ) ); ?>">
							<span class="home-discovery-card__label"><?php echo esc_html( $route['label'] ); ?></span>
							<span class="home-discovery-card__title"><?php echo esc_html( $route['title'] ); ?></span>
							<span class="home-discovery-card__meta"><?php echo esc_html( $route['meta'] ); ?></span>
							<span class="home-discovery-card__text"><?php echo esc_html( $route['description'] ); ?></span>
							<span class="home-discovery-card__action">Open flight handoff</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="home-module home-module--split">
				<div class="home-module__panel" aria-labelledby="home-explore-title">
					<div class="home-module__header">
						<h3 id="home-explore-title">Explore-anywhere prompts</h3>
						<p>Choose a direction, then let the provider-owned flight search handle current options.</p>
					</div>
					<div class="home-card-grid">
						<?php foreach ( $explore_ideas as $idea ) : ?>
							<a class="home-compact-card" href="<?php echo esc_url( add_query_arg( array( 'destination' => $idea['destination'], 'baf_surface' => 'home' ), $flight_search_url ) ); ?>">
								<span><?php echo esc_html( $idea['title'] ); ?></span>
								<small><?php echo esc_html( $idea['description'] ); ?></small>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="home-module__panel" aria-labelledby="home-flex-title">
					<div class="home-module__header">
						<h3 id="home-flex-title">Flexible-month planning</h3>
						<p>No static estimates. Open search and compare partner results.</p>
					</div>
					<div class="home-card-grid">
						<?php foreach ( $flex_months as $month ) : ?>
							<a class="home-compact-card" href="<?php echo esc_url( add_query_arg( array( 'travel_focus' => $month['focus'], 'baf_surface' => 'home' ), $flight_search_url ) ); ?>">
								<span><?php echo esc_html( $month['title'] ); ?></span>
								<small><?php echo esc_html( $month['description'] ); ?></small>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="home-module home-module--hotels" aria-labelledby="home-hotels-title">
				<div class="home-module__header">
					<h3 id="home-hotels-title">Hotel city discovery</h3>
					<p>City prompts open the approved Trip.com or Travelpayouts Hotels partner placement.</p>
				</div>
				<div class="home-card-grid home-card-grid--three">
					<?php foreach ( $hotel_cities as $city ) : ?>
						<a class="home-discovery-card home-discovery-card--hotel" href="<?php echo esc_url( add_query_arg( array( 'travel_destination' => $city['destination'], 'guests' => 2, 'baf_surface' => 'home' ), $hotel_search_url ) ); ?>">
							<span class="home-discovery-card__label">Hotel idea</span>
							<span class="home-discovery-card__title"><?php echo esc_html( $city['title'] ); ?></span>
							<span class="home-discovery-card__text"><?php echo esc_html( $city['description'] ); ?></span>
							<span class="home-discovery-card__action">Open hotel handoff</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<p class="home-discovery__disclosure">Sponsored searches may earn a commission. Bookings and Flights keeps the inspiration and page shell; availability, booking, payment, changes, and support stay with the provider.</p>

			<div class="home-retention" aria-labelledby="home-retention-title">
				<div class="home-module__header">
					<h3 id="home-retention-title">Keep planning without pretending every workflow is active</h3>
					<p>Alert workflows stay staged while the AI planner turns local trip intent into editable briefs.</p>
				</div>

				<div class="home-retention__grid">
					<a id="price-alerts" class="home-retention-card" href="<?php echo esc_url( add_query_arg( array( 'travel_focus' => 'price_alert', 'baf_surface' => 'home' ), $flight_search_url ) ); ?>">
						<span class="home-retention-card__label">Price alert preview</span>
						<span class="home-retention-card__title">Watch a route later</span>
						<span class="home-retention-card__text">Alerts are not active yet. Start with provider-owned flight search now, then return when saved alert capture is enabled.</span>
						<span class="home-retention-card__action">Open flight handoff</span>
					</a>

					<a id="ai-planner-entry" class="home-retention-card" href="<?php echo esc_url( $planner_anchor ); ?>">
						<span class="home-retention-card__label">AI trip planner</span>
						<span class="home-retention-card__title">Bring a trip idea</span>
						<span class="home-retention-card__text">Demo mode prepares a structured trip brief locally; live providers require explicit external AI consent before prompts leave WordPress.</span>
						<span class="home-retention-card__prompt">Example: four days in Lisbon with hotels near transit</span>
						<span class="home-retention-card__action">Open AI planner</span>
					</a>
				</div>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
