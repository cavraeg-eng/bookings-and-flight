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

P12.2 design-system documentation checks:

```bash
test -f .plan/phase-12-design-system-component-inventory.md
rg -n "Unified search panel|Travelpayouts widget frame|Affiliate disclosure|Admin widget placement table|Research Consulted" .plan/phase-12-design-system-component-inventory.md
rg -n "phase-12-design-system-component-inventory" .plan/phased-implementation.md .plan/architecture-baseline.md .plan/phase-review-log.md
wc -l themes/bookings-and-flights-static/assets/css/*.css plugins/bookings-flights-core/assets/css/*.css
git diff --check
```

P12.2 local result on 2026-05-12: design-token direction, real-media strategy, component inventory, disclosure treatment, accessibility checklist, and widget-frame guardrails are documented in `.plan/phase-12-design-system-component-inventory.md`. Current CSS inventory still shows `header.css` at 544 lines, so Phase 12.3 should split or protect header/style architecture before adding large navigation/search-shell styles. `home.css` remains gradient-heavy and generic, which is now explicitly deferred to later visual implementation. The Trip.com hotel wrapper still relies on a visible handoff fallback because provider-owned iframe content can compress on mobile. Codex review on PR #10 found no major issues.

P12.3 CSS architecture validation:

```bash
wc -l themes/bookings-and-flights-static/assets/css/*.css themes/bookings-and-flights-static/functions.php themes/bookings-and-flights-static/ARCHITECTURE.md
php -l themes/bookings-and-flights-static/functions.php
rg -n "components.css|bookings_and_flights-components|fonts -> tokens -> base -> components" themes/bookings-and-flights-static/functions.php themes/bookings-and-flights-static/ARCHITECTURE.md .plan/phase-12-css-split-theme-architecture.md
curl -I "http://localhost:10019/wp-content/themes/bookings-and-flights-static/assets/css/components.css"
curl -s "http://localhost:10019/" | rg "components.css|header.css|mobile-nav.css|footer.css"
git diff --check
```

P12.3 local result on 2026-05-12: shared `.skip-link` and `.btn` primitives moved from `header.css` into `themes/bookings-and-flights-static/assets/css/components.css`. `functions.php` now enqueues `components.css` after `base.css` and before `header.css`. CSS line counts after the split are: `components.css` 85, `header.css` 464, `mobile-nav.css` 70, `footer.css` 279, `home.css` 373, `tokens.css` 388, `base.css` 256, and `fonts.css` 17. No tracked CSS source file exceeds 600 lines. Local source and browser smoke confirmed `components.css`, `header.css`, `mobile-nav.css`, and `footer.css` each load once on the home page; default desktop and 390px mobile browser checks reported no console errors. Codex review on PR #11 found no major issues.

P12.4 widget-frame documentation checks:

```bash
test -f .plan/phase-12-widget-frame-layout-rules.md
rg -n "White Label search plus results|Hotel search widget|Required States|Disclosure Placement|Phase 13 Registry Prerequisites|Performance Rules" .plan/phase-12-widget-frame-layout-rules.md
rg -n "phase-12-widget-frame-layout-rules|P12.4|ONE-77" .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/phase-review-log.md .plan/regression-watchlist.md
git diff --check
```

P12.4 local result on 2026-05-12: widget frame dimensions, responsive constraints, loading/missing/consent-disabled/no-script/unavailable/empty/error states, disclosure placement, performance rules, and Phase 13 registry prerequisites are documented in `.plan/phase-12-widget-frame-layout-rules.md`. The rules explicitly preserve provider-owned live search/results behavior and require disclosure plus fallback handoff outside provider iframes/scripts. Codex review on PR #12 found no major issues.

P12.5 page-level wireframe documentation checks:

```bash
test -f .plan/phase-12-page-level-wireframes.md
rg -n "Home Wireframe|Flights Wireframe|Hotels Wireframe|Explore Wireframe|Destination Detail Wireframe|Route Detail Wireframe|Deals Wireframe|AI Trip Planner Wireframe|Saved Trips Wireframe|Admin Widget Placement Wireframe|Browser Screenshot Plan" .plan/phase-12-page-level-wireframes.md
rg -n "phase-12-page-level-wireframes|P12.5|ONE-78" .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/phase-review-log.md .plan/regression-watchlist.md
git diff --check
```

P12.5 local result on 2026-05-12: structured desktop and mobile wireframes are documented for the requested public and admin surfaces. The document maps each surface to WordPress owner, future template target, follow-up phase, Travelpayouts/partner placement, disclosure/CTA placement, and later browser screenshot validation. Codex review on PR #13 found no major issues.

P12.6 final Phase 12 review checks:

```bash
test -f .plan/phase-12-completion-gate.md
rg -n "Scope Review|Acceptance Criteria Review|Static Template Review|CSS File-Size Review|Responsive Wireframe Review|Phase 13 Start Checklist|Phase 12 Result" .plan/phase-12-completion-gate.md
wc -l themes/bookings-and-flights-static/assets/css/*.css plugins/bookings-flights-core/assets/css/*.css
rg -n "phase-12-completion-gate|P12.6|ONE-79" .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/phase-review-log.md .plan/regression-watchlist.md .plan/known-issues.md
git diff --check
```

P12.6 local result on 2026-05-12: the final Phase 12 gate documents scope review, acceptance-criteria status, static template review, responsive wireframe review, CSS file-size review, documentation completeness, deferred runtime implementation risks, runtime browser screenshots, keyboard navigation review, and the Phase 13 start checklist. Codex review on PR #14 found a P2 consistency issue because browser screenshots and keyboard review had not yet been executed; the follow-up validation pass resolved that blocker. The Codex in-app Browser captured an initial live pass, then became unavailable after a tab lifecycle error, so the final clean public screenshots used Playwright against `http://bookings-and-flights.local` at `1440x1000`, `1024x900`, and `390x844`. Home, Flights, and Hotels all returned `200`, rendered nonblank pages, had no framework overlay, had no horizontal overflow, and showed no admin toolbar in the clean pass. Explore, Deals, Trip Planner, and Saved Trips returned WordPress `404` pages and remain deferred implementation work. Keyboard review covered desktop header order, mobile header order, mobile menu open/focus-trap/Escape behavior, Flights widget focus, and Hotels handoff focus. Runtime keyboard bugs were fixed by adding visible focus outlines for Travelpayouts White Label mount points, adding a focus bridge that keeps the Trip.com iframe keyboard-reachable while adding a visible wrapper outline during iframe focus, and delaying the mobile menu focus handoff so focus lands on the first menu link after open. Codex review on PR #15 requested that the Trip.com iframe remain keyboard-reachable, and the follow-up patch keeps the iframe in sequential navigation while preserving the visible sponsored `Open hotel search` handoff link as the next tab stop. Validation also included PHP syntax checks for changed shortcode files, `bookings-flights-core` deactivate/reactivate, source scans for the hotel iframe focus bridge and `tpwl-search`, sensitive term scans that found only the public theme `tokens.css` design-token asset, and `git diff --check`. Runtime visual/template work remains deferred to Phase 13+ follow-up phases.

P13.1 placement registry validation:

```bash
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php
php -l plugins/bookings-flights-core/includes/class-plugin.php
php -l plugins/bookings-flights-core/includes/class-activator.php
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval 'use BAF\Core\Services\Travelpayouts_Widget_Registry_Service; Travelpayouts_Widget_Registry_Service::maybe_install(); $service = new Travelpayouts_Widget_Registry_Service(); /* verify public projection, private capability gate, admin save/delete smoke */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" plugin deactivate bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" plugin activate bookings-flights-core
git diff --check
```

P13.1 local result on 2026-05-12: `BAF\Core\Services\Travelpayouts_Widget_Registry_Service` installs `baf_travelpayouts_widget_registry` idempotently as a non-autoloaded option and seeds two current placement records from existing Travelpayouts settings: Flights White Label search and Hotels partner search. The smoke check confirmed the option exists with schema version `1.0.0`, public registry reads strip private embed references, embed URLs, and notes, private registry reads are blocked until an administrator capability is set, trusted server-side rendering reads return active configured private embed data, administrator save/delete works for a temporary placement, pasted Travelpayouts script code is reduced to the approved `https://tpwgts.com/wl_web/main.js?...` URL, non-approved Trip.com iframe paths are rejected for iframe placements, dashboard script URLs are rejected when submitted as iframe sources, SubID patterns are normalized to lowercase underscore format, repeated registry normalization preserves placement `updated_at` values instead of writing on every page load, and malformed stored placements are preserved during bootstrap normalization while staying out of runtime public reads. The temporary smoke placement was deleted. `bookings-flights-core` deactivate/reactivate passed. WP-CLI still emits the known bundled dependency deprecation warnings from WP-CLI and the Travelpayouts plugin.

P13.2 placement admin UI validation:

```bash
php -l plugins/bookings-flights-core/includes/admin/class-admin-manager.php
php -l plugins/bookings-flights-core/includes/admin/class-widget-placements-page.php
php -l plugins/bookings-flights-core/includes/admin/class-widget-placement-form.php
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval 'use BAF\Core\Admin\Admin_Manager; /* verify baf-widget-placements submenu registration for an administrator */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval 'use BAF\Core\Admin\Widget_Placements_Page; /* render page and verify table/form exist without raw embed URLs in the listing */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval 'use BAF\Core\Admin\Widget_Placement_Form; use BAF\Core\Services\Travelpayouts_Widget_Registry_Service; /* verify subscriber private-read denial, administrator create/update/delete smoke, public projection stripping, SubID normalization */'
node Playwright smoke against http://bookings-and-flights.local/wp-admin/admin.php?page=baf-widget-placements
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" plugin deactivate bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" plugin activate bookings-flights-core
git diff --check
```

P13.2 local result on 2026-05-12: `BAF\Core\Admin\Widget_Placements_Page` adds the `baf-widget-placements` submenu and `BAF\Core\Admin\Widget_Placement_Form` renders/sanitizes the placement form. The WP-CLI smoke checks confirmed the submenu registers for an administrator, an affiliate-only temporary user receives the Bookings & Flights parent menu and Widget Placements submenu without Settings access, private registry reads are denied to a temporary subscriber, administrator placement save/delete works through sanitized form payloads, public placement reads strip private embed URLs, and SubID patterns normalize to lowercase underscore format. The rendered admin page contains the title, summary cards, registry table, and create form; the listing page did not print saved Trip.com or Travelpayouts script URLs. Playwright runtime validation used a temporary local admin account, captured desktop and mobile screenshots, created a temporary Trip.com iframe placement, updated it to disabled, verified the disabled row state, captured keyboard focus traversal through the form fields, found no browser console errors or page errors, then deleted the temporary placement and temporary users. Clean final screenshots after cleanup were saved at `/tmp/one-81-widget-placements-clean-desktop.png` and `/tmp/one-81-widget-placements-clean-mobile.png`; the smoke screenshots and focus evidence remain under `/tmp/one-81-widget-placements-*.png` and `/tmp/one81-focus-path.json`. `bookings-flights-core` deactivate/reactivate passed. `debug.log` tail review found no new related fatal, parse, warning, or notice entries. Codex review on PR #17 found one PHP 8.0 compatibility issue on the final head; the follow-up patch replaced the PHP 8.1-only `never` return type with `void`, reran syntax/page-render checks, and confirmed no `never` return types remain in the core plugin includes. WP-CLI still emits known bundled dependency deprecation warnings from WP-CLI and the Travelpayouts plugin.

