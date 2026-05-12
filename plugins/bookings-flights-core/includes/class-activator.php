<?php
/**
 * Plugin activation routines.
 *
 * @package BAF\Core
 */

namespace BAF\Core;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Cron\Cron_Manager;
use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Migrations\Clicks_Table;
use BAF\Core\Migrations\Provider_Stats_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Taxonomies\Taxonomy_Registrar;

defined( 'ABSPATH' ) || exit;

final class Activator {

	public static function activate(): void {
		update_option( BAF_CORE_OPTION_VERSION, BAF_CORE_VERSION, false );

		Capability_Manager::add_administrator_capabilities();
		Clicks_Table::create();
		AI_Sessions_Table::create();
		Provider_Stats_Table::create();
		Cron_Manager::bootstrap();
		Cron_Manager::activate();
		Post_Type_Registrar::register();
		Taxonomy_Registrar::register();
		delete_option( 'rewrite_rules' );

		do_action( 'baf_core_activated' );
	}
}