<?php
/**
 * OpenAI itinerary provider.
 *
 * @package BAF\Core
 */

namespace BAF\Core\AI;

defined( 'ABSPATH' ) || exit;

final class OpenAI_Provider implements AI_Provider_Interface {

	private string $api_key;

	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	public function provider_id(): string {
		return 'openai';
	}

	public function mode(): string {
		return 'live';
	}

	public function generate_itinerary( array $request ): array|\WP_Error {
		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'model'           => 'gpt-4o-mini',
						'temperature'     => 0.5,
						'response_format' => array( 'type' => 'json_object' ),
						'messages'        => array(
							array(
								'role'    => 'system',
								'content' => $this->system_prompt(),
							),
							array(
								'role'    => 'user',
								'content' => wp_json_encode( $this->prompt_payload( $request ) ),
							),
						),
					)
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'baf_ai_provider_unavailable', __( 'The configured AI provider could not be reached.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );

		if ( $status < 200 || $status >= 300 ) {
			return new \WP_Error( 'baf_ai_provider_failed', __( 'The configured AI provider returned an error.', 'bookings-flights-core' ), array( 'status' => 502 ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			return new \WP_Error( 'baf_ai_provider_malformed', __( 'The configured AI provider returned malformed data.', 'bookings-flights-core' ), array( 'status' => 502 ) );
		}

		$content = (string) ( $body['choices'][0]['message']['content'] ?? '' );
		$output  = json_decode( $content, true );

		if ( ! is_array( $output ) ) {
			return new \WP_Error( 'baf_ai_provider_malformed', __( 'The configured AI provider did not return valid itinerary JSON.', 'bookings-flights-core' ), array( 'status' => 502 ) );
		}

		return $output;
	}

	private function system_prompt(): string {
		return 'Return only JSON for an editable travel itinerary. Do not publish content, execute bookings, call providers, create links, or claim availability. Use affiliate_opportunities only as not_executed recommendations that require approval. Required keys: title, summary, destination, duration_days, days, affiliate_opportunities, booking_notes, disclaimer. Each day requires day, title, summary, activities. Each activity requires time_of_day, title, description, location, affiliate_vertical.';
	}

	private function prompt_payload( array $request ): array {
		return array(
			'destination'  => sanitize_text_field( (string) ( $request['destination'] ?? '' ) ),
			'origin'       => sanitize_text_field( (string) ( $request['origin'] ?? '' ) ),
			'days'         => max( 1, min( 21, absint( $request['days'] ?? 3 ) ) ),
			'travel_style' => sanitize_text_field( (string) ( $request['travel_style'] ?? '' ) ),
			'budget'       => sanitize_text_field( (string) ( $request['budget'] ?? '' ) ),
			'preferences'  => sanitize_textarea_field( (string) ( $request['preferences'] ?? '' ) ),
		);
	}
}