P13.3 shortcode/block wrapper validation:

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-shortcode.php
php -l plugins/bookings-flights-core/includes/frontend/class-frontend-manager.php
php -l plugins/bookings-flights-core/includes/settings/class-settings-manager.php
node --check plugins/bookings-flights-core/assets/js/travelpayouts-widget-block.js
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval 'use BAF\Core\Services\Travelpayouts_Widget_Registry_Service; use BAF\Core\Settings\Settings_Manager; /* verify shortcode registration, block registration, active placement render, disabled/missing states, block render, SubID data attribute, disclosure/handoff output, and sensitive-term scan */'
node Playwright smoke against http://bookings-and-flights.local/one-82-registry-wrapper-smoke/
git diff --check
```

P13.3 local result on 2026-05-12: `BAF\Core\Frontend\Travelpayouts_Widget_Renderer`, `[baf_travelpayouts_widget]`, and the `baf/travelpayouts-widget` dynamic editor block render approved registry placements without storing raw embed code in editor content. WP-CLI confirmed the shortcode and block are registered, the Hotels partner placement renders with disclosure, handoff, no-script markup, and a normalized `data-baf-subid`, missing placements render a safe missing state, block rendering returns the same wrapper, and rendered output did not contain API token, authorization, bearer, secret, checkout, payment, or refund terms. Provider request consent was already enabled. A temporary public smoke page and disabled placement verified active block output plus disabled and missing shortcode states; both were deleted afterward. The Codex in-app Browser connected but rejected the tab as stale and then reported no active pane, so the final runtime pass used Playwright Chromium. Desktop `1440x900` and mobile `390x844` screenshots confirmed the wrapper rendered nonblank, preserved the Trip.com search controls without the oversized top header area, showed disclosure and visible handoff text, showed disabled/missing states, and had no horizontal overflow. Keyboard review confirmed focus reaches the Trip.com iframe and then the visible `Open hotel search` handoff link. Browser console captured only Chromium WebGL performance warnings and no page errors. Screenshots: `/tmp/one-82-widget-wrapper-desktop.png`, `/tmp/one-82-widget-wrapper-keyboard.png`, `/tmp/one-82-widget-wrapper-mobile-top.png`, and `/tmp/one-82-widget-wrapper-mobile.png`. Focus evidence: `/tmp/one-82-widget-wrapper-focus.json`. WP-CLI still emits known bundled dependency deprecation warnings from WP-CLI and the Travelpayouts plugin.

P13.3 Codex review follow-up on 2026-05-12: PR #18 review found that fixed White Label mount IDs could collide when multiple registry wrappers or the legacy White Label shortcode appeared on the same page. The registry wrapper and legacy shortcode now render unique placeholder IDs, claim the Travelpayouts-required `tpwl-search` and `tpwl-tickets` IDs only when no earlier White Label instance has already claimed them, show a visible unavailable fallback for additional instances, and remove unavailable placeholders from keyboard order. PHP syntax and `git diff --check` passed. Playwright Chromium rendered two temporary pages covering new-wrapper-first and legacy-shortcode-first DOM order at desktop and mobile widths; both pages reported no duplicate IDs, exactly one active fixed Search/Tickets ID pair, two visible conflict fallbacks, no horizontal overflow, no framework overlay, no page errors, active Search/Tickets keyboard reachability, and no focused unavailable placeholders. Final screenshots: `/tmp/one-82-white-label-new-first-desktop-final.png`, `/tmp/one-82-white-label-new-first-keyboard-final.png`, `/tmp/one-82-white-label-new-first-mobile-final.png`, `/tmp/one-82-white-label-legacy-first-desktop-final.png`, `/tmp/one-82-white-label-legacy-first-keyboard-final.png`, and `/tmp/one-82-white-label-legacy-first-mobile-final.png`. The temporary pages were deleted after validation. Provider-owned Travelpayouts scripts still emit known console warnings about React JSX source-map hints and duplicate GraphQL fragment names.

P13.3 context default follow-up on 2026-05-12: PR #18 review found that empty default `surface` and `slug` attributes from the shortcode and dynamic block could suppress the renderer's current page-context fallback, producing blank `data-baf-surface` values and less specific SubIDs. `Travelpayouts_Widget_Renderer::normalize_attributes()` now treats empty sanitized `surface` and `slug` values as missing and falls back to `current_surface()` and `current_slug()`. PHP syntax and `git diff --check` passed. A WP-CLI shortcode/block smoke check with explicit blank `surface` and `slug` attributes confirmed `data-baf-surface="site"` and `baf_site_hotels_page_hotels_partner_search` SubIDs in non-singular context with no blank surface values. A temporary published page rendered `[baf_travelpayouts_widget placement="hotels_partner_search"]`; Playwright confirmed `data-baf-surface="page"`, `baf_page_hotels_one_82_context_default_smoke_hotels_partner_search`, nonblank output, no framework overlay, no horizontal overflow, and no page errors. Screenshot: `/tmp/one-82-context-default-smoke.png`. The temporary page was deleted after validation.

P13.3 surface allowlist follow-up on 2026-05-12: PR #18 review found that active placements could render on surfaces outside their configured `public_surfaces` allowlist. `Travelpayouts_Widget_Renderer` now checks normalized surface values against `public_surfaces` before enqueuing or rendering provider output, returning a safe `surface-unavailable` state when the placement is not approved for the requested surface. PHP syntax and `git diff --check` passed. WP-CLI smoke checks confirmed `hotels_partner_search` renders the provider iframe on `surface="hotels"`, returns a safe unavailable state on `surface="flights"`, does not print the Trip.com iframe URL in the disallowed state, and treats blank surface defaults as non-empty before applying the allowlist. A temporary browser smoke page confirmed the allowed Hotels section rendered an iframe while the disallowed Flights section rendered the unavailable state with no iframe, no page errors, no framework overlay, and no horizontal overflow. Screenshot: `/tmp/one-82-surface-guard-smoke.png`. The temporary page was deleted after validation.

P13.3 dashboard-script fallback follow-up on 2026-05-12: PR #18 review found that dashboard-script widgets could show a false unavailable fallback because the renderer checked for a provider iframe once after 2.5 seconds and never recovered if the provider script initialized late. The renderer now creates the provider script under a wrapper-local watcher with `load`/`error` handlers, a mutation observer, and repeated polling before showing the fallback; if an iframe appears after the fallback state, the watcher clears the unavailable class and marks the placement loaded. PHP syntax and `git diff --check` passed. A temporary approved dashboard-script placement and page rendered through Playwright with the provider script intercepted to append its iframe after the fallback was already visible. Runtime state confirmed the wrapper first entered `is-unavailable`, then cleared `is-unavailable`, added `is-loaded`, hid the fallback, showed one iframe, had no horizontal overflow, and reported no page errors or warnings. Keyboard review confirmed tab focus reached the delayed iframe. Screenshots: `/tmp/one-82-dashboard-script-timeout-state.png` and `/tmp/one-82-dashboard-script-delayed-final.png`. The temporary page and placement were deleted after validation.

P13.4 SubID/disclosure/consent/frontend-state validation:

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-subid-service.php
git diff --check -- plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-subid-service.php plugins/bookings-flights-core/assets/css/frontend.css
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts --user=1 eval-file /dev/stdin
node Playwright smoke against http://bookings-and-flights.local/one-83-runtime-widget-qa/
```

P13.4 local result on 2026-05-12: `BAF\Core\Services\Travelpayouts_Widget_Subid_Service` centralizes SubID generation and URL mutation for approved Travelpayouts widget placements. WP-CLI smoke checks created temporary iframe, dashboard-script, disabled, and missing-configuration placements, then confirmed normalized SubIDs, `marker=partner.subid` preservation, disclosure output, no-script markup, loading `role="status"` markup, consent-disabled public messaging with no iframe/provider URL, disabled and missing-configuration states, and no `api_token`, authorization, bearer, secret, checkout, payment, or refund terms in rendered output. Temporary placements were deleted and consent was restored. The Codex in-app Browser path was attempted first, but the active pane was unavailable after a stale tab recovery attempt, so runtime validation used Playwright Chromium. Playwright created a temporary published page and verified desktop nonblank rendering, loading and loaded dashboard-script states, keyboard focus through the Trip.com iframe and handoff link, mobile rendering with no horizontal overflow, consent-disabled rendering with no provider scripts/iframes, and no relevant console errors/warnings. Screenshots: `/tmp/one-83-runtime-desktop.png`, `/tmp/one-83-runtime-loading.png`, `/tmp/one-83-runtime-loaded.png`, `/tmp/one-83-runtime-keyboard.png`, `/tmp/one-83-runtime-mobile.png`, and `/tmp/one-83-runtime-consent-disabled.png`. Focus evidence: `/tmp/one-83-runtime-focus.json`. The temporary page and placements were deleted after validation. `debug.log` tail review found only known WP-CLI/Travelpayouts PHP 8.5 deprecation noise.

P13.5 security/capability/nonce/exposure review validation:

```bash
rg -n "register_rest_route|admin_post_|wp_nonce|check_admin_referer|current_user_can|permission_callback|baf_travelpayouts_widget_registry|embed\\.|Travelpayouts_Widget" plugins/bookings-flights-core
rg -n "api[_-]?token|api[_-]?key|authorization|bearer|secret|private|notes|embed_value|hotel_widget_script_url|white_label_widget_id" plugins/bookings-flights-core/includes plugins/bookings-flights-core/assets/js/travelpayouts-widget-block.js .plan/architecture-baseline.md .plan/regression-watchlist.md
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts eval '/* anonymous registry private read/write/delete denial, admin sanitized save, public projection strip, cleanup */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts eval '/* REST inventory, AI anonymous rejection, public collection success, affiliate bridge config/postback exposure checks */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts eval '/* settings secret masking and frontend render exposure checks */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts --user=1 eval '/* missing placement admin nonce must fail */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts --user=1 eval '/* confirm nonce probe cleanup */'
```

P13.5 local result on 2026-05-12: The security review passed for the current Travelpayouts widget registry implementation. Source scans covered registry writes, admin-post actions, nonce checks, capability checks, REST permissions, frontend rendering, Settings API fields, secret-related terms, raw embed fields, and public documentation contracts. WP-CLI checks confirmed anonymous users cannot read private registry data or save/delete placements; administrator saves sanitize a malicious probe placement; public registry projections strip private embed URLs, references, admin notes, and saved secrets; actual core `baf/v1` endpoint routes use endpoint-specific permission callbacks; the AI itinerary route rejects anonymous POSTs; the public destination collection remains readable; affiliate bridge `/config` does not return secret-key names or values; affiliate bridge `/postback` rejects requests without the shared secret; saved Travelpayouts and AI secrets render as masked empty password fields; frontend shortcode output does not expose private notes, saved secret values, `api_token`, `api_key`, authorization, or bearer terms; and an admin-post save without a nonce exits with the expected expired-link failure without leaving the probe placement behind. Temporary probe placements and temporary option overrides were cleaned up. WP-CLI still emits known PHP 8.5 deprecation noise from bundled tooling.

P13.6 final Phase 13 review gate validation:

```bash
find plugins/bookings-flights-core -name '*.php' -print0 | xargs -0 -n1 php -l
node --check plugins/bookings-flights-core/assets/js/travelpayouts-widget-block.js
git diff --check
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts plugin status bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts eval '/* anonymous private registry read/save/delete denial */'
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts --user=1 eval '/* missing widget-placement nonce must fail before write */'
node Playwright runtime smoke against http://bookings-and-flights.local/one-85-phase-13-runtime-gate/
node REST permission/exposure smoke against /wp-json/baf/v1/destinations, /ai/itinerary, /config, and /postback
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts plugin deactivate bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts plugin activate bookings-flights-core
```

P13.6 local result on 2026-05-12: The final Phase 13 gate passed. Full PHP syntax checks for `plugins/bookings-flights-core`, block JavaScript syntax, `git diff --check`, plugin active checks, and `bookings-flights-core` deactivate/reactivate passed. Anonymous registry private reads, saves, and deletes returned forbidden errors. A missing-nonce admin save exited with the expected expired-link failure and did not create the `one85_nonce_probe` placement. REST smoke checks confirmed the public destinations collection returns `200`, anonymous valid AI itinerary POST returns a forbidden response without secrets, affiliate bridge `/config` returns no secret-key names or values, and `/postback` rejects missing-secret requests. The Codex in-app Browser connected but lost its active pane during navigation, so the required real runtime screenshots and keyboard review used Playwright Chromium. A temporary published page rendered `[baf_travelpayouts_widget placement="hotels_partner_search" surface="hotels" channel="one85" slug="phase_13_gate"]`; desktop and mobile screenshots confirmed configured output with disclosure, iframe, visible `Open hotel search` handoff, no horizontal overflow, no page errors, and runtime SubID `one85_hotels_hotels_phase_13_gate_hotels_partner_search` on the wrapper and provider URLs. Keyboard review confirmed focus reaches the Trip.com iframe and then the visible handoff link. A consent-disabled pass confirmed no provider iframe, provider script, handoff URL, raw provider URL, private note, or secret term appears in public output. Screenshots: `/tmp/one-85-phase-13-desktop.png`, `/tmp/one-85-phase-13-mobile.png`, `/tmp/one-85-phase-13-keyboard-iframe.png`, `/tmp/one-85-phase-13-keyboard-handoff.png`, and `/tmp/one-85-phase-13-consent-disabled.png`. Focus evidence: `/tmp/one-85-phase-13-focus.json`. The temporary page was deleted and provider request consent was restored. Runtime console capture showed only Chromium WebGL performance warnings from the provider context. WP-CLI still emits known PHP 8.5 deprecation noise from bundled tooling.

P14.1 homepage search shell validation:

```bash
php -l themes/bookings-and-flights-static/page-home.php
php -l themes/bookings-and-flights-static/header.php
php -l themes/bookings-and-flights-static/functions.php
git diff --check
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" --skip-plugins=travelpayouts eval '/* verify product nav fallback labels render */'
node Playwright smoke against http://bookings-and-flights.local/
```

P14.1 local result on 2026-05-12: PHP syntax checks for the changed theme templates/functions and `git diff --check` passed. WP-CLI confirmed the active header menu output includes Home, Flights, Hotels, Explore, Deals, Trip Planner, and Saved Trips even though the stored WordPress menu is still stale, and the stored-menu eligibility guard returns false unless top-level items match the expected product targets. The Codex in-app Browser was attempted first but no active pane was available, so runtime validation used Playwright Chromium. Desktop `1440x900`, tablet `900x1024`, and mobile `390x844` screenshots confirmed the local-media hero, flight/hotel search shell, disclosure, and navigation render without horizontal overflow. Keyboard review confirmed desktop tab order reaches header links, CTA, theme toggle, search fields, and search buttons; mobile menu keyboard review confirmed all seven product links and the Plan trip CTA are reachable and trapped in sequence. Search-submit smoke checks confirmed the flight and hotel forms capture entered values and land on the Flights and Hotels pages; the provider-owned Flight page normalizes the final visible URL after handoff. Frontend source contained no `api_token`, `api_key`, `access_token`, authorization, bearer, postback secret, or secret terms. Screenshots: `/tmp/one-86-desktop.png`, `/tmp/one-86-tablet.png`, `/tmp/one-86-mobile.png`, `/tmp/one-86-keyboard-desktop.png`, and `/tmp/one-86-keyboard-mobile-menu.png`. Runtime evidence: `/tmp/one-86-runtime-review.json`. Provider-owned Travelpayouts scripts on the downstream Flight page still emit known React source-map and duplicate GraphQL fragment warnings.

P14.1 Codex review patch result on 2026-05-12: WP-CLI confirmed the stale stored menu returns `has_product_core=false`, renders all seven fallback labels, keeps the Trip Planner homepage anchor, rejects external-host product URLs before a stored menu can bypass the fallback, and permits customized menu labels when top-level current-site targets match. The `/flights/` request guard allows provider-style flight URL parameters without triggering the destination CPT query-var 404. Playwright rerun confirmed desktop navigation labels, zero horizontal overflow, desktop tab order through the product links/CTA/theme/search controls, mobile menu visibility for all seven product links, mobile menu keyboard loop, and no console warnings/errors or failed requests. A flight-submit browser smoke confirmed the homepage form submits `origin`, `destination`, `depart_date`, and `return_date`, lands on the Flights page without a 404, and then lets the provider script normalize the URL into its own `flightSearch` state. Screenshots: `/tmp/one-86-final-review-desktop.png`, `/tmp/one-86-final-review-mobile-menu.png`, `/tmp/one-86-provider-flight-submit.png`. Runtime evidence: `/tmp/one-86-final-review-runtime.json`, `/tmp/one-86-provider-flight-submit.json`.

P14.2 Travelpayouts search placement validation:

```bash
php -l themes/bookings-and-flights-static/functions.php
php -l themes/bookings-and-flights-static/page-flights.php
php -l themes/bookings-and-flights-static/page-hotels.php
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
node --check themes/bookings-and-flights-static/assets/js/search-surface.js
git diff --check
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" plugin is-active bookings-flights-core
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" eval '/* theme/page/shortcode placement smoke checks */'
curl -sS "http://bookings-and-flights.local/flights/?origin=NYC&destination=TYO&depart_date=2026-06-10&return_date=2026-06-20&baf_surface=home"
curl -sS "http://bookings-and-flights.local/hotels/?travel_destination=Lisbon&check_in=2026-06-10&check_out=2026-06-20&guests=2&baf_surface=home"
node Playwright smoke against /flights/ and /hotels/ desktop/mobile surfaces
```

