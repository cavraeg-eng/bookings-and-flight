<?php
/**
 * Template Helper Functions
 *
 * These functions are safe to call from theme templates.
 * They have fallbacks in case the plugin is deactivated.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Get a field value for the current post
 *
 * @param string $key Field key
 * @param mixed $default Default value if field not found
 * @return mixed Field value or default
 */
function bookings_and_flights_field($key, $default = '') {
    if (!function_exists('get_post_meta')) {
        return $default;
    }

    $post_id = get_the_ID();
    if (!$post_id) {
        return $default;
    }

    static $meta_cache = array();

    if (!isset($meta_cache[$post_id])) {
        $meta_cache[$post_id] = get_post_meta($post_id);
    }

    // Resolve the prefixed meta key when the plugin is active.
    $lookup_key = $key;
    if (class_exists('Bookings_And_Flights_Config')) {
        $lookup_key = Bookings_And_Flights_Config::meta_key($key);
    }

    $value = '';
    if (isset($meta_cache[$post_id][$lookup_key]) && is_array($meta_cache[$post_id][$lookup_key])) {
        $value = count($meta_cache[$post_id][$lookup_key]) ? $meta_cache[$post_id][$lookup_key][0] : '';
    }

    // Handle serialized data (for gallery, repeater)
    if (is_string($value) && is_serialized($value)) {
        $value = maybe_unserialize($value);
    }

    // Return value if set, otherwise default
    $value = ($value !== '') ? $value : $default;

    return apply_filters('bookings_and_flights_field_value', $value, $key, $post_id);
}

/**
 * Get an image URL from an attachment ID
 *
 * @param int $image_id Attachment ID
 * @param string $size Image size (thumbnail, medium, large, full)
 * @return string Image URL or empty string
 */
function bookings_and_flights_image_url($image_id = 0, $size = 'full') {
    if (empty($image_id)) {
        return '';
    }

    $url = wp_get_attachment_image_url(intval($image_id), $size);
    return $url ? $url : '';
}

/**
 * Get an image tag with proper attributes
 *
 * @param int $image_id Attachment ID
 * @param string $size Image size
 * @param array $attrs Additional attributes
 * @return string HTML img tag or empty string
 */
function bookings_and_flights_image($image_id = 0, $size = 'full', $attrs = array()) {
    if (empty($image_id)) {
        return '';
    }

    $html = wp_get_attachment_image(intval($image_id), $size, false, $attrs);
    return $html ? $html : '';
}

/**
 * Get gallery image IDs
 *
 * @param string $key Field key
 * @return array Array of attachment IDs
 */
function bookings_and_flights_gallery($key) {
    $value = bookings_and_flights_field($key, array());

    if (is_string($value) && !empty($value)) {
        return array_map('absint', explode(',', $value));
    }

    if (is_array($value)) {
        return array_map('absint', $value);
    }

    return array();
}

/**
 * Get repeater field data
 *
 * @param string $key Field key
 * @return array Array of rows
 */
function bookings_and_flights_repeater($key) {
    $value = bookings_and_flights_field($key, array());
    return is_array($value) ? $value : array();
}

/**
 * Output a field value with escaping
 *
 * @param string $key Field key
 * @param mixed $default Default value
 */
function bookings_and_flights_the_field($key, $default = '') {
    echo esc_html(bookings_and_flights_field($key, $default));
}

/**
 * Output a field value as HTML (for wysiwyg fields)
 *
 * @param string $key Field key
 * @param mixed $default Default value
 */
function bookings_and_flights_the_content($key, $default = '') {
    echo wp_kses_post(bookings_and_flights_field($key, $default));
}

/**
 * Output a field value as attribute
 *
 * @param string $key Field key
 * @param mixed $default Default value
 */
function bookings_and_flights_the_attr($key, $default = '') {
    echo esc_attr(bookings_and_flights_field($key, $default));
}

/**
 * Output a field value as URL
 *
 * @param string $key Field key
 * @param mixed $default Default value
 */
function bookings_and_flights_the_url($key, $default = '') {
    echo esc_url(bookings_and_flights_field($key, $default));
}

/**
 * Get a link field value (url + label array)
 *
 * @param string $key Field key
 * @return array Array with 'url' and 'label' keys
 */
function bookings_and_flights_link($key) {
    $value = bookings_and_flights_field($key, array());

    if (!is_array($value)) {
        return array('url' => '', 'label' => '');
    }

    return array(
        'url'   => isset($value['url']) ? $value['url'] : '',
        'label' => isset($value['label']) ? $value['label'] : '',
    );
}

/**
 * Get an oEmbed field value as embed HTML
 *
 * @param string $key  Field key
 * @param array  $args Optional args passed to wp_oembed_get()
 * @return string Embed HTML or empty string
 */
function bookings_and_flights_oembed($key, $args = array()) {
    $url = bookings_and_flights_field($key, '');

    if (empty($url)) {
        return '';
    }

    if (!function_exists('wp_oembed_get')) {
        return '';
    }

    $html = wp_oembed_get($url, $args);
    return $html ? $html : '';
}

/**
 * Get a global option value (site-wide, not page-specific)
 *
 * Use for footer content, social links, contact info, CTA defaults.
 * Falls back to field definition defaults, then to $default param.
 *
 * @param string $key     Option key (e.g. 'social_instagram', 'contact_email')
 * @param mixed  $default Fallback value
 * @return mixed
 */
function bookings_and_flights_option($key, $default = '') {
    if (class_exists('Bookings_And_Flights_Options')) {
        return Bookings_And_Flights_Options::get($key, $default);
    }
    return $default;
}

/**
 * Output a global option value with escaping
 *
 * @param string $key     Option key
 * @param mixed  $default Fallback value
 */
function bookings_and_flights_the_option($key, $default = '') {
    echo esc_html(bookings_and_flights_option($key, $default));
}

/**
 * Output a global option value as URL
 *
 * @param string $key     Option key
 * @param mixed  $default Fallback value
 */
function bookings_and_flights_the_option_url($key, $default = '') {
    echo esc_url(bookings_and_flights_option($key, $default));
}
