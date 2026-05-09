<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */
$devDefinitionsPath = __DIR__ . '/dev/definitions.php';

if (file_exists($devDefinitionsPath)) {
    require_once $devDefinitionsPath;
}


defined('TRAVELPAYOUTS_PLUGIN_NAME') or define('TRAVELPAYOUTS_PLUGIN_NAME', 'travelpayouts');
defined('TRAVELPAYOUTS_PLUGIN_PATH') or define('TRAVELPAYOUTS_PLUGIN_PATH', __DIR__ );
defined('TRAVELPAYOUTS_PLUGIN_SRC') or define('TRAVELPAYOUTS_PLUGIN_SRC', TRAVELPAYOUTS_PLUGIN_PATH . 'src');
defined('TRAVELPAYOUTS_TEXT_DOMAIN') or define('TRAVELPAYOUTS_TEXT_DOMAIN', 'travelpayouts');
defined('TRAVELPAYOUTS_VERSION_KEY') or define('TRAVELPAYOUTS_VERSION_KEY', 'travelpayouts_version');
defined('TRAVELPAYOUTS_REDUX_OPTION') or define('TRAVELPAYOUTS_REDUX_OPTION', 'travelpayouts_admin_settings');
defined('TRAVELPAYOUTS_DEBUG') or define('TRAVELPAYOUTS_DEBUG', false);
defined('TRAVELPAYOUTS_SETTINGS_RESET_BUTTON') or define('TRAVELPAYOUTS_SETTINGS_RESET_BUTTON', false);
define('TRAVELPAYOUTS_VERSION', '1.2.2');
