<?php
/**
 * AI provider factory.
 *
 * @package BAF\Core
 */

namespace BAF\Core\AI;

use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Provider_Factory {

	public static function supports_live_provider( string $provider ): bool {
		return 'openai' === sanitize_key( $provider );
	}

	public static function live_readiness( ?array $settings = null, ?array $consent = null ): array {
		$settings = $settings ?? Settings_Manager::get_ai();
		$consent  = $consent ?? Settings_Manager::get_consent();
		$provider = sanitize_key( self::string_value( $settings['provider'] ?? '' ) );
		$api_key  = trim( self::string_value( $settings['api_key'] ?? '' ) );

		if ( '' === $provider ) {
			return self::readiness( false, 'missing_provider', __( 'Live mode needs a supported AI provider before requests can run.', 'bookings-flights-core' ) );
		}

		if ( ! self::supports_live_provider( $provider ) ) {
			return self::readiness( false, 'unsupported_provider', __( 'Live mode is selected, but the current WordPress adapter only supports OpenAI.', 'bookings-flights-core' ) );
		}

		if ( '' === $api_key ) {
			return self::readiness( false, 'missing_api_key', __( 'Live mode needs a configured provider API key before requests can run.', 'bookings-flights-core' ) );
		}

		if ( true !== (bool) ( $consent['allow_external_ai'] ?? false ) ) {
			return self::readiness( false, 'missing_external_ai_consent', __( 'Live mode needs External AI consent enabled in settings before requests can run.', 'bookings-flights-core' ) );
		}

		return self::readiness( true, 'ready', __( 'Live mode is configured; this request still needs the consent checkbox.', 'bookings-flights-core' ) );
	}

	public static function make(): AI_Provider_Interface|\WP_Error {
		$settings = Settings_Manager::get_ai();
		$mode     = sanitize_key( self::string_value( $settings['mode'] ?? '' ) );

		if ( 'demo' === $mode ) {
			return new Demo_AI_Provider();
		}

		$consent = Settings_Manager::get_consent();

		$api_key   = trim( self::string_value( $settings['api_key'] ?? '' ) );
		$readiness = self::live_readiness( $settings, $consent );

		if ( true !== (bool) $readiness['ready'] ) {
			return match ( (string) $readiness['reason'] ) {
				'missing_external_ai_consent' => new \WP_Error( 'baf_ai_consent_required', __( 'Live AI generation requires external AI consent before any data is sent to a provider.', 'bookings-flights-core' ), array( 'status' => 403 ) ),
				'unsupported_provider'        => new \WP_Error( 'baf_ai_provider_not_supported', __( 'The selected live AI provider is not supported by the current WordPress adapter.', 'bookings-flights-core' ), array( 'status' => 501 ) ),
				default                       => new \WP_Error( 'baf_ai_not_configured', __( 'Live AI generation is not configured yet.', 'bookings-flights-core' ), array( 'status' => 503 ) ),
			};
		}

		return new OpenAI_Provider( $api_key );
	}

	private static function readiness( bool $ready, string $reason, string $message ): array {
		return array(
			'ready'   => $ready,
			'reason'  => sanitize_key( $reason ),
			'message' => $message,
		);
	}

	private static function string_value( mixed $value ): string {
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( is_int( $value ) || is_float( $value ) || is_bool( $value ) ) {
			return (string) $value;
		}

		if ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			return (string) $value;
		}

		return '';
	}
}
