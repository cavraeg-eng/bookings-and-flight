<?php
/**
 * Affiliate click handoff REST controller.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

use BAF\Core\Services\Affiliate_Link_Service;
use BAF\Core\Services\Click_Tracking_Service;

defined( 'ABSPATH' ) || exit;

final class Affiliate_Click_Controller extends Base_Controller {

	private Affiliate_Link_Service $affiliate_links;

	private Click_Tracking_Service $click_tracking;

	public function __construct( ?Affiliate_Link_Service $affiliate_links = null, ?Click_Tracking_Service $click_tracking = null ) {
		parent::__construct();

		$this->rest_base      = 'affiliate/click';
		$this->affiliate_links = $affiliate_links ?? new Affiliate_Link_Service();
		$this->click_tracking  = $click_tracking ?? new Click_Tracking_Service();
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'handle_click' ),
					'permission_callback' => array( $this, 'handle_click_permissions_check' ),
					'args'                => $this->get_endpoint_args(),
				),
			)
		);
	}

	public function handle_click_permissions_check( $request ) {
		return Permissions::can_read_public_collection( $request );
	}

	public function handle_click( $request ) {
		$event = $this->affiliate_links->validate_handoff( $request->get_params() );

		if ( is_wp_error( $event ) ) {
			return $event;
		}

		$tracked = $this->click_tracking->track( $event );

		if ( is_wp_error( $tracked ) ) {
			return $tracked;
		}

		$response = new \WP_REST_Response( null, 302 );
		$response->header( 'Location', $event['target_url'] );
		$response->header( 'Cache-Control', 'no-store' );

		return $response;
	}

	private function get_endpoint_args(): array {
		return array(
			'post_id'  => array(
				'type'              => 'integer',
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'validate_callback' => array( $this, 'validate_non_negative_integer' ),
			),
			'provider' => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => array( $this, 'validate_non_empty_scalar' ),
			),
			'subid'    => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( $this, 'validate_non_empty_scalar' ),
			),
			'target'   => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( $this, 'validate_non_empty_scalar' ),
			),
			'expires'  => array(
				'type'              => 'integer',
				'required'          => true,
				'sanitize_callback' => 'absint',
				'validate_callback' => array( $this, 'validate_positive_integer' ),
			),
			'sig'      => array(
				'type'              => 'string',
				'required'          => true,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( $this, 'validate_non_empty_scalar' ),
			),
		);
	}

	public function validate_non_negative_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value >= 0;
	}

	public function validate_positive_integer( mixed $value ): bool {
		return is_numeric( $value ) && (int) $value > 0;
	}

	public function validate_non_empty_scalar( mixed $value ): bool {
		return is_scalar( $value ) && '' !== trim( (string) $value );
	}
}