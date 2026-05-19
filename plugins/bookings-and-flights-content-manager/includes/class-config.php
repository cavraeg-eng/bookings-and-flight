<?php
/**
 * Config Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Plugin configuration.
 */
class Bookings_And_Flights_Config {

    /**
     * Meta key prefix applied to all page field keys.
     */
    const META_PREFIX = 'bookings_and_flights_';

    /**
     * Prepend the meta prefix to a field key.
     *
     * @param string $field_key Unprefixed field key (e.g. 'hero_heading').
     * @return string Prefixed meta key (e.g. 'bookings_and_flights_hero_heading').
     */
    public static function meta_key( $field_key ) {
        if ( self::has_prefix( $field_key ) ) {
            return $field_key;
        }
        return self::META_PREFIX . $field_key;
    }

    /**
     * Strip the meta prefix from a meta key.
     *
     * @param string $meta_key Prefixed meta key.
     * @return string Unprefixed field key.
     */
    public static function field_key( $meta_key ) {
        if ( self::has_prefix( $meta_key ) ) {
            return substr( $meta_key, strlen( self::META_PREFIX ) );
        }
        return $meta_key;
    }

    /**
     * Check if a meta key already has the prefix.
     *
     * @param string $meta_key Meta key to check.
     * @return bool
     */
    public static function has_prefix( $meta_key ) {
        return 0 === strpos( $meta_key, self::META_PREFIX );
    }

    /**
     * Get template to title mapping.
     *
     * Add your page templates here. The key is the template filename,
     * the value is the meta box title shown in the editor.
     *
     * @return array
     */
    public static function get_template_map() {
        return array(
            'page-home.php'    => __('Home Page Content', 'bookings_and_flights-content-manager'),
            'page-about.php'   => __('About Page Content', 'bookings_and_flights-content-manager'),
            'page-services.php' => __('Services Page Content', 'bookings_and_flights-content-manager'),
            'page-contact.php' => __('Contact Page Content', 'bookings_and_flights-content-manager'),
        );
    }

    /**
     * Get registered CPT definitions for export/import.
     *
     * Each CPT entry declares how to export/import its posts.
     * Add entries here when you register a new CPT for the project.
     *
     * Keys in meta_fields, image_keys, and gallery_keys form the
     * complete allowlist — only these keys are imported. Any other
     * meta matching meta_prefix is exported but warned on import
     * if not declared here (schema drift detection).
     *
     * @return array
     */
    public static function get_cpt_definitions() {
        $definitions = array(
            // Example (uncomment and customize per project):
            // 'bookings_and_flights_team' => array(
            //     'label'        => __( 'Team Members', 'bookings_and_flights-content-manager' ),
            //     'meta_prefix'  => '_bookings_and_flights_team_',
            //     'meta_fields'  => array(
            //         '_bookings_and_flights_team_role'    => 'text',
            //         '_bookings_and_flights_team_bio'     => 'textarea',
            //         '_bookings_and_flights_team_website' => 'url',
            //     ),
            //     'image_keys'   => array(),
            //     'gallery_keys' => array(),
            //     'taxonomies'   => array(),
            //     'orderby'      => 'menu_order',
            //     'order'        => 'ASC',
            // ),
        );

        return apply_filters( 'bookings_and_flights_cpt_definitions', $definitions );
    }

    /**
     * Get field definition file list.
     *
     * @return array
     */
    public static function get_field_files() {
        $fields_dir = trailingslashit(BOOKINGS_AND_FLIGHTS_PLUGIN_DIR . 'fields');
        $files = glob($fields_dir . '*.php');

        if (!is_array($files)) {
            $files = array();
        }

        $files = array_filter($files, 'is_readable');
        sort($files);

        /**
         * Filter the list of field definition files to load.
         *
         * @param array $files Absolute file paths.
         */
        return apply_filters('bookings_and_flights_field_files', $files);
    }
}
