<?php
/**
 * Dedicated capability registration helpers.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Capabilities;

defined( 'ABSPATH' ) || exit;

final class Capability_Manager {

	public const MANAGE_SETTINGS    = 'manage_baf_settings';
	public const EDIT_CONTENT       = 'edit_baf_content';
	public const PUBLISH_CONTENT    = 'publish_baf_content';
	public const MANAGE_AFFILIATES  = 'manage_baf_affiliates';
	public const VIEW_REPORTS       = 'view_baf_reports';
	public const RUN_AI             = 'run_baf_ai';
	public const MANAGE_ALERTS      = 'manage_baf_alerts';

	public static function all(): array {
		return array(
			self::MANAGE_SETTINGS,
			self::EDIT_CONTENT,
			self::PUBLISH_CONTENT,
			self::MANAGE_AFFILIATES,
			self::VIEW_REPORTS,
			self::RUN_AI,
			self::MANAGE_ALERTS,
		);
	}

	public static function bootstrap(): void {
		add_filter( 'map_meta_cap', array( self::class, 'map_dedicated_capabilities' ), 10, 2 );
	}

	public static function map_dedicated_capabilities( array $caps, string $capability ): array {
		if ( in_array( $capability, self::all(), true ) ) {
			return array( $capability );
		}

		return $caps;
	}

	public static function add_administrator_capabilities(): void {
		$role = get_role( 'administrator' );

		if ( null === $role ) {
			return;
		}

		foreach ( self::all() as $capability ) {
			if ( true === $role->has_cap( $capability ) ) {
				continue;
			}

			$role->add_cap( $capability, true );
		}
	}

	public static function content_post_type_capabilities(): array {
		return self::post_type_capabilities( self::EDIT_CONTENT, self::PUBLISH_CONTENT, self::EDIT_CONTENT );
	}

	public static function affiliate_post_type_capabilities(): array {
		return self::post_type_capabilities( self::MANAGE_AFFILIATES, self::MANAGE_AFFILIATES, self::MANAGE_AFFILIATES );
	}

	public static function alert_post_type_capabilities(): array {
		return self::post_type_capabilities( self::MANAGE_ALERTS, self::MANAGE_ALERTS, self::MANAGE_ALERTS );
	}

	public static function content_taxonomy_capabilities(): array {
		return self::taxonomy_capabilities( self::EDIT_CONTENT, self::EDIT_CONTENT );
	}

	private static function post_type_capabilities( string $edit_capability, string $publish_capability, string $delete_capability ): array {
		return array(
			'edit_post'              => $edit_capability,
			'read_post'              => $edit_capability,
			'delete_post'            => $delete_capability,
			'edit_posts'             => $edit_capability,
			'edit_others_posts'      => $edit_capability,
			'delete_posts'           => $delete_capability,
			'publish_posts'          => $publish_capability,
			'read_private_posts'     => $edit_capability,
			'delete_private_posts'   => $delete_capability,
			'delete_published_posts' => $delete_capability,
			'delete_others_posts'    => $delete_capability,
			'edit_private_posts'     => $edit_capability,
			'edit_published_posts'   => $edit_capability,
			'create_posts'           => $edit_capability,
			'read'                   => 'read',
		);
	}

	private static function taxonomy_capabilities( string $manage_capability, string $assign_capability ): array {
		return array(
			'manage_terms' => $manage_capability,
			'edit_terms'   => $manage_capability,
			'delete_terms' => $manage_capability,
			'assign_terms' => $assign_capability,
		);
	}
}