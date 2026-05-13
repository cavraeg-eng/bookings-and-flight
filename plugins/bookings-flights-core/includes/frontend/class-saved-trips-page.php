<?php
/**
 * Saved trips frontend route.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Services\Saved_Trip_Service;

defined( 'ABSPATH' ) || exit;

final class Saved_Trips_Page {

	private const QUERY_VAR              = 'baf_saved_trips';
	private const REWRITE_VERSION_OPTION = 'baf_saved_trips_rewrite_version';
	private const SCRIPT_HANDLE          = 'baf-saved-trips';
	private const STYLE_HANDLE           = 'baf-saved-trips';

	public static function bootstrap(): void {
		add_action( 'init', array( self::class, 'register_rewrite' ) );
		add_action( 'init', array( self::class, 'maybe_flush_rewrites' ), 20 );
		add_filter( 'query_vars', array( self::class, 'register_query_var' ) );
		add_filter( 'body_class', array( self::class, 'body_class' ) );
		add_filter( 'template_include', array( self::class, 'template_include' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
	}

	public static function register_rewrite(): void {
		add_rewrite_rule( '^saved-trips/?$', 'index.php?' . self::QUERY_VAR . '=1', 'top' );
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

	public static function body_class( array $classes ): array {
		if ( self::is_saved_trips_request() ) {
			$classes[] = 'baf-saved-trips-route';
		}

		return $classes;
	}

	public static function template_include( string $template ): string {
		if ( ! self::is_saved_trips_request() ) {
			return $template;
		}

		global $wp_query;

		if ( $wp_query instanceof \WP_Query ) {
			$wp_query->is_404 = false;
		}

		status_header( 200 );

		return BAF_CORE_DIR . 'templates/saved-trips-page.php';
	}

	public static function enqueue_assets(): void {
		if ( ! self::is_saved_trips_request() ) {
			return;
		}

		$style_path  = BAF_CORE_DIR . 'assets/css/saved-trips.css';
		$script_path = BAF_CORE_DIR . 'assets/js/saved-trips.js';

		wp_enqueue_style(
			self::STYLE_HANDLE,
			BAF_CORE_URL . 'assets/css/saved-trips.css',
			array(),
			is_readable( $style_path ) ? (string) filemtime( $style_path ) : BAF_CORE_VERSION
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			BAF_CORE_URL . 'assets/js/saved-trips.js',
			array(),
			is_readable( $script_path ) ? (string) filemtime( $script_path ) : BAF_CORE_VERSION,
			true
		);

		wp_localize_script( self::SCRIPT_HANDLE, 'bafSavedTrips', self::script_data() );
	}

	public static function is_saved_trips_request(): bool {
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

		return '/saved-trips/' === trailingslashit( '/' . trim( $request_path, '/' ) );
	}

	private static function script_data(): array {
		$resume_trip_id = 0;

		if ( isset( $_GET['resume_trip'] ) && is_scalar( $_GET['resume_trip'] ) ) {
			$resume_trip_id = absint( wp_unslash( $_GET['resume_trip'] ) );
		}

		return array(
			'endpoint'     => esc_url_raw( rest_url( 'baf/v1/saved-trips' ) ),
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'isLoggedIn'   => is_user_logged_in(),
			'loginUrl'     => wp_login_url( home_url( '/saved-trips/' ) ),
			'resumeTripId' => $resume_trip_id,
			'urls'         => array(
				'flights' => esc_url_raw( home_url( '/flights/' ) ),
				'hotels'  => esc_url_raw( home_url( '/hotels/' ) ),
				'planner' => esc_url_raw( home_url( '/trip-planner/' ) ),
			),
			'strings'      => array(
				'confirmDelete' => __( 'Delete this saved trip intent from WordPress?', 'bookings-flights-core' ),
				'deleted'       => __( 'Saved trip intent deleted.', 'bookings-flights-core' ),
				'empty'         => __( 'No saved trips yet.', 'bookings-flights-core' ),
				'genericError'  => __( 'Saved trips could not be updated. Please try again.', 'bookings-flights-core' ),
				'loaded'        => __( 'Saved trip intent loaded for editing.', 'bookings-flights-core' ),
				'loading'       => __( 'Loading saved trips...', 'bookings-flights-core' ),
				'loginRequired' => __( 'Sign in before saving trip intent locally.', 'bookings-flights-core' ),
				'saved'         => __( 'Saved trip intent stored locally in WordPress.', 'bookings-flights-core' ),
				'updated'       => __( 'Saved trip intent updated.', 'bookings-flights-core' ),
			),
		);
	}
}
