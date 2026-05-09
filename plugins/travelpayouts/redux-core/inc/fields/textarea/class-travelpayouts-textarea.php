<?php
/**
 * Textarea Field
 *
 * @package     Redux_Travelpayouts Framework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redux_Travelpayouts_Textarea', false ) ) {

	/**
	 * Class Redux_Travelpayouts_Textarea
	 */
	class Redux_Travelpayouts_Textarea extends Redux_Travelpayouts_Field {

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since TravelpayoutsSettingsFramework 1.0.0
		 * */
		public function render() {

			$this->field['attributes'] = wp_parse_args(
				isset( $this->field['attributes'] ) ? $this->field['attributes'] : array(),
				array(
					'placeholder'  => isset( $this->field['placeholder'] ) ? $this->field['placeholder'] : '',
					'rows'         => isset( $this->field['rows'] ) ? $this->field['rows'] : 6,
					'autocomplete' => ( isset( $this->field['autocomplete'] ) && false === $this->field['autocomplete'] ) ? 'off' : '',
					'readonly'     => isset( $this->field['readonly'] ) && $this->field['readonly'] ? 'readonly' : '',
					'name'         => esc_attr( $this->field['name'] . $this->field['name_suffix'] ),
					'id'           => esc_attr( $this->field['id'] ),
					'class'        => isset( $this->field['class'] ) && ! empty( $this->field['class'] ) ? array( trim( $this->field['class'] ) ) : array(),
				)
			);

			$this->field['attributes']['class'][] = 'large-text tp-input tp-textarea';

			$this->field['attributes']['class'] = implode( ' ', $this->field['attributes']['class'] );

			$attributes_string = $this->render_attributes( $this->field['attributes'] );
			echo '<textarea ' . $attributes_string . '>' . esc_textarea( $this->value ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}

		/**
		 * Santize value.
		 *
		 * @param array $field Field array.
		 * @param array $value Values array.
		 *
		 * @return mixed|string|void
		 */
		public function sanitize( $field, $value ) {
			if ( ! isset( $value ) || empty( $value ) ) {
				$value = '';
			} else {
				$value = esc_textarea( $value );
			}

			return $value;
		}
	}
}

class_alias( 'Redux_Travelpayouts_Textarea', 'TravelpayoutsSettingsFramework_Textarea' );
