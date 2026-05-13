<?php
/**
 * SEO metadata and indexation helpers for flight and route surfaces.
 *
 * @package Bookings_and_Flights_Static
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'document_title_parts', 'bookings_and_flights_filter_document_title_parts' );
add_action( 'wp_head', 'bookings_and_flights_render_seo_metadata', 2 );
add_filter( 'wp_robots', 'bookings_and_flights_filter_wp_robots' );

function bookings_and_flights_normalize_route_code( $value ): string {
	$normalized = preg_replace( '/[^A-Z0-9]/', '', strtoupper( sanitize_text_field( (string) $value ) ) );

	return is_string( $normalized ) ? substr( $normalized, 0, 10 ) : '';
}

function bookings_and_flights_route_origin_filter(): string {
	if ( ! isset( $_GET['route_origin'] ) || ! is_scalar( $_GET['route_origin'] ) ) {
		return '';
	}

	return bookings_and_flights_normalize_route_code( wp_unslash( (string) $_GET['route_origin'] ) );
}

function bookings_and_flights_public_route_archive_url( string $origin = '' ): string {
	$archive_url = get_post_type_archive_link( 'route' );
	if ( ! is_string( $archive_url ) || '' === $archive_url ) {
		$archive_url = home_url( '/routes/' );
	}

	if ( '' === $origin ) {
		return $archive_url;
	}

	return add_query_arg( 'route_origin', $origin, $archive_url );
}

function bookings_and_flights_get_flight_query_code( string $key ): string {
	if ( ! isset( $_GET[ $key ] ) || ! is_scalar( $_GET[ $key ] ) ) {
		return '';
	}

	return bookings_and_flights_normalize_route_code( wp_unslash( (string) $_GET[ $key ] ) );
}

function bookings_and_flights_has_flight_search_query(): bool {
	$search_keys = array(
		'origin',
		'destination',
		'depart_date',
		'return_date',
		'travelers',
		'cabin',
		'travel_focus',
		'baf_surface',
		'baf_alert_status',
	);

	foreach ( $search_keys as $key ) {
		if ( isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) && '' !== trim( (string) wp_unslash( (string) $_GET[ $key ] ) ) ) {
			return true;
		}
	}

	return false;
}

function bookings_and_flights_is_hotels_request_path(): bool {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) ) : '';
	$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );

	if ( ! is_string( $request_path ) ) {
		return false;
	}

	$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home_path = is_string( $home_path ) ? untrailingslashit( $home_path ) : '';
	if ( '' !== $home_path && '/' !== $home_path && 0 === strpos( $request_path, $home_path . '/' ) ) {
		$request_path = substr( $request_path, strlen( $home_path ) );
	}

	return '/hotels/' === trailingslashit( '/' . trim( $request_path, '/' ) );
}

function bookings_and_flights_has_hotel_search_query(): bool {
	$search_keys = array(
		'travel_destination',
		'check_in',
		'check_out',
		'guests',
		'rooms',
		'stay_focus',
		'baf_surface',
	);

	foreach ( $search_keys as $key ) {
		if ( isset( $_GET[ $key ] ) && is_scalar( $_GET[ $key ] ) && '' !== trim( (string) wp_unslash( (string) $_GET[ $key ] ) ) ) {
			return true;
		}
	}

	return false;
}

function bookings_and_flights_route_label_for_post( int $post_id ): string {
	$origin              = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_origin', true ) );
	$destination         = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
	$origin_airport      = bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_origin_airport', true ) );
	$destination_airport = bookings_and_flights_normalize_route_code( get_post_meta( $post_id, 'baf_destination_airport', true ) );
	$route_label_parts   = array_filter(
		array(
			'' !== $origin_airport ? $origin_airport : $origin,
			'' !== $destination_airport ? $destination_airport : $destination,
		)
	);

	return 2 === count( $route_label_parts ) ? implode( ' to ', $route_label_parts ) : get_the_title( $post_id );
}

function bookings_and_flights_destination_label_for_post( int $post_id ): string {
	$destination = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );

	return '' !== $destination ? $destination : wp_strip_all_tags( get_the_title( $post_id ) );
}

function bookings_and_flights_get_seo_context(): array {
	if ( is_singular( 'destination' ) ) {
		$post_id           = get_queried_object_id();
		$destination_label = bookings_and_flights_destination_label_for_post( $post_id );
		$summary           = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_hotel_guide_summary', true ) );
		$excerpt           = has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';
		$description       = '' !== $summary ? $summary : $excerpt;

		return array(
			'title'       => sprintf(
				/* translators: %s: destination name. */
				__( '%s hotel guide', 'bookings_and_flights' ),
				$destination_label
			),
			'description' => '' !== $description ? wp_trim_words( $description, 28, '' ) : sprintf(
				/* translators: %s: destination name. */
				__( 'Browse the %s city hotel guide with editable stay guidance and a Travelpayouts-controlled hotel search handoff.', 'bookings_and_flights' ),
				$destination_label
			),
		);
	}

	if ( is_post_type_archive( 'destination' ) ) {
		$archive_url = get_post_type_archive_link( 'destination' );
		$archive_url = is_string( $archive_url ) && '' !== $archive_url ? $archive_url : home_url( '/destinations/' );

		return array(
			'title'       => __( 'City hotel guides', 'bookings_and_flights' ),
			'description' => __( 'Browse WordPress-owned city hotel guides with editorial stay guidance, internal route links, and Travelpayouts-controlled hotel search handoff.', 'bookings_and_flights' ),
			'canonical'   => $archive_url,
		);
	}

	if ( is_singular( 'route' ) ) {
		$post_id     = get_queried_object_id();
		$route_label = bookings_and_flights_route_label_for_post( $post_id );
		$excerpt     = has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';

		return array(
			'title'       => sprintf(
				/* translators: %s: route title. */
				__( '%s route guide', 'bookings_and_flights' ),
				wp_strip_all_tags( get_the_title( $post_id ) )
			),
			'description' => '' !== $excerpt ? wp_trim_words( $excerpt, 28, '' ) : sprintf(
				/* translators: %s: route label. */
				__( 'Plan %s with WordPress-owned route context before continuing to Travelpayouts-controlled provider search and booking support.', 'bookings_and_flights' ),
				$route_label
			),
		);
	}

	if ( is_post_type_archive( 'route' ) ) {
		$origin = bookings_and_flights_route_origin_filter();

		if ( '' !== $origin ) {
			return array(
				'title'       => sprintf(
					/* translators: %s: origin airport or city code. */
					__( 'Flights from %s', 'bookings_and_flights' ),
					$origin
				),
				'description' => sprintf(
					/* translators: %s: origin airport or city code. */
					__( 'Browse indexable WordPress route guides from %s, then continue into Travelpayouts-controlled flight search for live provider results.', 'bookings_and_flights' ),
					$origin
				),
				'canonical'   => bookings_and_flights_public_route_archive_url( $origin ),
			);
		}

		return array(
			'title'       => __( 'Flight route guides', 'bookings_and_flights' ),
			'description' => __( 'Browse WordPress-owned flight route guides with editorial planning context and internal links to Travelpayouts-controlled provider handoff.', 'bookings_and_flights' ),
			'canonical'   => bookings_and_flights_public_route_archive_url(),
		);
	}

	if ( function_exists( 'bookings_and_flights_is_flights_request_path' ) && bookings_and_flights_is_flights_request_path() ) {
		$origin      = bookings_and_flights_get_flight_query_code( 'origin' );
		$destination = bookings_and_flights_get_flight_query_code( 'destination' );
		$title       = __( 'Flight search handoff', 'bookings_and_flights' );

		if ( '' !== $origin && '' !== $destination ) {
			$title = sprintf(
				/* translators: 1: origin code, 2: destination code. */
				__( '%1$s to %2$s flight search handoff', 'bookings_and_flights' ),
				$origin,
				$destination
			);
		}

		return array(
			'title'       => $title,
			'description' => __( 'Search flights from the Bookings and Flights planning page, then continue to Travelpayouts-controlled provider results for live prices, booking, payment, changes, and support.', 'bookings_and_flights' ),
		);
	}

	return array();
}

function bookings_and_flights_filter_document_title_parts( array $parts ): array {
	$context = bookings_and_flights_get_seo_context();

	if ( isset( $context['title'] ) && '' !== $context['title'] ) {
		$parts['title'] = $context['title'];
	}

	return $parts;
}

function bookings_and_flights_render_seo_metadata(): void {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$context = bookings_and_flights_get_seo_context();

	if ( empty( $context ) ) {
		return;
	}

	if ( isset( $context['description'] ) && '' !== $context['description'] ) {
		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $context['description'] ) );
	}

	if ( isset( $context['canonical'] ) && '' !== $context['canonical'] ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $context['canonical'] ) );
	}
}

function bookings_and_flights_filter_wp_robots( array $robots ): array {
	$is_flight_search = function_exists( 'bookings_and_flights_is_flights_request_path' ) && bookings_and_flights_is_flights_request_path() && bookings_and_flights_has_flight_search_query();
	$is_hotel_search  = bookings_and_flights_is_hotels_request_path() && bookings_and_flights_has_hotel_search_query();

	if ( $is_flight_search || $is_hotel_search ) {
		unset( $robots['index'] );
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}
