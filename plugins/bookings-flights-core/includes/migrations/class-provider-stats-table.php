<?php

namespace BAF\Core\Migrations;

defined( 'ABSPATH' ) || exit;

final class Provider_Stats_Table {

	private const OPTION_TABLE_VERSION = 'baf_db_version_provider_stats';

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
snapshot_uuid varchar(36) NOT NULL,
recorded_at datetime NOT NULL,
provider varchar(64) NOT NULL DEFAULT '',
status varchar(20) NOT NULL DEFAULT '',
metric_key varchar(80) NOT NULL DEFAULT '',
metric_value decimal(18,4) DEFAULT NULL,
message varchar(255) NOT NULL DEFAULT '',
source varchar(64) NOT NULL DEFAULT '',
PRIMARY KEY  (id),
KEY provider (provider),
KEY status (status),
KEY metric_key (metric_key),
KEY recorded_at (recorded_at)
) {$charset_collate};";

		dbDelta( $sql );
		update_option( self::OPTION_TABLE_VERSION, BAF_CORE_DB_VERSION, false );
		update_option( Clicks_Table::OPTION_DB_VERSION, BAF_CORE_DB_VERSION, false );
	}

	public static function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . 'bf_provider_stats';
	}

	private static function exists(): bool {
		global $wpdb;

		$table_name = self::table_name();

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
	}
}
