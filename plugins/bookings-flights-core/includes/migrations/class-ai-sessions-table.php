<?php
/**
 * AI session table migration.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Migrations;

defined( 'ABSPATH' ) || exit;

final class AI_Sessions_Table {

	private const OPTION_TABLE_VERSION = 'baf_db_version_ai_sessions';

	public static function maybe_upgrade(): void {
		if ( BAF_CORE_DB_VERSION === (string) get_option( self::OPTION_TABLE_VERSION, '' ) && true === self::exists() ) {
			return;
		}

		self::create();
	}

	public static function create(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
run_uuid varchar(36) NOT NULL,
created_at datetime NOT NULL,
finished_at datetime DEFAULT NULL,
user_id bigint(20) unsigned NOT NULL DEFAULT 0,
provider varchar(64) NOT NULL DEFAULT '',
mode varchar(20) NOT NULL DEFAULT '',
status varchar(20) NOT NULL DEFAULT '',
prompt_version varchar(40) NOT NULL DEFAULT '',
source_post_id bigint(20) unsigned NOT NULL DEFAULT 0,
request_hash varchar(64) NOT NULL DEFAULT '',
output_hash varchar(64) NOT NULL DEFAULT '',
output_summary varchar(255) NOT NULL DEFAULT '',
error_code varchar(100) NOT NULL DEFAULT '',
error_message varchar(255) NOT NULL DEFAULT '',
PRIMARY KEY  (id),
KEY run_uuid (run_uuid),
KEY user_id (user_id),
KEY provider (provider),
KEY status (status),
KEY created_at (created_at)
) {$charset_collate};";

		dbDelta( $sql );
		update_option( self::OPTION_TABLE_VERSION, BAF_CORE_DB_VERSION, false );
		update_option( Clicks_Table::OPTION_DB_VERSION, BAF_CORE_DB_VERSION, false );
	}

	public static function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . 'bf_ai_sessions';
	}

	private static function exists(): bool {
		global $wpdb;

		$table_name = self::table_name();

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}
}
