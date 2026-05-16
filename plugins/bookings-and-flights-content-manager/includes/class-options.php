<?php
/**
 * Global Options Page
 *
 * Provides a settings page for site-wide content that isn't tied to a specific page
 * (social links, contact info, CTA defaults, footer description).
 *
 * Learned from: DJFuse build required building global options from scratch mid-project.
 * The footer had hardcoded social links, contact info, and CTA content that needed
 * to be editable by the client without touching theme files.
 *
 * @package Bookings and Flights_Content_Manager
 */

defined('ABSPATH') || exit;

class Bookings_And_Flights_Options {

    /**
     * Option group name
     */
    const OPTION_GROUP = 'bookings_and_flights_global_options';

    /**
     * Option name in wp_options table
     */
    const OPTION_NAME = 'bookings_and_flights_global';

    /**
     * Single instance
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
     * Initialize
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Add settings page under Settings menu
     */
    public function add_options_page() {
        add_options_page(
            __('Bookings and Flights Settings', 'bookings_and_flights-content-manager'),
            __('Bookings and Flights', 'bookings_and_flights-content-manager'),
            'manage_options',
            'bookings_and_flights-settings',
            array($this, 'render_options_page')
        );
    }

    /**
     * Enqueue admin assets on options page
     */
    public function enqueue_admin_assets($hook) {
        if ('settings_page_bookings_and_flights-settings' !== $hook) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'bookings_and_flights-admin',
            BOOKINGS_AND_FLIGHTS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BOOKINGS_AND_FLIGHTS_VERSION
        );

