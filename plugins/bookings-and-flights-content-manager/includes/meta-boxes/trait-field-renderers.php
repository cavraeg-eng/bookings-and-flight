<?php
/**
 * Field renderer methods for page template meta boxes.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined( 'ABSPATH' ) || exit;

trait Bookings_And_Flights_Meta_Boxes_Field_Renderers {
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
}
