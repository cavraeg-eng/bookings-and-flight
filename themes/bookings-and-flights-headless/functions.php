<?php
/**
 * Headless theme bootstrap.
 *
 * @package Bookings_And_Flights\Headless
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'script', 'style' ) );
} );

/**
 * Front-end URL the theme should redirect visitors to.
 * Override with: define( 'BAF_FRONTEND_URL', 'https://example.com' ) in wp-config.php
 */
if ( ! defined( 'BAF_FRONTEND_URL' ) ) {
	define( 'BAF_FRONTEND_URL', 'http://localhost:3000' );
}

/**
 * Disable all front-end rendering.
 * Admins are never redirected so they can keep managing content.
 */
add_action( 'template_redirect', function () {
	if ( is_admin() ) {
		return;
	}
	if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
		return;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$uri = (string) $_SERVER['REQUEST_URI'];
		if (
			str_starts_with( $uri, '/wp-admin' ) ||
			str_starts_with( $uri, '/wp-login.php' ) ||
			str_starts_with( $uri, '/wp-json' ) ||
			str_starts_with( $uri, '/wp-content' ) ||
			str_starts_with( $uri, '/wp-includes' ) ||
			str_starts_with( $uri, '/favicon' ) ||
			str_starts_with( $uri, '/robots.txt' ) ||
			str_starts_with( $uri, '/sitemap' )
		) {
			return;
		}
	}

	$target = rtrim( BAF_FRONTEND_URL, '/' ) . ( $_SERVER['REQUEST_URI'] ?? '/' );
	wp_safe_redirect( $target, 302 );
	exit;
}, 1 );
