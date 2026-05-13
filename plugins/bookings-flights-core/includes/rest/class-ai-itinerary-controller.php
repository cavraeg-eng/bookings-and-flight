<?php
/**
 * AI itinerary REST controller.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Services\AI_Itinerary_Service;

defined( 'ABSPATH' ) || exit;

final class AI_Itinerary_Controller extends Base_Controller {

	private AI_Itinerary_Service $service;

	public function __construct( ?AI_Itinerary_Service $service = null ) {
		parent::__construct();

		$this->rest_base = 'ai/itinerary';
		$this->service   = $service ?? new AI_Itinerary_Service();
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
		return Permissions::require_capability( Capability_Manager::RUN_AI );
	}

	public function create_item( $request ) {
		$result = $this->service->generate( $request->get_params() );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 201 );
	}

	private function get_endpoint_args(): array {
		return array(
			'destination'    => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_required_text' ),
			),
			'origin'         => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
			'prompt'         => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_textarea_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
			'departure_date' => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_date' ),
			),
			'return_date'    => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_date' ),
			),
			'days'           => array(
				'type'              => 'integer',
				'default'           => 3,
				'minimum'           => 1,
				'maximum'           => 21,
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_positive_integer' ),
				'validate_callback' => array( $this, 'validate_days' ),
			),
			'travelers'      => array(
				'type'              => 'integer',
				'default'           => 2,
				'minimum'           => 1,
				'maximum'           => 12,
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_positive_integer' ),
				'validate_callback' => array( $this, 'validate_travelers' ),
			),
			'travel_style'   => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
			'budget'         => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
			'preferences'    => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_textarea_arg' ),
				'validate_callback' => array( $this, 'validate_optional_scalar' ),
			),
			'source_post_id' => array(
				'type'              => 'integer',
				'default'           => 0,
				'sanitize_callback' => array( $this, 'sanitize_absint_arg' ),
				'validate_callback' => array( $this, 'validate_non_negative_integer' ),
			),
			'save'           => array(
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => array( $this, 'sanitize_boolean_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'external_ai_consent' => array(
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => array( $this, 'sanitize_boolean_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	public function sanitize_text_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}

	public function sanitize_textarea_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';
	}

	public function sanitize_absint_arg( mixed $value ): int {
		return is_scalar( $value ) ? absint( $value ) : 0;
	}

	public function sanitize_boolean_arg( mixed $value ): bool {
		return is_bool( $value ) || is_scalar( $value ) ? rest_sanitize_boolean( $value ) : false;
	}

	public function validate_required_text( mixed $value ): bool {
		return is_scalar( $value ) && '' !== trim( (string) $value ) && strlen( (string) $value ) <= 120;
	}

	public function validate_optional_scalar( mixed $value ): bool {
		return null === $value || ( is_scalar( $value ) && strlen( (string) $value ) <= 1200 );
	}

	public function validate_days( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 21;
	}

	public function validate_travelers( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 12;
	}

	public function validate_optional_date( mixed $value ): bool {
		if ( null === $value || '' === $value ) {
			return true;
		}

		if ( ! is_scalar( $value ) || 1 !== preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ) ) {
			return false;
		}

		$parts = array_map( 'absint', explode( '-', (string) $value ) );

		return 3 === count( $parts ) && checkdate( $parts[1], $parts[2], $parts[0] );
	}

	public function validate_non_negative_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 0;
	}
}
