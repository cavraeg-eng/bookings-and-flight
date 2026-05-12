<?php
/**
 * AI itinerary generation service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\AI\Itinerary_Schema;
use BAF\Core\AI\Provider_Factory;
use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Repositories\AI_Session_Repository;

defined( 'ABSPATH' ) || exit;

final class AI_Itinerary_Service {

	private const PROMPT_VERSION = 'itinerary_v1';

	private AI_Session_Repository $sessions;

	public function __construct( ?AI_Session_Repository $sessions = null ) {
		$this->sessions = $sessions ?? new AI_Session_Repository();
	}

	public function generate( array $request ): array|\WP_Error {
		$provider = Provider_Factory::make();

		if ( is_wp_error( $provider ) ) {
			return $provider;
		}

		$normalized = $this->normalize_request( $request );
		$session    = $this->sessions->start(
			array(
				'provider'       => $provider->provider_id(),
				'mode'           => $provider->mode(),
				'prompt_version' => self::PROMPT_VERSION,
				'source_post_id' => $normalized['source_post_id'],
				'request'        => $normalized,
			)
		);

		if ( is_wp_error( $session ) ) {
			return $session;
		}

		$output = $provider->generate_itinerary( $normalized );

		if ( is_wp_error( $output ) ) {
			$this->finish_error( (int) $session['id'], $output );

			return $output;
		}

		$itinerary = Itinerary_Schema::validate( $output );

		if ( is_wp_error( $itinerary ) ) {
			$this->finish_error( (int) $session['id'], $itinerary );

			return $itinerary;
		}

		$saved_post_id = 0;

		if ( true === $normalized['save'] ) {
			$saved_post_id = $this->save_draft_trip_plan( $itinerary, $normalized, (string) $session['run_uuid'] );

			if ( is_wp_error( $saved_post_id ) ) {
				$this->finish_error( (int) $session['id'], $saved_post_id );

				return $saved_post_id;
			}
		}

		$this->sessions->finish(
			(int) $session['id'],
			'success',
			array(
				'output'  => $itinerary,
				'summary' => $itinerary['title'],
			)
		);

		return array(
			'run_id'       => (string) $session['run_uuid'],
			'mode'         => $provider->mode(),
			'provider'     => $provider->provider_id(),
			'itinerary'    => $itinerary,
			'trip_plan_id' => $saved_post_id,
			'saved_status' => $saved_post_id > 0 ? 'draft' : '',
		);
	}

	private function normalize_request( array $request ): array {
		return array(
			'destination'    => substr( sanitize_text_field( (string) ( $request['destination'] ?? '' ) ), 0, 120 ),
			'origin'         => substr( sanitize_text_field( (string) ( $request['origin'] ?? '' ) ), 0, 120 ),
			'days'           => max( 1, min( 21, absint( $request['days'] ?? 3 ) ) ),
			'travel_style'   => substr( sanitize_text_field( (string) ( $request['travel_style'] ?? '' ) ), 0, 80 ),
			'budget'         => substr( sanitize_text_field( (string) ( $request['budget'] ?? '' ) ), 0, 80 ),
			'preferences'    => substr( sanitize_textarea_field( (string) ( $request['preferences'] ?? '' ) ), 0, 1200 ),
			'source_post_id' => absint( $request['source_post_id'] ?? 0 ),
			'save'           => true === (bool) ( $request['save'] ?? false ),
		);
	}

	private function save_draft_trip_plan( array $itinerary, array $request, string $run_uuid ): int|\WP_Error {
		if ( ! current_user_can( Capability_Manager::EDIT_CONTENT ) ) {
			return new \WP_Error( 'baf_ai_save_forbidden', __( 'You are not allowed to save AI-generated trip plans.', 'bookings-flights-core' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => Post_Type_Registrar::TRIP_PLAN,
				'post_status'  => 'draft',
				'post_title'   => $itinerary['title'],
				'post_excerpt' => $itinerary['summary'],
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error( 'baf_ai_save_failed', __( 'The itinerary was generated but could not be saved as a draft trip plan.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		update_post_meta( $post_id, 'baf_destination', $request['destination'] );
		update_post_meta( $post_id, 'baf_origin', $request['origin'] );
		update_post_meta( $post_id, 'baf_travel_style', $request['travel_style'] );
		update_post_meta( $post_id, 'baf_ai_source_session_id', $run_uuid );
		update_post_meta( $post_id, 'baf_itinerary_json', wp_json_encode( $itinerary ) );

		return (int) $post_id;
	}

	private function finish_error( int $session_id, \WP_Error $error ): void {
		$this->sessions->finish(
			$session_id,
			'failed',
			array(
				'error_code'    => $error->get_error_code(),
				'error_message' => $error->get_error_message(),
			)
		);
	}
}