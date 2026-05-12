# Validation Baseline

Use the most targeted validation for the files changed. Update this file when commands are discovered, added, removed, or changed.

## Environment Discovered

- Current workspace: `wp-content/`
- PHP CLI available: `/opt/homebrew/bin/php`
- PHP version observed: `8.5.4`
- WP-CLI available: `/opt/homebrew/bin/wp`
- WP-CLI version observed: `2.12.0`
- Node available: `/usr/local/bin/node`
- Node version observed: `v24.13.0`
- npm available: `/usr/local/bin/npm`
- npm version observed: `11.6.2`

WP-CLI currently emits a PHP 8.5 deprecation warning from its bundled dependency. Treat that as a tooling warning unless it blocks execution.

For the current Local by Flywheel site, plain `wp` may not resolve the MySQL socket while `DB_HOST` is `localhost`. Use PHP's `mysqli.default_socket` override when needed:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp <command>
```

## PHP Syntax Checking

Run for each changed PHP file:

```bash
php -l "relative/path/to/file.php"
```

For batches, prefer explicit changed-file lists rather than broad scans of vendor/plugin backup directories.

## Plugin Activation Checking

Command from the WordPress root, not `wp-content/`:

```bash
wp plugin activate bookings-flights-core
wp plugin deactivate bookings-flights-core
```

Existing plugin checks:

```bash
wp plugin activate bookings-and-flights-affiliate-bridge
wp plugin activate bookings-and-flights-content-manager
```

Phase 0 baseline check:

```bash
cd ..
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin activate bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp option get baf_core_version
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin status bookings-flights-core
```

## PHPUnit or PHP Tests

Not available yet:

- Reason: No project PHP test suite was discovered in the workspace setup review.
- Future recommendation: If tests are added, document exact commands and bootstrap requirements here.

## Frontend / TypeScript Build, Lint, and Test

For the existing `platform/` monorepo:

```bash
cd platform && npm run build:shared
cd platform && npm run typecheck
```

Individual workspace commands:

```bash
cd platform && npm run typecheck:shared
cd platform && npm run typecheck:api
cd platform && npm run typecheck:web
```

Development servers:

```bash
cd platform && npm run dev
cd platform && npm run dev:api
cd platform && npm run dev:web
```

Not available yet:

- Reason: No lint or test scripts were discovered in root `platform/package.json`.
- Future recommendation: Add lint/test scripts only when the project adopts them.

## REST Route Smoke Testing

Existing WordPress REST namespace:

```text
/wp-json/baf/v1/
```

Potential browser/curl checks when WordPress is running:

```bash
curl -i "http://localhost:10019/wp-json/baf/v1/config"
curl -i "http://localhost:10019/wp-json/baf/v1/status"
curl -i "http://localhost:10019/wp-json/baf/v1/destinations?per_page=1"
curl -i "http://localhost:10019/wp-json/baf/v1/routes?per_page=1"
curl -i "http://localhost:10019/wp-json/baf/v1/affiliate/click"
```

Expected:

- `/config` is intentionally public and must not return secrets.
- `/status` is protected and should fail for unauthenticated users.
- `/destinations` and `/routes` are intentionally public read-only collections.
- `/destinations` and `/routes` should include `X-WP-Total` and `X-WP-TotalPages` headers.
- `/destinations` and `/routes` must only expose safe public fields and must not expose private post meta, drafts, private records, provider credentials, alerts, trip plans, or partner data.
- `/affiliate/click` is public only for signed handoff URLs and must reject missing, expired, tampered, unapproved, or non-allowlisted redirect targets.

Phase 3 WP-CLI smoke checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'do_action( "rest_api_init" ); $routes = rest_get_server()->get_routes(); foreach ( array( "/baf/v1/destinations", "/baf/v1/routes" ) as $route ) { echo $route . ":" . ( isset( $routes[ $route ] ) ? "registered" : "missing" ) . PHP_EOL; }'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$request = new WP_REST_Request( "GET", "/baf/v1/destinations" ); $request->set_param( "per_page", 1 ); $response = rest_do_request( $request ); echo "destinations:" . $response->get_status() . PHP_EOL; print_r( $response->get_headers() );'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$request = new WP_REST_Request( "GET", "/baf/v1/routes" ); $request->set_param( "per_page", 1 ); $response = rest_do_request( $request ); echo "routes:" . $response->get_status() . PHP_EOL; print_r( $response->get_headers() );'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$request = new WP_REST_Request( "GET", "/baf/v1/destinations" ); $request->set_param( "per_page", 500 ); $response = rest_do_request( $request ); echo "invalid_per_page:" . $response->get_status() . PHP_EOL;'
```

