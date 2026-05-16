<?php
/**
 * Fields Registry Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Fields Registry
 */
class Bookings_And_Flights_Fields {

    private static $instance = null;
    private $fields = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register fields for a template
     */
    public function register($template, $fields) {
        $this->fields[$template] = $this->normalize_fields($fields, $template);
    }

    /**
     * Get all fields for a template
     */
    public function get_fields($template) {
        return isset($this->fields[$template]) ? $this->fields[$template] : array();
    }

    /**
     * Get all registered templates
     */
    public function get_templates() {
        return array_keys($this->fields);
    }

    /**
     * Get field definition by key
     */
    public function get_field($template, $key) {
        $fields = $this->get_fields($template);
        return isset($fields[$key]) ? $fields[$key] : null;
    }

    /**
     * Check if template has fields
     */
    public function has_fields($template) {
        return isset($this->fields[$template]) && !empty($this->fields[$template]);
    }

    /**
     * Normalize field definitions for consistent rendering.
     *
     * @param array  $fields Field definitions.
     * @param string $template Template slug.
     * @return array
     */
    private function normalize_fields($fields, $template) {
        $normalized = array();

        if (!is_array($fields)) {
            return $normalized;
        }

        foreach ($fields as $key => $field) {
            $normalized[$key] = $this->normalize_field($key, $field, $template);
        }

        return $normalized;
    }

    /**
     * Normalize a single field definition.
     *
     * @param string $key Field key.
     * @param array  $field Field definition.
     * @param string $template Template slug.
     * @return array
     */
    private function normalize_field($key, $field, $template) {
        if (!is_array($field)) {
            $field = array();
        }

        $defaults = array(
            'label'    => $this->default_label($key),
            'type'     => 'text',
            'default'  => '',
            'help'     => '',
            'required' => false,
            'section'  => '',
            'options'  => array(), // For select, radio, checkbox
            'rows'     => 4,       // For textarea
            'min'      => null,    // For number
            'max'      => null,    // For number
            'step'     => null,    // For number
            'subfields' => array(), // For repeater
            'time'      => false,    // For date (datetime-local when true)
            'show_when' => null,     // Conditional visibility
        );

        $field = array_merge($defaults, $field);

        $allowed_types = $this->get_allowed_types();
        if (!in_array($field['type'], $allowed_types, true)) {
            $field['type'] = 'text';
        }

        $field['required'] = !empty($field['required']);

        if ('' === $field['section']) {
            $field['section'] = $this->get_section_label($key, $template);
        }

        return $field;
    }

    /**
     * Default label from field key.
     *
     * @param string $key Field key.
     * @return string
     */
    private function default_label($key) {
        return ucwords(str_replace('_', ' ', $key));
    }

    /**
     * Get allowed field types.
     *
     * @return array
     */
    private function get_allowed_types() {
        $types = array(
            'text',
            'textarea',
            'wysiwyg',
            'image',
            'gallery',
            'url',
            'email',
            'number',
            'color',
            'select',
            'radio',
            'checkbox',
            'repeater',
            'link',
            'date',
            'oembed',
        );

        return apply_filters('bookings_and_flights_field_types', $types);
    }

    /**
     * Derive section label from field key.
     *
     * @param string $key Field key.
     * @param string $template Template slug.
     * @return string
     */
    private function get_section_label($key, $template) {
        $prefixes = $this->get_section_prefixes($template);

        uksort($prefixes, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        foreach ($prefixes as $prefix => $label) {
            if (0 === strpos($key, $prefix)) {
                return $label;
            }
        }

        return __('General', 'bookings_and_flights-content-manager');
    }

    /**
     * Map key prefixes to section labels.
     *
     * Customize this for your project. Field keys starting with
     * these prefixes will be grouped under the corresponding section.
     *
     * @param string $template Template slug.
     * @return array
     */
    private function get_section_prefixes($template) {
        $prefixes = array(
            'hero_'         => __('Hero Section', 'bookings_and_flights-content-manager'),
            'features_'     => __('Features Section', 'bookings_and_flights-content-manager'),
            'about_'        => __('About Section', 'bookings_and_flights-content-manager'),
            'services_'     => __('Services Section', 'bookings_and_flights-content-manager'),
            'testimonials_' => __('Testimonials Section', 'bookings_and_flights-content-manager'),
            'cta_'          => __('CTA Section', 'bookings_and_flights-content-manager'),
            'contact_'      => __('Contact Section', 'bookings_and_flights-content-manager'),
            'gallery_'      => __('Gallery Section', 'bookings_and_flights-content-manager'),
        );

        return apply_filters('bookings_and_flights_field_section_prefixes', $prefixes, $template);
    }
}
