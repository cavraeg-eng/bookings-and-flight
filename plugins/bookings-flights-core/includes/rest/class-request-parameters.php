<?php
/**
 * REST request parameter validation and sanitization helpers.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

defined( 'ABSPATH' ) || exit;

final class Request_Parameters {

	public static function validate_positive_integer( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): bool {
		return is_numeric( $value ) && (int) $value >= 1;
	}

	public static function sanitize_positive_integer( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): int {
		return max( 1, absint( $value ) );
	}

	public static function validate_per_page( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): bool {
		return is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 50;
	}

	public static function sanitize_per_page( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): int {
		return min( 50, max( 1, absint( $value ) ) );
	}

	public static function validate_search( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): bool {
		return null === $value || is_scalar( $value );
	}

	public static function sanitize_search( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): string {
		return sanitize_text_field( (string) $value );
	}

	public static function validate_order( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): bool {
		return in_array( strtoupper( (string) $value ), array( 'ASC', 'DESC' ), true );
	}

	public static function sanitize_order( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): string {
		$order = strtoupper( (string) $value );

		return in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC';
	}

	public static function validate_orderby( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): bool {
		return in_array( sanitize_key( (string) $value ), self::allowed_orderby(), true );
	}

	public static function sanitize_orderby( mixed $value, ?\WP_REST_Request $request = null, string $parameter = '' ): string {
		$orderby = sanitize_key( (string) $value );

		return in_array( $orderby, self::allowed_orderby(), true ) ? $orderby : 'date';
	}

	public static function allowed_orderby(): array {
		return array( 'date', 'modified', 'title', 'menu_order' );
	}
}