<?php
/**
 * Main Plugin Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Main Plugin Class
 */
class Bookings_And_Flights_Plugin {

    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Prevent direct instantiation
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        $this->load_textdomain();

        // Initialize global options (admin settings page)
        if (class_exists('Bookings_And_Flights_Options')) {
            $options = Bookings_And_Flights_Options::get_instance();
            $options->init();
        }

        // Conditionally disable block/classic editors for template pages.
        $this->maybe_disable_editors();

        if (is_admin()) {
            // Load admin assets
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

            // Load field definitions before meta boxes.
            add_action('admin_init', array($this, 'load_field_definitions'), 5);

            // Initialize meta boxes
            add_action('admin_init', array($this, 'init_meta_boxes'));

            // Initialize export/import tools page.
            if ( class_exists( 'Bookings_And_Flights_Export_Import' ) ) {
                Bookings_And_Flights_Export_Import::get_instance()->init();
            }

            // oEmbed AJAX preview handler.
            add_action('wp_ajax_bookings_and_flights_oembed_preview', array($this, 'ajax_oembed_preview'));
        }

        // Initialize revision support.
        if (class_exists('Bookings_And_Flights_Revisions')) {
            Bookings_And_Flights_Revisions::get_instance()->init();
        }
    }

    /**
     * Load plugin text domain.
     */
    private function load_textdomain() {
        load_plugin_textdomain(
            'bookings_and_flights-content-manager',
            false,
            dirname(BOOKINGS_AND_FLIGHTS_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on post edit screens
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || 'page' !== $screen->post_type) {
            return;
        }

        wp_enqueue_media();

        // Color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Admin CSS
        wp_enqueue_style(
            'bookings_and_flights-admin',
            BOOKINGS_AND_FLIGHTS_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            BOOKINGS_AND_FLIGHTS_VERSION
        );

        // Admin JS (media uploader, gallery, repeater, color picker)
        wp_enqueue_script(
            'bookings_and_flights-admin',
            BOOKINGS_AND_FLIGHTS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable', 'wp-color-picker'),
            BOOKINGS_AND_FLIGHTS_VERSION,
            true
        );

        // Localize script
        wp_localize_script('bookings_and_flights-admin', 'bookings_and_flightsAdmin', array(
            'uploaderTitle' => __('Select Image', 'bookings_and_flights-content-manager'),
            'uploaderButton' => __('Use This Image', 'bookings_and_flights-content-manager'),
            'galleryTitle' => __('Select Images', 'bookings_and_flights-content-manager'),
            'galleryButton' => __('Add to Gallery', 'bookings_and_flights-content-manager'),
            'confirmRemove' => __('Are you sure you want to remove this item?', 'bookings_and_flights-content-manager'),
            'ajaxUrl'       => admin_url('admin-ajax.php'),
            'oembedNonce'   => wp_create_nonce('bookings_and_flights_oembed_preview'),
        ));
    }

    /**
     * Load field definition files
     */
    public function load_field_definitions() {
        $field_files = Bookings_And_Flights_Config::get_field_files();
        $registry = Bookings_And_Flights_Fields::get_instance();

        foreach ($field_files as $file) {
            $path = $file;

            if (!is_readable($path)) {
                $path = trailingslashit(BOOKINGS_AND_FLIGHTS_PLUGIN_DIR . 'fields') . ltrim($file, '/');
                if ('.php' !== substr($path, -4)) {
                    $path .= '.php';
                }
            }

            if (!is_readable($path)) {
                continue;
            }

            $definitions = require $path;

            if (is_array($definitions)
                && isset($definitions['template'], $definitions['fields'])
                && is_array($definitions['fields'])
            ) {
                $registry->register($definitions['template'], $definitions['fields']);
            }
        }
    }

    /**
     * Initialize meta boxes
     */
    public function init_meta_boxes() {
        if (class_exists('Bookings_And_Flights_Meta_Boxes')) {
            $meta_boxes = Bookings_And_Flights_Meta_Boxes::get_instance();
            $meta_boxes->register();
        }
    }

    /**
     * Conditionally disable block and classic editors for template pages.
     */
    private function maybe_disable_editors() {
        $options = get_option( Bookings_And_Flights_Options::OPTION_NAME, array() );

        // Default to enabled (1) for new installs / upgrade scenarios.
        $disabled = isset( $options['disable_block_editor'] ) ? (int) $options['disable_block_editor'] : 1;

        if ( ! $disabled ) {
            return;
        }

        add_filter( 'use_block_editor_for_post', array( $this, 'filter_block_editor_for_post' ), 10, 2 );
        add_action( 'add_meta_boxes_page', array( $this, 'maybe_remove_classic_editor' ), 1 );
    }

    /**
     * Disable Gutenberg for pages with registered templates.
     *
     * @param bool    $use  Whether the block editor should be used.
     * @param WP_Post $post The post being edited.
     * @return bool
     */
    public function filter_block_editor_for_post( $use, $post ) {
        if ( 'page' !== $post->post_type ) {
            return $use;
        }

        if ( $this->is_registered_template_page( $post->ID ) ) {
            return false;
        }

        return $use;
    }

    /**
     * Remove the classic editor content area for template pages.
     *
     * Runs at priority 1 on add_meta_boxes_page, before meta box registration.
     *
     * @param WP_Post $post The post being edited.
     */
    public function maybe_remove_classic_editor( $post ) {
        if ( $this->is_registered_template_page( $post->ID ) ) {
            remove_post_type_support( 'page', 'editor' );
        }
    }

    /**
     * AJAX handler for oEmbed preview.
     */
    public function ajax_oembed_preview() {
        check_ajax_referer('bookings_and_flights_oembed_preview', 'nonce');

        if (!current_user_can('edit_pages')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'bookings_and_flights-content-manager')));
        }

        $url    = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        $scheme = wp_parse_url( $url, PHP_URL_SCHEME );

        if ( empty( $url ) || ! in_array( $scheme, array( 'http', 'https' ), true ) || ! wp_http_validate_url( $url ) ) {
            wp_send_json_error(array('message' => __('Invalid URL.', 'bookings_and_flights-content-manager')));
        }

        $html = wp_oembed_get($url);

        if ($html) {
            wp_send_json_success(array('html' => $html));
        } else {
            wp_send_json_error(array('message' => __('Unable to retrieve embed for this URL.', 'bookings_and_flights-content-manager')));
        }
    }

    /**
     * Check if a page uses a registered template.
     *
     * @param int $post_id Post ID.
     * @return bool
     */
    private function is_registered_template_page( $post_id ) {
        $template = get_page_template_slug( $post_id );

        if ( empty( $template ) || 'default' === $template ) {
            return false;
        }

        $template_map = Bookings_And_Flights_Config::get_template_map();

        return isset( $template_map[ $template ] );
    }
}
