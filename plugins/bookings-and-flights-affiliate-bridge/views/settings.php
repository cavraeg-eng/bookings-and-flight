<?php
/**
 * Admin settings view.
 *
 * Expects these vars from Admin::render_page():
 *   array $suppliers, array $creds, string $search_api_url,
 *   string $postback_secret, string $credential_sync_secret,
 *   string $postback_endpoint
 *
 * @package Bookings_And_Flights\Affiliate_Bridge
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Bookings and Flights — Affiliate Bridge', 'baf-affiliate-bridge' ); ?></h1>

	<p style="max-width: 760px;">
		<?php echo esc_html__( 'Paste your affiliate program credentials below. WordPress stores the secrets and securely syncs them to the private search-api when settings are saved. The public REST endpoint only exposes which suppliers are enabled, never the secrets.', 'baf-affiliate-bridge' ); ?>
	</p>

	<form method="post" action="options.php" style="max-width: 960px;">
		<?php settings_fields( 'baf_affiliate_bridge' ); ?>

		<h2 class="title"><?php echo esc_html__( 'Search API', 'baf-affiliate-bridge' ); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="baf_search_api_url"><?php echo esc_html__( 'Search-API URL', 'baf-affiliate-bridge' ); ?></label>
				</th>
				<td>
					<input
						name="<?php echo esc_attr( BAF_OPT_SEARCH_API_URL ); ?>"
						id="baf_search_api_url"
						type="url"
						class="regular-text code"
						value="<?php echo esc_attr( $search_api_url ); ?>"
						placeholder="http://localhost:4050"
					/>
					<p class="description"><?php echo esc_html__( 'Where the Fastify search-api is reachable from this WP instance. In Local that is http://localhost:4050. In production, use an internal URL.', 'baf-affiliate-bridge' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="baf_postback_secret"><?php echo esc_html__( 'Postback Secret', 'baf-affiliate-bridge' ); ?></label>
				</th>
				<td>
					<input
						name="<?php echo esc_attr( BAF_OPT_POSTBACK_SECRET ); ?>"
						id="baf_postback_secret"
						type="password"
						class="regular-text code"
						value=""
						placeholder="<?php echo esc_attr( '' !== $postback_secret ? __( 'Configured. Leave blank to keep saved secret.', 'baf-affiliate-bridge' ) : __( 'Not configured.', 'baf-affiliate-bridge' ) ); ?>"
						autocomplete="new-password"
					/>
					<p class="description"><?php echo esc_html__( 'Shared secret between this plugin and the search-api. 16–128 printable ASCII characters.', 'baf-affiliate-bridge' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="baf_credential_sync_secret"><?php echo esc_html__( 'Credential Sync Secret', 'baf-affiliate-bridge' ); ?></label>
				</th>
				<td>
					<input
						name="<?php echo esc_attr( BAF_OPT_CREDENTIAL_SYNC_SECRET ); ?>"
						id="baf_credential_sync_secret"
						type="password"
						class="regular-text code"
						value=""
						placeholder="<?php echo esc_attr( '' !== $credential_sync_secret ? __( 'Configured. Leave blank to keep saved secret.', 'baf-affiliate-bridge' ) : __( 'Not configured.', 'baf-affiliate-bridge' ) ); ?>"
						autocomplete="new-password"
					/>
					<p class="description"><?php echo esc_html__( 'Private WordPress-to-search-api secret for syncing supplier credentials. Set the same value as BAF_CREDENTIAL_SYNC_SECRET in the search-api environment.', 'baf-affiliate-bridge' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php echo esc_html__( 'Postback URL', 'baf-affiliate-bridge' ); ?></th>
				<td>
					<code style="display:inline-block; padding:0.4em 0.6em; background:#f0f0f1;"><?php echo esc_html( $postback_endpoint ); ?>?supplier={ID}</code>
					<p class="description"><?php echo esc_html__( 'Give this URL to each affiliate program, appending the supplier ID. Each supplier must send a POST request with the shared secret in the x-baf-secret header.', 'baf-affiliate-bridge' ); ?></p>
				</td>
			</tr>
		</table>

		<h2 class="title"><?php echo esc_html__( 'Supplier Credentials', 'baf-affiliate-bridge' ); ?></h2>

		<?php foreach ( $suppliers as $id => $def ) :
			$supplier_creds = $creds[ $id ] ?? array();
		?>
			<div style="padding:1.25rem; border:1px solid #c3c4c7; background:#fff; border-radius:6px; margin-bottom:1rem;">
				<h3 style="margin-top:0;">
					<?php echo esc_html( $def['label'] ); ?>
					<a href="<?php echo esc_url( $def['apply'] ); ?>" target="_blank" rel="noopener noreferrer" style="font-size:0.8em; font-weight:normal; margin-left:0.5em;">
						<?php echo esc_html__( 'Apply →', 'baf-affiliate-bridge' ); ?>
					</a>
				</h3>
				<p style="color:#646970; max-width: 60ch;"><?php echo esc_html( $def['blurb'] ); ?></p>
				<table class="form-table" style="margin-top:0;">
					<?php foreach ( $def['fields'] as $field_key => $field_label ) :
						$field_id   = 'baf_cred_' . $id . '_' . $field_key;
						$field_name = esc_attr( BAF_OPT_SUPPLIER_CREDS ) . '[' . esc_attr( $id ) . '][' . esc_attr( $field_key ) . ']';
						$field_val  = (string) ( $supplier_creds[ $field_key ] ?? '' );
						$is_secret  = \BAF\AffiliateBridge\Settings::is_secret_field( (string) $field_key );
					?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $field_label ); ?></label>
							</th>
							<td>
								<input
									type="<?php echo $is_secret ? 'password' : 'text'; ?>"
									id="<?php echo esc_attr( $field_id ); ?>"
									name="<?php echo $field_name; // phpcs:ignore ?>"
									value="<?php echo true === $is_secret ? '' : esc_attr( $field_val ); ?>"
									class="regular-text code"
									autocomplete="<?php echo true === $is_secret ? 'new-password' : 'off'; ?>"
									placeholder="<?php echo true === $is_secret ? esc_attr( '' !== $field_val ? __( 'Configured. Leave blank to keep saved secret.', 'baf-affiliate-bridge' ) : __( 'Not configured.', 'baf-affiliate-bridge' ) ) : ''; ?>"
								/>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php endforeach; ?>

		<?php submit_button( __( 'Save all', 'baf-affiliate-bridge' ) ); ?>
	</form>

	<hr />
	<h2><?php echo esc_html__( 'Architecture', 'baf-affiliate-bridge' ); ?></h2>
	<p style="max-width: 760px;">
		<?php echo esc_html__( 'This plugin is a thin bridge. It stores supplier credentials, exposes an enabled-supplier list to the Next.js app, and ingests conversion postbacks. All actual booking happens on the supplier site — we never process payments.', 'baf-affiliate-bridge' ); ?>
	</p>
</div>
