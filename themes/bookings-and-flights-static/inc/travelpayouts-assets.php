<?php
/**
 * Travelpayouts public asset controls.
 *
 * @package Bookings_and_Flights_Static
 */

function bookings_and_flights_has_official_travelpayouts_shortcode() {
	if ( ! is_singular() ) {
		return false;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || '' === (string) $post->post_content ) {
		return false;
	}

	return (bool) preg_match( '/\[(?:tp_|travelpayouts_)/', (string) $post->post_content );
}

function bookings_and_flights_is_travelpayouts_asset_handle( $handle ) {
	return 0 === strpos( (string) $handle, 'travelpayouts-assets-' );
}

function bookings_and_flights_limit_travelpayouts_public_assets() {
	if ( is_admin() || bookings_and_flights_has_official_travelpayouts_shortcode() ) {
		return;
	}

	global $wp_scripts, $wp_styles;

	if ( $wp_styles instanceof WP_Styles ) {
		foreach ( (array) $wp_styles->queue as $handle ) {
			if ( bookings_and_flights_is_travelpayouts_asset_handle( $handle ) ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}
	}

	if ( $wp_scripts instanceof WP_Scripts ) {
		foreach ( (array) $wp_scripts->queue as $handle ) {
			if ( bookings_and_flights_is_travelpayouts_asset_handle( $handle ) ) {
				wp_dequeue_script( $handle );
				wp_deregister_script( $handle );
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'bookings_and_flights_limit_travelpayouts_public_assets', 999 );
add_action( 'wp_print_styles', 'bookings_and_flights_limit_travelpayouts_public_assets', 1 );
add_action( 'wp_print_scripts', 'bookings_and_flights_limit_travelpayouts_public_assets', 1 );
add_action( 'wp_print_footer_scripts', 'bookings_and_flights_limit_travelpayouts_public_assets', 1 );
