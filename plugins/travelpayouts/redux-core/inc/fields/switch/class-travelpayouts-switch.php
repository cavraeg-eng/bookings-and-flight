<?php
/**
 * Switch Field
 * @package     Redux Framework
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined('ABSPATH') || exit;

if (!class_exists('Redux_Travelpayouts_Switch', false)) {

    /**
     * Class Redux_Travelpayouts_Switch
     */
    class Redux_Travelpayouts_Switch extends Redux_Travelpayouts_Field
    {

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         * @since ReduxFramework 0.0.4
         */
        public function render()
        {
            $cb_enabled = '';
            $cb_disabled = '';

            // Get selected.
            if (1 === (int)$this->value) {
                $cb_enabled = ' tp-switch-item--active';
            } else {
                $cb_disabled = ' tp-switch-item--active';
            }

            // Label ON.
            $this->field['on'] = isset($this->field['on']) ? $this->field['on'] : esc_html__('On', 'redux-framework');

            // Label OFF.
            $this->field['off'] = isset($this->field['off']) ? $this->field['off'] : esc_html__('Off', 'redux-framework');
            echo '<div class="tp-switch">';
            echo '<div class="tp-switch-item tp-switch-item--disable' . esc_attr($cb_disabled) . '" data-id="' . esc_attr($this->field['id']) . '"><span>' . esc_html($this->field['off']) . '</span></div>';
            echo '<div class="tp-switch-item tp-switch-item--enable' . esc_attr($cb_enabled) . '" data-id="' . esc_attr($this->field['id']) . '"><span>' . esc_html($this->field['on']) . '</span></div>';
            echo '<input type="hidden" class="checkbox checkbox-input ' . esc_attr($this->field['class']) . '" id="' . esc_attr($this->field['id']) . '" name="' . esc_attr($this->field['name'] . $this->field['name_suffix']) . '" value="' . esc_attr($this->value) . '" />';
            echo '</div>';
        }

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         * @since ReduxFramework 0.0.4
         */
        public function enqueue()
        {
            wp_enqueue_script(
                'redux-field-switch-js',
                Redux_Travelpayouts_Core::$url . 'inc/fields/switch/redux-switch' . Redux_Travelpayouts_Functions::is_min() . '.js',
                ['jquery', 'redux-js'],
                $this->timestamp,
                true
            );
        }
    }
}

class_alias('Redux_Travelpayouts_Switch', 'TravelpayoutsSettingsFramework__Switch');
