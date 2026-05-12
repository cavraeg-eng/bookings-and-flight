<?php
/**
 * Template Name: Home
 *
 * @package Bookings and Flights_Static
 */

get_header();
?>

<main id="main-content">

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero">
    <div class="hero__background">
        <?php
        $hero_image_id = bookings_and_flights_field( 'hero_image_id', 0 );
        if ( $hero_image_id ) :
            echo bookings_and_flights_image( $hero_image_id, 'full', array(
                'class'   => 'hero__image',
                'loading' => 'eager',
            ) );
        else : ?>
            <div class="hero__gradient-bg"></div>
        <?php endif; ?>
        <div class="hero__overlay"></div>
    </div>
    <div class="hero__content">
        <span class="hero__tagline reveal"><?php bookings_and_flights_the_field( 'hero_tagline', 'Welcome to ' . get_bloginfo( 'name' ) ); ?></span>
        <h1 class="hero__title reveal" data-reveal-delay="100ms"><?php bookings_and_flights_the_field( 'hero_title', 'Your Compelling Headline Here' ); ?></h1>
        <p class="hero__description reveal" data-reveal-delay="200ms"><?php bookings_and_flights_the_field( 'hero_description', 'A brief, compelling description that captures your value proposition and encourages visitors to explore further.' ); ?></p>
        <div class="hero__actions reveal" data-reveal-delay="300ms">
            <a href="<?php bookings_and_flights_the_url( 'hero_cta_url', '/contact/' ); ?>" class="btn btn--primary btn--large">
                <span><?php bookings_and_flights_the_field( 'hero_cta_text', 'Get Started' ); ?></span>
                <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="<?php bookings_and_flights_the_url( 'hero_cta2_url', '/about/' ); ?>" class="btn btn--outline">
                <span><?php bookings_and_flights_the_field( 'hero_cta2_text', 'Learn More' ); ?></span>
            </a>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURES SECTION
     ============================================================ -->
