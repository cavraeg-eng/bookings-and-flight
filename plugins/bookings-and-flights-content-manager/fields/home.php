<?php
/**
 * Home Page Fields
 * Template: page-home.php
 *
 * This is an example field definition file. Create similar files
 * for each page template that needs editable content.
 *
 * Field types supported:
 * - text: Single line text input
 * - textarea: Multi-line text input
 * - wysiwyg: Rich text editor
 * - image: Single image upload
 * - gallery: Multiple image upload
 * - url: URL input with validation
 * - email: Email input with validation
 * - number: Numeric input with min/max/step
 * - color: Color picker
 * - select: Dropdown select
 * - radio: Radio button group
 * - checkbox: Single checkbox
 * - repeater: Repeatable row group
 */

defined('ABSPATH') || exit;

$fields = array(
    // ========================================
    // HERO SECTION
    // ========================================
    'hero_image_id' => array(
        'label'   => __('Hero Image', 'bookings_and_flights-content-manager'),
        'type'    => 'image',
        'default' => 0,
        'help'    => __('Main hero background image (recommended: 1920x1080)', 'bookings_and_flights-content-manager'),
    ),
    'hero_tagline' => array(
        'label'   => __('Hero Tagline', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Welcome to Bookings and Flights', 'bookings_and_flights-content-manager'),
    ),
    'hero_title' => array(
        'label'   => __('Hero Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Your Compelling Headline Here', 'bookings_and_flights-content-manager'),
    ),
    'hero_description' => array(
        'label'   => __('Hero Description', 'bookings_and_flights-content-manager'),
        'type'    => 'textarea',
        'default' => __('A brief, compelling description that captures your value proposition and encourages visitors to explore further.', 'bookings_and_flights-content-manager'),
    ),
    'hero_cta_text' => array(
        'label'   => __('Hero CTA Button Text', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Get Started', 'bookings_and_flights-content-manager'),
    ),
    'hero_cta_url' => array(
        'label'   => __('Hero CTA Button URL', 'bookings_and_flights-content-manager'),
        'type'    => 'url',
        'default' => '/contact/',
    ),
    'hero_cta2_text' => array(
        'label'   => __('Hero Secondary CTA Text', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Learn More', 'bookings_and_flights-content-manager'),
    ),
    'hero_cta2_url' => array(
        'label'   => __('Hero Secondary CTA URL', 'bookings_and_flights-content-manager'),
        'type'    => 'url',
        'default' => '/about/',
    ),

    // ========================================
    // FEATURES SECTION
    // ========================================
    'features_tagline' => array(
        'label'   => __('Features Tagline', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Why Choose Us', 'bookings_and_flights-content-manager'),
    ),
    'features_title' => array(
        'label'   => __('Features Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('What Makes Us Different', 'bookings_and_flights-content-manager'),
    ),
    'features_description' => array(
        'label'   => __('Features Description', 'bookings_and_flights-content-manager'),
        'type'    => 'textarea',
        'default' => __('Discover the key benefits that set us apart from the competition.', 'bookings_and_flights-content-manager'),
    ),
    'features_items' => array(
        'label'     => __('Feature Items', 'bookings_and_flights-content-manager'),
        'type'      => 'repeater',
        'help'      => __('Add feature cards with icon, title, and description.', 'bookings_and_flights-content-manager'),
        'subfields' => array(
            'icon' => array(
                'label' => __('Icon Name', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
            'title' => array(
                'label' => __('Title', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
            'description' => array(
                'label' => __('Description', 'bookings_and_flights-content-manager'),
                'type'  => 'textarea',
            ),
        ),
    ),

    // ========================================
    // ABOUT SECTION
    // ========================================
    'about_tagline' => array(
        'label'   => __('About Tagline', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Our Story', 'bookings_and_flights-content-manager'),
    ),
    'about_title' => array(
        'label'   => __('About Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('About Bookings and Flights', 'bookings_and_flights-content-manager'),
    ),
    'about_content' => array(
        'label'   => __('About Content', 'bookings_and_flights-content-manager'),
        'type'    => 'wysiwyg',
        'default' => __('<p>Tell your story here. What makes your business unique? What are your values and mission?</p><p>Use this space to connect with your audience on a personal level.</p>', 'bookings_and_flights-content-manager'),
    ),
    'about_image_id' => array(
        'label'   => __('About Image', 'bookings_and_flights-content-manager'),
        'type'    => 'image',
        'default' => 0,
    ),
    'about_cta_text' => array(
        'label'   => __('About CTA Text', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Learn More About Us', 'bookings_and_flights-content-manager'),
    ),
    'about_cta_url' => array(
        'label'   => __('About CTA URL', 'bookings_and_flights-content-manager'),
        'type'    => 'url',
        'default' => '/about/',
    ),

    // ========================================
    // TESTIMONIALS SECTION
    // ========================================
    'testimonials_tagline' => array(
        'label'   => __('Testimonials Tagline', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('What Our Clients Say', 'bookings_and_flights-content-manager'),
    ),
    'testimonials_title' => array(
        'label'   => __('Testimonials Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Trusted by Many', 'bookings_and_flights-content-manager'),
    ),
    'testimonials_items' => array(
        'label'     => __('Testimonials', 'bookings_and_flights-content-manager'),
        'type'      => 'repeater',
        'help'      => __('Add customer testimonials.', 'bookings_and_flights-content-manager'),
        'subfields' => array(
            'quote' => array(
                'label' => __('Quote', 'bookings_and_flights-content-manager'),
                'type'  => 'textarea',
            ),
            'author' => array(
                'label' => __('Author Name', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
            'role' => array(
                'label' => __('Author Role/Company', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
        ),
    ),

    // ========================================
    // CTA SECTION
    // ========================================
    'cta_title' => array(
        'label'   => __('CTA Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Ready to Get Started?', 'bookings_and_flights-content-manager'),
    ),
    'cta_description' => array(
        'label'   => __('CTA Description', 'bookings_and_flights-content-manager'),
        'type'    => 'textarea',
        'default' => __('Contact us today to learn how we can help you achieve your goals.', 'bookings_and_flights-content-manager'),
    ),
    'cta_button_text' => array(
        'label'   => __('CTA Button Text', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Contact Us', 'bookings_and_flights-content-manager'),
    ),
    'cta_button_url' => array(
        'label'   => __('CTA Button URL', 'bookings_and_flights-content-manager'),
        'type'    => 'url',
        'default' => '/contact/',
    ),
    'cta_background_color' => array(
        'label'   => __('CTA Background Color', 'bookings_and_flights-content-manager'),
        'type'    => 'color',
        'default' => '#1a365d',
    ),

    // ========================================
    // CTA SECTION — Conditional Visibility Example
    // ========================================
    'cta_show_video' => array(
        'label'   => __('Show Video in CTA?', 'bookings_and_flights-content-manager'),
        'type'    => 'checkbox',
        'default' => 0,
        'help'    => __('Check to display a video embed in the CTA section.', 'bookings_and_flights-content-manager'),
    ),
    'cta_video_url' => array(
        'label'     => __('CTA Video URL', 'bookings_and_flights-content-manager'),
        'type'      => 'oembed',
        'help'      => __('Paste a YouTube or Vimeo URL for the CTA video.', 'bookings_and_flights-content-manager'),
        'show_when' => array('field' => 'cta_show_video', 'value' => '1'),
    ),

    // ========================================
    // ABOUT SECTION — Link & Date Examples
    // ========================================
    'about_external_link' => array(
        'label' => __('About External Link', 'bookings_and_flights-content-manager'),
        'type'  => 'link',
        'help'  => __('Optional external link displayed in the about section.', 'bookings_and_flights-content-manager'),
    ),
    'about_established_date' => array(
        'label' => __('Established Date', 'bookings_and_flights-content-manager'),
        'type'  => 'date',
        'help'  => __('When the business was established.', 'bookings_and_flights-content-manager'),
    ),
    'about_next_event' => array(
        'label' => __('Next Event Date & Time', 'bookings_and_flights-content-manager'),
        'type'  => 'date',
        'time'  => true,
        'help'  => __('Date and time of the next event (datetime-local example).', 'bookings_and_flights-content-manager'),
    ),

    // ========================================
    // GALLERY SECTION (Example)
    // ========================================
    'gallery_title' => array(
        'label'   => __('Gallery Title', 'bookings_and_flights-content-manager'),
        'type'    => 'text',
        'default' => __('Our Work', 'bookings_and_flights-content-manager'),
    ),
    'gallery_images' => array(
        'label' => __('Gallery Images', 'bookings_and_flights-content-manager'),
        'type'  => 'gallery',
        'help'  => __('Select multiple images for the gallery.', 'bookings_and_flights-content-manager'),
    ),
);

return array(
    'template' => 'page-home.php',
    'fields'   => $fields,
);
