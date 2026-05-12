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

	public static function validate( mixed $output ): array|\WP_Error {
		if ( ! is_array( $output ) ) {
			return self::error( 'baf_ai_output_not_object', __( 'The AI itinerary response was not a structured object.', 'bookings-flights-core' ) );
		}

		$duration_days = max( 1, min( 21, absint( $output['duration_days'] ?? 0 ) ) );
		$days          = self::sanitize_days( $output['days'] ?? array(), $duration_days );

		if ( '' === trim( (string) ( $output['title'] ?? '' ) ) || '' === trim( (string) ( $output['destination'] ?? '' ) ) || 0 === $duration_days || empty( $days ) ) {
			return self::error( 'baf_ai_output_missing_fields', __( 'The AI itinerary response missed required itinerary fields.', 'bookings-flights-core' ) );
		}

		return array(
			'title'                   => self::text( $output['title'], 160 ),
			'summary'                 => self::textarea( $output['summary'] ?? '', 900 ),
			'destination'             => self::text( $output['destination'], 120 ),
			'duration_days'           => $duration_days,
			'days'                    => $days,
			'affiliate_opportunities' => self::sanitize_opportunities( $output['affiliate_opportunities'] ?? array() ),
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

	private static function sanitize_opportunities( mixed $opportunities ): array {
		if ( ! is_array( $opportunities ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( array_slice( $opportunities, 0, 8 ) as $opportunity ) {
			if ( ! is_array( $opportunity ) ) {
				continue;
			}

			$vertical = sanitize_key( (string) ( $opportunity['vertical'] ?? 'none' ) );

			$sanitized[] = array(
				'provider'          => sanitize_key( (string) ( $opportunity['provider'] ?? 'travelpayouts' ) ),
				'vertical'          => in_array( $vertical, self::VERTICALS, true ) ? $vertical : 'none',
				'label'             => self::text( $opportunity['label'] ?? '', 140 ),
				'status'            => 'not_executed',
				'requires_approval' => true,
			);
		}

		return $sanitized;
	}

	private static function text( mixed $value, int $limit ): string {
		return substr( sanitize_text_field( (string) $value ), 0, $limit );
	}

	private static function textarea( mixed $value, int $limit ): string {
		return substr( sanitize_textarea_field( (string) $value ), 0, $limit );
	}

	private static function error( string $code, string $message ): \WP_Error {
		return new \WP_Error( $code, $message, array( 'status' => 502 ) );
	}
}