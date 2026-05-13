<?php
/**
 * Plugin activation routines.
 *
 * @package BAF\Core
 */

namespace BAF\Core;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Cron\Cron_Manager;
use BAF\Core\Frontend\AI_Planner_Page;
use BAF\Core\Migrations\AI_Sessions_Table;
use BAF\Core\Migrations\Clicks_Table;
use BAF\Core\Migrations\Provider_Stats_Table;
use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Services\Travelpayouts_Widget_Registry_Service;
use BAF\Core\Taxonomies\Taxonomy_Registrar;

defined( 'ABSPATH' ) || exit;

final class Activator {

	public static function activate(): void {
		update_option( BAF_CORE_OPTION_VERSION, BAF_CORE_VERSION, false );

		Capability_Manager::add_administrator_capabilities();
		Clicks_Table::create();
		AI_Sessions_Table::create();
		Provider_Stats_Table::create();
		Travelpayouts_Widget_Registry_Service::maybe_install();
		Cron_Manager::bootstrap();
		Cron_Manager::activate();
		Post_Type_Registrar::register();
		Taxonomy_Registrar::register();
		AI_Planner_Page::register_rewrite();
		delete_option( 'rewrite_rules' );

		do_action( 'baf_core_activated' );
	}
}
