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
add_action( 'pre_get_posts', 'bookings_and_flights_filter_taxonomy_archive_query' );

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

function bookings_and_flights_public_deal_archive_url(): string {
	$archive_url = get_post_type_archive_link( 'travel_deal' );

	return is_string( $archive_url ) && '' !== $archive_url ? $archive_url : home_url( '/travel-deals/' );
}

function bookings_and_flights_taxonomy_archive_post_type_map(): array {
	return array(
		'travel_region'   => array( 'destination', 'route', 'travel_deal' ),
		'travel_style'    => array( 'destination', 'route', 'travel_deal' ),
		'travel_vertical' => array( 'route', 'travel_deal' ),
		'travel_season'   => array( 'destination', 'route', 'travel_deal' ),
	);
}

function bookings_and_flights_taxonomy_archive_post_types( string $taxonomy ): array {
	$post_type_map = bookings_and_flights_taxonomy_archive_post_type_map();

	return $post_type_map[ $taxonomy ] ?? array( 'destination', 'route', 'travel_deal' );
}

function bookings_and_flights_filter_taxonomy_archive_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$matched_taxonomy = '';
	foreach ( array_keys( bookings_and_flights_taxonomy_archive_post_type_map() ) as $taxonomy ) {
		if ( $query->is_tax( $taxonomy ) ) {
			$matched_taxonomy = $taxonomy;
			break;
		}
	}

	if ( '' === $matched_taxonomy ) {
		return;
	}

	$query->set( 'post_type', bookings_and_flights_taxonomy_archive_post_types( $matched_taxonomy ) );
	$query->set( 'post_status', 'publish' );
	$query->set( 'posts_per_page', 12 );
	$query->set( 'ignore_sticky_posts', true );
}

function bookings_and_flights_taxonomy_canonical_url( WP_Term $term ): string {
	$paged = max( 1, absint( get_query_var( 'paged' ) ) );
	$term_link = get_term_link( $term );

	if ( is_wp_error( $term_link ) ) {
		return '';
	}

	if ( $paged <= 1 ) {
		return $term_link;
	}

	if ( get_option( 'permalink_structure' ) ) {
		return trailingslashit( $term_link ) . user_trailingslashit( 'page/' . $paged, 'paged' );
	}

	return add_query_arg( 'paged', $paged, $term_link );
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

function bookings_and_flights_deal_label_for_post( int $post_id ): string {
	$destination = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination', true ) );
	$season      = wp_get_post_terms( $post_id, 'travel_season', array( 'fields' => 'names' ) );

	if ( ! is_wp_error( $season ) && ! empty( $season[0] ) && '' !== $destination ) {
		return sprintf(
			/* translators: 1: season label, 2: destination label. */
			__( '%1$s trip idea for %2$s', 'bookings_and_flights' ),
			sanitize_text_field( (string) $season[0] ),
			$destination
		);
	}

	return '' !== $destination ? $destination : wp_strip_all_tags( get_the_title( $post_id ) );
}

function bookings_and_flights_get_seo_context(): array {
	if ( is_singular( 'destination' ) ) {
		$post_id           = get_queried_object_id();
		$destination_label = bookings_and_flights_destination_label_for_post( $post_id );
		$summary           = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_hotel_guide_summary', true ) );
		$destination_facts = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_destination_facts', true ) );
		$excerpt           = has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';
		$description       = '' !== $excerpt ? $excerpt : ( '' !== $destination_facts ? $destination_facts : $summary );

		return array(
			'title'       => sprintf(
				/* translators: %s: destination name. */
				__( '%s destination guide', 'bookings_and_flights' ),
				$destination_label
			),
			'description' => '' !== $description ? wp_trim_words( $description, 28, '' ) : sprintf(
				/* translators: %s: destination name. */
				__( 'Browse the %s destination guide with editable timing, activities, route, hotel, and partner handoff planning context.', 'bookings_and_flights' ),
				$destination_label
			),
		);
	}

	if ( is_post_type_archive( 'destination' ) ) {
		$archive_url = get_post_type_archive_link( 'destination' );
		$archive_url = is_string( $archive_url ) && '' !== $archive_url ? $archive_url : home_url( '/destinations/' );

		return array(
			'title'       => __( 'Destination guides', 'bookings_and_flights' ),
			'description' => __( 'Browse WordPress-owned destination guides with editable timing, activity, route, hotel, internal-link, and partner handoff planning context.', 'bookings_and_flights' ),
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

	if ( is_singular( 'travel_deal' ) ) {
		$post_id     = get_queried_object_id();
		$deal_label  = bookings_and_flights_deal_label_for_post( $post_id );
		$excerpt     = has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '';
		$source_note = sanitize_text_field( (string) get_post_meta( $post_id, 'baf_deal_source_note', true ) );

		return array(
			'title'       => sprintf(
				/* translators: %s: deal title. */
				__( '%s travel deal brief', 'bookings_and_flights' ),
				wp_strip_all_tags( get_the_title( $post_id ) )
			),
			'description' => '' !== $excerpt ? wp_trim_words( $excerpt, 28, '' ) : ( '' !== $source_note ? wp_trim_words( $source_note, 28, '' ) : sprintf(
				/* translators: %s: deal label. */
				__( 'Review the %s editorial travel deal brief before opening Travelpayouts-controlled provider search and booking support.', 'bookings_and_flights' ),
				$deal_label
			) ),
		);
	}

	if ( is_post_type_archive( 'travel_deal' ) ) {
		return array(
			'title'       => __( 'Travel deal ideas', 'bookings_and_flights' ),
			'description' => __( 'Browse WordPress-owned travel deal briefs with seasonal, weekend, style, activity, disclosure, and approved provider handoff context.', 'bookings_and_flights' ),
			'canonical'   => bookings_and_flights_public_deal_archive_url(),
		);
	}

	if ( is_tax( array( 'travel_region', 'travel_style', 'travel_vertical', 'travel_season' ) ) ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$term_name       = sanitize_text_field( $term->name );
			$canonical       = bookings_and_flights_taxonomy_canonical_url( $term );
			$taxonomy_labels = array(
				'travel_region'   => __( 'region', 'bookings_and_flights' ),
				'travel_style'    => __( 'travel style', 'bookings_and_flights' ),
				'travel_vertical' => __( 'travel vertical', 'bookings_and_flights' ),
				'travel_season'   => __( 'season', 'bookings_and_flights' ),
			);
			$taxonomy_label  = $taxonomy_labels[ $term->taxonomy ] ?? __( 'travel topic', 'bookings_and_flights' );
			$description     = '' !== trim( (string) $term->description ) ? wp_trim_words( wp_strip_all_tags( $term->description ), 28, '' ) : sprintf(
				/* translators: 1: taxonomy term, 2: taxonomy label. */
				__( 'Browse public destination, route, and deal content for %1$s, grouped by %2$s with approved provider handoff links.', 'bookings_and_flights' ),
				$term_name,
				$taxonomy_label
			);

			return array(
				'title'       => sprintf(
					/* translators: %s: taxonomy term. */
					__( '%s travel ideas', 'bookings_and_flights' ),
					$term_name
				),
				'description' => $description,
				'canonical'   => $canonical,
			);
		}
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
