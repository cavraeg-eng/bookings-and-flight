<?php
/**
 * Saved trips page template.
 *
 * @package BAF\Core
 */

defined( 'ABSPATH' ) || exit;

$is_logged_in = is_user_logged_in();
$login_url    = wp_login_url( home_url( '/saved-trips/' ) );

get_header();
?>

<main id="main-content" class="baf-saved-trips-page">
	<section class="baf-saved-trips" aria-labelledby="baf-saved-trips-title">
		<div class="baf-saved-trips__shell">
			<header class="baf-saved-trips__header">
				<p class="baf-saved-trips__eyebrow"><?php esc_html_e( 'Saved trips', 'bookings-flights-core' ); ?></p>
				<h1 id="baf-saved-trips-title" class="baf-saved-trips__title"><?php esc_html_e( 'Keep trip intent local until the provider handoff', 'bookings-flights-core' ); ?></h1>
				<p class="baf-saved-trips__lede"><?php esc_html_e( 'Save destinations, dates, traveler count, and the approved Travelpayouts placement path. Booking, payment, availability, changes, and support stay with Travelpayouts or the partner provider.', 'bookings-flights-core' ); ?></p>
			</header>

			<?php if ( ! $is_logged_in ) : ?>
				<section class="baf-saved-trips__signin" aria-labelledby="baf-saved-trips-signin-title">
					<div>
						<p class="baf-saved-trips__eyebrow"><?php esc_html_e( 'Member save flow', 'bookings-flights-core' ); ?></p>
						<h2 id="baf-saved-trips-signin-title"><?php esc_html_e( 'Sign in to save trip intent', 'bookings-flights-core' ); ?></h2>
						<p><?php esc_html_e( 'Anonymous visitors are sent to sign in before Bookings and Flights stores saved-trip data. No partner booking, payment, confirmation, price, or availability data is stored here.', 'bookings-flights-core' ); ?></p>
					</div>
					<a class="baf-saved-trips__button" href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Sign in', 'bookings-flights-core' ); ?></a>
				</section>
			<?php else : ?>
				<div class="baf-saved-trips__workspace">
					<form class="baf-saved-trips__form" data-baf-saved-trip-form aria-describedby="baf-saved-trips-status">
						<input type="hidden" name="saved_trip_id" value="">

						<div class="baf-saved-trips__form-head">
							<h2><?php esc_html_e( 'Trip intent', 'bookings-flights-core' ); ?></h2>
							<button class="baf-saved-trips__link-button" type="button" data-baf-saved-trip-reset hidden><?php esc_html_e( 'New saved trip', 'bookings-flights-core' ); ?></button>
						</div>

						<div class="baf-saved-trips__grid">
							<label class="baf-saved-trips__field" for="baf_saved_origin">
								<span><?php esc_html_e( 'Origin', 'bookings-flights-core' ); ?></span>
								<input id="baf_saved_origin" type="text" name="origin" maxlength="120" autocomplete="off" placeholder="<?php esc_attr_e( 'Miami', 'bookings-flights-core' ); ?>">
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_destination">
								<span><?php esc_html_e( 'Destination', 'bookings-flights-core' ); ?></span>
								<input id="baf_saved_destination" type="text" name="destination" maxlength="120" autocomplete="off" placeholder="<?php esc_attr_e( 'Lisbon', 'bookings-flights-core' ); ?>" required>
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_departure">
								<span><?php esc_html_e( 'Depart', 'bookings-flights-core' ); ?></span>
								<input id="baf_saved_departure" type="date" name="departure_date">
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_return">
								<span><?php esc_html_e( 'Return', 'bookings-flights-core' ); ?></span>
								<input id="baf_saved_return" type="date" name="return_date">
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_travelers">
								<span><?php esc_html_e( 'Travelers', 'bookings-flights-core' ); ?></span>
								<input id="baf_saved_travelers" type="number" name="travelers" value="2" min="1" max="12" inputmode="numeric">
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_style">
								<span><?php esc_html_e( 'Style', 'bookings-flights-core' ); ?></span>
								<select id="baf_saved_style" name="travel_style">
									<option value="balanced"><?php esc_html_e( 'Balanced', 'bookings-flights-core' ); ?></option>
									<option value="food_culture"><?php esc_html_e( 'Food and culture', 'bookings-flights-core' ); ?></option>
									<option value="family"><?php esc_html_e( 'Family', 'bookings-flights-core' ); ?></option>
									<option value="budget"><?php esc_html_e( 'Budget-aware', 'bookings-flights-core' ); ?></option>
									<option value="luxury"><?php esc_html_e( 'Luxury', 'bookings-flights-core' ); ?></option>
									<option value="outdoors"><?php esc_html_e( 'Outdoors', 'bookings-flights-core' ); ?></option>
								</select>
							</label>
							<label class="baf-saved-trips__field" for="baf_saved_placement">
								<span><?php esc_html_e( 'Provider path', 'bookings-flights-core' ); ?></span>
								<select id="baf_saved_placement" name="placement_key">
									<option value="flights_white_label_search"><?php esc_html_e( 'Flights search path', 'bookings-flights-core' ); ?></option>
									<option value="hotels_partner_search"><?php esc_html_e( 'Hotels partner path', 'bookings-flights-core' ); ?></option>
								</select>
							</label>
						</div>

						<label class="baf-saved-trips__field baf-saved-trips__field--full" for="baf_saved_note">
							<span><?php esc_html_e( 'Planning note', 'bookings-flights-core' ); ?></span>
							<textarea id="baf_saved_note" name="note" rows="4" maxlength="500" placeholder="<?php esc_attr_e( 'Neighborhoods to compare, timing constraints, or people joining.', 'bookings-flights-core' ); ?>"></textarea>
						</label>

						<label class="baf-saved-trips__consent" for="baf_saved_consent">
							<input id="baf_saved_consent" type="checkbox" name="local_storage_consent" value="1" required>
							<span><?php esc_html_e( 'Store this trip intent locally in WordPress. Provider booking, payment, availability, confirmation, and support data stays outside Bookings and Flights.', 'bookings-flights-core' ); ?></span>
						</label>

						<div class="baf-saved-trips__actions">
							<button class="baf-saved-trips__button" type="submit" data-baf-saved-trip-submit><?php esc_html_e( 'Save trip intent', 'bookings-flights-core' ); ?></button>
							<p id="baf-saved-trips-status" class="baf-saved-trips__status" data-baf-saved-trip-status role="status" aria-live="polite"></p>
						</div>
					</form>

					<section class="baf-saved-trips__board" aria-labelledby="baf-saved-trips-board-title">
						<div class="baf-saved-trips__board-head">
							<h2 id="baf-saved-trips-board-title"><?php esc_html_e( 'Your saved trips', 'bookings-flights-core' ); ?></h2>
							<span class="baf-saved-trips__count" data-baf-saved-trip-count></span>
						</div>
						<div class="baf-saved-trips__list" data-baf-saved-trip-list></div>
						<p class="baf-saved-trips__empty" data-baf-saved-trip-empty hidden><?php esc_html_e( 'No saved trips yet.', 'bookings-flights-core' ); ?></p>
					</section>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
