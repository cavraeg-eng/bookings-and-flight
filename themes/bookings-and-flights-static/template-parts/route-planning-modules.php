<?php
/**
 * Route planning modules for editable SEO route pages.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'route_label'         => '',
		'origin_airport'      => '',
		'destination_airport' => '',
		'destination_label'   => '',
		'travel_time'         => '',
		'airport_notes'       => '',
		'flexible_dates'      => '',
		'destination_notes'   => '',
		'flight_url'          => home_url( '/flights/' ),
		'hotel_url'           => home_url( '/hotels/' ),
		'activity_url'        => home_url( '/#explore' ),
		'calendar_url'        => '#route-discovery-widgets',
		'alert_url'           => '#route-alerts',
	)
);

$route_label         = sanitize_text_field( (string) $args['route_label'] );
$origin_airport      = bookings_and_flights_normalize_route_code( (string) $args['origin_airport'] );
$destination_airport = bookings_and_flights_normalize_route_code( (string) $args['destination_airport'] );
$destination_label   = sanitize_text_field( (string) $args['destination_label'] );
$route_label         = '' !== $route_label ? $route_label : __( 'this route', 'bookings_and_flights' );
$destination_label   = '' !== $destination_label ? $destination_label : ( '' !== $destination_airport ? $destination_airport : __( 'the destination', 'bookings_and_flights' ) );

$copy = static function ( string $value ): string {
	return sanitize_textarea_field( $value );
};

$modules = array(
	array(
		'label' => __( 'Travel time', 'bookings_and_flights' ),
		'title' => __( 'Travel time context', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['travel_time'] ? $copy( (string) $args['travel_time'] ) : __( 'Use flight duration, time-zone changes, connection risk, and arrival timing as editorial planning context before checking current provider itineraries.', 'bookings_and_flights' ),
	),
	array(
		'label' => __( 'Airports', 'bookings_and_flights' ),
		'title' => __( 'Airport and connection notes', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['airport_notes'] ? $copy( (string) $args['airport_notes'] ) : sprintf(
			/* translators: 1: origin airport code, 2: destination airport code. */
			__( 'Frame nearby airport choices, transfer time, baggage rules, and connection tradeoffs for %1$s to %2$s without claiming live availability.', 'bookings_and_flights' ),
			'' !== $origin_airport ? $origin_airport : __( 'the origin', 'bookings_and_flights' ),
			'' !== $destination_airport ? $destination_airport : __( 'the destination', 'bookings_and_flights' )
		),
	),
	array(
		'label' => __( 'Flexible dates', 'bookings_and_flights' ),
		'title' => __( 'Flexible-date guidance', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['flexible_dates'] ? $copy( (string) $args['flexible_dates'] ) : __( 'Use month, weekday, holiday, and shoulder-season notes as planning prompts. Current fares and calendar behavior remain inside Travelpayouts.', 'bookings_and_flights' ),
	),
	array(
		'label' => __( 'Destination', 'bookings_and_flights' ),
		'title' => __( 'Hotels and activities at destination', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['destination_notes'] ? $copy( (string) $args['destination_notes'] ) : sprintf(
			/* translators: %s: destination label. */
			__( 'After choosing flights into %s, continue into hotel and activity planning modules while bookings and live provider details stay outside WordPress.', 'bookings_and_flights' ),
			$destination_label
		),
	),
);

$links = array(
	array(
		'label' => __( 'Provider search', 'bookings_and_flights' ),
		'title' => __( 'Open provider flight search', 'bookings_and_flights' ),
		'copy'  => __( 'Live results, filters, fares, booking, payment, changes, and support stay with Travelpayouts or the partner provider.', 'bookings_and_flights' ),
		'url'   => (string) $args['flight_url'],
	),
	array(
		'label' => __( 'Calendar', 'bookings_and_flights' ),
		'title' => __( 'Review low-price calendar module', 'bookings_and_flights' ),
		'copy'  => __( 'Use the approved Travelpayouts calendar widget for flexible-date context without storing fare inventory in WordPress.', 'bookings_and_flights' ),
		'url'   => (string) $args['calendar_url'],
	),
	array(
		'label' => __( 'Alert', 'bookings_and_flights' ),
		'title' => __( 'Set route alert intent', 'bookings_and_flights' ),
		'copy'  => __( 'Save a local alert request as planning intent; provider fare monitoring and booking remain separate from WordPress.', 'bookings_and_flights' ),
		'url'   => (string) $args['alert_url'],
	),
	array(
		'label' => __( 'Hotels', 'bookings_and_flights' ),
		'title' => __( 'Open destination hotel handoff', 'bookings_and_flights' ),
		'copy'  => __( 'Use WordPress context to frame the stay, then confirm current rooms, taxes, policies, and booking terms with the partner provider.', 'bookings_and_flights' ),
		'url'   => (string) $args['hotel_url'],
	),
	array(
		'label' => __( 'Activities', 'bookings_and_flights' ),
		'title' => __( 'Explore destination activity prompts', 'bookings_and_flights' ),
		'copy'  => __( 'Keep activities as editorial planning context until a later approved provider or AI workflow owns execution.', 'bookings_and_flights' ),
		'url'   => (string) $args['activity_url'],
	),
);
?>

<section class="route-content route-content--modules" aria-labelledby="route-modules-title">
	<div class="route-content__inner route-content__inner--stack">
		<div class="route-section-heading">
			<p class="route-section-heading__eyebrow"><?php esc_html_e( 'Route content engine', 'bookings_and_flights' ); ?></p>
			<h2 id="route-modules-title"><?php esc_html_e( 'Timing, airports, dates, and destination context', 'bookings_and_flights' ); ?></h2>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: route label. */
						__( 'Use these editable modules to make %s useful before a traveler opens provider-controlled search.', 'bookings_and_flights' ),
						$route_label
					)
				);
				?>
			</p>
		</div>

		<div class="route-module-grid">
			<?php foreach ( $modules as $module ) : ?>
				<article class="route-module">
					<span class="route-module__label"><?php echo esc_html( $module['label'] ); ?></span>
					<h3><?php echo esc_html( $module['title'] ); ?></h3>
					<p><?php echo esc_html( $module['copy'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="route-link-card-grid" aria-label="<?php esc_attr_e( 'Route handoff and internal modules', 'bookings_and_flights' ); ?>">
			<?php foreach ( $links as $link ) : ?>
				<a class="route-link-card" href="<?php echo esc_url( $link['url'] ); ?>">
					<span class="route-link-card__label"><?php echo esc_html( $link['label'] ); ?></span>
					<strong><?php echo esc_html( $link['title'] ); ?></strong>
					<span><?php echo esc_html( $link['copy'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
