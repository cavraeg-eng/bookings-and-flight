<?php
/**
 * Affiliate click table migration.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Migrations;

defined( 'ABSPATH' ) || exit;

final class Clicks_Table {

	public const OPTION_DB_VERSION = 'baf_db_version';
	private const OPTION_TABLE_VERSION = 'baf_db_version_clicks';

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
event_uuid varchar(36) NOT NULL,
occurred_at datetime NOT NULL,
post_id bigint(20) unsigned NOT NULL DEFAULT 0,
provider varchar(64) NOT NULL DEFAULT '',
subid varchar(191) NOT NULL DEFAULT '',
target_host varchar(191) NOT NULL DEFAULT '',
user_id bigint(20) unsigned NOT NULL DEFAULT 0,
ip_hash varchar(64) NOT NULL DEFAULT '',
user_agent_hash varchar(64) NOT NULL DEFAULT '',
referer_hash varchar(64) NOT NULL DEFAULT '',
PRIMARY KEY  (id),
KEY event_uuid (event_uuid),
KEY post_id (post_id),
KEY provider (provider),
KEY occurred_at (occurred_at)
) {$charset_collate};";

		dbDelta( $sql );
		update_option( self::OPTION_TABLE_VERSION, BAF_CORE_DB_VERSION, false );
		update_option( self::OPTION_DB_VERSION, BAF_CORE_DB_VERSION, false );
	}

	public static function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . 'bf_clicks';
	}

	private static function exists(): bool {
		global $wpdb;

		$table_name = self::table_name();

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}
}
