<?php
/**
 * Register Extensions for use
 *
 * @package Redux_Travelpayouts Framework/Classes
 * @since       3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Extensions', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Extensions
	 */
	class Redux_Travelpayouts_Extensions extends Redux_Travelpayouts_Class {

		/**
		 * Redux_Travelpayouts_Extensions constructor.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework object pointer.
		 */
		public function __construct( $parent ) {
			parent::__construct( $parent );

			$this->load();
		}

		/**
		 * Class load functions.
		 */
		private function load() {
			$core = $this->core();

			$max = 1;

			if ( Redux_Travelpayouts_Core::$pro_loaded ) {
				$max = 2;
			}

			for ( $i = 1; $i <= $max; $i ++ ) {
				$path = Redux_Travelpayouts_Core::$dir . 'inc/extensions/';

				if ( 2 === $i ) {
					$path = Redux_Travelpayouts_Pro::$dir . 'core/inc/extensions/';
				}

				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				$path = apply_filters( 'redux_travelpayouts/' . $core->args['opt_name'] . '/extensions/dir', $path );

				/**
				 * Action 'redux_travelpayouts/extensions/before'
				 *
				 * @param object $this TravelpayoutsSettingsFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( 'redux_travelpayouts/extensions/before', $core );

				/**
				 * Action 'redux_travelpayouts/extensions/{opt_name}/before'
				 *
				 * @param object $this TravelpayoutsSettingsFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "redux_travelpayouts/extensions/{$core->args['opt_name']}/before", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'redux_travelpayouts/extensions/' . $core->old_opt_name . '/before', $core );
				}

				require_once Redux_Travelpayouts_Core::$dir . 'inc/classes/class-travelpayouts-extension-abstract.php';

				$path = untrailingslashit( $path );

				Redux_Travelpayouts::set_extensions( $core->args['opt_name'], $path, true );

                // Preloading extensions passed in the config
                if(isset($core->args['extensions']) && is_array($core->args['extensions'])) {
                    Redux_Travelpayouts::addPsr4Extensions( $core->args['opt_name'], $core->args['extensions'], true );
                }
				/**
				 * Action 'redux_travelpayouts/extensions/{opt_name}'
				 *
				 * @param object $this TravelpayoutsSettingsFramework
				 */
				// phpcs:ignore WordPress.NamingConventions.ValidHookName
				do_action( "redux_travelpayouts/extensions/{$core->args['opt_name']}", $core );

				if ( isset( $core->old_opt_name ) && null !== $core->old_opt_name ) {
					// phpcs:ignore WordPress.NamingConventions.ValidHookName
					do_action( 'redux_travelpayouts/extensions/' . $core->old_opt_name, $core );
				}
			}
		}
	}
}
