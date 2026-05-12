<?php
/**
 * Redux_Travelpayouts Helper Class
 *
 * @class   Redux_Travelpayouts_Helpers
 * @version 3.0.0
 * @package Redux_Travelpayouts Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'Redux_Travelpayouts_Helpers', false ) ) {

	/**
	 * Redux_Travelpayouts Helpers Class
	 * Class of useful functions that can/should be shared among all Redux_Travelpayouts files.
	 *
	 * @since       3.0.0
	 */
	class Redux_Travelpayouts_Helpers {

		/**
		 * Resuable supported unit array.
		 *
		 * @var array
		 */
		public static $array_units = array( '', '%', 'in', 'cm', 'mm', 'em', 'rem', 'ex', 'pt', 'pc', 'px', 'vh', 'vw', 'vmin', 'vmax', 'ch' );

		/**
		 * Retrieve section array from field ID.
		 *
		 * @param string $opt_name Panel opt_name.
		 * @param string $field_id Field ID.
		 */
		public static function section_from_field_id( $opt_name = '', $field_id = '' ) {
			if ( '' !== $opt_name ) {
				$redux = Redux_Travelpayouts::instance( $opt_name );

				if ( is_object( $redux ) ) {
					$sections = $redux->sections;

					if ( is_array( $sections ) && ! empty( $sections ) ) {
						foreach ( $sections as $idx => $section ) {
							if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
								foreach ( $section['fields'] as $i => $field ) {
									if ( is_array( $field ) && ! empty( $field ) ) {
										if ( isset( $field['id'] ) && $field['id'] === $field_id ) {
											return $section;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Verify integer value.
		 *
		 * @param mixed $val Value to test.
		 *
		 * @return bool|false|int
		 */
		public static function is_integer( $val ) {
			if ( ! is_scalar( $val ) || is_bool( $val ) ) {
				return false;
			}

			return is_float( $val ) ? false : preg_match( '~^((?:\+|-)?[0-9]+)$~', $val );
		}

		/**
		 * Deprecated. Gets panel tab number from specified field.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework object.
		 * @param array  $field  Field array.
		 *
		 * @return int|string
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function tabFromField( $parent, $field ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Helpers::tab_from_field( $parent, $field )' );

			return self::tab_from_field( $parent, $field );
		}

		/**
		 * Gets panel tab number from specified field.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework object.
		 * @param array  $field  Field array.
		 *
		 * @return int|string
		 */
		public static function tab_from_field( $parent, $field ) {
			foreach ( $parent->sections as $k => $section ) {
				if ( ! isset( $section['title'] ) ) {
					continue;
				}

				if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
					if ( self::recursive_array_search( $field, $section['fields'] ) ) {
						return $k;
					}
				}
			}
		}

		/**
		 * Deprecated. Verifies if specified field type is in use.
		 *
		 * @param object $fields Field arrays.
		 * @param array  $field  Field array.
		 *
		 * @return int|string
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function isFieldInUseByType( $fields, $field = array() ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			// TODO - Uncomment this at release.
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			// _deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Helpers::tab_from_field( $parent, $field )' );
			return self::is_field_in_use_by_type( $fields, $field );
		}

		/**
		 * Verifies if specified field type is in use.
		 *
		 * @param array $fields Field arrays.
		 * @param array $field  Field array to check.
		 *
		 * @return bool
		 */
		public static function is_field_in_use_by_type( $fields, $field = array() ) {
			foreach ( $field as $name ) {
				if ( array_key_exists( $name, $fields ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Deprecated Verifies if field is in use.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework object.
		 * @param array  $field  Field type.
		 *
		 * @return bool
		 * @deprecated No longer using camelCase function names.
		 */
		public static function isFieldInUse( $parent, $field ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			// TODO - Uncomment this at release.
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			// _deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Helpers::is_field_in_use( $parent, $field )' );
			return self::is_field_in_use( $parent, $field );
		}

		/**
		 * Verifies if field is in use.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework object.
		 * @param array  $field  Field type.
		 *
		 * @return bool
		 */
		public static function is_field_in_use( $parent, $field ) {
			if ( empty( $parent->sections ) ) {
				return;
			}
			foreach ( $parent->sections as $k => $section ) {
				if ( ! isset( $section['title'] ) ) {
					continue;
				}

				if ( isset( $section['fields'] ) && ! empty( $section['fields'] ) ) {
					if ( self::recursive_array_search( $field, $section['fields'] ) ) {
						return true;
					}
				}
			}
		}

		/**
		 * Returns major version from version number.
		 *
		 * @param string $v Version number.
		 *
		 * @return string
		 */
		public static function major_version( $v ) {
			$version = explode( '.', $v );
			if ( count( $version ) > 1 ) {
				return $version[0] . '.' . $version[1];
			} else {
				return $v;
			}
		}


		/**
		 * Deprecated. Checks for localhost environment.
		 *
		 * @return Redux_Travelpayouts_Helpers::is_local_host()
		 * @deprecated No longer using camelCase naming convention.
		 * @since      4.0
		 */
		public static function isLocalHost() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Helpers::is_local_host()' );

			return self::is_local_host();
		}

		/**
		 * Checks for localhost environment.
		 *
		 * @return bool
		 */
		public static function is_local_host() {
			$is_local = false;

			$domains_to_check = array_unique(
				array(
					'siteurl' => wp_parse_url( get_site_url(), PHP_URL_HOST ),
					'homeurl' => wp_parse_url( get_home_url(), PHP_URL_HOST ),
				)
			);

			$forbidden_domains = array(
				'wordpress.com',
				'localhost',
				'localhost.localdomain',
				'127.0.0.1',
				'::1',
				'local.wordpress.test',         // VVV pattern.
				'local.wordpress-trunk.test',   // VVV pattern.
				'src.wordpress-develop.test',   // VVV pattern.
				'build.wordpress-develop.test', // VVV pattern.
			);

			foreach ( $domains_to_check as $domain ) {
				// If it's empty, just fail out.
				if ( ! $domain ) {
					$is_local = true;
					break;
				}

				// None of the explicit localhosts.
				if ( in_array( $domain, $forbidden_domains, true ) ) {
					$is_local = true;
					break;
				}

				// No .test or .local domains.
				if ( preg_match( '#\.(test|local)$#i', $domain ) ) {
					$is_local = true;
					break;
				}
			}

			return $is_local;
		}

		/**
		 * Deprecated. Checks if WP_DEBUG is enabled.
		 *
		 * @return Redux_Travelpayouts_Helpers::is_wp_debug()
		 * @deprecated No longer using camelCase naming convention.
		 * @since      4.0
		 */
		public static function isWpDebug() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Functions_Ex::is_wp_debug()' );

			return self::is_wp_debug();
		}

		/**
		 * Checks if WP_DEBUG is enabled.
		 *
		 * @return bool
		 */
		public static function is_wp_debug() {
			return ( defined( 'WP_DEBUG' ) && true === WP_DEBUG );
		}

		/**
		 * Get extensions.
		 *
		 * @param string $opt_name Panel opt_name.
		 *
		 * @return array
		 */
		public static function get_extensions( $opt_name = '' ) {
			if ( empty( $opt_name ) ) {
				$instances = Redux_Travelpayouts_Instances::get_all_instances();
			} else {
				$instances = array(
					Redux_Travelpayouts_Instances::get_instance( $opt_name ),
				);
			}

			$extensions = array();

			if ( ! empty( $instances ) ) {
				foreach ( $instances as $instance ) {
					if ( isset( $instance->extensions ) && is_array( $instance->extensions ) && ! empty( $instance->extensions ) ) {
						foreach ( $instance->extensions as $key => $extension ) {
							if ( in_array(
								$key,
								array(
									'metaboxes_lite',
									'import_export',
									'customizer',
									'options_object',
								),
								true
							)
							) {
								continue;
							}

							if ( isset( $extension::$version ) ) {
								$extensions[ $key ] = $extension::$version;
							} elseif ( isset( $extension->version ) ) {
								$extensions[ $key ] = $extension->version;
							} else {
								$extensions[ $key ] = true;
							}
						}
					}
				}
			}

			return $extensions;

		}

		/**
		 * Deprecated. Determines if theme is parent.
		 *
		 * @param string $file Path to file.
		 *
		 * @return Redux_Travelpayouts_Instances::isParentTheme( $file )
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function isParentTheme( $file ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::is_parent_theme( $file )' );

			return self::is_parent_theme( $file );
		}

		/**
		 * Determines if theme is parent.
		 *
		 * @param string $file Path to theme dir.
		 *
		 * @return bool
		 */
		public static function is_parent_theme( $file ) {
			$file = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file );
			$dir  = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( get_template_directory() );

			$file = str_replace( '//', '/', $file );
			$dir  = str_replace( '//', '/', $dir );

			if ( strpos( $file, $dir ) !== false ) {
				return true;
			}

			return false;
		}

		/**
		 * Deprecated. Moved to another class.
		 *
		 * @param string $file Path to file.
		 *
		 * @return Redux_Travelpayouts_Instances::wp_normalize_path( $file )
		 * @deprecated Moved to another class.
		 */
		public static function wp_normalize_path( $file ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file )' );

			return Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file );
		}

		/**
		 * Deprecated. Determines if theme is child.
		 *
		 * @param string $file Path to file.
		 *
		 * @return Redux_Travelpayouts_Instances::is_child_theme( $file )
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function isChildTheme( $file ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::is_child_theme( $file )' );

			return self::is_child_theme( $file );
		}

		/**
		 * Deprecated. Returns true if Redux_Travelpayouts is running as a plugin.
		 *
		 * @return Redux_Travelpayouts_Helpers::()
		 * @deprecated No longer using camelCase naming convention.
		 */
		private static function reduxAsPlugin() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Core::$as_plugin()' );

			return Redux_Travelpayouts_Core::$as_plugin;
		}

		/**
		 * Determines if theme is child.
		 *
		 * @param string $file Path to theme dir.
		 *
		 * @return bool
		 */
		public static function is_child_theme( $file ) {
			$file = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file );
			$dir  = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( get_stylesheet_directory() );

			$file = str_replace( '//', '/', $file );
			$dir  = str_replace( '//', '/', $dir );

			if ( strpos( $file, $dir ) !== false ) {
				return true;
			}

			return false;
		}

		/**
		 * Deprecated. Determines if file is a theme.
		 *
		 * @param string $file Path to file.
		 *
		 * @return Redux_Travelpayouts_Instances::is_theme( $file )
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function isTheme( $file ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			// _deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::is_theme( $file )' );

			return self::is_theme( $file );
		}

		/**
		 * Determines if file is a theme.
		 *
		 * @param string $file Path to fle to test.
		 *
		 * @return bool
		 */
		public static function is_theme( $file ) {
			if ( true === self::is_child_theme( $file ) || true === self::is_parent_theme( $file ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Determines deep array status.
		 *
		 * @param array $needle   array to test.
		 * @param array $haystack Array to search.
		 *
		 * @return bool
		 */
		public static function array_in_array( $needle, $haystack ) {
			// Make sure $needle is an array for foreach.
			if ( ! is_array( $needle ) ) {
				$needle = array( $needle );
			}
			// For each value in $needle, return TRUE if in $haystack.
			foreach ( $needle as $pin ) {
				if ( in_array( $pin, $haystack, true ) ) {
					return true;
				}
			}

			// Return FALSE if none of the values from $needle are found in $haystack.
			return false;
		}

		/**
		 * Enum through an entire deep array.
		 *
		 * @param string $needle   String to search for.
		 * @param array  $haystack Array in which to search.
		 *
		 * @return bool
		 */
		public static function recursive_array_search( $needle, $haystack ) {
			foreach ( $haystack as $key => $value ) {
				if ( $needle === $value || ( is_array( $value ) && self::recursive_array_search( $needle, $value ) !== false ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Take a path and return it clean.
		 *
		 * @param string $path Path to clean.
		 *
		 * @return Redux_Travelpayouts_Functions_Ex::wp_normalize_path($path)
		 * @deprecated Replaced with wp_normalize_path.
		 * @since      3.1.7
		 */
		public static function cleanFilePath( $path ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			// TODO - Uncomment this at release.
			// phpcs:ignore Squiz.PHP.CommentedOutCode
			// _deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0', 'Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $path )' );
			return Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $path );
		}

		/**
		 * Create unique hash.
		 *
		 * @return string
		 */
		public static function get_hash() {
			$remote_addr = isset( Redux_Travelpayouts_Core::$server['REMOTE_ADDR'] )
				? Redux_Travelpayouts_Core::$server['REMOTE_ADDR']
				: '127.0.0.1';
			return md5( network_site_url() . '-' . $remote_addr );
		}

		/**
		 * Return array of installed themes.
		 *
		 * @return array
		 */
		public static function get_wp_themes() {
			global $wp_theme_paths;

			$wp_theme_paths = array();
			$themes         = wp_get_themes();
			$theme_paths    = array();

			foreach ( $themes as $theme ) {
				$path          = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( trailingslashit( $theme->get_theme_root() ) . $theme->get_template() );
				$theme_paths[] = $path;

				if ( Redux_Travelpayouts_Functions_Ex::wp_normalize_path( realpath( $path ) ) !== $path ) {
					$theme_paths[] = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( realpath( $path ) );
				}

				$wp_theme_paths[ $path ] = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( realpath( $path ) );
			}

			return array(
				'full_paths'  => $wp_theme_paths,
				'theme_paths' => $theme,
			);
		}

		/**
		 * Get info for specified file.
		 *
		 * @param string $file File to check.
		 *
		 * @return array|bool
		 */
		public static function path_info( $file ) {
			$theme_info  = Redux_Travelpayouts_Functions_Ex::is_inside_theme( $file );
			$plugin_info = Redux_Travelpayouts_Functions_Ex::is_inside_plugin( $file );

			if ( false !== $theme_info ) {
				return $theme_info;
			} elseif ( false !== $plugin_info ) {
				return $plugin_info;
			}

			return array();
		}

		/**
		 * Compiles caller data for Redux.
		 *
		 * @param book $simple Mode.
		 *
		 * @return array
		 */
		public static function process_Redux_Travelpayouts_callers( $simple = false ) {
			$data = array();

			foreach ( Redux_Travelpayouts_Core::$callers as $opt_name => $callers ) {
				foreach ( $callers as $caller ) {
					$plugin_info = self::is_inside_plugin( $caller );
					$theme_info  = self::is_inside_theme( $caller );

					if ( $theme_info ) {
						if ( ! isset( $data['theme'][ $theme_info['slug'] ] ) ) {
							$data['theme'][ $theme_info['slug'] ] = array();
						}
						if ( ! isset( $data['theme'][ $theme_info['slug'] ][ $opt_name ] ) ) {
							$data['theme'][ $theme_info['slug'] ][ $opt_name ] = array();
						}
						if ( $simple ) {
							$data['theme'][ $theme_info['slug'] ][ $opt_name ][] = $theme_info['basename'];
						} else {
							$data['theme'][ $theme_info['slug'] ][ $opt_name ][] = $theme_info;
						}
					} elseif ( $plugin_info ) {
						if ( ! isset( $data['plugin'][ $plugin_info['slug'] ] ) ) {
							$data['plugin'][ $plugin_info['slug'] ] = array();
						}
						if ( ! in_array( $opt_name, $data['plugin'][ $plugin_info['slug'] ], true ) ) {
							if ( ! isset( $data['plugin'][ $plugin_info['slug'] ][ $opt_name ] ) ) {
								$data['plugin'][ $plugin_info['slug'] ][ $opt_name ] = array();
							}
							if ( $simple ) {
								$data['plugin'][ $plugin_info['slug'] ][ $opt_name ][] = $plugin_info['basename'];
							} else {
								$data['plugin'][ $plugin_info['slug'] ][ $opt_name ][] = $plugin_info;
							}
						}
					} else {
						continue;
					}
				}
			}

			return $data;
		}

		/**
		 * Take a path and delete it
		 *
		 * @param string $dir Dir to remove.
		 *
		 * @since    3.3.3
		 */
		public static function rmdir( $dir ) {
			if ( is_dir( $dir ) ) {
				$objects = scandir( $dir );

				foreach ( $objects as $object ) {
					if ( '.' !== $object && '..' !== $object ) {
						if ( filetype( $dir . '/' . $object ) === 'dir' ) {
							rmdir( $dir . '/' . $object );
						} else {
							unlink( $dir . '/' . $object );
						}
					}
				}

				reset( $objects );

				rmdir( $dir );
			}
		}

		/**
		 * Field Render Function.
		 * Takes the color hex value and converts to a rgba.
		 *
		 * @param string $hex   Color value.
		 * @param string $alpha Alpha value.
		 *
		 * @since TravelpayoutsSettingsFramework 3.0.4
		 */
		public static function hex2rgba( $hex, $alpha = '' ) {
			$hex = str_replace( '#', '', $hex );
			if ( 3 === strlen( $hex ) ) {
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			$rgb = $r . ',' . $g . ',' . $b;

			if ( '' === $alpha ) {
				return $rgb;
			} else {
				$alpha = floatval( $alpha );

				return 'rgba(' . $rgb . ',' . $alpha . ')';
			}
		}

		/**
		 * Deprecated. Returns string boolean value.
		 *
		 * @param string $var String to convert to true boolean.
		 *
		 * @return Redux_Travelpayouts_Helpers::make_bool_str( $var )
		 * @deprecated No longer using camelCase naming convention.
		 */
		public static function makeBoolStr( $var ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::make_bool_str( $var )' );

			return self::make_bool_str( $var );
		}

		/**
		 * Returns string boolean value.
		 *
		 * @param mixed $var true|false to convert.
		 *
		 * @return string
		 */
		public static function make_bool_str( $var ) {
			if ( false === $var || 'false' === $var || 0 === $var || '0' === $var || '' === $var || empty( $var ) ) {
				return 'false';
			} elseif ( true === $var || 'true' === $var || 1 === $var || '1' === $var ) {
				return 'true';
			} else {
				return $var;
			}
		}

		/**
		 * Compile localized array.
		 *
		 * @param array $localize Array of localized strings.
		 *
		 * @return mixed
		 */
		public static function localize( $localize ) {
			$redux = Redux_Travelpayouts::instance( $localize['args']['opt_name'] );
			$nonce = wp_create_nonce( 'redux-ads-nonce' );
			$base  = admin_url( 'admin-ajax.php' ) . '?t=' . $redux->core_thread . '&action=Redux_Travelpayouts_p&nonce=' . $nonce . '&url=';

			return $localize;
		}

		/**
		 * Check mokama.
		 *
		 * @access public
		 * @since 4.0.0
		 */
		public static function mokama() {
			return defined( 'RDX_MOKAMA' );
		}

		/**
		 * Deprecated. Returns array of Redux_Travelpayouts templates.
		 *
		 * @param string $custom_template_path Path to custom template.
		 *
		 * @return Redux_Travelpayouts_Helpers::get_Redux_Travelpayouts_templates( $custom_template_path )
		 * @deprecated No longer using camelCase naming convention.
		 */
		private static function getReduxTemplates( $custom_template_path ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::get_Redux_Travelpayouts_templates( $custom_template_path )' );

			return self::get_Redux_Travelpayouts_templates( $custom_template_path );
		}

		/**
		 * Returns array of Redux_Travelpayouts templates.
		 *
		 * @param string $custom_template_path Path to template dir.
		 *
		 * @return array
		 */
		private static function get_Redux_Travelpayouts_templates( $custom_template_path ) {
			$filesystem         = Redux_Travelpayouts_Filesystem::get_instance();
			$template_paths     = array( 'TravelpayoutsSettingsFramework' => Redux_Travelpayouts_Core::$dir . 'templates/panel' );
			$scanned_files      = array();
			$found_files        = array();
			$outdated_templates = false;

			foreach ( $template_paths as $plugin_name => $template_path ) {
				$scanned_files[ $plugin_name ] = self::scan_template_files( $template_path );
			}

			foreach ( $scanned_files as $plugin_name => $files ) {
				foreach ( $files as $file ) {
					if ( file_exists( $custom_template_path . '/' . $file ) ) {
						$theme_file = $custom_template_path . '/' . $file;
					} else {
						$theme_file = false;
					}

					if ( $theme_file ) {
						$core_version  = self::get_template_version( Redux_Travelpayouts_Core::$dir . 'templates/panel/' . $file );
						$theme_version = self::get_template_version( $theme_file );

						if ( $core_version && ( empty( $theme_version ) || version_compare( $theme_version, $core_version, '<' ) ) ) {
							if ( ! $outdated_templates ) {
								$outdated_templates = true;
							}

							$found_files[ $plugin_name ][] = sprintf( '<code>%s</code> ' . esc_html__( 'version', 'redux-framework' ) . ' <strong style="color:red">%s</strong> ' . esc_html__( 'is out of date. The core version is', 'redux-framework' ) . ' %s', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ), $theme_version ? $theme_version : '-', $core_version );
						} else {
							$found_files[ $plugin_name ][] = sprintf( '<code>%s</code>', str_replace( WP_CONTENT_DIR . '/themes/', '', $theme_file ) );
						}
					}
				}
			}

			return $found_files;
		}

		/**
		 * Scan template files for ver changes.
		 *
		 * @param string $template_path Path to templates.
		 *
		 * @return array
		 */
		private static function scan_template_files( $template_path ) {
			$files  = scandir( $template_path );
			$result = array();

			if ( $files ) {
				foreach ( $files as $key => $value ) {
					if ( ! in_array( $value, array( '.', '..' ), true ) ) {
						if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
							$sub_files = Redux_Travelpayouts_scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value );
							foreach ( $sub_files as $sub_file ) {
								$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
							}
						} else {
							$result[] = $value;
						}
					}
				}
			}

			return $result;
		}

		/**
		 * Retrieves template version.
		 *
		 * @param string $file Path to template file.
		 *
		 * @return string
		 */
		public static function get_template_version( $file ) {
			$filesystem = Redux_Travelpayouts_Filesystem::get_instance();
			// Avoid notices if file does not exist.
			if ( ! file_exists( $file ) ) {
				return '';
			}

			$data = get_file_data( $file, array( 'version' ), 'plugin' );

			if ( ! empty( $data[0] ) ) {
				return $data[0];
			} else {
				$file_data = $filesystem->get_contents( $file );

				$file_data = str_replace( "\r", "\n", $file_data );
				$version   = '1.0.0';

				if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
					$version = _cleanup_header_comment( $match[1] );
				}

				return $version;
			}
		}

		/**
		 * Create HTML attribute string.
		 *
		 * @param array $attributes Array of attributes.
		 */
		public static function html_attributes( $attributes = array() ) {
			$string = join(
				' ',
				array_map(
					function ( $key ) use ( $attributes ) {
						if ( is_bool( $attributes[ $key ] ) ) {
							return $attributes[ $key ] ? $key : '';
						}

						return $key . '="' . $attributes[ $key ] . '"';
					},
					array_keys( $attributes )
				)
			) . ' ';
		}

		/**
		 * Output filesize based on letter indicator.
		 *
		 * @param string $size Size with letter.
		 *
		 * @return bool|int|string
		 */
		private static function let_to_num( $size ) {
			$l   = substr( $size, - 1 );
			$ret = substr( $size, 0, - 1 );

			switch ( strtoupper( $l ) ) {
				case 'P':
					$ret *= 1024;
					// Must remain recursive, do not use 'break'.
				case 'T':
					$ret *= 1024;
					// Must remain recursive, do not use 'break'.
				case 'G':
					$ret *= 1024;
					// Must remain recursive, do not use 'break'.
				case 'M':
					$ret *= 1024;
					// Must remain recursive, do not use 'break'.
				case 'K':
					$ret *= 1024;
			}

			return $ret;
		}

		/**
		 * Normalize extensions dir.
		 *
		 * @param string $dir Path to extensions.
		 *
		 * @return string
		 */
		public static function get_extension_dir( $dir ) {
			return trailingslashit( Redux_Travelpayouts_Functions_Ex::wp_normalize_path( dirname( $dir ) ) );
		}

		/**
		 * Normalize extensions URL.
		 *
		 * @param string $dir Path to extensions.
		 *
		 * @return mixed
		 */
		public static function get_extension_url( $dir ) {
			$ext_dir = self::get_extension_dir( $dir );
			$ext_url = str_replace( Redux_Travelpayouts_Functions_Ex::wp_normalize_path( WP_CONTENT_DIR ), WP_CONTENT_URL, $ext_dir );

			return $ext_url;
		}

		/**
		 * Checks a nested capabilities array or string to determine if the current user meets the requirements.
		 *
		 * @param string|array $capabilities Permission string or array to check. See self::user_can() for details.
		 *
		 * @return bool Whether or not the user meets the requirements. False on invalid user.
		 * @since 3.6.3.4
		 */
		public static function current_user_can( $capabilities ) {
			$current_user = wp_get_current_user();

			if ( empty( $current_user ) ) {
				return false;
			}

			$name_arr = func_get_args();
			$args     = array_merge( array( $current_user ), $name_arr );

			return call_user_func_array( array( 'self', 'user_can' ), $args );
		}

		/**
		 * Checks a nested capabilities array or string to determine if the user meets the requirements.
		 * You can pass in a simple string like 'edit_posts' or an array of conditions.
		 * The capability 'relation' is reserved for controlling the relation mode (AND/OR), which defaults to AND.
		 * Max depth of 30 levels.  False is returned for any conditions exceeding max depth.
		 * If you want to check meta caps, you must also pass the object ID on which to check against.
		 * If you get the error: PHP Notice:  Undefined offset: 0 in /wp-includes/capabilities.php, you didn't
		 * pass the required $object_id.
		 *
		 * @param int|object   $user          User ID or WP_User object to check. Defaults to the current user.
		 * @param string|array $capabilities  Capability string or array to check. The array lets you use multiple
		 *                                    conditions to determine if a user has permission.
		 *                                    Invalid conditions are skipped (conditions which aren't a string/array/bool/number(cast to bool)).
		 *                                    Example array where the user needs to have either the 'edit_posts' capability OR doesn't have the
		 *                                    'delete_pages' cap OR has the 'update_plugins' AND 'add_users' capabilities.
		 *                                    array(
		 *                                    'relation'     => 'OR',      // Optional, defaults to AND.
		 *                                    'edit_posts',                // Equivalent to 'edit_posts' => true,
		 *                                    'delete_pages' => false,     // Tests that the user DOESN'T have this capability
		 *                                    array(                       // Nested conditions array (up to 30 nestings)
		 *                                    'update_plugins',
		 *                                    'add_users',
		 *                                    ),
		 *                                    ).
		 * @param int          $object_id     (Optional) ID of the specific object to check against if capability is a "meta" cap.
		 *                                    e.g. 'edit_post', 'edit_user', 'edit_page', etc.
		 *
		 * @return bool Whether or not the user meets the requirements.
		 *              Will always return false for:
		 *              - Invalid/missing user
		 *              - If the $capabilities is not a string or array
		 *              - Max nesting depth exceeded (for that level)
		 * @since 3.6.3.4
		 * @example
		 *        ::user_can( 42, 'edit_pages' );                        // Checks if user ID 42 has the 'edit_pages' cap.
		 *        ::user_can( 42, 'edit_page', 17433 );                  // Checks if user ID 42 has the 'edit_page' cap for post ID 17433.
		 *        ::user_can( 42, array( 'edit_pages', 'edit_posts' ) ); // Checks if user ID 42 has both the 'edit_pages' and 'edit_posts' caps.
		 */
		public static function user_can( $user, $capabilities, $object_id = null ) {
			static $depth = 0;

			if ( $depth >= 30 ) {
				return false;
			}

			if ( empty( $user ) ) {
				return false;
			}

			if ( ! is_object( $user ) ) {
				$user = get_userdata( $user );
			}

			if ( is_string( $capabilities ) ) {
				// Simple string capability check.
				$args = array( $user, $capabilities );

				if ( null !== $object_id ) {
					$args[] = $object_id;
				}

				return call_user_func_array( 'user_can', $args );
			} else {
				// Only strings and arrays are allowed as valid capabilities.
				if ( ! is_array( $capabilities ) ) {
					return false;
				}
			}

			// Capability array check.
			$or = false;

			foreach ( $capabilities as $key => $value ) {
				if ( 'relation' === $key ) {
					if ( 'OR' === $value ) {
						$or = true;
					}

					continue;
				}

				/**
				 * Rules can be in 4 different formats:
				 * [
				 *   [0]      => 'foobar',
				 *   [1]      => array(...),
				 *   'foobar' => false,
				 *   'foobar' => array(...),
				 * ]
				 */
				if ( is_numeric( $key ) ) {
					// Numeric key.
					if ( is_string( $value ) ) {
						// Numeric key with a string value is the capability string to check
						// [0] => 'foobar'.
						$args = array( $user, $value );

						if ( null !== $object_id ) {
							$args[] = $object_id;
						}

						$expression_result = call_user_func_array( 'user_can', $args ) === true;
					} elseif ( is_array( $value ) ) {
						$depth ++;

						$expression_result = self::user_can( $user, $value, $object_id );

						$depth --;
					} else {
						// Invalid types are skipped.
						continue;
					}
				} else {
					// Non-numeric key.
					if ( is_scalar( $value ) ) {
						$args = array( $user, $key );

						if ( null !== $object_id ) {
							$args[] = $object_id;
						}

						$expression_result = call_user_func_array( 'user_can', $args ) === (bool) $value;
					} elseif ( is_array( $value ) ) {
						$depth ++;

						$expression_result = self::user_can( $user, $value, $object_id );

						$depth --;
					} else {
						// Invalid types are skipped.
						continue;
					}
				}

				// Check after every evaluation if we know enough to return a definitive answer.
				if ( $or ) {
					if ( $expression_result ) {
						// If the relation is OR, return on the first true expression.
						return true;
					}
				} else {
					if ( ! $expression_result ) {
						// If the relation is AND, return on the first false expression.
						return false;
					}
				}
			}

			// If we get this far on an OR, then it failed.
			// If we get this far on an AND, then it succeeded.
			return ! $or;
		}

		/**
		 * Check if Google font update is needed.
		 *
		 * @return bool
		 */
		public static function google_fonts_update_needed() {

			$path = trailingslashit( Redux_Travelpayouts_Core::$upload_dir ) . 'google_fonts.json';
			$now  = time();
			$secs = 60 * 60 * 24 * 7;
			if ( file_exists( $path ) ) {
				if ( ( $now - filemtime( $path ) ) < $secs ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Retrieve list of dev keys.
		 *
		 * @return array|false|string
		 */
		public static function get_developer_keys() {

			// TODO - Get shim values for here.
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			$data = array( apply_filters( 'redux_travelpayouts/tracking/developer', array() ) );
			if ( 1 === count( $data ) ) {
				if ( empty( $data[0] ) ) {
					$data = array();
				}
			}
			$instances = Redux_Travelpayouts_Instances::get_all_instances();
			$data      = array();
			if ( ! empty( $instance ) ) {
				foreach ( $instances as $instance ) {
					if ( isset( $instance->args['developer'] ) && ! empty( $instance->args['developer'] ) ) {
						$data[] = $instance->args['developer'];
					}
				}
			}

			return $data;
		}

		/**
		 * Retrieve updated Google font array.
		 *
		 * @param bool $download Flag to download to file.
		 *
		 * @return array|WP_Error
		 */
		public static function google_fonts_array( $download = false ) {
			if ( ! empty( Redux_Travelpayouts_Core::$google_fonts ) && ! self::google_fonts_update_needed() ) {
				return Redux_Travelpayouts_Core::$google_fonts;
			}

			$filesystem = Redux_Travelpayouts_Filesystem::get_instance();

			$path = trailingslashit( Redux_Travelpayouts_Core::$upload_dir ) . 'google_fonts.json';

			if ( ! file_exists( $path ) || ( file_exists( $path ) && $download && self::google_fonts_update_needed() ) ) {

			} elseif ( file_exists( $path ) ) {
				Redux_Travelpayouts_Core::$google_fonts = json_decode( $filesystem->get_contents( $path ), true );
				if ( empty( Redux_Travelpayouts_Core::$google_fonts ) ) {
					$filesystem->unlink( $path );
				}
			}

			return Redux_Travelpayouts_Core::$google_fonts;
		}

		/**
		 * Deprecated. Gets all Redux_Travelpayouts instances
		 *
		 * @return Redux_Travelpayouts_Instances::get_all_instances()
		 * @deprecated No longer using camelCase naming convention and moved to a different class.
		 */
		public static function getReduxInstances() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName
			_deprecated_function( __CLASS__ . '::' . __FUNCTION__, 'Redux_Travelpayouts 4.0.0', 'Redux_Travelpayouts_Instances::get_all_instances()' );

			return Redux_Travelpayouts_Instances::get_all_instances();
		}

		/**
		 * Is Inside Plugin
		 *
		 * @param string $file File name.
		 *
		 * @return array|bool
		 */
		public static function is_inside_plugin( $file ) {

			// phpcs:ignore Squiz.PHP.CommentedOutCode
			// if ( substr( strtoupper( $file ), 0, 2 ) === 'C:' ) {
			// $file = ltrim( $file, 'C:' );
			// $file = ltrim( $file, 'c:' );
			// } .
			//
			$plugin_basename = plugin_basename( $file );

			if ( Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file ) !== '/' . $plugin_basename ) {
				$slug = explode( '/', $plugin_basename );
				$slug = $slug[0];

				return array(
					'slug'      => $slug,
					'basename'  => $plugin_basename,
					'path'      => Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file ),
					'url'       => plugins_url( $plugin_basename ),
					'real_path' => Redux_Travelpayouts_Functions_Ex::wp_normalize_path( dirname( realpath( $file ) ) ),
				);
			}

			return false;
		}

		/**
		 * Is inside theme.
		 *
		 * @param string $file File name.
		 *
		 * @return array|bool
		 */
		public static function is_inside_theme( $file = '' ) {
			$theme_paths = array(
				Redux_Travelpayouts_Functions_Ex::wp_normalize_path( get_template_directory() )   => get_template_directory_uri(),
				Redux_Travelpayouts_Functions_Ex::wp_normalize_path( get_stylesheet_directory() ) => get_stylesheet_directory_uri(),
			);

			$theme_paths = array_unique( $theme_paths );

			$file_path = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $file );
			$filename  = explode( '/', $file_path );
			$filename  = end( $filename );
			foreach ( $theme_paths as $theme_path => $url ) {

				$real_path = Redux_Travelpayouts_Functions_Ex::wp_normalize_path( realpath( $theme_path ) );

				if ( strpos( $file_path, trailingslashit( $real_path ) ) !== false ) {
					$slug = explode( '/', Redux_Travelpayouts_Functions_Ex::wp_normalize_path( $theme_path ) );
					if ( empty( $slug ) ) {
						continue;
					}
					$slug          = end( $slug );
					$relative_path = explode( $slug, dirname( $file_path ) );

					if ( 1 === count( $relative_path ) ) {
						$relative_path = $file_path;
					} else {
						$relative_path = $relative_path[1];
					}
					$relative_path = ltrim( $relative_path, '/' );

					$data = array(
						'slug'      => $slug,
						'path'      => trailingslashit( trailingslashit( $theme_path ) . $relative_path ) . $filename,
						'real_path' => trailingslashit( trailingslashit( $real_path ) . $relative_path ) . $filename,
						'url'       => trailingslashit( trailingslashit( $url ) . $relative_path ) . $filename,
					);

					$basename         = explode( $data['slug'], $data['path'] );
					$basename         = end( $basename );
					$basename         = ltrim( $basename, '/' );
					$data['basename'] = trailingslashit( $data['slug'] ) . $basename;

					if ( is_child_theme() ) {
						$parent              = get_template_directory();
						$data['parent_slug'] = explode( '/', $parent );
						$data['parent_slug'] = end( $data['parent_slug'] );
						if ( $data['slug'] === $data['parent_slug'] ) {
							unset( $data['parent_slug'] );
						}
					}

					return $data;
				}
			}

			return false;
		}

		/**
		 * Nonces.
		 *
		 * @return array
		 */
		public static function nonces() {
			$array = array(
				'9fced129522f128b2445a41fb0b6ef9f',
				'70dda5dfb8053dc6d1c492574bce9bfd',
				'62933a2951ef01f4eafd9bdf4d3cd2f0',
				'a398fb77df76e6153df57cd65fd0a7c5',
				'1cb251ec0d568de6a929b520c4aed8d1',
				'6394d816bfb4220289a6f4b29cfb1834',
			);

			return $array;
		}

		/**
		 * Get plugin options.
		 *
		 * @return array|mixed|void
		 */
		public static function get_plugin_options() {
			$defaults = array(
				'demo' => false,
			);
			$options  = array();

			// If multisite is enabled.
			if ( is_multisite() ) {

				// Get network activated plugins.
				$plugins = get_site_option( 'active_sitewide_plugins' );

				foreach ( $plugins as $file => $plugin ) {
					if ( strpos( $file, 'redux-framework.php' ) !== false ) {
						$plugin_network_activated = true;
						$options                  = get_site_option( 'TravelpayoutsSettingsFrameworkPlugin', $defaults );
					}
				}
			}

			// If options aren't set, grab them now!
			if ( empty( $options ) ) {
				$options = get_option( 'TravelpayoutsSettingsFrameworkPlugin', $defaults );
			}

			return $options;
		}

		/**
		 * Sanitize array keys and values.
		 *
		 * @param array $array Array to sanitize.
		 */
		public static function sanitize_array( $array ) {
			return self::array_map_r( 'sanitize_text_field', $array );
		}

		/**
		 * Recursive array map.
		 *
		 * @param string $func function to run.
		 * @param array  $arr  Array to clean.
		 *
		 * @return array
		 */
		private static function array_map_r( $func, $arr ) {
			$new_arr = array();

			foreach ( $arr as $key => $value ) {
				$new_arr[ $key ] = ( is_array( $value ) ? self::array_map_r( $func, $value ) : ( is_array( $func ) ? call_user_func_array( $func, $value ) : $func( $value ) ) );
			}

			return $new_arr;
		}

		/**
		 * AJAX callback.
		 */
		public static function hash_arg() {
			echo esc_html( md5( Redux_Travelpayouts_Functions_Ex::hash_key() . '-redux' ) );
			die();
		}

		/**
		 * Detect if Gutenberg is running on the current page.
		 *
		 * @return bool
		 */
		public static function is_gutenberg_page() {
			if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
				// The Gutenberg plugin is on.
				return true;
			}
			$current_screen = get_current_screen();
			if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
				// Gutenberg page on 5+.
				return true;
			}
			return false;
		}

        /**
         * trying to get already loaded field className if defined
         * @param TravelpayoutsSettingsFramework $core
         * @param array $field
         * @return string|false
         */
        public static function resolvePsr4FieldClass(TravelpayoutsSettingsFramework $core, array $field)
        {
            $className = apply_filters(
            // phpcs:ignore WordPress.NamingConventions.ValidHookName
                "redux_travelpayouts/{$core->args['opt_name']}/field/class-psr4/{$field['type']}",
                null,
                $field
            );
            return $className && class_exists($className) ? $className : false;
        }

        /**
         * @param string $className
         * @return false|string
         */
        public static function getFilePathFromClassName(string $className)
        {
            try {
                return (new ReflectionClass($className))->getFileName();
            } catch (ReflectionException $e) {
            }
            return false;
        }

    }
}
