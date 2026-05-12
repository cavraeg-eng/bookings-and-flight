<?php
/**
 * Redux_Travelpayouts Class
 *
 * @class Redux_Travelpayouts_Class
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Class', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Class
	 */
	class Redux_Travelpayouts_Class {

		/**
		 * Poiner to TravelpayoutsSettingsFramework object.
		 *
		 * @var null|TravelpayoutsSettingsFramework
		 */
		public $parent = null;

		/**
		 * Global arguments array.
		 *
		 * @var array|mixed|void
		 */
		public $args = array();

		/**
		 * Project opt_name
		 *
		 * @var mixed|string
		 */
		public $opt_name = '';

		/**
		 * Redux_Travelpayouts_Class constructor.
		 *
		 * @param null|TravelpayoutsSettingsFramework $parent Pointer to TravelpayoutsSettingsFramework object.
		 */
		public function __construct( $parent = null ) {
			if ( null !== $parent && is_object( $parent ) ) {
				$this->parent   = $parent;
				$this->args     = $parent->args;
				$this->opt_name = $this->args['opt_name'];
			}
		}

		/**
		 * Pointer to project specific TravelpayoutsSettingsFramework object.
		 *
		 * @return null|TravelpayoutsSettingsFramework
		 */
		public function core() {
			if ( isset( $this->opt_name ) && '' !== $this->opt_name ) {
				return Redux_Travelpayouts::instance( $this->opt_name );
			}

			return null;
		}

	}

}
