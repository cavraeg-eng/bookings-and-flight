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

Manual/browser checks:

- Confirm the Travelpayouts plugin account setup page accepts Token, Partner ID, traffic source, and optional White Label URL.
- Confirm at least one flight search form renders on a test WordPress page.
- Confirm at least one hotel widget/table renders on a test WordPress page.
- Confirm generated links or widget settings support the documented SubID strategy.
- Confirm widget searches/handoffs open Travelpayouts-controlled or partner-controlled result pages.
- Confirm White Label Widget type can keep users on a WordPress page with the Bookings and Flights header where the embedded result UX is sufficient.
- If White Label Page type is used, compare the result page against the WordPress homepage header and confirm the configured logo, favicon, brand name, header background color/image, heading copy, and header/footer menu links match as closely as Travelpayouts allows.
- Confirm generated frontend source does not expose API tokens, partner secrets, postback secrets, raw private prompts, or private user data.
- Confirm widgets do not overlap, clip, or break mobile layouts.
- If the plugin fails compatibility checks, validate the fallback path using Travelpayouts dashboard-generated widget or White Label embed code inside a capability-gated WordPress wrapper.

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

## Documentation-Only Changes

For documentation-only changes:

- Confirm required files exist.
- Confirm phase statuses and architecture contracts are consistent.
- No PHP/TypeScript runtime validation is required unless code also changes.
