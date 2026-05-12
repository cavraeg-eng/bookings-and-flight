<?php
/**
 * SubID builder for Travelpayouts widget placements.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Services;

use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Widget_Subid_Service {

	private const DEFAULT_PATTERN = '{channel}_{surface}_{vertical}_{slug}_{placement}';
	private const DEFAULT_SUBID   = 'baf_site_travel_page_placement';

	public static function build( array $placement, array $context ): string {
		$tracking = Settings_Manager::get_tracking();
		$channel  = self::sanitize_segment( (string) ( $context['channel'] ?? '' ) );

		if ( '' === $channel ) {
			$channel = self::sanitize_segment( (string) ( $tracking['subid_prefix'] ?? 'baf' ) );
		}

		$tokens = array(
			'{channel}'   => $channel,
			'{surface}'   => self::sanitize_segment( (string) ( $context['surface'] ?? 'site' ) ),
			'{vertical}'  => self::sanitize_segment( (string) ( $placement['vertical'] ?? 'travel' ) ),
			'{slug}'      => self::sanitize_segment( (string) ( $context['slug'] ?? 'page' ) ),
			'{placement}' => self::sanitize_segment( (string) ( $placement['key'] ?? $context['placement'] ?? 'placement' ) ),
		);

		$pattern = self::sanitize_pattern( (string) ( $placement['subid_pattern'] ?? self::DEFAULT_PATTERN ) );
		$subid   = strtr( $pattern, $tokens );

		return self::sanitize_subid( $subid );
	}

	public static function add_to_url( string $url, string $subid, string $partner_id = '' ): string {
		$url   = esc_url_raw( trim( $url ) );
		$subid = self::sanitize_subid( $subid );

		if ( '' === $url || '' === $subid ) {
			return $url;
		}

		$query = array();
		wp_parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );

		if ( isset( $query['marker'] ) && is_scalar( $query['marker'] ) && '' !== (string) $query['marker'] ) {
			$partner_id = strtok( sanitize_text_field( (string) $query['marker'] ), '.' );
		}

		$partner_id = self::sanitize_partner_id( (string) $partner_id );

		if ( '' !== $partner_id ) {
			return esc_url_raw( add_query_arg( 'marker', $partner_id . '.' . $subid, $url ) );
		}

		return esc_url_raw( add_query_arg( 'subid', $subid, $url ) );
	}

	private static function sanitize_pattern( string $value ): string {
		$value = strtolower( trim( $value ) );

		if ( '' === $value ) {
			return self::DEFAULT_PATTERN;
		}

		$value = preg_replace( '/[^a-z0-9_{}]+/', '_', $value );
		$value = trim( preg_replace( '/_+/', '_', (string) $value ), '_' );

		return '' === $value ? self::DEFAULT_PATTERN : substr( $value, 0, 191 );
	}

	private static function sanitize_subid( string $value ): string {
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9_]+/', '_', $value );
		$value = trim( preg_replace( '/_+/', '_', (string) $value ), '_' );

		return substr( '' === $value ? self::DEFAULT_SUBID : $value, 0, 191 );
	}

	private static function sanitize_segment( string $value ): string {
		$value = strtolower( trim( $value ) );
		$value = preg_replace( '/[^a-z0-9_]+/', '_', $value );
		$value = trim( preg_replace( '/_+/', '_', (string) $value ), '_' );

		return substr( $value, 0, 64 );
	}

	private static function sanitize_partner_id( string $value ): string {
		return substr( preg_replace( '/[^A-Za-z0-9_]+/', '', trim( $value ) ), 0, 64 );
	}
}
