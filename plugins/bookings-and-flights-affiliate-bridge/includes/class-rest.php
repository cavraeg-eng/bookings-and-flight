<?php
/**
 * REST routes — /wp-json/baf/v1/*
 * - config: server-safe read (no secrets) used by Next.js during SSR.
 * - status: quick search-api reachability check for the admin UI.
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

namespace BAF\AffiliateBridge;

defined( 'ABSPATH' ) || exit;

class REST {

	public const NAMESPACE = 'baf/v1';

	public static function bootstrap(): void {
		add_action( 'rest_api_init', array( self::class, 'register_routes' ) );
	}

	public static function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/config',
			array(
				'methods'             => 'GET',
				'permission_callback' => array( self::class, 'current_user_can_manage' ),
				'callback'            => array( self::class, 'get_config' ),
			)
		);
		register_rest_route(
			self::NAMESPACE,
			'/status',
			array(
				'methods'             => 'GET',
				'permission_callback' => array( self::class, 'current_user_can_manage' ),
				'callback'            => array( self::class, 'get_status' ),
			)
		);
	}

	public static function current_user_can_manage(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Public config consumed by the Next.js app. NEVER returns API keys —
	 * only which suppliers are enabled and the search-api URL.
	 */
	public static function get_config( \WP_REST_Request $req ): \WP_REST_Response {
		$creds     = (array) get_option( BAF_OPT_SUPPLIER_CREDS, array() );
		$enabled   = array();
		$suppliers = Settings::suppliers();
		foreach ( $suppliers as $id => $def ) {
			$entry = $creds[ $id ] ?? array();
			$required_fields = array_keys( $def['fields'] );
			$has_all = ! empty( $required_fields ) && array_reduce(
				$required_fields,
				static fn( $acc, $f ) => $acc && ! empty( $entry[ $f ] ),
				true
			);
			if ( $has_all ) {
				$enabled[] = $id;
			}
		}
		return new \WP_REST_Response(
			array(
				'search_api_url'     => (string) get_option( BAF_OPT_SEARCH_API_URL, '' ),
				'enabled_suppliers'  => $enabled,
				'available_suppliers'=> array_keys( $suppliers ),
			),
			200
		);
	}

	public static function get_status( \WP_REST_Request $req ): \WP_REST_Response {
		$url = rtrim( (string) get_option( BAF_OPT_SEARCH_API_URL, '' ), '/' );
		if ( ! $url ) {
			return new \WP_REST_Response( array( 'reachable' => false, 'error' => 'not_configured' ), 200 );
		}
		$res = wp_remote_get( $url . '/health', array( 'timeout' => 3 ) );
		if ( is_wp_error( $res ) ) {
			return new \WP_REST_Response( array( 'reachable' => false, 'error' => $res->get_error_message() ), 200 );
		}
		$code = (int) wp_remote_retrieve_response_code( $res );
		return new \WP_REST_Response( array( 'reachable' => $code === 200, 'status_code' => $code ), 200 );
	}
}
