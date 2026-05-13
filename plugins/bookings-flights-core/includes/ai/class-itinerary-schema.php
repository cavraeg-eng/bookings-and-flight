<?php
/**
 * Structured itinerary schema validation.
 *
 * @package BAF\Core
 */

namespace BAF\Core\AI;

defined( 'ABSPATH' ) || exit;

final class Itinerary_Schema {

	private const TIME_OF_DAY = array( 'morning', 'afternoon', 'evening', 'flexible' );
	private const VERTICALS   = array( 'flights', 'hotels', 'cars', 'activities', 'insurance', 'esim', 'transfers', 'none' );
	private const OPPORTUNITY_SCHEMA_VERSION = 'travelpayouts_opportunity_v1';
	private const OPPORTUNITY_TYPES          = array( 'flight_search', 'hotel_search', 'activity_search', 'route_widget', 'saved_trip_cta', 'price_alert_cta', 'partner_handoff' );
	private const CONFIDENCE_LEVELS          = array( 'low', 'medium', 'high' );
	private const FORBIDDEN_CLAIM_FIELDS     = array(
		'booking_id',
		'confirmation_number',
		'payment_status',
		'price',
		'price_amount',
		'price_currency',
		'rate',
		'availability',
		'available',
		'live_inventory',
		'provider_link',
		'deeplink',
		'handoff_url',
		'published_url',
		'reservation_id',
		'ticket_number',
	);
	private const FORBIDDEN_STATUS_VALUES    = array( 'available', 'booked', 'confirmed', 'executed', 'paid', 'published', 'reserved', 'ticketed' );

	public static function validate( mixed $output ): array|\WP_Error {
		if ( ! is_array( $output ) ) {
			return self::error( 'baf_ai_output_not_object', __( 'The AI itinerary response was not a structured object.', 'bookings-flights-core' ) );
		}

		$duration_days = max( 1, min( 21, absint( $output['duration_days'] ?? 0 ) ) );
		$days          = self::sanitize_days( $output['days'] ?? array(), $duration_days );

		if ( '' === trim( (string) ( $output['title'] ?? '' ) ) || '' === trim( (string) ( $output['destination'] ?? '' ) ) || 0 === $duration_days || empty( $days ) ) {
			return self::error( 'baf_ai_output_missing_fields', __( 'The AI itinerary response missed required itinerary fields.', 'bookings-flights-core' ) );
		}

		$opportunities = self::sanitize_opportunities( $output['affiliate_opportunities'] ?? array(), (string) ( $output['destination'] ?? '' ) );

		if ( is_wp_error( $opportunities ) ) {
			return $opportunities;
		}

		return array(
			'title'                   => self::text( $output['title'], 160 ),
			'summary'                 => self::textarea( $output['summary'] ?? '', 900 ),
			'destination'             => self::text( $output['destination'], 120 ),
			'duration_days'           => $duration_days,
			'days'                    => $days,
			'opportunity_schema'      => self::OPPORTUNITY_SCHEMA_VERSION,
			'affiliate_opportunities' => $opportunities,
			'booking_notes'           => self::textarea( $output['booking_notes'] ?? '', 700 ),
			'disclaimer'              => self::textarea( $output['disclaimer'] ?? '', 700 ),
		);
	}