Exact Local by Flywheel URL/port should be confirmed before running smoke tests.

## Travelpayouts Plugin and Widget Validation

Use this when implementing Phase 11 or any Travelpayouts-controlled frontend/search work.

Compatibility checks:

```bash
cd ..
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin install travelpayouts --version=1.2.2
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin status travelpayouts
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin activate travelpayouts
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin deactivate travelpayouts
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp plugin activate travelpayouts
```

P11.1 local result on 2026-05-09: official `travelpayouts` version `1.2.2` was installed from WordPress.org; activate, deactivate, and reactivate passed on WordPress `6.9.4`; `wp plugin status travelpayouts` reported `Status: Active`; `travelpayouts_version` was set to `1.2.2`; no new entries were written to `debug.log` during the activation gate. WP-CLI still emits the known PHP `8.5.4` bundled dependency deprecation warning.

Source syntax check:

```bash
find plugins/travelpayouts -name '*.php' -print0 | xargs -0 -n 1 php -l
```

P11.1 local result: all official plugin PHP files reported no syntax errors. PHP `8.5.4` emitted deprecation warnings from bundled/vendor Travelpayouts code during direct linting; keep this on the Phase 11 admin/browser watchlist.

If the plugin is not installed yet in a fresh workspace, document the installation path used and then rerun the compatibility checks.

Account setup and secret checks:

- Confirm the account setup page exposes Token, Partner ID, traffic source/project, optional flights White Label domain, and optional hotels White Label domain.
- Confirm saved API tokens are not rendered into admin input values or page source.
- Confirm blank token submissions preserve the existing saved token, while non-blank token, Partner ID, project, and White Label domain values are sanitized before storage.
- Confirm the Gutenberg token route returns only non-secret configured state and never the raw token.
- Confirm local smoke checks restore or delete temporary credential options after validation.

P11.2 local result on 2026-05-12: the site started with no `travelpayouts_admin_settings` option. Temporary-token WP-CLI checks confirmed blank token submission preserves the stored token, Partner ID is reduced to digits, project/domain fields are sanitized, the Redux text renderer outputs a password field with no `value` attribute, and the Gutenberg token action returns `has_access_token: true` with a blank `access_token` and no raw token. The temporary option was deleted after validation. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning.

Manual/browser checks:

- Confirm at least one flight search form renders on a test WordPress page.
- Confirm at least one hotel widget/table renders on a test WordPress page.
- Confirm generated links or widget settings support the documented SubID strategy.
- Confirm widget searches/handoffs open Travelpayouts-controlled or partner-controlled result pages.
- Confirm White Label Widget type can keep users on a WordPress page with the Bookings and Flights header where the embedded result UX is sufficient.
- If White Label Page type is used, compare the result page against the WordPress homepage header and confirm the configured logo, favicon, brand name, header background color/image, heading copy, and header/footer menu links match as closely as Travelpayouts allows.
- Confirm generated frontend source does not expose API tokens, partner secrets, postback secrets, raw private prompts, or private user data.
- Confirm widgets do not overlap, clip, or break mobile layouts.
- If the plugin fails compatibility checks, validate the fallback path using Travelpayouts dashboard-generated widget or White Label embed code inside a capability-gated WordPress wrapper.

P11.3 local result on 2026-05-12: a temporary WordPress page was created with `[tp_popular_routes_widget destination="BKK" subid="baf_home_flights_test_surface"]`, `[tp_hotel_widget ...]`, and `[tp_hotel_selections_widget ...]` while `travelpayouts_admin_settings` contained only a temporary non-secret marker, language, and currency. WP-CLI `do_shortcode()` confirmed the flight shortcode rendered a `//www.travelpayouts.com/weedle/widget.js` script with `marker=123456.wpplugin_baf_home_flights_test_surface`, `currency=usd`, `locale=en`, `destination=BKK`, and default `host=hydra.aviasales.ru`; both hotel shortcodes returned empty output. Browser checks at `1280x900` and `375x812` confirmed the page loaded, the flight script was present, hotel scripts were absent, and no checkout/payment text appeared in main content. A frontend source scan found no `account_api_token`, `api_token`, `postback`, `secret`, `access_token`, authorization/bearer string, checkout, payment, or refund text. The temporary page and `travelpayouts_admin_settings` option were deleted after validation. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning.