P14.2 local result on 2026-05-12: PHP syntax checks for the changed theme/core PHP files, JavaScript syntax check for `search-surface.js`, and `git diff --check` passed. File-size checks kept changed source files at or under 600 lines. WP-CLI confirmed the active theme, active `bookings-flights-core` plugin, published Flights/Hotels pages, registered wrapper shortcode, configured flight White Label state with handoff/marker/SubID output, configured hotel partner state with handoff output, and safe missing-placement state. `bookings-flights-core` deactivate/reactivate passed; WP-CLI emitted the known PHP 8.5 bundled tooling deprecation noise but returned the plugin to active. HTTP checks returned `200` for homepage-originated Flights and Hotels query URLs, and rendered source scans found no API token, API key, authorization, bearer, access-token, refresh-token, client-secret, secret, checkout, payment, or refund terms. The Codex in-app Browser was attempted first but had no active pane, so runtime validation used Playwright Chromium. Desktop `1440x960` and mobile `390x844` screenshots confirmed configured Flights and Hotels surfaces, readable fixed header, no horizontal overflow, visible handoff links, and no provider overlay. Keyboard review confirmed Flights reaches `Open flight search` after the header controls and Hotels reaches the Trip.com iframe followed by `Open hotel search`. Console capture showed only provider-owned Travelpayouts React source-map/duplicate-fragment warnings on Flights and Chromium WebGL performance warnings from provider context on Hotels; no page errors or failed requests were recorded. Screenshots: `/tmp/one87-flights-desktop-final.png`, `/tmp/one87-flights-mobile-final.png`, `/tmp/one87-hotels-desktop-final.png`, `/tmp/one87-hotels-mobile-final.png`, `/tmp/one87-flights-widget-desktop-final.png`, `/tmp/one87-flights-widget-mobile-final.png`, `/tmp/one87-hotels-widget-desktop-final.png`, and `/tmp/one87-hotels-widget-mobile-final.png`. Runtime evidence: `/tmp/one87-runtime-review-final.json`.

P14.2 Codex review accessibility follow-up on 2026-05-12: PR #23 review found that the loaded White Label placeholder nodes stayed `aria-hidden` after the provider script loaded and that script `load` was not enough proof of rendered provider content. The renderer now leaves loaded placeholders at `tabindex="-1"` to avoid duplicate sequential keyboard stops, removes `aria-hidden` only after content is rendered, and shows the unavailable fallback plus handoff when the script loads without rendering content. PHP syntax, `git diff --check`, and a Playwright runtime check for `#tpwl-search` unavailable/loaded state passed.

P14.3 homepage discovery module validation:

```bash
php -l themes/bookings-and-flights-static/page-home.php
git diff --check
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" post list --post_type=destination,route,travel_deal --post_status=publish --fields=ID,post_type,post_title --format=csv
curl -sS -L -o /tmp/one88-home.html -w '%{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/'
rg -n "\\$[0-9]|price|fare|from only|cheap(est)?|lowest|guarantee(d)?|real-time|\\blive\\b|exclusive deal|limited-time|api[_-]?key|api[_-]?token|authorization|bearer|access[_-]?token|refresh[_-]?token|client[_-]?secret|postback|secret" themes/bookings-and-flights-static/page-home.php themes/bookings-and-flights-static/assets/css/home.css /tmp/one88-home.html
node Playwright smoke against http://bookings-and-flights.local/
```

P14.3 local result on 2026-05-12: PHP syntax for `page-home.php` and `git diff --check` passed. File-size checks kept `page-home.php` at 297 lines, `home.css` at 516 lines, and `functions.php` at 600 lines. WP-CLI found no published `destination`, `route`, or `travel_deal` records, so the implementation uses clearly labeled editorial/static inspiration. HTTP homepage smoke returned `200`. Source scans found no broad price, fare, cheap, live, guarantee, exclusive-deal, limited-time, or secret-token matches. Playwright Chromium captured desktop, tablet, and mobile first-viewport and discovery screenshots; all three viewports rendered three route cards, three explore cards, three flexible-month cards, and three hotel cards; disclosure was visible; no horizontal overflow, console warnings, page errors, or failed requests were recorded. Extended keyboard review reached route, flexible-month, and hotel discovery cards. Screenshots: `/tmp/one88-home-desktop.png`, `/tmp/one88-home-tablet.png`, `/tmp/one88-home-mobile.png`, `/tmp/one88-home-discovery-desktop.png`, `/tmp/one88-home-discovery-tablet.png`, and `/tmp/one88-home-discovery-mobile.png`. Runtime evidence: `/tmp/one88-runtime-review.json`.

P14.4 price-alert and AI-planner entry validation:

```bash
php -l themes/bookings-and-flights-static/page-home.php
git diff --check
curl -sS -L -o /tmp/one89-home.html -w '%{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/'
rg -n "\\$[0-9]|from only|cheap(est)?|lowest|guarantee(d)?|real-time|\\blive\\b|exclusive deal|limited-time|auto[- ]?book|auto[- ]?publish|api[_-]?key|api[_-]?token|authorization|bearer|access[_-]?token|refresh[_-]?token|client[_-]?secret|postback|secret" themes/bookings-and-flights-static/page-home.php themes/bookings-and-flights-static/assets/css/home.css /tmp/one89-home.html
rg -n "<form|method=\"post\"|wp_nonce|check_admin_referer|admin-post|fetch\\(|XMLHttpRequest|navigator\\.sendBeacon" themes/bookings-and-flights-static/page-home.php themes/bookings-and-flights-static/assets/css/home.css
node Playwright smoke against http://bookings-and-flights.local/
```

P14.4 local result on 2026-05-12: PHP syntax for `page-home.php` and `git diff --check` passed. File-size checks kept `page-home.php` at 322 lines, `home.css` at 588 lines, and `functions.php` at 600 lines. HTTP homepage smoke returned `200`. Source scans found no unsupported live/fake deal/auto-book/auto-publish or secret-token matches. Write-path review found only the existing GET search forms and no new POST/fetch/beacon/nonce path. Playwright Chromium captured desktop and mobile retention screenshots, confirmed two retention cards render, verified the alert CTA opens `/flights/?travel_focus=price_alert`, verified the planner entry routes to `#trip-planner`, confirmed both cards are keyboard reachable, and recorded no horizontal overflow, console warnings, page errors, or failed requests. Screenshots: `/tmp/one89-retention-desktop.png` and `/tmp/one89-retention-mobile.png`. Runtime evidence: `/tmp/one89-runtime-review.json`.

P14.5 trust, disclosure, footer, and legal validation:

```bash
php -l themes/bookings-and-flights-static/page-home.php
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l themes/bookings-and-flights-static/footer.php
php -l themes/bookings-and-flights-static/page-legal.php
git diff --check
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes
curl -ksS "http://bookings-and-flights.local/"
curl -ksS "http://bookings-and-flights.local/flights/"
curl -ksS "http://bookings-and-flights.local/hotels/"
curl -ksS "http://bookings-and-flights.local/privacy-policy/"
curl -ksS "http://bookings-and-flights.local/terms-and-conditions/"
node Playwright trust/footer/legal smoke against homepage, Flights, Hotels, Privacy, and Terms pages
```

P14.5 local result on 2026-05-12: PHP syntax checks for changed theme PHP files and `git diff --check` passed. File-size checks kept `functions.php` at 600 lines, `home.css` at 588 lines, and all changed theme/CSS files under 600 lines. WP-CLI with the Local MySQL socket confirmed the active theme and published Home, Flights, Hotels, Contact, Privacy Policy, and Terms & Conditions pages; the plain WP-CLI command without the socket still hits the known Local database socket issue. HTTP smoke returned `200` for `/`, `/flights/`, `/hotels/`, `/contact/`, `/privacy-policy/`, and `/terms-and-conditions/`. Source checks confirmed visible Affiliate disclosure, Partner checkout, Support, Destination index, Terms, Privacy, Travelpayouts/provider handoff copy, and the corrected `/terms-and-conditions/` footer link. Source scans found no API token, API key, authorization, bearer, access token, refresh token, client secret, postback secret, broad direct-booking, checkout-with-Bookings, guaranteed, lowest-price, live-fare, or direct-booking claims. The Codex in-app Browser path was attempted first but had no active pane, so runtime validation used Playwright Chromium. Desktop/mobile screenshots confirmed homepage trust cards and footer compliance render without text overflow, Flights/Hotels render local support notes below the approved provider wrapper, and Privacy/Terms legal sections include affiliate/provider handoff language. Link smoke checks for Terms, Privacy, Support, and Destination index returned `200` or the expected homepage anchor. Keyboard review reached Support, Destination index, Terms, Privacy, `Open flight search`, and `Open hotel search`. Footer legal link contrast is `11.76:1`, and footer disclosure contrast is `5.28:1`. Provider-owned Flights warnings and browser WebGL provider-context warnings were classified as existing watchlist items because there were no page errors or failed requests. Screenshots: `/tmp/one90-home-trust-desktop.png`, `/tmp/one90-home-trust-mobile.png`, `/tmp/one90-footer-compliance-desktop.png`, `/tmp/one90-footer-compliance-mobile.png`, `/tmp/one90-flights-support-desktop.png`, `/tmp/one90-hotels-support-desktop.png`, `/tmp/one90-terms-disclosure-desktop.png`, and `/tmp/one90-privacy-affiliate-desktop.png`. Runtime evidence: `/tmp/one90-runtime-review-final.json`.

P14.6 responsive/accessibility/performance/screenshot pass validation:

```bash
php -l themes/bookings-and-flights-static/functions.php
php -l themes/bookings-and-flights-static/inc/travelpayouts-assets.php
git diff --check -- themes/bookings-and-flights-static/assets/css/header.css themes/bookings-and-flights-static/assets/css/footer.css themes/bookings-and-flights-static/assets/css/mobile-nav.css themes/bookings-and-flights-static/functions.php themes/bookings-and-flights-static/inc/travelpayouts-assets.php .plan/phased-implementation.md .plan/validation-baseline.md .plan/regression-watchlist.md .plan/known-issues.md .plan/phase-review-log.md
wc -l themes/bookings-and-flights-static/functions.php themes/bookings-and-flights-static/inc/travelpayouts-assets.php themes/bookings-and-flights-static/assets/css/header.css themes/bookings-and-flights-static/assets/css/footer.css themes/bookings-and-flights-static/assets/css/mobile-nav.css
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" theme get bookings-and-flights-static --field=name
curl -ksS -L -o /tmp/one91-home-precheck.html -w '%{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/'
curl -ksS -L -o /tmp/one91-flights-precheck.html -w '%{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/'
curl -ksS -L -o /tmp/one91-hotels-precheck.html -w '%{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/hotels/'
node Playwright responsive/accessibility/script-scope smoke against Home, Flights, and Hotels
```

P14.6 local result on 2026-05-12: PHP syntax checks for `functions.php` and `inc/travelpayouts-assets.php`, targeted `git diff --check`, file-size checks, Local-socket WP-CLI active theme check, and HTTP `200` smoke checks for Home, Flights, and Hotels passed. The Codex Browser path was unavailable in the current tool surface, so runtime validation used Playwright Chromium. Desktop `1440x1000`, tablet `900x1024`, and mobile `390x844` screenshots confirmed the homepage and public search surfaces render nonblank with no horizontal overflow, no framework overlays, no page errors, and no failed requests. Keyboard review confirmed desktop homepage tab order reaches header navigation, Plan trip, the theme toggle, search inputs, and search submit; mobile menu tab order reaches product links and Plan trip; Flights reaches `Open flight search`; and Hotels reaches the Trip.com iframe followed by `Open hotel search`. Reduced-motion review confirmed all mobile-menu links and the Plan trip CTA use `0s` transition delays while visible. Touch-target review confirmed the mobile header logo, theme toggle, menu toggle, footer legal links, and footer navigation links meet the reviewed target sizes. Script-scope review confirmed Home loads no official Travelpayouts plugin assets, no `search-surface.js`, and no White Label script; Flights loads `search-surface.js` and the approved White Label script without official plugin runtime assets; and Hotels keeps the approved widget/handoff output without `search-surface.js` or official plugin runtime assets. Provider-owned Flights React JSX-source warnings remain classified as known Travelpayouts White Label runtime noise because there were no page errors, failed requests, overlays, or broken handoff/keyboard behavior. Screenshots: `/tmp/one91-home-desktop.png`, `/tmp/one91-home-desktop-full.png`, `/tmp/one91-home-tablet.png`, `/tmp/one91-home-tablet-full.png`, `/tmp/one91-home-mobile.png`, `/tmp/one91-home-mobile-full.png`, `/tmp/one91-home-mobile-menu.png`, `/tmp/one91-home-mobile-reduced-motion-menu.png`, `/tmp/one91-flights-desktop.png`, `/tmp/one91-flights-desktop-widget.png`, `/tmp/one91-flights-mobile.png`, `/tmp/one91-flights-mobile-widget.png`, `/tmp/one91-hotels-desktop.png`, `/tmp/one91-hotels-desktop-widget.png`, `/tmp/one91-hotels-mobile.png`, and `/tmp/one91-hotels-mobile-widget.png`. Runtime evidence: `/tmp/one91-runtime-review.json`.

P14.6 Codex review follow-up on 2026-05-12: PR #27 review found that official Travelpayouts assets could be removed when an official shortcode rendered outside the queried singular post content. The asset guard now keeps official plugin assets on non-singular contexts by default, scans active widget instance content for official Travelpayouts shortcodes/blocks, supports a template-level `bookings_and_flights_has_official_travelpayouts_output` opt-in filter, and still prunes the official plugin runtime from the reviewed Home, Flights, and Hotels singular surfaces when no official shortcode/widget/filter output is present. PHP syntax, targeted `git diff --check`, Local-socket WP-CLI smoke checks for post-content detection, active-widget detection, non-singular preservation, and filter opt-in, and the Playwright responsive/script-scope pass passed after the patch.

P14.7 final Phase 14 review gate validation:

```bash
php -l themes/bookings-and-flights-static/page-home.php
php -l themes/bookings-and-flights-static/page-flights.php
php -l themes/bookings-and-flights-static/page-hotels.php
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l themes/bookings-and-flights-static/footer.php
php -l themes/bookings-and-flights-static/page-legal.php
php -l themes/bookings-and-flights-static/functions.php
php -l themes/bookings-and-flights-static/inc/travelpayouts-assets.php
php -d mysqli.default_socket="/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock" /opt/homebrew/bin/wp --path="/Users/djcavy/Local Sites/bookings-and-flights/app/public" post list --post_type=page --fields=ID,post_title,post_name,post_status --format=table --skip-plugins --skip-themes
curl -ksS -L -o /tmp/one92-home.html -w 'home %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/'
curl -ksS -L -o /tmp/one92-flights.html -w 'flights %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/'
curl -ksS -L -o /tmp/one92-hotels.html -w 'hotels %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/hotels/'
curl -ksS -L -o /tmp/one92-privacy.html -w 'privacy %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/privacy-policy/'
curl -ksS -L -o /tmp/one92-terms.html -w 'terms %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/terms-and-conditions/'
rg source checks for required disclosure/handoff/legal text
rg negative source checks for secrets, unsupported direct-checkout claims, fake live-fare/guarantee claims, auto-booking, and auto-publishing language
node Playwright final Phase 14 smoke against Home, Flights, and Hotels
git diff --check
```

