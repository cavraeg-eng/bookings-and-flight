<?php
/**
 * Fallback template. Should rarely render because template_redirect
 * 302s to the Next.js app first.
 *
 * @package Bookings_And_Flights\Headless
 */
defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="noindex" />
	<?php wp_head(); ?>
</head>
<body style="font: 16px system-ui, sans-serif; background:#0b1220; color:#e5e7eb; display:flex; min-height:100vh; align-items:center; justify-content:center; padding:2rem;">
	<div style="max-width: 520px; text-align:center;">
		<h1 style="margin:0 0 0.5rem;">Bookings and Flights</h1>
		<p>Headless mode is on. Redirecting to the Next.js app…</p>
		<p><a href="<?php echo esc_url( BAF_FRONTEND_URL ); ?>" style="color:#f59e0b;">Continue to <?php echo esc_html( BAF_FRONTEND_URL ); ?></a></p>
	</div>
	<?php wp_footer(); ?>
	<script>window.location.replace('<?php echo esc_js( BAF_FRONTEND_URL ); ?>');</script>
</body>
</html>