P11.4-P11.6 local result on 2026-05-12: the activation/deactivation gate was re-run and `travelpayouts` version `1.2.2` returned to `Status: Active`. A compact WP-CLI shortcode smoke check started from the expected missing `travelpayouts_admin_settings` option, used a temporary non-secret marker, confirmed the flight shortcode still rendered `weedle/widget.js` with `baf_home_flights_test_surface`, confirmed hotel widget and hotel selections shortcode output lengths were `0`, found no forbidden token/secret/authorization/checkout/payment/refund strings, and restored the option to missing. A temporary browser validation page was then checked at `1280x900` and `375x812`; both viewports retained the WordPress header/home link and footer, loaded one `weedle/widget.js` script, showed no checkout/payment/refund text, and logged no browser console errors. A `curl` source scan found `weedle/widget.js` and no forbidden secret/payment terms. The temporary page and temporary `travelpayouts_admin_settings` option were deleted after validation. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning, but commands succeeded with the Local MySQL socket.

P11.6 account persistence follow-up on 2026-05-12: a backend reproduction confirmed that partial account settings submissions could drop the saved API token, Partner ID, selected Project, or White Label domains when Redux omitted fields or submitted a blank/placeholder Project value. `AccountOptionsSanitizer` now preserves existing account values across missing submissions, preserves the saved Project across blank placeholder submissions, and sanitizes Project IDs as numeric values. A direct sanitizer regression check confirmed token, marker, project, flights White Label, and hotels White Label preservation without mutating the live option. `PlatformsEndpoint` now keeps the saved Project available as a fallback select option when Travelpayouts traffic-source lookup is unavailable. The account form field registry now exposes `hotels_domain` instead of hiding it behind the staged plugin's hardcoded HotelLook availability gate. Plugin deactivate/reactivate passed, the flight shortcode still rendered a Travelpayouts `weedle/widget.js` source without exposing saved credentials, and hotel shortcode output remains `0` because Hotellook is a legacy disabled path; current hotel search should use a dashboard-generated Trip.com or other Hotels & Accommodation widget/link.

P11.6 White Label Widget wrapper follow-up on 2026-05-12: current official Travelpayouts account settings contain Token, Partner ID, Project, flights White Label domain, and hotels White Label domain. The missing item was a WordPress-side place to paste Travelpayouts White Label Widget code. `bookings-flights-core` now registers `baf_travelpayouts_settings.white_label_widget_id` and `baf_travelpayouts_settings.white_label_results_url` on the Integrations page and renders `[baf_travelpayouts_white_label]`. Sanitizer checks confirmed a pasted Travelpayouts script is reduced to the `wl_id` only, raw script is not stored, blank API-token saves preserve the saved token, root-relative results URLs become absolute site URLs, and a shortcode smoke check emitted `tpwl-search`, `tpwl-tickets`, the Travelpayouts `tpwgts.com/wl_web/main.js` loader, and configured `resultsURL` from an injected test option without mutating live settings. `bookings-flights-core` deactivate/reactivate passed. The real Widget ID is still missing from live settings until it is pasted in the new Integrations field.

P11.6 Trip.com hotel widget wrapper follow-up on 2026-05-12: after confirming the official plugin hotel path is legacy Hotellook-based and the user is using Trip.com for hotel searches, `bookings-flights-core` now registers `baf_travelpayouts_settings.hotel_widget_script_url` on the Integrations page and renders `[baf_travelpayouts_hotel_widget]`. PHP syntax checks passed for the changed core files. WP-CLI Settings API checks confirmed the hotel widget, White Label Widget ID, and White Label results URL fields are registered. Sanitizer checks confirmed pasted Travelpayouts widget code is reduced to an allowlisted `https://tp.media/...` script URL, raw script is not stored, and a non-Travelpayouts host is rejected. A shortcode smoke check with an injected test option confirmed `[baf_travelpayouts_hotel_widget]` is registered, emits the Trip.com wrapper and `tp.media` script URL, and contains no API-token, authorization, payment, checkout, or refund terms. `bookings-flights-core` deactivate/reactivate passed and returned to active. WP-CLI required the Local MySQL socket to be passed through PHP `mysqli.default_socket`; the known WP-CLI PHP deprecation warning still appears. The real Trip.com/Hotels & Accommodation widget script is still missing from live settings until it is pasted and browser-validated on the intended page.

