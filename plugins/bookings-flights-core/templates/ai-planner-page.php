<?php
/**
 * AI planner page template.
 *
 * @package BAF\Core
 */

use BAF\Core\AI\Provider_Factory;
use BAF\Core\Capabilities\Capability_Manager;
use BAF\Core\Settings\Settings_Manager;

defined( 'ABSPATH' ) || exit;

$ai_settings      = Settings_Manager::get_ai();
$consent_settings = Settings_Manager::get_consent();
$can_run_ai       = current_user_can( Capability_Manager::RUN_AI );
$can_edit_content = current_user_can( Capability_Manager::EDIT_CONTENT );
$mode             = sanitize_key( is_scalar( $ai_settings['mode'] ?? '' ) ? (string) $ai_settings['mode'] : '' );
$live_readiness   = Provider_Factory::live_readiness( $ai_settings, $consent_settings );
$mode_message     = 'live' === $mode ? (string) $live_readiness['message'] : __( 'Demo mode is available without live credentials or external provider calls.', 'bookings-flights-core' );

get_header();
?>

<main id="main-content" class="baf-ai-planner-page">
	<section class="baf-ai-planner" aria-labelledby="baf-ai-planner-title">
		<div class="baf-ai-planner__shell">
			<header class="baf-ai-planner__header">
				<p class="baf-ai-planner__eyebrow"><?php esc_html_e( 'AI trip planner', 'bookings-flights-core' ); ?></p>
				<h1 id="baf-ai-planner-title" class="baf-ai-planner__title"><?php esc_html_e( 'Turn travel intent into an editable trip brief', 'bookings-flights-core' ); ?></h1>
				<p class="baf-ai-planner__lede"><?php esc_html_e( 'Describe the trip, add the practical details, and generate a structured draft. Booking, payment, availability, changes, and support stay with Travelpayouts or the partner provider.', 'bookings-flights-core' ); ?></p>
			</header>

			<div class="baf-ai-planner__workspace">
				<form class="baf-ai-planner__form" data-baf-ai-planner-form aria-describedby="baf-ai-planner-status">
					<?php if ( ! $can_run_ai ) : ?>
						<p class="baf-ai-planner__notice baf-ai-planner__notice--error"><?php esc_html_e( 'AI planning is available to signed-in editors with AI permission in this phase.', 'bookings-flights-core' ); ?></p>
					<?php endif; ?>

					<label class="baf-ai-planner__field baf-ai-planner__field--full" for="baf_ai_prompt">
						<span><?php esc_html_e( 'Trip prompt', 'bookings-flights-core' ); ?></span>
						<textarea id="baf_ai_prompt" name="prompt" rows="6" maxlength="1200" required placeholder="<?php esc_attr_e( 'Plan a relaxed five-day Lisbon trip with food markets, scenic neighborhoods, and a beach day.', 'bookings-flights-core' ); ?>"></textarea>
					</label>

					<div class="baf-ai-planner__grid">
						<label class="baf-ai-planner__field" for="baf_ai_origin">
							<span><?php esc_html_e( 'Origin', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_origin" type="text" name="origin" maxlength="80" placeholder="<?php esc_attr_e( 'Miami', 'bookings-flights-core' ); ?>">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_destination">
							<span><?php esc_html_e( 'Destination', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_destination" type="text" name="destination" maxlength="120" required placeholder="<?php esc_attr_e( 'Lisbon', 'bookings-flights-core' ); ?>">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_departure">
							<span><?php esc_html_e( 'Depart', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_departure" type="date" name="departure_date">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_return">
							<span><?php esc_html_e( 'Return', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_return" type="date" name="return_date">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_days">
							<span><?php esc_html_e( 'Days', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_days" type="number" name="days" value="4" min="1" max="21" inputmode="numeric">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_travelers">
							<span><?php esc_html_e( 'Travelers', 'bookings-flights-core' ); ?></span>
							<input id="baf_ai_travelers" type="number" name="travelers" value="2" min="1" max="12" inputmode="numeric">
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_style">
							<span><?php esc_html_e( 'Style', 'bookings-flights-core' ); ?></span>
							<select id="baf_ai_style" name="travel_style">
								<option value="balanced"><?php esc_html_e( 'Balanced', 'bookings-flights-core' ); ?></option>
								<option value="food_culture"><?php esc_html_e( 'Food and culture', 'bookings-flights-core' ); ?></option>
								<option value="family"><?php esc_html_e( 'Family', 'bookings-flights-core' ); ?></option>
								<option value="budget"><?php esc_html_e( 'Budget-aware', 'bookings-flights-core' ); ?></option>
								<option value="luxury"><?php esc_html_e( 'Luxury', 'bookings-flights-core' ); ?></option>
								<option value="outdoors"><?php esc_html_e( 'Outdoors', 'bookings-flights-core' ); ?></option>
							</select>
						</label>
						<label class="baf-ai-planner__field" for="baf_ai_budget">
							<span><?php esc_html_e( 'Budget', 'bookings-flights-core' ); ?></span>
							<select id="baf_ai_budget" name="budget">
								<option value=""><?php esc_html_e( 'Flexible', 'bookings-flights-core' ); ?></option>
								<option value="budget"><?php esc_html_e( 'Budget', 'bookings-flights-core' ); ?></option>
								<option value="midrange"><?php esc_html_e( 'Midrange', 'bookings-flights-core' ); ?></option>
								<option value="premium"><?php esc_html_e( 'Premium', 'bookings-flights-core' ); ?></option>
							</select>
						</label>
					</div>

					<label class="baf-ai-planner__consent" for="baf_ai_external_consent">
						<input id="baf_ai_external_consent" type="checkbox" name="external_ai_consent" value="1">
						<span><?php esc_html_e( 'Allow this planner request to use the configured live AI provider if live mode is enabled. Demo mode stays inside WordPress.', 'bookings-flights-core' ); ?></span>
					</label>

					<label class="baf-ai-planner__consent baf-ai-planner__save-option" for="baf_ai_save_draft">
						<input id="baf_ai_save_draft" type="checkbox" name="save_draft" value="1" <?php disabled( ! $can_edit_content ); ?>>
						<span><?php esc_html_e( 'Save as an editable WordPress Trip Plan draft. It will not publish automatically.', 'bookings-flights-core' ); ?></span>
					</label>

					<div class="baf-ai-planner__actions">
						<button class="baf-ai-planner__submit" type="submit" <?php disabled( ! $can_run_ai ); ?>><?php esc_html_e( 'Create trip brief', 'bookings-flights-core' ); ?></button>
						<p class="baf-ai-planner__mode">
							<?php
							echo esc_html( $mode_message );
							?>
						</p>
					</div>

					<p id="baf-ai-planner-status" class="baf-ai-planner__status" data-baf-ai-planner-status role="status" aria-live="polite"></p>
				</form>

				<section class="baf-ai-planner__result" data-baf-ai-planner-result aria-labelledby="baf-ai-planner-result-title">
					<div class="baf-ai-planner__empty" data-baf-ai-planner-empty>
						<h2 id="baf-ai-planner-result-title"><?php esc_html_e( 'Trip brief output', 'bookings-flights-core' ); ?></h2>
						<p><?php esc_html_e( 'Generated briefs appear here with sanitized itinerary fields and recommendation-only Travelpayouts opportunities.', 'bookings-flights-core' ); ?></p>
					</div>

					<div class="baf-ai-planner__success" data-baf-ai-planner-success hidden>
						<p class="baf-ai-planner__eyebrow" data-baf-ai-planner-run></p>
						<h2 data-baf-ai-planner-title></h2>
						<p data-baf-ai-planner-summary></p>

						<div class="baf-ai-planner__brief" aria-label="<?php esc_attr_e( 'Structured trip brief', 'bookings-flights-core' ); ?>">
							<dl data-baf-ai-planner-brief></dl>
						</div>

						<p class="baf-ai-planner__draft" data-baf-ai-planner-draft hidden></p>
						<div class="baf-ai-planner__days" data-baf-ai-planner-days></div>
						<div class="baf-ai-planner__opportunities" data-baf-ai-planner-opportunities></div>
						<p class="baf-ai-planner__disclaimer" data-baf-ai-planner-disclaimer></p>
					</div>
				</section>
			</div>
		</div>
	</section>
</main>

<?php
get_footer();
