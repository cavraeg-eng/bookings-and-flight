<?php
/**
 * Redux_Travelpayouts Core Class
 *
 * @class Redux_Travelpayouts_Core
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Core', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Core
	 */
	class Redux_Travelpayouts_Core {

		/**
		 * Class instance.
		 *
		 * @var object
		 */
		public static $instance;

		/**
		 * Project version
		 *
		 * @var project string
		 */
		public static $version;

		/**
		 * Project directory.
		 *
		 * @var project string.
		 */
		public static $dir;

		/**
		 * Project URL.
		 *
		 * @var project URL.
		 */
		public static $url;

		/**
		 * Base directory path.
		 *
		 * @var string
		 */
		public static $Redux_Travelpayouts_path;

		/**
		 * Absolute direction path to WordPress upload directory.
		 *
		 * @var null
		 */
		public static $upload_dir = null;

		/**
		 * Full URL to WordPress upload directory.
		 *
		 * @var string
		 */
		public static $upload_url = null;

		/**
		 * Set when Redux_Travelpayouts is run as a plugin.
		 *
		 * @var bool
		 */
		public static $is_plugin = true;

		/**
		 * Indicated in_theme or in_plugin.
		 *
		 * @var string
		 */
		public static $installed = '';

		/**
		 * Set when Redux_Travelpayouts is run as a plugin.
		 *
		 * @var bool
		 */
		public static $as_plugin = false;

		/**
		 * Set when Redux_Travelpayouts is embedded within a theme.
		 *
		 * @var bool
		 */
		public static $in_theme = false;

		/**
		 * Set when Redux_Travelpayouts Pro plugin is loaded and active.
		 *
		 * @var bool
		 */
		public static $pro_loaded = false;

		/**
		 * Pointer to updated google fonts array.
		 *
		 * @var array
		 */
		public static $google_fonts = array();

		/**
		 * List of files calling Redux.
		 *
		 * @var array
		 */
		public static $callers = array();

		/**
		 * Nonce.
		 *
		 * @var string
		 */
		public static $wp_nonce;

		/**
		 * Pointer to _SERVER global.
		 *
		 * @var null
		 */
		public static $server = null;

		/**
		 * Pointer to the thirdparty fixes class.
		 *
		 * @var null
		 */
		public static $third_party_fixes = null;

		/**
		 * Creates instance of class.
		 *
		 * @return Redux_Travelpayouts_Core
		 * @throws Exception Comment.
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();

				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();
			}

			return self::$instance;
		}

		/**
		 * Class init.
		 */
		private function init() {

			self::$server = array(
				'SERVER_SOFTWARE' => '',
				'REMOTE_ADDR'     => Redux_Travelpayouts_Helpers::is_local_host() ? '127.0.0.1' : '',
				'HTTP_USER_AGENT' => '',
			);
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
				self::$server['SERVER_SOFTWARE'] = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
			}
			if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
				self::$server['REMOTE_ADDR'] = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			}
			if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
				self::$server['HTTP_USER_AGENT'] = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
			}
			// phpcs:enable

			self::$dir = trailingslashit( wp_normalize_path( dirname( realpath( __FILE__ ) ) ) );

			//Redux_Travelpayouts_Functions_Ex::generator();

			$plugin_info = Redux_Travelpayouts_Functions_Ex::is_inside_plugin( __FILE__ );

			if ( false !== $plugin_info ) {
				self::$installed = class_exists( 'Redux_Travelpayouts_Framework_Plugin' ) ? 'plugin' : 'in_plugin';
				self::$is_plugin = class_exists( 'Redux_Travelpayouts_Framework_Plugin' );
				self::$as_plugin = true;
				self::$url       = trailingslashit( dirname( $plugin_info['url'] ) );
			} else {
				$theme_info = Redux_Travelpayouts_Functions_Ex::is_inside_theme( __FILE__ );
				if ( false !== $theme_info ) {
					self::$url       = trailingslashit( dirname( $theme_info['url'] ) );
					self::$in_theme  = true;
					self::$installed = 'in_theme';
				}
			}

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$url = apply_filters( 'redux_travelpayouts/url', self::$url );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$dir = apply_filters( 'redux_travelpayouts/dir', self::$dir );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$is_plugin = apply_filters( 'redux_travelpayouts/is_plugin', self::$is_plugin );

			if ( ! function_exists( 'current_time' ) ) {
				require_once ABSPATH . '/wp-includes/functions.php';
			}

			$upload_dir       = wp_upload_dir();
			self::$upload_dir = $upload_dir['basedir'] . '/redux_travelpayouts/';
			self::$upload_url = str_replace( array( 'https://', 'http://' ), '//', $upload_dir['baseurl'] . '/redux_travelpayouts/' );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$upload_dir = apply_filters( 'redux_travelpayouts/upload_dir', self::$upload_dir );

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			self::$upload_url = apply_filters( 'redux_travelpayouts/upload_url', self::$upload_url );

		}

		/**
		 * Code to execute on framework __construct.
		 *
		 * @param object $parent Pointer to TravelpayoutsSettingsFramework object.
		 * @param array  $args Global arguments array.
		 */
		public static function core_construct( $parent, $args ) {
			self::$third_party_fixes = new Redux_Travelpayouts_ThirdParty_Fixes( $parent );
		}

		/**
		 * Autoregister run.
		 *
		 * @throws Exception Comment.
		 */
		private function includes() {
			require_once dirname( __FILE__ ) . '/inc/classes/class-travelpayouts-path.php';
			require_once dirname( __FILE__ ) . '/inc/classes/class-travelpayouts-functions-ex.php';
			require_once dirname( __FILE__ ) . '/inc/classes/class-travelpayouts-helpers.php';
			Redux_Travelpayouts_Functions_Ex::register_class_path( 'Redux', dirname( __FILE__ ) . '/inc/classes' );
			spl_autoload_register( array( $this, 'register_classes' ) );

			new Redux_Travelpayouts_Rest_Api_Builder( $this );

			$hash_arg = md5( trailingslashit( network_site_url() ) . '-redux' );
			add_action( 'wp_ajax_nopriv_' . $hash_arg, array( 'Redux_Travelpayouts_Helpers', 'hash_arg' ) );
			add_action( 'wp_ajax_' . $hash_arg, array( 'Redux_Travelpayouts_Helpers', 'hash_arg' ) );
			add_action( 'wp_ajax_Redux_Travelpayouts_support_hash', array( 'Redux_Travelpayouts_Functions', 'support_hash' ) );
        }

		/**
		 * Register callback for autoload.
		 *
		 * @param string $class_name name of class.
		 */
		public function register_classes( $class_name ) {
			$class_name_test = Redux_Travelpayouts_Core::strtolower( $class_name );

			if ( strpos( $class_name_test, 'redux' ) === false ) {
				return;
			}

			if ( ! class_exists( 'Redux_Travelpayouts_Functions_Ex' ) ) {
				require_once Redux_Travelpayouts_Path::get_path( '/inc/classes/class-travelpayouts-functions-ex.php' );
			}

			if ( ! class_exists( $class_name ) ) {
				// Backward compatibility for extensions sucks!
				if ( 'Redux_Travelpayouts_Instances' === $class_name ) {
					require_once Redux_Travelpayouts_Path::get_path( '/inc/classes/class-travelpayouts-instances.php' );
					require_once Redux_Travelpayouts_Path::get_path( '/inc/lib/redux-instances.php' );

					return;
				}

				// Load Redux_Travelpayouts APIs.
				if ( 'Redux_Travelpayouts' === $class_name ) {
					require_once Redux_Travelpayouts_Path::get_path( '/inc/classes/class-travelpayouts-api.php' );

					return;
				}

				$mappings = array(
					'TravelpayoutsSettingsFrameworkInstances'  => 'Redux_Travelpayouts_Instances',
					'reduxTpCorePanel'           => 'Redux_Travelpayouts_Panel',
					'reduxTpCoreEnqueue'         => 'Redux_Travelpayouts_Enqueue',
					'Redux_Travelpayouts_Abstract_Extension' => 'Redux_Travelpayouts_Extension_Abstract',
				);
				$alias    = false;
				if ( isset( $mappings[ $class_name ] ) ) {
					$alias      = $class_name;
					$class_name = $mappings[ $class_name ];
				}

				// Everything else.
				$file = 'class.' . $class_name_test . '.php';

				$class_path = Redux_Travelpayouts_Path::get_path( '/inc/classes/' . $file );

				if ( ! file_exists( $class_path ) ) {
					$class_file_name = str_replace( '_', '-', $class_name );
					$file            = 'class-' . $class_name_test . '.php';
					$class_path      = Redux_Travelpayouts_Path::get_path( '/inc/classes/' . $file );
				}

				if ( file_exists( $class_path ) && ! class_exists( $class_name ) ) {
					require_once $class_path;
				}
				if ( class_exists( $class_name ) && ! empty( $alias ) && ! class_exists( $alias ) ) {
					class_alias( $class_name, $alias );
				}
			}

			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'redux_travelpayouts/core/includes', $this );
		}

		/**
		 * Hooks to run on instance creation.
		 */
		private function hooks() {
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'redux_travelpayouts/core/hooks', $this );
		}

		/**
		 * Action to run on WordPress heartbeat.
		 *
		 * @return bool
		 */
		public static function is_heartbeat() {
			// Disregard WP AJAX 'heartbeat'call.  Why waste resources?
			if ( isset( $_POST ) && isset( $_POST['_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_nonce'] ) ), 'heartbeat-nonce' ) ) {
				if ( isset( $_POST['action'] ) && 'heartbeat' === sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {

					// Hook, for purists.
					if ( has_action( 'redux_travelpayouts/ajax/heartbeat' ) ) {
						// phpcs:ignore WordPress.NamingConventions.ValidHookName
						do_action( 'redux_travelpayouts/ajax/heartbeat' );
					}

					return true;
				}

				return false;
			}

			// Buh bye!
			return false;
		}

		/**
		 * Helper method to check for mb_strtolower or to use the standard strtolower.
		 *
		 * @param string $str String to make lowercase.
		 *
		 * @return string
		 */
		public static function strtolower( $str ) {
			if ( function_exists( 'mb_strtolower' ) && function_exists( 'mb_detect_encoding' ) ) {
				return mb_strtolower( $str, mb_detect_encoding( $str ) );
			} else {
				return strtolower( $str );
			}
		}
	}

	/*
	 * Backwards comparability alias
	 */
	class_alias( 'Redux_Travelpayouts_Core', 'travelpayouts-redux-core' );
}
