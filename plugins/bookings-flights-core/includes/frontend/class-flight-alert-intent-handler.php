<?php
/**
 * Frontend flight alert intent form handler.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Post_Types\Post_Type_Registrar;
use BAF\Core\Services\Flight_Alert_Service;

defined( 'ABSPATH' ) || exit;

final class Flight_Alert_Intent_Handler {

	public const ACTION              = 'baf_save_flight_alert';
	public const DELETE_ACTION       = 'baf_delete_flight_alert';
	public const NONCE_ACTION        = 'baf_save_flight_alert';
	public const NONCE_FIELD         = 'baf_alert_nonce';
	public const DELETE_NONCE_ACTION = 'baf_delete_flight_alert';
	public const DELETE_NONCE_FIELD  = 'baf_alert_delete_nonce';
	public const STATUS_QUERY_ARG    = 'baf_alert_status';

	private const STATUS_SAVED           = 'saved';
	private const STATUS_UPDATED         = 'updated';
	private const STATUS_DELETED         = 'deleted';
	private const STATUS_INVALID_EMAIL   = 'invalid_email';
	private const STATUS_MISSING_ROUTE   = 'missing_route';
	private const STATUS_CONSENT         = 'consent_required';
	private const STATUS_UNAVAILABLE     = 'unavailable';
	private const STATUS_INVALID_REQUEST = 'invalid_request';
	private const STATUS_RATE_LIMITED    = 'rate_limited';
	private const STATUS_LIMIT_REACHED   = 'limit_reached';
	private const STATUS_DELETE_INVALID  = 'delete_invalid';
	private const THROTTLE_SECONDS       = 60;

	public static function bootstrap(): void {
		add_action( 'admin_post_' . self::ACTION, array( self::class, 'handle_submit' ) );
		add_action( 'admin_post_nopriv_' . self::ACTION, array( self::class, 'handle_submit' ) );
		add_action( 'admin_post_' . self::DELETE_ACTION, array( self::class, 'handle_delete' ) );
		add_action( 'admin_post_nopriv_' . self::DELETE_ACTION, array( self::class, 'handle_delete' ) );
	}

	public static function handle_submit(): void {
		check_admin_referer( self::NONCE_ACTION, self::NONCE_FIELD );

		$posted       = self::posted_data();
		$redirect_url = self::redirect_url( self::url_field( $posted, 'baf_alert_redirect' ) );

		if ( ! post_type_exists( Post_Type_Registrar::TRAVEL_ALERT ) ) {
			self::redirect_with_status( $redirect_url, self::STATUS_UNAVAILABLE );
		}

		if ( 'POST' !== self::request_method() ) {
			self::redirect_with_status( $redirect_url, self::STATUS_INVALID_REQUEST );
		}

		if ( '1' !== self::field( $posted, 'baf_alert_consent' ) ) {
			self::redirect_with_status( $redirect_url, self::STATUS_CONSENT );
		}

		$route_id    = absint( self::field( $posted, 'baf_alert_route_post_id' ) );
		$route_codes = self::route_codes_from_post( $route_id );
		$origin      = self::normalize_iata( self::field( $posted, 'baf_alert_origin' ) );
		$destination = self::normalize_iata( self::field( $posted, 'baf_alert_destination' ) );

		if ( '' === $origin && isset( $route_codes['origin'] ) ) {
			$origin = $route_codes['origin'];
		}

		if ( '' === $destination && isset( $route_codes['destination'] ) ) {
			$destination = $route_codes['destination'];
		}

		if ( '' === $origin || '' === $destination ) {
			self::redirect_with_status( $redirect_url, self::STATUS_MISSING_ROUTE );
		}

		$email = sanitize_email( self::field( $posted, 'baf_alert_email' ) );
		if ( '' === $email && is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$email = sanitize_email( (string) $user->user_email );
		}

		if ( '' === $email || ! is_email( $email ) ) {
			self::redirect_with_status( $redirect_url, self::STATUS_INVALID_EMAIL );
		}

		$user_id     = get_current_user_id();
		$frequency   = self::allowed_value( self::field( $posted, 'baf_alert_frequency' ), array( 'daily', 'weekly', 'monthly' ), 'weekly' );
		$cabin       = self::allowed_value( self::field( $posted, 'baf_alert_cabin' ), array( 'economy', 'premium_economy', 'business', 'first' ), 'economy' );
		$depart_date = self::normalize_date( self::field( $posted, 'baf_alert_depart_date' ) );
		$return_date = self::normalize_date( self::field( $posted, 'baf_alert_return_date' ) );
		$travelers   = min( 9, max( 1, absint( self::field( $posted, 'baf_alert_travelers' ) ) ) );
		$surface     = sanitize_key( self::field( $posted, 'baf_alert_surface' ) );
		$source_url  = self::url_field( $posted, 'baf_alert_source_url' );
		$route_key   = $origin . '-' . $destination;

		if ( self::is_rate_limited( $email, $route_key ) ) {
			self::redirect_with_status( $redirect_url, self::STATUS_RATE_LIMITED );
		}

		$result = ( new Flight_Alert_Service() )->save(
			array(
				'origin'      => $origin,
				'destination' => $destination,
				'email'       => $email,
				'user_id'     => $user_id,
				'frequency'   => $frequency,
				'cabin'       => $cabin,
				'depart_date' => $depart_date,
				'return_date' => $return_date,
				'travelers'   => $travelers,
				'surface'     => $surface,
				'source_url'  => $source_url,
				'route_id'    => $route_id,
			)
		);

		if ( is_wp_error( $result ) ) {
			$status = 'baf_alert_limit_reached' === $result->get_error_code() ? self::STATUS_LIMIT_REACHED : self::STATUS_UNAVAILABLE;
			self::redirect_with_status( $redirect_url, $status );
		}

		self::mark_rate_limited( $email, $route_key );

		$status = 'updated' === (string) ( $result['mode'] ?? '' ) ? self::STATUS_UPDATED : self::STATUS_SAVED;
		self::redirect_with_status( $redirect_url, $status );
	}

	public static function handle_delete(): void {
		$data         = self::request_data();
		$redirect_url = self::redirect_url( self::url_field( $data, 'baf_alert_redirect' ) );

		if ( 'POST' !== self::request_method() ) {
			self::redirect_with_status( $redirect_url, self::STATUS_DELETE_INVALID );
		}

		$alert_id     = absint( self::field( $data, 'baf_alert_id' ) );
		$token        = self::field( $data, 'baf_alert_token' );

		check_admin_referer( self::delete_nonce_action( $alert_id, $token ), self::DELETE_NONCE_FIELD );

		$result       = ( new Flight_Alert_Service() )->delete_by_token( $alert_id, $token );

		self::redirect_with_status( $redirect_url, is_wp_error( $result ) ? self::STATUS_DELETE_INVALID : self::STATUS_DELETED );
	}

	public static function delete_nonce_action( int $alert_id, string $token ): string {
		$token = preg_replace( '/[^a-f0-9]/', '', strtolower( $token ) );
		$token = is_string( $token ) ? $token : '';

		return self::DELETE_NONCE_ACTION . '_' . absint( $alert_id ) . '_' . substr( hash( 'sha256', $token ), 0, 16 );
	}

	public static function normalize_iata( string $value ): string {
		$value = preg_replace( '/[^A-Z]/', '', strtoupper( $value ) );
		$value = is_string( $value ) ? $value : '';

		return preg_match( '/^[A-Z]{3}$/', $value ) ? $value : '';
	}

	public static function normalize_date( string $value ): string {
		$value = trim( $value );

		if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches ) ) {
			return '';
		}

		return wp_checkdate( (int) $matches[2], (int) $matches[3], (int) $matches[1], $value ) ? $value : '';
	}

	public static function allowed_value( string $value, array $allowed, string $fallback ): string {
		$value = sanitize_key( $value );

		return in_array( $value, $allowed, true ) ? $value : $fallback;
	}

	private static function posted_data(): array {
		if ( empty( $_POST ) || ! is_array( $_POST ) ) {
			return array();
		}

		return wp_unslash( $_POST );
	}

	private static function request_data(): array {
		$get  = ! empty( $_GET ) && is_array( $_GET ) ? wp_unslash( $_GET ) : array();
		$post = ! empty( $_POST ) && is_array( $_POST ) ? wp_unslash( $_POST ) : array();

		return array_merge( $get, $post );
	}

	private static function field( array $data, string $key ): string {
		if ( ! isset( $data[ $key ] ) || ! is_scalar( $data[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( (string) $data[ $key ] );
	}

	private static function url_field( array $data, string $key ): string {
		if ( ! isset( $data[ $key ] ) || ! is_scalar( $data[ $key ] ) ) {
			return '';
		}

		return esc_url_raw( (string) $data[ $key ] );
	}

	private static function request_method(): string {
		if ( ! isset( $_SERVER['REQUEST_METHOD'] ) || ! is_scalar( $_SERVER['REQUEST_METHOD'] ) ) {
			return '';
		}

		return strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) );
	}

	private static function route_codes_from_post( int $route_id ): array {
		if ( $route_id <= 0 || Post_Type_Registrar::ROUTE !== get_post_type( $route_id ) ) {
			return array();
		}

		return array(
			'origin'      => self::normalize_iata( (string) get_post_meta( $route_id, 'baf_origin_airport', true ) ),
			'destination' => self::normalize_iata( (string) get_post_meta( $route_id, 'baf_destination_airport', true ) ),
		);
	}

	private static function is_rate_limited( string $email, string $route_key ): bool {
		return false !== get_transient( self::rate_limit_key( $email, $route_key ) );
	}

	private static function mark_rate_limited( string $email, string $route_key ): void {
		set_transient( self::rate_limit_key( $email, $route_key ), '1', self::THROTTLE_SECONDS );
	}

	private static function rate_limit_key( string $email, string $route_key ): string {
		$user_id    = get_current_user_id();
		$remote     = isset( $_SERVER['REMOTE_ADDR'] ) && is_scalar( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) && is_scalar( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 160 ) : '';
		$fingerprint = wp_hash(
			implode(
				'|',
				array(
					(string) $user_id,
					$remote,
					$user_agent,
					strtolower( $email ),
					$route_key,
				)
			)
		);

		return 'baf_alert_rate_' . substr( md5( $fingerprint ), 0, 24 );
	}

	private static function redirect_url( string $url ): string {
		$fallback = home_url( '/flights/' );
		$url      = '' !== $url ? $url : $fallback;
		$url      = wp_validate_redirect( $url, $fallback );

		return remove_query_arg( self::STATUS_QUERY_ARG, $url );
	}

	private static function redirect_with_status( string $redirect_url, string $status ): void {
		wp_safe_redirect(
			add_query_arg(
				self::STATUS_QUERY_ARG,
				sanitize_key( $status ),
				$redirect_url
			)
		);
		exit;
	}
}
