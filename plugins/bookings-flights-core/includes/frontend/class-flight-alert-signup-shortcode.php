<?php
/**
 * Frontend flight alert intent signup shortcode.
 *
 * @package BAF\Core
 */

namespace BAF\Core\Frontend;

use BAF\Core\Post_Types\Post_Type_Registrar;

defined( 'ABSPATH' ) || exit;

final class Flight_Alert_Signup_Shortcode {

	public static function render( array|string $atts = array() ): string {
		wp_enqueue_style( Frontend_Manager::ASSET_HANDLE );

		$attributes = shortcode_atts(
			array(
				'origin'      => '',
				'destination' => '',
				'depart_date' => '',
				'return_date' => '',
				'travelers'   => '1',
				'cabin'       => 'economy',
				'frequency'   => 'weekly',
				'surface'     => 'flights',
				'route_id'    => '0',
				'redirect'    => '',
				'source_url'  => '',
				'context'     => 'flights',
			),
			is_array( $atts ) ? $atts : array(),
			'baf_flight_alert_signup'
		);

		$origin      = Flight_Alert_Intent_Handler::normalize_iata( (string) $attributes['origin'] );
		$destination = Flight_Alert_Intent_Handler::normalize_iata( (string) $attributes['destination'] );
		$depart_date = Flight_Alert_Intent_Handler::normalize_date( (string) $attributes['depart_date'] );
		$return_date = Flight_Alert_Intent_Handler::normalize_date( (string) $attributes['return_date'] );
		$travelers   = min( 9, max( 1, absint( $attributes['travelers'] ) ) );
		$cabin       = Flight_Alert_Intent_Handler::allowed_value( (string) $attributes['cabin'], array( 'economy', 'premium_economy', 'business', 'first' ), 'economy' );
		$frequency   = Flight_Alert_Intent_Handler::allowed_value( (string) $attributes['frequency'], array( 'daily', 'weekly', 'monthly' ), 'weekly' );
		$surface     = sanitize_key( (string) $attributes['surface'] );
		$route_id    = absint( $attributes['route_id'] );
		$context     = sanitize_html_class( (string) $attributes['context'] );
		$current_url = self::current_url();
		$redirect    = '' !== (string) $attributes['redirect'] ? esc_url_raw( (string) $attributes['redirect'] ) : $current_url;
		$source_url  = '' !== (string) $attributes['source_url'] ? esc_url_raw( (string) $attributes['source_url'] ) : $current_url;
		$email       = '';

		if ( is_user_logged_in() ) {
			$user  = wp_get_current_user();
			$email = sanitize_email( (string) $user->user_email );
		}

		$title_id = wp_unique_id( 'baf-flight-alert-title-' );
		$message  = self::status_message();

		ob_start();
		?>
		<section class="<?php echo esc_attr( trim( 'baf-flight-alert baf-flight-alert--' . $context ) ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<div class="baf-flight-alert__content">
				<p class="baf-flight-alert__eyebrow"><?php esc_html_e( 'Price alerts', 'bookings-flights-core' ); ?></p>
				<h2 id="<?php echo esc_attr( $title_id ); ?>" class="baf-flight-alert__title"><?php esc_html_e( 'Save this route as a local watch intent', 'bookings-flights-core' ); ?></h2>
				<p class="baf-flight-alert__copy"><?php esc_html_e( 'Bookings and Flights stores the alert request locally. Travelpayouts or the partner provider still controls live fares, result filters, booking, payment, changes, and support.', 'bookings-flights-core' ); ?></p>
			</div>

			<?php if ( ! post_type_exists( Post_Type_Registrar::TRAVEL_ALERT ) ) : ?>
				<p class="baf-flight-alert__notice baf-flight-alert__notice--error" role="status"><?php esc_html_e( 'Alert capture is not available because the local alert records are not registered yet.', 'bookings-flights-core' ); ?></p>
			<?php else : ?>
				<?php if ( null !== $message ) : ?>
					<p class="<?php echo esc_attr( 'baf-flight-alert__notice baf-flight-alert__notice--' . $message['tone'] ); ?>" role="<?php echo esc_attr( $message['role'] ); ?>"><?php echo esc_html( $message['text'] ); ?></p>
				<?php endif; ?>

				<form class="baf-flight-alert__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="<?php echo esc_attr( Flight_Alert_Intent_Handler::ACTION ); ?>">
					<input type="hidden" name="baf_alert_redirect" value="<?php echo esc_url( $redirect ); ?>">
					<input type="hidden" name="baf_alert_source_url" value="<?php echo esc_url( $source_url ); ?>">
					<input type="hidden" name="baf_alert_surface" value="<?php echo esc_attr( $surface ); ?>">
					<input type="hidden" name="baf_alert_depart_date" value="<?php echo esc_attr( $depart_date ); ?>">
					<input type="hidden" name="baf_alert_return_date" value="<?php echo esc_attr( $return_date ); ?>">
					<input type="hidden" name="baf_alert_travelers" value="<?php echo esc_attr( (string) $travelers ); ?>">
					<input type="hidden" name="baf_alert_cabin" value="<?php echo esc_attr( $cabin ); ?>">
					<input type="hidden" name="baf_alert_route_post_id" value="<?php echo esc_attr( (string) $route_id ); ?>">
					<?php wp_nonce_field( Flight_Alert_Intent_Handler::NONCE_ACTION, Flight_Alert_Intent_Handler::NONCE_FIELD ); ?>

					<div class="baf-flight-alert__grid">
						<label class="baf-flight-alert__field">
							<span><?php esc_html_e( 'Email', 'bookings-flights-core' ); ?></span>
							<input type="email" name="baf_alert_email" value="<?php echo esc_attr( $email ); ?>" autocomplete="email" required>
						</label>
						<label class="baf-flight-alert__field">
							<span><?php esc_html_e( 'From', 'bookings-flights-core' ); ?></span>
							<input type="text" name="baf_alert_origin" value="<?php echo esc_attr( $origin ); ?>" maxlength="3" pattern="[A-Za-z]{3}" autocapitalize="characters" autocomplete="off" required>
						</label>
						<label class="baf-flight-alert__field">
							<span><?php esc_html_e( 'To', 'bookings-flights-core' ); ?></span>
							<input type="text" name="baf_alert_destination" value="<?php echo esc_attr( $destination ); ?>" maxlength="3" pattern="[A-Za-z]{3}" autocapitalize="characters" autocomplete="off" required>
						</label>
						<label class="baf-flight-alert__field">
							<span><?php esc_html_e( 'Frequency', 'bookings-flights-core' ); ?></span>
							<select name="baf_alert_frequency">
								<option value="daily" <?php selected( $frequency, 'daily' ); ?>><?php esc_html_e( 'Daily', 'bookings-flights-core' ); ?></option>
								<option value="weekly" <?php selected( $frequency, 'weekly' ); ?>><?php esc_html_e( 'Weekly', 'bookings-flights-core' ); ?></option>
								<option value="monthly" <?php selected( $frequency, 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'bookings-flights-core' ); ?></option>
							</select>
						</label>
					</div>

					<label class="baf-flight-alert__consent">
						<input type="checkbox" name="baf_alert_consent" value="1" required>
						<span><?php esc_html_e( 'Store my email and route watch intent locally so Bookings and Flights can manage this alert request. I understand live fares and booking support remain with the provider.', 'bookings-flights-core' ); ?></span>
					</label>

					<button class="baf-flight-alert__submit" type="submit"><?php esc_html_e( 'Save alert intent', 'bookings-flights-core' ); ?></button>
				</form>
			<?php endif; ?>
		</section>
		<?php

		return (string) ob_get_clean();
	}

