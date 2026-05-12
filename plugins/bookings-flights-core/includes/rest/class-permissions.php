<?php
/**
 * REST permission helpers.
 *
 * @package BAF\Core
 */

namespace BAF\Core\REST;

defined( 'ABSPATH' ) || exit;

final class Permissions {

	public static function can_read_public_collection( \WP_REST_Request $request ): bool {
		return true;
	}

	public static function require_capability( string $capability ): bool|\WP_Error {
		if ( current_user_can( $capability ) ) {
			return true;
		}

		return new \WP_Error(
			'baf_rest_forbidden',
			__( 'You are not allowed to access this Bookings and Flights resource.', 'bookings-flights-core' ),
			array( 'status' => rest_authorization_required_code() )
		);
	}
}