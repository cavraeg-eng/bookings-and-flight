<?php
/**
 * Plugin Name: Bookings and Flights Content Manager
 * Plugin URI:  http://bookings-and-flights.local
 * Description: Manage all editable content (text + images) for the Bookings and Flights Static theme
 * Version:     1.0.0
 * Author:      Cav
 * Author URI:
 * Text Domain: bookings_and_flights-content-manager
 * Domain Path: /languages
 * Requires PHP: 8.0
 * Requires at least: 6.0
 *
 * @package Bookings and Flights_Content_Manager
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Plugin version
define('BOOKINGS_AND_FLIGHTS_VERSION', '1.0.0');

// Plugin paths
define('BOOKINGS_AND_FLIGHTS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOOKINGS_AND_FLIGHTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BOOKINGS_AND_FLIGHTS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Autoloader for plugin classes
 *
 * @param string $class Class name to load
 */
function bookings_and_flights_autoloader($class) {
    // Only load classes from our namespace
    if (strpos($class, 'Bookings_And_Flights_') !== 0) {
        return;
    }

    // Remove the Bookings_And_Flights_ prefix and convert to filename
    $class_name = str_replace('Bookings_And_Flights_', '', $class);
    $class_file = str_replace('_', '-', strtolower($class_name));
    $file = BOOKINGS_AND_FLIGHTS_PLUGIN_DIR . 'includes/class-' . $class_file . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('bookings_and_flights_autoloader');

/**
 * Initialize the plugin
 */
function bookings_and_flights_init() {
    // Load helper functions (always available)
    require_once BOOKINGS_AND_FLIGHTS_PLUGIN_DIR . 'includes/helpers.php';

    // Initialize main plugin class
    $plugin = Bookings_And_Flights_Plugin::get_instance();
    $plugin->init();
}
add_action('plugins_loaded', 'bookings_and_flights_init');

/**
 * Activation hook
 */
function bookings_and_flights_activate() {
    // Seed global option defaults if not already set
    require_once BOOKINGS_AND_FLIGHTS_PLUGIN_DIR . 'includes/class-options.php';
    Bookings_And_Flights_Options::seed_defaults();

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'bookings_and_flights_activate');

/**
 * Deactivation hook
 */
function bookings_and_flights_deactivate() {
    // Clean up if needed
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'bookings_and_flights_deactivate');
