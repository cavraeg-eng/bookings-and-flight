<?php
/**
 * Sanitizer Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Input Sanitizer
 */
class Bookings_And_Flights_Sanitizer {

    /**
     * Sanitize value by field type
     *
     * @param mixed  $value The value to sanitize.
     * @param string $type  The field type.
     * @param array  $field Optional field definition for complex types.
     * @return mixed Sanitized value.
     */
    public static function sanitize($value, $type = 'text', $field = array()) {
        switch ($type) {
            case 'text':
                return sanitize_text_field($value);

            case 'textarea':
                return sanitize_textarea_field($value);

            case 'url':
                return esc_url_raw($value);

            case 'email':
                return sanitize_email($value);

            case 'image':
                return absint($value);

            case 'number':
                if (!is_numeric($value)) {
                    return 0;
                }
                return floatval($value);

            case 'wysiwyg':
                return wp_kses_post($value);

            case 'color':
                return sanitize_hex_color($value);

            case 'gallery':
                if (is_array($value)) {
                    return array_map('absint', $value);
                }
                // Handle comma-separated string
                if (is_string($value) && !empty($value)) {
                    return array_map('absint', explode(',', $value));
                }
                return array();

            case 'select':
            case 'radio':
                return sanitize_text_field($value);

            case 'checkbox':
                return !empty($value) ? 1 : 0;

            case 'link':
                return self::sanitize_link($value);

            case 'date':
                return self::sanitize_date($value);

            case 'oembed':
                return esc_url_raw($value);

            case 'repeater':
                return self::sanitize_repeater($value, $field);

            default:
                return sanitize_text_field($value);
        }
    }

    /**
     * Sanitize link field (url + label array).
     *
     * @param mixed $value The link value.
     * @return array Sanitized link array.
     */
    private static function sanitize_link($value) {
        if (!is_array($value)) {
            return array('url' => '', 'label' => '');
        }

        return array(
            'url'   => isset($value['url']) ? esc_url_raw($value['url']) : '',
            'label' => isset($value['label']) ? sanitize_text_field($value['label']) : '',
        );
    }

    /**
     * Sanitize date/datetime field.
     *
     * Accepts YYYY-MM-DD or YYYY-MM-DDTHH:MM format.
     *
     * @param mixed $value The date value.
     * @return string Sanitized date string or empty string.
     */
    private static function sanitize_date($value) {
        $value = sanitize_text_field($value);

        if ('' === $value) {
            return '';
        }

        // YYYY-MM-DD or YYYY-MM-DDTHH:MM
        if (preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2})?$/', $value)) {
            return $value;
        }

        return '';
    }

    /**
     * Sanitize repeater field
     *
     * @param mixed $value The repeater value (array of rows).
     * @param array $field The field definition with subfields.
     * @return array Sanitized repeater data.
     */
    private static function sanitize_repeater($value, $field) {
        if (!is_array($value)) {
            return array();
        }

        $subfields = isset($field['subfields']) ? $field['subfields'] : array();
        $sanitized = array();

        foreach ($value as $row) {
            if (!is_array($row)) {
                continue;
            }

            $sanitized_row = array();
            foreach ($row as $key => $val) {
                $subfield_type = isset($subfields[$key]['type']) ? $subfields[$key]['type'] : 'text';
                $sanitized_row[$key] = self::sanitize($val, $subfield_type);
            }
            $sanitized[] = $sanitized_row;
        }

        return $sanitized;
    }
}
