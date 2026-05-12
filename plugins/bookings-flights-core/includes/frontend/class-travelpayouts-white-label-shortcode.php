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

		$instance_id = wp_unique_id( 'baf-tpwl-legacy-' );

		return self::render_container( $mode, $instance_id ) . self::render_loader_script( $widget_id, (string) $settings['white_label_results_url'], $mode, $instance_id );
	}

	private static function render_container( string $mode, string $instance_id ): string {
		$output = sprintf(
			'<div class="baf-travelpayouts-white-label" id="%s">',
			esc_attr( $instance_id )
		);

		if ( in_array( $mode, array( 'both', 'search' ), true ) ) {
			$output .= sprintf(
				'<div id="%1$s" class="baf-travelpayouts-white-label__slot" tabindex="0"></div>',
				esc_attr( $instance_id . 'search' )
			);
		}

		if ( in_array( $mode, array( 'both', 'results' ), true ) ) {
			$output .= sprintf(
				'<div id="%1$s" class="baf-travelpayouts-white-label__slot" tabindex="0"></div>',
				esc_attr( $instance_id . 'tickets' )
			);
		}

		$output .= '<div class="baf-travelpayouts-widget__fallback" role="status">' . esc_html__( 'Travelpayouts White Label could not load because another White Label search is already active on this page.', 'bookings-flights-core' ) . '</div>';
		$output .= '</div>';

		return $output;
	}

	private static function render_loader_script( string $widget_id, string $results_url, string $mode, string $instance_id ): string {
		$script_src    = add_query_arg( 'wl_id', rawurlencode( $widget_id ), 'https://tpwgts.com/wl_web/main.js' );
		$configuration = array();

		if ( '' !== $results_url ) {
			$configuration['resultsURL'] = $results_url;
		}

		$configuration_json = wp_json_encode( $configuration );
		$script_src_json    = wp_json_encode( esc_url_raw( $script_src ) );
		$instance_id_json   = wp_json_encode( $instance_id );
		$search_id_json     = wp_json_encode( $instance_id . 'search' );
		$results_id_json    = wp_json_encode( $instance_id . 'tickets' );
		$fixed_search_json  = wp_json_encode( 'tpwl-search' );
		$fixed_results_json = wp_json_encode( 'tpwl-tickets' );
		$needs_search_json  = wp_json_encode( in_array( $mode, array( 'both', 'search' ), true ) );
		$needs_results_json = wp_json_encode( in_array( $mode, array( 'both', 'results' ), true ) );

		if ( false === $configuration_json || false === $script_src_json || false === $instance_id_json || false === $search_id_json || false === $results_id_json || false === $fixed_search_json || false === $fixed_results_json || false === $needs_search_json || false === $needs_results_json ) {
			return '';
		}

		return sprintf(
			'<script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){var wrapper=document.getElementById(%1$s);var needsSearch=%2$s;var needsResults=%3$s;var search=needsSearch?document.getElementById(%4$s):null;var results=needsResults?document.getElementById(%5$s):null;if(!wrapper||(needsSearch&&!search)||(needsResults&&!results)){return;}if((needsSearch&&document.getElementById(%6$s))||(needsResults&&document.getElementById(%7$s))){wrapper.classList.add("is-unavailable");if(search){search.setAttribute("tabindex","-1");search.setAttribute("aria-hidden","true");}if(results){results.setAttribute("tabindex","-1");results.setAttribute("aria-hidden","true");}return;}if(search){search.id=%6$s;}if(results){results.id=%7$s;}window.TPWL_CONFIGURATION=Object.assign({},window.TPWL_CONFIGURATION||{},%8$s);var script=document.createElement("script");script.async=true;script.type="module";script.src=%9$s;document.head.appendChild(script);}());</script>',
			$instance_id_json,
			$needs_search_json,
			$needs_results_json,
			$search_id_json,
			$results_id_json,
			$fixed_search_json,
			$fixed_results_json,
			$configuration_json,
			$script_src_json
		);
	}
}
