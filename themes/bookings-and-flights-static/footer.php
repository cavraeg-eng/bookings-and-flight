</div><!-- /.site-content -->

<?php
/**
 * Footer Template
 *
 * Footer content (description, social URLs, contact info) is pulled from the
 * global options page (Settings → Bookings and Flights) via bookings_and_flights_option().
 * If the content manager plugin is deactivated, fallback functions in functions.php
 * return empty strings gracefully.
 *
 * Supports 3 layouts: columns-3 (default), minimal, centered.
 * Layout is set via the FOOTER_LAYOUT constant in functions.php.
 */

$allowed_footer_layouts = array( 'columns-3', 'minimal', 'centered' );
$footer_layout = defined( 'FOOTER_LAYOUT' ) ? FOOTER_LAYOUT : 'columns-3';
if ( ! in_array( $footer_layout, $allowed_footer_layouts, true ) ) {
	$footer_layout = 'columns-3';
}

$baf_footer_clean_option = static function ( $key, $fallback = '' ) {
	$value = bookings_and_flights_option( $key, '' );
	$value = is_scalar( $value ) ? trim( (string) $value ) : '';

	if ( '' === $value || preg_match( '/^\{\{[^}]+\}\}$/', $value ) ) {
		return $fallback;
	}

	return $value;
};

$footer_description = $baf_footer_clean_option(
	'footer_description',
	__( 'Travel discovery, planning ideas, and live search paths in one WordPress-native experience.', 'bookings_and_flights' )
);
$contact_address_1  = $baf_footer_clean_option( 'contact_address_1', __( 'Plan trips online', 'bookings_and_flights' ) );
$contact_address_2  = $baf_footer_clean_option( 'contact_address_2', __( 'Bookings complete with trusted travel partners', 'bookings_and_flights' ) );
$contact_phone      = $baf_footer_clean_option( 'contact_phone' );
$contact_email      = $baf_footer_clean_option( 'contact_email' );
$contact_phone_href = preg_replace( '/[^0-9+]/', '', $contact_phone );
$footer_legal_links = array(
	'terms'       => array(
		'label' => __( 'Terms', 'bookings_and_flights' ),
		'url'   => home_url( '/terms-and-conditions/' ),
	),
	'privacy'     => array(
		'label' => __( 'Privacy', 'bookings_and_flights' ),
		'url'   => home_url( '/privacy-policy/' ),
	),
	'support'     => array(
		'label' => __( 'Support', 'bookings_and_flights' ),
		'url'   => home_url( '/contact/' ),
	),
	'destinations' => array(
		'label' => __( 'Destination index', 'bookings_and_flights' ),
		'url'   => home_url( '/#explore' ),
	),
);

