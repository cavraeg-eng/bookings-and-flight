<?php
/**
 * Settings registration — single source of truth for all option schemas
 * and the supplier catalog.
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

namespace BAF\AffiliateBridge;

defined( 'ABSPATH' ) || exit;

class Settings {
	private static bool $credentials_sync_queued = false;

	/** Supplier catalog. Keys must match the monorepo's SupplierId union. */
	public static function suppliers(): array {
		return array(
			'travelpayouts' => array(
				'label'  => 'Travelpayouts',
				'blurb'  => 'Flights + hotels meta-aggregator. Provides Aviasales, Hotellook, and many other affiliate links under one account.',
				'fields' => array(
					'api_token' => 'API Token',
					'marker'    => 'Affiliate Marker',
				),
				'apply'  => 'https://www.travelpayouts.com/en',
			),
			'booking'       => array(
				'label'  => 'Booking.com Affiliate',
				'blurb'  => 'Hotels. Largest inventory globally. Add API credentials when Booking Demand API access is approved.',
				'fields' => array(
					'affiliate_id' => 'Affiliate ID',
					'api_token'    => 'API Token',
				),
				'apply'  => 'https://www.booking.com/affiliate-program/v2/index.html',
			),
			'viator'        => array(
				'label'  => 'Viator (TripAdvisor)',
				'blurb'  => 'Tours and experiences. Partner API + deeplinks.',
				'fields' => array(
					'api_key'    => 'API Key',
					'partner_id' => 'Partner ID',
				),
				'apply'  => 'https://www.viator.com/affiliate',
			),
			'discovercars'  => array(
				'label'  => 'DiscoverCars',
				'blurb'  => 'Car rentals aggregator. Deeplink + postback.',
				'fields' => array(
					'partner_id' => 'Partner ID',
				),
				'apply'  => 'https://www.discovercars.com/partners',
			),
			'kiwi'          => array(
				'label'  => 'Kiwi.com',
				'blurb'  => 'Flights with smart virtual-interline routing.',
				'fields' => array(
					'affiliate_id' => 'Affiliate ID',
				),
				'apply'  => 'https://affiliates.kiwi.com/',
			),
		);
	}

	public static function bootstrap(): void {
		add_action( 'admin_init', array( self::class, 'ensure_bridge_secrets' ), 0 );
		add_action( 'admin_init', array( self::class, 'register' ) );
		add_action( 'added_option', array( self::class, 'maybe_queue_credentials_sync' ), 10, 2 );
		add_action( 'updated_option', array( self::class, 'maybe_queue_credentials_sync' ), 10, 3 );
	}

	public static function ensure_bridge_secrets(): void {
		if ( ! get_option( BAF_OPT_POSTBACK_SECRET ) ) {
			update_option( BAF_OPT_POSTBACK_SECRET, \baf_affiliate_bridge_default_secret( BAF_OPT_POSTBACK_SECRET ) );
		}

		if ( ! get_option( BAF_OPT_CREDENTIAL_SYNC_SECRET ) ) {
			update_option( BAF_OPT_CREDENTIAL_SYNC_SECRET, \baf_affiliate_bridge_default_secret( BAF_OPT_CREDENTIAL_SYNC_SECRET ) );
		}
	}

	public static function register(): void {
		register_setting(
			'baf_affiliate_bridge',
			BAF_OPT_SUPPLIER_CREDS,
			array(
				'type'              => 'object',
				'sanitize_callback' => array( self::class, 'sanitize_creds' ),
				'default'           => array(),
			)
		);
		register_setting(
			'baf_affiliate_bridge',
			BAF_OPT_SEARCH_API_URL,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'default'           => 'http://localhost:4050',
			)
		);
		register_setting(
			'baf_affiliate_bridge',
			BAF_OPT_POSTBACK_SECRET,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( self::class, 'sanitize_secret' ),
				'default'           => '',
			)
		);
		register_setting(
			'baf_affiliate_bridge',
			BAF_OPT_CREDENTIAL_SYNC_SECRET,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( self::class, 'sanitize_credential_sync_secret' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Strips unknown suppliers/fields. Never trusts client keys.
	 */
	public static function sanitize_creds( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$clean    = array();
		$suppliers = self::suppliers();

		foreach ( $suppliers as $supplier_id => $def ) {
			if ( empty( $value[ $supplier_id ] ) || ! is_array( $value[ $supplier_id ] ) ) {
				continue;
			}
			$clean[ $supplier_id ] = array();
			foreach ( array_keys( $def['fields'] ) as $field ) {
				if ( isset( $value[ $supplier_id ][ $field ] ) ) {
					$field_value = sanitize_text_field( (string) $value[ $supplier_id ][ $field ] );

					if ( '' === $field_value && self::is_secret_field( $field ) ) {
						$existing = (array) get_option( BAF_OPT_SUPPLIER_CREDS, array() );

						if ( isset( $existing[ $supplier_id ][ $field ] ) ) {
							$field_value = sanitize_text_field( (string) $existing[ $supplier_id ][ $field ] );
						}
					}

					$clean[ $supplier_id ][ $field ] = $field_value;
				}
			}
		}
		return $clean;
	}

	public static function is_secret_field( string $field ): bool {
		return 1 === preg_match( '/token|key|secret/i', $field );
	}

	public static function sanitize_secret( $value ): string {
		$v = is_string( $value ) ? trim( $value ) : '';
		// Accept only printable ASCII, 16-128 chars.
		if ( ! preg_match( '/^[\x21-\x7e]{16,128}$/', $v ) ) {
			return (string) get_option( BAF_OPT_POSTBACK_SECRET, '' );
		}
		return $v;
	}

	public static function sanitize_credential_sync_secret( $value ): string {
		$v = is_string( $value ) ? trim( $value ) : '';
		// Accept only printable ASCII, 16-128 chars.
		if ( ! preg_match( '/^[\x21-\x7e]{16,128}$/', $v ) ) {
			return (string) get_option( BAF_OPT_CREDENTIAL_SYNC_SECRET, '' );
		}
		return $v;
	}

	public static function maybe_queue_credentials_sync( string $option_name, ...$unused ): void {
		unset( $unused );

		if ( ! in_array( $option_name, array( BAF_OPT_SUPPLIER_CREDS, BAF_OPT_SEARCH_API_URL, BAF_OPT_CREDENTIAL_SYNC_SECRET ), true ) ) {
			return;
		}

		if ( true === self::$credentials_sync_queued ) {
			return;
		}

		self::$credentials_sync_queued = true;
		add_action( 'shutdown', array( self::class, 'sync_credentials_to_search_api' ) );
	}

	public static function sync_credentials_to_search_api(): array {
		$api_url = rtrim( (string) get_option( BAF_OPT_SEARCH_API_URL, '' ), '/' );
		$secret  = (string) get_option( BAF_OPT_CREDENTIAL_SYNC_SECRET, '' );

		if ( '' === $api_url || '' === $secret ) {
			return array(
				'ok'    => false,
				'error' => 'not_configured',
			);
		}

		$res = wp_remote_post(
			$api_url . '/integrations/credentials',
			array(
				'timeout' => 5,
				'headers' => array(
					'content-type'              => 'application/json',
					'x-credential-sync-secret'  => $secret,
				),
				'body'    => wp_json_encode( self::search_api_credentials_payload() ),
			)
		);

		if ( is_wp_error( $res ) ) {
			return array(
				'ok'    => false,
				'error' => 'sync_failed',
			);
		}

		$status = (int) wp_remote_retrieve_response_code( $res );

		return array(
			'ok'          => 200 <= $status && 300 > $status,
			'status_code' => $status,
		);
	}

	private static function search_api_credentials_payload(): array {
		$creds = (array) get_option( BAF_OPT_SUPPLIER_CREDS, array() );

		return array(
			'travelpayouts' => array(
				'token'  => (string) ( $creds['travelpayouts']['api_token'] ?? '' ),
				'marker' => (string) ( $creds['travelpayouts']['marker'] ?? '' ),
			),
			'booking'       => array(
				'affiliateId' => (string) ( $creds['booking']['affiliate_id'] ?? '' ),
				'apiToken'    => (string) ( $creds['booking']['api_token'] ?? '' ),
				'useSandbox'  => false,
			),
			'viator'        => array(
				'apiKey'    => (string) ( $creds['viator']['api_key'] ?? '' ),
				'partnerId' => (string) ( $creds['viator']['partner_id'] ?? '' ),
			),
			'discovercars'  => array(
				'partnerId' => (string) ( $creds['discovercars']['partner_id'] ?? '' ),
			),
			'kiwi'          => array(
				'affiliateId' => (string) ( $creds['kiwi']['affiliate_id'] ?? '' ),
			),
		);
	}
}
