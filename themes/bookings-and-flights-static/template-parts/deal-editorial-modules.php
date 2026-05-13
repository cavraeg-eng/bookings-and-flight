<?php
/**
 * Deal editorial and partner handoff modules.
 *
 * @package Bookings_and_Flights_Static
 */

$args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'deal_label'       => '',
		'destination_label' => '',
		'seasonal_context' => '',
		'weekend_ideas'    => '',
		'theme_notes'      => '',
		'activity_notes'   => '',
		'partner_notes'    => '',
		'source_note'      => '',
		'flight_url'       => home_url( '/flights/' ),
		'hotel_url'        => home_url( '/hotels/' ),
		'activity_url'     => home_url( '/#explore' ),
		'archive_url'      => home_url( '/travel-deals/' ),
	)
);

$deal_label        = sanitize_text_field( (string) $args['deal_label'] );
$destination_label = sanitize_text_field( (string) $args['destination_label'] );
$deal_label        = '' !== $deal_label ? $deal_label : __( 'this travel deal brief', 'bookings_and_flights' );
$destination_label = '' !== $destination_label ? $destination_label : __( 'the destination', 'bookings_and_flights' );

$clean_textarea = static function ( string $value ): string {
	return sanitize_textarea_field( $value );
};

$modules = array(
	array(
		'label' => __( 'Seasonal', 'bookings_and_flights' ),
		'title' => __( 'Seasonal trip framing', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['seasonal_context'] ? $clean_textarea( (string) $args['seasonal_context'] ) : __( 'Use month, holiday, shoulder-season, weather, and crowd notes as planning context. Current fares, availability, and rules stay with provider search.', 'bookings_and_flights' ),
	),
	array(
		'label' => __( 'Weekend', 'bookings_and_flights' ),
		'title' => __( 'Weekend and short-stay ideas', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['weekend_ideas'] ? $clean_textarea( (string) $args['weekend_ideas'] ) : __( 'Frame long-weekend, quick-break, and flexible-day ideas without claiming exact inventory, room availability, or current provider prices.', 'bookings_and_flights' ),
	),
	array(
		'label' => __( 'Style', 'bookings_and_flights' ),
		'title' => __( 'Budget, family, luxury, beach, and business fit', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['theme_notes'] ? $clean_textarea( (string) $args['theme_notes'] ) : __( 'Use travel-style labels to organize editorial guidance. Let provider tools confirm current fares, rooms, policies, and supplier terms.', 'bookings_and_flights' ),
	),
	array(
		'label' => __( 'Activities', 'bookings_and_flights' ),
		'title' => __( 'Activity prompts near the trip idea', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['activity_notes'] ? $clean_textarea( (string) $args['activity_notes'] ) : sprintf(
			/* translators: %s: destination label. */
			__( 'Suggest what to compare around %s, then keep tickets, times, provider rules, payment, changes, and support outside WordPress until a later approved integration owns them.', 'bookings_and_flights' ),
			$destination_label
		),
	),
	array(
		'label' => __( 'Sources', 'bookings_and_flights' ),
		'title' => __( 'Claim-safety notes', 'bookings_and_flights' ),
		'copy'  => '' !== (string) $args['source_note'] ? $clean_textarea( (string) $args['source_note'] ) : __( 'Use cautious editorial wording unless a claim is sourced. Do not invent scarcity, current price, live availability, or booking terms.', 'bookings_and_flights' ),
	),
);

$partner_copy = '' !== (string) $args['partner_notes'] ? $clean_textarea( (string) $args['partner_notes'] ) : __( 'Sponsored partner cards may earn a commission. Search results, booking, payment, changes, and support stay with Travelpayouts or the partner provider.', 'bookings_and_flights' );

$links = array(
	array(
		'label' => __( 'Flights', 'bookings_and_flights' ),
		'title' => __( 'Open flight handoff', 'bookings_and_flights' ),
		'copy'  => __( 'Continue from the editorial brief into the approved flight search surface for provider-owned results.', 'bookings_and_flights' ),
		'url'   => (string) $args['flight_url'],
	),
	array(
		'label' => __( 'Hotels', 'bookings_and_flights' ),
		'title' => __( 'Open hotel handoff', 'bookings_and_flights' ),
		'copy'  => __( 'Use the destination context here, then confirm current rooms, taxes, policies, and booking terms with the partner provider.', 'bookings_and_flights' ),
		'url'   => (string) $args['hotel_url'],
	),
	array(
		'label' => __( 'Activities', 'bookings_and_flights' ),
		'title' => __( 'Explore activity prompts', 'bookings_and_flights' ),
		'copy'  => __( 'Treat activities as editorial discovery until an approved provider workflow is added.', 'bookings_and_flights' ),
		'url'   => (string) $args['activity_url'],
	),
	array(
		'label' => __( 'More ideas', 'bookings_and_flights' ),
		'title' => __( 'Browse travel deal ideas', 'bookings_and_flights' ),
		'copy'  => __( 'Move between related editorial briefs without implying that WordPress owns live supplier inventory.', 'bookings_and_flights' ),
		'url'   => (string) $args['archive_url'],
	),
);
?>

<section id="deal-partner-cards" class="deal-section deal-section--modules" aria-labelledby="deal-modules-title">
	<div class="deal-section__inner deal-section__inner--stack">
		<div class="deal-heading">
			<p class="deal-heading__eyebrow"><?php esc_html_e( 'Editorial deal engine', 'bookings_and_flights' ); ?></p>
			<h2 id="deal-modules-title"><?php esc_html_e( 'Seasonal, weekend, style, and activity context', 'bookings_and_flights' ); ?></h2>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: deal label. */
						__( 'Use these editable modules to make %s useful before a traveler opens provider-controlled search.', 'bookings_and_flights' ),
						$deal_label
					)
				);
				?>
			</p>
		</div>

		<div class="deal-module-grid">
			<?php foreach ( $modules as $module ) : ?>
				<article class="deal-module">
					<span class="deal-module__label"><?php echo esc_html( $module['label'] ); ?></span>
					<h3><?php echo esc_html( $module['title'] ); ?></h3>
					<p><?php echo esc_html( $module['copy'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="deal-partner-card-grid" aria-label="<?php esc_attr_e( 'Sponsored partner and editorial handoffs', 'bookings_and_flights' ); ?>">
			<?php foreach ( $links as $link ) : ?>
				<a class="deal-partner-card" href="<?php echo esc_url( $link['url'] ); ?>">
					<span class="deal-partner-card__label"><?php echo esc_html( $link['label'] ); ?></span>
					<strong><?php echo esc_html( $link['title'] ); ?></strong>
					<span><?php echo esc_html( $link['copy'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>

		<p class="deal-disclosure">
			<strong><?php esc_html_e( 'Affiliate disclosure:', 'bookings_and_flights' ); ?></strong>
			<?php echo esc_html( $partner_copy ); ?>
		</p>
	</div>
</section>
