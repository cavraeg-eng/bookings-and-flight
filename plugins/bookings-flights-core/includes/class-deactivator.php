<?php
/**
 * Plugin deactivation routines.
 *
 * @package BAF\Core
 */

namespace BAF\Core;

use BAF\Core\Cron\Cron_Manager;

defined( 'ABSPATH' ) || exit;

final class Deactivator {

	public static function deactivate(): void {
		Cron_Manager::unschedule_events();
		delete_option( 'rewrite_rules' );

		do_action( 'baf_core_deactivated' );
	}
}