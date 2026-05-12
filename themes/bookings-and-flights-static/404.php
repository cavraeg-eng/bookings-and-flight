<?php
get_header();
?>
<main class="container" id="main-content">
    <h1><?php esc_html_e('Page Not Found', 'bookings_and_flights'); ?></h1>
    <p><?php esc_html_e('The page you are looking for could not be found.', 'bookings_and_flights'); ?></p>
    <p><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Return to homepage', 'bookings_and_flights'); ?></a></p>
</main>
<?php
get_footer();
