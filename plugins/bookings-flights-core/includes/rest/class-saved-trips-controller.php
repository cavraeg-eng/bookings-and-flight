<?php
/**
 * Saved trip REST controller.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Services\Saved_Trip_Service;

defined( 'ABSPATH' ) || exit;

final class Saved_Trips_Controller extends Base_Controller {

	private Saved_Trip_Service $service;

	public function __construct( ?Saved_Trip_Service $service = null ) {
		parent::__construct();

		$this->rest_base = 'saved-trips';
		$this->service   = $service ?? new Saved_Trip_Service();
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'private_item_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'private_item_permissions_check' ),
					'args'                => $this->get_endpoint_args(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description'       => __( 'Saved trip intent ID.', 'bookings-flights-core' ),
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => array( $this, 'sanitize_absint_arg' ),
						'validate_callback' => array( $this, 'validate_positive_integer' ),
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'private_item_permissions_check' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'private_item_permissions_check' ),
					'args'                => $this->get_endpoint_args( false ),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'private_item_permissions_check' ),
				),
			)
		);
	}

	public function private_item_permissions_check( $request ) {
		$capability = Permissions::require_capability( 'read' );

		if ( is_wp_error( $capability ) ) {
			return $capability;
		}

		$nonce = $request instanceof \WP_REST_Request ? (string) $request->get_header( 'x_wp_nonce' ) : '';

		if ( '' === $nonce || false === wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error( 'baf_saved_trip_nonce_required', __( 'A valid WordPress REST nonce is required for saved trip intent.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		return true;
	}

	public function get_items( $request ) {
		$result = $this->service->list_for_current_user( (int) $request['page'], (int) $request['per_page'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$response = new \WP_REST_Response( $result['items'], 200 );
		$response->header( 'X-WP-Total', (int) $result['total'] );
		$response->header( 'X-WP-TotalPages', (int) $result['total_pages'] );

		return $response;
	}

	public function get_item( $request ) {
		$result = $this->service->get_for_current_user( (int) $request['id'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 200 );
	}

	public function create_item( $request ) {
		$result = $this->service->save_for_current_user( $request->get_params() );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 201 );
	}

	public function update_item( $request ) {
		$result = $this->service->update_for_current_user( $this->request_payload_params( $request ), (int) $request['id'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 200 );
	}

	public function delete_item( $request ) {
		$result = $this->service->delete_for_current_user( (int) $request['id'] );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response( $result, 200 );
	}

	private function get_endpoint_args( bool $is_create = true ): array {
		return array(
			'destination'           => array(
				'type'              => 'string',
				'required'          => $is_create,
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_required_text' ),
			),
			'origin'                => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_text' ),
			),
			'departure_date'        => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_date' ),
			),
			'return_date'           => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_text_arg' ),
				'validate_callback' => array( $this, 'validate_optional_date' ),
			),
			'travelers'             => array(
				'type'              => 'integer',
				'default'           => 2,
				'minimum'           => 1,
				'maximum'           => 12,
				'sanitize_callback' => array( Request_Parameters::class, 'sanitize_positive_integer' ),
				'validate_callback' => array( $this, 'validate_travelers' ),
			),
			'travel_style'          => array(
				'type'              => 'string',
				'default'           => 'balanced',
				'enum'              => array( 'balanced', 'food_culture', 'family', 'budget', 'luxury', 'outdoors' ),
				'sanitize_callback' => array( $this, 'sanitize_key_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'placement_key'         => array(
				'type'              => 'string',
				'default'           => 'flights_white_label_search',
				'enum'              => array( 'flights_white_label_search', 'hotels_partner_search' ),
				'sanitize_callback' => array( $this, 'sanitize_key_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
			'note'                  => array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_textarea_arg' ),
				'validate_callback' => array( $this, 'validate_note' ),
			),
			'local_storage_consent' => array(
				'type'              => 'boolean',
				'required'          => true,
				'sanitize_callback' => array( $this, 'sanitize_boolean_arg' ),
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	public function sanitize_absint_arg( mixed $value ): int {
		return is_scalar( $value ) ? absint( $value ) : 0;
	}

	public function sanitize_boolean_arg( mixed $value ): bool {
		return is_bool( $value ) || is_scalar( $value ) ? rest_sanitize_boolean( $value ) : false;
	}

	public function sanitize_key_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
	}

	public function sanitize_text_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
	}

	public function sanitize_textarea_arg( mixed $value ): string {
		return is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';
	}

	public function validate_note( mixed $value ): bool {
		return null === $value || ( is_scalar( $value ) && strlen( (string) $value ) <= 500 );
	}

	public function validate_optional_text( mixed $value ): bool {
		return null === $value || ( is_scalar( $value ) && strlen( (string) $value ) <= 120 );
	}

	public function validate_required_text( mixed $value ): bool {
		return is_scalar( $value ) && '' !== trim( (string) $value ) && strlen( (string) $value ) <= 120;
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

	public function validate_positive_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value > 0;
	}

	public function validate_travelers( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 12;
	}

	private function request_payload_params( \WP_REST_Request $request ): array {
		$json_params = $request->get_json_params();
		$body_params = $request->get_body_params();
		$params      = array();

		if ( is_array( $body_params ) ) {
			$params = array_merge( $params, $body_params );
		}

		if ( is_array( $json_params ) ) {
			$params = array_merge( $params, $json_params );
		}

		if ( array() === $params ) {
			$params = $request->get_params();
		}

		unset( $params['id'] );

		return $params;
	}
}