P14.7 local result on 2026-05-12: The final Phase 14 review gate passed locally. PHP syntax checks for the homepage, Flights, Hotels, shared search placement, footer, legal template, functions file, and Travelpayouts asset helper passed. WP-CLI with the Local MySQL socket confirmed Home, Flights, Hotels, About, Contact, Services, Privacy Policy, and Terms & Conditions pages are published; the command still emits known WP-CLI/PHP 8.5 deprecation noise but exits successfully. HTTP smoke returned `200` for Home, Flights, Hotels, Privacy, and Terms. Source scans confirmed required affiliate disclosure, partner checkout, Support, Destination index, Travelpayouts/handoff, `Open flight search`, `Open hotel search`, Privacy, and Terms text, and found no API tokens, authorization/bearer terms, client/postback secrets, direct-checkout claims, guaranteed-lowest-price claims, live/real-time fare claims, auto-booking, or auto-publishing language. Playwright Chromium captured final desktop, tablet, and mobile screenshots for the homepage, mobile-menu screenshots, and desktop/mobile widget screenshots for Flights and Hotels. The runtime report found no findings: no horizontal overflow, page errors, failed requests, relevant console errors, blank pages, or script-scope regressions. Homepage counts confirmed discovery/entry/retention content and visible disclosure/trip-planner signals. Script-scope checks confirmed Home loads no official Travelpayouts plugin assets, no `search-surface.js`, and no White Label script; Flights loads the approved search-surface and White Label assets without official plugin runtime assets; and Hotels keeps the approved widget output without search-surface or official plugin runtime assets. Keyboard review confirmed Home reaches the search submit path, mobile menu reaches Plan trip, Flights reaches `Open flight search`, and Hotels reaches the Trip.com iframe and `Open hotel search`. Screenshots: `/tmp/one92-home-desktop.png`, `/tmp/one92-home-desktop-full.png`, `/tmp/one92-home-tablet.png`, `/tmp/one92-home-mobile.png`, `/tmp/one92-home-mobile-full.png`, `/tmp/one92-home-mobile-menu.png`, `/tmp/one92-flights-desktop.png`, `/tmp/one92-flights-widget-desktop.png`, `/tmp/one92-flights-mobile.png`, `/tmp/one92-flights-widget-mobile.png`, `/tmp/one92-hotels-desktop.png`, `/tmp/one92-hotels-widget-desktop.png`, `/tmp/one92-hotels-mobile.png`, and `/tmp/one92-hotels-widget-mobile.png`. Runtime evidence: `/tmp/one92-phase14-review.json`. Decision: Phase 14 is complete after this review gate merges, and Phase 15 may start from the completed homepage/search-surface baseline.

P15.1 flights landing page and search module validation:

```bash
php -l themes/bookings-and-flights-static/page-flights.php
node --check themes/bookings-and-flights-static/assets/js/search-surface.js
git diff --check -- themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/assets/css/search-surface.css themes/bookings-and-flights-static/assets/js/search-surface.js
wc -l themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/assets/css/search-surface.css themes/bookings-and-flights-static/assets/js/search-surface.js
curl -ksS -L -o /tmp/one93-flights-intent.html -w 'flights-intent %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/?origin=nyc&destination=lax&depart_date=2026-06-10&return_date=2026-06-17&travelers=2&cabin=business&travel_focus=deal_dates&baf_surface=home'
rg source checks for the intent module, provider-controlled labels, rendered details, secrets, and unsupported booking/fare claims
node Playwright runtime smoke against /flights/ desktop and mobile surfaces
```

P15.1 local result on 2026-05-12: PHP syntax for `page-flights.php`, JavaScript syntax for `search-surface.js`, targeted `git diff --check`, HTTP `200` smoke for a submitted Flights intent URL, source checks, and file-size checks passed. Changed source files remained below 600 lines: `page-flights.php` 218 lines, `search-surface.css` 446 lines, and `search-surface.js` 37 lines. Source checks confirmed the local Flight intent module, provider-controlled Direct-only/Nearby airports/Flexible-date labels, rendered `2 travelers, Business` detail, and the provider availability/support disclaimer. Source scans found no API token, API key, authorization, bearer, access token, refresh token, client secret, postback secret, guaranteed-lowest-price claim, real-time fare claim, direct-checkout claim, book-directly claim, or Bookings-and-Flights payment claim. The Codex in-app Browser path was attempted first but had no active pane, so runtime validation used Playwright Chromium. Desktop `1440x1000` and mobile `390x844` screenshots confirmed the Flights page renders nonblank, has no framework overlay, no horizontal overflow, visible provider-controlled option labels, and a visible `Open flight search` handoff. The update-intent interaction changed the local form to `SEA` to `MIA`, `3` travelers, `Premium economy`, rendered the updated intent details, and cleaned provider query parameters from the visible URL before the Travelpayouts widget could reinterpret them. Keyboard review reached the local origin, destination, traveler, cabin, `Update flight intent`, and `Open flight search` targets on desktop and mobile. No relevant app console errors or failed requests were recorded; the known Travelpayouts-owned JSX-source and duplicate GraphQL fragment warnings remain watchlist-only provider noise. Screenshots: `/tmp/one93-flights-desktop.png`, `/tmp/one93-flights-provider-desktop.png`, `/tmp/one93-flights-updated-intent.png`, `/tmp/one93-flights-mobile.png`, and `/tmp/one93-flights-provider-mobile.png`. Runtime evidence: `/tmp/one93-runtime-review.json`.

P15.2 route detail and origin page template validation:

```bash
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
php -l themes/bookings-and-flights-static/archive-route.php
php -l themes/bookings-and-flights-static/single-route.php
php -l themes/bookings-and-flights-static/template-parts/route-card.php
git diff --check -- plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php themes/bookings-and-flights-static/archive-route.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/template-parts/route-card.php themes/bookings-and-flights-static/assets/css/route-surface.css
wc -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php themes/bookings-and-flights-static/archive-route.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/template-parts/route-card.php themes/bookings-and-flights-static/assets/css/route-surface.css
curl -ksS -L -o /tmp/one94-routes.html -w 'routes %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/'
curl -ksS -L -o /tmp/one94-routes-origin.html -w 'origin %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/?route_origin=JFK'
curl -ksS -L -o /tmp/one94-route-single.html -w 'route-single %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/new-york-to-los-angeles-flights-2/'
rg source checks for route archive/detail copy, Travelpayouts disclosure, configured widget nodes, related-route links, secrets, unsupported fare/booking claims, auto-booking, and auto-publishing language
node Playwright route/archive/origin/single smoke against desktop and mobile surfaces
```

P15.2 local result on 2026-05-12: PHP syntax checks for changed core/theme PHP files, targeted `git diff --check`, file-size checks, HTTP `200` smoke for `/routes/`, `/routes/?route_origin=JFK`, and a temporary route detail URL, source scans, and Playwright runtime review passed. Temporary local route posts were created for runtime validation and deleted afterward. The first runtime pass found a real bug: `flights_white_label_search` was not approved for the new `route` surface, so the route detail page returned a safe `surface-unavailable` state instead of the White Label widget. The registry schema migrated to `1.0.1` and adds `route` to the starter flight placement's public surfaces once. A second browser pass found that Travelpayouts renders White Label content in shadow DOM, so the wrapper stayed visually in `is-loading`; the renderer now treats shadow-root content as loaded and hides the loading state after provider content appears. Desktop/mobile screenshots confirmed route archive, origin-filter archive, route detail, provider widget, disclosure, alert handoff, and related-route cards render without horizontal overflow, duplicate IDs, blank states, framework overlays, page errors, relevant console errors, or broken keyboard navigation. Keyboard review reached archive route links, route handoffs, `Browse routes`, provider `Open flight search`, and `Open alert handoff`. Screenshots: `/tmp/one94-routes-archive-desktop.png`, `/tmp/one94-routes-archive-mobile.png`, `/tmp/one94-routes-origin-desktop.png`, `/tmp/one94-route-single-desktop.png`, `/tmp/one94-route-single-provider-desktop.png`, `/tmp/one94-route-single-mobile.png`, and `/tmp/one94-route-single-provider-mobile.png`. Runtime evidence: `/tmp/one94-runtime-review.json`.

P15.3 low-price calendar, popular route, and route-map widget validation:

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-shortcode.php
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-starter-placements.php
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l themes/bookings-and-flights-static/template-parts/flight-discovery-widgets.php
php -l themes/bookings-and-flights-static/page-flights.php
php -l themes/bookings-and-flights-static/single-route.php
git diff --check -- plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-shortcode.php plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-starter-placements.php plugins/bookings-flights-core/assets/css/frontend.css themes/bookings-and-flights-static/template-parts/travel-search-placement.php themes/bookings-and-flights-static/template-parts/flight-discovery-widgets.php themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/assets/css/search-surface.css .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/regression-watchlist.md .plan/known-issues.md .plan/phase-review-log.md
wc -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-shortcode.php plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-starter-placements.php plugins/bookings-flights-core/assets/css/frontend.css themes/bookings-and-flights-static/template-parts/travel-search-placement.php themes/bookings-and-flights-static/template-parts/flight-discovery-widgets.php themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/assets/css/search-surface.css
wp option get baf_travelpayouts_widget_registry --format=json
wp eval shortcode smoke checks for [tp_calendar_widget], [tp_popular_routes_widget], [tp_map_widget], and [baf_travelpayouts_widget] wrapper output
curl -ksS -L -o /tmp/one95-flights.html -w 'flights %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/?origin=JFK&destination=LAX&depart_date=2026-06-10&return_date=2026-06-17&travelers=2&cabin=business&baf_surface=home'
curl -ksS -L -o /tmp/one95-flights-missing.html -w 'flights-missing %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/'
curl -ksS -L -o /tmp/one95-route.html -w 'route %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/one95-test-new-york-to-los-angeles-flights/'
node Playwright runtime smoke against Flights, route, mobile, desktop, and missing-code surfaces
```

P15.3 local result on 2026-05-12: PHP syntax checks for changed core/theme PHP files, targeted `git diff --check`, file-size checks, WP-CLI registry migration and shortcode smoke checks, HTTP `200` smoke for configured Flights, missing-code Flights, and a temporary route detail URL, source scans, and Playwright Chromium runtime review passed. Temporary local route post `297` was used for route screenshots and deleted afterward. The registry schema migrated to `1.0.2` and seeds `flights_low_price_calendar`, `flights_popular_routes`, and `flights_route_map` starter placements for the `flights` and `route` public surfaces. Runtime screenshots confirmed the official Travelpayouts calendar, popular-routes, and map widgets render inside reserved discovery cards on desktop and mobile without horizontal overflow, duplicate IDs, blank wrapper states, framework overlays, app page errors, or visible fallback states. Missing origin/destination context renders three visible missing-code cards instead of broken widgets. Keyboard review reached public navigation, local intent controls, `Update flight intent`, `Open flight search`, route handoffs, and provider-owned widget focus points without trapping the page. Bugs fixed during validation: the official-widget loaded detector first treated placeholder markup as loaded, then missed useful provider shadow DOM content, and the map iframe rendered blank when initialized offscreen; the renderer now requires real provider content, recognizes shadow roots, and refreshes map iframes once when the wrapper becomes visible. Provider-owned Aviasales analytics `400` pixels, a provider image `404`, JSX-source/duplicate GraphQL warnings, and WebGL/map-image warnings remain watchlist-only because the widgets rendered and no app-owned source, secret, layout, or handoff failure was found. Screenshots: `/tmp/one95-flights-discovery-desktop.png`, `/tmp/one95-flights-discovery-mobile.png`, `/tmp/one95-flights-discovery-missing.png`, `/tmp/one95-route-discovery-desktop.png`, and `/tmp/one95-route-discovery-mobile.png`. Runtime evidence: `/tmp/one95-runtime-review.json`.

P15.4 White Label result flow and header continuity validation:

```bash
php -l themes/bookings-and-flights-static/template-parts/white-label-continuity.php
php -l themes/bookings-and-flights-static/page-flights.php
php -l themes/bookings-and-flights-static/single-route.php
git diff --check -- themes/bookings-and-flights-static/template-parts/white-label-continuity.php themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/assets/css/search-surface.css themes/bookings-and-flights-static/assets/css/route-surface.css themes/bookings-and-flights-static/assets/css/white-label-continuity.css .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/regression-watchlist.md .plan/known-issues.md .plan/phase-review-log.md
wc -l themes/bookings-and-flights-static/template-parts/white-label-continuity.php themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/single-route.php themes/bookings-and-flights-static/assets/css/search-surface.css themes/bookings-and-flights-static/assets/css/route-surface.css themes/bookings-and-flights-static/assets/css/white-label-continuity.css
curl -ksS -L -o /tmp/one96-flights.html 'http://bookings-and-flights.local/flights/?origin=JFK&destination=LAX&depart_date=2026-06-10&return_date=2026-06-17&travelers=2&cabin=business&baf_surface=route_single'
curl -ksS -L -o /tmp/one96-route.html 'http://bookings-and-flights.local/routes/one-96-test-new-york-to-los-angeles-flights/'
rg source checks for White Label continuity copy, route-back links, affiliate/provider handoff language, secrets, direct-checkout claims, guaranteed-fare claims, auto-booking, and WordPress-owned payment claims
node Playwright runtime smoke against Home, Flights, route detail, desktop/mobile provider sections, and keyboard navigation
```

P15.4 local result on 2026-05-12: PHP syntax checks for changed theme PHP files, targeted `git diff --check`, file-size checks, HTTP `200` smoke for Home, Flights, Routes, and temporary route detail URLs, source scans, and Playwright Chromium runtime review passed. Changed source files remained at or below the 600-line ceiling: `white-label-continuity.php` 59 lines, `page-flights.php` 276 lines, `single-route.php` 334 lines, `search-surface.css` 531 lines, `route-surface.css` 350 lines, and `white-label-continuity.css` 84 lines. Runtime review confirmed the new continuity bands render before the approved `flights_white_label_search` module, the White Label providers reach `is-loaded` on Flights and route detail pages, Home/Flights/route headers share the same logo and primary navigation labels, desktop/mobile layouts have no horizontal overflow, duplicate IDs, app page errors, or secret terms, and keyboard navigation reaches continuity links plus the visible `Open flight search` handoff. The first visual pass found that jump/anchor navigation to the provider section could place the continuity band under the fixed header; provider-section scroll margin was added and the browser pass was rerun. A self-review split continuity styles into `white-label-continuity.css` instead of leaving `search-surface.css` at the 600-line ceiling and patched the reusable template's optional-title ARIA fallback. Temporary local route posts were used for route screenshots and deleted after validation. Provider-owned Sentry/API aborts and React/GraphQL console warnings remain watchlist-only because the modules rendered and no app-owned route, layout, handoff, secret, or keyboard failure was found. Screenshots: `/tmp/one96-home-desktop.png`, `/tmp/one96-home-mobile.png`, `/tmp/one96-flights-desktop.png`, `/tmp/one96-flights-mobile.png`, `/tmp/one96-flights-provider-desktop.png`, `/tmp/one96-flights-provider-mobile.png`, `/tmp/one96-route-desktop.png`, `/tmp/one96-route-mobile.png`, `/tmp/one96-route-provider-desktop.png`, and `/tmp/one96-route-provider-mobile.png`. Runtime evidence: `/tmp/one96-runtime-review.json`.

P15.5 price-alert intent validation:

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-flight-alert-intent-handler.php
php -l plugins/bookings-flights-core/includes/frontend/class-flight-alert-signup-shortcode.php
php -l plugins/bookings-flights-core/includes/frontend/class-frontend-manager.php
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l themes/bookings-and-flights-static/page-flights.php
php -l themes/bookings-and-flights-static/single-route.php
git diff --check -- plugins/bookings-flights-core/includes/frontend/class-flight-alert-intent-handler.php plugins/bookings-flights-core/includes/frontend/class-flight-alert-signup-shortcode.php plugins/bookings-flights-core/includes/frontend/class-frontend-manager.php plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php plugins/bookings-flights-core/assets/css/frontend.css themes/bookings-and-flights-static/page-flights.php themes/bookings-and-flights-static/single-route.php .plan/phased-implementation.md .plan/architecture-baseline.md .plan/validation-baseline.md .plan/regression-watchlist.md .plan/known-issues.md .plan/phase-review-log.md
wp eval checks for `baf_flight_alert_signup`, `travel_alert`, and registered alert meta
curl HTTP/source smoke for `/flights/?origin=NYC&destination=LAX&travel_focus=price_alert`
curl `admin-post.php` happy-path, lowercase route-code, immediate duplicate/rate-limit, missing-nonce, invalid-email, and missing-consent probes
wp plugin deactivate bookings-flights-core && wp plugin activate bookings-flights-core
node Playwright runtime smoke against Flights and route alert forms, saved state, desktop/mobile screenshots, and keyboard navigation
```

