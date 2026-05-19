<?php
/**
 * Meta Boxes Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/meta-boxes/trait-field-renderers.php';

/**
 * Meta Boxes Manager
 */
class Bookings_And_Flights_Meta_Boxes {

    use Bookings_And_Flights_Meta_Boxes_Field_Renderers;

    private static $instance = null;
    private $fields_registry = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Template to title mapping
     */
    private $templates = array();

    /**
     * Register all meta boxes
     */
    public function register() {
        $this->fields_registry = Bookings_And_Flights_Fields::get_instance();

        // Define template to title mapping
        $this->templates = Bookings_And_Flights_Config::get_template_map();

        // Use add_meta_boxes hook to register conditionally
        add_action('add_meta_boxes_page', array($this, 'add_template_meta_box'));

        // Save meta boxes
        add_action('save_post', array($this, 'save_meta_box'), 10, 2);
    }

    /**
     * Add meta box only for the current page's template
     */
    public function add_template_meta_box($post) {
        // Get the current page's template
        $current_template = get_page_template_slug($post->ID);

        // If no template set, check if it's being set via POST (template selector)
        if (empty($current_template) && isset($_GET['post'])) {
            $post_id = absint($_GET['post']);
            if ($post_id) {
                $current_template = get_post_meta($post_id, '_wp_page_template', true);
            }
        }

        if (!empty($current_template)) {
            $current_template = sanitize_text_field($current_template);
        }

        if (
            !empty($current_template)
            && $this->fields_registry
            && $this->fields_registry->has_fields($current_template)
        ) {
            $title = isset($this->templates[$current_template])
                ? $this->templates[$current_template]
                : $this->auto_title_from_template($current_template);

            add_meta_box(
                'bookings_and_flights-' . sanitize_title($current_template),
                $title,
                array($this, 'render_meta_box'),
                'page',
                'normal',
                'high',
                array('template' => $current_template)
            );
        }
    }

    private function auto_title_from_template($template) {
        $base = basename($template);
        $base = preg_replace('/\.php$/', '', $base);
        $base = preg_replace('/^page-/', '', $base);
        $base = str_replace(array('-', '_'), ' ', $base);
        $title = ucwords(trim($base));

        if ('' === $title) {
            $title = __('Page', 'bookings_and_flights-content-manager');
        }

        if (0 === strpos($template, 'page-') && false === stripos($title, 'page')) {
            $title .= ' ' . __('Page', 'bookings_and_flights-content-manager');
        }

        $title .= ' ' . __('Content', 'bookings_and_flights-content-manager');
        return $title;
    }

    /**
     * Render meta box content
     */
    public function render_meta_box($post, $meta_box) {
        $template = $meta_box['args']['template'];
        $fields = $this->fields_registry->get_fields($template);

        if (empty($fields)) {
            echo '<p>' . esc_html__('No fields defined for this template.', 'bookings_and_flights-content-manager') . '</p>';
            return;
        }

        $groups = $this->group_fields($fields);
        $this->render_sections($groups, $post);

        // Add nonce for security
        wp_nonce_field('bookings_and_flights_save_fields', 'bookings_and_flights_meta_nonce');
    }

    /**
     * Group fields by section label.
     */
    private function group_fields($fields) {
        $groups = array();

        foreach ($fields as $key => $field) {
            $group_name = isset($field['section']) && '' !== $field['section']
                ? $field['section']
                : __('General', 'bookings_and_flights-content-manager');

            if (!isset($groups[$group_name])) {
                $groups[$group_name] = array(
                    'id' => sanitize_title($group_name),
                    'label' => $group_name,
                    'fields' => array(),
                );
            }

            $groups[$group_name]['fields'][$key] = $field;
        }

        return $groups;
    }

