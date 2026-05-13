<?php
/**
 * Demo AI provider.
 *
 * @package BAF\Core
 */

namespace BAF\Core\AI;

defined( 'ABSPATH' ) || exit;

final class Demo_AI_Provider implements AI_Provider_Interface {

	public function provider_id(): string {
		return 'demo';
	}

	public function mode(): string {
		return 'demo';
	}

	public function generate_itinerary( array $request ): array|\WP_Error {
		$destination  = sanitize_text_field( (string) ( $request['destination'] ?? __( 'your destination', 'bookings-flights-core' ) ) );
		$origin       = sanitize_text_field( (string) ( $request['origin'] ?? '' ) );
		$prompt       = sanitize_textarea_field( (string) ( $request['prompt'] ?? '' ) );
		$days         = max( 1, min( 21, absint( $request['days'] ?? 3 ) ) );
		$travelers    = max( 1, min( 12, absint( $request['travelers'] ?? 2 ) ) );
		$travel_style = sanitize_text_field( (string) ( $request['travel_style'] ?? __( 'balanced', 'bookings-flights-core' ) ) );
		$day_items    = array();
		$prompt_note  = '' !== $prompt ? __( 'The entered prompt was converted into this editable local brief without calling a live AI provider.', 'bookings-flights-core' ) : __( 'This editable local brief uses the planner form fields without calling a live AI provider.', 'bookings-flights-core' );

		for ( $day = 1; $day <= $days; ++$day ) {
			$day_items[] = array(
				'day'        => $day,
				'title'      => sprintf( __( 'Day %1$d in %2$s', 'bookings-flights-core' ), $day, $destination ),
				'summary'    => sprintf( __( 'A %1$s-paced discovery day with editable recommendations for %2$s.', 'bookings-flights-core' ), $travel_style, $destination ),
				'activities' => array(
					array(
						'time_of_day'        => 'morning',
						'title'              => __( 'Arrival and orientation', 'bookings-flights-core' ),
						'description'        => '' !== $origin ? sprintf( __( 'Review flight options from %1$s and plan an easy arrival into %2$s.', 'bookings-flights-core' ), $origin, $destination ) : sprintf( __( 'Plan an easy arrival into %s and get familiar with the area.', 'bookings-flights-core' ), $destination ),
						'location'           => $destination,
						'affiliate_vertical' => 'flights',
					),
					array(
						'time_of_day'        => 'afternoon',
						'title'              => __( 'Neighborhood highlights', 'bookings-flights-core' ),
						'description'        => __( 'Choose one walkable area, note restaurants and transit options, and keep the schedule flexible.', 'bookings-flights-core' ),
						'location'           => $destination,
						'affiliate_vertical' => 'activities',
					),
					array(
						'time_of_day'        => 'evening',
						'title'              => __( 'Hotel base check-in', 'bookings-flights-core' ),
						'description'        => __( 'Compare lodging areas and save a shortlist before clicking through to partner booking sites.', 'bookings-flights-core' ),
						'location'           => $destination,
						'affiliate_vertical' => 'hotels',
					),
				),
			);
		}

		return array(
			'title'                   => sprintf( __( '%1$d-day %2$s itinerary', 'bookings-flights-core' ), $days, $destination ),
			'summary'                 => sprintf( __( 'Demo itinerary for %1$s with a %2$s travel style for %3$d traveler(s). %4$s', 'bookings-flights-core' ), $destination, $travel_style, $travelers, $prompt_note ),
			'destination'             => $destination,
			'duration_days'           => $days,
			'days'                    => $day_items,
			'affiliate_opportunities' => array(
				array(
					'provider'            => 'travelpayouts',
					'vertical'            => 'flights',
					'recommendation_type' => 'flight_search',
					'label'               => __( 'Flight comparison handoff', 'bookings-flights-core' ),
					'placement_context'   => 'planner_result',
					'destination'         => $destination,
					'route'               => '' !== $origin ? $origin . ' to ' . $destination : $destination,
					'suggested_subid'     => 'ai_planner_flights',
					'disclosure_required' => true,
					'confidence'          => 'medium',
					'limitations'         => __( 'Recommendation only. No live fare, seat availability, booking, or partner link has been created.', 'bookings-flights-core' ),
					'status'              => 'not_executed',
					'requires_approval'   => true,
				),
				array(
					'provider'            => 'travelpayouts',
					'vertical'            => 'hotels',
					'recommendation_type' => 'hotel_search',
					'label'               => __( 'Hotel comparison handoff', 'bookings-flights-core' ),
					'placement_context'   => 'planner_result',
					'destination'         => $destination,
					'route'               => $destination,
					'suggested_subid'     => 'ai_planner_hotels',
					'disclosure_required' => true,
					'confidence'          => 'medium',
					'limitations'         => __( 'Recommendation only. No live room rate, availability, booking, or partner link has been created.', 'bookings-flights-core' ),
					'status'              => 'not_executed',
					'requires_approval'   => true,
				),
			),
			'booking_notes'           => __( 'Bookings and Flights helps compare ideas and sends users to partners for booking; it does not process checkout.', 'bookings-flights-core' ),
			'disclaimer'              => __( 'AI-assisted suggestions are drafts. Review availability, safety, visas, prices, and affiliate disclosures before use.', 'bookings-flights-core' ),
		);
	}
}
