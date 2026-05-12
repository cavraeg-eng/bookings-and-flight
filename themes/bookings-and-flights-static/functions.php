<?php
/**
 * Bookings and Flights Static Theme Functions
 *
 * @package Bookings and Flights_Static
 */

// Header layout: 'logo-left-nav-center' | 'logo-center-nav-left' | 'logo-left-nav-right'
define('HEADER_LAYOUT', 'logo-left-nav-center');

// Footer layout: 'columns-3' | 'minimal' | 'centered'
define('FOOTER_LAYOUT', 'columns-3');

// Theme support
function bookings_and_flights_setup() {
    load_theme_textdomain( 'bookings_and_flights', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => 'Primary Menu',
        'footer'  => 'Footer Menu',
    ) );

}
add_action( 'after_setup_theme', 'bookings_and_flights_setup' );

// Fallback menu when no menu is assigned
function bookings_and_flights_fallback_menu($args = array()) {
    if (is_object($args)) {
        $args = (array) $args;
    }

    $menu_items = array(
        'Home'    => home_url( '/' ),
        'About'   => home_url( '/about/' ),
        'Services' => home_url( '/services/' ),
        'Contact' => home_url( '/contact/' ),
    );

    $menu_class = isset($args['menu_class']) ? $args['menu_class'] : '';
    $container_class = isset($args['container_class']) ? $args['container_class'] : '';

    $li_class = 'header__nav-item';
    $link_class = 'header__nav-link';
    if (false !== strpos($menu_class, 'mobile-nav__list') || false !== strpos($container_class, 'mobile-nav')) {
        $li_class = 'mobile-nav__item';
        $link_class = 'mobile-nav__link';
    }

    echo '<ul class="' . esc_attr($menu_class ? $menu_class : 'header__nav-list') . '">';
    foreach ( $menu_items as $label => $url ) {
        $request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $active       = ( parse_url( $url, PHP_URL_PATH ) === parse_url( $request_uri, PHP_URL_PATH ) ) ? ' class="current-menu-item"' : '';
        $active_class = $active ? ' current-menu-item' : '';
        echo '<li class="' . esc_attr($li_class . $active_class) . '"><a class="' . esc_attr($link_class) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}

// Fallback menu for footer nav when no menu is assigned
function bookings_and_flights_footer_fallback_menu( $args = array() ) {
	if ( is_object( $args ) ) {
		$args = (array) $args;
	}
	$menu_items = array(
		'Home'     => home_url( '/' ),
		'About'    => home_url( '/about/' ),
		'Services' => home_url( '/services/' ),
		'Contact'  => home_url( '/contact/' ),
	);
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'site-footer__links';
	echo '<ul class="' . esc_attr( $menu_class ) . '">';
	foreach ( $menu_items as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

if (!class_exists('WP_Forge_Menu_Walker')) {
    class WP_Forge_Menu_Walker extends Walker_Nav_Menu {
        private $li_class;
        private $link_class;

        public function __construct($li_class, $link_class) {
            $this->li_class = $li_class;
            $this->link_class = $link_class;
        }

        public function start_lvl(&$output, $depth = 0, $args = null) {
            $output .= '<ul class="header__sub-menu">';
        }

        public function end_lvl(&$output, $depth = 0, $args = null) {
            $output .= "</ul>\n";
        }

        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            $classes = empty($item->classes) ? array() : (array) $item->classes;

            if ($depth > 0) {
                $classes[] = $this->li_class . '-child';
                $link_class = $this->link_class . '-child';
            } else {
                $classes[] = $this->li_class;
                $link_class = $this->link_class;
            }

            $class_names = join(' ', array_filter(array_map('sanitize_html_class', $classes)));

            $output .= '<li class="' . esc_attr($class_names) . '">';

            $atts = array();
            $atts['href'] = !empty($item->url) ? $item->url : '';
            $atts['class'] = $link_class;

            if ( $this->has_children && 0 === $depth ) {
                $atts['aria-haspopup'] = 'true';
                $atts['aria-expanded'] = 'false';
            }

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if ('' === $value) {
                    continue;
                }
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }

            $title = apply_filters('the_title', $item->title, $item->ID);
            $item_output = '<a' . $attributes . '>' . esc_html($title) . '</a>';

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }

        public function end_el(&$output, $item, $depth = 0, $args = null) {
            $output .= "</li>\n";
        }
    }
}

// Enqueue CSS based on page template
function bookings_and_flights_enqueue_assets() {
    // 1. Fonts - must load first (font files)
    wp_enqueue_style(
        'bookings_and_flights-fonts',
        get_template_directory_uri() . '/assets/css/fonts.css',
        array(),
        filemtime( get_template_directory() . '/assets/css/fonts.css' )
    );

    // 2. Tokens - CSS variables used by all other styles
    wp_enqueue_style(
        'bookings_and_flights-tokens',
        get_template_directory_uri() . '/assets/css/tokens.css',
        array('bookings_and_flights-fonts'),
        filemtime( get_template_directory() . '/assets/css/tokens.css' )
    );

    // 3. Base - reset and base styles (depends on tokens)
    wp_enqueue_style(
        'bookings_and_flights-base',
        get_template_directory_uri() . '/assets/css/base.css',
        array('bookings_and_flights-tokens'),
        filemtime( get_template_directory() . '/assets/css/base.css' )
    );

    // 4. Shared components, header, mobile navigation, and footer - depend on base styles
    wp_enqueue_style(
        'bookings_and_flights-components',
        get_template_directory_uri() . '/assets/css/components.css',
        array('bookings_and_flights-base'),
        filemtime( get_template_directory() . '/assets/css/components.css' )
    );
    wp_enqueue_style(
        'bookings_and_flights-header',
        get_template_directory_uri() . '/assets/css/header.css',
        array('bookings_and_flights-components'),
        filemtime( get_template_directory() . '/assets/css/header.css' )
    );
    wp_enqueue_style(
        'bookings_and_flights-mobile-nav',
        get_template_directory_uri() . '/assets/css/mobile-nav.css',
        array('bookings_and_flights-header'),
        filemtime( get_template_directory() . '/assets/css/mobile-nav.css' )
    );
    wp_enqueue_style(
        'bookings_and_flights-footer',
        get_template_directory_uri() . '/assets/css/footer.css',
        array('bookings_and_flights-mobile-nav'),
        filemtime( get_template_directory() . '/assets/css/footer.css' )
    );

    // 404 page assets (not template-based, must check before $template early return)
    if ( is_404() ) {
        $css_404 = get_template_directory() . '/assets/css/404.css';
        if ( file_exists( $css_404 ) ) {
            wp_enqueue_style(
                'bookings_and_flights-404',
                get_template_directory_uri() . '/assets/css/404.css',
                array( 'bookings_and_flights-tokens', 'bookings_and_flights-base' ),
                filemtime( $css_404 )
            );
        }
        return;
    }

    // 5. Page-specific styles — auto-discovered from template slug
    // Convention: page-{slug}.php -> assets/css/{slug}.css
    $template = get_page_template_slug();

    if ( ! $template ) {
        return;
    }

    $template_base = basename( $template, '.php' );
    $css_slug      = preg_replace( '/^page-/', '', $template_base );
    $css_path      = get_template_directory() . '/assets/css/' . $css_slug . '.css';

    if ( file_exists( $css_path ) ) {
        wp_enqueue_style(
            'bookings_and_flights-page-style',
            get_template_directory_uri() . '/assets/css/' . $css_slug . '.css',
            array( 'bookings_and_flights-tokens', 'bookings_and_flights-base' ),
            filemtime( $css_path )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'bookings_and_flights_enqueue_assets' );

// Enqueue JavaScript based on page template
function bookings_and_flights_enqueue_scripts() {
    // Header script - ALL pages (mobile menu, scroll handling, header variant)
    wp_enqueue_script(
        'bookings_and_flights-header',
        get_template_directory_uri() . '/assets/js/header.js',
        array(),
        filemtime( get_template_directory() . '/assets/js/header.js' ),
        true
    );

    // Reveal animations - ALL pages
    wp_enqueue_script(
        'bookings_and_flights-reveal',
        get_template_directory_uri() . '/assets/js/reveal.js',
        array(),
        filemtime( get_template_directory() . '/assets/js/reveal.js' ),
        true
    );

    // Dark mode toggle - ALL pages
    wp_enqueue_script(
        'bookings_and_flights-dark-mode',
        get_template_directory_uri() . '/assets/js/dark-mode.js',
        array(),
        filemtime( get_template_directory() . '/assets/js/dark-mode.js' ),
        true
    );

    // 404 page script (not template-based, must check before $template early return)
    if ( is_404() ) {
        $js_404 = get_template_directory() . '/assets/js/404.js';
        if ( file_exists( $js_404 ) ) {
            wp_enqueue_script(
                'bookings_and_flights-404',
                get_template_directory_uri() . '/assets/js/404.js',
                array(),
                filemtime( $js_404 ),
                true
            );
        }
        return;
    }

    // Page-specific scripts — auto-discovered from template slug
    // Convention: page-{slug}.php -> assets/js/{slug}.js
    $template = get_page_template_slug();

    if ( ! $template ) {
        return;
    }

    $template_base = basename( $template, '.php' );
    $js_slug       = preg_replace( '/^page-/', '', $template_base );
    $js_path       = get_template_directory() . '/assets/js/' . $js_slug . '.js';

    if ( file_exists( $js_path ) ) {
        wp_enqueue_script(
            'bookings_and_flights-page-script',
            get_template_directory_uri() . '/assets/js/' . $js_slug . '.js',
            array(),
            filemtime( $js_path ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'bookings_and_flights_enqueue_scripts' );


// ── Dark Mode — FOUC Prevention ──────────────────────────────────────────────
// Inline script reads localStorage before any CSS renders to prevent flash.
// This is the one justified exception to "no inline scripts".
function bookings_and_flights_dark_mode_head() {
    ?>
    <script>
    (function(){var t=localStorage.getItem('bookings_and_flights_theme');if(t){document.documentElement.classList.add(t==='dark'?'theme-dark':'theme-light');}})();
    </script>
    <?php
}
add_action( 'wp_head', 'bookings_and_flights_dark_mode_head', 1 );

// ── Security Hardening ───────────────────────────────────────────────────────
// Disable comments and pingbacks (static/brochure sites don't use them)
add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );

// Remove WordPress version from <head> (prevents version fingerprinting)
remove_action( 'wp_head', 'wp_generator' );

// Disable XML-RPC (brute-force attack vector, unused on static sites)
add_filter( 'xmlrpc_enabled', '__return_false' );

// ── SEOPress Compatibility ───────────────────────────────────────────────────
// Suppress unused breadcrumb CSS (no-op if SEOPress is absent)
add_filter( 'seopress_pro_breadcrumbs_css', '__return_empty_string' );
// Disable WP core XML sitemaps — SEOPress provides better ones
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// ── Performance: Strip HTML Comments ─────────────────────────────────────────
// Remove developer comments from frontend output to reduce page weight.
// Preserves IE conditionals, <!--more-->, and <!--nextpage-->.
function bookings_and_flights_strip_html_comments( $buffer ) {
    return preg_replace( '/<!--(?!\[if\s|!?\[endif|more|nextpage).*?-->/s', '', $buffer );
}
function bookings_and_flights_start_html_stripping() {
    if ( ! is_admin() ) {
        ob_start( 'bookings_and_flights_strip_html_comments' );
    }
}
add_action( 'template_redirect', 'bookings_and_flights_start_html_stripping' );


/**
 * Fallback for bookings_and_flights_field() if plugin is inactive
 * This ensures the site doesn't break if the plugin is deactivated
 */
if (!function_exists('bookings_and_flights_field')) {
    function bookings_and_flights_field($key, $default = '') {
        return $default;
    }
}

/**
 * Fallback for bookings_and_flights_image_url() if plugin is inactive
 */
if (!function_exists('bookings_and_flights_image_url')) {
    function bookings_and_flights_image_url($image_id = 0, $size = 'full') {
        return '';
    }
}

/**
 * Fallback for bookings_and_flights_image() if plugin is inactive
 */
if (!function_exists('bookings_and_flights_image')) {
    function bookings_and_flights_image($image_id = 0, $size = 'full', $attrs = array()) {
        return '';
    }
}

if (!function_exists('bookings_and_flights_gallery')) {
    function bookings_and_flights_gallery($key) { return array(); }
}
if (!function_exists('bookings_and_flights_repeater')) {
    function bookings_and_flights_repeater($key) { return array(); }
}
if (!function_exists('bookings_and_flights_link')) {
    function bookings_and_flights_link($key) { return array('url' => '', 'label' => ''); }
}
if (!function_exists('bookings_and_flights_oembed')) {
    function bookings_and_flights_oembed($key, $args = array()) { return ''; }
}
if (!function_exists('bookings_and_flights_the_field')) {
    function bookings_and_flights_the_field($key, $default = '') { echo esc_html($default); }
}
if (!function_exists('bookings_and_flights_the_content')) {
    function bookings_and_flights_the_content($key, $default = '') { echo wp_kses_post($default); }
}
if (!function_exists('bookings_and_flights_the_attr')) {
    function bookings_and_flights_the_attr($key, $default = '') { echo esc_attr($default); }
}
if (!function_exists('bookings_and_flights_the_url')) {
    function bookings_and_flights_the_url($key, $default = '') { echo esc_url($default); }
}
if (!function_exists('bookings_and_flights_option')) {
    function bookings_and_flights_option($key, $default = '') { return $default; }
}
if (!function_exists('bookings_and_flights_the_option')) {
    function bookings_and_flights_the_option($key, $default = '') { echo esc_html($default); }
}
if (!function_exists('bookings_and_flights_the_option_url')) {
    function bookings_and_flights_the_option_url($key, $default = '') { echo esc_url($default); }
}
