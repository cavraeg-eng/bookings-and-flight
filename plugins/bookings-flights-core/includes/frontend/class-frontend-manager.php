<?php
/**
 * Frontend shortcode and asset bootstrap.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

defined( 'ABSPATH' ) || exit;

final class Frontend_Manager {

	public const ASSET_HANDLE = 'baf-frontend';

	public static function bootstrap(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'register_assets' ) );
		add_shortcode( 'baf_travel_cards', array( Travel_Cards_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_affiliate_disclosure', array( Affiliate_Disclosure_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_travelpayouts_white_label', array( Travelpayouts_White_Label_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_travelpayouts_hotel_widget', array( Travelpayouts_Hotel_Widget_Shortcode::class, 'render' ) );
	}

	public static function register_assets(): void {
		$frontend_asset_path = BAF_CORE_DIR . 'assets/css/frontend.css';
		$frontend_version    = is_readable( $frontend_asset_path ) ? (string) filemtime( $frontend_asset_path ) : BAF_CORE_VERSION;

		wp_register_style(
			self::ASSET_HANDLE,
			BAF_CORE_URL . 'assets/css/frontend.css',
			array(),
			$frontend_version
		);
	}
}
