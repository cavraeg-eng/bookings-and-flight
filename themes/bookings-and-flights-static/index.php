<?php
/**
 * Main template file
 *
 * @package Bookings and Flights_Static
 */

get_header();
?>
<main class="container" id="main-content">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>

        <?php the_posts_navigation(); ?>
    <?php else : ?>
        <h1><?php esc_html_e('Nothing Found', 'bookings_and_flights'); ?></h1>
        <p><?php esc_html_e('It looks like nothing was found at this location.', 'bookings_and_flights'); ?></p>
    <?php endif; ?>
</main>
<?php
get_footer();
