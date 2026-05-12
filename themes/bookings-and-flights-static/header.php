<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Preload Critical Fonts -->
    <?php if (file_exists(get_template_directory() . '/assets/fonts/display-font-400.woff2')) : ?>
        <link rel="preload" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/fonts/display-font-400.woff2" as="font" type="font/woff2" crossorigin>
    <?php endif; ?>
    <?php if (file_exists(get_template_directory() . '/assets/fonts/body-font-400.woff2')) : ?>
        <link rel="preload" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/fonts/body-font-400.woff2" as="font" type="font/woff2" crossorigin>
    <?php endif; ?>

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip Link for Accessibility -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<?php
$allowed_header_layouts = array(
    'logo-left-nav-center',
    'logo-center-nav-left',
    'logo-left-nav-right',
);
$configured_header_layout = defined( 'HEADER_LAYOUT' ) ? HEADER_LAYOUT : 'logo-left-nav-center';
$header_layout = in_array( $configured_header_layout, $allowed_header_layouts, true ) ? $configured_header_layout : 'logo-left-nav-center';
$show_desktop_cta = 'logo-left-nav-right' !== $header_layout;
$is_nav_left_layout = 'logo-center-nav-left' === $header_layout;
?>

<!-- Header -->
<header class="header header--layout-<?php echo esc_attr( $header_layout ); ?>" role="banner" id="site-header">
    <div class="header__container">
        <?php if ( $is_nav_left_layout ) : ?>
            <!-- Navigation -->
            <nav class="header__nav" role="navigation" aria-label="Main navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'header__nav-list',
                    'fallback_cb'    => 'bookings_and_flights_fallback_menu',
                    'walker'         => new WP_Forge_Menu_Walker('header__nav-item', 'header__nav-link'),
                ));
                ?>
            </nav>
        <?php endif; ?>

        <!-- Logo - Light variant (for dark backgrounds) -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo-light" aria-label="Bookings and Flights Home">
            <span class="header__logo-text">Bookings and Flights</span>
        </a>

        <!-- Logo - Dark variant (for light backgrounds) -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__logo-dark" aria-label="Bookings and Flights Home">
            <span class="header__logo-text">Bookings and Flights</span>
        </a>

        <?php if ( ! $is_nav_left_layout ) : ?>
            <!-- Navigation -->
            <nav class="header__nav" role="navigation" aria-label="Main navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'header__nav-list',
                    'fallback_cb'    => 'bookings_and_flights_fallback_menu',
                    'walker'         => new WP_Forge_Menu_Walker('header__nav-item', 'header__nav-link'),
                ));
                ?>
            </nav>
        <?php endif; ?>

        <!-- Header Actions (CTA + Theme Toggle) -->
        <div class="header__actions">
            <?php if ( $show_desktop_cta ) : ?>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="header__cta btn btn--primary">
                    <span>Get Started</span>
                    <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            <?php endif; ?>

            <!-- Theme Toggle (dark mode) -->
            <button class="header__theme-toggle" data-theme-toggle
                    aria-label="Toggle dark mode" type="button">
                <svg class="header__theme-icon header__theme-icon--sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="header__theme-icon header__theme-icon--moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
        </div>

        <!-- Light menu toggle (for dark backgrounds) -->
        <button class="header__menu-toggle-light" aria-label="Toggle menu" aria-expanded="false" data-menu-toggle="light">
            <span class="header__menu-bar"></span>
            <span class="header__menu-bar"></span>
            <span class="header__menu-bar"></span>
        </button>

        <!-- Dark menu toggle (for light backgrounds) -->
        <button class="header__menu-toggle-dark" aria-label="Toggle menu" aria-expanded="false" data-menu-toggle="dark">
            <span class="header__menu-bar"></span>
            <span class="header__menu-bar"></span>
            <span class="header__menu-bar"></span>
        </button>
    </div>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav" aria-hidden="true" data-mobile-nav>
    <nav class="mobile-nav__content">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'mobile-nav__list',
            'fallback_cb'    => 'bookings_and_flights_fallback_menu',
            'walker'         => new WP_Forge_Menu_Walker('mobile-nav__item', 'mobile-nav__link'),
        ));
        ?>
        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mobile-nav__cta btn btn--primary btn--large">Get Started</a>
    </nav>
</div>

<!-- Main content wrapper -->
<div class="site-content">
