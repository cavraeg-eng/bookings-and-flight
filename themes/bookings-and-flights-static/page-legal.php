<?php
/**
 * Template Name: Legal Page
 * Description: Shared template for Privacy Policy and Terms & Conditions pages.
 *              Uses the page slug to determine which content block to display.
 *
 * Learned from: Both DJFuse and GlenSea builds needed legal pages. Rather than
 * building from scratch each time, this reusable template handles both pages
 * with a shared layout (compact hero, sticky TOC sidebar, prose styling).
 *
 * @package Bookings and Flights_Static
 */

get_header();

$page_slug = get_post_field('post_name', get_post());
$is_privacy = (strpos($page_slug, 'privacy') !== false);
?>

<main class="legal" id="main-content">
    <!-- Compact Hero -->
    <section class="legal-hero" data-grain>
        <div class="legal-hero__content">
            <p class="legal-hero__overline">Legal</p>
            <h1 class="legal-hero__title">
                <?php echo $is_privacy
                    ? esc_html__('Privacy Policy', 'bookings_and_flights')
                    : esc_html__('Terms & Conditions', 'bookings_and_flights'); ?>
            </h1>
            <p class="legal-hero__date">
                <?php printf(
                    esc_html__('Last updated: %s', 'bookings_and_flights'),
                    wp_date('F j, Y', get_the_modified_date('U'))
                ); ?>
            </p>
        </div>
    </section>

    <!-- Content with Sticky TOC -->
    <section class="legal-body">
        <div class="legal-body__container">
            <!-- Table of Contents Sidebar -->
            <aside class="legal-toc" aria-label="<?php esc_attr_e('Table of Contents', 'bookings_and_flights'); ?>">
                <h2 class="legal-toc__title"><?php esc_html_e('Contents', 'bookings_and_flights'); ?></h2>
                <nav class="legal-toc__nav" id="legal-toc-nav">
                    <!-- Populated by JavaScript from h2 headings -->
                </nav>
            </aside>

            <!-- Prose Content -->
            <article class="legal-prose" id="legal-prose">
                <?php if ($is_privacy) : ?>
                    <!-- ============================================ -->
                    <!-- PRIVACY POLICY CONTENT                       -->
                    <!-- Replace this block with your actual content   -->
                    <!-- ============================================ -->

                    <h2 id="introduction">Introduction</h2>
                    <p>Welcome to Bookings and Flights. This Privacy Policy explains how we collect, use, and protect your personal information when you visit our website.</p>

                    <h2 id="information-we-collect">Information We Collect</h2>
                    <p>We collect information you voluntarily provide through our contact form, including:</p>
                    <ul>
                        <li>Name</li>
                        <li>Email address</li>
                        <li>Phone number (if provided)</li>
                        <li>Message content</li>
                    </ul>

                    <h2 id="how-we-use-information">How We Use Your Information</h2>
                    <p>We use the information collected solely to:</p>
                    <ul>
                        <li>Respond to your inquiries</li>
                        <li>Provide the services you requested</li>
                        <li>Communicate about your project or booking</li>
                    </ul>

                    <h2 id="cookies">Cookies & Analytics</h2>
                    <p>This website may use essential cookies required for normal operation. We do not use tracking cookies or third-party analytics unless explicitly stated.</p>

                    <h2 id="third-party-services">Third-Party Services</h2>
                    <p>We may use the following third-party services:</p>
                    <ul>
                        <li><strong>Form processing</strong> — to handle contact form submissions</li>
                        <li><strong>Hosting provider</strong> — to serve website content</li>
                    </ul>

                    <h2 id="data-retention">Data Retention</h2>
                    <p>Form submissions are retained for the duration necessary to complete your inquiry. You may request deletion of your data at any time by contacting us.</p>

                    <h2 id="your-rights">Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access the personal data we hold about you</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your data</li>
                        <li>Withdraw consent at any time</li>
                    </ul>

                    <h2 id="contact-us">Contact Us</h2>
                    <p>If you have questions about this Privacy Policy, please <a href="<?php echo esc_url(home_url('/contact/')); ?>">contact us</a>.</p>

                <?php else : ?>
                    <!-- ============================================ -->
                    <!-- TERMS & CONDITIONS CONTENT                   -->
                    <!-- Replace this block with your actual content   -->
                    <!-- ============================================ -->

                    <h2 id="acceptance">Acceptance of Terms</h2>
                    <p>By accessing and using this website, you accept and agree to be bound by the terms and provisions of this agreement.</p>

                    <h2 id="website-use">Use of Website</h2>
                    <p>This website is provided for informational purposes only. The content is intended to provide general information about Bookings and Flights and the services offered.</p>

                    <h2 id="intellectual-property">Intellectual Property</h2>
                    <p>All content on this website, including text, images, logos, and design elements, is the property of Bookings and Flights and is protected by applicable copyright and trademark laws.</p>

                    <h2 id="disclaimer">Disclaimer</h2>
                    <p>The information on this website is provided "as is" without warranties of any kind. We make no representations about the accuracy or completeness of the content.</p>

                    <h2 id="limitation-of-liability">Limitation of Liability</h2>
                    <p>Bookings and Flights shall not be liable for any indirect, incidental, or consequential damages arising from your use of this website.</p>

                    <h2 id="external-links">External Links</h2>
                    <p>This website may contain links to external sites. We are not responsible for the content or privacy practices of those sites.</p>

                    <h2 id="changes-to-terms">Changes to Terms</h2>
                    <p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting to this page.</p>

                    <h2 id="governing-law">Governing Law</h2>
                    <p>These terms shall be governed by and construed in accordance with the laws of the applicable jurisdiction.</p>

                    <h2 id="contact-us">Contact Us</h2>
                    <p>If you have questions about these Terms & Conditions, please <a href="<?php echo esc_url(home_url('/contact/')); ?>">contact us</a>.</p>

                <?php endif; ?>
            </article>
        </div>
    </section>
</main>

<?php get_footer(); ?>