P15.5 local result on 2026-05-13: PHP syntax checks, targeted `git diff --check`, file-size checks, shortcode/CPT/meta registration checks, HTTP/source smoke, admin-post alert probes, plugin deactivate/reactivate, and Playwright Chromium runtime review passed. Changed source files remained below 600 lines: `class-flight-alert-intent-handler.php` 255, `class-flight-alert-signup-shortcode.php` 196, `frontend.css` 587, `page-flights.php` 296, and `single-route.php` 349. The happy-path admin-post probe created a private `travel_alert` with route `NYC-LAX`, weekly frequency, consented email, source surface, dates, travelers, cabin, and requested status. Lowercase route-code submission normalized to stored route `NYC-LAX`. Missing nonce returned `403`; invalid email and missing consent redirected to explicit safe states without creating records; an immediate duplicate submission for the same client/email/route redirected to `rate_limited` without creating a second record. Codex PR review follow-up also verified the shortcode rebuilds current form URLs from the request path before `home_url()` so subdirectory installs do not duplicate the home path. Runtime review captured Flights and route desktop/mobile screenshots, verified the saved alert state after real form submission, confirmed consent/provider-limit copy, found no horizontal overflow, duplicate IDs, framework overlays, app source-secret terms, or app-owned page errors, and confirmed keyboard navigation reaches email, origin, destination, frequency, consent, and `Save alert intent` on Flights and route pages. The Codex in-app Browser plugin was attempted first, but its expected tab API was unavailable in this session, so Playwright Chromium was used. Existing search-surface history cleanup removes the visible success query string after the server-rendered saved message loads. Provider-owned Travelpayouts Sentry/analytics, duplicate GraphQL, JSX-source, and WebGL/map warnings remain watchlist-only because the alert workflow and page layout passed. Screenshots: `/tmp/one97-final-flights-desktop.png`, `/tmp/one97-final-flights-alert-desktop.png`, `/tmp/one97-final-flights-success-desktop.png`, `/tmp/one97-final-flights-mobile.png`, `/tmp/one97-final-route-desktop.png`, `/tmp/one97-final-route-alert-desktop.png`, and `/tmp/one97-final-route-mobile.png`. Codex review-fix screenshots: `/tmp/one97-review-fix-flights-success.png`, `/tmp/one97-review-fix-flights-rate-limited.png`, `/tmp/one97-review-fix-flights-mobile.png`, and `/tmp/one97-review-fix-route-alert.png`. Runtime evidence: `/tmp/one97-runtime-review.json` and `/tmp/one97-review-fix-runtime.json`.

P15.6 SEO metadata, source review, and route indexing validation:

```bash
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
php -l themes/bookings-and-flights-static/functions.php
php -l themes/bookings-and-flights-static/archive-route.php
php -l themes/bookings-and-flights-static/single-route.php
php -l themes/bookings-and-flights-static/template-parts/route-card.php
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
git diff --check
curl source smoke for `/flights/`, `/flights/?origin=nyc&destination=lax&depart_date=2026-08-01`, `/routes/`, `/routes/?route_origin=nyc`, and a temporary route detail URL
curl public REST smoke for `/wp-json/wp/v2/route?per_page=1`
node Playwright runtime smoke against route origin archive, route detail, Flights query metadata, desktop/mobile screenshots, and keyboard navigation
```

P15.6 local result on 2026-05-13: PHP syntax checks, targeted `git diff --check`, file-size checks, plugin deactivate/reactivate, HTTP/source smoke, public REST/content smoke, and Playwright Chromium runtime review passed. Changed files remained below the 600-line ceiling: `functions.php` 600, `seo-metadata.php` 193, `archive-route.php` 125, `single-route.php` 347, `route-card.php` 73, and `class-post-type-registrar.php` 263. Source checks confirmed `/routes/` and `/routes/?route_origin=NYC` have route SEO titles, descriptions, and archive canonical URLs; route detail pages use excerpt-backed descriptions and core singular canonical output; `/flights/` is canonical and indexable; and `/flights/?origin=NYC&destination=LAX&depart_date=2026-08-01` renders `noindex, follow` with canonical `/flights/`. Source scans found no `Deprecated`, `Warning`, `Fatal`, API key, secret, unsupported price, fake scarcity, checkout, payment, or booking-owner leakage in app-owned output. Public REST route smoke returned a bounded route collection. Runtime review used temporary route posts `309` and `310`, captured `/tmp/one98-routes-origin-desktop.png`, `/tmp/one98-route-single-keyboard.png`, `/tmp/one98-flights-query-noindex.png`, and `/tmp/one98-routes-origin-mobile.png`, saved evidence to `/tmp/one98-runtime-report.json`, then deleted the temporary route posts and confirmed `remaining=0`. Keyboard review tabbed from the origin archive through primary navigation and route actions to the `ONE-98 Test Route NYC to LAX` link, then pressed Enter into the route detail page. The Codex in-app Browser plugin was attempted first, but no active browser pane was available, so Playwright Chromium was used. Provider-owned Travelpayouts console warnings and aborted analytics/image requests remain watchlist-only because app-owned console and failed-request buckets were empty.

P15.7 final Flights Experience review validation:

```bash
curl -ksS -L -o /tmp/one99-flights.html -w 'flights %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/'
curl -ksS -L -o /tmp/one99-flights-query.html -w 'flights_query %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/flights/?origin=nyc&destination=lax&depart_date=2026-08-01'
curl -ksS -L -o /tmp/one99-routes.html -w 'routes %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/'
curl -ksS -L -o /tmp/one99-routes-origin.html -w 'routes_origin %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/?route_origin=nyc'
curl -ksS -L -o /tmp/one99-route-single.html -w 'route_single %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/routes/one-99-gate-route-nyc-to-lax/'
curl -ksS -L -o /tmp/one99-rest-route.json -w 'rest_route %{http_code}\n' 'http://bookings-and-flights.local/wp-json/wp/v2/route?per_page=1'
rg source checks for required titles, descriptions, canonical URLs, noindex behavior, widget placement output, route context, and negative secret/error/unsupported-claim terms
curl -ksS -L -o /tmp/one99-alert-missing-nonce.html -w 'alert_missing_nonce %{http_code}\n' -X POST 'http://bookings-and-flights.local/wp-admin/admin-post.php' --data 'action=baf_save_flight_alert&baf_alert_email=phase15-gate@example.test&baf_alert_origin=NYC&baf_alert_destination=LAX&baf_alert_frequency=weekly&baf_alert_consent=1'
wp eval alert email creation check for `phase15-gate@example.test`
node Playwright runtime smoke against Flights, White Label widget section, route detail, mobile origin archive, and keyboard route navigation
wp post delete temporary route post and confirm `remaining=0`
git diff --check
```

P15.7 local result on 2026-05-13: The final Phase 15 review gate passed locally. HTTP smoke returned `200` for `/flights/`, `/flights/?origin=nyc&destination=lax&depart_date=2026-08-01`, `/routes/`, `/routes/?route_origin=nyc`, a temporary route detail URL, and `/wp-json/wp/v2/route?per_page=1`. Source checks confirmed Flights query URLs render `noindex, follow` with canonical `/flights/`, route archives canonicalize `/routes/?route_origin=NYC`, route detail pages keep excerpt-backed descriptions, and the approved `flights_white_label_search` placement remains configured. Source scans found no app-owned `Deprecated`, `Warning`, `Fatal`, API key, token, authorization, bearer, postback secret, password, fake scarcity, direct-checkout, auto-booking, or stored-inventory leakage. Missing alert nonce returned `403`, and the follow-up alert email check confirmed no `travel_alert` was created. Playwright Chromium captured `/tmp/one99-flights-desktop.png`, `/tmp/one99-flights-widget-desktop.png`, `/tmp/one99-route-detail-desktop.png`, `/tmp/one99-routes-origin-mobile.png`, and `/tmp/one99-keyboard-route-link.png`, with runtime evidence in `/tmp/one99-runtime-report.json`. The runtime pass found no app-owned console errors, app-owned failed requests, relevant failed requests, framework overlays, horizontal overflow, duplicate IDs, or blank pages. Keyboard navigation tabbed from the origin archive to the temporary `ONE-99 Gate Route NYC to LAX` route card link and Enter opened the route detail page. The Codex in-app Browser plugin was attempted first, but its runtime did not expose a usable tab API in this session, so Playwright Chromium was used for the required screenshots and keyboard review. Temporary route post `312` was deleted after validation and confirmed at `remaining=0`. Provider-owned Travelpayouts console warnings and external request noise remain watchlist-only because app-owned checks passed and widget/handoff output stayed usable.

P16.1 Hotels landing page and search/widget module validation:

```bash
php -l themes/bookings-and-flights-static/page-hotels.php
php -l themes/bookings-and-flights-static/functions.php
node --check themes/bookings-and-flights-static/assets/js/search-surface.js
wc -l themes/bookings-and-flights-static/functions.php themes/bookings-and-flights-static/page-hotels.php themes/bookings-and-flights-static/assets/css/hotels-surface.css themes/bookings-and-flights-static/assets/js/search-surface.js
curl -ksS -L -o /tmp/one100-hotels.html -w 'hotels %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/hotels/'
curl -ksS -L -o /tmp/one100-hotels-intent.html -w 'hotels_intent %{http_code} %{url_effective}\n' 'http://bookings-and-flights.local/hotels/?travel_destination=Miami%20Beach&check_in=2026-08-10&check_out=2026-08-14&guests=3&rooms=2&stay_focus=near_transit&baf_surface=home'
wp option get baf_travelpayouts_widget_registry --format=json
rg source checks for hotel-intent output, `hotels_partner_search`, `hotels-surface.css`, `search-surface.js`, intent summaries, handoff/disclosure copy, secrets, PHP warnings, direct-checkout claims, fake rate claims, auto-booking, and WordPress-owned inventory claims
node Playwright runtime smoke against Hotels desktop, widget section, mobile, updated intent interaction, clean URL, console/request health, and keyboard navigation to `Open hotel search`
git diff --check
```

P16.1 local result on 2026-05-13: PHP syntax passed for `page-hotels.php` and `functions.php`, JavaScript syntax passed for `search-surface.js`, `git diff --check` passed, and changed source files stayed at or below the 600-line guideline: `functions.php` 600, `page-hotels.php` 222, `hotels-surface.css` 223, and `search-surface.js` 43. HTTP smoke returned `200` for `/hotels/`, `/hotels/?travel_destination=Miami%20Beach&check_in=2026-08-10&check_out=2026-08-14&guests=3&rooms=2&stay_focus=near_transit&baf_surface=home`, and a Flights query regression URL. Registry smoke confirmed `hotels_partner_search` is active, approved for `home` and `hotels`, and rendering in `iframe` mode. Source checks confirmed the Hotels intent module, hotel-specific CSS, shared cleanup script, approved placement key, `Open live hotel search`, sanitized intent summary, and provider-owned live-search/booking copy; negative scans found no app-owned `Deprecated`, `Warning`, `Fatal`, API key, token, authorization, bearer, postback secret, password, guaranteed-lowest-rate, real-time fare, direct-checkout, auto-booking, stored-inventory, WordPress-owned hotel inventory, or secret leakage. Runtime review captured `/tmp/one100-hotels-desktop.png`, `/tmp/one100-hotels-widget-desktop.png`, `/tmp/one100-hotels-mobile.png`, `/tmp/one100-hotels-updated-intent.png`, and `/tmp/one100-hotels-keyboard-handoff.png`, with evidence in `/tmp/one100-runtime-report.json`. Playwright found no app-owned console errors, app-owned failed requests, relevant failed requests, framework overlays, horizontal overflow, duplicate IDs, blank page, missing hotel CSS, or missing handoff. The interaction pass updated intent to Chicago Loop, 2026-09-04 to 2026-09-07, four guests, two rooms, Work trip, and confirmed the visible URL was cleaned back to `/hotels/`. Keyboard review reached the local destination/date/guest/room/focus controls, `Update hotel intent`, the provider iframe, and `Open hotel search`. The first browser pass found the shared URL cleanup script was only enqueued on Flights; the enqueue condition and cleanup key list were patched, then the browser pass was rerun successfully. The Codex in-app Browser plugin was attempted first earlier in this session, but its runtime did not expose a usable tab API, so Playwright Chromium was used.

P16.2 City hotel guide templates and editorial modules validation:

```bash
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
php -l themes/bookings-and-flights-static/single-destination.php
php -l themes/bookings-and-flights-static/archive-destination.php
php -l themes/bookings-and-flights-static/page-hotels.php
wc -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php themes/bookings-and-flights-static/inc/seo-metadata.php themes/bookings-and-flights-static/single-destination.php themes/bookings-and-flights-static/archive-destination.php themes/bookings-and-flights-static/page-hotels.php themes/bookings-and-flights-static/assets/css/hotel-guide.css themes/bookings-and-flights-static/assets/css/hotels-surface.css themes/bookings-and-flights-static/functions.php
wp eval registered destination hotel-guide meta checks for `baf_hotel_guide_summary`, `baf_hotel_neighborhoods`, `baf_hotel_best_for`, `baf_hotel_family_notes`, `baf_hotel_luxury_notes`, `baf_hotel_budget_notes`, and `baf_hotel_landmark_notes`
wp plugin deactivate bookings-flights-core && wp plugin activate bookings-flights-core
curl HTTP/source smoke for a temporary destination guide, `/destinations/`, `/hotels/`, and `/hotels/?travel_destination=Lisbon&baf_surface=destination_archive`
rg source checks for city guide SEO metadata, guide modules, related routes, `hotels_partner_search`, `destination_single` SubID context, `hotel-guide.css`, disclosure, and negative secret/direct-checkout/unsupported-inventory terms
node Playwright runtime smoke against destination guide desktop/mobile, destination guide modules, destination archive desktop/mobile, Hotels guide teaser, provider sections, console/request health, duplicate-ID and overflow checks, and keyboard navigation
git diff --check
```