P11.6 widget-save regression follow-up on 2026-05-12: the Bookings & Flights Integrations form was confirmed to use the correct Settings API group and field names, but pasted White Label and Trip.com widget values could still save as blank when the copied code used a slightly different Travelpayouts format or was rejected by the sanitizer. `sanitize_travelpayouts()` now accepts common `wl_id`, `wl-id`, and `data-wl-id` formats, accepts `src = "..."` and protocol-relative Travelpayouts widget scripts, restricts hotel widget scripts to Travelpayouts widget hosts and script paths, and preserves any existing saved widget value with an admin error instead of silently blanking it when a pasted value is invalid. PHP syntax passed; direct sanitizer checks passed for full White Label script extraction, spaced `tp.media/content` hotel script extraction, invalid-value preservation, and a temporary database round-trip with dummy non-secret widget values followed by restoring the original live option.

P11.6 `tpwgt.com` Trip.com widget follow-up on 2026-05-12: the real Trip.com hotel widget snippet used `https://tpwgt.com/content?...`, which is a Travelpayouts widget host not covered by the first allowlist. The sanitizer now accepts `tpwgt.com` and subdomains while keeping the `/content` script path requirement. PHP syntax passed, the exact user-provided Trip.com snippet was recognized as host `tpwgt.com` and path `/content`, and the sanitized script URL was saved to `baf_travelpayouts_settings.hotel_widget_script_url` without printing the full tracking URL. The White Label Widget ID is still missing and the hotel shortcode still needs to be placed on the intended page and browser-validated.

P11.6 White Label Page template follow-up on 2026-05-12: the user-provided White Label code was a full Page-type HTML template with Travelpayouts placeholders such as `[:embed_script:]`, `[:route_info:]`, `[:current_year:]`, `tpwl-search`, and `tpwl-tickets`, not Widget-type code containing a `wl_id`. PHP syntax passed and a representative sanitizer check confirmed Page-type templates now preserve any existing widget ID and show the specific `baf_white_label_page_template_pasted` admin error instead of the generic missing-`wl_id` message. The template belongs in the Travelpayouts White Label Page design/template area, while the Bookings & Flights field still needs Widget-type code that contains `https://tpwgts.com/wl_web/main.js?wl_id=...` or the raw `wl_id`.

P11.6 final browser validation on 2026-05-12: the published Flights page now contains `[baf_travelpayouts_white_label]`, the published Hotels page now contains `[baf_travelpayouts_hotel_widget]`, and the live `baf_travelpayouts_settings` option reports the White Label Widget ID, White Label results URL, and hotel widget URL present without printing the saved values. Chrome desktop validation confirmed the Flights page renders a clean Travelpayouts White Label search form and the Hotels page renders the Trip.com hotel surface with a visible sponsored handoff link. Chrome mobile-width validation confirmed both pages fit without obvious overlap or clipped controls. Opening the direct Trip.com partner URL rendered the Trip.com destination/date/room search form. A fixed-header spacing patch keeps generic page content visible below the site header, and the oversized generated header/footer stylesheet was split into `header.css`, `mobile-nav.css`, and `footer.css` before publishing the static theme. The bundled PHP-DI `ObjectCreator` compatibility patch removed PHP 8.5 deprecation output from the Flights page. `curl` source scans for `/flights/` and `/hotels/` found no `Deprecated`, `Warning`, `Fatal`, API token, authorization, bearer, checkout, payment, refund, or secret text and confirmed the split theme CSS handles load. REST smoke checks confirmed unauthenticated AI itinerary creation returns `401`, public destinations return a bounded collection, and unsigned affiliate click handoff returns a missing-parameter `400` rather than redirecting.

