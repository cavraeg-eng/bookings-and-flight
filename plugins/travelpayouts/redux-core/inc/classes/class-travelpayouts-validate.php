<?php
/**
 * Redux_Travelpayouts Validate Class
 *
 * @class Redux_Travelpayouts_Validate
 * @version 4.0.0
 * @package Redux_Travelpayouts Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Validate', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Validate
	 */
	abstract class Redux_Travelpayouts_Validate {

		/**
		 * Redux_Travelpayouts_Validate constructor.
		 *
		 * @param object $parent TravelpayoutsSettingsFramework pointer.
		 * @param array  $field Fields array.
		 * @param array  $value Values array.
		 * @param mixed  $current Current.
		 */
		public function __construct( $parent, $field, $value, $current ) {
			$this->parent  = $parent;
			$this->field   = $field;
			$this->value   = $value;
			$this->current = $current;

			if ( isset( $this->field['validate_msg'] ) ) {
				$this->field['msg'] = $this->field['validate_msg'];

				unset( $this->field['validate_msg'] );
			}

			$this->validate();
		}

		/**
		 * Validate.
		 *
		 * @return mixed
		 */
		abstract public function validate();
	}
}
