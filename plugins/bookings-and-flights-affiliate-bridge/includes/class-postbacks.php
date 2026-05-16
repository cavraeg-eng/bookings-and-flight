<?php
/**
 * Supplier postback ingestion.
 *
 * Suppliers call our public endpoint /wp-json/baf/v1/postback when a booking
 * completes. We validate a shared secret (either a query param or header),
 * then forward to the Node search-api so the click gets marked converted.
 *
 * Suppliers differ wildly in what they POST; adapters live as closures in
 * $map below. Each one returns a normalized {clickId, value, currency} tuple
 * or null to reject.
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

namespace BAF\AffiliateBridge;

defined( 'ABSPATH' ) || exit;

class Postbacks {

	public const NAMESPACE = 'baf/v1';

	public static function bootstrap(): void {
		add_action( 'rest_api_init', array( self::class, 'register_routes' ) );
	}

	public static function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/postback',
			array(
				'methods'             => array( 'GET', 'POST' ),
				'permission_callback' => '__return_true', // auth is secret-based
				'callback'            => array( self::class, 'handle' ),
			)
		);
	}

	public static function handle( \WP_REST_Request $req ): \WP_REST_Response {
		$secret_param = $req->get_param( 'secret' );
		$secret_hdr   = $req->get_header( 'x-baf-secret' );
		$stored       = (string) get_option( BAF_OPT_POSTBACK_SECRET, '' );

		$provided = is_string( $secret_hdr ) && $secret_hdr !== ''
			? $secret_hdr
			: (string) ( $secret_param ?? '' );

		if ( '' === $stored || ! hash_equals( $stored, $provided ) ) {
			return new \WP_REST_Response( array( 'error' => 'forbidden' ), 403 );
		}

		$supplier = sanitize_key( (string) ( $req->get_param( 'supplier' ) ?? '' ) );
		$map      = self::adapters();

		if ( ! isset( $map[ $supplier ] ) ) {
			return new \WP_REST_Response( array( 'error' => 'unknown_supplier' ), 400 );
		}

		$params = array_merge( $req->get_query_params(), (array) $req->get_json_params(), (array) $req->get_body_params() );
		$normalized = $map[ $supplier ]( $params );

		if ( ! $normalized ) {
			return new \WP_REST_Response( array( 'error' => 'invalid_payload' ), 400 );
		}

		// Forward to Node search-api.
		$api_url = rtrim( (string) get_option( BAF_OPT_SEARCH_API_URL, '' ), '/' );
		if ( ! $api_url ) {
			return new \WP_REST_Response( array( 'error' => 'api_not_configured' ), 500 );
		}

		$res = wp_remote_post(
			$api_url . '/postbacks/supplier-conversion',
			array(
				'timeout'  => 5,
				'headers'  => array(
					'content-type'      => 'application/json',
					'x-postback-secret' => $stored,
				),
				'body'     => wp_json_encode( $normalized ),
			)
		);

		if ( is_wp_error( $res ) ) {
			return new \WP_REST_Response(
				array( 'error' => 'forward_failed', 'detail' => $res->get_error_message() ),
				502
			);
		}

		$status = (int) wp_remote_retrieve_response_code( $res );

		if ( 200 > $status || 300 <= $status ) {
			return new \WP_REST_Response(
				array( 'error' => 'forward_rejected', 'api_status' => $status ),
				502
			);
		}

		return new \WP_REST_Response(
			array( 'ok' => true, 'api_status' => $status ),
			200
		);
	}

	/**
	 * Supplier-specific payload normalizers.
	 * Return shape: ['clickId' => string, 'value' => float, 'currency' => 'USD']
	 * Or null to reject.
	 */
	private static function adapters(): array {
		return array(
			'travelpayouts' => static function ( array $p ): ?array {
				$click_id = (string) ( $p['sub_id'] ?? $p['clickId'] ?? '' );
				$value    = (float) ( $p['payout'] ?? $p['value'] ?? 0.0 );
				$currency = strtoupper( (string) ( $p['currency'] ?? 'USD' ) );
				if ( $click_id === '' ) {
					return null;
				}
				return compact( 'click_id', 'value', 'currency' ) + array(
					'clickId'  => $click_id,
					'value'    => $value,
					'currency' => $currency,
				);
			},
			'booking'       => static function ( array $p ): ?array {
				$click_id = (string) ( $p['label'] ?? $p['clickId'] ?? '' );
				$value    = (float) ( $p['commission'] ?? 0.0 );
				$currency = strtoupper( (string) ( $p['currency'] ?? 'USD' ) );
				if ( $click_id === '' ) {
					return null;
				}
				return array(
					'clickId'  => $click_id,
					'value'    => $value,
					'currency' => $currency,
				);
			},
			'viator'        => static function ( array $p ): ?array {
				$click_id = (string) ( $p['mcid'] ?? $p['clickId'] ?? '' );
				$value    = (float) ( $p['amount'] ?? 0.0 );
				$currency = strtoupper( (string) ( $p['currency'] ?? 'USD' ) );
				if ( $click_id === '' ) {
					return null;
				}
				return array(
					'clickId'  => $click_id,
					'value'    => $value,
					'currency' => $currency,
				);
			},
			'discovercars'  => static function ( array $p ): ?array {
				$click_id = (string) ( $p['click_id'] ?? $p['sub1'] ?? '' );
				$value    = (float) ( $p['commission'] ?? 0.0 );
				$currency = strtoupper( (string) ( $p['currency'] ?? 'EUR' ) );
				if ( $click_id === '' ) {
					return null;
				}
				return array(
					'clickId'  => $click_id,
					'value'    => $value,
					'currency' => $currency,
				);
			},
			'kiwi'          => static function ( array $p ): ?array {
				$click_id = (string) ( $p['sub_id'] ?? $p['clickId'] ?? '' );
				$value    = (float) ( $p['commission'] ?? 0.0 );
				$currency = strtoupper( (string) ( $p['currency'] ?? 'EUR' ) );
				if ( $click_id === '' ) {
					return null;
				}
				return array(
					'clickId'  => $click_id,
					'value'    => $value,
					'currency' => $currency,
				);
			},
		);
	}
}
