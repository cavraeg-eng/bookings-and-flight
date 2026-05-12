<?php
/**
 * Main plugin bootstrap.
 *
 * @package BAF\Core
 */

namespace BAF\Core;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Admin\Admin_Manager;
use BAF\Core\Cron\Cron_Manager;
use BAF\Core\Frontend\Frontend_Manager;
use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Migrations\Clicks_Table;
use BAF\Core\Migrations\Provider_Stats_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\REST\Rest_Manager;
use BAF\Core\Settings\Settings_Manager;
use BAF\Core\Taxonomies\Taxonomy_Registrar;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	private static bool $bootstrapped = false;

	public static function bootstrap(): void {
		if ( true === self::$bootstrapped ) {
			return;
		}

		self::$bootstrapped = true;

		add_action( 'init', array( Post_Type_Registrar::class, 'register' ), 0 );
		add_action( 'init', array( Taxonomy_Registrar::class, 'register' ), 1 );
		add_action( 'rest_api_init', array( Rest_Manager::class, 'register_routes' ) );

		Capability_Manager::bootstrap();
		Cron_Manager::bootstrap();
		Cron_Manager::schedule_events();
		Capability_Manager::add_administrator_capabilities();
		Clicks_Table::maybe_upgrade();
		AI_Sessions_Table::maybe_upgrade();
		Provider_Stats_Table::maybe_upgrade();
		Settings_Manager::bootstrap();
		Admin_Manager::bootstrap();
		Frontend_Manager::bootstrap();

		do_action( 'baf_core_loaded' );
	}
}