<section class="features">
    <div class="features__container">
        <header class="features__header">
            <span class="features__tagline reveal"><?php bookings_and_flights_the_field( 'features_tagline', 'Why Choose Us' ); ?></span>
            <h2 class="features__title reveal" data-reveal-delay="100ms"><?php bookings_and_flights_the_field( 'features_title', 'What Makes Us Different' ); ?></h2>
            <p class="features__description reveal" data-reveal-delay="200ms"><?php bookings_and_flights_the_field( 'features_description', 'Discover the key benefits that set us apart from the competition.' ); ?></p>
        </header>

        <div class="features__grid">
            <?php
            $features = bookings_and_flights_repeater( 'features_items' );
            if ( ! empty( $features ) ) :
                $delay = 100;
                foreach ( $features as $feature ) :
            ?>
                <article class="feature-card reveal" data-reveal-delay="<?php echo esc_attr( $delay . 'ms' ); ?>">
                    <?php if ( ! empty( $feature['icon'] ) ) : ?>
                        <div class="feature-card__icon">
                            <span class="feature-card__icon-name"><?php echo esc_html( $feature['icon'] ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ( ! empty( $feature['title'] ) ) : ?>
                        <h3 class="feature-card__title"><?php echo esc_html( $feature['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $feature['description'] ) ) : ?>
                        <p class="feature-card__description"><?php echo esc_html( $feature['description'] ); ?></p>
                    <?php endif; ?>
                </article>
            <?php
                    $delay += 100;
                endforeach;
            else :
                // Fallback: show placeholder cards when no repeater data exists
                $placeholders = array(
                    array( 'title' => 'Feature One',   'description' => 'A brief description of this feature and the value it provides.' ),
                    array( 'title' => 'Feature Two',   'description' => 'A brief description of this feature and the value it provides.' ),
                    array( 'title' => 'Feature Three', 'description' => 'A brief description of this feature and the value it provides.' ),
                );
                $delay = 100;
                foreach ( $placeholders as $ph ) :
            ?>
                <article class="feature-card reveal" data-reveal-delay="<?php echo esc_attr( $delay . 'ms' ); ?>">
                    <div class="feature-card__icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <h3 class="feature-card__title"><?php echo esc_html( $ph['title'] ); ?></h3>
                    <p class="feature-card__description"><?php echo esc_html( $ph['description'] ); ?></p>
                </article>
            <?php
                    $delay += 100;
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT SECTION
     ============================================================ -->
<section class="about">
    <div class="about__container">
        <div class="about__content">
            <span class="about__tagline reveal"><?php bookings_and_flights_the_field( 'about_tagline', 'Our Story' ); ?></span>
            <h2 class="about__title reveal" data-reveal-delay="100ms"><?php bookings_and_flights_the_field( 'about_title', 'About ' . get_bloginfo( 'name' ) ); ?></h2>
            <div class="about__description reveal" data-reveal-delay="200ms"><?php bookings_and_flights_the_content( 'about_content', '<p>Tell your story here. What makes your business unique?</p>' ); ?></div>
            <a href="<?php bookings_and_flights_the_url( 'about_cta_url', '/about/' ); ?>" class="btn btn--primary reveal" data-reveal-delay="300ms">
                <span><?php bookings_and_flights_the_field( 'about_cta_text', 'Learn More About Us' ); ?></span>
                <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="about__image reveal" data-reveal-delay="200ms">
            <?php
            $about_image_id = bookings_and_flights_field( 'about_image_id', 0 );
            if ( $about_image_id ) :
                echo bookings_and_flights_image( $about_image_id, 'large', array(
                    'class'   => 'about__img',
                    'loading' => 'lazy',
                ) );
            else : ?>
                <div class="about__gradient-bg"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS SECTION
     ============================================================ -->
<section class="testimonials">
    <div class="testimonials__container">
        <header class="testimonials__header">
            <span class="testimonials__tagline reveal"><?php bookings_and_flights_the_field( 'testimonials_tagline', 'What Our Clients Say' ); ?></span>
            <h2 class="testimonials__title reveal" data-reveal-delay="100ms"><?php bookings_and_flights_the_field( 'testimonials_title', 'Trusted by Many' ); ?></h2>
        </header>

        <div class="testimonials__grid">
            <?php
            $testimonials = bookings_and_flights_repeater( 'testimonials_items' );
            if ( ! empty( $testimonials ) ) :
                $delay = 100;
                foreach ( $testimonials as $testimonial ) :
            ?>
                <blockquote class="testimonial-card reveal" data-reveal-delay="<?php echo esc_attr( $delay . 'ms' ); ?>">
                    <div class="testimonial-card__stars" aria-label="5 out of 5 stars">
                        ★★★★★
                    </div>
                    <?php if ( ! empty( $testimonial['quote'] ) ) : ?>
                        <p class="testimonial-card__quote">"<?php echo esc_html( $testimonial['quote'] ); ?>"</p>
                    <?php endif; ?>
                    <footer class="testimonial-card__footer">
                        <?php if ( ! empty( $testimonial['author'] ) ) : ?>
                            <cite class="testimonial-card__author"><?php echo esc_html( $testimonial['author'] ); ?></cite>
                        <?php endif; ?>
                        <?php if ( ! empty( $testimonial['role'] ) ) : ?>
                            <span class="testimonial-card__role"><?php echo esc_html( $testimonial['role'] ); ?></span>
                        <?php endif; ?>
                    </footer>
                </blockquote>
            <?php
                    $delay += 100;
                endforeach;
            else :
                // Fallback: show placeholder testimonials when no repeater data exists
                $placeholders = array(
                    array( 'quote' => 'Exceptional service and attention to detail. Highly recommended!', 'author' => 'Jane Smith', 'role' => 'Business Owner' ),
                    array( 'quote' => 'They transformed our vision into reality. Outstanding work.',     'author' => 'John Doe',   'role' => 'Marketing Director' ),
                    array( 'quote' => 'Professional, creative, and a pleasure to work with.',            'author' => 'Sarah Lee',  'role' => 'Entrepreneur' ),
                );
                $delay = 100;
                foreach ( $placeholders as $ph ) :
            ?>
                <blockquote class="testimonial-card reveal" data-reveal-delay="<?php echo esc_attr( $delay . 'ms' ); ?>">
                    <div class="testimonial-card__stars" aria-label="5 out of 5 stars">
                        ★★★★★
                    </div>
                    <p class="testimonial-card__quote">"<?php echo esc_html( $ph['quote'] ); ?>"</p>
                    <footer class="testimonial-card__footer">
                        <cite class="testimonial-card__author"><?php echo esc_html( $ph['author'] ); ?></cite>
                        <span class="testimonial-card__role"><?php echo esc_html( $ph['role'] ); ?></span>
                    </footer>
                </blockquote>
            <?php
                    $delay += 100;
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA SECTION
     ============================================================ -->
<section class="cta">
    <div class="cta__container">
        <div class="cta__content">
            <h2 class="cta__title reveal"><?php bookings_and_flights_the_field( 'cta_title', 'Ready to Get Started?' ); ?></h2>
            <p class="cta__description reveal" data-reveal-delay="100ms"><?php bookings_and_flights_the_field( 'cta_description', 'Contact us today to learn how we can help you achieve your goals.' ); ?></p>
            <a href="<?php bookings_and_flights_the_url( 'cta_button_url', '/contact/' ); ?>" class="btn btn--primary btn--large reveal" data-reveal-delay="200ms">
                <span><?php bookings_and_flights_the_field( 'cta_button_text', 'Contact Us' ); ?></span>
                <svg class="btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

</main>

<?php
get_footer();