$social_links = array(
	'social_facebook'  => array( 'label' => 'Facebook',  'icon' => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>' ),
	'social_instagram' => array( 'label' => 'Instagram', 'icon' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>' ),
	'social_x'         => array( 'label' => 'X',         'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>' ),
	'social_tiktok'    => array( 'label' => 'TikTok',    'icon' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>' ),
	'social_youtube'   => array( 'label' => 'YouTube',   'icon' => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>' ),
	'social_linkedin'  => array( 'label' => 'LinkedIn',  'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>' ),
);
?>

<!-- Site Footer -->
<footer class="site-footer site-footer--layout-<?php echo esc_attr( $footer_layout ); ?>">
	<!-- Hello Travelpayouts: bookingsandflights.com project ownership verification. -->
	<div class="site-footer__container">

		<?php if ( 'columns-3' === $footer_layout ) : ?>

		<!-- Main Footer Content -->
		<div class="site-footer__main">
			<!-- About Column -->
			<div class="site-footer__column site-footer__about">
				<h3 class="site-footer__title">Bookings and Flights</h3>
				<p class="site-footer__text">
					<?php echo esc_html( $footer_description ); ?>
				</p>
				<div class="site-footer__social">
					<?php
					foreach ( $social_links as $key => $social ) :
						$url = bookings_and_flights_option( $key );
						if ( ! empty( $url ) ) :
					?>
					<a href="<?php echo esc_url( $url ); ?>" class="site-footer__social-link" aria-label="<?php echo esc_attr( $social['label'] ); ?>" target="_blank" rel="noopener noreferrer">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
							<?php echo $social['icon']; ?>
						</svg>
					</a>
					<?php
						endif;
					endforeach;
					?>
				</div>
			</div>

			<!-- Quick Links Column -->
			<div class="site-footer__column">
				<h4 class="site-footer__heading"><?php esc_html_e( 'Quick Links', 'bookings_and_flights' ); ?></h4>
				<nav aria-label="<?php esc_attr_e( 'Footer navigation', 'bookings_and_flights' ); ?>">
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer',
						'menu_class'     => 'site-footer__links',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => 'bookings_and_flights_footer_fallback_menu',
					) );
					?>
				</nav>
			</div>

			<!-- Contact Column -->
			<div class="site-footer__column">
				<h4 class="site-footer__heading"><?php esc_html_e( 'Contact', 'bookings_and_flights' ); ?></h4>
				<address class="site-footer__contact">
					<p>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
							<circle cx="12" cy="10" r="3"/>
						</svg>
						<?php echo esc_html( $contact_address_1 ); ?><br>
						<?php echo esc_html( $contact_address_2 ); ?>
					</p>
					<?php if ( '' !== $contact_phone && '' !== $contact_phone_href ) : ?>
					<p>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
						</svg>
						<a href="tel:<?php echo esc_attr( $contact_phone_href ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
					</p>
					<?php endif; ?>
					<?php if ( '' !== $contact_email && is_email( $contact_email ) ) : ?>
					<p>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
							<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
							<polyline points="22,6 12,13 2,6"/>
						</svg>
						<a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
					</p>
					<?php endif; ?>
				</address>
			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="site-footer__bottom">
			<p class="site-footer__copyright">
				&copy; <?php echo wp_date( 'Y' ); ?> Bookings and Flights. <?php esc_html_e( 'All rights reserved.', 'bookings_and_flights' ); ?>
			</p>
		</div>

		<?php elseif ( 'minimal' === $footer_layout ) : ?>

		<div class="site-footer__minimal">
			<p class="site-footer__copyright">
				&copy; <?php echo wp_date( 'Y' ); ?> Bookings and Flights. <?php esc_html_e( 'All rights reserved.', 'bookings_and_flights' ); ?>
			</p>
			<div class="site-footer__social">
				<?php
				foreach ( $social_links as $key => $social ) :
					$url = bookings_and_flights_option( $key );
					if ( ! empty( $url ) ) :
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="site-footer__social-link" aria-label="<?php echo esc_attr( $social['label'] ); ?>" target="_blank" rel="noopener noreferrer">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<?php echo $social['icon']; ?>
					</svg>
				</a>
				<?php
					endif;
				endforeach;
				?>
			</div>
		</div>

		<?php elseif ( 'centered' === $footer_layout ) : ?>

		<div class="site-footer__centered">
			<h3 class="site-footer__title">Bookings and Flights</h3>

			<nav aria-label="<?php esc_attr_e( 'Footer navigation', 'bookings_and_flights' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'menu_class'     => 'site-footer__links site-footer__links--inline',
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => 'bookings_and_flights_footer_fallback_menu',
				) );
				?>
			</nav>

			<div class="site-footer__social">
				<?php
				foreach ( $social_links as $key => $social ) :
					$url = bookings_and_flights_option( $key );
					if ( ! empty( $url ) ) :
				?>
				<a href="<?php echo esc_url( $url ); ?>" class="site-footer__social-link" aria-label="<?php echo esc_attr( $social['label'] ); ?>" target="_blank" rel="noopener noreferrer">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<?php echo $social['icon']; ?>
					</svg>
				</a>
				<?php
					endif;
				endforeach;
				?>
			</div>

			<p class="site-footer__copyright">
				&copy; <?php echo wp_date( 'Y' ); ?> Bookings and Flights. <?php esc_html_e( 'All rights reserved.', 'bookings_and_flights' ); ?>
			</p>
		</div>

		<?php endif; ?>

		<div class="site-footer__compliance">
			<p class="site-footer__disclosure">
				<?php esc_html_e( 'Affiliate disclosure: Bookings and Flights may earn commissions from sponsored searches or travel links. Live prices, booking, payment, changes, and reservation support are handled by the booking site.', 'bookings_and_flights' ); ?>
			</p>
			<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Legal, support, and destination links', 'bookings_and_flights' ); ?>">
				<?php foreach ( $footer_legal_links as $footer_link ) : ?>
					<a href="<?php echo esc_url( $footer_link['url'] ); ?>"><?php echo esc_html( $footer_link['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
