<?php
/**
 * Travelpayouts Hotels & Accommodation widget shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class Travelpayouts_Hotel_Widget_Shortcode {

	public static function render( $attributes = array() ): string {
		$settings   = Settings_Manager::get_travelpayouts();
		$script_url = (string) ( $settings['hotel_widget_script_url'] ?? '' );

		if ( '' === $script_url ) {
			if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
				return '<p class="baf-notice baf-notice--warning">' . esc_html__( 'Travelpayouts Trip.com hotel widget script is not configured.', 'bookings-flights-core' ) . '</p>';
			}

			return '';
		}

		$consent = Settings_Manager::get_consent();

		if ( true !== (bool) $consent['allow_provider_requests'] ) {
			if ( current_user_can( Capability_Manager::MANAGE_SETTINGS ) ) {
				return '<p class="baf-notice baf-notice--warning">' . esc_html__( 'Travelpayouts Trip.com hotel widget is configured, but provider request consent is disabled.', 'bookings-flights-core' ) . '</p>';
			}

			return '';
		}

		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		if ( self::is_tripcom_partner_embed_url( $script_url ) ) {
			return sprintf(
				'<div class="baf-travelpayouts-hotel-widget is-loaded" data-provider="tripcom"><div class="baf-travelpayouts-hotel-widget__frame"><iframe src="%1$s" title="%2$s" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div><p class="baf-travelpayouts-hotel-widget__handoff"><a href="%1$s" target="_blank" rel="nofollow sponsored noopener noreferrer">%3$s</a></p></div>',
				esc_url( $script_url ),
				esc_attr__( 'Trip.com hotel search', 'bookings-flights-core' ),
				esc_html__( 'Open hotel search', 'bookings-flights-core' )
			);
		}

		$wrapper_id      = wp_unique_id( 'baf-tripcom-widget-' );
		$wrapper_id_json = wp_json_encode( $wrapper_id );
		if ( false === $wrapper_id_json ) {
			return '';
		}

		return sprintf(
			'<div class="baf-travelpayouts-hotel-widget" id="%1$s" data-provider="tripcom"><script async src="%2$s" data-noptimize="1" data-cfasync="false" data-wpfc-render="false"></script><div class="baf-travelpayouts-hotel-widget__fallback" role="status">%3$s</div><noscript><p class="baf-travelpayouts-hotel-widget__noscript">%4$s</p></noscript></div><script data-noptimize="1" data-cfasync="false" data-wpfc-render="false">(function(){var wrapperId=%5$s;window.setTimeout(function(){var wrapper=document.getElementById(wrapperId);if(!wrapper){return;}var hasFrame=!!wrapper.querySelector("iframe");wrapper.classList.toggle("is-loaded",hasFrame);wrapper.classList.toggle("is-unavailable",!hasFrame);},2500);}());</script>',
			esc_attr( $wrapper_id ),
			esc_url( $script_url ),
			esc_html__( 'Hotel search could not load in this browser. Try refreshing the page or disabling content blockers for this site.', 'bookings-flights-core' ),
			esc_html__( 'Enable JavaScript to load the Trip.com hotel search widget.', 'bookings-flights-core' ),
			$wrapper_id_json
		);
	}

	private static function is_tripcom_partner_embed_url( string $url ): bool {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$path = strtolower( (string) wp_parse_url( $url, PHP_URL_PATH ) );

		return ( 'trip.com' === $host || str_ends_with( $host, '.trip.com' ) ) && str_starts_with( $path, '/partners/ad/' );
	}
}
