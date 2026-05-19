<?php
/**
 * Template Name: Home
 *
 * @package Bookings_and_Flights_Static
 */

$flight_search_url          = home_url( '/flights/' );
$hotel_search_url           = home_url( '/hotels/' );
$flight_provider_search_url = home_url( '/flights/#flights-provider-search' );
$hotel_provider_search_url  = home_url( '/hotels/#hotels-provider-search' );
$flight_provider_link       = static function ( array $args ) use ( $flight_search_url ): string {
	return add_query_arg( $args, $flight_search_url ) . '#flights-provider-search';
};
$hotel_provider_link        = static function ( array $args ) use ( $hotel_search_url ): string {
	return add_query_arg( $args, $hotel_search_url ) . '#hotels-provider-search';
};
$planner_anchor             = home_url( '/trip-planner/' );
$saved_trips_url            = home_url( '/saved-trips/' );
$hero_image_id              = absint( bookings_and_flights_field( 'hero_image_id', 0 ) );
$fallback_image             = get_template_directory_uri() . '/assets/images/home-hero-beach.jpg';
	$route_starters             = array(
		array(
				'label'       => 'Big-city escape',
				'title'       => 'New York to Tokyo',
				'description' => 'Compare dates, stops, and neighborhoods before you open live fares.',
			'meta'        => 'NYC to TYO',
			'origin'      => 'NYC',
			'destination' => 'TYO',
			'image'       => 'https://pd.w.org/2025/09/38768b5b57492a850.81875712-1536x864.jpeg',
		),
		array(
				'label'       => 'Warm-water reset',
				'title'       => 'Los Angeles to Honolulu',
				'description' => 'Keep the island plan visible while you check real routes and dates.',
			'meta'        => 'LAX to HNL',
			'origin'      => 'LAX',
			'destination' => 'HNL',
			'image'       => $fallback_image,
		),
		array(
				'label'       => 'Atlantic gateway',
				'title'       => 'Miami to Lisbon',
				'description' => 'Start with a Europe route and compare cabins, stops, and timing.',
			'meta'        => 'MIA to LIS',
			'origin'      => 'MIA',
			'destination' => 'LIS',
			'image'       => 'https://pd.w.org/2024/02/10765c3d08bccce08.12231948-1536x1021.jpg',
			),
		);
		$quick_routes               = array(
			array(
				'label'       => 'NYC to TYO',
				'origin'      => 'NYC',
				'destination' => 'TYO',
			),
			array(
				'label'       => 'LAX to HNL',
				'origin'      => 'LAX',
				'destination' => 'HNL',
			),
			array(
				'label'       => 'MIA to LIS',
				'origin'      => 'MIA',
				'destination' => 'LIS',
			),
		);
		$trip_intents               = array(
		array(
				'title'       => 'Food weekends',
				'description' => 'Short flights, central stays, markets, and late dinners without overplanning.',
			'destination' => 'MEX',
			'image'       => 'https://pd.w.org/2024/02/33265c4d9b2d61cf2.86919103-1152x1536.jpeg',
		),
		array(
				'title'       => 'Europe by rail',
				'description' => 'Fly into one gateway, then build the hotel plan around the train legs.',
			'destination' => 'LIS',
			'image'       => 'https://pd.w.org/2024/02/10765c3d08bccce08.12231948-768x510.jpg',
		),
		array(
				'title'       => 'Island reset',
				'description' => 'Start with slower days, easy stays, and live search when dates are ready.',
			'destination' => 'HNL',
			'image'       => 'https://pd.w.org/2022/10/970633c6312c0dc51.25581912-1536x1024.jpg',
		),
	);
	$hotel_cities               = array(
		array(
				'title'       => 'Lisbon neighborhoods',
				'description' => 'Find transit-friendly areas before checking live rooms.',
			'destination' => 'Lisbon',
			'image'       => 'https://pd.w.org/2025/02/61567a139e9698916.33883534-1536x1024.jpg',
		),
		array(
				'title'       => 'Tokyo hotel zones',
				'description' => 'Compare station access and room style before opening rates.',
			'destination' => 'Tokyo',
			'image'       => 'https://pd.w.org/2022/04/603624d41c6bb55d3.38213642-1536x1152.jpeg',
		),
		array(
				'title'       => 'Mexico City stays',
				'description' => 'Start with walkable districts, then check live hotel options.',
			'destination' => 'Mexico City',
			'image'       => 'https://pd.w.org/2026/04/11769e1153db3edf8.41610904-1107x1536.jpg',
		),
	);
	$visual_panels              = array(
		array(
				'label' => 'Beach days',
				'title' => 'Warm routes and easy stays',
			'image' => $fallback_image,
		),
		array(
				'label' => 'City energy',
				'title' => 'Neighborhoods before rates',
			'image' => 'https://pd.w.org/2026/04/11769e1153db3edf8.41610904-1107x1536.jpg',
		),
		array(
				'label' => 'Multi-stop travel',
				'title' => 'Fly in, keep moving',
			'image' => 'https://pd.w.org/2024/02/10765c3d08bccce08.12231948-1536x1021.jpg',
		),
	);
