<?php
/**
 * Frontend shortcode and asset bootstrap.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

defined( 'ABSPATH' ) || exit;

final class Frontend_Manager {

	public const ASSET_HANDLE               = 'baf-frontend';
	public const SCRIPT_HANDLE              = 'baf-frontend-runtime';
	public const ALERT_ASSET_HANDLE         = 'baf-flight-alert';
	public const WIDGET_BLOCK_SCRIPT_HANDLE = 'baf-travelpayouts-widget-block';

	public static function bootstrap(): void {
		add_action( 'init', array( self::class, 'register_blocks' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'register_assets' ) );
		add_shortcode( 'baf_travel_cards', array( Travel_Cards_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_affiliate_disclosure', array( Affiliate_Disclosure_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_travelpayouts_white_label', array( Travelpayouts_White_Label_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_travelpayouts_hotel_widget', array( Travelpayouts_Hotel_Widget_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_travelpayouts_widget', array( Travelpayouts_Widget_Shortcode::class, 'render' ) );
		add_shortcode( 'baf_flight_alert_signup', array( Flight_Alert_Signup_Shortcode::class, 'render' ) );
		Flight_Alert_Intent_Handler::bootstrap();
		AI_Planner_Page::bootstrap();
		Saved_Trips_Page::bootstrap();
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

		$frontend_script_path    = BAF_CORE_DIR . 'assets/js/frontend.js';
		$frontend_script_version = is_readable( $frontend_script_path ) ? (string) filemtime( $frontend_script_path ) : BAF_CORE_VERSION;

		wp_register_script(
			self::SCRIPT_HANDLE,
			BAF_CORE_URL . 'assets/js/frontend.js',
			array(),
			$frontend_script_version,
			true
		);

		$alert_asset_path = BAF_CORE_DIR . 'assets/css/flight-alert.css';
		$alert_version    = is_readable( $alert_asset_path ) ? (string) filemtime( $alert_asset_path ) : BAF_CORE_VERSION;

		wp_register_style(
			self::ALERT_ASSET_HANDLE,
			BAF_CORE_URL . 'assets/css/flight-alert.css',
			array( self::ASSET_HANDLE ),
			$alert_version
		);
	}

	public static function register_blocks(): void {
		$script_path    = BAF_CORE_DIR . 'assets/js/travelpayouts-widget-block.js';
		$script_version = is_readable( $script_path ) ? (string) filemtime( $script_path ) : BAF_CORE_VERSION;

		wp_register_script(
			self::WIDGET_BLOCK_SCRIPT_HANDLE,
			BAF_CORE_URL . 'assets/js/travelpayouts-widget-block.js',
			array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
			$script_version,
			true
		);

		register_block_type(
			'baf/travelpayouts-widget',
			array(
				'api_version'     => 2,
				'title'           => __( 'Travelpayouts Widget', 'bookings-flights-core' ),
				'description'     => __( 'Render an approved Bookings and Flights travel placement by registry key.', 'bookings-flights-core' ),
				'category'        => 'widgets',
				'icon'            => 'airplane',
				'editor_script'   => self::WIDGET_BLOCK_SCRIPT_HANDLE,
				'render_callback' => array( self::class, 'render_widget_block' ),
				'attributes'      => array(
					'placement' => array(
						'type'    => 'string',
						'default' => '',
					),
					'surface'   => array(
						'type'    => 'string',
						'default' => '',
					),
					'channel'   => array(
						'type'    => 'string',
						'default' => '',
					),
					'slug'      => array(
						'type'    => 'string',
						'default' => '',
					),
				),
			)
		);
	}

	public static function render_widget_block( array $attributes ): string {
		return Travelpayouts_Widget_Renderer::render( $attributes );
	}
}
