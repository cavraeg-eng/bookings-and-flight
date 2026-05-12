<?php
/**
 * Redux_Travelpayouts Framework Instance Container Class
 * Automatically captures and stores all instances
 * of TravelpayoutsSettingsFramework at instantiation.
 *
 * @package     Redux_Travelpayouts_Framework/Classes
 * @subpackage  Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Instances', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Instances
	 */
	class Redux_Travelpayouts_Instances {

		/**
		 * TravelpayoutsSettingsFramework instances
		 *
		 * @var TravelpayoutsSettingsFramework[]
		 */
		private static $instances;

		/**
		 * Get Instance
		 * Get Redux_Travelpayouts_Instances instance
		 * OR an instance of TravelpayoutsSettingsFramework by [opt_name]
		 *
		 * @param  string|false $opt_name the defined opt_name.
		 *
		 * @return TravelpayoutsSettingsFramework class instance
		 */
		public static function get_instance( $opt_name = false ) {

			if ( $opt_name && ! empty( self::$instances[ $opt_name ] ) ) {
				return self::$instances[ $opt_name ];
			}

			return null;
		}

		/**
		 * Shim for old get_Redux_Travelpayouts_instance method.
		 *
		 * @param  string|false $opt_name the defined opt_name.
		 *
		 * @return TravelpayoutsSettingsFramework class instance
		 */
		public static function get_Redux_Travelpayouts_instance( $opt_name = '' ) {
			return self::get_instance( $opt_name );
		}

		/**
		 * Get all instantiated TravelpayoutsSettingsFramework instances (so far)
		 *
		 * @return [type] [description]
		 */
		public static function get_all_instances() {
			return self::$instances;
		}

		/**
		 * Redux_Travelpayouts_Instances constructor.
		 *
		 * @param mixed $Redux_Travelpayouts_framework Is object.
		 */
		public function __construct( $Redux_Travelpayouts_framework = false ) {
			if ( false !== $Redux_Travelpayouts_framework ) {
				$this->store( $Redux_Travelpayouts_framework );
			} else {
				add_action( 'redux_travelpayouts/construct', array( $this, 'store' ), 5, 1 );
			}
		}

		/**
		 * Action hook callback.
		 *
		 * @param object $Redux_Travelpayouts_framework Pointer.
		 */
		public function store( $Redux_Travelpayouts_framework ) {
			if ( $Redux_Travelpayouts_framework instanceof TravelpayoutsSettingsFramework ) {
				$key                     = $Redux_Travelpayouts_framework->args['opt_name'];
				self::$instances[ $key ] = $Redux_Travelpayouts_framework;
			}
		}
	}
}

if ( ! class_exists( 'TravelpayoutsSettingsFrameworkInstances' ) ) {
	class_alias( 'Redux_Travelpayouts_Instances', 'TravelpayoutsSettingsFrameworkInstances' );
}

if ( ! function_exists( 'get_Redux_Travelpayouts_instance' ) ) {
	/**
	 * Shim function that some theme oddly used.
	 *
	 * @param  string|false $opt_name the defined opt_name.
	 *
	 * @return TravelpayoutsSettingsFramework class instance
	 */
	function get_Redux_Travelpayouts_instance( $opt_name ) {
		return Redux_Travelpayouts_Instances::get_instance( $opt_name );
	}
}