P11.6 Codex review follow-up on 2026-05-12: PR #6 was merged before the delayed Codex review comments posted, then the actionable P1 review items were patched on a follow-up branch. The White Label and hotel widget shortcodes now require `baf_consent_settings.allow_provider_requests` before rendering external Travelpayouts or Trip.com scripts/iframes; disabled consent returns no third-party request surface for public visitors and an escaped admin-only notice for managers. The custom table migrations now use per-table version options (`baf_db_version_clicks`, `baf_db_version_ai_sessions`, `baf_db_version_provider_stats`) in addition to the aggregate `baf_db_version`, so a successful click-table upgrade cannot skip AI session or provider stats `dbDelta()` runs in the same release. Validation: PHP syntax checks passed for both shortcode files and all three migration files; shortcode smoke checks passed for disabled and enabled provider consent; migration version-gate checks passed with aggregate version current and table-specific versions forced old; `git diff --check` passed. WP-CLI still emits known Travelpayouts PHP 8.5 deprecation noise, but commands completed and output checks passed.

P11.6 consent-enabled completion validation on 2026-05-12: after `baf_consent_settings.allow_provider_requests` was enabled by the admin, the live `baf_travelpayouts_settings` option reported the White Label Widget ID, White Label results URL, and hotel widget URL present without printing saved values. Source checks confirmed `/flights/` renders `tpwl-search`, `tpwl-tickets`, and the Travelpayouts `tpwgts.com/wl_web/main.js` loader, while `/hotels/` renders the Trip.com partner iframe and sponsored handoff link. A rendered Playwright pass at 1440x900 and 390x844 confirmed the Bookings and Flights header remains present, both pages return 200, no `{{placeholder}}` defaults remain visible, and no API key, authorization, bearer, secret, checkout, payment, PHP warning, or deprecation text appears in the rendered source. During this pass, a visible static-theme footer placeholder bug was fixed by replacing placeholder defaults with escaped production-safe footer fallback copy and hiding empty phone/email links; the hotel iframe wrapper was also adjusted so the mobile page no longer overflows horizontally. The Trip.com iframe can still compress its own internal fields on narrow screens because that UI is provider-owned, so the visible handoff button remains the reliable mobile fallback.

Backend mode decision checks:

- Confirm the final backend mode says Travelpayouts controls monetized search/results/booking handoff.
- Confirm official-plugin-first is limited to validated surfaces, and every unvalidated or incompatible surface has a Travelpayouts dashboard-generated widget/White Label fallback inside a capability-gated WordPress wrapper.
- Confirm `/search/flights` and `/search/hotels` remain shell/configuration, placement, consent/disclosure, SubID, missing-configuration, or handoff metadata routes rather than live inventory APIs.
- Source-scan WordPress plugin code and docs for new canonical live inventory storage before closing backend-mode work.

P11.5 documentation result on 2026-05-12: backend mode is explicit as official-plugin-first for the locally validated flight widget path and Travelpayouts dashboard-generated hotel widget/table and White Label embeds inside the governed WordPress wrapper for unvalidated or inactive plugin surfaces. A source scan found no implemented WordPress `/search/flights` or `/search/hotels` route and no new WordPress canonical live-inventory storage. Existing `platform/` Fastify search routes, adapters, in-process offer caches, and search-session logging remain optional integration infrastructure and are not approved as the canonical WordPress backend by P11.5.

## REST Permission Failure Testing

For every protected route:

1. Test while unauthenticated.
2. Test as a user without the required capability when possible.
3. Confirm response does not expose private data or secrets.
4. Confirm HTTP status and error body are intentional.

Document route-specific commands in this file as routes are implemented.

Phase 0 note:

- `bookings-flights-core` intentionally registers no REST routes.
- Existing `baf/v1` routes belong to `bookings-and-flights-affiliate-bridge`.

Phase 3 note:

- `bookings-flights-core` registers public read-only `/destinations` and `/routes` routes with explicit custom permission callbacks.
- These routes do not have an unauthenticated failure case by design because they expose only published public discovery content.
- Continue checking existing protected `/status` while unauthenticated to verify the shared namespace still includes permission-failure coverage.

