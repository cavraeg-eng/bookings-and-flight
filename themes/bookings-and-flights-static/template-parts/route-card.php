<?php
/**
 * Route summary card.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'post_id' => get_the_ID(),
	)
);

$post_id = absint( $args['post_id'] );

if ( 0 === $post_id || 'route' !== get_post_type( $post_id ) ) {
	return;
}

$route_meta = static function ( string $key ) use ( $post_id ): string {
	return sanitize_text_field( (string) get_post_meta( $post_id, $key, true ) );
};

$route_code = static function ( string $value ): string {
	return bookings_and_flights_normalize_route_code( $value );
};

$origin            = $route_meta( 'baf_origin' );
$destination       = $route_meta( 'baf_destination' );
$origin_airport    = $route_code( $route_meta( 'baf_origin_airport' ) );
$destination_airport = $route_code( $route_meta( 'baf_destination_airport' ) );
$departure_window  = $route_meta( 'baf_departure_window' );
$route_title       = get_the_title( $post_id );
$route_label_parts = array_filter(
	array(
		'' !== $origin_airport ? $origin_airport : $origin,
		'' !== $destination_airport ? $destination_airport : $destination,
	)
);
$route_label       = 2 === count( $route_label_parts ) ? implode( ' to ', $route_label_parts ) : $route_title;
$flight_url_args   = array(
	'baf_surface' => 'route_card',
);

if ( '' !== $origin_airport ) {
	$flight_url_args['origin'] = $origin_airport;
}

if ( '' !== $destination_airport ) {
	$flight_url_args['destination'] = $destination_airport;
}

$flight_url = add_query_arg( $flight_url_args, home_url( '/flights/' ) );
?>

<article class="route-card">
	<p class="route-card__eyebrow"><?php esc_html_e( 'Route guide', 'bookings_and_flights' ); ?></p>
	<h2 class="route-card__title">
		<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $route_title ); ?></a>
	</h2>
	<p class="route-card__route"><?php echo esc_html( $route_label ); ?></p>
	<?php if ( '' !== $departure_window ) : ?>
		<p class="route-card__meta"><?php echo esc_html( $departure_window ); ?></p>
	<?php endif; ?>
	<?php if ( has_excerpt( $post_id ) ) : ?>
		<p class="route-card__excerpt"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
	<?php endif; ?>
	<div class="route-card__actions">
		<a class="route-card__link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'View route guide', 'bookings_and_flights' ); ?></a>
		<a class="route-card__handoff" href="<?php echo esc_url( $flight_url ); ?>"><?php esc_html_e( 'Open flight handoff', 'bookings_and_flights' ); ?></a>
	</div>
</article>
