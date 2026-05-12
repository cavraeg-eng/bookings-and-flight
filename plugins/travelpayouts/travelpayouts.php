<?php

/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * @link              http://www.travelpayouts.com/?locale=en
 * @since             1.0.0
 * @package           Travelpayouts
 * @wordpress-plugin
 * Plugin Name:       Travelpayouts
 * Plugin URI:        https://wordpress.org/plugins/travelpayouts/
 * Description:       Earn money and make your visitors happy! Offer them useful tools for their travel needs. Earn on commission for each booking.
 * Version:           1.2.2
 * Author:            travelpayouts
 * Author URI:        http://www.travelpayouts.com/?locale=en
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       travelpayouts
 * Domain Path:       /languages
 */

use Travelpayouts\includes\Activator;
use Travelpayouts\includes\Deactivator;

// If this file is called directly, abort before using WordPress constants.
if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . '/wp-admin/includes/plugin.php';

// Import vendor
$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    deactivate_plugins(plugin_basename(__FILE__));
    wp_die('Main autoloader file is not exist');
}

require 'redux-core/travelpayouts-settings-framework.php';

require_once $autoloadPath;

register_activation_hook(__FILE__, [Activator::class, 'onActivate']);
register_deactivation_hook(__FILE__, [Deactivator::class, 'onDeactivation']);
Travelpayouts::getInstance();
