<?php
/**
 * Travelpayouts White Label Widget shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_White_Label_Shortcode {

	public static function render( $attributes = array() ): string {
		$settings  = Settings_Manager::get_travelpayouts();
		$widget_id = (string) ( $settings['white_label_widget_id'] ?? '' );

		if ( '' === $widget_id ) {
			if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
				return '<p class="baf-notice baf-notice--warning">' . esc_html__( 'Travelpayouts White Label Widget ID is not configured.', 'bookings-flights-core' ) . '</p>';
			}

			return '';
		}

		$consent = Settings_Manager::get_consent();

		if ( true !== (bool) $consent['allow_provider_requests'] ) {
			if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
				return '<p class="baf-notice baf-notice--warning">' . esc_html__( 'Travelpayouts White Label Widget is configured, but provider request consent is disabled.', 'bookings-flights-core' ) . '</p>';
			}

			return '';
		}

		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		$attributes = shortcode_atts(
			array(
				'mode' => 'both',
			),
			(array) $attributes,
			'baf_travelpayouts_white_label'
		);

		$mode = sanitize_key( (string) $attributes['mode'] );
		if ( ! in_array( $mode, array( 'both', 'search', 'results' ), true ) ) {
			$mode = 'both';
		}

		return self::render_container( $mode ) . self::render_loader_script( $widget_id, (string) $settings['white_label_results_url'] );
	}

	private static function render_container( string $mode ): string {
		$output = '<div class="baf-travelpayouts-white-label">';

		if ( in_array( $mode, array( 'both', 'search' ), true ) ) {
			$output .= '<div id="tpwl-search"></div>';
		}

		if ( in_array( $mode, array( 'both', 'results' ), true ) ) {
			$output .= '<div id="tpwl-tickets"></div>';
		}

		$output .= '</div>';

		return $output;
	}

	private static function render_loader_script( string $widget_id, string $results_url ): string {
		$script_src    = add_query_arg( 'wl_id', rawurlencode( $widget_id ), 'https://tpwgts.com/wl_web/main.js' );
		$configuration = array();

		if ( '' !== $results_url ) {
			$configuration['resultsURL'] = $results_url;
		}

		$configuration_json = wp_json_encode( $configuration );
		$script_src_json    = wp_json_encode( esc_url_raw( $script_src ) );

		if ( false === $configuration_json || false === $script_src_json ) {
			return '';
		}

		return sprintf(
			'<script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){window.TPWL_CONFIGURATION=Object.assign({},window.TPWL_CONFIGURATION||{},%1$s);var script=document.createElement("script");script.async=true;script.type="module";script.src=%2$s;document.head.appendChild(script);}());</script>',
			$configuration_json,
			$script_src_json
		);
	}
}