Phase 9 release readiness WP-CLI smoke checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'do_action( "rest_api_init" ); $routes = rest_get_server()->get_routes(); foreach ( array( "/baf/v1/config", "/baf/v1/status", "/baf/v1/postback", "/baf/v1/destinations", "/baf/v1/routes", "/baf/v1/affiliate/click", "/baf/v1/ai/itinerary" ) as $route ) { echo $route . ":" . ( isset( $routes[ $route ] ) ? "registered" : "missing" ) . PHP_EOL; }'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$checks = array( array( "GET", "/baf/v1/status", array() ), array( "POST", "/baf/v1/ai/itinerary", array( "destination" => "Lisbon" ) ), array( "GET", "/baf/v1/destinations", array( "per_page" => 1 ) ), array( "GET", "/baf/v1/routes", array( "per_page" => 1 ) ), array( "GET", "/baf/v1/affiliate/click", array() ), array( "GET", "/baf/v1/postback", array() ), array( "GET", "/baf/v1/config", array() ) ); wp_set_current_user( 0 ); foreach ( $checks as $check ) { $request = new WP_REST_Request( $check[0], $check[1] ); foreach ( $check[2] as $key => $value ) { $request->set_param( $key, $value ); } $response = rest_do_request( $request ); echo $check[1] . ":" . $response->get_status() . PHP_EOL; }'
```

Expected:

- `/status` and `/ai/itinerary` fail unauthenticated with `401`.
- `/destinations`, `/routes`, and `/config` return `200` for unauthenticated safe reads.
- `/affiliate/click` rejects unsigned/missing handoffs with `400`.
- `/postback` rejects missing/invalid shared-secret requests with `403`.
- All routes have explicit permission callbacks.

## Database Migration Verification

For future custom tables:

1. Confirm activation creates or updates tables idempotently.
2. Confirm table names use `$wpdb->prefix`.
3. Confirm charset/collation use the current site settings.
4. Confirm no destructive migration occurs without explicit approval.
5. Confirm repeated activation does not duplicate schema or data.

Potential WP-CLI checks from WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'global $wpdb; $table = $wpdb->prefix . "bf_clicks"; echo "bf_clicks:" . ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table ? "present" : "missing" ) . PHP_EOL;'
wp option get baf_db_version
```

Phase 4 note:

- `bookings-flights-core` creates `$wpdb->prefix . 'bf_clicks'` idempotently with `dbDelta()` for optional affiliate click event metadata.
- The table stores post ID, provider, SubID, target host, user ID, generated event UUID, event time, and hashed request identifiers. It must not store provider tokens, full target URLs, raw IP addresses, raw user agents, or raw referrers.

Phase 6 note:

- `bookings-flights-core` creates `$wpdb->prefix . 'bf_ai_sessions'` idempotently with `dbDelta()` for AI generation run metadata.
- The table stores run UUID, timestamps, user ID, provider, mode, status, prompt version, optional source post ID, request/output hashes, output summary, and sanitized errors. It must not store provider API keys, raw prompts, raw model responses, private customer data, or full generated itinerary JSON.

Phase 8 note:

- `bookings-flights-core` creates `$wpdb->prefix . 'bf_provider_stats'` idempotently with `dbDelta()` for provider status reporting.
- The table stores snapshot UUID, timestamp, provider key, status, metric key/value, sanitized message, and source label. It must not store API keys, raw provider payloads, private customer data, raw request identifiers, or conversion identifiers.

