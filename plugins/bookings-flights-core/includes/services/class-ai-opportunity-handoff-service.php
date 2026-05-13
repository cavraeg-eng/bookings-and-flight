<?php
/**
 * Approval-oriented local AI opportunity handoff service.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\AI\Itinerary_Schema;
use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class AI_Opportunity_Handoff_Service {

	public const META_KEY = 'baf_ai_handoff_intents';

	private const ACTION_TYPES = array( 'placement_card', 'placement_draft', 'saved_trip_cta', 'alert_cta' );

	public function prepare( array $request ): array|\WP_Error {
		$normalized = $this->normalize_request( $request );

		if ( ! current_user_can( Capability_Manager::EDIT_CONTENT ) ) {
			return $this->forbidden_error();
		}

		if ( '' === $normalized['action_type'] ) {
			return new \WP_Error( 'baf_ai_handoff_action_invalid', __( 'Choose a supported local handoff action type.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		if ( true !== $normalized['approved'] ) {
			return new \WP_Error( 'baf_ai_handoff_approval_required', __( 'Confirm approval before preparing a Travelpayouts handoff.', 'bookings-flights-core' ), array( 'status' => 400 ) );
		}

		if ( true !== $normalized['provider_request_consent'] ) {
			return new \WP_Error( 'baf_ai_handoff_request_consent_required', __( 'Confirm provider request consent before preparing this handoff.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$consent = Settings_Manager::get_consent();

		if ( true !== (bool) $consent['allow_provider_requests'] ) {
			return new \WP_Error( 'baf_ai_handoff_consent_required', __( 'Provider request consent must be enabled before Travelpayouts handoffs can be prepared.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$post = get_post( $normalized['trip_plan_id'] );

		if ( ! $post instanceof \WP_Post || Post_Type_Registrar::TRIP_PLAN !== $post->post_type ) {
			return new \WP_Error( 'baf_ai_handoff_trip_plan_not_found', __( 'The source Trip Plan draft was not found.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $this->forbidden_error();
		}

		$itinerary = $this->load_itinerary( $post->ID );

		if ( is_wp_error( $itinerary ) ) {
			return $itinerary;
		}

		if ( ! isset( $itinerary['affiliate_opportunities'][ $normalized['opportunity_index'] ] ) ) {
			return new \WP_Error( 'baf_ai_handoff_opportunity_not_found', __( 'The requested AI opportunity was not found on this Trip Plan.', 'bookings-flights-core' ), array( 'status' => 404 ) );
		}

		$opportunity = $itinerary['affiliate_opportunities'][ $normalized['opportunity_index'] ];
		$guard       = $this->validate_opportunity( $opportunity );

		if ( is_wp_error( $guard ) ) {
			return $guard;
		}

		$intent  = $this->build_intent( $post->ID, $normalized, $opportunity, $itinerary );
		$intents = $this->upsert_intent( $post->ID, $intent );

		if ( is_wp_error( $intents ) ) {
			return $intents;
		}

		do_action( 'baf_ai_handoff_prepared', $post->ID, $intent );

		return array(
			'status'              => 'prepared',
			'trip_plan_id'        => $post->ID,
			'intent'              => $intent,
			'provider_action'     => 'not_executed',
			'external_data_sent'  => false,
			'stored_intent_count' => count( $intents ),
		);
	}

	private function normalize_request( array $request ): array {
		$action_type = sanitize_key( $this->string_value( $request['action_type'] ?? 'placement_card' ) );

		return array(
			'trip_plan_id'             => absint( $this->string_value( $request['trip_plan_id'] ?? 0 ) ),
			'opportunity_index'        => max( 0, absint( $this->string_value( $request['opportunity_index'] ?? 0 ) ) ),
			'action_type'              => in_array( $action_type, self::ACTION_TYPES, true ) ? $action_type : '',
			'approved'                 => $this->boolean_value( $request['approved'] ?? false ),
			'provider_request_consent' => $this->boolean_value( $request['provider_request_consent'] ?? false ),
			'approval_note'            => substr( sanitize_textarea_field( $this->string_value( $request['approval_note'] ?? '' ) ), 0, 500 ),
		);
	}

	private function load_itinerary( int $post_id ): array|\WP_Error {
		$stored = get_post_meta( $post_id, 'baf_itinerary_json', true );

		if ( '' === (string) $stored ) {
			return new \WP_Error( 'baf_ai_handoff_itinerary_missing', __( 'This Trip Plan does not include a validated itinerary payload.', 'bookings-flights-core' ), array( 'status' => 409 ) );
		}

		$decoded = json_decode( (string) $stored, true );

		if ( ! is_array( $decoded ) ) {
			return new \WP_Error( 'baf_ai_handoff_itinerary_invalid', __( 'This Trip Plan itinerary payload is not valid JSON.', 'bookings-flights-core' ), array( 'status' => 409 ) );
		}

		$validated = Itinerary_Schema::validate( $decoded );

		if ( is_wp_error( $validated ) ) {
			return $validated;
		}

		return $validated;
	}

	private function validate_opportunity( array $opportunity ): true|\WP_Error {
		if ( 'travelpayouts' !== (string) ( $opportunity['provider'] ?? '' ) || 'not_executed' !== (string) ( $opportunity['status'] ?? '' ) ) {
			return new \WP_Error( 'baf_ai_handoff_not_allowed', __( 'Only not-executed Travelpayouts opportunities can be prepared for handoff.', 'bookings-flights-core' ), array( 'status' => 409 ) );
		}

		if ( true !== (bool) ( $opportunity['requires_approval'] ?? false ) || true !== (bool) ( $opportunity['disclosure_required'] ?? false ) ) {
			return new \WP_Error( 'baf_ai_handoff_missing_guardrails', __( 'This opportunity is missing required approval or disclosure guardrails.', 'bookings-flights-core' ), array( 'status' => 409 ) );
		}

		return true;
	}

	private function build_intent( int $post_id, array $request, array $opportunity, array $itinerary ): array {
		$intent_id = 'aih_' . substr( wp_hash( $post_id . ':' . $request['opportunity_index'] . ':' . $request['action_type'] ), 0, 16 );

		return array(
			'id'                       => $intent_id,
			'schema_version'           => 'ai_handoff_intent_v1',
			'source_schema'            => (string) ( $itinerary['opportunity_schema'] ?? '' ),
			'trip_plan_id'             => $post_id,
			'opportunity_index'        => $request['opportunity_index'],
			'action_type'              => $request['action_type'],
			'action_label'             => $this->action_label( $request['action_type'] ),
			'status'                   => 'draft_approved',
			'provider'                 => 'travelpayouts',
			'provider_action'          => 'not_executed',
			'provider_action_executed' => false,
			'external_request_sent'    => false,
			'vertical'                 => sanitize_key( (string) $opportunity['vertical'] ),
			'recommendation_type'      => sanitize_key( (string) $opportunity['recommendation_type'] ),
			'label'                    => sanitize_text_field( (string) $opportunity['label'] ),
			'placement_context'        => sanitize_text_field( (string) $opportunity['placement_context'] ),
			'destination'              => sanitize_text_field( (string) $opportunity['destination'] ),
			'route'                    => sanitize_text_field( (string) $opportunity['route'] ),
			'suggested_subid'          => sanitize_key( (string) $opportunity['suggested_subid'] ),
			'confidence'               => sanitize_key( (string) $opportunity['confidence'] ),
			'disclosure_required'      => true,
			'approval_state'           => 'approved_local_handoff',
			'approved_by'              => get_current_user_id(),
			'approved_at'              => wp_date( DATE_ATOM ),
			'approval_note'            => $request['approval_note'],
			'blocked_actions'          => array( 'book', 'pay', 'publish', 'execute_provider_search', 'create_live_link', 'claim_price_or_availability' ),
		);
	}

	private function upsert_intent( int $post_id, array $intent ): array|\WP_Error {
		$existing = $this->stored_intents( $post_id );
		$replaced = false;

		foreach ( $existing as $index => $stored ) {
			if ( ! is_array( $stored ) || (string) ( $stored['id'] ?? '' ) !== $intent['id'] ) {
				continue;
			}

			$existing[ $index ] = $intent;
			$replaced           = true;
			break;
		}

		if ( false === $replaced ) {
			$existing[] = $intent;
		}

		$encoded = wp_json_encode( $existing );

		if ( false === $encoded ) {
			return new \WP_Error( 'baf_ai_handoff_save_failed', __( 'The approved handoff intent could not be serialized.', 'bookings-flights-core' ), array( 'status' => 500 ) );
		}

		update_post_meta( $post_id, self::META_KEY, $encoded );

		return $existing;
	}

	private function stored_intents( int $post_id ): array {
		$stored = get_post_meta( $post_id, self::META_KEY, true );

		if ( '' === (string) $stored ) {
			return array();
		}

		$decoded = json_decode( (string) $stored, true );

		return is_array( $decoded ) ? array_values( array_filter( $decoded, 'is_array' ) ) : array();
	}

	private function action_label( string $action_type ): string {
		return match ( $action_type ) {
			'placement_draft' => __( 'Placement draft', 'bookings-flights-core' ),
			'saved_trip_cta'  => __( 'Saved-trip CTA', 'bookings-flights-core' ),
			'alert_cta'       => __( 'Alert CTA', 'bookings-flights-core' ),
			default           => __( 'Travelpayouts card', 'bookings-flights-core' ),
		};
	}

	private function forbidden_error(): \WP_Error {
		return new \WP_Error( 'baf_ai_handoff_forbidden', __( 'You are not allowed to prepare AI handoffs.', 'bookings-flights-core' ), array( 'status' => rest_authorization_required_code() ) );
	}

	private function boolean_value( mixed $value ): bool {
		if ( is_bool( $value ) || is_scalar( $value ) ) {
			return rest_sanitize_boolean( $value );
		}

		return false;
	}

	private function string_value( mixed $value ): string {
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
}