        wp_enqueue_script(
            'bookings_and_flights-admin',
            BOOKINGS_AND_FLIGHTS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            BOOKINGS_AND_FLIGHTS_VERSION,
            true
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            self::OPTION_GROUP,
            self::OPTION_NAME,
            array($this, 'sanitize_options')
        );
    }

    /**
     * Get all global fields definition
     *
     * Override this method or filter 'bookings_and_flights_global_fields' to add/remove fields.
     */
    public static function get_fields() {
        $fields = array(

            // ============================================
            // EDITOR SETTINGS
            // ============================================
            'disable_block_editor' => array(
                'label'   => __('Disable Block Editor for Template Pages', 'bookings_and_flights-content-manager'),
                'type'    => 'checkbox',
                'section' => 'editor',
                'default' => 1,
                'help'    => __('When enabled, pages with registered templates use only custom fields — the block editor and classic editor content area are removed.', 'bookings_and_flights-content-manager'),
            ),

            // ============================================
            // SOCIAL LINKS
            // ============================================
            'social_facebook' => array(
                'label'   => __('Facebook URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),
            'social_instagram' => array(
                'label'   => __('Instagram URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),
            'social_x' => array(
                'label'   => __('X (Twitter) URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),
            'social_tiktok' => array(
                'label'   => __('TikTok URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),
            'social_youtube' => array(
                'label'   => __('YouTube URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),
            'social_linkedin' => array(
                'label'   => __('LinkedIn URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'social',
                'default' => '',
            ),

            // ============================================
            // CONTACT INFO
            // ============================================
            'contact_email' => array(
                'label'   => __('Contact Email', 'bookings_and_flights-content-manager'),
                'type'    => 'email',
                'section' => 'contact',
                'default' => '',
            ),
            'contact_phone' => array(
                'label'   => __('Phone Number', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'contact',
                'default' => '',
            ),
            'contact_address_1' => array(
                'label'   => __('Address Line 1', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'contact',
                'default' => '',
            ),
            'contact_address_2' => array(
                'label'   => __('Address Line 2', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'contact',
                'default' => '',
            ),
            'contact_location' => array(
                'label'   => __('Location Label', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'contact',
                'help'    => __('Short location label, e.g. "Worldwide" or "Kingston, Jamaica"', 'bookings_and_flights-content-manager'),
                'default' => '',
            ),

            // ============================================
            // FOOTER
            // ============================================
            'footer_description' => array(
                'label'   => __('Footer Description', 'bookings_and_flights-content-manager'),
                'type'    => 'textarea',
                'section' => 'footer',
                'default' => '',
                'rows'    => 3,
            ),

            // ============================================
            // CTA DEFAULTS
            // ============================================
            'cta_heading' => array(
                'label'   => __('CTA Heading', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'cta',
                'default' => __('Ready to Get Started?', 'bookings_and_flights-content-manager'),
            ),
            'cta_text' => array(
                'label'   => __('CTA Description', 'bookings_and_flights-content-manager'),
                'type'    => 'textarea',
                'section' => 'cta',
                'default' => '',
                'rows'    => 2,
            ),
            'cta_button_text' => array(
                'label'   => __('CTA Button Text', 'bookings_and_flights-content-manager'),
                'type'    => 'text',
                'section' => 'cta',
                'default' => __('Get in Touch', 'bookings_and_flights-content-manager'),
            ),
            'cta_button_url' => array(
                'label'   => __('CTA Button URL', 'bookings_and_flights-content-manager'),
                'type'    => 'url',
                'section' => 'cta',
                'default' => '/contact/',
            ),
        );

        return apply_filters('bookings_and_flights_global_fields', $fields);
    }

    /**
     * Get a single global option value
     *
     * @param string $key     Field key
     * @param mixed  $default Default value
     * @return mixed
     */
    public static function get($key, $default = '') {
        $options = get_option(self::OPTION_NAME, array());

        if (isset($options[$key]) && '' !== $options[$key]) {
            return $options[$key];
        }

        // Check field definitions for default
        $fields = self::get_fields();
        if (isset($fields[$key]['default']) && '' !== $fields[$key]['default']) {
            return $fields[$key]['default'];
        }

        return $default;
    }

    /**
     * Sanitize options on save
     */
    public function sanitize_options($input) {
        $sanitized = array();
        $fields = self::get_fields();

        foreach ($fields as $key => $field) {
            $type = isset($field['type']) ? $field['type'] : 'text';

            // Unchecked checkboxes are absent from $_POST.
            if ( 'checkbox' === $type ) {
                $sanitized[$key] = ! empty( $input[$key] ) ? 1 : 0;
                continue;
            }

            if (!isset($input[$key])) {
                $sanitized[$key] = '';
                continue;
            }

            switch ($type) {
                case 'url':
                    $sanitized[$key] = esc_url_raw($input[$key]);
                    break;
                case 'email':
                    $sanitized[$key] = sanitize_email($input[$key]);
                    break;
                case 'textarea':
                    $sanitized[$key] = sanitize_textarea_field($input[$key]);
                    break;
                case 'number':
                    $sanitized[$key] = floatval($input[$key]);
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($input[$key]);
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Section descriptions shown beneath the card heading.
     *
     * @return array
     */
    private static function get_section_descriptions() {
        return array(
            'editor'  => __('Control how the WordPress editor behaves for pages using custom templates.', 'bookings_and_flights-content-manager'),
            'social'  => __('Social media profile URLs used in the header, footer, and SEO markup.', 'bookings_and_flights-content-manager'),
            'contact' => __('Business contact details displayed in the footer and contact page.', 'bookings_and_flights-content-manager'),
            'footer'  => __('Content that appears in the site-wide footer area.', 'bookings_and_flights-content-manager'),
            'cta'     => __('Default call-to-action content reused across multiple pages.', 'bookings_and_flights-content-manager'),
        );
    }

    /**
     * Render the options page
     */
    public function render_options_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $fields  = self::get_fields();
        $options = get_option(self::OPTION_NAME, array());

        // Group fields by section.
        $sections = array();
        foreach ($fields as $key => $field) {
            $section = isset($field['section']) ? $field['section'] : 'general';
            $sections[$section][$key] = $field;
        }

        $section_labels = array(
            'editor'  => __('Editor Settings', 'bookings_and_flights-content-manager'),
            'social'  => __('Social Links', 'bookings_and_flights-content-manager'),
            'contact' => __('Contact Information', 'bookings_and_flights-content-manager'),
            'footer'  => __('Footer Content', 'bookings_and_flights-content-manager'),
            'cta'     => __('CTA Defaults', 'bookings_and_flights-content-manager'),
            'general' => __('General', 'bookings_and_flights-content-manager'),
        );

        $section_descriptions = self::get_section_descriptions();

        ?>
        <div class="bookings_and_flights-settings">

            <div class="bookings_and_flights-settings__header">
                <h1 class="bookings_and_flights-settings__title"><?php esc_html_e('Bookings and Flights Settings', 'bookings_and_flights-content-manager'); ?></h1>
                <p class="bookings_and_flights-settings__desc"><?php esc_html_e('Manage global content, social links, and editor behaviour for your site.', 'bookings_and_flights-content-manager'); ?></p>
            </div>

            <form method="post" action="options.php">
                <?php settings_fields(self::OPTION_GROUP); ?>

                <div class="bookings_and_flights-settings__grid">
                    <?php foreach ($sections as $section_key => $section_fields) : ?>
                        <div class="bookings_and_flights-settings__card <?php echo esc_attr('bookings_and_flights-settings__card--' . $section_key); ?>">
                            <div class="bookings_and_flights-settings__card-header">
                                <h2 class="bookings_and_flights-settings__card-title">
                                    <?php echo esc_html(isset($section_labels[$section_key]) ? $section_labels[$section_key] : ucfirst($section_key)); ?>
                                </h2>
                                <?php if (!empty($section_descriptions[$section_key])) : ?>
                                    <p class="bookings_and_flights-settings__card-desc">
                                        <?php echo esc_html($section_descriptions[$section_key]); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="bookings_and_flights-settings__card-body">
                                <?php foreach ($section_fields as $key => $field) :
                                    $value = isset($options[$key]) ? $options[$key] : (isset($field['default']) ? $field['default'] : '');
                                    $type  = isset($field['type']) ? $field['type'] : 'text';
                                    $name  = self::OPTION_NAME . '[' . $key . ']';
                                    ?>
                                    <div class="bookings_and_flights-settings__field">
                                        <?php if ('checkbox' === $type) : ?>
                                            <label class="bookings_and_flights-settings__toggle" for="<?php echo esc_attr($key); ?>">
                                                <input
                                                    type="checkbox"
                                                    id="<?php echo esc_attr($key); ?>"
                                                    name="<?php echo esc_attr($name); ?>"
                                                    value="1"
                                                    <?php checked($value, 1); ?>
                                                >
                                                <span class="bookings_and_flights-settings__toggle-label">
                                                    <?php esc_html_e('Enabled', 'bookings_and_flights-content-manager'); ?>
                                                </span>
                                            </label>
                                        <?php else : ?>
                                            <label class="bookings_and_flights-settings__label" for="<?php echo esc_attr($key); ?>">
                                                <?php echo esc_html($field['label']); ?>
                                            </label>

                                            <?php if ('textarea' === $type) : ?>
                                                <textarea
                                                    id="<?php echo esc_attr($key); ?>"
                                                    name="<?php echo esc_attr($name); ?>"
                                                    rows="<?php echo esc_attr(isset($field['rows']) ? $field['rows'] : 4); ?>"
                                                    class="bookings_and_flights-settings__textarea"
                                                ><?php echo esc_textarea($value); ?></textarea>
                                            <?php else : ?>
                                                <input
                                                    type="<?php echo esc_attr($type); ?>"
                                                    id="<?php echo esc_attr($key); ?>"
                                                    name="<?php echo esc_attr($name); ?>"
                                                    value="<?php echo esc_attr($value); ?>"
                                                    class="bookings_and_flights-settings__input"
                                                >
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (!empty($field['help'])) : ?>
                                            <p class="bookings_and_flights-settings__help"><?php echo esc_html($field['help']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bookings_and_flights-settings__footer">
                    <?php submit_button(__('Save Changes', 'bookings_and_flights-content-manager'), 'primary', 'submit', false); ?>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Seed default values on plugin activation
     */
    public static function seed_defaults() {
        $existing = get_option(self::OPTION_NAME, false);

        if (false !== $existing) {
            return; // Don't overwrite existing values
        }

        $defaults = array();
        $fields = self::get_fields();

        foreach ($fields as $key => $field) {
            if (isset($field['default'])) {
                $defaults[$key] = $field['default'];
            }
        }

        update_option(self::OPTION_NAME, $defaults);
    }
}
