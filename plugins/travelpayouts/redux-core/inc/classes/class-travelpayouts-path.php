<?php
/**
 * Redux_Travelpayouts Path Class
 *
 * @class Redux_Travelpayouts_Path
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Path', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Path
	 */
	class Redux_Travelpayouts_Path {

		/**
		 * Class init
		 */
		public static function init() {

		}

		/**
		 * Gets Redux_Travelpayouts path.
		 *
		 * @param string $relative_path Self explanitory.
		 *
		 * @return string
		 */
		public static function get_path( $relative_path ) {
			$path = Redux_Travelpayouts_Core::$Redux_Travelpayouts_path . $relative_path;

			if ( Redux_Travelpayouts_Core::$pro_loaded ) {

				$pro_path = Redux_Travelpayouts_Pro::$dir . '/core' . $relative_path;

				if ( file_exists( $pro_path ) ) {
					$path = $pro_path;
				}
			}

			return $path;
		}

		/**
		 * Require class.
		 *
		 * @param string $relative_path Path.
		 */
		public static function require_class( $relative_path ) {
			$path = self::get_path( $relative_path );

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	Redux_Travelpayouts_Path::init();
}
