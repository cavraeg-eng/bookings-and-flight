<?php
/**
 * Plugin Name:       Bookings and Flights — Affiliate Bridge
 * Description:       Admin UI for supplier affiliate credentials and REST endpoints for supplier conversion postbacks. Does NOT hold payments. All bookings complete on the supplier site.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            Bookings and Flights
 * License:           Proprietary
 * Text Domain:       baf-affiliate-bridge
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

defined( 'ABSPATH' ) || exit;

define( 'BAF_AFFILIATE_BRIDGE_VERSION', '0.1.0' );
define( 'BAF_AFFILIATE_BRIDGE_FILE', __FILE__ );
define( 'BAF_AFFILIATE_BRIDGE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAF_AFFILIATE_BRIDGE_URL', plugin_dir_url( __FILE__ ) );

// Option keys — single source of truth.
define( 'BAF_OPT_SUPPLIER_CREDS', 'baf_supplier_credentials' );
define( 'BAF_OPT_SEARCH_API_URL', 'baf_search_api_url' );
define( 'BAF_OPT_POSTBACK_SECRET', 'baf_postback_secret' );
define( 'BAF_OPT_CREDENTIAL_SYNC_SECRET', 'baf_credential_sync_secret' );
define( 'BAF_LOCAL_POSTBACK_SECRET', 'dev-secret-change-me' );
define( 'BAF_LOCAL_CREDENTIAL_SYNC_SECRET', 'dev-credential-sync-secret' );

function baf_affiliate_bridge_default_secret( string $option_name ): string {
	$is_local_env = in_array( wp_get_environment_type(), array( 'local', 'development' ), true );

	if ( true === $is_local_env ) {
		if ( BAF_OPT_CREDENTIAL_SYNC_SECRET === $option_name ) {
			return BAF_LOCAL_CREDENTIAL_SYNC_SECRET;
		}

		if ( BAF_OPT_POSTBACK_SECRET === $option_name ) {
			return BAF_LOCAL_POSTBACK_SECRET;
		}
	}

	return wp_generate_password( 48, false, false );
}

require_once BAF_AFFILIATE_BRIDGE_DIR . 'includes/class-settings.php';
require_once BAF_AFFILIATE_BRIDGE_DIR . 'includes/class-admin.php';
require_once BAF_AFFILIATE_BRIDGE_DIR . 'includes/class-rest.php';
require_once BAF_AFFILIATE_BRIDGE_DIR . 'includes/class-postbacks.php';

add_action( 'plugins_loaded', function () {
	\BAF\AffiliateBridge\Settings::bootstrap();
	\BAF\AffiliateBridge\Admin::bootstrap();
	\BAF\AffiliateBridge\REST::bootstrap();
	\BAF\AffiliateBridge\Postbacks::bootstrap();
} );

register_activation_hook( __FILE__, function () {
	if ( ! get_option( BAF_OPT_POSTBACK_SECRET ) ) {
		update_option( BAF_OPT_POSTBACK_SECRET, baf_affiliate_bridge_default_secret( BAF_OPT_POSTBACK_SECRET ) );
	}
	if ( ! get_option( BAF_OPT_CREDENTIAL_SYNC_SECRET ) ) {
		update_option( BAF_OPT_CREDENTIAL_SYNC_SECRET, baf_affiliate_bridge_default_secret( BAF_OPT_CREDENTIAL_SYNC_SECRET ) );
	}
	if ( ! get_option( BAF_OPT_SEARCH_API_URL ) ) {
		update_option( BAF_OPT_SEARCH_API_URL, 'http://localhost:4050' );
	}
} );
