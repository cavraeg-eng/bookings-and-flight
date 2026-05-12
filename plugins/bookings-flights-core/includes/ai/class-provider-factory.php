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

	public static function make(): AI_Provider_Interface|\WP_Error {
		$settings = Settings_Manager::get_ai();
		$mode     = sanitize_key( (string) $settings['mode'] );

		if ( 'demo' === $mode ) {
			return new Demo_AI_Provider();
		}

		$consent = Settings_Manager::get_consent();

		if ( true !== (bool) $consent['allow_external_ai'] ) {
			return new \WP_Error( 'baf_ai_consent_required', __( 'Live AI generation requires external AI consent before any data is sent to a provider.', 'bookings-flights-core' ), array( 'status' => 403 ) );
		}

		$provider = sanitize_key( (string) $settings['provider'] );
		$api_key  = trim( (string) $settings['api_key'] );

		if ( '' === $provider || '' === $api_key ) {
			return new \WP_Error( 'baf_ai_not_configured', __( 'Live AI generation is not configured yet.', 'bookings-flights-core' ), array( 'status' => 503 ) );
		}

		if ( 'openai' !== $provider ) {
			return new \WP_Error( 'baf_ai_provider_not_supported', __( 'The selected live AI provider is not supported by the current WordPress adapter.', 'bookings-flights-core' ), array( 'status' => 501 ) );
		}

		return new OpenAI_Provider( $api_key );
	}
}