Phase 8 WP-CLI smoke checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'global $wpdb; $table = $wpdb->prefix . "bf_provider_stats"; echo "bf_provider_stats:" . ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table ? "present" : "missing" ) . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp cron event run baf_sync_provider_stats
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$admins = get_users( array( "role" => "administrator", "number" => 1, "fields" => "ID" ) ); wp_set_current_user( (int) $admins[0] ); echo "can_reports:" . ( current_user_can( BAF\Core\Capabilities\Capability_Manager::VIEW_REPORTS ) ? "yes" : "no" ) . PHP_EOL; wp_set_current_user( 0 ); echo "anon_reports:" . ( current_user_can( BAF\Core\Capabilities\Capability_Manager::VIEW_REPORTS ) ? "yes" : "no" ) . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$admins = get_users( array( "role" => "administrator", "number" => 1, "fields" => "ID" ) ); wp_set_current_user( (int) $admins[0] ); BAF\Core\Admin\Admin_Manager::register_menu(); global $submenu; $slugs = array(); foreach ( (array) ( $submenu["baf-dashboard"] ?? array() ) as $item ) { $slugs[] = $item[2]; } echo "reports_menu:" . ( in_array( "baf-reports", $slugs, true ) ? "registered" : "missing" ) . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$admins = get_users( array( "role" => "administrator", "number" => 1, "fields" => "ID" ) ); wp_set_current_user( (int) $admins[0] ); ob_start(); BAF\Core\Admin\Reports_Page::render(); $html = ob_get_clean(); echo "reports_render:" . ( str_contains( $html, "Bookings and Flights Reports" ) ? "ok" : "missing" ) . PHP_EOL;'
```

Phase 9 release readiness custom table check from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'global $wpdb; foreach ( array( "bf_clicks", "bf_ai_sessions", "bf_provider_stats" ) as $suffix ) { $table = $wpdb->prefix . $suffix; echo $suffix . ":" . ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) === $table ? "present" : "missing" ) . PHP_EOL; } echo "baf_db_version:" . get_option( "baf_db_version", "" ) . PHP_EOL; echo "baf_core_version:" . get_option( "baf_core_version", "" ) . PHP_EOL;'
```

Phase 6 WP-CLI smoke checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'do_action( "rest_api_init" ); $routes = rest_get_server()->get_routes(); echo "/baf/v1/ai/itinerary:" . ( isset( $routes["/baf/v1/ai/itinerary"] ) ? "registered" : "missing" ) . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$request = new WP_REST_Request( "POST", "/baf/v1/ai/itinerary" ); $request->set_param( "destination", "Lisbon" ); $response = rest_do_request( $request ); echo "unauth:" . $response->get_status() . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$admins = get_users( array( "role" => "administrator", "number" => 1, "fields" => "ID" ) ); wp_set_current_user( (int) $admins[0] ); update_option( "baf_ai_settings", array( "mode" => "demo", "provider" => "", "api_key" => "" ), false ); $request = new WP_REST_Request( "POST", "/baf/v1/ai/itinerary" ); $request->set_param( "destination", "Lisbon" ); $request->set_param( "days", 2 ); $response = rest_do_request( $request ); echo "demo:" . $response->get_status() . PHP_EOL;'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval '$admins = get_users( array( "role" => "administrator", "number" => 1, "fields" => "ID" ) ); wp_set_current_user( (int) $admins[0] ); update_option( "baf_ai_settings", array( "mode" => "live", "provider" => "openai", "api_key" => "test-key-not-used" ), false ); update_option( "baf_consent_settings", array( "allow_external_ai" => false, "allow_provider_requests" => false, "consent_notice_override" => "" ), false ); $request = new WP_REST_Request( "POST", "/baf/v1/ai/itinerary" ); $request->set_param( "destination", "Lisbon" ); $response = rest_do_request( $request ); echo "missing_consent:" . $response->get_status() . PHP_EOL; update_option( "baf_ai_settings", array( "mode" => "demo", "provider" => "", "api_key" => "" ), false );'
```

## WP-Cron / Background Job Verification

Phase 5 scheduled hooks:

- `baf_refresh_cached_offers`
- `baf_process_travel_alerts`
- `baf_sync_provider_stats`
- `baf_cleanup_job_records`

WP-CLI checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'foreach ( array( "baf_refresh_cached_offers", "baf_process_travel_alerts", "baf_sync_provider_stats", "baf_cleanup_job_records" ) as $hook ) { echo $hook . ":" . ( false === wp_next_scheduled( $hook ) ? "missing" : "scheduled" ) . PHP_EOL; }'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp cron event run baf_sync_provider_stats
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp option get baf_job_status --format=json
```

Expected:

- Activation schedules all Phase 5 hooks once.
- Deactivation unschedules all Phase 5 hooks.
- Running a job records a non-secret status in `baf_job_status`.
- Missing provider configuration or disabled consent defers work safely rather than making provider calls during render.

## Admin UI Manual Review

For admin-facing work:

- Confirm page only appears to authorized users.
- Confirm settings save requires nonce and capability.
- Confirm missing provider configuration state is clear.
- Confirm secret fields are masked and not exposed in page source.
- Confirm success/error notices are escaped.
- Confirm styles are scoped to plugin admin pages.

