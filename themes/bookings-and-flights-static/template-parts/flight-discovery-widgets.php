<?php
/**
 * Flight discovery widget placements.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'surface'     => 'flights',
		'channel'     => 'search_page',
		'slug'        => 'flight_discovery',
		'origin'      => '',
		'destination' => '',
		'details'     => array(),
	)
);

$normalize_iata = static function ( string $value ): string {
	$value = strtoupper( trim( $value ) );

	return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
};

$surface     = sanitize_key( (string) $args['surface'] );
$channel     = sanitize_key( (string) $args['channel'] );
$slug        = sanitize_key( (string) $args['slug'] );
$origin      = $normalize_iata( (string) $args['origin'] );
$destination = $normalize_iata( (string) $args['destination'] );
$details     = is_array( $args['details'] ) ? $args['details'] : array();

$render_missing = static function ( string $title, string $message ): void {
	?>
	<section class="flight-discovery__missing" role="status">
		<h3><?php echo esc_html( $title ); ?></h3>
		<p><?php echo esc_html( $message ); ?></p>
	</section>
	<?php
};
?>

<section class="flight-discovery" aria-labelledby="flight-discovery-title">
	<div class="flight-discovery__header">
		<p class="flight-discovery__eyebrow"><?php esc_html_e( 'Flight discovery widgets', 'bookings_and_flights' ); ?></p>
		<h2 id="flight-discovery-title" class="flight-discovery__title"><?php esc_html_e( 'Explore provider-owned route signals', 'bookings_and_flights' ); ?></h2>
		<p class="flight-discovery__copy"><?php esc_html_e( 'These Travelpayouts widgets add calendar, popular-route, and map context while bookings, prices, support, and tracking remain provider-controlled.', 'bookings_and_flights' ); ?></p>
	</div>

	<div class="flight-discovery__grid">
		<?php if ( '' !== $origin && '' !== $destination ) : ?>
			<?php
			get_template_part(
				'template-parts/travel-search-placement',
				null,
				array(
					'placement'        => 'flights_low_price_calendar',
					'surface'          => $surface,
					'channel'          => $channel,
					'slug'             => $slug . '_calendar',
					'class'            => 'search-placement--discovery',
					'eyebrow'          => __( 'Travelpayouts calendar', 'bookings_and_flights' ),
					'title'            => __( 'Flexible-date calendar', 'bookings_and_flights' ),
					'description'      => __( 'Review the provider calendar for this route, then confirm exact fare conditions inside Travelpayouts.', 'bookings_and_flights' ),
					'origin'           => $origin,
					'destination'      => $destination,
					'details'          => $details,
					'fallback_message' => __( 'The low-price calendar placement is not configured yet.', 'bookings_and_flights' ),
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'Flexible-date calendar', 'bookings_and_flights' ), __( 'Add a 3-letter origin and destination to show this Travelpayouts calendar.', 'bookings_and_flights' ) ); ?>
		<?php endif; ?>

		<?php if ( '' !== $destination ) : ?>
			<?php
			get_template_part(
				'template-parts/travel-search-placement',
				null,
				array(
					'placement'        => 'flights_popular_routes',
					'surface'          => $surface,
					'channel'          => $channel,
					'slug'             => $slug . '_popular_routes',
					'class'            => 'search-placement--discovery',
					'eyebrow'          => __( 'Travelpayouts routes', 'bookings_and_flights' ),
					'title'            => __( 'Popular routes to this destination', 'bookings_and_flights' ),
					'description'      => __( 'Use Travelpayouts route discovery for destination ideas without storing provider fare inventory in WordPress.', 'bookings_and_flights' ),
					'destination'      => $destination,
					'details'          => $details,
					'fallback_message' => __( 'The popular routes placement is not configured yet.', 'bookings_and_flights' ),
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'Popular routes', 'bookings_and_flights' ), __( 'Add a 3-letter destination to show popular Travelpayouts routes.', 'bookings_and_flights' ) ); ?>
		<?php endif; ?>

		<?php if ( '' !== $origin ) : ?>
			<?php
			get_template_part(
				'template-parts/travel-search-placement',
				null,
				array(
					'placement'        => 'flights_route_map',
					'surface'          => $surface,
					'channel'          => $channel,
					'slug'             => $slug . '_route_map',
					'class'            => 'search-placement--discovery',
					'eyebrow'          => __( 'Travelpayouts map', 'bookings_and_flights' ),
					'title'            => __( 'Route map from this origin', 'bookings_and_flights' ),
					'description'      => __( 'See provider-owned route map context before continuing to the main Travelpayouts handoff.', 'bookings_and_flights' ),
					'origin'           => $origin,
					'details'          => $details,
					'fallback_message' => __( 'The route map placement is not configured yet.', 'bookings_and_flights' ),
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'Route map', 'bookings_and_flights' ), __( 'Add a 3-letter origin to show the Travelpayouts route map.', 'bookings_and_flights' ) ); ?>
		<?php endif; ?>
	</div>
</section>
