<?php
/**
 * Meta Boxes Class
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

/**
 * Meta Boxes Manager
 */
class Bookings_And_Flights_Meta_Boxes {

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
    private function render_field_input($key, $field, $value) {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $default = isset($field['default']) ? $field['default'] : '';

        // For placeholder, truncate long defaults
        $placeholder = is_string($default) ? $default : '';
        if (strlen($placeholder) > 100) {
            $placeholder = substr($placeholder, 0, 100) . '...';
        }

        switch ($type) {
            case 'image':
                $this->render_image_field($key, $value, $field);
                break;

            case 'gallery':
                $this->render_gallery_field($key, $value, $field);
                break;

            case 'textarea':
                $rows = isset($field['rows']) ? intval($field['rows']) : 4;
                printf(
                    '<textarea name="%s" id="%s" rows="%d" placeholder="%s">%s</textarea>',
                    esc_attr($key),
                    esc_attr($key),
                    $rows,
                    esc_attr($placeholder),
                    esc_textarea($value)
                );
                break;

            case 'wysiwyg':
                $settings = array(
                    'textarea_name' => $key,
                    'textarea_rows' => isset($field['rows']) ? intval($field['rows']) : 8,
                    'media_buttons' => true,
                    'teeny' => false,
                );
                wp_editor($value, $key, $settings);
                break;

            case 'url':
                printf(
                    '<input type="url" name="%s" id="%s" value="%s" placeholder="%s" class="large-text">',
                    esc_attr($key),
                    esc_attr($key),
                    esc_url($value),
                    esc_attr($placeholder)
                );
                break;

            case 'email':
                printf(
                    '<input type="email" name="%s" id="%s" value="%s" placeholder="%s" class="large-text">',
                    esc_attr($key),
                    esc_attr($key),
                    esc_attr($value),
                    esc_attr($placeholder)
                );
                break;

            case 'number':
                $min = isset($field['min']) ? 'min="' . intval($field['min']) . '"' : '';
                $max = isset($field['max']) ? 'max="' . intval($field['max']) . '"' : '';
                $step = isset($field['step']) ? 'step="' . esc_attr($field['step']) . '"' : '';
                printf(
                    '<input type="number" name="%s" id="%s" value="%s" placeholder="%s" class="small-text" %s %s %s>',
                    esc_attr($key),
                    esc_attr($key),
                    $value !== '' ? floatval($value) : '',
                    esc_attr($default),
                    $min,
                    $max,
                    $step
                );
                break;

            case 'color':
                printf(
                    '<input type="text" name="%s" id="%s" value="%s" class="bookings_and_flights-color-picker" data-default-color="%s">',
                    esc_attr($key),
                    esc_attr($key),
                    esc_attr($value),
                    esc_attr($default)
                );
                break;

            case 'select':
                $options = isset($field['options']) ? $field['options'] : array();
                echo '<select name="' . esc_attr($key) . '" id="' . esc_attr($key) . '">';
                foreach ($options as $opt_value => $opt_label) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($opt_value),
                        selected($value, $opt_value, false),
                        esc_html($opt_label)
                    );
                }
                echo '</select>';
                break;

            case 'radio':
                $options = isset($field['options']) ? $field['options'] : array();
                echo '<div class="bookings_and_flights-radio-group">';
                foreach ($options as $opt_value => $opt_label) {
                    printf(
                        '<label><input type="radio" name="%s" value="%s" %s> %s</label>',
                        esc_attr($key),
                        esc_attr($opt_value),
                        checked($value, $opt_value, false),
                        esc_html($opt_label)
                    );
                }
                echo '</div>';
                break;

            case 'checkbox':
                printf(
                    '<input type="checkbox" name="%s" id="%s" value="1" %s>',
                    esc_attr($key),
                    esc_attr($key),
                    checked($value, 1, false)
                );
                break;

            case 'link':
                $this->render_link_field($key, $value, $field);
                break;

            case 'date':
                $input_type = !empty($field['time']) ? 'datetime-local' : 'date';
                $max_width = !empty($field['time']) ? '280px' : '200px';
                printf(
                    '<input type="%s" name="%s" id="%s" value="%s" class="bookings_and_flights-date-input" style="max-width:%s;">',
                    esc_attr($input_type),
                    esc_attr($key),
                    esc_attr($key),
                    esc_attr($value),
                    esc_attr($max_width)
                );
                break;