	private static function sanitize_days( mixed $days, int $duration_days ): array {
		if ( ! is_array( $days ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( array_slice( $days, 0, $duration_days ) as $day ) {
			if ( ! is_array( $day ) ) {
				continue;
			}

			$activities = self::sanitize_activities( $day['activities'] ?? array() );

			if ( empty( $activities ) ) {
				continue;
			}

			$sanitized[] = array(
				'day'        => max( 1, absint( $day['day'] ?? count( $sanitized ) + 1 ) ),
				'title'      => self::text( $day['title'] ?? '', 140 ),
				'summary'    => self::textarea( $day['summary'] ?? '', 500 ),
				'activities' => $activities,
			);
		}

		return $sanitized;
	}

	private static function sanitize_activities( mixed $activities ): array {
		if ( ! is_array( $activities ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( array_slice( $activities, 0, 6 ) as $activity ) {
			if ( ! is_array( $activity ) || '' === trim( (string) ( $activity['title'] ?? '' ) ) ) {
				continue;
			}

			$time_of_day = sanitize_key( (string) ( $activity['time_of_day'] ?? 'flexible' ) );
			$vertical    = sanitize_key( (string) ( $activity['affiliate_vertical'] ?? 'none' ) );

			$sanitized[] = array(
				'time_of_day'        => in_array( $time_of_day, self::TIME_OF_DAY, true ) ? $time_of_day : 'flexible',
				'title'              => self::text( $activity['title'], 140 ),
				'description'        => self::textarea( $activity['description'] ?? '', 700 ),
				'location'           => self::text( $activity['location'] ?? '', 160 ),
				'affiliate_vertical' => in_array( $vertical, self::VERTICALS, true ) ? $vertical : 'none',
			);
		}

		return $sanitized;
	}

	private static function sanitize_opportunities( mixed $opportunities, string $destination ): array|\WP_Error {
		if ( ! is_array( $opportunities ) ) {
			return array();
		}

		$sanitized = array();
		$destination = self::text( $destination, 120 );

		foreach ( array_slice( $opportunities, 0, 8 ) as $index => $opportunity ) {
			if ( ! is_array( $opportunity ) ) {
				continue;
			}

			$claim_error = self::validate_recommendation_only( $opportunity );

			if ( is_wp_error( $claim_error ) ) {
				return $claim_error;
			}

			$provider = sanitize_key( self::string_value( $opportunity['provider'] ?? 'travelpayouts' ) );
			$vertical = sanitize_key( self::string_value( $opportunity['vertical'] ?? 'none' ) );
			$type     = sanitize_key( self::string_value( $opportunity['recommendation_type'] ?? self::default_recommendation_type( $vertical ) ) );
			$label    = self::text( $opportunity['label'] ?? '', 140 );

			if ( 'travelpayouts' !== $provider ) {
				return self::error( 'baf_ai_opportunity_unsupported_provider', __( 'The AI opportunity response used an unsupported affiliate provider.', 'bookings-flights-core' ) );
			}

			if ( '' === $label ) {
				return self::error( 'baf_ai_opportunity_missing_label', __( 'The AI opportunity response missed a required recommendation label.', 'bookings-flights-core' ) );
			}

			$sanitized[] = array(
				'schema_version'      => self::OPPORTUNITY_SCHEMA_VERSION,
				'provider'            => 'travelpayouts',
				'vertical'            => in_array( $vertical, self::VERTICALS, true ) ? $vertical : 'none',
				'recommendation_type' => in_array( $type, self::OPPORTUNITY_TYPES, true ) ? $type : self::default_recommendation_type( $vertical ),
				'label'               => $label,
				'placement_context'   => self::text( $opportunity['placement_context'] ?? '', 120 ),
				'destination'         => self::text( $opportunity['destination'] ?? $destination, 120 ),
				'route'               => self::text( $opportunity['route'] ?? '', 160 ),
				'suggested_subid'     => self::suggested_subid( $opportunity, $vertical, $destination, $index ),
				'disclosure_required' => true,
				'confidence'          => self::confidence( $opportunity['confidence'] ?? 'medium' ),
				'limitations'         => self::textarea( $opportunity['limitations'] ?? __( 'Recommendation only. No live price, availability, booking, payment, or provider action has been executed.', 'bookings-flights-core' ), 300 ),
				'status'              => 'not_executed',
				'requires_approval'   => true,
				'approval_state'      => 'requires_editor_approval',
				'blocked_actions'     => array( 'book', 'pay', 'publish', 'execute_provider_search', 'create_live_link', 'claim_price_or_availability' ),
			);
		}

		return $sanitized;
	}

	private static function validate_recommendation_only( array $opportunity ): bool|\WP_Error {
		$status = sanitize_key( self::string_value( $opportunity['status'] ?? 'not_executed' ) );

		if ( 'not_executed' !== $status || in_array( $status, self::FORBIDDEN_STATUS_VALUES, true ) ) {
			return self::error( 'baf_ai_opportunity_executed_claim', __( 'The AI opportunity response claimed a provider action that is not allowed.', 'bookings-flights-core' ) );
		}

		if ( array_key_exists( 'requires_approval', $opportunity ) && true !== self::boolean_value( $opportunity['requires_approval'] ) ) {
			return self::error( 'baf_ai_opportunity_missing_approval', __( 'The AI opportunity response must require editor approval.', 'bookings-flights-core' ) );
		}

		if ( array_key_exists( 'disclosure_required', $opportunity ) && true !== self::boolean_value( $opportunity['disclosure_required'] ) ) {
			return self::error( 'baf_ai_opportunity_missing_disclosure', __( 'The AI opportunity response must require affiliate disclosure.', 'bookings-flights-core' ) );
		}

		foreach ( self::FORBIDDEN_CLAIM_FIELDS as $field ) {
			if ( ! array_key_exists( $field, $opportunity ) ) {
				continue;
			}

			$value = $opportunity[ $field ];

			if ( false === $value || null === $value ) {
				continue;
			}

			if ( ! is_scalar( $value ) ) {
				return self::error( 'baf_ai_opportunity_provider_claim', __( 'The AI opportunity response included provider-owned booking, price, availability, or link data.', 'bookings-flights-core' ) );
			}

			if ( '' === trim( self::string_value( $value ) ) ) {
				continue;
			}

			return self::error( 'baf_ai_opportunity_provider_claim', __( 'The AI opportunity response included provider-owned booking, price, availability, or link data.', 'bookings-flights-core' ) );
		}

		return true;
	}

	private static function default_recommendation_type( string $vertical ): string {
		return match ( sanitize_key( $vertical ) ) {
			'flights'    => 'flight_search',
			'hotels'     => 'hotel_search',
			'activities' => 'activity_search',
			default      => 'partner_handoff',
		};
	}

	private static function confidence( mixed $value ): string {
		$confidence = sanitize_key( self::string_value( $value ) );

		return in_array( $confidence, self::CONFIDENCE_LEVELS, true ) ? $confidence : 'medium';
	}

	private static function suggested_subid( array $opportunity, string $vertical, string $destination, int $index ): string {
		$provided = self::sanitize_subid( $opportunity['suggested_subid'] ?? '' );

		if ( '' !== $provided ) {
			return $provided;
		}

		$context = self::sanitize_subid( $opportunity['placement_context'] ?? '' );

		if ( '' === $context ) {
			$context = self::sanitize_subid( $destination );
		}

		return self::sanitize_subid( implode( '_', array_filter( array( 'ai', $vertical, $context, (string) ( $index + 1 ) ) ) ) );
	}

	private static function sanitize_subid( mixed $value ): string {
		$value = strtolower( self::string_value( $value ) );
		$value = (string) preg_replace( '/[^a-z0-9_]+/', '_', $value );
		$value = trim( $value, '_' );

		return substr( $value, 0, 96 );
	}

	private static function text( mixed $value, int $limit ): string {
		return substr( sanitize_text_field( self::string_value( $value ) ), 0, $limit );
	}

	private static function textarea( mixed $value, int $limit ): string {
		return substr( sanitize_textarea_field( self::string_value( $value ) ), 0, $limit );
	}

	private static function boolean_value( mixed $value ): bool {
		if ( is_bool( $value ) || is_scalar( $value ) ) {
			return rest_sanitize_boolean( $value );
		}

		return false;
	}

	private static function string_value( mixed $value ): string {
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( is_int( $value ) || is_float( $value ) || is_bool( $value ) ) {
			return (string) $value;
		}

		if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			return (string) $value;
		}

		return '';
	}

	private static function error( string $code, string $message ): \WP_Error {
		return new \WP_Error( $code, $message, array( 'status' => 502 ) );
	}
}
