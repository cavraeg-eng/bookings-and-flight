<?php
/**
 * AI planner frontend route.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

final class AI_Planner_Page {

	private const QUERY_VAR              = 'baf_ai_planner';
	private const REWRITE_VERSION_OPTION = 'baf_ai_planner_rewrite_version';
	private const SCRIPT_HANDLE          = 'baf-ai-planner';
	private const STYLE_HANDLE           = 'baf-ai-planner';

	public static function bootstrap(): void {
		add_action( 'init', array( self::class, 'register_rewrite' ) );
		add_action( 'init', array( self::class, 'maybe_flush_rewrites' ), 20 );
		add_filter( 'query_vars', array( self::class, 'register_query_var' ) );
		add_filter( 'template_include', array( self::class, 'template_include' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	public static function register_rewrite(): void {
		add_rewrite_rule( '^trip-planner/?$', 'index.php?' . self::QUERY_VAR . '=1', 'top' );
	}

	public static function maybe_flush_rewrites(): void {
		if ( BAF_CORE_VERSION === (string) get_option( self::REWRITE_VERSION_OPTION, '' ) ) {
			return;
		}

		flush_rewrite_rules( false );
		update_option( self::REWRITE_VERSION_OPTION, BAF_CORE_VERSION, false );
	}

	public static function register_query_var( array $vars ): array {
		$vars[] = self::QUERY_VAR;

		return $vars;
	}

	public static function template_include( string $template ): string {
		if ( ! self::is_planner_request() ) {
			return $template;
		}

		global $wp_query;

		if ( $wp_query instanceof \WP_Query ) {
			$wp_query->is_404 = false;
		}

		status_header( 200 );

		return BAF_CORE_DIR . 'templates/ai-planner-page.php';
	}

	public static function enqueue_assets(): void {
		if ( ! self::is_planner_request() ) {
			return;
		}

		$style_path  = BAF_CORE_DIR . 'assets/css/ai-planner.css';
		$script_path = BAF_CORE_DIR . 'assets/js/ai-planner.js';

		wp_enqueue_style(
			self::STYLE_HANDLE,
			BAF_CORE_URL . 'assets/css/ai-planner.css',
			array(),
			is_readable( $style_path ) ? (string) filemtime( $style_path ) : BAF_CORE_VERSION
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			BAF_CORE_URL . 'assets/js/ai-planner.js',
			array(),
			is_readable( $script_path ) ? (string) filemtime( $script_path ) : BAF_CORE_VERSION,
			true
		);

		wp_localize_script( self::SCRIPT_HANDLE, 'bafAiPlanner', self::script_data() );
	}

	public static function is_planner_request(): bool {
		if ( '1' === (string) get_query_var( self::QUERY_VAR ) ) {
			return true;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$request_path = wp_parse_url( $request_uri, PHP_URL_PATH );

		if ( ! is_string( $request_path ) ) {
			return false;
		}

		$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
		$home_path = is_string( $home_path ) ? untrailingslashit( $home_path ) : '';

		if ( '' !== $home_path && '/' !== $home_path && 0 === strpos( $request_path, $home_path . '/' ) ) {
			$request_path = substr( $request_path, strlen( $home_path ) );
		}

		return '/trip-planner/' === trailingslashit( '/' . trim( $request_path, '/' ) );
	}

	private static function script_data(): array {
		$ai      = Settings_Manager::get_ai();
		$consent = Settings_Manager::get_consent();

		return array(
			'endpoint'          => esc_url_raw( rest_url( 'baf/v1/ai/itinerary' ) ),
			'nonce'             => wp_create_nonce( 'wp_rest' ),
			'canRunAi'          => current_user_can( Capability_Manager::RUN_AI ),
			'canEditContent'    => current_user_can( Capability_Manager::EDIT_CONTENT ),
			'mode'              => sanitize_key( (string) $ai['mode'] ),
			'externalAiAllowed' => (bool) $consent['allow_external_ai'],
			'strings'           => array(
				'capabilityRequired' => __( 'AI planning is available to signed-in editors with AI permission in this phase.', 'bookings-flights-core' ),
				'consentRequired'    => __( 'Live AI mode needs the external AI consent checkbox before any prompt data can leave WordPress.', 'bookings-flights-core' ),
				'draftSaved'         => __( 'Trip brief ready and saved as an editable draft. Review it before publishing.', 'bookings-flights-core' ),
				'genericError'       => __( 'The planner could not create a trip brief. Review the fields and try again.', 'bookings-flights-core' ),
				'loading'            => __( 'Preparing a structured trip brief...', 'bookings-flights-core' ),
			),
		);
	}
}