	private static function current_url(): string {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) && is_scalar( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$request_uri = '/' . ltrim( $request_uri, '/' );

		return esc_url_raw( remove_query_arg( Flight_Alert_Intent_Handler::STATUS_QUERY_ARG, home_url( $request_uri ) ) );
	}

	private static function status_message(): ?array {
		if ( ! isset( $_GET[ Flight_Alert_Intent_Handler::STATUS_QUERY_ARG ] ) || ! is_scalar( $_GET[ Flight_Alert_Intent_Handler::STATUS_QUERY_ARG ] ) ) {
			return null;
		}

		$status = sanitize_key( wp_unslash( $_GET[ Flight_Alert_Intent_Handler::STATUS_QUERY_ARG ] ) );
		$map    = array(
			'saved'           => array(
				'text' => __( 'Your alert intent was saved locally. Confirm live prices and booking details inside the provider search before buying.', 'bookings-flights-core' ),
				'tone' => 'success',
				'role' => 'status',
			),
			'invalid_email'   => array(
				'text' => __( 'Enter a valid email address before saving this alert intent.', 'bookings-flights-core' ),
				'tone' => 'error',
				'role' => 'alert',
			),
			'missing_route'   => array(
				'text' => __( 'Enter both three-letter route codes before saving this alert intent.', 'bookings-flights-core' ),
				'tone' => 'error',
				'role' => 'alert',
			),
			'consent_required' => array(
				'text' => __( 'Consent is required before Bookings and Flights can store this local alert request.', 'bookings-flights-core' ),
				'tone' => 'error',
				'role' => 'alert',
			),
			'unavailable'     => array(
				'text' => __( 'Alert capture is unavailable right now. Use the provider search to confirm live prices.', 'bookings-flights-core' ),
				'tone' => 'error',
				'role' => 'alert',
			),
			'invalid_request' => array(
				'text' => __( 'The alert request could not be saved. Please submit the form again.', 'bookings-flights-core' ),
				'tone' => 'error',
				'role' => 'alert',
			),
		);

		return $map[ $status ] ?? null;
	}
}