P16.2 local result on 2026-05-13: PHP syntax, `git diff --check`, plugin deactivate/reactivate, registered meta checks, HTTP/source smoke, and Playwright Chromium runtime review passed. Changed files stayed under the 600-line guideline: `functions.php` 600, `class-post-type-registrar.php` 270, `seo-metadata.php` 231, `single-destination.php` 309, `archive-destination.php` 113, `page-hotels.php` 309, `hotel-guide.css` 395, and `hotels-surface.css` 223. Temporary validation content used destination post `313` and route post `314`, then removed both posts and confirmed `temporary_posts_remaining=0`. Source checks confirmed destination SEO title/description/canonical output, city guide archive canonical output, city hotel guide modules, related route link output, Hotels page guide cards, `hotel-guide.css`, the approved `hotels_partner_search` provider output, and `destination_single_hotels_hotels_destination_313_hotels_partner_search` SubID context. Negative scans found no app-owned `Deprecated`, `Warning`, `Fatal`, API key, token, authorization, bearer, postback secret, password, guaranteed-lowest-rate, real-time-rate, direct-checkout, auto-booking, unavailable-placement state, or WordPress-owned hotel inventory claims. One safe existing Hotels sentence says WordPress does not store or rank live room inventory. Runtime screenshots: `/tmp/one101-destination-single-desktop.png`, `/tmp/one101-destination-modules-desktop.png`, `/tmp/one101-destination-provider-desktop.png`, `/tmp/one101-destination-single-mobile.png`, `/tmp/one101-destination-archive-desktop.png`, `/tmp/one101-destination-archive-mobile.png`, `/tmp/one101-hotels-city-guides-desktop.png`, `/tmp/one101-hotels-provider-desktop.png`, `/tmp/one101-keyboard-city-guide-link.png`, and `/tmp/one101-keyboard-hotel-handoff.png`. Runtime evidence: `/tmp/one101-runtime-report.json`. The runtime report found no app-owned console errors, app-owned failed requests, framework overlays, horizontal overflow, duplicate IDs, blank pages, missing hotel-guide CSS, unsupported live-filter/direct-booking claims, or unavailable placement states. Keyboard review reached the temporary city guide link on `/destinations/`, pressing Enter opened the city guide, and the city guide tab path reached `Open hotel search`. The Codex in-app Browser plugin was attempted first, but its runtime exposed `browser.nameSession` as a non-function, so Playwright Chromium was used. A first browser pass found the destination guide had requested the unapproved `destination` placement surface and rendered the shared unavailable state; the template now renders the existing approved hotel placement with `surface="hotels"` while retaining `destination_single` channel/SubID context, and the runtime pass was rerun successfully.

P16.3 Hotel widget/table/map placements and SubIDs validation:

```bash
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-starter-placements.php
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php
php -l plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php
php -l plugins/bookings-flights-core/includes/admin/class-widget-placement-form.php
php -l plugins/bookings-flights-core/includes/admin/class-widget-placements-page.php
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l themes/bookings-and-flights-static/template-parts/hotel-discovery-placements.php
php -l themes/bookings-and-flights-static/page-hotels.php
php -l themes/bookings-and-flights-static/single-destination.php
wc -l changed PHP/CSS files
wp option get baf_travelpayouts_widget_registry --format=json using the Local `qRHZasMmV` MySQL socket
wp plugin status bookings-flights-core
curl HTTP/source smoke for `/hotels/` and a temporary destination guide
rg source checks for `hotels_map_handoff`, `hotels_listing_handoff`, placement classes, matching SubIDs, disclosure, `frontend.css`, `hotel-guide.css`, and negative secret/error/unsupported-inventory terms
node Playwright runtime smoke against Hotels desktop/mobile, destination guide desktop/mobile, companion placement screenshots, console/request health, duplicate-ID and overflow checks, card-height checks, and keyboard navigation to `Open hotel map` and `Open hotel listings`
node Playwright admin smoke for `hotels_listing_handoff` widget-family editing after Codex PR review
wp post delete temporary destination guide and confirm `temporary_posts_remaining=0`
git diff --check
```

P16.3 local result on 2026-05-13: PHP syntax, file-size checks, `git diff --check`, registry migration smoke, plugin status check, HTTP/source smoke, and Playwright Chromium runtime review passed. Changed files stayed under the 600-line guideline: `class-travelpayouts-widget-starter-placements.php` 318, `class-travelpayouts-widget-registry-service.php` 492, `class-travelpayouts-widget-renderer.php` 531, `frontend.css` 587, `hotel-discovery-placements.php` 82, `hotel-guide.css` 441, `page-hotels.php` 324, `single-destination.php` 328, and `travel-search-placement.php` 122. Registry smoke confirmed schema `1.0.3` with active `hotels_map_handoff` and `hotels_listing_handoff` placements using `handoff_link` mode and the existing Trip.com partner URL. Source checks confirmed the reusable hotel partner tools section on `/hotels/` and a temporary destination guide, `data-baf-subid` values for map/listing placements, matching `subid=` query values in handoff URLs, `baf-travelpayouts-widget--hotel_map`, `baf-travelpayouts-widget--hotel_listing`, and scoped `baf-travelpayouts-widget--placement-hotels_partner_search` classes. Negative scans found no app-owned `Deprecated`, `Warning`, `Fatal`, API key, token, authorization, bearer, postback secret, password, guaranteed-lowest-rate, real-time-rate, direct-checkout, auto-booking, unavailable-placement state, Booking.com White Label promise, or WordPress-owned hotel inventory claim; the only `token` match was the existing safe design-token stylesheet name. Runtime screenshots: `/tmp/one102-hotels-desktop.png`, `/tmp/one102-hotels-companions-desktop.png`, `/tmp/one102-hotels-mobile.png`, `/tmp/one102-hotels-companions-mobile.png`, `/tmp/one102-destination-desktop.png`, `/tmp/one102-destination-companions-desktop.png`, `/tmp/one102-destination-mobile.png`, `/tmp/one102-destination-companions-mobile.png`, `/tmp/one102-keyboard-map-link.png`, and `/tmp/one102-keyboard-listings-link.png`. Runtime evidence: `/tmp/one102-runtime-report.json`. The runtime report found no app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, blank pages, missing companion sections, unavailable-placement states, unsupported hotel claims, mismatched SubIDs, or oversized handoff cards. Keyboard review tabbed to `Open hotel map` at tab stop 29 and `Open hotel listings` at tab stop 30. Temporary destination post `315` was removed after validation and confirmed at `temporary_posts_remaining=0`. WP-CLI required the Local `qRHZasMmV` MySQL socket; the standard `localhost` socket path failed outside Local's runtime wrapper. WP-CLI still emits known PHP 8.5 `Colors.php` deprecation noise unless display errors are disabled. The Codex in-app Browser plugin was not exposed as a usable browser automation target in this turn, so Playwright Chromium was used for screenshots and keyboard review. Codex PR review found that the admin placement editor family dropdowns were missing the new `hotel_listing` family, which could silently rewrite `hotels_listing_handoff` on save. The follow-up patch added `hotel_listing` to both admin family choice lists, reran PHP syntax for those admin files, reran `git diff --check`, and confirmed the admin/UI family choices match the registry allowlist. A Playwright admin smoke created and removed temporary administrator user `one102-admin-review`, opened the `hotels_listing_handoff` edit form, confirmed the selected widget family remained `hotel_listing`, confirmed the option exists, and verified keyboard focus moves from Context to Widget family with Tab. Admin screenshots: `/tmp/one102-admin-hotel-listing-family.png` and `/tmp/one102-admin-hotel-listing-family-focus.png`. The only admin request failures were provider-owned/WordPress background aborts (`travelpayouts/widget/index`, `admin-ajax.php`, and Snowplow) with no console errors.

P16.4 Handoff language, disclosure, and unsupported-filter guardrails validation:

```bash
php -l themes/bookings-and-flights-static/template-parts/travel-search-placement.php
php -l themes/bookings-and-flights-static/template-parts/hotel-discovery-placements.php
php -l themes/bookings-and-flights-static/page-hotels.php
php -l themes/bookings-and-flights-static/single-destination.php
php -l themes/bookings-and-flights-static/archive-destination.php
wc -l changed PHP/CSS files
rg source checks for Booking.com/White Label promises, direct checkout, auto-booking, guarantees, unsupported local filters, vague hotel-search CTA text, and missing disclosure language
node Playwright runtime smoke against `/hotels/`, hotel intent URL, `/destinations/`, temporary destination guide desktop/mobile, labelled placement disclosures, console/request health, duplicate-ID and overflow checks, bad-claim checks, and keyboard navigation to `Open partner search`, `Open hotel map`, and `Open hotel listings`
wp post delete temporary destination guide and confirm `temporary_posts_remaining=0`
git diff --check
```

P16.4 local result on 2026-05-13: PHP syntax checks, file-size checks, targeted source scans, `git diff --check`, and Playwright Chromium runtime review passed. Changed files stayed under the 600-line guideline: `search-surface.css` 535, `hotel-guide.css` 445, `page-hotels.php` 324, `single-destination.php` 328, `archive-destination.php` 113, `travel-search-placement.php` 128, and `hotel-discovery-placements.php` 82. Source scans found no hotel-surface Booking.com promise, White Label promise, direct-checkout claim, auto-booking claim, guaranteed-rate claim, unsupported local-filter claim, vague `Search this city`/`Search hotels` hotel CTA, live-rate claim, or live-room-inventory claim; the only matching `live prices` phrase was an unrelated existing Flights SEO description outside Phase 16. Runtime screenshots: `/tmp/one103-hotels-desktop.png`, `/tmp/one103-hotels-intent-desktop.png`, `/tmp/one103-hotels-mobile.png`, `/tmp/one103-hotels-disclosures-desktop.png`, `/tmp/one103-destinations-desktop.png`, `/tmp/one103-destination-detail-desktop.png`, `/tmp/one103-destination-detail-mobile.png`, `/tmp/one103-keyboard-partner-search.png`, and `/tmp/one103-keyboard-hotel-listings.png`. Runtime evidence: `/tmp/one103-runtime-report.json`. The final runtime report found no unsupported hotel claim patterns, missing labelled placement disclosures, app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, or blank pages on Hotels, hotel-intent, destination archive, and destination guide surfaces. Keyboard review reached `Open partner search`, `Open hotel map`, and `Open hotel listings`. Visual review found that the companion-card height rules partially overlapped the new yellow disclosure band; `hotel-guide.css` now keeps companion widgets auto-height and the disclosure sits below the widget body. Follow-up screenshots and DOM checks confirmed both companion disclosure bands begin with `Affiliate disclosure:` and sit below the widget frame. Temporary destination posts `318` and `319` were removed after validation. Provider-owned Chromium WebGL performance warnings remain classified as non-blocking provider runtime noise.

P16.5 mobile widget/map layout and source review validation:

```bash
wc -l themes/bookings-and-flights-static/assets/css/header.css themes/bookings-and-flights-static/assets/css/hotel-guide.css
node Playwright Chromium responsive/source review across `/hotels/`, a hotel-intent URL, `/destinations/`, and a temporary destination guide at desktop, tablet, mobile, and 320px narrow widths
node Playwright keyboard review for `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search`
wp post delete temporary destination guide and confirm `temporary_posts_remaining=0`
git diff --check
```

P16.5 local result on 2026-05-13: file-size checks and Playwright Chromium runtime review passed after two focused CSS touch-target fixes. Changed files stayed under the 600-line guideline: `header.css` 473 and `hotel-guide.css` 448. The Codex in-app Browser plugin was attempted first, but no active Codex browser pane was available, so Playwright Chromium was used for the required real browser screenshots and keyboard review. Runtime evidence is saved at `/tmp/one104-responsive-report.json`. The final report covered 16 route/viewport combinations and found no app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, unsupported hotel copy, sensitive source terms, missing affiliate disclosures, clipping problems, or small touch targets. Screenshots include `/tmp/one104-hotels-desktop.png`, `/tmp/one104-hotels-tablet.png`, `/tmp/one104-hotels-mobile.png`, `/tmp/one104-hotels-narrow.png`, `/tmp/one104-hotels-intent-mobile.png`, `/tmp/one104-destinations-mobile.png`, `/tmp/one104-destination-detail-mobile.png`, `/tmp/one104-destination-detail-narrow.png`, `/tmp/one104-hotels-mobile-keyboard.png`, `/tmp/one104-hotels-mobile-map-keyboard.png`, `/tmp/one104-destination-narrow-listings-keyboard.png`, and `/tmp/one104-destination-narrow-partner-keyboard.png`. Keyboard review reached `Update hotel intent` at tab stop 17, `Open hotel map` at tab stop 22, `Open hotel listings` at tab stop 9, and `Open partner search` at tab stop 23. The first strict runtime pass found the 320px header menu toggle could shrink below 44px and surfaced short hit areas on header nav links and hotel guide card title links; `header.css` now prevents menu-toggle flex shrink and gives nav links a 44px minimum block target, while `hotel-guide.css` gives card title links a 44px minimum block target. Provider-owned/Chromium WebGL performance warnings remain non-blocking runtime noise when app-owned checks pass.

P16.6 final Hotels and Stays review gate validation:

```bash
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
wc -l themes/bookings-and-flights-static/inc/seo-metadata.php
curl HTTP/source smoke for `/hotels/`, hotel-intent URL, `/destinations/`, and temporary destination guide
wp eval registry smoke for `hotels_partner_search`, `hotels_map_handoff`, and `hotels_listing_handoff`
wp plugin status bookings-flights-core
wp eval subdirectory-install smoke for `/blog/hotels/` hotel-intent robots handling
node Playwright Chromium responsive/source review across `/hotels/`, a hotel-intent URL, `/destinations/`, and a temporary destination guide at desktop, tablet, mobile, and 320px narrow widths
node Playwright keyboard review for `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search`
wp post delete temporary destination guide and confirm `temporary_posts_remaining=0`
git diff --check
```