$planning_steps             = array(
	array(
			'title'       => 'Start with the trip shape',
			'description' => 'Pick the route, stay zone, and kind of days you want.',
	),
	array(
			'title'       => 'Search when it feels real',
			'description' => 'Open live flight or hotel options once the idea has direction.',
	),
	array(
			'title'       => 'Book on the travel site',
			'description' => 'Final prices, checkout, changes, and support stay there.',
	),
);
$home_partner_widgets       = array(
	array(
		'placement'   => 'flights_white_label_search',
		'channel'     => 'home_partner_flights',
		'slug'        => 'home_flight_widget',
		'class'       => 'search-placement--home-widget search-placement--home-flight-widget',
			'eyebrow'     => __( 'When the route is ready', 'bookings_and_flights' ),
			'title'       => __( 'Search flights from the trip you picked', 'bookings_and_flights' ),
			'description' => __( 'Adjust dates, travelers, and routes here when you are ready to compare live fares.', 'bookings_and_flights' ),
		'origin'      => 'NYC',
		'destination' => 'LAX',
		'details'     => array(
			array(
					'label' => __( 'Use when', 'bookings_and_flights' ),
					'value' => __( 'Route and dates are close', 'bookings_and_flights' ),
			),
			array(
					'label' => __( 'Next step', 'bookings_and_flights' ),
					'value' => __( 'Review live fares', 'bookings_and_flights' ),
			),
		),
	),
	array(
		'placement'   => 'hotels_partner_search',
		'channel'     => 'home_partner_hotels',
		'slug'        => 'home_hotel_widget',
		'class'       => 'search-placement--home-widget search-placement--home-hotel-widget',
			'eyebrow'     => __( 'When the stay area feels right', 'bookings_and_flights' ),
			'title'       => __( 'Check hotels around the place you want to be', 'bookings_and_flights' ),
			'description' => __( 'Use this after you have a city, neighborhood, or stay style in mind.', 'bookings_and_flights' ),
		'details'     => array(
			array(
					'label' => __( 'Use when', 'bookings_and_flights' ),
					'value' => __( 'City or area is chosen', 'bookings_and_flights' ),
			),
			array(
					'label' => __( 'Next step', 'bookings_and_flights' ),
					'value' => __( 'Compare live rooms', 'bookings_and_flights' ),
			),
		),
	),
	array(
		'placement'   => 'flights_popular_routes',
		'channel'     => 'home_partner_routes',
		'slug'        => 'home_popular_routes',
		'class'       => 'search-placement--home-widget search-placement--home-popular-routes',
			'eyebrow'     => __( 'Still choosing?', 'bookings_and_flights' ),
			'title'       => __( 'Browse routes before you commit to a search', 'bookings_and_flights' ),
			'description' => __( 'Use popular paths when you want a starting point but do not have the exact trip yet.', 'bookings_and_flights' ),
		'destination' => 'TYO',
		'details'     => array(
			array(
					'label' => __( 'Use when', 'bookings_and_flights' ),
					'value' => __( 'Destination is still open', 'bookings_and_flights' ),
			),
			array(
					'label' => __( 'Next step', 'bookings_and_flights' ),
					'value' => __( 'Pick a route to test', 'bookings_and_flights' ),
			),
		),
	),
);
$home_monetization_cards    = array(
	array(
			'label'       => 'Airport ride',
			'title'       => 'Get from airport to hotel',
			'description' => 'Add this after your arrival time starts to make sense.',
		'url'         => 'https://kiwitaxi.tpx.gr/GyRl1qn9',
			'cta'         => 'Check transfers',
	),
	array(
			'label'       => 'Road days',
			'title'       => 'See if a car fits the plan',
			'description' => 'Useful when the trip goes beyond one walkable city.',
		'url'         => 'https://economybookings.tpx.gr/1GrOPhId',
			'cta'         => 'Check cars',
	),
	array(
		'label'       => 'Things to do',
			'title'       => 'Give the trip a reason',
			'description' => 'Find concerts, games, and city moments around your dates.',
		'url'         => 'https://ticketnetwork.tpx.gr/ZGWrIqmd',
			'cta'         => 'Check events',
	),
	array(
			'label'       => 'Travel cover',
			'title'       => 'Protect the expensive parts',
			'description' => 'Compare coverage once flights and stays are taking shape.',
		'url'         => 'https://ektatraveling.tpx.gr/Y56l4XET',
			'cta'         => 'Check coverage',
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
				<div class="home-hero__copy" data-reveal>
						<p class="home-kicker"><?php esc_html_e( 'Search when the trip feels right', 'bookings_and_flights' ); ?></p>
						<h1 id="home-hero-title" class="home-hero__title"><?php esc_html_e( 'Find the trip you actually want to take.', 'bookings_and_flights' ); ?></h1>
						<p class="home-hero__lede"><?php esc_html_e( 'Start with a route, a stay area, or a travel idea. When it feels worth checking, open live flight and hotel options without losing the plan.', 'bookings_and_flights' ); ?></p>
					<div class="home-hero__actions" aria-label="<?php esc_attr_e( 'Primary travel search actions', 'bookings_and_flights' ); ?>">
						<a class="home-button home-button--primary" href="<?php echo esc_url( $flight_search_url ); ?>"><?php esc_html_e( 'Search flights', 'bookings_and_flights' ); ?></a>
						<a class="home-button home-button--secondary" href="<?php echo esc_url( $hotel_search_url ); ?>"><?php esc_html_e( 'Search hotels', 'bookings_and_flights' ); ?></a>
					</div>
				</div>

				<section class="home-search" aria-labelledby="home-search-title" data-home-search-tabs data-reveal>
					<div class="home-search__mast">
						<div>
								<p class="home-kicker home-kicker--dark"><?php esc_html_e( 'Start with the basics', 'bookings_and_flights' ); ?></p>
								<h2 id="home-search-title" class="home-search__title"><?php esc_html_e( 'Where are you thinking of going?', 'bookings_and_flights' ); ?></h2>
						</div>
							<p class="home-search__summary"><?php esc_html_e( 'Use the quick search if you already have a route or city in mind. You can keep browsing below if you are still deciding.', 'bookings_and_flights' ); ?></p>
					</div>

				<div class="home-search__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Travel search type', 'bookings_and_flights' ); ?>">
					<button class="home-search__tab is-active" type="button" id="home-search-tab-flights" role="tab" aria-selected="true" aria-controls="home-search-panel-flights" data-home-search-tab="flights">
						<span class="home-search__tab-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false"><path d="M3.5 12.5 21 5.5l-5.8 15-4-6.6-7.7-1.4Z"/><path d="m11.2 13.9 9.4-8"/></svg>
						</span>
						<span><?php esc_html_e( 'Flights', 'bookings_and_flights' ); ?></span>
					</button>
					<button class="home-search__tab" type="button" id="home-search-tab-hotels" role="tab" aria-selected="false" aria-controls="home-search-panel-hotels" data-home-search-tab="hotels">
						<span class="home-search__tab-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false"><path d="M4 20V8.5A2.5 2.5 0 0 1 6.5 6H17a3 3 0 0 1 3 3v11"/><path d="M4 13h16"/><path d="M7 13V9.5h4V13"/><path d="M13 13V9.5h4V13"/><path d="M3 20h18"/></svg>
						</span>
						<span><?php esc_html_e( 'Hotels', 'bookings_and_flights' ); ?></span>
					</button>
				</div>

				<div class="home-search__panels">
					<form class="home-search__form is-active" action="<?php echo esc_url( $flight_provider_search_url ); ?>" method="get" data-baf-placement-key="flights_white_label_search" data-home-search-panel="flights" id="home-search-panel-flights" role="tabpanel" aria-labelledby="home-search-tab-flights">
							<div class="home-search__form-head">
									<span class="home-search__vertical"><?php esc_html_e( 'Live travel search', 'bookings_and_flights' ); ?></span>
									<span class="home-search__placement"><?php esc_html_e( 'Fares open on the flight site', 'bookings_and_flights' ); ?></span>
							</div>

						<div class="home-search__grid home-search__grid--flights">
							<label class="home-search__field">
								<span><?php esc_html_e( 'From', 'bookings_and_flights' ); ?></span>
								<input type="text" name="origin" placeholder="NYC" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="<?php esc_attr_e( 'Use a 3-letter airport or city code', 'bookings_and_flights' ); ?>">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'To', 'bookings_and_flights' ); ?></span>
								<input type="text" name="destination" placeholder="TYO" autocomplete="off" autocapitalize="characters" maxlength="3" pattern="[A-Za-z]{3}" title="<?php esc_attr_e( 'Use a 3-letter airport or city code', 'bookings_and_flights' ); ?>">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Depart', 'bookings_and_flights' ); ?></span>
								<input type="date" name="depart_date">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Return', 'bookings_and_flights' ); ?></span>
								<input type="date" name="return_date">
							</label>
							<details class="home-search__passenger-picker" data-baf-passenger-picker>
								<summary>
									<span><?php esc_html_e( 'Travelers and cabin', 'bookings_and_flights' ); ?></span>
									<strong data-baf-passenger-summary><?php esc_html_e( '1 adult, Economy', 'bookings_and_flights' ); ?></strong>
								</summary>
								<div class="home-search__passenger-panel">
									<label class="home-search__field">
										<span><?php esc_html_e( 'Adults', 'bookings_and_flights' ); ?></span>
										<input type="number" name="adults" value="1" min="1" max="9" inputmode="numeric">
									</label>
									<label class="home-search__field">
										<span><?php esc_html_e( 'Children', 'bookings_and_flights' ); ?></span>
										<input type="number" name="children" value="0" min="0" max="8" inputmode="numeric">
									</label>
									<label class="home-search__field">
										<span><?php esc_html_e( 'Infants', 'bookings_and_flights' ); ?></span>
										<input type="number" name="infants" value="0" min="0" max="1" inputmode="numeric">
									</label>
									<label class="home-search__field home-search__field--cabin">
										<span><?php esc_html_e( 'Cabin', 'bookings_and_flights' ); ?></span>
										<select name="cabin">
											<option value="economy" selected><?php esc_html_e( 'Economy', 'bookings_and_flights' ); ?></option>
											<option value="premium_economy"><?php esc_html_e( 'Premium economy', 'bookings_and_flights' ); ?></option>
											<option value="business"><?php esc_html_e( 'Business', 'bookings_and_flights' ); ?></option>
											<option value="first"><?php esc_html_e( 'First', 'bookings_and_flights' ); ?></option>
										</select>
									</label>
								</div>
							</details>
						</div>

						<input type="hidden" name="flightSearch" value="" data-baf-flight-search>
						<input type="hidden" name="travelers" value="1" data-baf-passenger-total>
						<input type="hidden" name="baf_surface" value="home">
						<button class="home-search__submit" type="submit"><?php esc_html_e( 'Search flights', 'bookings_and_flights' ); ?></button>
					</form>

					<form class="home-search__form" action="<?php echo esc_url( $hotel_provider_search_url ); ?>" method="get" data-baf-placement-key="hotels_partner_search" data-home-search-panel="hotels" id="home-search-panel-hotels" role="tabpanel" aria-labelledby="home-search-tab-hotels">
							<div class="home-search__form-head">
									<span class="home-search__vertical"><?php esc_html_e( 'Live travel search', 'bookings_and_flights' ); ?></span>
									<span class="home-search__placement"><?php esc_html_e( 'Rates open on the hotel site', 'bookings_and_flights' ); ?></span>
							</div>

						<div class="home-search__grid home-search__grid--hotels">
							<label class="home-search__field home-search__field--wide">
								<span><?php esc_html_e( 'Destination', 'bookings_and_flights' ); ?></span>
								<input type="text" name="travel_destination" placeholder="Lisbon" autocomplete="off">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Check in', 'bookings_and_flights' ); ?></span>
								<input type="date" name="check_in">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Check out', 'bookings_and_flights' ); ?></span>
								<input type="date" name="check_out">
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Guests', 'bookings_and_flights' ); ?></span>
								<select name="guests">
									<option value="1"><?php esc_html_e( '1 guest', 'bookings_and_flights' ); ?></option>
									<option value="2" selected><?php esc_html_e( '2 guests', 'bookings_and_flights' ); ?></option>
									<option value="3"><?php esc_html_e( '3 guests', 'bookings_and_flights' ); ?></option>
									<option value="4"><?php esc_html_e( '4 guests', 'bookings_and_flights' ); ?></option>
								</select>
							</label>
							<label class="home-search__field">
								<span><?php esc_html_e( 'Rooms', 'bookings_and_flights' ); ?></span>
								<select name="rooms">
									<option value="1" selected><?php esc_html_e( '1 room', 'bookings_and_flights' ); ?></option>
									<option value="2"><?php esc_html_e( '2 rooms', 'bookings_and_flights' ); ?></option>
									<option value="3"><?php esc_html_e( '3 rooms', 'bookings_and_flights' ); ?></option>
								</select>
							</label>
						</div>

						<input type="hidden" name="baf_surface" value="home">
						<button class="home-search__submit" type="submit"><?php esc_html_e( 'Search hotels', 'bookings_and_flights' ); ?></button>
					</form>
					</div>

					<div class="home-search__quick" aria-label="<?php esc_attr_e( 'Popular flight starters', 'bookings_and_flights' ); ?>">
						<span><?php esc_html_e( 'Popular starts', 'bookings_and_flights' ); ?></span>
						<?php foreach ( $quick_routes as $route ) : ?>
							<a href="<?php echo esc_url( $flight_provider_link( array( 'origin' => $route['origin'], 'destination' => $route['destination'], 'baf_surface' => 'home' ) ) ); ?>"><?php echo esc_html( $route['label'] ); ?></a>
						<?php endforeach; ?>
					</div>

					<p class="home-search__disclosure"><?php esc_html_e( 'Live results open on the booking site, where final prices, payment, changes, and support are handled.', 'bookings_and_flights' ); ?></p>
				</section>
			</div>
		</section>

		<section class="home-rail" aria-label="<?php esc_attr_e( 'Travel planning entry points', 'bookings_and_flights' ); ?>">
			<div class="home-rail__container">
				<a class="home-rail__item" id="explore" href="<?php echo esc_url( $flight_provider_link( array( 'travel_mode' => 'explore', 'baf_surface' => 'home' ) ) ); ?>">
						<span><?php esc_html_e( 'Need ideas?', 'bookings_and_flights' ); ?></span>
						<small><?php esc_html_e( 'Browse trip starters', 'bookings_and_flights' ); ?></small>
			</a>
			<a class="home-rail__item" id="deals" href="<?php echo esc_url( $flight_provider_link( array( 'travel_focus' => 'deal_dates', 'baf_surface' => 'home' ) ) ); ?>">
					<span><?php esc_html_e( 'Know the timing?', 'bookings_and_flights' ); ?></span>
					<small><?php esc_html_e( 'Start with dates', 'bookings_and_flights' ); ?></small>
			</a>
			<a class="home-rail__item" id="trip-planner" href="<?php echo esc_url( $planner_anchor ); ?>">
					<span><?php esc_html_e( 'Need a plan?', 'bookings_and_flights' ); ?></span>
					<small><?php esc_html_e( 'Shape the itinerary', 'bookings_and_flights' ); ?></small>
			</a>
			<a class="home-rail__item" id="saved-trips" href="<?php echo esc_url( $saved_trips_url ); ?>">
					<span><?php esc_html_e( 'Coming back?', 'bookings_and_flights' ); ?></span>
					<small><?php esc_html_e( 'Return to saved plans', 'bookings_and_flights' ); ?></small>
				</a>
			</div>
		</section>

		<section class="home-partner-tools" id="active-travelpayouts-tools" aria-labelledby="home-partner-tools-title">
			<div class="home-partner-tools__container">
				<div class="home-partner-tools__intro" data-reveal>
						<p class="home-kicker home-kicker--dark"><?php esc_html_e( 'Ready to compare?', 'bookings_and_flights' ); ?></p>
						<h2 id="home-partner-tools-title"><?php esc_html_e( 'Turn a travel idea into live options.', 'bookings_and_flights' ); ?></h2>
						<p><?php esc_html_e( 'These searches belong here once you have a route, a city, or a stay area in mind. Compare live options, then finish booking on the travel site that shows the result.', 'bookings_and_flights' ); ?></p>
				</div>

				<div class="home-partner-tools__grid">
					<?php foreach ( $home_partner_widgets as $widget ) : ?>
						<?php
						get_template_part(
							'template-parts/travel-search-placement',
							null,
							array(
								'placement'        => $widget['placement'],
								'surface'          => 'home',
								'channel'          => $widget['channel'],
								'slug'             => $widget['slug'],
								'class'            => $widget['class'],
								'eyebrow'          => $widget['eyebrow'],
								'title'            => $widget['title'],
								'description'      => $widget['description'],
								'origin'           => $widget['origin'] ?? '',
								'destination'      => $widget['destination'] ?? '',
								'details'          => $widget['details'],
									'fallback_message' => __( 'This search is being configured. You can still use the flight or hotel pages from the main navigation.', 'bookings_and_flights' ),
									'support_note'     => __( 'Sponsored search may earn a commission. Live inventory, prices, payment, changes, and support stay with the booking site.', 'bookings_and_flights' ),
							)
						);
						?>
					<?php endforeach; ?>
				</div>

				<div class="home-program-lanes" aria-label="<?php esc_attr_e( 'Travel add-ons for later in the plan', 'bookings_and_flights' ); ?>">
					<?php foreach ( $home_monetization_cards as $card ) : ?>
						<a class="home-program-card" href="<?php echo esc_url( $card['url'] ); ?>" target="_blank" rel="nofollow sponsored noopener noreferrer">
							<span class="home-program-card__label"><?php echo esc_html( $card['label'] ); ?></span>
							<span class="home-program-card__title"><?php echo esc_html( $card['title'] ); ?></span>
							<span class="home-program-card__text"><?php echo esc_html( $card['description'] ); ?></span>
							<span class="home-program-card__action"><?php echo esc_html( $card['cta'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>

					<p class="home-partner-tools__disclosure"><?php esc_html_e( 'The planning stays here. Live prices, checkout, changes, and support happen on the travel site you choose after search.', 'bookings_and_flights' ); ?></p>
			</div>
		</section>

		<section class="home-visual-story" aria-labelledby="home-visual-story-title">
			<div class="home-visual-story__container">
				<div class="home-visual-story__copy" data-reveal>
						<p class="home-kicker home-kicker--dark"><?php esc_html_e( 'Still deciding?', 'bookings_and_flights' ); ?></p>
							<h2 id="home-visual-story-title"><?php esc_html_e( 'Start with the kind of trip you want.', 'bookings_and_flights' ); ?></h2>
							<p><?php esc_html_e( 'Pick a feeling first. Search makes more sense once the destination, dates, or travel style has a shape.', 'bookings_and_flights' ); ?></p>
				</div>

				<div class="home-visual-story__mosaic" aria-hidden="true" data-reveal>
					<?php foreach ( $visual_panels as $panel ) : ?>
						<figure class="home-visual-story__tile">
							<img src="<?php echo esc_url( $panel['image'] ); ?>" alt="" loading="lazy" decoding="async">
							<figcaption>
								<span><?php echo esc_html( $panel['label'] ); ?></span>
								<strong><?php echo esc_html( $panel['title'] ); ?></strong>
							</figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<section class="home-discovery" aria-labelledby="home-discovery-title">
			<div class="home-discovery__container">
				<div class="home-discovery__intro" data-reveal>
						<p class="home-kicker home-kicker--dark"><?php esc_html_e( 'Browse with a reason', 'bookings_and_flights' ); ?></p>
							<h2 id="home-discovery-title" class="home-discovery__title"><?php esc_html_e( 'Find a trip you can picture.', 'bookings_and_flights' ); ?></h2>
						<p class="home-discovery__summary"><?php esc_html_e( 'Start with one clear idea, then open live options only when it feels worth checking.', 'bookings_and_flights' ); ?></p>
			</div>

			<div class="home-module home-module--routes" aria-labelledby="home-routes-title" data-reveal>
				<div class="home-module__header">
						<h3 id="home-routes-title"><?php esc_html_e( 'Flight ideas with a next step', 'bookings_and_flights' ); ?></h3>
						<p><?php esc_html_e( 'Choose a route, then adjust dates and travelers in live search.', 'bookings_and_flights' ); ?></p>
				</div>
					<div class="home-card-grid home-card-grid--featured">
						<?php foreach ( $route_starters as $route ) : ?>
							<a class="home-discovery-card" href="<?php echo esc_url( $flight_provider_link( array( 'origin' => $route['origin'], 'destination' => $route['destination'], 'baf_surface' => 'home' ) ) ); ?>">
								<span class="home-discovery-card__media" aria-hidden="true">
									<img src="<?php echo esc_url( $route['image'] ); ?>" alt="" loading="lazy" decoding="async">
								</span>
								<span class="home-discovery-card__body">
									<span class="home-discovery-card__label"><?php echo esc_html( $route['label'] ); ?></span>
									<span class="home-discovery-card__title"><?php echo esc_html( $route['title'] ); ?></span>
									<span class="home-discovery-card__meta"><?php echo esc_html( $route['meta'] ); ?></span>
									<span class="home-discovery-card__text"><?php echo esc_html( $route['description'] ); ?></span>
										<span class="home-discovery-card__action"><?php esc_html_e( 'Check flights', 'bookings_and_flights' ); ?></span>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

			<div class="home-module home-module--split" data-reveal>
				<div class="home-module__panel" aria-labelledby="home-intents-title">
					<div class="home-module__header">
							<h3 id="home-intents-title"><?php esc_html_e( 'What kind of trip is this?', 'bookings_and_flights' ); ?></h3>
							<p><?php esc_html_e( 'Use these when you know the mood more than the exact place.', 'bookings_and_flights' ); ?></p>
					</div>
						<div class="home-stack-list">
							<?php foreach ( $trip_intents as $idea ) : ?>
								<a class="home-compact-card" href="<?php echo esc_url( $flight_provider_link( array( 'destination' => $idea['destination'], 'baf_surface' => 'home' ) ) ); ?>">
									<span class="home-compact-card__media" aria-hidden="true">
										<img src="<?php echo esc_url( $idea['image'] ); ?>" alt="" loading="lazy" decoding="async">
									</span>
									<span class="home-compact-card__title"><?php echo esc_html( $idea['title'] ); ?></span>
									<small><?php echo esc_html( $idea['description'] ); ?></small>
								</a>
							<?php endforeach; ?>
						</div>
				</div>

				<div class="home-module__panel home-module__panel--dark" aria-labelledby="home-steps-title">
					<div class="home-module__header">
							<h3 id="home-steps-title"><?php esc_html_e( 'How this page is meant to work', 'bookings_and_flights' ); ?></h3>
							<p><?php esc_html_e( 'Browse first when you are unsure. Search live when a route or stay area feels worth checking.', 'bookings_and_flights' ); ?></p>
					</div>
					<ol class="home-step-list">
						<?php foreach ( $planning_steps as $step ) : ?>
							<li>
								<strong><?php echo esc_html( $step['title'] ); ?></strong>
								<span><?php echo esc_html( $step['description'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>

			<div class="home-module home-module--hotels" aria-labelledby="home-hotels-title" data-reveal>
				<div class="home-module__header">
						<h3 id="home-hotels-title"><?php esc_html_e( 'Stay ideas that make search easier', 'bookings_and_flights' ); ?></h3>
						<p><?php esc_html_e( 'Pick a neighborhood style first. Then compare live rooms with less guesswork.', 'bookings_and_flights' ); ?></p>
				</div>
					<div class="home-card-strip">
						<?php foreach ( $hotel_cities as $city ) : ?>
							<a class="home-discovery-card home-discovery-card--hotel" href="<?php echo esc_url( $hotel_provider_link( array( 'travel_destination' => $city['destination'], 'guests' => 2, 'baf_surface' => 'home' ) ) ); ?>">
								<span class="home-discovery-card__media" aria-hidden="true">
									<img src="<?php echo esc_url( $city['image'] ); ?>" alt="" loading="lazy" decoding="async">
								</span>
								<span class="home-discovery-card__body">
									<span class="home-discovery-card__label"><?php esc_html_e( 'Stay idea', 'bookings_and_flights' ); ?></span>
									<span class="home-discovery-card__title"><?php echo esc_html( $city['title'] ); ?></span>
									<span class="home-discovery-card__text"><?php echo esc_html( $city['description'] ); ?></span>
									<span class="home-discovery-card__action"><?php esc_html_e( 'Check hotels', 'bookings_and_flights' ); ?></span>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>

			<?php get_template_part( 'template-parts/home-retention', null, array( 'price_alert_url' => $flight_provider_link( array( 'travel_focus' => 'price_alert', 'baf_surface' => 'home' ) ), 'planner_url' => $planner_anchor ) ); ?>

			<p class="home-discovery__disclosure"><?php esc_html_e( 'Some searches may earn a commission. Bookings and Flights helps with planning and discovery; booking sites handle live prices, payment, changes, and support.', 'bookings_and_flights' ); ?></p>
		</div>
	</section>
</main>

<?php
get_footer();