Phase 2 core settings smoke checks from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'do_action( "admin_init" ); global $wp_registered_settings; foreach ( array( "baf_settings", "baf_travelpayouts_settings", "baf_ai_settings", "baf_consent_settings", "baf_tracking_settings" ) as $option ) { echo $option . ":" . ( isset( $wp_registered_settings[ $option ] ) ? "registered" : "missing" ) . PHP_EOL; }'
```

Expected:

- All five Phase 2 core options are registered.
- Secret values are not rendered into core or affiliate bridge admin HTML.
- Unauthenticated users do not have `manage_baf_settings`.
- Existing `baf/v1/status` remains inaccessible to unauthenticated users.

Phase 5 admin jobs smoke check from the WordPress root:

```bash
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp eval 'do_action( "admin_menu" ); $hook = get_plugin_page_hookname( "baf-jobs", "baf-dashboard" ); echo "baf-jobs:" . ( "" === $hook ? "missing" : "registered" ) . PHP_EOL;'
```

Phase 8 admin reports checks:

- Confirm `baf-reports` only appears to users with `view_baf_reports`.
- Confirm unauthenticated users do not have `view_baf_reports`.
- Confirm report rendering and CSV rows do not expose provider API keys, raw prompts, raw IP addresses, user agents, referrers, or private customer data.

## Browser/UI Automation

Available general options:

- Playwright/browser tooling in the agent environment.
- Existing screenshots in `output/playwright/`.

Not available yet:

- Reason: No committed project browser test suite was discovered.
- Future recommendation: Add smoke tests for critical admin and frontend workflows after UI surfaces stabilize.

## Phase 12 Information Architecture Validation

Use this for Phase 12 planning tickets before visual/template implementation begins.

Static inventory commands:

```bash
git ls-files .plan
rg --files themes/bookings-and-flights-static
rg -n "register_post_type|register_taxonomy|add_shortcode|register_block_type|wp_nav_menu|register_nav_menus|Travelpayouts|tpwl|tpwgts|baf_" themes/bookings-and-flights-static plugins/bookings-flights-core .plan -g '!platform/**'
wc -l themes/bookings-and-flights-static/assets/css/*.css themes/bookings-and-flights-static/*.php plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
```

WordPress inventory commands:

Use the active environment's `wp` binary, project wrapper, or WP-CLI alias. If Local or another runtime requires a site-specific PHP/MySQL socket, configure that in the local wrapper or shell environment instead of hard-coding it into the shared baseline.

```bash
wp --skip-plugins=travelpayouts post list --post_type=page --post_status=publish,draft,private --fields=ID,post_title,post_name,post_status,page_template --format=table
wp --skip-plugins=travelpayouts menu item list primary-menu --fields=db_id,title,url,type,object,object_id,classes --format=table
wp --skip-plugins=travelpayouts post list --post_type=destination,route,travel_deal,trip_plan,travel_partner,travel_alert --post_status=any --fields=ID,post_title,post_type,post_status,post_name --format=table
```

P12.1 local result on 2026-05-12: Phase 11 was confirmed complete and the backend mode remains Travelpayouts-controlled. The published front page is page ID `246` using `page-home.php`; `/flights/` is page ID `261` with `[baf_travelpayouts_white_label]`; `/hotels/` is page ID `262` with `[baf_travelpayouts_hotel_widget]`. The assigned primary menu has eight items: Home, Flights, Hotels, About, Contact, Services, Privacy Policy, and Terms & Conditions. It is missing the Phase 12 target items Explore, Deals, Trip Planner, and Saved Trips. No local destination, route, travel deal, trip plan, partner, or alert posts exist yet. Current tracked static theme source files are under the 600-line limit, but `header.css` is near the limit at 544 lines. Documentation review is captured in `.plan/phase-12-sitemap-navigation-page-ownership.md`. WP-CLI emitted known PHP 8.5 deprecation warnings from tooling and the Travelpayouts plugin, but inventory commands completed.

## Documentation-Only Changes

For documentation-only changes:

- Confirm required files exist.
- Confirm phase statuses and architecture contracts are consistent.
- No PHP/TypeScript runtime validation is required unless code also changes.
