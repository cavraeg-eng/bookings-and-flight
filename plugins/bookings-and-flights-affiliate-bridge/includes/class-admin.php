<?php
/**
 * Admin screen — Settings → Affiliate Bridge.
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

namespace BAF\AffiliateBridge;

defined( 'ABSPATH' ) || exit;

class Admin {

	public const MENU_SLUG = 'baf-affiliate-bridge';

	public static function bootstrap(): void {
		add_action( 'admin_menu', array( self::class, 'register_menu' ) );
	}

	public static function register_menu(): void {
		add_options_page(
			__( 'Affiliate Bridge', 'baf-affiliate-bridge' ),
			__( 'Affiliate Bridge', 'baf-affiliate-bridge' ),
			'manage_options',
			self::MENU_SLUG,
			array( self::class, 'render_page' )
		);
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$suppliers        = Settings::suppliers();
		$creds            = (array) get_option( BAF_OPT_SUPPLIER_CREDS, array() );
		$search_api_url   = (string) get_option( BAF_OPT_SEARCH_API_URL, 'http://localhost:4050' );
		$postback_secret  = (string) get_option( BAF_OPT_POSTBACK_SECRET, '' );
		$postback_endpoint = rest_url( 'baf/v1/postback' );

		include BAF_AFFILIATE_BRIDGE_DIR . 'views/settings.php';
	}
}