            case 'oembed':
                $this->render_oembed_field($key, $value, $field);
                break;

            case 'repeater':
                $this->render_repeater_field($key, $value, $field);
                break;

            case 'text':
            default:
                printf(
                    '<input type="text" name="%s" id="%s" value="%s" placeholder="%s" class="large-text">',
                    esc_attr($key),
                    esc_attr($key),
                    esc_attr($value),
                    esc_attr($placeholder)
                );
                break;
        }
    }

    /**
     * Render image upload field
     */
    private function render_image_field($key, $value, $field) {
        $image_url = '';

        if (!empty($value)) {
            $image_url = wp_get_attachment_image_url($value, 'thumbnail');
        }

        ?>
        <div class="bookings_and_flights-image-upload">
            <div class="bookings_and_flights-image-preview">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="">
                <?php else : ?>
                    <?php esc_html_e('No image selected', 'bookings_and_flights-content-manager'); ?>
                <?php endif; ?>
            </div>
            <div>
                <input type="hidden"
                       name="<?php echo esc_attr($key); ?>"
                       id="<?php echo esc_attr($key); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       class="bookings_and_flights-image-id">
                <button type="button"
                        class="button bookings_and_flights-upload-button"
                        data-target="<?php echo esc_attr($key); ?>">
                    <?php esc_html_e('Select Image', 'bookings_and_flights-content-manager'); ?>
                </button>
                <button type="button"
                        class="button bookings_and_flights-remove-button"
                        style="<?php echo empty($value) ? 'display:none;' : ''; ?>"
                        data-target="<?php echo esc_attr($key); ?>">
                    <?php esc_html_e('Remove', 'bookings_and_flights-content-manager'); ?>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Render gallery field
     */
    private function render_gallery_field($key, $value, $field) {
        $image_ids = array();
        if (!empty($value)) {
            if (is_array($value)) {
                $image_ids = $value;
            } elseif (is_string($value)) {
                $image_ids = array_filter(array_map('absint', explode(',', $value)));
            }
        }
        ?>
        <div class="bookings_and_flights-gallery-upload" data-field="<?php echo esc_attr($key); ?>">
            <div class="bookings_and_flights-gallery-preview">
                <?php foreach ($image_ids as $id) :
                    $url = wp_get_attachment_image_url($id, 'thumbnail');
                    if ($url) : ?>
                        <div class="bookings_and_flights-gallery-item" data-id="<?php echo esc_attr($id); ?>">
                            <img src="<?php echo esc_url($url); ?>" alt="">
                            <button type="button" class="bookings_and_flights-gallery-remove">&times;</button>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
            <input type="hidden"
                   name="<?php echo esc_attr($key); ?>"
                   id="<?php echo esc_attr($key); ?>"
                   value="<?php echo esc_attr(implode(',', $image_ids)); ?>"
                   class="bookings_and_flights-gallery-ids">
            <button type="button" class="button bookings_and_flights-gallery-button">
                <?php esc_html_e('Add Images', 'bookings_and_flights-content-manager'); ?>
            </button>
        </div>
        <?php
    }

    /**
     * Render link field (URL + Label side-by-side)
     */
    private function render_link_field($key, $value, $field) {
        $link_url   = '';
        $link_label = '';

        if (is_array($value)) {
            $link_url   = isset($value['url']) ? $value['url'] : '';
            $link_label = isset($value['label']) ? $value['label'] : '';
        }
        ?>
        <div class="bookings_and_flights-link-field">
            <div class="bookings_and_flights-link-field__input">
                <label for="<?php echo esc_attr($key); ?>_url"><?php esc_html_e('URL', 'bookings_and_flights-content-manager'); ?></label>
                <input type="url"
                       name="<?php echo esc_attr($key); ?>[url]"
                       id="<?php echo esc_attr($key); ?>_url"
                       value="<?php echo esc_url($link_url); ?>"
                       placeholder="https://"
                       class="large-text">
            </div>
            <div class="bookings_and_flights-link-field__input">
                <label for="<?php echo esc_attr($key); ?>_label"><?php esc_html_e('Label', 'bookings_and_flights-content-manager'); ?></label>
                <input type="text"
                       name="<?php echo esc_attr($key); ?>[label]"
                       id="<?php echo esc_attr($key); ?>_label"
                       value="<?php echo esc_attr($link_label); ?>"
                       placeholder="<?php esc_attr_e('Link text', 'bookings_and_flights-content-manager'); ?>"
                       class="large-text">
            </div>
        </div>
        <?php
    }

    /**
     * Render oEmbed field (URL input + live preview)
     */
    private function render_oembed_field($key, $value, $field) {
        $embed_html = '';
        if (!empty($value)) {
            $embed_html = wp_oembed_get($value);
        }
        ?>
        <div class="bookings_and_flights-oembed-field" data-field="<?php echo esc_attr($key); ?>">
            <input type="url"
                   name="<?php echo esc_attr($key); ?>"
                   id="<?php echo esc_attr($key); ?>"
                   value="<?php echo esc_url($value); ?>"
                   placeholder="https://www.youtube.com/watch?v=..."
                   class="large-text bookings_and_flights-oembed-input">
            <div class="bookings_and_flights-oembed-preview">
                <?php if ($embed_html) : ?>
                    <?php echo $embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php elseif (!empty($value)) : ?>
                    <p class="bookings_and_flights-oembed-error"><?php esc_html_e('Unable to load embed preview.', 'bookings_and_flights-content-manager'); ?></p>
                <?php else : ?>
                    <p class="bookings_and_flights-oembed-placeholder"><?php esc_html_e('Enter a URL above to see a preview.', 'bookings_and_flights-content-manager'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render repeater field
     */
    private function render_repeater_field($key, $value, $field) {
        $rows = is_array($value) ? $value : array();
        $subfields = isset($field['subfields']) ? $field['subfields'] : array();
        ?>
        <div class="bookings_and_flights-repeater" data-field="<?php echo esc_attr($key); ?>">
            <div class="bookings_and_flights-repeater-rows">
                <?php foreach ($rows as $index => $row) : ?>
                    <div class="bookings_and_flights-repeater-row">
                        <div class="bookings_and_flights-repeater-handle">&#9776;</div>
                        <div class="bookings_and_flights-repeater-content">
                            <?php foreach ($subfields as $subkey => $subfield) :
                                $subvalue = isset($row[$subkey]) ? $row[$subkey] : '';
                                $subname = $key . '[' . $index . '][' . $subkey . ']';
                                ?>
                                <div class="bookings_and_flights-repeater-subfield">
                                    <label><?php echo esc_html($subfield['label']); ?></label>
                                    <?php $this->render_subfield_input($subname, $subfield, $subvalue); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="button bookings_and_flights-repeater-remove">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button bookings_and_flights-repeater-add">
                <?php esc_html_e('Add Row', 'bookings_and_flights-content-manager'); ?>
            </button>
            <script type="text/template" class="bookings_and_flights-repeater-template">
                <div class="bookings_and_flights-repeater-row">
                    <div class="bookings_and_flights-repeater-handle">&#9776;</div>
                    <div class="bookings_and_flights-repeater-content">
                        <?php foreach ($subfields as $subkey => $subfield) :
                            $subname = $key . '[{{INDEX}}][' . $subkey . ']';
                            ?>
                            <div class="bookings_and_flights-repeater-subfield">
                                <label><?php echo esc_html($subfield['label']); ?></label>
                                <?php $this->render_subfield_input($subname, $subfield, ''); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="button bookings_and_flights-repeater-remove">&times;</button>
                </div>
            </script>
        </div>
        <?php
    }

    /**
     * Render subfield input for repeater
     */
    private function render_subfield_input($name, $field, $value) {
        $type = isset($field['type']) ? $field['type'] : 'text';

        switch ($type) {
            case 'textarea':
                printf(
                    '<textarea name="%s" rows="2">%s</textarea>',
                    esc_attr($name),
                    esc_textarea($value)
                );
                break;

            case 'url':
                printf(
                    '<input type="url" name="%s" value="%s" class="large-text">',
                    esc_attr($name),
                    esc_url($value)
                );
                break;

            case 'text':
            default:
                printf(
                    '<input type="text" name="%s" value="%s" class="large-text">',
                    esc_attr($name),
                    esc_attr($value)
                );
                break;
        }
    }

    /**
     * Save meta box data
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
