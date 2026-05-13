<?php
/**
 * Travelpayouts public asset controls.
 *
 * @package Bookings_and_Flights_Static
 */

function bookings_and_flights_content_has_official_travelpayouts_shortcode( $content ) {
	if ( is_array( $content ) || is_object( $content ) ) {
		foreach ( (array) $content as $value ) {
			if ( bookings_and_flights_content_has_official_travelpayouts_shortcode( $value ) ) {
				return true;
			}
		}

		return false;
	}

	if ( '' === (string) $content ) {
		return false;
	}

	return (bool) preg_match( '/(?:\[(?:tp_|travelpayouts_)|<!--\s+wp:travelpayouts\/)/', (string) $content );
}

function bookings_and_flights_active_widgets_have_official_travelpayouts_shortcode() {
	static $has_shortcode = null;

	if ( null !== $has_shortcode ) {
		return $has_shortcode;
	}

	$has_shortcode = false;
	$sidebars      = get_option( 'sidebars_widgets' );

	if ( ! is_array( $sidebars ) ) {
		return $has_shortcode;
	}

	foreach ( $sidebars as $sidebar_id => $widget_ids ) {
		if ( 'wp_inactive_widgets' === $sidebar_id || ! is_array( $widget_ids ) ) {
			continue;
		}

		foreach ( $widget_ids as $widget_id ) {
			$id_base = preg_replace( '/-\d+$/', '', (string) $widget_id );
			$number  = substr( (string) $widget_id, strlen( $id_base ) + 1 );
			$options = get_option( 'widget_' . $id_base );

			if ( is_array( $options ) && isset( $options[ $number ] ) && bookings_and_flights_content_has_official_travelpayouts_shortcode( $options[ $number ] ) ) {
				$has_shortcode = true;
				return $has_shortcode;
			}
		}
	}

	return $has_shortcode;
}

function bookings_and_flights_has_official_travelpayouts_shortcode() {
	$has_shortcode = ! is_singular();

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post && bookings_and_flights_content_has_official_travelpayouts_shortcode( $post->post_content ) ) {
			$has_shortcode = true;
		}
	}

	if ( bookings_and_flights_active_widgets_have_official_travelpayouts_shortcode() ) {
		$has_shortcode = true;
	}

	return (bool) apply_filters( 'bookings_and_flights_has_official_travelpayouts_output', $has_shortcode );
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
