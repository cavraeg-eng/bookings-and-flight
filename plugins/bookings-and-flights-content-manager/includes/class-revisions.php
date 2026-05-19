<?php
/**
 * Revisions Class
 *
 * Tracks custom field changes in WordPress revisions.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Revision support for custom fields.
 */
class Bookings_And_Flights_Revisions {

    private static $instance = null;

    /**
     * Virtual revision field key used for the diff screen.
     */
    const REVISION_FIELD_KEY = 'bookings_and_flights_custom_fields';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Register hooks.
     */
    public function init() {
        // Register meta with revisions_enabled for WP 6.4+.
        add_action('admin_init', array($this, 'register_meta_for_revisions'), 6);

        // Copy field meta from parent post to revision after meta box saves (priority 20, after save at 10).
        add_action('save_post', array($this, 'save_revision_meta'), 20, 2);

        // Restore field meta from revision back to parent post.
        add_action('wp_restore_post_revision', array($this, 'restore_revision_meta'), 10, 2);

        // Register a "Custom Fields" entry in the diff screen.
        add_filter('_wp_post_revision_fields', array($this, 'add_revision_diff_field'), 10, 2);

        // Render the diff content for our virtual field.
        add_filter('_wp_post_revision_field_' . self::REVISION_FIELD_KEY, array($this, 'render_revision_diff_field'), 10, 4);
    }

    /**
     * Register meta keys with revisions_enabled for WP 6.4+.
     */
    public function register_meta_for_revisions() {
        if (!class_exists('Bookings_And_Flights_Fields')) {
            return;
        }

        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        $templates = $fields_registry->get_templates();

        foreach ($templates as $template) {
            $fields = $fields_registry->get_fields($template);

            foreach ($fields as $key => $field) {
                $meta_key = Bookings_And_Flights_Config::meta_key($key);

                register_meta('post', $meta_key, array(
                    'type'              => 'string',
                    'single'            => true,
                    'revisions_enabled' => true,
                ));
            }
        }
    }

    /**
     * Copy all field meta from parent post to the revision.
     *
     * Runs at priority 20 on save_post, after meta box save at priority 10.
     *
     * @param int     $post_id Post ID (could be the revision).
     * @param WP_Post $post    Post object.
     */
    public function save_revision_meta($post_id, $post) {
        if ('page' !== $post->post_type) {
            return;
        }

        // Only act on the parent post, not on the revision itself.
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Check if this page uses a registered template.
        $template = get_page_template_slug($post_id);
        if (empty($template)) {
            return;
        }

        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        if (!$fields_registry->has_fields($template)) {
            return;
        }

        // Get the latest revision for this post.
        $revisions = wp_get_post_revisions($post_id, array(
            'posts_per_page' => 1,
            'orderby'        => 'ID',
            'order'          => 'DESC',
        ));

        if (empty($revisions)) {
            return;
        }

        $revision = reset($revisions);
        $fields   = $fields_registry->get_fields($template);

        foreach ($fields as $key => $field) {
            $meta_key = Bookings_And_Flights_Config::meta_key($key);
            $value    = get_post_meta($post_id, $meta_key, true);

            if ('' !== $value && false !== $value) {
                update_metadata('post', $revision->ID, $meta_key, $value);
            }
        }
    }

    /**
     * Restore field meta from revision back to the parent post.
     *
     * @param int $post_id     Parent post ID.
     * @param int $revision_id Revision post ID.
     */
    public function restore_revision_meta($post_id, $revision_id) {
        $template = get_page_template_slug($post_id);
        if (empty($template)) {
            return;
        }

        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        if (!$fields_registry->has_fields($template)) {
            return;
        }

        $fields = $fields_registry->get_fields($template);

        foreach ($fields as $key => $field) {
            $meta_key = Bookings_And_Flights_Config::meta_key($key);
            $value    = get_metadata('post', $revision_id, $meta_key, true);

            if ('' !== $value && false !== $value) {
                update_post_meta($post_id, $meta_key, $value);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }

    /**
     * Register a "Custom Fields" entry in the revision diff screen.
     *
     * @param array $fields Revision fields.
     * @param array $post   Post data (or post array).
     * @return array
     */
    public function add_revision_diff_field($fields, $post = null) {
        $fields[self::REVISION_FIELD_KEY] = __('Custom Fields', 'bookings_and_flights-content-manager');
        return $fields;
    }

    /**
     * Render all field values as "Label: value" lines for diff comparison.
     *
     * @param string  $value       Current field value (empty for virtual fields).
     * @param string  $field_key   The revision field key.
     * @param WP_Post $revision    The revision post object.
     * @param string  $context     Context ('from' or 'to').
     * @return string
     */
    public function render_revision_diff_field($value, $field_key, $revision, $context) {
        // Determine the parent post ID.
        $parent_id = wp_is_post_revision($revision);
        if (!$parent_id) {
            $parent_id = $revision->ID;
        }

        $template = get_page_template_slug($parent_id);
        if (empty($template)) {
            return '';
        }

        $fields_registry = Bookings_And_Flights_Fields::get_instance();
        if (!$fields_registry->has_fields($template)) {
            return '';
        }

        $fields = $fields_registry->get_fields($template);
        $lines  = array();

        foreach ($fields as $key => $field) {
            $meta_key   = Bookings_And_Flights_Config::meta_key($key);
            $meta_value = get_metadata('post', $revision->ID, $meta_key, true);

            if (is_array($meta_value)) {
                $meta_value = wp_json_encode($meta_value, JSON_UNESCAPED_SLASHES);
            }

            $label = isset($field['label']) ? $field['label'] : $key;
            $lines[] = $label . ': ' . (string) $meta_value;
        }

        return implode("\n", $lines);
    }
}