P16.6 local result on 2026-05-13: PHP syntax passed for `seo-metadata.php`, file-size checks kept `seo-metadata.php` at 271 lines, `bookings-flights-core` reported `Status: Active`, and HTTP/source smoke returned `200` for `/hotels/`, the hotel-intent URL, `/destinations/`, and the temporary destination guide. Registry smoke confirmed schema `1.0.3` with active `hotels_partner_search`, `hotels_map_handoff`, and `hotels_listing_handoff` placements. Runtime evidence is saved at `/tmp/one105-phase16-report.json`; the final report covered 16 route/viewport combinations and returned `findingCount=0`. Screenshots include `/tmp/one105-hotels-mobile.png`, `/tmp/one105-destination-detail-narrow.png`, `/tmp/one105-hotels-intent-desktop.png`, `/tmp/one105-keyboard-update-hotel-intent.png`, `/tmp/one105-keyboard-open-hotel-map.png`, `/tmp/one105-keyboard-open-hotel-listings.png`, and `/tmp/one105-keyboard-open-partner-search.png`. Keyboard review reached `Update hotel intent` at tab stop 17, `Open hotel map` at tab stop 22, `Open hotel listings` at tab stop 9, and `Open partner search` at tab stop 23. The first final-gate pass found that transient hotel-intent query URLs canonicalized to `/hotels/` but did not render `noindex, follow`; `seo-metadata.php` now detects hotel search query parameters on `/hotels/` and applies the same transient-query robots behavior used for Flights. Codex PR review then found the hotel path helper missed WordPress subdirectory installs; the helper now strips the `home_url()` path prefix before comparing `/hotels/`, matching the Flights helper. Follow-up source smoke confirmed the hotel-intent URL renders `<meta name='robots' content='max-image-preview:large, noindex, follow' />` with canonical `/hotels/`, and a WP-CLI subdirectory-install simulation confirmed `/blog/hotels/` hotel-intent requests also receive `noindex, follow`. Temporary destination post `321` was removed and confirmed at `temporary_posts_remaining=0`.

P17.1 destination guide templates validation:

```bash
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l themes/bookings-and-flights-static/archive-destination.php
php -l themes/bookings-and-flights-static/single-destination.php
php -l themes/bookings-and-flights-static/template-parts/destination-planning-modules.php
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
wc -l changed PHP/CSS files
wp post term list temporary destination posts for `travel_region`, `travel_style`, and `travel_season`
curl HTTP/source smoke for `/destinations/` and a temporary destination guide
rg source checks for taxonomy labels, destination modules, provider handoffs, secrets, direct checkout, auto-booking, unsupported inventory claims, and numeric seed labels
node Playwright Chromium responsive review against `/destinations/` and a temporary destination guide at desktop, mobile, and 320px narrow widths
node Playwright keyboard review for `Open provider flight search`, `Open provider hotel search`, `Open hotel map`, `Open hotel listings`, and a related destination guide link
wp post delete temporary destination posts and confirm `temporary_posts_remaining=0`
git diff --check
```

P17.1 local result on 2026-05-13: PHP syntax passed for changed PHP files, changed files remained below the 600-line guideline, and `git diff --check` passed. HTTP/source smoke returned `200` for `/destinations/` and a temporary destination guide. Source checks confirmed destination taxonomy labels, destination SEO description precedence from excerpt to destination facts to legacy hotel summary fallback, destination facts, best-time/activity/seasonal modules, related destination links, related route fallback messaging, provider-owned flight/hotel handoff copy, visible affiliate disclosures, and no API keys, authorization/bearer strings, postback secrets, private keys, direct checkout, auto-booking, guaranteed availability, unsupported local inventory claims, old city-guide template strings, or numeric seed labels. Codex PR review found and the follow-up patch fixed an unconstrained related-destination query when no taxonomy terms exist; focused browser evidence for that empty-state path is saved at `/tmp/one106-no-taxonomy-related-report.json` and `/tmp/one106-no-taxonomy-related-desktop.png`. Playwright Chromium evidence is saved at `/tmp/one106-destination-report.json`; screenshots include `/tmp/one106-destinations-archive-desktop.png`, `/tmp/one106-destinations-archive-mobile.png`, `/tmp/one106-destinations-archive-narrow.png`, `/tmp/one106-destination-single-desktop.png`, `/tmp/one106-destination-single-mobile.png`, `/tmp/one106-destination-single-narrow.png`, `/tmp/one106-keyboard-open-provider-flight-search.png`, `/tmp/one106-keyboard-open-provider-hotel-search.png`, `/tmp/one106-keyboard-open-hotel-map.png`, `/tmp/one106-keyboard-open-hotel-listings.png`, and `/tmp/one106-keyboard-one-106-related-coast-guide.png`. The final runtime report covered six route/viewport combinations, reached all five keyboard targets, and returned `findingCount=0`. The Codex in-app Browser plugin was attempted first, but no active Codex browser pane was available, so Playwright Chromium was used for the required real browser screenshots and keyboard review.

P17.2 route guide templates validation:

```bash
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l themes/bookings-and-flights-static/archive-route.php
php -l themes/bookings-and-flights-static/single-route.php
php -l themes/bookings-and-flights-static/template-parts/route-planning-modules.php
wc -l changed PHP/CSS files
wp plugin status bookings-flights-core
wp eval route meta registration smoke for `baf_route_travel_time`, `baf_route_airport_notes`, `baf_route_flexible_dates`, and `baf_route_destination_notes`
node fetch source smoke for `/routes/`, `/routes/?route_origin=nyc`, a temporary route guide, and a no-context route
node Playwright Chromium responsive review against route archive, origin archive, route single, and no-context route pages
node Playwright keyboard review for `Open flight handoff`, `Watch route`, `Review low-price calendar module`, `Open destination hotel handoff`, `Explore destination activity prompts`, related route, and destination guide links
wp post delete temporary route/destination posts and confirm remaining counts are zero
git diff --check
```

P17.2 local result on 2026-05-13: PHP syntax passed for changed PHP files, changed files remained below the 600-line guideline, `git diff --check` passed, and `bookings-flights-core` reported active. WP-CLI confirmed `baf_route_travel_time`, `baf_route_airport_notes`, `baf_route_flexible_dates`, and `baf_route_destination_notes` are registered for `route` with `show_in_rest=false`, type `string`, sanitization callbacks, and auth callbacks. Node source smoke returned `200` for `/routes/`, `/routes/?route_origin=nyc`, a temporary route guide, and a no-context route. Source checks confirmed route SEO modules, travel-time/airport/flexible-date/destination modules, low-price calendar handoff, destination hotel/activity handoff, related route links, destination guide links, origin normalization to `NYC`, no-context related-route empty state, no arbitrary related content on the no-context route, and no API keys, authorization/bearer strings, postback secrets, private keys, direct checkout, auto-booking, guaranteed availability, stored/local fare inventory claims, fake prices, or fake scarcity. Codex PR review found and the follow-up patch fixed taxonomy-based related-route matching being skipped whenever airport meta existed; follow-up source and browser validation used a route with airport codes plus a taxonomy-only related route to confirm the peer appears. Playwright Chromium evidence is saved at `/tmp/one107-route-report.json`; screenshots include `/tmp/one107-routes-archive-desktop.png`, `/tmp/one107-routes-archive-mobile.png`, `/tmp/one107-routes-archive-narrow.png`, `/tmp/one107-origin-archive-desktop.png`, `/tmp/one107-route-single-desktop.png`, `/tmp/one107-route-single-mobile.png`, `/tmp/one107-route-single-narrow.png`, `/tmp/one107-route-no-context-desktop.png`, `/tmp/one107-keyboard-open-flight-handoff.png`, `/tmp/one107-keyboard-watch-route.png`, `/tmp/one107-keyboard-review-low-price-calendar.png`, `/tmp/one107-keyboard-destination-hotel-handoff.png`, `/tmp/one107-keyboard-activity-prompts.png`, `/tmp/one107-keyboard-related-route.png`, and `/tmp/one107-keyboard-destination-guide-link.png`. The final runtime report covered eight route/viewport combinations, reached all seven keyboard targets, and returned `findingCount=0`. Tool discovery did not expose the in-app Browser controls for this turn, so Playwright Chromium was used for the required real browser screenshots and keyboard review. Provider-owned `@babel/plugin-transform-react-jsx-source` and `sentry.avs.io` runtime noise was filtered as non-blocking after the first pass confirmed no app-owned console/request issues. Temporary posts `333`, `334`, `335`, and `336` plus temporary term `36` were deleted and confirmed at zero remaining.

P17.3 deal template validation:

```bash
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-starter-placements.php
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
php -l themes/bookings-and-flights-static/archive-travel_deal.php
php -l themes/bookings-and-flights-static/single-travel_deal.php
php -l themes/bookings-and-flights-static/template-parts/deal-editorial-modules.php
wc -l changed PHP/CSS files
wp plugin is-active bookings-flights-core
wp eval deal meta registration smoke for `baf_deal_seasonal_context`, `baf_deal_weekend_ideas`, `baf_deal_theme_notes`, `baf_deal_activity_notes`, `baf_deal_partner_notes`, and `baf_deal_source_note`
wp eval registry smoke for `flights_white_label_search` allowing the `deal` public surface
node fetch source smoke for `/travel-deals/`, a temporary deal brief, and a no-context deal brief
node fetch SubID/disclosure smoke for the deal White Label placement
node Playwright Chromium responsive review against deal archive, deal single, and no-context deal pages at desktop, mobile, and 320px narrow widths
node Playwright keyboard review for `Open flight handoff`, `Review partner cards`, `Browse deal ideas`, `Open hotel handoff`, `Explore activity prompts`, related deal, matching route, and destination guide links
wp post delete temporary deal/route/destination posts and confirm remaining counts are zero
git diff --check
```

P17.3 local result on 2026-05-13: PHP syntax passed for changed PHP files, changed files remained below the 600-line guideline, and `git diff --check` passed. `bookings-flights-core` is active. WP-CLI confirmed the six deal-only meta keys are registered for `travel_deal` with `show_in_rest=false` and sanitization callbacks. Registry smoke confirmed `flights_white_label_search` now allows the `deal` surface, and source smoke confirmed the deal single renders `data-baf-placement="flights_white_label_search"`, `data-baf-surface="deal"`, SubID `deal_single_deal_flights_deal_337_flights_white_label_search`, and three visible affiliate disclosures. Node source smoke returned `200` for `/travel-deals/`, `/travel-deals/one-108-spring-family-paris-deal-brief/`, and `/travel-deals/one-108-no-context-deal-brief/`, with no source matches for app-owned fatal errors, direct checkout, auto-booking, guaranteed availability, fake scarcity, API keys, bearer tokens, or postback secrets. Playwright Chromium evidence is saved at `/tmp/one108-deal-report.json`; screenshots include `/tmp/one108-deals-archive-desktop.png`, `/tmp/one108-deals-archive-mobile.png`, `/tmp/one108-deals-archive-narrow.png`, `/tmp/one108-deal-single-desktop.png`, `/tmp/one108-deal-single-mobile.png`, `/tmp/one108-deal-single-narrow.png`, `/tmp/one108-deal-no-context-desktop.png`, `/tmp/one108-keyboard-open-flight-handoff.png`, `/tmp/one108-keyboard-review-partner-cards.png`, `/tmp/one108-keyboard-browse-deal-ideas.png`, `/tmp/one108-keyboard-open-hotel-handoff.png`, `/tmp/one108-keyboard-explore-activity-prompts.png`, `/tmp/one108-keyboard-related-brief.png`, `/tmp/one108-keyboard-matching-route.png`, and `/tmp/one108-keyboard-destination-guide.png`. The final runtime report covered seven route/viewport combinations, reached all eight keyboard targets, and returned `findingCount=0` with no app-owned console errors, failed requests, horizontal overflow, small visible touch targets, blank pages, framework overlays, or deal hero/header overlap. The first visual pass found the deal hero tucked under the fixed header on mobile and narrow viewports; `deal-surface.css` now adds header clearance before the first hero text. Code review found and fixed an explicitly escaped title output issue in the deal single hero. Codex PR review found and fixed route matching that required both origin and destination airport metadata; follow-up Playwright validation used a deal and a route sharing destination airport only to confirm the matching route appears, with screenshot `/tmp/one108-review-route-destination-only-match.png` and no app-owned console errors or failed requests. The Codex in-app Browser path was attempted first but failed because no active Codex browser pane was available, so Playwright Chromium was used for the required real browser screenshots and keyboard review. Provider-owned runtime noise remained non-blocking. Temporary posts `337`, `338`, `339`, `340`, and `341`, temporary terms `37`, `38`, `39`, and `40`, and Codex review follow-up posts `342` and `343` were deleted and confirmed at zero remaining.

P17.4 taxonomy archive and internal-linking validation:

```bash
php -l themes/bookings-and-flights-static/taxonomy.php
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
wc -l themes/bookings-and-flights-static/taxonomy.php themes/bookings-and-flights-static/assets/css/taxonomy-surface.css themes/bookings-and-flights-static/inc/seo-metadata.php themes/bookings-and-flights-static/assets/css/mobile-nav.css
node fetch source smoke for a temporary `travel_style` archive page one and page two
node Playwright Chromium responsive review against the temporary taxonomy archive at desktop, mobile, and 320px narrow widths
node Playwright keyboard review for `Open flight handoff`, `Open hotel handoff`, `Destinations`, `Routes`, `Deals`, `Trip planner`, a destination guide link, and pagination `Next`
wp post delete temporary taxonomy content and confirm remaining posts are zero
wp term delete temporary taxonomy term and confirm the term is removed
git diff --check
```

P17.4 local result on 2026-05-13: PHP syntax passed for the new taxonomy template and changed SEO metadata file, changed files remained below the 600-line guideline, and `git diff --check` passed. Source smoke returned `200` for the temporary taxonomy archive and page two pagination, confirmed the archive rendered public destination/route/deal cards, internal-linking rules, and pagination, and confirmed the private destination plus non-public `trip_plan` validation content did not appear. Playwright Chromium evidence is saved at `/tmp/one109-taxonomy-report.json`; screenshots include `/tmp/one109-taxonomy-desktop.png`, `/tmp/one109-taxonomy-mobile.png`, `/tmp/one109-taxonomy-narrow.png`, `/tmp/one109-keyboard-open-flight-handoff.png`, `/tmp/one109-keyboard-open-hotel-handoff.png`, `/tmp/one109-keyboard-destinations.png`, `/tmp/one109-keyboard-routes.png`, `/tmp/one109-keyboard-deals.png`, `/tmp/one109-keyboard-planner.png`, `/tmp/one109-keyboard-open-destination-guide.png`, and `/tmp/one109-keyboard-next.png`. The final runtime report returned `findingCount=0` with no app-owned console errors, failed requests, horizontal overflow, small visible touch targets, blank pages, framework overlays, or fixed-header overlap. The first touch-target pass found mobile nav links could render below 44px on the taxonomy surface, so `mobile-nav.css` now gives mobile nav links an inline-flex 44px minimum block target; follow-up validation passed. Codex PR review found page-two taxonomy archives emitted the page-one canonical, the second pass found raw paginated request URLs could preserve tracking query args, and the third pass found the template's secondary `WP_Query` could drift from WordPress pagination validity. The theme now bounds the main taxonomy query with `pre_get_posts`, the template renders that main loop, and the SEO metadata helper builds taxonomy page one canonicals from the clean term link plus page two-or-deeper pagination paths. Follow-up canonical smokes created temporary travel-style terms and thirteen destination posts, confirmed page one canonicalized to `/travel-styles/one109-canonical-smoke/`, page two canonicalized to `/travel-styles/one109-canonical-smoke/page/2/`, a tracked page-two request with `?utm_source=codex` still canonicalized to clean `/page/2/`, page two rendered the thirteenth post, and cleanup left no remaining posts or term. A main-query smoke created thirteen public destination posts, one private destination, and one published `trip_plan`, then confirmed page two rendered the thirteenth public post while the private destination and `trip_plan` stayed hidden and cleanup left no remaining posts or term. Temporary posts `344` through `358` and temporary term `41` were deleted and confirmed absent after validation. WP-CLI emitted the known PHP 8.5 `Colors.php` deprecation noise, but the cleanup commands succeeded.

