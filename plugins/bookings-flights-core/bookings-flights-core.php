<?php
/**
 * Plugin Name:       Bookings and Flights Core
 * Description:       Core bootstrap for the Bookings and Flights WordPress-native travel platform.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            Bookings and Flights
 * License:           Proprietary
 * Text Domain:       bookings-flights-core
 *
 * @package BAF\Core
 */

defined( 'ABSPATH' ) || exit;

define( 'BAF_CORE_VERSION', '0.1.0' );
define( 'BAF_CORE_DB_VERSION', '1.2.0' );
define( 'BAF_CORE_FILE', __FILE__ );
define( 'BAF_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAF_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'BAF_CORE_BASENAME', plugin_basename( __FILE__ ) );
define( 'BAF_CORE_OPTION_VERSION', 'baf_core_version' );

spl_autoload_register( 'baf_core_autoload' );

function baf_core_autoload( string $class ): void {
	$prefix = 'BAF\\Core\\';

	if ( 0 !== strpos( $class, $prefix ) ) {
		return;
	}

	$relative_class = substr( $class, strlen( $prefix ) );
	$parts          = explode( '\\', $relative_class );
	$class_name     = array_pop( $parts );
	$directory      = BAF_CORE_DIR . 'includes/';

	if ( ! empty( $parts ) ) {
		$directory .= strtolower( str_replace( '_', '-', implode( '/', $parts ) ) ) . '/';
	}

	$file = $directory . 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

	if ( is_readable( $file ) ) {
		require_once $file;
	}
}

function baf_core_bootstrap(): void {
	\BAF\Core\Plugin::bootstrap();
}
add_action( 'plugins_loaded', 'baf_core_bootstrap' );

register_activation_hook( __FILE__, array( \BAF\Core\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( \BAF\Core\Deactivator::class, 'deactivate' ) );