    /**
     * Render sectioned interface.
     *
     * Single section: renders flat (no tabs).
     * Multiple sections: renders horizontal tab bar + tab panels.
     */
    private function render_sections($groups, $post) {
        // Single section — render flat, no tabs.
        if ( count( $groups ) <= 1 ) {
            foreach ( $groups as $group ) {
                echo '<div class="bookings_and_flights-section">';
                printf(
                    '<h3 class="bookings_and_flights-section-title">%s</h3>',
                    esc_html( $group['label'] )
                );
                echo '<div class="bookings_and_flights-section-body">';
                $this->render_fields( $group['fields'], $post );
                echo '</div>';
                echo '</div>';
            }
            return;
        }

        // Multiple sections — render as tabs.
        $template = isset( $_GET['post'] ) ? get_page_template_slug( absint( $_GET['post'] ) ) : '';
        $tabs_id  = sanitize_title( $template ? $template : 'fields' );
        $groups   = array_values( $groups );

        echo '<div class="bookings_and_flights-tabs" data-tabs-id="' . esc_attr( $tabs_id ) . '">';

        // Tab navigation.
        echo '<div class="bookings_and_flights-tabs__nav" role="tablist">';
        foreach ( $groups as $index => $group ) {
            $tab_id   = $tabs_id . '-tab-' . $group['id'];
            $panel_id = $tabs_id . '-panel-' . $group['id'];
            $selected = ( 0 === $index ) ? 'true' : 'false';
            $tabindex = ( 0 === $index ) ? '0' : '-1';

            printf(
                '<button type="button" role="tab" class="bookings_and_flights-tabs__tab%s" id="%s" aria-selected="%s" aria-controls="%s" tabindex="%s">%s</button>',
                ( 0 === $index ) ? ' bookings_and_flights-tabs__tab--active' : '',
                esc_attr( $tab_id ),
                esc_attr( $selected ),
                esc_attr( $panel_id ),
                esc_attr( $tabindex ),
                esc_html( $group['label'] )
            );
        }
        echo '</div>';

        // Tab panels.
        foreach ( $groups as $index => $group ) {
            $tab_id   = $tabs_id . '-tab-' . $group['id'];
            $panel_id = $tabs_id . '-panel-' . $group['id'];
            $hidden   = ( 0 !== $index ) ? ' hidden' : '';

            printf(
                '<div role="tabpanel" class="bookings_and_flights-tabs__panel" id="%s" aria-labelledby="%s"%s>',
                esc_attr( $panel_id ),
                esc_attr( $tab_id ),
                $hidden
            );
            $this->render_fields( $group['fields'], $post );
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Render individual fields
     */
    private function render_fields($fields, $post) {
        echo '<div class="bookings_and_flights-fields">';

        foreach ($fields as $key => $field) {
            $value = get_post_meta($post->ID, Bookings_And_Flights_Config::meta_key($key), true);

            // Build field wrapper classes.
            $classes = array('bookings_and_flights-field');
            if (!empty($field['required'])) {
                $classes[] = 'required';
            }

            // Conditional visibility attributes.
            $conditional_attrs = '';
            if (!empty($field['show_when']) && is_array($field['show_when'])) {
                $show_field = isset($field['show_when']['field']) ? $field['show_when']['field'] : '';
                $show_value = isset($field['show_when']['value']) ? $field['show_when']['value'] : '';

                if ('' !== $show_field) {
                    $classes[] = 'bookings_and_flights-field--conditional';
                    $conditional_attrs = sprintf(
                        ' data-show-when-field="%s" data-show-when-value="%s"',
                        esc_attr($show_field),
                        esc_attr($show_value)
                    );

                    // Server-side evaluation to prevent FOUC.
                    $controller_value = get_post_meta($post->ID, Bookings_And_Flights_Config::meta_key($show_field), true);
                    if ((string) $controller_value !== (string) $show_value) {
                        $classes[] = 'bookings_and_flights-field--hidden';
                    }
                }
            }

            printf('<div class="%s"%s>', esc_attr(implode(' ', $classes)), $conditional_attrs);

            // Label
            printf(
                '<label for="%s">%s</label>',
                esc_attr($key),
                esc_html($field['label'])
            );

            // Render field by type
            $this->render_field_input($key, $field, $value);

            // Help text (if provided)
            if (!empty($field['help'])) {
                printf(
                    '<p class="description">%s</p>',
                    esc_html($field['help'])
                );
            }

            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Render individual field input
     */
    public function save_meta_box($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['bookings_and_flights_meta_nonce']) ||
            !wp_verify_nonce($_POST['bookings_and_flights_meta_nonce'], 'bookings_and_flights_save_fields')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Only save for pages
        if ('page' !== $post->post_type) {
            return;
        }

        // Get template
        $template = get_page_template_slug($post_id);
        if (empty($template)) {
            // Try to determine template from meta
            $template = get_post_meta($post_id, '_wp_page_template', true);
        }

        if (empty($template)) {
            return;
        }

        // Get fields for this template
        $fields = $this->fields_registry->get_fields($template);

        if (empty($fields)) {
            return;
        }

        // Save each field
        foreach ($fields as $key => $field) {
            if (!isset($_POST[$key])) {
                // Handle checkbox - unchecked means not in POST
                if ($field['type'] === 'checkbox') {
                    update_post_meta($post_id, Bookings_And_Flights_Config::meta_key($key), 0);
                } elseif (in_array($field['type'], array('repeater', 'gallery'), true)) {
                    update_post_meta($post_id, Bookings_And_Flights_Config::meta_key($key), array());
                }
                continue;
            }

            $value = wp_unslash( $_POST[$key] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

            // Sanitize based on type
            $type = isset($field['type']) ? $field['type'] : 'text';
            $value = Bookings_And_Flights_Sanitizer::sanitize($value, $type, $field);

            // Update meta
            update_post_meta($post_id, Bookings_And_Flights_Config::meta_key($key), $value);
        }
    }
}
