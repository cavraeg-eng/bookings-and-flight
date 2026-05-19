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
$has_route_context = '' !== $origin || '' !== $destination;
$section_class     = 'flight-discovery ' . ( $has_route_context ? 'flight-discovery--ready' : 'flight-discovery--empty' );

$render_missing = static function ( string $title, string $message ): void {
	?>
	<section class="flight-discovery__missing" role="status">
		<h3><?php echo esc_html( $title ); ?></h3>
		<p><?php echo esc_html( $message ); ?></p>
	</section>
	<?php
};
?>

<section class="<?php echo esc_attr( $section_class ); ?>" aria-labelledby="flight-discovery-title">
	<div class="flight-discovery__header">
		<p class="flight-discovery__eyebrow"><?php esc_html_e( 'Explore more options', 'bookings_and_flights' ); ?></p>
		<h2 id="flight-discovery-title" class="flight-discovery__title"><?php echo esc_html( $has_route_context ? __( 'Still deciding where or when to fly?', 'bookings_and_flights' ) : __( 'Search first, then compare the extras', 'bookings_and_flights' ) ); ?></h2>
		<p class="flight-discovery__copy"><?php echo esc_html( $has_route_context ? __( 'Use these quick views when your trip is flexible. Check cheaper-looking dates, scan common routes, or open a map view before you choose a fare.', 'bookings_and_flights' ) : __( 'Add your airports above and these tools will open date, route, and map views that make the next step easier to understand.', 'bookings_and_flights' ) ); ?></p>
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
					'eyebrow'          => __( 'Date view', 'bookings_and_flights' ),
					'title'            => __( 'Check flexible days', 'bookings_and_flights' ),
					'description'      => __( 'Look around your planned dates to spot better timing before you compare live fares.', 'bookings_and_flights' ),
					'origin'           => $origin,
					'destination'      => $destination,
					'details'          => $details,
					'fallback_message' => __( 'The low-price calendar placement is not configured yet.', 'bookings_and_flights' ),
					'support_note'     => '',
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'Check flexible days', 'bookings_and_flights' ), __( 'Enter both airports above to unlock the date view for this route.', 'bookings_and_flights' ) ); ?>
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
					'eyebrow'          => __( 'Route ideas', 'bookings_and_flights' ),
					'title'            => __( 'See popular ways in', 'bookings_and_flights' ),
					'description'      => __( 'Browse common flight paths into this destination when you are open to nearby departure cities.', 'bookings_and_flights' ),
					'destination'      => $destination,
					'details'          => $details,
					'fallback_message' => __( 'The popular routes placement is not configured yet.', 'bookings_and_flights' ),
					'support_note'     => '',
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'See popular ways in', 'bookings_and_flights' ), __( 'Add a destination above to show route ideas into that place.', 'bookings_and_flights' ) ); ?>
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
					'eyebrow'          => __( 'Map view', 'bookings_and_flights' ),
					'title'            => __( 'See where you can fly', 'bookings_and_flights' ),
					'description'      => __( 'Scan destinations from your starting airport when you want inspiration before narrowing the trip.', 'bookings_and_flights' ),
					'origin'           => $origin,
					'details'          => $details,
					'fallback_message' => __( 'The route map placement is not configured yet.', 'bookings_and_flights' ),
					'support_note'     => '',
				)
			);
			?>
		<?php else : ?>
			<?php $render_missing( __( 'See where you can fly', 'bookings_and_flights' ), __( 'Add a starting airport above to open the route map.', 'bookings_and_flights' ) ); ?>
		<?php endif; ?>
	</div>

	<p class="flight-discovery__note"><?php esc_html_e( 'Sponsored travel tools may earn a commission. Prices, checkout, changes, and support are handled by the booking site you choose.', 'bookings_and_flights' ); ?></p>
</section>