P17.5 editor workflow and content-manager field pipeline validation:

```bash
php -l plugins/bookings-flights-core/includes/class-plugin.php
php -l plugins/bookings-flights-core/includes/post-types/class-editor-meta-boxes.php
wc -l plugins/bookings-flights-core/includes/class-plugin.php plugins/bookings-flights-core/includes/post-types/class-editor-meta-boxes.php .plan/editor-workflow-content-manager-review.md
wp plugin is-active bookings-flights-core
wp eval editor meta-box registration smoke for `destination`, `route`, and `travel_deal`
wp eval save-handler smoke with valid nonce, invalid nonce, airport-code sanitization, budget sanitization, textarea sanitization, and empty-value cleanup
rg source checks for raw Travelpayouts script requirements in P17 editor/template paths
node Playwright Chromium admin/editor smoke screenshots for destination, route, and travel deal edit screens at desktop and mobile widths
node Playwright Chromium keyboard review through the P17 editor fields and publish controls
git diff --check
```

P17.5 local result on 2026-05-13: PHP syntax passed for `plugins/bookings-flights-core/includes/class-plugin.php` and `plugins/bookings-flights-core/includes/post-types/class-editor-meta-boxes.php`, changed source files remained below the 600-line guideline, and `git diff --check` passed. `bookings-flights-core` is active in the local WordPress site. Admin-context smoke confirmed the structured editor meta box registers for `destination`, `route`, and `travel_deal`; valid nonce saves update sanitized meta; invalid nonce saves do not write; airport codes normalize to uppercase alphanumeric values; negative budget context clamps to zero; textarea input strips markup; empty fields are cleaned up; and the rendered meta box includes the raw-script boundary note. Source review confirmed P17 templates use approved Travelpayouts placement wrappers and shell handoff links instead of requiring raw provider scripts in editor content. Playwright Chromium evidence is saved at `/tmp/one110-editor-report.json`; screenshots include `/tmp/one110-editor-destination-desktop.png`, `/tmp/one110-editor-route-desktop.png`, `/tmp/one110-editor-deal-desktop.png`, `/tmp/one110-editor-destination-mobile.png`, `/tmp/one110-keyboard-destination-field.png`, and `/tmp/one110-keyboard-save-control.png`. Browser QA confirmed the destination, route, and deal edit screens render the expected meta boxes without modal overlays, framework errors, app-owned console errors, or relevant failed requests; keyboard review confirmed focus on `baf_core_editor_meta[baf_destination]`, Tab to `baf_core_editor_meta[baf_destination_airport]`, and focus on `Save draft`. The first smoke found and fixed an admin-context guard for `add_meta_box()`, and the first keyboard pass found and fixed Destination field grouping/tab order from stale "Route context" to "Guide context". Temporary browser QA posts and the temporary admin user were deleted and confirmed absent. The Codex in-app Browser path was attempted first but had no active pane, so Playwright Chromium was used for the required real browser screenshots and keyboard review. WP-CLI/PHP emitted the known Travelpayouts PHP 8.5 deprecation noise, but commands completed.

P17.6 accessibility, responsive, SEO, and disclosure validation:

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php
php -l plugins/bookings-flights-core/includes/frontend/class-flight-alert-signup-shortcode.php
php -l themes/bookings-and-flights-static/single-destination.php
wc -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php plugins/bookings-flights-core/assets/css/frontend.css themes/bookings-and-flights-static/assets/css/deal-surface.css themes/bookings-and-flights-static/assets/css/taxonomy-surface.css themes/bookings-and-flights-static/single-destination.php
node Playwright Chromium accessibility/responsive/source/SEO review for destination, route, deal, taxonomy, archives, transient Flights query, and transient Hotels query states
node Playwright Chromium keyboard review for destination, route, deal, and taxonomy pages
node Playwright Chromium focused mobile copy follow-up for destination fallback text
wp plugin activate bookings-flights-core
wp eval temporary ONE-111 fixture cleanup for posts and travel taxonomy terms
git diff --check
```

P17.6 local result on 2026-05-13: PHP syntax passed for changed PHP files. The changed source files remained under the 600-line guideline, with `frontend.css` at 590 lines and now documented for future splitting before substantial expansion. Playwright Chromium evidence is saved at `/tmp/one111-accessibility-seo-report.json`; screenshots include `/tmp/one111-destination-desktop.png`, `/tmp/one111-route-desktop.png`, `/tmp/one111-deal-desktop.png`, `/tmp/one111-taxonomy-desktop.png`, `/tmp/one111-destinations-archive-desktop.png`, `/tmp/one111-routes-archive-desktop.png`, `/tmp/one111-deals-archive-desktop.png`, `/tmp/one111-destination-mobile.png`, `/tmp/one111-route-narrow.png`, `/tmp/one111-deal-mobile.png`, `/tmp/one111-taxonomy-narrow.png`, `/tmp/one111-keyboard-destination.png`, `/tmp/one111-keyboard-route.png`, `/tmp/one111-keyboard-deal.png`, `/tmp/one111-keyboard-taxonomy.png`, and focused follow-up `/tmp/one111-destination-copy-followup-mobile.png`. The first strict pass found and the follow-up fixes resolved a route alert consent target below 44px, an unnamed provider route-map iframe, sub-44px deal/taxonomy card title links, and default destination copy that still referenced guaranteed availability. The final report returned `status=pass` and `findingCount=0` with no app-owned console errors, blocking failed requests, horizontal overflow, unnamed links, missing image alt findings, hidden disclosures, blank pages, framework overlays, or small visible interactive targets. A focused mobile runtime follow-up after the final copy tightening confirmed the destination fallback text renders "availability commitments", does not render "guaranteed availability" or "confirmed availability", keeps affiliate disclosure visible, has no horizontal overflow, and has no app-owned console errors. SEO source checks confirmed transient Flights and Hotels query URLs render `noindex, follow` and canonicalize to `/flights/` and `/hotels/`, while public taxonomy archives remain indexable. Provider-owned WebGL, Babel, and GraphQL warnings remained non-blocking after app-owned checks passed. WP-CLI emitted the known local PHP 8.5 deprecation noise from WP-CLI internals and the third-party Travelpayouts plugin during activation and cleanup, but commands completed and temporary posts/terms were confirmed absent.

P17.7 final Phase 17 review validation:

```bash
php -l plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php
php -l plugins/bookings-flights-core/includes/post-types/class-editor-meta-boxes.php
php -l themes/bookings-and-flights-static/archive-destination.php
php -l themes/bookings-and-flights-static/single-destination.php
php -l themes/bookings-and-flights-static/archive-route.php
php -l themes/bookings-and-flights-static/single-route.php
php -l themes/bookings-and-flights-static/archive-travel_deal.php
php -l themes/bookings-and-flights-static/single-travel_deal.php
php -l themes/bookings-and-flights-static/taxonomy.php
php -l themes/bookings-and-flights-static/inc/seo-metadata.php
wc -l P17 PHP/CSS files
wp eval CPT and registered post-meta smoke for public/private CPT boundaries, `show_in_rest=false`, sanitization callbacks, and auth callbacks
node Playwright Chromium runtime/source/SEO/disclosure/console/request review for destination, route, deal, taxonomy, transient Flights query, and transient Hotels query states
node Playwright Chromium keyboard review for destination, route, deal, and taxonomy pages
wp eval temporary ONE-112 fixture cleanup for public/private posts and travel taxonomy terms
git diff --check
```

P17.7 local result on 2026-05-13: PHP syntax passed for P17 CPT/editor/template/SEO files, and reviewed P17 source files remain under the 600-line guideline. `frontend.css` is still 590 lines and remains documented for future splitting before substantial expansion. WP-CLI smoke confirmed `bookings-flights-core` is active; `destination`, `route`, and `travel_deal` are public with expected archives; `trip_plan`, `travel_alert`, and `travel_partner` remain private; and destination, route, and travel-deal editor meta keys are registered with `show_in_rest=false`, sanitization callbacks, and edit-meta authorization callbacks. Playwright Chromium evidence is saved at `/tmp/one112-phase17-final-report.json`; screenshots include `/tmp/one112-destinations_archive-desktop.png`, `/tmp/one112-destination_single-desktop.png`, `/tmp/one112-routes_archive-desktop.png`, `/tmp/one112-route_single-desktop.png`, `/tmp/one112-deals_archive-desktop.png`, `/tmp/one112-deal_single-desktop.png`, `/tmp/one112-taxonomy_region-desktop.png`, `/tmp/one112-flights_query-desktop.png`, `/tmp/one112-hotels_query-desktop.png`, `/tmp/one112-keyboard-destination_single.png`, `/tmp/one112-keyboard-route_single.png`, `/tmp/one112-keyboard-deal_single.png`, and `/tmp/one112-keyboard-taxonomy_region.png`. The final report returned `status=pass` and `findingCount=0`, with no app-owned console errors, app-owned failed requests, horizontal overflow, hidden disclosures, unnamed visible links, forbidden source terms, leaked private/non-public taxonomy content, or unexpected index/canonical behavior. The first report flagged the route alert checkbox visual size and an `avsplow.com` provider analytics-pixel `400`; follow-up inspection confirmed the checkbox is inside a large label hit area and the `400` belongs to provider-owned widget telemetry. Temporary posts `488` through `492` and temporary terms `one112-region`, `one112-style`, `one112-vertical`, and `one112-season` were deleted and confirmed absent. WP-CLI emitted the known local PHP 8.5 deprecation noise from WP-CLI internals and the third-party Travelpayouts plugin, but commands completed.

## Documentation-Only Changes

For documentation-only changes:

- Confirm required files exist.
- Confirm phase statuses and architecture contracts are consistent.
- No PHP/TypeScript runtime validation is required unless code also changes.

## Phase 18 AI Planner Validation

Use this when changing `/trip-planner/`, AI planner assets, or `POST /wp-json/baf/v1/ai/itinerary` prompt-to-brief behavior.

```bash
php -l plugins/bookings-flights-core/includes/ai/class-demo-ai-provider.php
php -l plugins/bookings-flights-core/includes/ai/class-openai-provider.php
php -l plugins/bookings-flights-core/includes/class-activator.php
php -l plugins/bookings-flights-core/includes/frontend/class-ai-planner-page.php
php -l plugins/bookings-flights-core/includes/frontend/class-frontend-manager.php
php -l plugins/bookings-flights-core/includes/rest/class-ai-itinerary-controller.php
php -l plugins/bookings-flights-core/includes/services/class-ai-itinerary-service.php
php -l plugins/bookings-flights-core/templates/ai-planner-page.php
php -l themes/bookings-and-flights-static/functions.php
php -l themes/bookings-and-flights-static/header.php
php -l themes/bookings-and-flights-static/page-home.php
php -l themes/bookings-and-flights-static/single-destination.php
php -l themes/bookings-and-flights-static/taxonomy.php
php -l themes/bookings-and-flights-static/template-parts/destination-planning-modules.php
node --check plugins/bookings-flights-core/assets/js/ai-planner.js
wc -l changed PHP/CSS/JS files
wp eval REST registration, unauthenticated permission, demo happy path, and live missing per-request consent smoke for `/baf/v1/ai/itinerary`
curl -s -o /tmp/one113-trip-planner.html -w '%{http_code} %{url_effective}\n' "http://bookings-and-flights.local/trip-planner/"
node Playwright Chromium screenshot and keyboard review for `/trip-planner/` desktop, success, and mobile states
git diff --check
```

P18.1 local result on 2026-05-13: PHP syntax passed for the changed core AI, frontend, REST, service, template, and static-theme PHP files. `node --check` passed for `plugins/bookings-flights-core/assets/js/ai-planner.js`. Changed source files remained at or below the 600-line guideline; `themes/bookings-and-flights-static/functions.php` stayed exactly 600 lines and should not receive substantial future additions without a split. WP-CLI REST smoke confirmed `/baf/v1/ai/itinerary` is registered, unauthenticated create requests return `401`, demo-mode planner requests return `201` with mode `demo` and a 4-day itinerary, and live mode without per-request consent returns `403:baf_ai_request_consent_required` before provider selection. `bookings-flights-core` deactivate/reactivate passed with known WP-CLI/Travelpayouts PHP 8.5 deprecation noise. `curl` returned `200` for `/trip-planner/`. Playwright Chromium evidence is saved at `/tmp/one113-ai-planner-browser-report.json`; screenshots include `/tmp/one113-ai-planner-empty-desktop.png`, `/tmp/one113-ai-planner-keyboard.png`, `/tmp/one113-ai-planner-success-desktop.png`, and `/tmp/one113-ai-planner-success-mobile.png`. The final runtime pass returned `status=pass` and `findingCount=0`, confirming the page title and URL, meaningful nonblank planner content, no framework overlay, no relevant app-owned console errors or failed responses, no source-secret matches, no sub-40px visible controls, no horizontal overflow on desktop or mobile, keyboard focus on `TEXTAREA#baf_ai_prompt`, successful result status `Trip brief ready. Review every field before using it in public content.`, four rendered itinerary days, two recommendation-only handoff opportunities, header/mobile CTAs pointing to `/trip-planner/`, and no raw prompt echo in the rendered result. The first browser pass found a 22px consent checkbox input and a false positive source-secret regex match on WordPress core's `luminous-dusk` preset name; the checkbox target was enlarged and the scan was narrowed to realistic secret-token shapes before the final pass. Codex PR review follow-up added real calendar validation with `checkdate()`, mirrored that validation in service normalization, and made planner live readiness depend on `Provider_Factory::supports_live_provider()`; focused PHP syntax, REST invalid-date smoke, unsupported-provider readiness smoke, and `git diff --check` passed. Post-review browser validation again attempted the Codex in-app Browser first and fell back to Playwright Chromium because no active pane was available; the authenticated result report at `/tmp/one113-after-codex-authenticated-report.json` passed with four days, two opportunities, no raw prompt echo, and no console/request failures, and the keyboard report at `/tmp/one113-after-codex-keyboard-auth-report.json` passed with focus reaching prompt, consent, and submit. The temporary admin users `codex_one113_ai_planner` and `codex_one113_review` were deleted after validation.
