<?php
/**
 * Hotel companion placements for provider-owned maps and listings.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'title'       => __( 'Provider hotel map and listing handoffs', 'bookings_and_flights' ),
		'description' => __( 'Use these sponsored companion placements to continue into provider-owned map controls, hotel listings, rates, taxes, booking, payment, changes, and support.', 'bookings_and_flights' ),
		'channel'     => 'search_page',
		'slug_prefix' => 'hotels',
		'class'       => '',
		'details'     => array(),
	)
);

$title_id    = wp_unique_id( 'hotel-discovery-placements-title-' );
$channel     = sanitize_key( (string) $args['channel'] );
$slug_prefix = sanitize_key( (string) $args['slug_prefix'] );
$class       = sanitize_html_class( (string) $args['class'] );
$details     = is_array( $args['details'] ) ? $args['details'] : array();

$placements = array(
	array(
		'placement'   => 'hotels_map_handoff',
		'slug_suffix' => 'hotel_map',
		'class'       => 'search-placement--hotel-companion search-placement--hotel-map',
		'eyebrow'     => __( 'Partner map', 'bookings_and_flights' ),
		'title'       => __( 'Open provider hotel map', 'bookings_and_flights' ),
		'description' => __( 'Map position, neighborhood filters, amenities, live rates, taxes, and room policies remain inside the partner hotel surface.', 'bookings_and_flights' ),
		'fallback'    => __( 'Hotel map handoff is being configured through the Travelpayouts placement registry.', 'bookings_and_flights' ),
		'support'     => __( 'Sponsored hotel map handoff may earn a commission. Bookings and Flights does not store live map inventory or complete reservations.', 'bookings_and_flights' ),
	),
	array(
		'placement'   => 'hotels_listing_handoff',
		'slug_suffix' => 'hotel_listings',
		'class'       => 'search-placement--hotel-companion search-placement--hotel-listings',
		'eyebrow'     => __( 'Partner listings', 'bookings_and_flights' ),
		'title'       => __( 'Open provider hotel listings', 'bookings_and_flights' ),
		'description' => __( 'Use provider-owned listings to compare current hotels, prices, rooms, taxes, policies, and final booking details.', 'bookings_and_flights' ),
		'fallback'    => __( 'Hotel listing handoff is being configured through the Travelpayouts placement registry.', 'bookings_and_flights' ),
		'support'     => __( 'Sponsored hotel listing handoff may earn a commission. Live hotel data, checkout, payment, changes, and support stay with the partner provider.', 'bookings_and_flights' ),
	),
);
?>

<section class="<?php echo esc_attr( trim( 'hotel-partner-placements ' . $class ) ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div class="hotel-partner-placements__inner">
		<div class="hotel-guide-heading">
			<p class="hotel-guide-heading__eyebrow"><?php esc_html_e( 'Hotel partner tools', 'bookings_and_flights' ); ?></p>
			<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( (string) $args['title'] ); ?></h2>
			<p><?php echo esc_html( (string) $args['description'] ); ?></p>
		</div>

		<div class="hotel-partner-placements__grid">
			<?php foreach ( $placements as $placement ) : ?>
				<?php
				get_template_part(
					'template-parts/travel-search-placement',
					null,
					array(
						'placement'        => $placement['placement'],
						'surface'          => 'hotels',
						'channel'          => $channel,
						'slug'             => trim( $slug_prefix . '_' . sanitize_key( $placement['slug_suffix'] ), '_' ),
						'class'            => $placement['class'],
						'eyebrow'          => $placement['eyebrow'],
						'title'            => $placement['title'],
						'description'      => $placement['description'],
						'details'          => $details,
						'fallback_message' => $placement['fallback'],
						'support_note'     => $placement['support'],
					)
				);
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
