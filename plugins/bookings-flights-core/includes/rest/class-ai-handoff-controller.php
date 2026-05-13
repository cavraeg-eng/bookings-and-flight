<?php
/**
 * AI opportunity handoff REST controller.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Services\AI_Opportunity_Handoff_Service;

defined( 'ABSPATH' ) || exit;

final class AI_Handoff_Controller extends Base_Controller {

	private AI_Opportunity_Handoff_Service $service;

	public function __construct( ?AI_Opportunity_Handoff_Service $service = null ) {
		parent::__construct();

		$this->rest_base = 'ai/handoff';
		$this->service   = $service ?? new AI_Opportunity_Handoff_Service();
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args(),
				),
			)
		);
	}

	public function create_item_permissions_check( $request ) {
		$capability = Permissions::require_capability( Capability_Manager::EDIT_CONTENT );

		if ( is_wp_error( $capability ) ) {
			return $capability;
		}

		$nonce = $request instanceof \WP_REST_Request ? (string) $request->get_header( 'x_wp_nonce' ) : '';

		if ( '' === $nonce || false === wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error( 'baf_ai_handoff_nonce_required', __( 'A valid WordPress REST nonce is required to prepare this handoff.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return true;
	}

	public function create_item( $request ) {
		$result = $this->service->prepare( $request->get_params() );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 201 );
	}

	private function get_endpoint_args(): array {
		return array(
			'trip_plan_id'             => array(
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_absint_arg' ),
				'validate_callback' => array( $this, 'validate_positive_integer' ),
			),
			'opportunity_index'        => array(
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_absint_arg' ),
				'validate_callback' => array( $this, 'validate_non_negative_integer' ),
			),
			'action_type'              => array(
				'type'              => 'string',
				'default'           => 'placement_card',
				'enum'              => array( 'placement_card', 'placement_draft', 'saved_trip_cta', 'alert_cta' ),
				'sanitize_callback' => array( $this, 'sanitize_key_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'approved'                 => array(
				'type'              => 'boolean',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_boolean_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'provider_request_consent' => array(
				'type'              => 'boolean',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_boolean_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'approval_note'            => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_textarea_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
		);
	}

	public function sanitize_absint_arg( mixed $value ): int {
		return is_scalar( $value ) ? absint( $value ) : 0;
	}

	public function sanitize_key_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
	}

	public function sanitize_boolean_arg( mixed $value ): bool {
		return is_bool( $value ) || is_scalar( $value ) ? rest_sanitize_boolean( $value ) : false;
	}

	public function sanitize_textarea_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';
	}

	public function validate_positive_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value > 0;
	}

	public function validate_non_negative_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 0;
	}

	public function validate_optional_scalar( mixed $value ): bool {
		return null === $value || ( is_scalar( $value ) && strlen( (string) $value ) <= 500 );
	}
}
