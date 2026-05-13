# Phase Review Log

Append phase reviews using this format.

```markdown
## Phase X Review - YYYY-MM-DD

Status:

Reviewer:

Scope reviewed:

Acceptance criteria result:

Security review:

REST permission review:

Database/migration review:

UI review:

Regression review:

Validation performed:

Bugs found:

Bugs fixed:

Bugs deferred:

Documentation updated:

Decision:
```

## Setup Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Initial project operating system setup only. No product implementation.

Acceptance criteria result: Required governance documents and AGENTS instructions created.

Security review: Security requirements documented; no executable product code changed.

REST permission review: Existing `baf/v1` namespace and existing affiliate bridge routes documented for future review.

Database/migration review: Planned table contracts documented; no migrations created.

UI review: Admin and frontend UI requirements documented; no UI changed.

Regression review: Existing plugins, themes, and `platform/` were inspected for high-level contracts. No runtime changes made.

Validation performed: Documentation-only setup; no PHP or TypeScript code changed.

Bugs found: None in changed files. Existing architecture discrepancy noted: `platform/README.md` mentions search API on `:4000`; `platform/INTEGRATIONS.md` says `:4050`.

Bugs fixed: None.

Bugs deferred: Reconcile platform API port documentation before platform integration work.

Documentation updated: `AGENTS.md`, `.plan/project-overview.md`, `.plan/architecture-baseline.md`, `.plan/phased-implementation.md`, `.plan/phase-review-log.md`, `.plan/validation-baseline.md`, `.plan/decisions.md`, `.plan/known-issues.md`, `.plan/regression-watchlist.md`.

Decision: Setup can be used for Phase 0 alignment. Product implementation has not started.

## Phase 0 Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 0 architecture baseline and plugin skeleton. Existing plugins, themes, and `platform/` were inspected for current contracts and discrepancies.

Acceptance criteria result: Passed. Naming contracts are documented; `bookings-flights-core` exists and activates; no provider secrets are exposed; no product workflow was implemented; validation commands are documented.

Security review: Passed. The core plugin adds no public UI, no REST routes, no AJAX actions, no provider calls, no secret handling, and no user input handling. Direct file access is guarded.

REST permission review: Passed. The core plugin registers no REST routes in Phase 0. Existing affiliate bridge routes remain the only known `baf/v1` routes and are tracked in the regression watchlist.

Database/migration review: Passed. No custom tables or migrations were added. Activation only stores the non-secret `baf_core_version` option idempotently.

UI review: Not applicable. No admin, frontend, shortcode, block, or settings UI was added.

Regression review: Existing affiliate bridge REST/config behavior, provider credential storage, theme security hardening, platform boundary, and new core bootstrap lifecycle are on the regression watchlist.

Validation performed: PHP syntax checks for changed PHP files; WP-CLI plugin activation/deactivation check; `wp option get baf_core_version`; REST route inventory confirmed no core REST routes were added. WP-CLI emitted the known PHP 8.5 deprecation warning but commands succeeded.

Bugs found: Confirmed existing/stale port documentation mismatch: platform runtime defaults use `4050`, but `platform/README.md` and affiliate bridge activation default still reference `4000`.

Bugs fixed: Created the missing planned core plugin skeleton and removed the obsolete known issue stating it did not exist.

Bugs deferred: Platform/API port documentation and affiliate bridge default URL reconciliation deferred to the first platform or settings integration task.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/known-issues.md`, `.plan/regression-watchlist.md`.

Decision: Phase 0 can move to `Completed`.

## Phase 1 Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 1 core data model against the phase objective, prerequisites, deliverables, and acceptance criteria. Phase 0 skeleton was present and active before implementation.

Acceptance criteria result: Passed. The documented CPT keys and taxonomy keys register successfully; dedicated capabilities are mapped to administrators; rewrite behavior is activation-safe through rewrite rule invalidation after registration; no direct SQL or unbounded repository queries were introduced.

Security review: Passed for Phase 1 scope. No public forms, AJAX actions, custom REST routes, provider calls, secrets, or direct database queries were added. Post meta is registered with sanitizers, `show_in_rest => false`, and edit authorization callbacks. Write service methods check dedicated capabilities before repository writes.

REST permission review: Passed. No custom `baf/v1` REST endpoints were added. CPTs and taxonomies use WordPress core REST integration for editor compatibility.

Database/migration review: Passed. No custom tables or migrations were added. Activation remains idempotent and only updates `baf_core_version`, maps administrator capabilities, registers WordPress objects, and invalidates rewrite rules.

UI review: Passed for Phase 1 scope. WordPress admin list/edit UI is provided by core CPT/taxonomy registration. No custom admin UI, frontend UI, shortcodes, or blocks were added.

Regression review: Core bootstrap watchlist was updated for registered CPT/taxonomy/capability behavior. A new core data model watchlist entry was added for stable keys, capability grants, meta privacy, and bounded repository queries.

Validation performed: PHP syntax checks for all PHP files in `plugins/bookings-flights-core`; WP-CLI plugin activation smoke check; WP-CLI CPT and taxonomy registration checks; administrator capability smoke check. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: None in the implemented Phase 1 scope.

Bugs fixed: Fixed the core autoloader path conversion so underscored namespace segments such as `Post_Types` and `Travel_Entity_Service` resolve to hyphenated plugin directories/files.

Bugs deferred: Existing platform/API port documentation mismatch remains deferred from Phase 0 because Phase 1 did not touch platform integration.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Plugin Handbook: Registering Custom Post Types (`register_post_type()`, `init`, naming limits)
- WordPress Taxonomies Handbook and `register_taxonomy()` reference
- WordPress Roles and Capabilities handbook
- WordPress `register_meta()` reference
- WordPress `flush_rewrite_rules()` reference

Decision: Phase 1 can move to `Completed`.

## Phase 2 Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 2 admin/settings foundation against objective, prerequisites, deliverables, acceptance criteria, and existing affiliate bridge settings. Phase 0 and Phase 1 were complete; affiliate bridge settings existed and required reconciliation rather than replacement.

Acceptance criteria result: Passed. Sensitive settings are gated by `manage_baf_settings`/administrator-only capabilities; core and affiliate bridge secret fields are masked; admin dashboard/integration pages show missing-configuration and configured states; Settings API sanitizers are registered for all Phase 2 core options.

Security review: Passed. Core settings use `register_setting()`, Settings API nonce fields, option-page capability filters, sanitization callbacks, and escaped rendering. Secret inputs render blank and preserve existing secrets when left blank. Affiliate bridge postback and supplier secrets no longer render raw values in the admin page.

REST permission review: Passed. No new REST routes were added. Existing affiliate bridge `/status` permission smoke check still fails safely for unauthenticated users. Existing public `/config` remains on the regression watchlist for secret exposure.

Database/migration review: Passed. No custom tables or migrations were introduced.

UI review: Passed for Phase 2 scope. Added scoped `baf-admin` styles, top-level core admin menu, Settings page, Integrations page, status cards, warnings, configured states, and links to the existing affiliate bridge settings. Assets enqueue only on registered core admin screens.

Regression review: Affiliate bridge settings were reconciled by keeping the existing page/option keys, masking secrets, preserving existing secrets on blank submissions, and updating the default search API URL to `http://localhost:4050` to match platform runtime configuration.

Validation performed: PHP syntax checks for changed PHP files; WP-CLI plugin activation check for `bookings-flights-core` and `bookings-and-flights-affiliate-bridge`; Settings API registration smoke check for `baf_settings`, `baf_travelpayouts_settings`, `baf_ai_settings`, `baf_consent_settings`, and `baf_tracking_settings`; secret exposure smoke checks for core and affiliate bridge admin HTML; permission failure checks for unauthenticated `manage_baf_settings` and affiliate bridge status permission; affiliate bridge sanitizer check confirmed blank secret submissions preserve existing secret values and strip unknown supplier fields. WP-CLI emitted the known PHP 8.5 deprecation warning but commands succeeded.

Bugs found: Existing affiliate bridge admin rendered postback secret fragments and supplier/API secret values; affiliate bridge local search API defaults still referenced `http://localhost:4000`.

Bugs fixed: Masked affiliate bridge postback and provider secret fields, preserved existing bridge secrets when blank masked fields are submitted, removed postback secret fragments from rendered URL examples, and updated bridge defaults/admin copy to `http://localhost:4050`.

Bugs deferred: Platform README port documentation still references `:4000` and remains tracked in `.plan/known-issues.md` because Phase 2 did not edit platform documentation.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/known-issues.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Settings API Handbook: `register_setting()`, settings groups, fields, sections, sanitization callbacks, `settings_fields()`, and Settings API nonces.
- WordPress Plugin Security Handbook: capability checks, nonces, sanitizing input, escaping output, and protecting sensitive data.
- WordPress Plugin Handbook admin menu guidance: top-level/submenu registration and admin page organization.
- WordPress plugin JavaScript/enqueueing guidance: `admin_enqueue_scripts` and loading assets only on relevant admin screens.

Decision: Phase 2 can move to `Completed`.

## Phase 3 Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 3 REST API foundation against objective, prerequisites, deliverables, and acceptance criteria. Phase 0 through Phase 2 were complete; Phase 1 data model and service/repository boundaries were available for public CPT list routes.

Acceptance criteria result: Passed. Core REST controllers are thin, every new route has an explicit permission callback, list endpoints paginate with bounded `per_page`, request parameters are validated and sanitized, and responses avoid private data exposure.

Security review: Passed. `GET /wp-json/baf/v1/destinations` and `GET /wp-json/baf/v1/routes` are intentionally public read-only discovery endpoints that return only published, non-password-protected public CPT content and safe fields. Private post meta, drafts, private posts, partner records, trip plans, alerts, provider credentials, AI/session data, and settings secrets are not returned.

REST permission review: Passed. New core routes register under `baf/v1` with callable permission callbacks. Public callbacks are explicit rather than omitted. Existing protected affiliate bridge `/status` route still fails unauthenticated requests with a safe 401 response.

Database/migration review: Passed. No custom tables, migrations, direct SQL, or destructive data changes were introduced. Temporary validation fixture posts were deleted after smoke checks.

UI review: Not applicable. No admin UI, frontend UI, shortcode, block, or template surface changed.

Regression review: Existing affiliate bridge public config/protected status behavior remains on the watchlist. A new core REST foundation watchlist entry tracks route registration, permission callbacks, pagination, validation, and public-field exposure.

Validation performed: PHP syntax checks for changed PHP files; plugin deactivation/reactivation check; REST route registration smoke check; permission callback inventory check; public list smoke checks for empty collections; invalid parameter failure check for `per_page=500`; unauthenticated permission failure check for existing `/status`; fixture-based checks confirmed published destination/route records appear, draft records do not appear, password-protected records do not appear, and private meta values are not exposed. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: During review, public list filtering initially did not explicitly exclude password-protected published posts.

Bugs fixed: Added `has_password => false` to the public travel entity service query boundary and validated that password-protected destination fixtures are excluded.

Bugs deferred: Existing platform README port documentation mismatch remains deferred from earlier phases because Phase 3 did not touch platform documentation.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress REST API Handbook: custom endpoint registration, `register_rest_route()`, `permission_callback`, args validation/sanitization, and REST response handling.
- WordPress `WP_REST_Controller` reference: controller structure, collection methods, schemas, and `WP_REST_Response`/`WP_Error` conventions.
- WordPress Security Common APIs guidance: validate and sanitize input, escape/prepare output, and avoid trusting unreviewed data.

Decision: Phase 3 can move to `Completed`.

## Phase 4 Review - 2026-04-27

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 4 primary product workflow against the objective, prerequisites, deliverables, acceptance criteria, existing REST/settings/provider boundaries, and earlier phase contracts. Phase 0 through Phase 3 were complete enough for a minimal WordPress-native affiliate card workflow. Saved trip and alert capture were deferred because Phase 4 prerequisites only require the first affiliate workflow and alert/trip UX is planned for later phases.

Acceptance criteria result: Passed. `[baf_travel_cards]` renders a user-facing affiliate card in editable WordPress content; generated links perform partner handoff only and do not implement checkout or payments; Travelpayouts links include documented `marker.subid` behavior; affiliate disclosure is rendered in monetized cards and available as `[baf_affiliate_disclosure]`; missing consent or missing marker fails gracefully with escaped frontend notices.

Security review: Passed. Provider request consent is required before affiliate cards or handoffs are usable. Travelpayouts API tokens are not used in frontend generation and are not exposed in shortcode HTML, REST responses, or click records. Handoff URLs are signed, expire after 24 hours, and restrict redirect targets to `aviasales.com`/`www.aviasales.com`. Shortcode input is sanitized and frontend output is escaped. Click tracking stores safe metadata plus hashed request identifiers, not raw IP addresses, raw user agents, raw referrers, provider tokens, or full target URLs.

REST permission review: Passed. `GET /wp-json/baf/v1/affiliate/click` registers under `baf/v1` with an explicit public permission callback because it only accepts signed, expiring, allowlisted handoff payloads. Missing, invalid, expired, or consent-disabled handoff requests fail safely with `WP_Error` responses. Existing `/destinations`, `/routes`, `/config`, and `/status` contracts remain unchanged.

Database/migration review: Passed. `$wpdb->prefix . 'bf_clicks'` is created idempotently with `dbDelta()`, uses `$wpdb->prefix`, the site charset/collation, and a stored `baf_db_version`. No destructive migration was introduced. Dynamic inserts use `$wpdb->insert()` formats.

UI review: Passed for Phase 4 scope. Affiliate cards use scoped `baf-frontend` styles, responsive CSS, a visible disclosure, clear missing-configuration notices, and 44px-minimum touch target behavior. No theme template or platform changes were required.

Regression review: Core REST route registration, settings secrecy, provider consent gates, affiliate bridge public config, and core data model contracts were re-checked. A new affiliate card/click handoff watchlist entry was added.

Validation performed: PHP syntax checks for all PHP files in `plugins/bookings-flights-core`; WP-CLI plugin activation smoke check; REST route registration and permission callback inventory for `/destinations`, `/routes`, and `/affiliate/click`; migration verification for `$wpdb->prefix . 'bf_clicks'` and `baf_db_version`; shortcode render happy-path check with temporary Travelpayouts marker and provider consent; successful signed click handoff returned `302` to Aviasales and wrote one click record; missing consent and missing marker checks returned graceful errors; frontend shortcode HTML check confirmed disclosure was present and a temporary API-token value was not exposed. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency. The direct `wp db query` check could not run because the local `mysql` client is unavailable, so table verification used `$wpdb` via `wp eval`.

Bugs found: During review, signed handoff URLs generated while consent was enabled would still redirect after consent was disabled until expiry. A related hardening gap allowed signed URLs to remain valid after the configured Travelpayouts marker changed.

Bugs fixed: Added provider-consent and current-marker checks to handoff validation so disabling provider consent or changing the configured marker immediately prevents affiliate redirects.

Bugs deferred: Existing platform README port documentation mismatch remains deferred because Phase 4 did not use `platform/`. Saved trip and alert capture remain planned for later phases because this phase delivered the first end-to-end affiliate workflow without expanding into alert/trip product surfaces.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Plugin Security Handbook and Common APIs security guidance: capability checks, nonces, sanitizing input, escaping output, and secret handling.
- WordPress REST API Handbook: `register_rest_route()`, `permission_callback`, args validation/sanitization, `WP_REST_Response`, and `WP_Error`.
- WordPress Shortcodes Handbook: prefixed shortcodes, sanitized attributes, returned output, and escaped rendering.
- WordPress Creating Tables with Plugins handbook: `$wpdb->prefix`, `$wpdb->get_charset_collate()`, `dbDelta()`, activation timing, and schema versioning.
- Travelpayouts Help Center: ID/SubID affiliate marker behavior, `marker` parameter, SubID naming conventions, and partner link API requirements.

Decision: Phase 4 can move to `Completed`.

## Phase 5 Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 5 background jobs and automation against objective, prerequisites, deliverables, acceptance criteria, and current Phase 0-4 implementation. Prior phases provided the core plugin lifecycle, settings/consent foundation, CPTs, affiliate workflow, and the `bf_clicks` migration; Phase 5 did not require new custom tables.

Acceptance criteria result: Passed. Long-running automation is scheduled through WP-Cron instead of page render; hooks are registered and unscheduled safely; failures/deferrals are recorded without secrets; retry attempts are bounded per retry window; job handlers use bounded queries and safe no-provider-call placeholders until later provider phases.

Security review: Passed. The background jobs page is gated by `manage_baf_settings`. Job status output is escaped. `baf_job_status` stores only status, timestamps, attempts, safe messages, and sanitized non-secret metadata. Provider credentials, API tokens, raw prompts, target URLs, customer data, and private request data are not logged.

REST permission review: Passed. Phase 5 added no REST routes and did not change existing route permissions. Existing Phase 3/4 route contracts remain unchanged.

Database/migration review: Passed. No custom table migration was introduced. Status tracking uses the bounded non-autoloaded `baf_job_status` option. Existing `bf_clicks` migration remains unchanged.

UI review: Passed. Added a scoped Background Jobs admin submenu under Bookings & Flights with scheduled job visibility, next-run status, last status/message, empty pending state, and escaped output. Admin assets remain scoped to registered core admin screens.

Regression review: Rechecked core plugin activation/deactivation lifecycle, cron scheduling/unscheduling, admin menu registration, and existing settings/capability boundaries. Added background jobs to the regression watchlist.

Validation performed: PHP syntax checks for changed PHP files; plugin activation/deactivation checks; WP-CLI scheduled hook checks before and after deactivation/reactivation; WP-CLI cron smoke test for `baf_sync_provider_stats`; verified `baf_job_status` records a safe deferred status when Travelpayouts marker is missing; admin menu smoke check confirmed `baf-jobs` is registered. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: Initial scheduling check showed hooks missing because the plugin was already active before Phase 5 code existed and activation did not rerun.

Bugs fixed: Added idempotent scheduling during plugin bootstrap in addition to activation so already-active installs self-heal missing Phase 5 cron events without duplicating hooks.

Bugs deferred: Cached offer refresh, alert processing, and provider stats sync are safe placeholder/bounded handlers until later provider/search/alert phases define live provider contracts and alert delivery behavior. Existing platform README port documentation mismatch remains deferred because Phase 5 did not touch `platform/`.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Plugin Handbook: WP-Cron overview and scheduling recurring events with `wp_schedule_event()`, `wp_next_scheduled()`, custom intervals, and event hooks.
- WordPress Code Reference/cron guidance for safely clearing scheduled hooks with `wp_clear_scheduled_hook()`/unscheduling on deactivation.
- WordPress Plugin Security Handbook/Common APIs security guidance: capability checks, sanitizing input, escaping output, nonces, and safe handling of operational data.
- WordPress Settings API Handbook: admin Settings API capability and sanitization patterns used by the existing settings foundation.
- WordPress Creating Tables with Plugins handbook: reviewed `$wpdb->prefix`, charset/collation, and `dbDelta()` guidance; no new Phase 5 table was required.

Decision: Phase 5 can move to `Completed`.

## Phase 6 Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 6 AI and external provider integration against objective, prerequisites, deliverables, acceptance criteria, and current Phase 0-5 implementation. Settings, REST, consent, capabilities, CPT/meta, affiliate card workflow, and cron foundations were present. Implementation stayed in the WordPress core plugin; `platform/` was not changed because the current phase could be satisfied with a WordPress-native adapter boundary.

Acceptance criteria result: Passed. Demo itinerary generation works without credentials or external requests. Live generation requires configured provider credentials and explicit external AI consent. Structured itinerary output is validated and sanitized before response or draft save. AI can only save optional `trip_plan` drafts and cannot publish. Affiliate/provider opportunities are recommendations only and are marked not executed/requires approval. Provider errors are converted to safe WordPress errors.

Security review: Passed. `POST /wp-json/baf/v1/ai/itinerary` requires `run_baf_ai`; optional draft saving also checks `edit_baf_content`. Request parameters are validated/sanitized. The live OpenAI adapter keeps API keys server-side. `bf_ai_sessions` stores hashed request/output metadata, sanitized summaries, and sanitized errors only; it does not store provider API keys, raw prompts, raw provider responses, private customer data, or full itinerary JSON. Live mode fails before network calls when consent is missing.

REST permission review: Passed. The new AI endpoint has a real permission callback and returns 401 for unauthenticated requests. Demo generation returns 201 for an administrator with `run_baf_ai`; missing live consent returns a safe 403 error.

Database/migration review: Passed. Added idempotent `bf_ai_sessions` migration using `$wpdb->prefix`, site charset/collation, and `dbDelta()`. Existing installs self-upgrade through `maybe_upgrade()`; activation also creates the table. The shared `baf_db_version` is now `1.1.0`.

UI review: Passed for current scope. No new admin screen was introduced. Existing AI settings remain in the Integrations page, secret fields remain masked, and demo mode remains configurable.

Regression review: Existing Phase 1 dedicated capabilities were discovered to be blocked by WordPress meta-cap handling for unknown primitive capabilities when saving generated draft trip plans. Fixed by directly mapping dedicated `baf_` capabilities and disabling CPT meta-cap remapping for custom primitive capability maps. Rechecked admin `run_baf_ai` and `edit_baf_content` capability behavior.

Validation performed: PHP syntax checks for changed PHP files; plugin activation check; `bf_ai_sessions` table verification; `baf_db_version` verification; REST route registration smoke test; unauthenticated permission failure test; authenticated demo generation test with draft `trip_plan` save; missing live consent test; AI session log review confirmed hashes/safe summaries instead of raw prompts or secrets. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: Dedicated administrator `edit_baf_content` capability existed on the role but `current_user_can( 'edit_baf_content' )` returned false because WordPress treated the unknown capability as a denied meta capability.

Bugs fixed: Added direct mapping for dedicated Bookings and Flights capabilities and set custom CPTs to use explicit primitive capability maps, restoring expected admin capability behavior without broadening access.

Bugs deferred: Live adapters currently support only OpenAI from the WordPress plugin. Anthropic and Vercel AI Gateway remain selectable settings from Phase 2 but fail safely as unsupported until dedicated adapters are implemented. `POST /ai/chat` remains planned. No real provider search/tool execution is implemented; affiliate opportunities are recommendation metadata only.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress REST API Handbook: custom endpoint registration, `permission_callback`, argument validation/sanitization, and REST response/error behavior.
- WordPress Plugin Security Handbook/Common APIs security guidance: capabilities, sanitizing input, escaping output, nonces, and secret handling.
- WordPress Creating Tables with Plugins handbook: `$wpdb->prefix`, charset/collation, `dbDelta()`, and migration version checks.
- WordPress Settings API Handbook: registered settings, sanitization callbacks, and capability restrictions for provider/API-key settings.
- AI SDK structured data generation documentation: schema validation before using structured model output.
- AI SDK tools and tool-calling documentation: schema boundaries, approval gates, and preventing automatic sensitive tool execution.
- OpenAI Chat Completions API documentation: server-side Bearer authorization and JSON response handling.
- Travelpayouts Help Center: Partner ID/marker and SubID tracking requirements and allowed SubID characters.

Decision: Phase 6 can move to `Completed`.

## Phase 7 Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 7 UI polish, accessibility, and responsive behavior against objective, prerequisites, deliverables, acceptance criteria, and current Phase 0-6 implementation. Existing UI surfaces were the core admin dashboard/settings/integrations/jobs pages, affiliate card/disclosure shortcodes, and the active static WordPress theme navigation. `platform/` was not changed because the active WordPress UI surfaces satisfied the phase scope.

Acceptance criteria result: Passed. Admin and frontend UI surfaces now handle configured, missing-configuration, pending, notice, and success/warning states with clearer labels. Styles remain scoped to plugin `baf-` classes or a small theme accessibility stylesheet. Keyboard and screen-reader behavior was reviewed and improved for admin shortcuts/statuses, job tables, affiliate cards, mobile navigation, and the theme color toggle. Responsive behavior was checked at desktop and mobile breakpoints.

Security review: Passed. No new REST routes, settings, provider calls, database writes, or secrets were introduced. Admin pages remain gated by `manage_baf_settings`. Output added in this phase is escaped. The affiliate shortcode smoke test confirmed a temporary API token was not rendered in frontend HTML.

REST permission review: Passed. No REST routes were added or changed in Phase 7. Existing route contracts and permission callbacks remain unchanged.

Database/migration review: Passed. No database schema, migration, cron, or option contract changes were introduced.

UI review: Passed. Added admin dashboard shortcuts, visible status badges, `role="status"` state regions, responsive admin form/table refinements, a screen-reader caption and mobile labels for the background jobs table, accessible affiliate card heading associations, notice status semantics, container-query-based affiliate card layout, focus-visible styles, mobile theme navigation `aria-controls`, color-toggle `aria-pressed` syncing, and mobile touch-target/overlay refinements in a dedicated theme accessibility stylesheet.

Regression review: Rechecked core admin settings, background jobs, affiliate card disclosure/secret exposure, theme mobile navigation, frontend home responsive behavior, and existing activation behavior. Added a UI accessibility/responsive behavior section to the regression watchlist.

Validation performed: PHP syntax checks for changed PHP files; plugin activation check; admin menu and admin asset enqueue smoke checks; theme accessibility stylesheet enqueue smoke check; admin dashboard/jobs render smoke checks for shortcuts, badges, table caption, and mobile labels; affiliate shortcode happy-path and missing-consent smoke checks; frontend secret exposure check with a temporary token; Playwright desktop and mobile homepage review; Playwright mobile menu interaction review; Playwright authenticated admin dashboard/jobs screenshot review; browser console warning/error review. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: Playwright MCP browser was already locked by an existing profile, so manual browser validation used the project Playwright CLI wrapper instead.

Bugs fixed: None beyond Phase 7 polish changes.

Bugs deferred: Existing oversized theme stylesheet `themes/bookings-and-flights-static/assets/css/header-footer.css` remains above the 600-line guideline from earlier generated theme work; Phase 7 avoided adding to it by creating `assets/css/accessibility.css`, but a broader stylesheet split is deferred because it would be a larger theme refactor outside this phase.

Documentation updated: `.plan/phased-implementation.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Plugin Developer Handbook: hooks, admin pages/settings surfaces, plugin organization, and asset enqueueing practices.
- WordPress Plugin Security Handbook/Common APIs security guidance: capability checks, sanitizing input, escaping output, nonces, and secret handling.
- WordPress Accessibility Coding Standards: WCAG 2.2 AA orientation, keyboard operability, semantic structure, labels, focus management, and color contrast.
- WordPress plugin JavaScript/enqueueing guidance: `admin_enqueue_scripts`, `wp_enqueue_scripts`, dependencies, and loading assets only where needed.
- Responsive design skill guidance: mobile-first breakpoints, fluid typography/spacing, container queries, responsive tables, and 44px touch targets.

Decision: Phase 7 can move to `Completed`.

## Phase 8 Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 8 analytics, reporting, and optimization against objective, prerequisites, deliverables, acceptance criteria, and current Phase 0-7 implementation. Prior phases provided click tracking (`bf_clicks`), AI run logging (`bf_ai_sessions`), background provider stats sync, capabilities, admin UI, and privacy/consent foundations. Search and alert event tracking and provider conversion/postback data are not implemented yet, so Phase 8 reports expose safe unavailable states for unavailable revenue/conversion data rather than fabricating metrics.

Acceptance criteria result: Passed. Reports are capability-gated by `view_baf_reports`; report queries use approved date windows and limited result sets; private user/request data is minimized; provider stats failures are handled safely through background job status and sanitized provider stats records.

Security review: Passed. The reports page and CSV export require administrator-only `view_baf_reports`; export links use WordPress nonces. Reports do not expose API keys, raw provider payloads, raw prompts, raw IP addresses, raw user agents, raw referrers, full target URLs, private customer data, or conversion identifiers. Output is escaped and CSV values are sanitized.

REST permission review: Passed. No new REST routes were introduced. Existing `/destinations`, `/routes`, `/affiliate/click`, and `/ai/itinerary` route registration remains unchanged, and no `/reports` REST route exists.

Database/migration review: Passed. Added idempotent `bf_provider_stats` migration using `$wpdb->prefix`, site charset/collation, and `dbDelta()`. Existing installs self-upgrade through `maybe_upgrade()`; activation also creates the table. The shared `baf_db_version` is now `1.2.0`. Dynamic custom SQL in reporting uses `$wpdb->prepare()` and bounded `LIMIT` clauses.

Admin UI review: Passed. Added `Bookings & Flights > Reports` with date-range filtering, summary cards, empty states, provider status snapshots, content inventory, a safe revenue/conversion unavailable notice, and CSV export. Styles are scoped to `baf-` admin classes.

Regression review: Rechecked activation/deactivation, custom table migration, report capability gates, admin reports menu registration, provider stats cron smoke behavior, existing REST route inventory, reports rendering, CSV rows, and secret exposure. Added core reporting dashboard to the regression watchlist.

Validation performed: PHP syntax checks for changed PHP files; file size review for changed files; plugin deactivate/activate check; `bf_provider_stats` table verification; `baf_db_version` verification; provider stats cron smoke test; provider stats insert smoke test with null metric; admin `view_baf_reports` capability check for administrator and unauthenticated users; reports menu registration smoke check; admin reports render smoke check; reporting service summary and CSV row smoke checks; secret exposure review for reports HTML/CSV; existing REST route inventory confirmed no reporting REST endpoint was added; debug log scan found no plugin-related fatal errors or warnings. WP-CLI emitted the known PHP 8.5 deprecation warning from its bundled dependency, but commands succeeded.

Bugs found: The first admin menu smoke check returned missing because no administrator user was set in the WP-CLI process before calling `add_menu_page()`; re-running with an administrator user confirmed `baf-reports` registers correctly. The initial provider stats repository passed a null metric value with a `%f` format; adjusted insertion to omit the `metric_value` column when no value exists.

Bugs fixed: Provider stats repository now preserves null metric values by omitting the column/format when no metric value is provided. Reports rendering was split into `BAF\Core\Admin\Reports_Page` to keep admin source files under the project file-size limit.

Bugs deferred: Search event tracking, alert event analytics, provider conversion/postback revenue reporting, and A/B testing foundations remain out of scope until their data models and approval requirements are implemented in a later phase.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress Plugin Developer Handbook: modular plugin organization, admin menus, hooks, activation/deactivation, and safe data handling.
- WordPress Plugin Security Handbook/Common APIs security guidance: capability checks, sanitizing input, escaping output, nonces, privacy, and safe database handling.
- WordPress Creating Tables with Plugins handbook: `$wpdb->prefix`, charset/collation, `dbDelta()`, idempotent schema versioning, and prepared database operations.
- WordPress REST API Handbook and custom endpoint guidance: permission callbacks, argument validation/sanitization, response behavior, and avoiding private data exposure; reviewed to confirm no reporting REST route was needed.

Decision: Phase 8 can move to `Completed`.

## Phase 9 Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Phase 9 hardening, regression review, and release readiness against objective, prerequisites, deliverables, acceptance criteria, and current Phase 0-8 implementation. Reviewed core plugin lifecycle, CPT/taxonomy/capability contracts, Settings API usage, REST routes, affiliate bridge routes, public shortcodes, AI provider boundaries, custom table migrations, cron scheduling, admin pages, reports, static theme surfaces, and `platform/` type/build readiness.

Current state summary: Phase 0-8 are documented as completed. The current WordPress product includes the core plugin `bookings-flights-core`, affiliate bridge plugin, content manager plugin, static theme, and `platform/` integration layer. Canonical contracts remain unchanged: `BAF\Core`, `baf_`, REST namespace `baf/v1`, CPT keys, option keys, table prefix, cron hooks, and shortcode names are preserved.

Plan alignment assessment: Passed. Phase 9 did not require product code changes. The appropriate implementation was a release hardening review, targeted validation, known-issue triage, regression watchlist update, validation baseline update, and release checklist documentation.

Acceptance criteria result: Passed. Security review found no release-blocking defects. Protected routes and actions have permission-failure checks. Activation/deactivation checks passed. Known issues are triaged. Release blockers are explicit and none are currently open.

Security review: Passed. Admin pages remain capability gated by `manage_baf_settings` or `view_baf_reports`; AI generation requires `run_baf_ai`; optional AI draft saving requires `edit_baf_content`; affiliate bridge status requires `manage_options`; settings are registered with sanitizers and Settings API nonces; rendered settings/report/shortcode output is escaped; secret fields render masked; live AI and provider handoffs require explicit consent/configuration; AI logs and click logs store hashes/safe summaries instead of raw prompts, API keys, full URLs, IP addresses, user agents, or referrers.

REST permission review: Passed. Registered `baf/v1` routes were inventoried and all route endpoints expose explicit `permission_callback` values. Unauthenticated `/status` returned 401, unauthenticated `/ai/itinerary` returned 401, unsigned `/affiliate/click` returned 400, missing-secret `/postback` returned 403, and public safe reads `/config`, `/destinations`, and `/routes` returned 200. No reports REST endpoint is exposed.

Database/migration review: Passed. `bf_clicks`, `bf_ai_sessions`, and `bf_provider_stats` are present with `baf_db_version` `1.2.0`. Existing migrations use `$wpdb->prefix`, site charset/collation, `dbDelta()`, and prepared table-existence checks. Reporting custom SQL uses prepared values and bounded limits.

Activation/deactivation review: Passed. Deactivating `bookings-flights-core` unscheduled `baf_refresh_cached_offers`, `baf_process_travel_alerts`, `baf_sync_provider_stats`, and `baf_cleanup_job_records`. Reactivating the plugin succeeded and scheduled all hooks again without deleting permanent data.

Admin/frontend UI review: Passed for release readiness. Admin integrations and reports rendered for an administrator without exposing a temporary provider token. Reports show empty/unavailable states for unavailable conversion data. `[baf_travel_cards]` rendered in a configured provider state without exposing the token. Static theme PHP syntax passed. Manual browser review was not repeated in Phase 9 because no UI code changed; Phase 7 browser validation remains the current visual baseline.

Regression review: Passed with deferred non-blocking items documented. Core route contracts, capability mappings, settings registration, cron hooks, custom tables, AI consent behavior, affiliate handoff safety, reports privacy, and platform type contracts were rechecked. Added a release readiness regression gate to the watchlist.

Validation performed: PHP syntax checks for active Bookings and Flights plugin/theme PHP files; plugin status checks for core, affiliate bridge, and content manager; core plugin deactivate/activate check; cron unschedule/reschedule verification; custom table and version verification; REST route inventory and permission callback check; unauthenticated REST smoke and permission-failure checks; administrator capability and settings registration checks; admin integrations/reports secret exposure checks; frontend shortcode secret exposure check; AI demo generation smoke check; live AI missing-consent smoke check; provider stats cron smoke check; `platform` shared/API/web typechecks; `platform` shared build; debug log tail review. WP-CLI emitted the known PHP 8.5 deprecation warning from bundled dependencies, but commands succeeded.

Bugs found: No release-blocking code defects found. Phase 9 confirmed a second oversized generated static theme stylesheet, `themes/bookings-and-flights-static/assets/css/home.css`, in addition to the previously known oversized `header-footer.css`.

Bugs fixed: None. Phase 9 required documentation and release gate updates only.

Bugs deferred: Platform API port documentation mismatch; WP-CLI PHP 8.5 bundled dependency deprecation warnings; oversized static theme stylesheets; unsupported live AI providers beyond OpenAI; search event tracking, alert analytics, conversion/postback revenue reporting, and A/B testing foundations.

Documentation updated: `.plan/phased-implementation.md`, `.plan/phase-review-log.md`, `.plan/known-issues.md`, `.plan/regression-watchlist.md`, `.plan/validation-baseline.md`, `.plan/release-readiness-checklist.md`.

Research consulted:
- WordPress Plugin Developer Handbook: plugin lifecycle, organization, hooks, activation/deactivation, and safe release checks.
- WordPress Plugin Security Handbook/Common APIs security guidance: capabilities, sanitizing input, escaping output, nonces, secret handling, and safe database usage.
- WordPress REST API Handbook/custom endpoint guidance: `permission_callback`, argument validation/sanitization, `WP_REST_Response`, and `WP_Error` behavior.
- WordPress Settings API Handbook: `register_setting()`, sanitization callbacks, Settings API nonce/capability handling, and option storage.
- WordPress Creating Tables with Plugins handbook: `$wpdb->prefix`, charset/collation, `dbDelta()`, idempotent schema versioning, and prepared database operations.
- WordPress Plugin Activation/Deactivation Hooks documentation: activation setup, deactivation cleanup, rewrite flushing, and deactivation versus uninstall boundaries.
- WordPress Cron Handbook: scheduled event registration, recurrence, and safe background processing review.
- AI SDK structured data and tool-calling documentation: schema validation, tool/action gating, and avoiding autonomous sensitive execution.
- OpenAI API documentation: server-side authorization and safe provider error handling.
- Travelpayouts Help Center: marker/SubID tracking and affiliate handoff context.

Decision: Phase 9 can move to `Completed`. No release blockers remain open.

## Frontend Smoke Review - 2026-04-28

Status: `Completed`

Reviewer: Verdent

Scope reviewed: Active static WordPress theme frontend, homepage search flow, linked public pages, key internal links, mobile navigation, public REST smoke endpoints, plugin activation status, and debug log tail.

Current state summary: Local WordPress frontend is available at `http://localhost:10019` with `bookings-and-flights-static` active. The homepage loaded successfully on desktop and mobile with no browser console warnings/errors or failed network requests. Public safe REST reads returned expected statuses, and protected `/status` returned an unauthenticated `401`.

Bugs found: Homepage destination cards and price-intelligence CTAs pointed to unimplemented `/flights/` and `/ai-planner/` paths, producing 404s. The footer legal link pointed to `/terms/` even though the canonical published page is `/terms-and-conditions/`. The search page query originally used `destination`, which conflicts with the registered `destination` CPT query var and can trigger a 404 on the Services page. About, Services, and Contact templates were published but rendered empty main content.

Bugs fixed: Homepage search and destination links now route to `/services/` with a non-conflicting `travel_destination` query parameter. Price-watch and AI-planner CTAs now link to anchors on the Services page. Footer legal link now uses `/terms-and-conditions/`. About, Services, and Contact templates now render useful public content, and Services renders a safe affiliate-card state plus the searched route context.

Regression review: Rechecked homepage desktop/mobile rendering, mobile menu interaction, browser console/network health, internal homepage links, Services search submission, PHP syntax for changed theme files, public REST smoke endpoints, core plugin activation status, and debug log tail. Existing deferred WP-CLI PHP 8.5 deprecation and Local socket warning remain environment/tooling noise.

Validation performed: PHP syntax checks for changed theme PHP files; HTTP smoke checks for `/`, `/about/`, `/services/`, `/contact/`, `/privacy-policy/`, `/terms-and-conditions/`, `/services/?travel_type=flights&travel_destination=Tokyo`, `/wp-json/baf/v1/destinations`, `/wp-json/baf/v1/routes`, `/wp-json/baf/v1/config`, and unauthenticated `/wp-json/baf/v1/status`; Playwright desktop and mobile homepage review; Playwright mobile menu interaction; Playwright internal-link crawl from the homepage; Playwright search form submission to Services; `bookings-flights-core` activation check; debug log tail review.

Documentation updated: `.plan/phase-review-log.md`, `.plan/regression-watchlist.md`.

Research consulted:
- WordPress `home_url()` code reference: generating site-aware frontend URLs in templates.
- WordPress `esc_url()` code reference: escaping URLs before HTML output.

## Phase 11.1 Compatibility Research and Local Activation Gate - 2026-05-09

Status: `Completed` for P11.1 only. Phase 11 remains `In Progress`.

Reviewer: Codex

Scope reviewed: Official Travelpayouts WordPress plugin compatibility research and local plugin activation/deactivation gate. This did not include admin credential setup, real Token/Partner ID configuration, widget rendering, handoff behavior, White Label header continuity, or production placement approval.

Current state summary: The official `travelpayouts` plugin version `1.2.2` is installed from WordPress.org and active in the Local WordPress workspace. The local WordPress version is `6.9.4`. The Local MySQL socket required for reliable WP-CLI checks is `/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock`.

Plan alignment assessment: Passed. Phase 11 calls for installing or staging the official Travelpayouts plugin and verifying activation/deactivation before deciding plugin-first placement or fallback embeds. P11.1 intentionally stopped at the research and activation gate because the remaining Phase 11 acceptance criteria need admin/browser/widget credentials and handoff checks.

Acceptance criteria result: Partial for Phase 11, passed for P11.1. The official plugin is staged and the local activation/deactivation gate passed. Plugin-first production use is not fully confirmed until admin setup, frontend widget rendering, SubID/handoff behavior, White Label continuity, mobile/desktop rendering, and secret exposure checks pass.

Security review: Passed for P11.1 scope. No Travelpayouts Token, Partner ID, traffic source, White Label URL, or production secret was configured. The only observed Travelpayouts option after activation was `travelpayouts_version = 1.2.2`. No new `debug.log` entries were written during activate/deactivate/reactivate.

Publish security follow-up: GitHub push protection identified an embedded Airtable personal access token in the official plugin package during PR publication. The staged local package now redacts the hard-coded token and makes the Airtable distribution script fail closed unless a token is supplied outside Git through `TRAVELPAYOUTS_AIRTABLE_TOKEN`.

Activation/deactivation review: Passed. `wp plugin activate travelpayouts`, `wp plugin deactivate travelpayouts`, and a final `wp plugin activate travelpayouts` succeeded. `wp plugin status travelpayouts` reported `Status: Active`, `Version: 1.2.2`.

Compatibility review: WordPress.org still warns that the plugin has not been tested with the latest three major WordPress releases. Direct PHP syntax validation found no syntax errors across the official plugin package, but PHP `8.5.4` emitted deprecation warnings from bundled Redux/PHP-DI/Parsedown/Opis/Travelpayouts code. Local web PHP is configured with `E_ALL & ~E_DEPRECATED`, so this did not block activation, but future admin/browser validation should watch for visible warnings if error reporting changes.

Validation performed: Official plugin metadata check via WordPress.org/plugin API; WP-CLI plugin list/status; official plugin install from WordPress.org with `--version=1.2.2`; activate/deactivate/reactivate gate using the Local MySQL socket; `travelpayouts_version` option check; `debug.log` before/after activation review; direct PHP syntax scan for official plugin PHP files. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning, but commands succeeded.

Bugs found: No activation fatal error, database connection blocker, or new debug-log entry was found during P11.1. Direct syntax scanning exposed PHP `8.5.4` deprecation warnings in official plugin/vendor code.

Bugs fixed: Redacted the embedded Airtable personal access token from the staged plugin package and disabled that distribution script when no out-of-repository token is configured. After Codex review, moved the Travelpayouts bootstrap direct-access guard before `ABSPATH` usage so direct requests exit safely instead of triggering an undefined constant fatal. Follow-up review fixed Travelpayouts API token exposure by changing the Gutenberg REST token action to return only non-secret configured state and by masking the landing-page token field while preserving existing saved tokens on blank submission.

Bugs deferred: Admin setup smoke check, widget/table/search-form render checks, handoff and SubID checks, White Label header continuity review, mobile/desktop browser checks, and secret-exposure source/browser review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress.org Travelpayouts plugin page: current plugin version, active install metadata, and the latest-three-major-releases compatibility warning.
- Travelpayouts Help Center, What is the Travelpayouts WordPress plugin: supported widgets, tables, search forms, White Label result option, and PHP/WordPress technical requirements.
- Travelpayouts Help Center, How to install Travelpayouts WordPress plugin: installation, Token/Partner ID, traffic source, and optional White Label setup flow.
- Travelpayouts Help Center, General plugin settings: host, White Label, click behavior, script placement, cache, nofollow, and event-tracking settings.
- Travelpayouts Help Center, Adding widgets, tables, and links through Travelpayouts WordPress plugin: widget/table/link placement flow and SubID support.
- Travelpayouts Help Center, Setting up White Label through Travelpayouts WordPress plugin: White Label setup and search-results behavior.
- Travelpayouts Help Center, Shortcodes of tables and widgets: shortcode families and responsive widget parameters.
- WordPress Plugin Developer Handbook, Activation/Deactivation Hooks: expected activation/deactivation responsibilities and lifecycle boundary.
- WordPress Plugin Security Handbook/Common APIs security guidance: capability, sanitization, escaping, nonce, and secret-handling context for the deferred admin/widget gates.

## Phase 11.2 Account Setup Smoke Checks and Safe Credential Handling - 2026-05-12

Status: `Completed` for P11.2 only. Phase 11 remains `In Progress`.

Reviewer: Codex

Scope reviewed: Travelpayouts account/setup smoke checks with safe credential handling. This included identifying required setup fields, validating missing and temporary configured states, hardening saved-token rendering/persistence, and checking the Gutenberg token route for non-secret behavior. This did not include real Travelpayouts credentials, frontend widget rendering, handoff/SubID checks, White Label continuity, or production placement approval.

Current state summary: The official `travelpayouts` plugin version `1.2.2` remains active on WordPress `6.9.4`. The local site started P11.2 with no `travelpayouts_admin_settings` option, which is the expected missing-configuration state for this workspace.

Plan alignment assessment: Passed. Phase 11 requires configuring Token, Partner ID, traffic source, and optional White Label URL through a safe documented process. The implementation stayed within the official plugin setup path and did not introduce a custom flight/hotel inventory backend.

Acceptance criteria result: Partial for Phase 11, passed for P11.2. Account setup requirements are identified and the local missing/configured credential gate passed with temporary values. Plugin-first production use is not fully confirmed until frontend widget rendering, generated link/SubID behavior, handoff, White Label continuity, mobile/desktop rendering, and browser/source secret exposure checks pass.

Admin setup review: Passed locally with temporary values. Required account fields are API token, Partner ID, traffic source/project, optional flights White Label domain, and optional hotels White Label domain.

Security review: Passed for P11.2 scope. Saved API tokens are rendered as blank password fields instead of raw values, blank token submissions preserve the existing token, account option writes sanitize token, Partner ID, project, and White Label domain fields, and the Gutenberg token action returns only `has_access_token` plus a blank `access_token`. Temporary test credentials were deleted after validation.

REST permission review: Passed for the reviewed Travelpayouts token path. The route remains gated by the plugin's `manage_options` check, and the action response no longer exposes the raw token.

Validation performed: PHP syntax checks for changed Travelpayouts PHP files; `git diff --check`; WP-CLI option smoke check for missing configuration; temporary-token option preservation/sanitization smoke check; Redux secret field render smoke check; Gutenberg token action smoke check in a separate WP-CLI process to verify configured state without exposing the token; source scan for the prior raw-token response and landing-page token-value patterns; plugin status check. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning, but commands succeeded when run with the Local MySQL socket.

Bugs found: The imported Travelpayouts account settings field could render a saved API token into admin HTML, and the account option write path did not centrally sanitize account values or preserve the token when a masked field was intentionally left blank.

Bugs fixed: Masked the account settings API token field, added secret-field support to the imported Redux text renderer, preserved saved account tokens on blank submissions, sanitized Travelpayouts account option writes, escaped landing setup form URLs/attributes, and handled missing landing setup fields without PHP notices.

Bugs deferred: Frontend widget/table/search-form render checks, generated link/SubID behavior, handoff behavior, White Label header continuity review, mobile/desktop browser checks, and browser-based secret-exposure review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center, How to install Travelpayouts WordPress plugin: account setup requires Token, Partner ID, traffic source, and optional White Label domains.
- Travelpayouts Help Center, WordPress plugin section: current plugin setup topics, widgets, tables, links, and White Label follow-up checks.
- WordPress Plugin Security Handbook/Common APIs security guidance: capabilities, sanitizing input, escaping output, nonces, and secret-handling context.
- WordPress Settings API Handbook: settings storage and sanitization context for option updates.

## Phase 11.3 Flight, Hotel, and White Label Test Surfaces - 2026-05-12

Status: `Completed` for P11.3 only. Phase 11 remains `In Progress`.

Reviewer: Codex

Scope reviewed: Temporary/documented Travelpayouts test surfaces for flight widgets, hotel widget/table shortcodes, and White Label result behavior. This included local shortcode rendering, frontend source review, desktop/mobile browser smoke checks, official plugin source inspection for hotel availability, and fallback-path documentation. This did not include real Travelpayouts credentials, a real White Label domain, production DNS/CNAME setup, or final production placement approval.

Current state summary: The official `travelpayouts` plugin version `1.2.2` remains active on WordPress `6.9.4`. The P11.3 smoke test used a temporary non-secret marker (`123456`) and deleted the temporary test page and `travelpayouts_admin_settings` option after validation.

Plan alignment assessment: Passed. Phase 11 asks for plugin-first compatibility evidence without introducing a custom inventory backend. P11.3 confirmed the official flight-widget path locally, identified the official hotel-widget blocker, and kept the hotel/White Label fallback inside Travelpayouts-controlled dashboard embeds rather than WordPress-owned search inventory.

Acceptance criteria result: Partial for Phase 11, passed for P11.3. Flight widget rendering and source-level handoff evidence passed on desktop and mobile. Hotel widget/table rendering did not pass through the official plugin because the staged plugin disables HotelLook tools. White Label behavior is documented from official docs and plugin settings, but real result-page continuity still needs configured White Label domains or dashboard-generated White Label widget code.

Flight surface review: Passed locally. `[tp_popular_routes_widget destination="BKK" subid="baf_home_flights_test_surface"]` rendered a Travelpayouts script source of `//www.travelpayouts.com/weedle/widget.js?marker=123456.wpplugin_baf_home_flights_test_surface&currency=usd&locale=en&powered_by=true&destination=BKK&host=hydra.aviasales.ru`.

Hotel surface review: Blocked for the official plugin path. `[tp_hotel_widget]` and `[tp_hotel_selections_widget]` rendered empty output. Source inspection showed hotel widget models depend on `HotelLookWidgetShortcodeModel::isActive()`, which calls `BrandSubscriptionService::isHotelLookAvailable()`, and this staged plugin returns `false`; inactive shortcodes are registered as callbacks that return an empty string. Later P11.6 review confirmed Travelpayouts now treats Hotellook tools as shut down, so the production hotel path should use a dashboard-generated Trip.com or other Hotels & Accommodation brand widget/link inside the governed WordPress wrapper.

White Label review: Documented, not fully validated against a real domain. Official Travelpayouts documentation confirms that plugin White Label fields can route widget/table/search results to configured White Label domains, Widget type can keep search and results on the embedded WordPress page, and Page type requires domain setup plus Travelpayouts appearance customization. For Bookings and Flights, Widget type remains preferred for full WordPress header continuity; Page type remains available only when its fuller result UX is needed and the logo, favicon, brand name, colors, heading copy, and menu/footer links are configured to match the home-site header.

Security review: Passed for P11.3 scope. No real Token, API secret, postback secret, authorization header, private customer data, checkout, payment, refund, or WordPress-owned booking flow appeared in the temporary frontend source or browser main-content checks. The temporary marker is a non-secret test value. Temporary validation content and options were removed after checks.

REST permission review: Not applicable. No REST routes were added or changed.

Database/migration review: Passed. No schema or migration changed. The temporary validation page and temporary `travelpayouts_admin_settings` option were deleted after smoke checks.

UI review: Passed for the flight-widget wrapper surface at the validation level available without a live external widget interaction. Browser checks at `1280x900` and `375x812` loaded the temporary page, confirmed the flight widget script was present, confirmed hotel scripts were absent, and did not find checkout/payment language in main content.

Regression review: Rechecked the Travelpayouts-controlled backend boundary, SubID convention, no-custom-inventory rule, no frontend secret exposure, and no direct booking/payment surface in WordPress output.

Validation performed: `wp plugin status travelpayouts`; temporary `travelpayouts_admin_settings` marker/language/currency setup; temporary WordPress page creation with flight and hotel shortcodes; WP-CLI `do_shortcode()` smoke check for flight, hotel widget, and hotel selections shortcodes; Browser desktop and mobile viewport checks; frontend source scan for token/secret/authorization/checkout/payment/refund strings; plugin hotel availability source inspection; temporary page deletion; temporary option deletion; `git diff --check`. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning, but commands succeeded with the Local MySQL socket.

Bugs found: Official hotel widget/table shortcodes render empty in the staged plugin because HotelLook availability is hardcoded off through `BrandSubscriptionService::isHotelLookAvailable()`.

Bugs fixed: None. This is treated as an official-plugin compatibility/fallback finding rather than a local patch because forcing the legacy HotelLook gate on would activate a discontinued provider surface without proof that the account/subscription path supports it.

Bugs deferred: Validate the dashboard-generated hotel widget/table fallback; validate real White Label Widget code on a WordPress page; validate Page-type White Label header continuity after domain/CNAME and Travelpayouts appearance settings are configured.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center, Adding widgets, tables, and links through Travelpayouts WordPress plugin: plugin widget/table/link placement and SubID support.
- Travelpayouts Help Center, General plugin settings: host, White Label, click behavior, script include, nofollow, and cache settings.
- Travelpayouts Help Center, Setting up White Label through Travelpayouts WordPress plugin: routing search results to configured White Label domains.
- Travelpayouts Help Center, Travelpayouts White Label Web Setup Guide: Widget versus Page type, hosting/domain requirements, and appearance settings.
- Travelpayouts Help Center, Setting up a White Label with Widget type: embedded widget code, same-page results, and optional separate results page.
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: external partner booking/payment boundary, Booking.com limitation, CNAME/domain rules, and search-crawler limitation.
- WordPress Plugin Security Handbook/Common APIs security guidance: secret handling, sanitizing input, escaping output, and capability context.
- WordPress Shortcodes Handbook: shortcode rendering and returned output context for the local smoke checks.

Decision: P11.3 can move to `Completed`. For the remaining Phase 11 work, plugin-first is acceptable only for the tested flight widget path; hotel surfaces should use Travelpayouts dashboard-generated fallback embeds until the official plugin exposes active HotelLook tools, and White Label continuity needs a configured Widget or Page-type validation pass before production approval.

## Phase 11.4 Header Continuity and White Label Configuration Notes - 2026-05-12

Status: `Completed` for P11.4. Phase 11 remains `In Review` until the deferred production setup checks pass.

Reviewer: Codex

Scope reviewed: Compared the current WordPress home-site shell against Travelpayouts White Label Widget and Page-type options. The local browser check confirmed the WordPress site exposes a Bookings and Flights home link, primary navigation, footer navigation, legal links, and a route back to `/`. No real Travelpayouts White Label domain was configured in this local workspace.

Plan alignment assessment: Passed. The continuity plan keeps WordPress as the branded shell and Travelpayouts as the monetized result/handoff layer. It does not introduce a custom inventory endpoint.

Continuity decision: White Label Widget type is preferred because it keeps the Travelpayouts search/results module inside the WordPress page and preserves the WordPress header and footer. Page-type White Label is allowed only when the fuller result UX is needed and the Travelpayouts dashboard is configured to mirror the home-site header.

Required Page-type inputs: logo URL, favicon URL, brand name `Bookings and Flights`, header background color or image treatment, search-heading copy, primary menu links for Home, About, Services, and Contact, footer links, legal links, and a visible route back to the main WordPress site.

Limitations captured: Widget type has less page-level result control but best header continuity. Page type can be styled in Travelpayouts, but it is not the real WordPress header and needs configured domain/CNAME, brand assets, menu/footer matching, and a return route. Booking and payment still complete on external partner sites.

Validation performed: Browser check of `http://bookings-and-flights.local/` and the temporary P11 validation page at `1280x900` and `375x812`; DOM checks for the Bookings and Flights home link, primary/footer navigation, Travelpayouts widget script, no checkout/payment/refund text, and no browser console errors.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/phased-implementation.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: Widget type preserves an existing site structure, Page type is standalone, and booking/payment completes on external partner sites.
- Travelpayouts Help Center, Setting up a White Label with Widget type: Widget setup includes project, language/currency, destination, and design settings.
- Travelpayouts Help Center, Setting up White Label through Travelpayouts WordPress plugin: plugin White Label fields route search results to configured White Label domains.

Decision: P11.4 can move to `Completed`. Future frontend phases should use Widget type first for full WordPress header continuity and reserve Page type for configured-domain result pages that match the home shell as closely as Travelpayouts allows.

## Phase 11.5 Backend Mode Decision and Documentation Update - 2026-05-12

Status: `Completed` for P11.5. Phase 11 remains `In Review` until the deferred production setup checks pass.

Reviewer: Codex

Scope reviewed: Synthesized P11.1-P11.4 evidence and updated the backend boundary, fallback path, SubID strategy, known follow-ups, `platform/` non-canonical status, and regression watchlist.

Backend mode decision: Use the official Travelpayouts WordPress plugin first only where local compatibility is proven. For Phase 11 that means the tested flight widget/search path. Use Travelpayouts dashboard-generated Trip.com or other Hotels & Accommodation brand widget/link code and White Label Widget/Page code inside the future capability-gated `baf` placement registry when the official plugin path is unavailable, disabled, or not configured with real domains.

Search surface mode decision: Planned `/search/flights` and `/search/hotels` may expose WordPress-owned shell configuration, approved placement metadata, disclosure copy, consent state, SubID/handoff metadata, or safe redirect information. They must not store or serve canonical live supplier inventory or make `platform/` the canonical WordPress search backend without a new documented architecture decision.

SubID decision: Keep `{channel}_{surface}_{vertical}_{slug}_{placement}` using lowercase Latin letters, numbers, and underscores. Do not include private user, trip, customer, analytics, or prompt data in SubIDs.

Validation performed: Documentation diff review; contract review for unchanged plugin slugs, REST namespace, option keys, CPT keys, and planned table prefixes; source and diff review confirming no new WordPress route, database table, migration, live inventory storage, or custom booking engine was introduced. Existing `platform/` Fastify search routes and adapters remain optional integration infrastructure.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phased-implementation.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center, Adding widgets, tables, and links through Travelpayouts WordPress plugin: plugin placement flow and SubID support.
- Travelpayouts Help Center, ID and SubID: Partner ID/marker attribution and SubID tracking behavior.
- WordPress Plugin Security Handbook: capability, sanitization, escaping, and secret-handling context.
- WordPress REST API Handbook, Adding Custom Endpoints: route permission expectations and REST-surface context.

Decision: P11.5 can move to `Completed`. Phase 13 should implement the governed registry/fallback wrapper rather than re-litigating whether WordPress should become a custom travel inventory backend.

## Phase 11.6 Phase Review and Validation Gate - 2026-05-12

Status: `In Review`. Local evidence gate passed, but Phase 11 cannot move to `Completed` until the deferred production setup checks pass.

Reviewer: Codex

Scope reviewed: Full Phase 11 objective, child-ticket sequence, acceptance criteria, security posture, REST/database impact, admin/frontend behavior, regression risk, documentation, and validation results.

Current state summary: P11.1, P11.2, P11.3, P11.4, P11.5, and P11.6 are complete locally. The official `travelpayouts` plugin version `1.2.2` remains active on WordPress `6.9.4`. The final backend mode is plugin-first for validated flight widgets and Travelpayouts dashboard-generated fallback embeds for hotel and White Label surfaces until real account/domain validation expands the official-plugin path.

Acceptance criteria result: Partial for Phase 11, passed for the local evidence gate. A plugin-first or fallback path is confirmed; no direct booking, checkout, payment, custom inventory backend, new REST route, or canonical live inventory store was introduced; temporary frontend source scans did not expose tokens, Partner ID secrets, API keys, authorization strings, checkout/payment/refund language, or private data; White Label requirements and limitations are documented; and the SubID strategy remains `{channel}_{surface}_{vertical}_{slug}_{placement}`. Phase completion remains blocked by validation of dashboard-generated hotel widget/table/embed fallback code and real White Label Widget/Page configuration.

Functional review: Passed for the locally available happy path. The official flight widget renders through the plugin with the expected Travelpayouts script and marker/SubID behavior. Hotel widgets are intentionally deferred to dashboard-generated fallback embeds because the staged plugin disables HotelLook tools. White Label is documented and gated on configured domains/dashboard code, so those production paths still require validation before Phase 11 completion.

Error, empty-state, and missing-configuration review: Passed. Missing `travelpayouts_admin_settings` remains a safe local state. Temporary credential/options checks restore or delete test options. Hotel shortcode empty output is documented as a legacy plugin compatibility finding, not hidden as a successful hotel render.

Security and data review: Passed. The phase did not add public REST endpoints, admin write actions, database migrations, direct provider calls, or secret-rendering paths. Previous P11.2 hardening keeps account tokens masked, preserves tokens on blank submission, sanitizes account fields, and returns non-secret token state from the Gutenberg token action.

Regression review: Passed for local documentation and source-scan scope. The Travelpayouts-controlled backend boundary, no-custom-inventory rule, no direct checkout/payment rule, SubID convention, White Label continuity path, and fallback wrapper requirement are all documented in the architecture baseline, validation baseline, known issues, and regression watchlist.

Validation performed: `wp plugin status travelpayouts`; `wp plugin deactivate travelpayouts`; `wp plugin activate travelpayouts`; compact WP-CLI `do_shortcode()` smoke check for `[tp_popular_routes_widget]`, `[tp_hotel_widget]`, and `[tp_hotel_selections_widget]`; forbidden-term scan of shortcode output; temporary browser validation page creation; Browser checks at `1280x900` and `375x812`; `curl` source scan for `weedle/widget.js` and forbidden token/secret/payment terms; temporary page deletion; temporary option deletion; documentation diff review; `git diff --check`. WP-CLI emitted the known PHP `8.5.4` bundled dependency deprecation warning, but commands succeeded with the Local MySQL socket.

Bugs found: Official hotel widget/table shortcodes still render empty because the staged plugin disables HotelLook availability.

Bugs fixed: None in P11.6. Earlier Phase 11 work fixed token masking, blank-token preservation, account option sanitization, and token-route secret exposure.

Bugs deferred: Validate dashboard-generated hotel widget/table/embed fallback; validate real White Label Widget code on a WordPress page; validate Page-type White Label header continuity after domain/CNAME and Travelpayouts appearance settings are configured; keep WP-CLI PHP `8.5.4` deprecation warning on the environment watchlist.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Plugin Security Handbook: sanitization, escaping, capability checks, nonces, and secret-handling context.
- WordPress REST API Handbook, Adding Custom Endpoints: permission callback expectations and route review context.
- WordPress Settings API Handbook: option storage and sanitization context.
- WordPress Plugin Handbook, Activation and Deactivation Hooks: plugin lifecycle validation context.
- Travelpayouts Help Center, How to install Travelpayouts WordPress plugin: plugin setup requires Token, Partner ID, traffic source, and optional White Label domains.
- Travelpayouts Help Center, Adding widgets, tables, and links through Travelpayouts WordPress plugin: widget/table/link placement and SubID support.
- Travelpayouts Help Center, Setting up White Label through Travelpayouts WordPress plugin: routing search results to configured White Label domains.
- Travelpayouts Help Center, Setting up a White Label with Widget type: embedded Widget setup and design options.
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: Widget versus Page type, domain/CNAME requirements, external booking/payment boundary, and Booking.com limitation.
- Travelpayouts Help Center, ID and SubID: Partner ID/marker and SubID tracking behavior.

Decision: Phase 11 remains `In Review`. Phase 12 may use the documented Travelpayouts-controlled backend boundary for design work, but Phase 11 cannot be marked `Completed` until production hotel fallback embeds and real White Label Widget/Page configuration are validated with Travelpayouts dashboard setup.

## Phase 11.6 Account Persistence Follow-Up - 2026-05-12

Status: `In Review`. Phase 11 remains blocked by real hotel fallback and White Label validation.

Reviewer: Codex

Scope reviewed: Travelpayouts account settings persistence after navigating away from the admin page, Project selection preservation, hotels White Label field visibility, and current backend shortcode behavior.

Current state summary: The live local option has a saved API token, Partner ID, Project, flights White Label domain, and hotels White Label domain. The hotels White Label field is now visible on the account settings form, and the Project select can show the saved Project as a fallback when Travelpayouts traffic-source lookup is unavailable. Official hotel shortcode output remains empty because HotelLook availability is still disabled inside the staged plugin.

Bugs found: Partial Redux account saves could omit the API token, Partner ID, Project, or White Label domain keys and overwrite the stored option without those values. A blank/placeholder Project submission could also replace a previously selected Project. The hotels White Label field was hidden because it depended on the staged plugin's hardcoded HotelLook availability gate.

Bugs fixed: `AccountOptionsSanitizer` now preserves saved account token, Partner ID, Project, flights White Label domain, and hotels White Label domain values when a settings save omits those fields; it also preserves the saved Project when the UI submits a blank/placeholder value and sanitizes Project IDs as numeric values. `PlatformsEndpoint` now includes a safe saved-Project fallback option so the Project select does not appear empty when the traffic-source API cannot return choices. `AccountForm` now exposes the hotels White Label field so the domain can be entered and persisted.

Validation performed: PHP syntax checks for changed Travelpayouts PHP files; direct sanitizer regression checks for missing and blank account submissions; WP-CLI account field visibility check confirming `hotels_domain` exists and is not hidden; Project option fallback check confirming the saved Project remains in select options; plugin deactivate/reactivate check; current account-state check without printing credentials; flight shortcode source check for `weedle/widget.js`, marker/SubID behavior, and no saved-token exposure; hotel shortcode check confirming it still returns empty output; homepage source scan for secret/payment terms; `git diff --check`.

Bugs deferred: Validate the saved hotels White Label domain with a real Travelpayouts White Label surface or dashboard-generated Trip.com/Hotels & Accommodation widget; validate real White Label Widget/Page behavior with the Bookings and Flights header continuity requirements; keep official hotel plugin shortcodes on the legacy fallback watchlist.

Documentation updated: `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: option storage and Settings API sanitization context.
- WordPress Common APIs Security Handbook: sanitize untrusted values and rely on WordPress APIs for data safety.
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: Widget/Page type behavior, domain requirements, external booking boundary, and hotel-widget availability context.

Decision: The missing API token, Partner ID, and Project persistence issue is patched locally, and the hotels White Label field/value is now present. The next manual setup item is to validate the hotel fallback or real White Label surface before Phase 11 can move from `In Review` to `Completed`.

## Phase 11.6 White Label Widget Placement Follow-Up - 2026-05-12

Status: `In Review`. Phase 11 still needs real White Label Widget/Page validation.

Reviewer: Codex

Scope reviewed: Current saved Travelpayouts account settings, WordPress-side storage for White Label Widget code, Page-type White Label domain placement, and a safe frontend wrapper for the Travelpayouts Widget-type White Label path.

Current state summary: The official Travelpayouts account option now has Token, Partner ID, Project, flights White Label domain, and hotels White Label domain present. The missing backend surface was a Bookings and Flights field for Travelpayouts White Label Widget code. `bookings-flights-core` now exposes this on `Bookings & Flights > Integrations` as `White Label Widget code or ID`, stores only the extracted `wl_id`, and renders the placement with `[baf_travelpayouts_white_label]`. Page-type White Label flight/hotel domains remain in the official Travelpayouts plugin account settings.

Bugs found: The plan called for a Travelpayouts dashboard-generated White Label Widget fallback, but there was no admin field or shortcode wrapper where the Widget-type `wl_id` could be entered and rendered inside the WordPress shell.

Bugs fixed: Added `baf_travelpayouts_settings.white_label_widget_id`, `baf_travelpayouts_settings.white_label_results_url`, sanitizer support that extracts a `wl_id` from pasted Travelpayouts code instead of storing raw script, and the `[baf_travelpayouts_white_label]` shortcode that renders `tpwl-search`, `tpwl-tickets`, and the approved Travelpayouts `tpwgts.com/wl_web/main.js` loader.

Validation performed: PHP syntax checks for changed core and Travelpayouts files; Settings API field registration check for the new Widget ID and results URL fields; sanitizer regression check for pasted Travelpayouts code, root-relative results URL normalization, and blank token preservation; shortcode render smoke check using an injected test option without mutating live settings; current safe account-state check; `bookings-flights-core` deactivate/reactivate; `git diff --check`.

Bugs deferred: Paste the real Travelpayouts White Label Widget code or `wl_id` into the new Bookings & Flights Integrations field; add `[baf_travelpayouts_white_label]` to the intended WordPress page; validate desktop/mobile rendering and header continuity; continue validating Page-type White Label domains through the official Travelpayouts plugin account page.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: `register_setting()`, Settings API sanitizers, and option storage context.
- WordPress Common APIs Security Handbook: sanitizing untrusted input and escaping frontend/admin output.
- WordPress Shortcodes Handbook: shortcode output context.
- Travelpayouts Help Center, Setting up a White Label with Widget type: Widget setup, `wl_id` loader code, search/results containers, and optional results URL behavior.
- Travelpayouts Help Center, Travelpayouts White Label Web Setup Guide: Widget type versus Page type setup and hosting/domain requirements.
- Travelpayouts Help Center, Setting up White Label through Travelpayouts WordPress plugin: Page/domain setup belongs in the official plugin White Label fields.

Decision: The backend now has a clear place for Widget-type White Label setup. Phase 11 remains `In Review` until the real Travelpayouts Widget ID or Page-type White Label flow is saved and browser-validated on the intended page.

## Phase 11.6 Trip.com Hotel Widget Follow-Up - 2026-05-12

Status: `In Review`. Real Trip.com/Hotels & Accommodation widget script is saved; shortcode placement and browser validation are still needed.

Reviewer: Codex

Scope reviewed: Official plugin hotel compatibility, current Travelpayouts hotel provider direction, Trip.com hotel widget placement, and safe WordPress-side storage/rendering for Travelpayouts dashboard hotel widget code.

Current state summary: The official Travelpayouts plugin in this workspace still contains legacy Hotellook-backed hotel shortcode classes, and those shortcodes render empty locally. Travelpayouts documentation says Hotellook tools are shut down and recommends replacing disabled hotel tools with widgets or links from current Hotels & Accommodation brands. The user confirmed the current hotel search path is Trip.com, so the production hotel path should be a Travelpayouts dashboard-generated Trip.com hotel widget/link instead of the imported plugin's HotelLook shortcode path.

Bugs found: The phase notes and setup checklist still treated hotel completion as waiting on a generic dashboard fallback or upstream HotelLook activation. That framing was stale because Hotellook is shut down and the actionable path is a current Travelpayouts Hotels & Accommodation brand widget, specifically the user's Trip.com hotel search setup.

Bugs fixed: Added `baf_travelpayouts_settings.hotel_widget_script_url`, a Hotels & Accommodation/Trip.com widget code field on `Bookings & Flights > Integrations`, sanitizer support that extracts and stores only an allowlisted Travelpayouts widget script URL from pasted code, and the `[baf_travelpayouts_hotel_widget]` shortcode that renders the approved script inside the WordPress page shell.

Validation performed: PHP syntax checks for changed core files; WP-CLI Settings API field registration check for the Trip.com hotel widget field and existing White Label fields; sanitizer regression check for pasted Travelpayouts widget script extraction, raw-script stripping, and non-Travelpayouts host rejection; shortcode smoke check using an injected test option without mutating live settings; current safe account-state check without printing credentials; `bookings-flights-core` deactivate/reactivate and final active-status check. WP-CLI required the Local MySQL socket to be passed through PHP `mysqli.default_socket`, and the known WP-CLI PHP deprecation warning still appears.

Bugs deferred: Paste the real Travelpayouts Trip.com hotel widget code or script URL into the new Integrations field; add `[baf_travelpayouts_hotel_widget]` to the intended WordPress hotel page; browser-validate desktop/mobile rendering, header continuity, outbound handoff, and no secret/payment leakage.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: `register_setting()`, Settings API sanitizers, and option storage context.
- WordPress Common APIs Security Handbook: sanitizing untrusted input and escaping frontend/admin output.
- WordPress Shortcodes Handbook: shortcode output context.
- Travelpayouts Help Center, FAQ on the closure of Hotellook: Hotellook widgets, landing pages, API, and old White Label hotel tab shutdown; replacement with Hotels & Accommodation widgets or links.
- Travelpayouts Help Center, Getting started with widgets: dashboard widget discovery, customization, copy-code flow, Project requirement, and tracking through Partner ID.
- Travelpayouts Help Center, Types of widgets: Search Form and White Label widget behavior, including Trip.com as a Travelpayouts widget example.

Decision: HotelLook/Hotellook is no longer the completion target for Phase 11. Phase 11 remains `In Review` until the saved Trip.com or other Travelpayouts Hotels & Accommodation widget code is placed with `[baf_travelpayouts_hotel_widget]` and browser-validated on the intended WordPress page.

## Phase 11.6 Widget Save Regression Follow-Up - 2026-05-12

Status: `In Review`. Save behavior is patched; the real Trip.com widget is saved, while the White Label Widget ID still needs to be pasted and browser-validated.

Reviewer: Codex

Scope reviewed: Bookings & Flights Integrations form submission, Settings API registration, White Label Widget ID parsing, Trip.com/Hotels widget script parsing, failed-save behavior, and live saved option state.

Current state summary: The Integrations form posts to the correct Settings API group and fields. The live option has the saved results URL and now has the real Trip.com hotel widget script saved from the user's `tpwgt.com/content` snippet. The White Label Widget ID is still missing. The cause of the blank-after-save behavior was sanitizer rejection of copied Travelpayouts snippets that used formats outside the first narrow parser, followed by returning an empty value without a useful admin error.

Bugs found: White Label widget parsing only handled a small `wl_id=` shape, and the hotel widget parser only handled `src="..."` without spacing. Invalid or unrecognized pasted widget code could silently save as blank, and an already saved widget value could be cleared by an invalid pasted replacement.

Bugs fixed: Expanded White Label parsing to accept common `wl_id`, `wl-id`, and `data-wl-id` shapes. Expanded hotel widget parsing to accept `src = "..."`, unquoted `src=...`, and protocol-relative Travelpayouts widget script URLs. Added Travelpayouts widget host/path validation and Settings API errors for rejected pasted values. Rejected non-empty pasted values now preserve the existing saved widget value instead of blanking it.

Validation performed: PHP syntax check for `class-settings-manager.php`; direct sanitizer check for full White Label script extraction; direct sanitizer check for spaced `tp.media/content` hotel script extraction; invalid White Label and invalid hotel widget checks confirmed existing saved values are preserved; temporary database round-trip with dummy non-secret values confirmed both widget fields save and the original live option is restored afterward. WP-CLI required the Local MySQL socket to be passed through PHP `mysqli.default_socket`, and the known WP-CLI PHP deprecation warning still appears.

Bugs deferred: Paste the real Travelpayouts White Label Widget code, confirm it persists after save, add `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` to the intended pages, and browser-validate desktop/mobile rendering plus header continuity.

Documentation updated: `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: registered option sanitization and admin settings save flow.
- WordPress Common APIs Security Handbook: sanitize untrusted input and escape output.
- Travelpayouts Help Center, Getting started with widgets: dashboard copy-code flow and widget code placement.
- Travelpayouts Help Center, Popular routes widget example: `tp.media/content` script code shape.
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: Widget type placement and external booking boundary.

Decision: The blank-after-save path is patched, and the real Trip.com `tpwgt.com/content` widget script is saved. The next check is saving the real White Label Widget ID/code, followed by shortcode placement and browser validation.

## Phase 11.6 `tpwgt.com` Trip.com Widget Follow-Up - 2026-05-12

Status: `In Review`. The Trip.com script is saved; browser validation remains.

Reviewer: Codex

Scope reviewed: User-provided Travelpayouts Trip.com widget code, sanitizer allowlist, live `baf_travelpayouts_settings.hotel_widget_script_url` save state, and Phase 11 completion blockers.

Current state summary: The user-provided Trip.com hotel widget code uses `https://tpwgt.com/content?...`. The previous sanitizer treated `tpwgt.com` as unknown even though the `/content` path matches the Travelpayouts widget script pattern. After patching the allowlist, the exact snippet is recognized and saved as the current hotel widget script URL.

Bugs found: The Travelpayouts widget host allowlist missed `tpwgt.com`, causing a real Trip.com dashboard widget script to be rejected with the generic unrecognized-widget error.

Bugs fixed: Added `tpwgt.com` and `*.tpwgt.com` to the Travelpayouts widget host allowlist while preserving the existing widget script path requirement.

Validation performed: PHP syntax check for `class-settings-manager.php`; exact user-provided Trip.com snippet sanitizer check confirmed it is accepted as host `tpwgt.com` and path `/content`; saved the sanitized script URL to `baf_travelpayouts_settings.hotel_widget_script_url`; verified the live option reports the Trip.com hotel widget script present without printing the full tracking URL. WP-CLI required the Local MySQL socket to be passed through PHP `mysqli.default_socket`, and the known WP-CLI PHP deprecation warning still appears.

Bugs deferred: Add `[baf_travelpayouts_hotel_widget]` to the intended hotel page and browser-validate desktop/mobile rendering, header continuity, outbound handoff, and no secret/payment leakage. Save and validate the real White Label Widget ID/code separately.

Documentation updated: `.plan/known-issues.md`, `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: registered option sanitization and admin settings save flow.
- WordPress Common APIs Security Handbook: sanitize untrusted input and restrict saved embed URLs.
- Travelpayouts Help Center, Getting started with widgets: dashboard copy-code flow and widget code placement.

Decision: The provided Trip.com widget code is valid for this integration and is now saved. Phase 11 still cannot complete until the widget shortcode is placed and browser-validated, and the White Label Widget ID/code is saved and validated.

## Phase 11.6 White Label Page Template Follow-Up - 2026-05-12

Status: `In Review`. The pasted White Label template is identified; Widget-type ID/code is still needed for the WordPress wrapper.

Reviewer: Codex

Scope reviewed: User-provided White Label HTML, Widget-type versus Page-type setup boundary, sanitizer behavior, live widget setting state, and shortcode placement.

Current state summary: The submitted White Label code is a full Page-type HTML template with Travelpayouts placeholders such as `[:embed_script:]`, `[:route_info:]`, and `[:current_year:]`. It also includes `tpwl-search` and `tpwl-tickets` containers, but it does not contain a `wl_id` or `main.js?wl_id=...` loader. The Bookings & Flights `White Label Widget code or ID` field is intentionally for Widget-type setup, so this Page-type template cannot be saved as a widget ID.

Bugs found: The generic no-`wl_id` admin error did not clearly tell the user that the pasted HTML was Page-type template code and belongs in the Travelpayouts dashboard, not the WordPress Widget ID field.

Bugs fixed: Added detection for Travelpayouts Page-type template markers including `[:embed_script:]`, `[:route_info:]`, `[:current_year:]`, and combined `tpwl-search`/`tpwl-tickets`. The sanitizer now shows a specific admin error explaining that Page-type template code should be pasted in the Travelpayouts White Label Page settings, while this WordPress field requires Widget-type code containing `wl_id`.

Validation performed: PHP syntax check for `class-settings-manager.php`; representative Page-type template sanitizer check confirmed the existing widget ID is preserved and the specific `baf_white_label_page_template_pasted` admin error is emitted; live option check confirmed the Trip.com hotel widget remains saved, the White Label Widget ID is still missing, and no intended pages currently contain `[baf_travelpayouts_white_label]` or `[baf_travelpayouts_hotel_widget]`. WP-CLI required the Local MySQL socket to be passed through PHP `mysqli.default_socket`, and the known WP-CLI PHP deprecation warning still appears.

Bugs deferred: Create or copy the Widget-type White Label code from Travelpayouts, save the `wl_id` or `main.js?wl_id=...` snippet in Bookings & Flights Integrations, add the shortcodes to the intended pages, and browser-validate the final experience.

Documentation updated: `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center, Setting up a White Label with Widget type: Widget-type loader uses `main.js?wl_id=...` with `tpwl-search` and `tpwl-tickets` containers.
- Travelpayouts Help Center, What is White Label Web by Travelpayouts: Widget type embeds search/results on the existing site, while Page type is a standalone White Label page.
- WordPress Settings API Handbook: registered option sanitization and admin settings save flow.

Decision: Nothing is wrong with the submitted HTML as a Page-type template, but it is the wrong artifact for the WordPress Widget ID field. The next required value is the Widget-type `wl_id` or the Widget-type loader snippet from Travelpayouts.

## Phase 11.6 Final Browser Validation Gate - 2026-05-12

Status: `In Review`. Local code and browser validation passed; GitHub PR review and merge remain the final completion gate.

Reviewer: Codex

Scope reviewed: Published Flights and Hotels WordPress pages, saved Bookings & Flights Travelpayouts integration settings, official Travelpayouts plugin account persistence, Trip.com hotel handoff, White Label Widget wrapper, frontend source output, mobile-width rendering, and Phase 11 backend-boundary documentation.

Current state summary: The live local setup has the White Label Widget ID, White Label results URL, and Trip.com hotel partner embed saved in `baf_travelpayouts_settings`. The published Flights page contains `[baf_travelpayouts_white_label]`, and the published Hotels page contains `[baf_travelpayouts_hotel_widget]`. The Hotels page renders a Trip.com partner iframe and a visible sponsored `Open hotel search` handoff link; the direct Trip.com partner search form opens successfully in Chrome. The Flights page renders the Travelpayouts White Label search form inside the WordPress shell.

Bugs found: The Hotels page content was partially hidden by the fixed site header on generic pages. The script-generated Trip.com widget path could render a blank third-party iframe in Chrome, while the direct partner URL rendered the real Trip.com search form. The Flights page displayed a PHP-DI deprecation from the bundled Travelpayouts dependency when local PHP displayed deprecations.

Bugs fixed: Added top spacing for generic `.site-content > .container` page content in the static theme. Split the oversized generated header/footer stylesheet into focused header, mobile navigation, and footer stylesheets before publishing the theme. Allowed and rendered direct Trip.com partner embed URLs, while keeping a visible sponsored handoff link for browsers or extensions that block the iframe. Patched the bundled PHP-DI `ObjectCreator` compatibility path so `ReflectionProperty::setAccessible()` is skipped on PHP 8.1+ and the nullable class-name signature is explicit.

Validation performed: Chrome desktop review confirmed the Flights page loads a clean Travelpayouts White Label search form with no PHP warning output, and the Hotels page shows the Trip.com hotel surface plus visible handoff. Chrome mobile-width review confirmed the Flights and Hotels pages fit without obvious overlap or clipped controls. The direct Trip.com partner URL rendered the expected destination, date, room/guest, and search controls. `curl` source scans for `/flights/` and `/hotels/` found no `Deprecated`, `Warning`, `Fatal`, API token, authorization, bearer, checkout, payment, refund, or secret text, and confirmed the split theme CSS handles load instead of the old combined header/footer stylesheet. REST smoke checks confirmed unauthenticated AI itinerary creation returns `401`, public destinations return a bounded empty collection, and unsigned affiliate click handoff returns a missing-parameter `400` rather than redirecting. PHP syntax and `git diff --check` passed for the changed code.

Bugs deferred: Full production proof still depends on the deployed Travelpayouts account/domain configuration and real browser mix. Keep the Trip.com iframe/handoff behavior on the Phase 13 registry watchlist and preserve the rule that Travelpayouts controls monetized search/results/booking handoff.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/known-issues.md`, `.plan/phased-implementation.md`, `.plan/regression-watchlist.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API Handbook: registered option sanitization and admin settings save flow.
- WordPress Common APIs Security Handbook: sanitize untrusted input and escape frontend/admin output.
- WordPress Shortcodes Handbook: shortcode output context.
- Travelpayouts Help Center, Setting up a White Label with Widget type: Widget loader, search/results containers, and Widget-type setup boundary.
- Travelpayouts Help Center, FAQ on the closure of Hotellook: legacy Hotellook shutdown and replacement with current Hotels & Accommodation widgets or links.
- Travelpayouts Help Center, Getting started with widgets: dashboard copy-code and partner widget setup flow.

Decision: Phase 11.6 is locally validated and ready for PR review. Do not mark ONE-73 or Phase 11 complete until the branch is published, reviewed, merged, and Linear is updated with the final PR and validation evidence.

## Phase 11.6 Codex Review Follow-Up - 2026-05-12

Status: `In Review`. Follow-up fixes are implemented locally and ready for PR review.

Reviewer: Codex

Scope reviewed: Delayed Codex review comments on PR #6, external provider consent behavior for Travelpayouts widget shortcodes, and custom table migration version gates.

Current state summary: PR #6 was merged before Codex posted review comments. The thread-aware review check found three unresolved P1 comments: White Label widgets rendered third-party scripts without provider consent, hotel widgets rendered third-party scripts/iframes without provider consent, and the shared `baf_db_version` option could let one table migration mark the schema current before other table migrations ran.

Bugs found: External Travelpayouts widgets could bypass the `allow_provider_requests` consent gate when a widget ID or hotel widget URL was configured. Future multi-table schema updates could skip `bf_ai_sessions` or `bf_provider_stats` if `bf_clicks` updated the aggregate schema version first.

Bugs fixed: `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` now check `baf_consent_settings.allow_provider_requests` before rendering any external Travelpayouts or Trip.com script/iframe. Public visitors receive no third-party widget output when consent is disabled; administrators see an escaped missing-consent notice. The click, AI session, and provider stats migrations now maintain table-specific schema version options in addition to the aggregate `baf_db_version`.

Validation performed: PHP syntax checks passed for the two shortcode files and three migration files. Shortcode smoke checks confirmed disabled consent blocks external White Label and hotel output, and enabled consent renders the configured external surface. Migration smoke checks forced the aggregate schema version current while table-specific versions were old; all three migrations still ran and refreshed their table-specific version options. `git diff --check` passed. WP-CLI emitted the known Travelpayouts PHP 8.5 deprecation noise but commands completed.

Bugs deferred: Broader official Travelpayouts PHP 8.5 deprecation cleanup remains a compatibility watch item; public page source remains clean for the validated pages.

Documentation updated: `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Creating Tables with Plugins Handbook: `dbDelta()`, table prefix/collation, and schema version options for upgrades.
- WordPress Plugin Security Handbook: capability checks, sanitization, escaping, and protecting external/private data boundaries.
- WordPress Shortcodes Handbook: shortcode render behavior and safe generated output.

Decision: P11.6 should remain in review until this follow-up branch is published, checked by Codex, merged, and Linear is updated with the final merge evidence.

## Phase 11.6 Hotel Widget Display Completion Follow-Up - 2026-05-12

Status: `Completed`. Local validation passed, PR #8 was reviewed by Codex, the selector-scope fix was merged, and Linear `ONE-73` was synced to Done.

Reviewer: Codex

Scope reviewed: User-reported Hotel page widget display, Trip.com partner iframe wrapper behavior, static-theme footer fallback output, mobile overflow, and consent-enabled Travelpayouts rendering.

Current state summary: Provider request consent is enabled, and the live settings contain the White Label Widget ID, White Label results URL, and Trip.com hotel partner URL. The Hotels page rendered the provider-owned Trip.com iframe, but the embedded UI showed prominent Trip.com branding above the search controls and compressed the provider fields on mobile. The static footer also exposed unresolved placeholder defaults when global contact/footer options were empty.

Bugs found: The static theme footer rendered `{{footer_*}}` and `{{contact_*}}` placeholder text on public pages. The direct Trip.com iframe wrapper forced a wide mobile iframe and exposed provider branding before the search controls, creating a poor branded page experience.

Bugs fixed: Footer placeholder defaults are now normalized to escaped production-safe fallback copy, and empty phone/email links are hidden rather than rendering fake placeholder links. The Trip.com partner iframe is wrapped in a cropped frame so the Hotels page presents the search controls without the large provider logo/header, while preserving the sponsored handoff button to the provider site. A Codex P1 review finding on PR #8 was patched by scoping the iframe translation to `.baf-travelpayouts-hotel-widget__frame iframe`, leaving script-generated non-Trip.com widget iframes unshifted.

Validation performed: PHP syntax passed for `themes/bookings-and-flights-static/footer.php` and `plugins/bookings-flights-core/includes/frontend/class-travelpayouts-hotel-widget-shortcode.php`; `git diff --check` passed. Source checks confirmed `/hotels/` renders the Trip.com partner iframe and handoff link without unresolved `{{placeholder}}` tokens or API key, authorization, bearer, secret, checkout, payment, PHP warning, or deprecation text. Playwright validation at desktop dark, desktop light, and mobile dark widths confirmed the Bookings and Flights header remains present, the Trip.com search fields are visible without the large provider logo/header, the handoff button remains visible, and the mobile page has no horizontal overflow. After the Codex selector-scope fix, Playwright revalidated desktop dark and mobile dark viewports and confirmed the crop transform only applies inside the framed Trip.com embed. Console warnings were limited to provider-owned WebGL performance messages.

Bugs deferred: Trip.com iframe field layout inside the iframe remains provider-owned and can still compress labels on narrow screens. The visible `Open hotel search` handoff button remains the reliable mobile fallback if the iframe is blocked or visually constrained.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Developer Resources: `esc_url()` and `esc_attr()` output escaping expectations for URLs and attributes.
- WordPress Plugin Security Handbook: escaping output and protecting external provider boundaries.
- Travelpayouts Help Center: widget and White Label setup guidance for provider-controlled search widgets and partner handoff behavior.

Decision: Phase 11 remains `Completed`. PR #8 merged the focused display follow-up after Codex review, and Linear `ONE-73` was updated with the final merge evidence.

## Phase 12.1 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-74` sitemap, navigation, and page ownership map. Reviewed Phase 11 backend mode, Phase 12 plan requirements, local blueprint source, static theme templates/assets, live WordPress pages/menu, core CPT registrations, and frontend shortcodes.

Acceptance criteria result: Passed. The target navigation now includes Flights, Hotels, Explore, Deals, Trip Planner, and Saved Trips. Each core surface has a documented owner and data source. SEO page families for flights, hotels, destinations, routes, and deals are mapped. Conflicts with current templates and live menu state are documented.

Security review: Passed for documentation scope. No executable code, public routes, settings, provider calls, or database writes were added. The documented model preserves the Travelpayouts-controlled backend boundary and keeps WordPress from owning live supplier inventory, booking, or payment.

REST permission review: Not applicable for this documentation-only child task. No REST routes changed.

Database/migration review: Not applicable. No schema, migration, or data mutation changed.

UI review: Static inventory only. The live menu still contains the older Home, Flights, Hotels, About, Contact, Services, Privacy Policy, and Terms & Conditions set, so the menu/fallback mismatch is documented for later implementation before visual completion.

Regression review: Added a Phase 12 public information architecture watchlist entry for primary navigation, fallback navigation, page ownership, CPT SEO pages, and `/deals/` versus `travel-deals` archive reconciliation.

Validation performed: Static template/page inventory review; WordPress page/menu inventory through WP-CLI; CPT and shortcode code review; CSS/PHP file-size review; documentation update review. WP-CLI emitted known PHP 8.5 deprecation warnings from tooling and the Travelpayouts plugin, but commands completed.

Bugs found: Existing primary menu and theme fallback menu do not match Phase 12 navigation. Home remains a generic boilerplate page. About, Contact, and Services templates are effectively empty. Flights and Hotels currently use `index.php` rather than dedicated search templates. No local destination/route/deal content exists yet.

Bugs fixed: Corrected the stale Phase 11.6 follow-up review entry from `In Review` to `Completed` now that PR #8 was reviewed, merged, and synced to Linear. Patched the Codex P2 review finding by replacing machine-specific WP-CLI paths in the shared Phase 12 validation commands with environment-agnostic `wp` examples.

Bugs deferred: Menu reconciliation, fallback menu update, homepage/search-template rebuild, seed content/empty-state decisions, and `/deals/` versus `travel-deals` archive reconciliation are deferred to follow-on Phase 12/implementation tickets.

Documentation updated: `.plan/phase-12-sitemap-navigation-page-ownership.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Hierarchy.
- WordPress Theme Handbook: Navigation Menus.
- WordPress Block Editor Handbook.
- WordPress Theme Handbook: Including CSS and JavaScript.
- WordPress Theme Handbook: Accessibility.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: White Label Web and Widget-type setup.

Decision: `ONE-74` passed local documentation review after the Codex P2 validation-command finding was patched. Phase 12 remains `In Progress` for the remaining child tickets.

## Phase 12.2 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-75` design tokens, media strategy, component inventory, accessibility requirements, disclosure treatment, and initial Travelpayouts widget-frame guardrails. Reviewed Phase 12.1 IA output, Phase 12 plan requirements, product blueprint design-system requirements, current theme tokens/base/home CSS, theme architecture, core frontend widget styles, and Travelpayouts wrapper contracts.

Acceptance criteria result: Passed for documentation scope. Tokens and component inventory are documented; real media strategy is defined; accessibility requirements cover focus, touch targets, labels, reduced motion, text resizing, and mobile behavior; Travelpayouts widget frame guardrails are documented for later implementation.

Security review: Passed for documentation scope. No executable code, settings, routes, secrets, provider calls, migrations, or public data paths changed. The design system explicitly keeps affiliate disclosures visible and avoids exposing provider-owned booking or inventory behavior as WordPress-owned.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, migrations, options, or persisted data changed.

UI review: Documentation review only. Existing public UI still has boilerplate homepage sections, gradient fallback hero treatment, older navigation, and provider-owned hotel iframe compression risk. These remain deferred to later Phase 12 and implementation tickets.

Regression review: Added a Phase 12 design-system and widget-frame watchlist for media usage, palette drift, touch targets, disclosure visibility, widget loading states, reduced motion, focus states, and mobile overlap.

Validation performed: Reviewed official WordPress theme/style/asset/image/accessibility docs, reviewed official Travelpayouts widget and White Label docs, inventoried current static theme and core frontend CSS, confirmed CSS file sizes, and added documentation-only validation checks for the P12.2 design inventory.

Bugs found: Existing implementation still has a generic marketing homepage, gradient-heavy hero fallback, large radius tokens/plugin card radius, `header.css` close to the 600-line limit, and Trip.com provider-owned mobile compression risk.

Bugs fixed: None in runtime code. The bugs and risks are documented so Phase 12.3, Phase 12.4, and later visual work can address them in order.

Bugs deferred: CSS split and architecture preparation is deferred to Phase 12.3. Detailed widget loading/frame rules are deferred to Phase 12.4. Page-level wireframes are deferred to Phase 12.5. Homepage/search-template rebuild is deferred to later implementation phases.

Documentation updated: `.plan/phase-12-design-system-component-inventory.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Global Settings and Styles.
- WordPress Theme Handbook: Including Assets.
- WordPress Theme Handbook: Accessibility.
- WordPress Theme Handbook: Images.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: What is White Label Web by Travelpayouts?

Decision: `ONE-75` passed local documentation review and Codex PR review with no major issues on PR #10. Keep Phase 12 `In Progress` for the remaining child tickets until the full Phase 12 review gate passes.

## Phase 12.3 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-76` CSS split and static theme architecture preparation. Reviewed official WordPress asset/theme-structure docs, Travelpayouts widget placement caution, Phase 12.1 IA map, Phase 12.2 design-system inventory, current static theme enqueue logic, CSS file sizes, and core frontend widget wrapper scope.

Acceptance criteria result: Passed for local implementation scope. CSS file-size state is documented; a small mechanical split moved shared primitives out of `header.css`; future CSS module ownership and enqueueing rules are documented; no unrelated redesign was included.

Security review: Passed. No provider credentials, settings, REST routes, forms, database writes, or Travelpayouts script behavior changed. The split preserves theme-only CSS and leaves `baf-` scoped plugin widget wrapper CSS in the core plugin.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, options, migrations, or custom tables changed.

UI review: Passed for the split scope. The moved `.skip-link` and `.btn` rules are unchanged and now load through `components.css` before `header.css`, `mobile-nav.css`, `footer.css`, and page-specific CSS. Browser smoke at default desktop size and a 390px mobile viewport confirmed the new stylesheet loads, shared controls are present, mobile toggles are present, and no console errors were reported.

Regression review: Updated the Phase 12 design-system and widget-frame watchlist to keep shared primitives in `components.css` and header-only styling in `header.css`.

Validation performed: CSS file-size inventory; PHP syntax check for `themes/bookings-and-flights-static/functions.php`; enqueue/reference scan for `components.css`; `curl` asset/source checks; browser smoke at default desktop and 390px mobile viewport; documentation consistency review.

Bugs found: `header.css` was carrying shared `.skip-link` and `.btn` primitives even though those classes are used by page templates and mobile navigation, not only the header module.

Bugs fixed: Added `themes/bookings-and-flights-static/assets/css/components.css`, moved shared primitives there unchanged, and enqueued it between `base.css` and `header.css`. Updated `themes/bookings-and-flights-static/ARCHITECTURE.md` and `.plan/` docs.

Bugs deferred: `home.css` remains generic and gradient-heavy until the homepage rebuild. Mobile navigation text sizing and hardcoded transition delays need review when the Phase 12 target navigation is implemented. Detailed Travelpayouts widget-frame loading and responsive rules remain in Phase 12.4.

Documentation updated: `.plan/phase-12-css-split-theme-architecture.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`, `themes/bookings-and-flights-static/ARCHITECTURE.md`.

Research consulted:
- WordPress Theme Handbook: Including Assets.
- WordPress Theme Handbook: Theme Structure.
- WordPress Theme Handbook: Global Settings and Styles.
- Travelpayouts Help Center: Getting started with widgets.

Decision: `ONE-76` passed local CSS split, syntax, source, desktop browser, mobile browser, and Codex PR review. Codex found no major issues on PR #11.

## Phase 12.4 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-77` widget frame, loading, and responsive layout rules. Reviewed official Travelpayouts widget and White Label documentation, WordPress asset/security guidance, Phase 11 wrapper validation, Phase 12.1 page ownership, Phase 12.2 design-system rules, Phase 12.3 CSS architecture, existing White Label/Trip.com shortcode wrappers, current `baf-` frontend CSS, and Phase 13 registry expectations.

Acceptance criteria result: Passed for documentation scope. Widget frame dimensions and responsive constraints are documented; loading, empty, error, disabled, no-script, unavailable, and missing-configuration states are specified; affiliate disclosure location is defined for widget surfaces; performance constraints are captured; Phase 13 prerequisites are documented.

Security review: Passed for documentation scope. No executable code, settings, routes, migrations, or database writes changed. The rules require public output to fail closed, keep provider scripts scoped to approved placements, avoid leaking raw embed code, and keep disclosure/fallback outside provider-owned iframes.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, options, custom tables, or migrations changed.

UI review: Documentation review only. The frame rules specify stable min-heights, mobile overflow containment, visible fallback handoff links, iframe titles, no-script states, and no hidden affiliate disclosure on mobile.

Regression review: Updated the Phase 12 design-system and widget-frame watchlist to point future work at the widget-frame rules document.

Validation performed: Responsive wireframe review, layout-shift risk review, existing shortcode wrapper/CSS inventory, Phase 13 registry prerequisite review, and documentation anchor checks.

Bugs found: Existing Trip.com direct partner iframe requires a carefully documented crop/fallback exception because provider-owned content can compress on mobile or be blocked. Existing White Label wrapper has a minimal frame reservation but not a full reusable registry state model yet.

Bugs fixed: None in runtime code. The layout risks and registry prerequisites are documented before Phase 13 implementation.

Bugs deferred: Runtime registry implementation, exact real-widget tuning, route/map/calendar widget smoke tests, and broader public template integration are deferred to Phase 13 and later experience phases.

Documentation updated: `.plan/phase-12-widget-frame-layout-rules.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: What is White Label Web by Travelpayouts?
- WordPress Theme Handbook: Including Assets.
- WordPress Plugin Security Handbook: Securing Input and Securing Output.

Decision: `ONE-77` passed local documentation review and Codex PR review with no major issues on PR #12. Keep Phase 12 `In Progress` for remaining wireframe and final review tickets.

## Phase 12.5 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-78` page-level wireframes for home, flights, hotels, explore, destination detail, route detail, deals, AI planner, saved trips, about/legal, and admin widget placement surfaces. Reviewed official WordPress template hierarchy/templates/theme structure/asset loading documentation, Travelpayouts widget and White Label Widget documentation, Phase 12.1 through Phase 12.4 outputs, current static theme templates, current core CPTs, shortcodes, and the Travelpayouts-first blueprint.

Acceptance criteria result: Passed for local documentation scope. Each requested core surface has a structured wireframe or layout spec; mobile and desktop behavior is covered; widget, disclosure, and CTA placement is explicit; template ownership and follow-up phase mapping are documented; a browser screenshot plan is documented for later UI implementation.

Security review: Passed for documentation scope. No executable code, REST routes, settings, migrations, or database writes changed. The wireframes preserve the Travelpayouts-controlled backend boundary, avoid direct checkout, require visible disclosures, and keep raw provider scripts behind the future governed registry.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, options, custom tables, or migrations changed.

UI review: Documentation review only. The wireframes cover mobile, tablet, and desktop structure; no-overflow widget frames; disclosure placement; accessible search/control expectations; empty states for missing CPT content; and screenshot validation requirements for later visual implementation.

Regression review: Updated the Phase 12 design-system/watchlist documentation so future template and visual work points to `.plan/phase-12-page-level-wireframes.md`.

Validation performed: Responsive wireframe review, acceptance criteria cross-check against the blueprint, current theme/template inventory, CPT/shortcode ownership review, and browser screenshot plan documentation.

Bugs found: Current `page-home.php` remains generic and gradient-heavy; Flights and Hotels still render through the fallback `index.php`; Explore, Deals, Trip Planner, and Saved Trips pages are not yet implemented; local destination, route, and deal seed content is absent.

Bugs fixed: None in runtime code. The missing template and content risks are documented with owner/template/phase mapping before implementation.

Bugs deferred: Runtime templates, visual CSS, menu updates, seed content, Phase 13 widget registry, and browser screenshot execution are deferred to their mapped follow-up phases.

Documentation updated: `.plan/phase-12-page-level-wireframes.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Hierarchy.
- WordPress Theme Handbook: Templates.
- WordPress Theme Handbook: Theme Structure.
- WordPress Theme Handbook: Including Assets.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: `ONE-78` passed local documentation review and Codex PR review with no major issues on PR #13. Keep Phase 12 `In Progress` for the final review ticket.

## Phase 12.6 Review - 2026-05-12

Status: `Completed after runtime validation follow-up`

Reviewer: Codex

Scope reviewed: `ONE-79` final Phase 12 review and documentation gate. Reviewed Phase 12 objective, acceptance criteria, Phase 12.1 sitemap/navigation/page ownership, Phase 12.2 design-system inventory, Phase 12.3 CSS architecture, Phase 12.4 widget-frame rules, Phase 12.5 page-level wireframes, architecture baseline, regression watchlist, known issues, current static theme templates, and CSS file sizes.

Acceptance criteria result: Passed for Phase 12 design/documentation scope. Phase 12 now documents IA, design system, CSS ownership, widget-frame behavior, page wireframes, deferred runtime implementation risks, and Phase 13 prerequisites. Runtime homepage/search/template implementation remains intentionally deferred to mapped follow-up phases.

Security review: Passed for documentation scope. No executable code, REST routes, settings, migrations, or database writes changed. The Phase 13 checklist preserves provider consent, capability gates, nonces, sanitization, escaping, secret masking, disclosure output, and the Travelpayouts-controlled backend boundary.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, options, custom tables, or migrations changed.

UI review: Initial PR #14 documentation review confirmed current runtime gaps were documented and responsive wireframes passed at the documentation level. The runtime validation follow-up below records the required browser screenshot and keyboard navigation execution.

Regression review: Updated the Phase 12 watchlist and known issues so future Phase 13+ work starts from the completion gate rather than rediscovering IA/design decisions.

Validation performed: Static template review, responsive wireframe review, CSS file-size review, documentation review, and `git diff --check`. Runtime screenshots and keyboard navigation review are recorded in the follow-up section below.

Bugs found: Current runtime still has older menu/fallback menu and `Get Started` CTA behavior, generic homepage sections, fallback `index.php` rendering for Flights/Hotels, missing Explore/Deals/Trip Planner/Saved Trips pages, missing seed content for destination/route/deal screenshot validation, and no Phase 13 widget registry yet.

Bugs fixed: None in runtime code. Deferred runtime gaps are documented in `.plan/phase-12-completion-gate.md` and `.plan/known-issues.md`.

Bugs deferred: Runtime templates, visual CSS, menu updates, seed content, widget registry implementation, and screenshot-backed visual QA for later implementation phases are deferred to Phase 13 and later mapped product phases.

Documentation updated: `.plan/phase-12-completion-gate.md`, `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Hierarchy.
- WordPress Theme Handbook: Templates.
- WordPress Theme Handbook: Theme Structure.
- WordPress Theme Handbook: Including Assets.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: PR #14 initially could not close the Phase 12 gate because Codex review found a P2 consistency issue: `.plan/phased-implementation.md` still required browser screenshots and keyboard navigation review, while the completion gate had deferred those checks. The runtime validation follow-up below resolved that blocker.

## Phase 12.6 Runtime Validation Follow-Up - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-79` runtime screenshot and keyboard-navigation blocker from PR #14. Reviewed the local WordPress runtime at `http://bookings-and-flights.local`, Home, Flights, Hotels, planned-but-unpublished routes, static theme header/mobile menu behavior, and the core Travelpayouts White Label and Trip.com shortcode wrappers.

Acceptance criteria result: Passed for the Phase 12 gate after executing the previously missing browser screenshots and keyboard review. Home, Flights, and Hotels were captured at desktop `1440x1000`, tablet `1024x900`, and mobile `390x844`. Explore, Deals, Trip Planner, and Saved Trips still return WordPress `404` pages and remain deferred implementation work.

Security review: Passed for this follow-up. No secrets, API tokens, authorization strings, checkout, payment, refund, or direct WordPress booking flow appeared in the Flights or Hotels output scans. Provider request consent remained enabled for the validated runtime surfaces.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Not applicable. No database schema, options, custom tables, or migrations changed. The admin toolbar user preference was temporarily hidden for Browser screenshot diagnosis and restored to its original `true` value.

UI review: Passed with one runtime keyboard bug fixed. The desktop header, mobile header, and mobile menu have visible focus states; mobile menu opens by keyboard, updates `aria-expanded` and `aria-hidden`, moves focus into menu links, traps focus, and closes with `Escape`. The Travelpayouts flight widget focus mount now has a visible WordPress-owned outline. The Trip.com iframe remains keyboard reachable and the WordPress wrapper adds a visible focus outline while iframe focus is active; the sponsored `Open hotel search` link remains the next keyboard-accessible handoff path.

Regression review: Public widget output still renders the Flights White Label mount and Hotels Trip.com iframe plus handoff link. The core frontend CSS remains under the 600-line guideline at 240 lines.

Validation performed: Codex in-app Browser screenshot pass before Browser fallback; Playwright runtime screenshots for Home, Flights, and Hotels at `1440x1000`, `1024x900`, and `390x844`; Playwright keyboard traces for desktop header, mobile header, mobile menu, Flights widget focus, and Hotels handoff focus; route checks for `/explore/`, `/deals/`, `/trip-planner/`, and `/saved-trips/`; PHP syntax checks for changed shortcode files; `bookings-flights-core` deactivate/reactivate smoke check; source scan for hotel iframe focus bridge, Flights `tpwl-search`, and sensitive term scan that found only the public theme `tokens.css` design-token asset; `git diff --check`.

Bugs found: The Travelpayouts White Label mount could receive keyboard focus without a visible WordPress-owned focus outline. The Trip.com provider iframe could become a keyboard stop without a reliable WordPress-owned visible focus indicator. The live mobile menu opened with correct ARIA state, but the first focus handoff was too early and left focus on the toggle instead of moving into the menu.

Bugs fixed: Added focus outlines for Travelpayouts White Label mount points and added a small focus bridge so the Trip.com wrapper shows a visible outline while the keyboard-reachable provider iframe has focus. Codex review on PR #15 specifically requested keeping the iframe keyboard-reachable, and the follow-up patch preserves that behavior. Delayed the mobile menu focus handoff enough for the opened panel to accept focus and used `preventScroll` when focusing the first mobile-nav link.

Bugs deferred: Runtime homepage content remains placeholder/generic. The live primary menu is still missing Explore, Deals, Trip Planner, and Saved Trips. Flights and Hotels still render through `index.php` until dedicated templates land. Provider-owned Travelpayouts White Label scripts emit console warnings about React JSX source maps and duplicate GraphQL fragment names. These remain deferred to Phase 13 and later mapped implementation phases.

Documentation updated: `.plan/phase-12-completion-gate.md`, `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Including Assets.
- WordPress Plugin Security Handbook: Security overview.
- Existing Phase 12 research records for WordPress template hierarchy, theme structure, and Travelpayouts widget/White Label behavior.

Decision: `ONE-79` can move to Done after PR review and merge. Phase 12 can be marked `Completed`; runtime visual/template implementation remains in Phase 13 and later mapped phases.

## Phase 13.1 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-80` placement data model and registry service. Reviewed Phase 11 backend mode, Phase 12 widget-frame prerequisites, Phase 13 objective and child order, current `bookings-flights-core` settings/options/capability/service patterns, existing White Label and Trip.com shortcode wrappers, and Travelpayouts SubID/widget guidance.

Acceptance criteria result: Passed for P13.1 implementation scope. The registry data model includes placement key, name, vertical, context, widget family, render mode, embed source/mode/reference/url, status, SubID pattern, public surfaces, consent flag, disclosure copy/rule, frame reservations, fallback metadata, and notes. The service sanitizes writes, installs an idempotent non-autoloaded option, and seeds current Flights/Hotels placements from existing safe settings. Admin UI and frontend rendering remain in later Phase 13 child issues.

Security review: Passed for the service seam. Private embed references, embed URLs, and admin notes are available through capability-gated admin service calls requiring `manage_baf_affiliates` or `manage_baf_settings`; active configured placements also have a trusted server-side rendering read path so shortcodes/blocks do not bypass the registry. Public placement projections strip private embed values and notes. Pasted Travelpayouts script/iframe/link snippets are reduced to approved URLs or references rather than storing arbitrary raw embed code.

REST permission review: Not applicable. No REST route was added in P13.1. Future REST or block-editor consumers must use the public projection unless the request is already capability-gated; the rendering read path is for trusted PHP renderers only.

Database/migration review: Passed. No custom table was introduced. `baf_travelpayouts_widget_registry` is installed as a WordPress option using the Options API, is non-autoloaded, is normalized non-destructively on bootstrap/activation, and preserves malformed stored placements during normalization so a page load cannot silently delete recoverable registry data.

UI review: Not applicable for runtime UI. P13.1 creates the storage/service layer only; admin management UI remains in `ONE-81`.

Regression review: Existing `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes remain unchanged. Existing `baf_travelpayouts_settings` fields remain intact and are used only to seed the first registry records when the registry option is missing.

Validation performed: PHP syntax checks for the new service, plugin bootstrap, and activator; option/service smoke check for install, public/private projection, trusted rendering read path, capability gate, sanitizer, malformed placement preservation, non-approved iframe path rejection, dashboard script URL rejection in iframe mode, temporary administrator save, temporary delete, SubID normalization, and idempotent registry normalization; plugin deactivate/reactivate smoke check; `git diff --check`; Codex review on PR #16, including follow-up fixes for all posted findings and final no-major-issues result on the latest implementation head.

Bugs found: Initial bootstrap called the registry installer during `plugins_loaded`, which triggered WordPress's just-in-time translation warning because default placement strings passed through translation functions too early. Codex review on PR #16 found that registry normalization was touching placement `updated_at` values, that future unauthenticated frontend renderers would not have a service-owned path to private active embed data, that malformed stored placements could be dropped during bootstrap normalization, and that iframe mode could accept allowlisted dashboard script URLs that are not valid iframe sources.

Bugs fixed: Moved runtime registry installation to `init` and made stored default placement copy plain data instead of translated UI strings. Updated registry sanitization so bootstrap/normalization preserves existing placement timestamps and malformed stored placements, while only actual saves refresh `updated_at`. Added a trusted server-side rendering read path for active, configured placements. Restricted iframe mode to known iframe-like partner paths and explicitly rejects widget script paths.

Bugs deferred: Admin UI, nonces, frontend shortcode/block wrapper, public configured/missing/disabled/loading/no-script states, and full Phase 13 security review remain in the later mapped Phase 13 issues.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Settings API: settings forms, nonces, sanitization, and capability behavior.
- WordPress Options API: storing, retrieving, and updating named options.
- WordPress Plugin Security Handbook: capability checks, input sanitization, and output/privacy boundaries.
- Travelpayouts Help Center: ID/SubID affiliate marker guidance.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: `ONE-80` can move to Done after PR #16 merge. Phase 13 remains `In Progress` for the remaining registry UI, wrapper, state, security, and review child issues.

## Phase 13.2 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-81` admin UI for Travelpayouts widget placement management. Reviewed Phase 13 objective, P13.1 registry service, current admin menu/settings/report patterns, capability contracts, nonce/write handling requirements, existing Travelpayouts White Label and Trip.com seeded placements, and Phase 12 runtime screenshot/keyboard expectations.

Acceptance criteria result: Passed locally for the PR candidate. The `baf-widget-placements` submenu lists approved placements, summary state cards, missing-configuration hints, edit/delete actions, and a create/edit form. Users with `manage_baf_affiliates` or `manage_baf_settings` can manage placements; unauthorized users cannot read private placement data through the registry service. Empty, saved, active, disabled, error, and missing-configuration states are present.

Security review: Passed locally. Save/delete handlers run through `admin-post.php`, check capabilities, verify nonces, and route writes through `Travelpayouts_Widget_Registry_Service`. Posted form data uses `wp_unslash()` and sanitization before the registry service performs final normalization. Rendered table rows, notices, dashboard cards, and public placement reads do not print private embed references or URLs. Raw embed values are visible only in the capability-gated edit form.

REST permission review: Not applicable. No REST routes changed.

Database/migration review: Passed. No custom table or schema migration changed. The existing non-autoloaded `baf_travelpayouts_widget_registry` option remains the storage contract.

UI review: Passed locally with runtime browser evidence. Playwright captured desktop and mobile admin screenshots against `http://bookings-and-flights.local/wp-admin/admin.php?page=baf-widget-placements`, performed a create/update-to-disabled smoke flow, verified clean post-cleanup screenshots, and traced keyboard focus through the form fields without traps or hidden focused controls.

Regression review: Existing seeded Flights White Label and Hotels partner search placements remained present after the smoke placement was deleted. Existing admin Settings, Integrations, Background Jobs, and Reports slugs remain registered, and the admin CSS remains under the file-size limit.

Validation performed: PHP syntax checks for changed admin PHP files; `git diff --check`; WP-CLI menu registration smoke for administrators and affiliate-only managers; WP-CLI page-render smoke confirming no raw Trip.com or Travelpayouts script URL printed on the listing page; WP-CLI subscriber/private-read denial plus administrator create/delete smoke; Playwright desktop/mobile screenshots and keyboard navigation review; browser console/page-error check; temporary smoke placement/user cleanup verification; `debug.log` related-error tail review; `bookings-flights-core` deactivate/reactivate smoke check; Codex review on PR #17 found one PHP-version compatibility issue, and the follow-up patch removed the only `never` return type.

Bugs found: Initial WP-CLI attempts failed against the default `localhost` MySQL socket; the Local site requires passing `/Users/djcavy/Library/Application Support/Local/run/qRHZasMmV/mysql/mysqld.sock` through `mysqli.default_socket`. The first Playwright cleanup trap had a shell-quoting issue after successful browser validation. Local code review also found that an affiliate-only manager could pass the placement capability but miss the Bookings & Flights parent menu because the parent menu was still settings-only. Codex review found that `Widget_Placements_Page::redirect()` used PHP 8.1's `never` return type even though the plugin advertises PHP 8.0 support.

Bugs fixed: Reran WP-CLI with the Local MySQL socket, cleaned the temporary smoke placement and temporary admin user directly, verified cleanup state, and captured clean final screenshots after cleanup. Updated the parent admin menu capability/callback so affiliate-only managers receive the Bookings & Flights parent menu and Widget Placements submenu without exposing Settings. Replaced the `never` return type with PHP 8.0-compatible `void` and confirmed no `never` return types remain in the core plugin includes.

Bugs deferred: Frontend shortcode/block rendering from the registry, public configured/missing/disabled/loading/no-script/error states, richer placement search-surface mode handling, and the full Phase 13 completion review remain in later child issues.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Plugin Handbook: Administration Menus.
- WordPress Plugin Security Handbook: Checking User Capabilities.
- WordPress Plugin Security Handbook: Nonces.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.

Decision: `ONE-81` can move to Done after PR #17 merge. Keep Phase 13 `In Progress` until the remaining wrapper/state/security child issues pass review and merge.

## Phase 13.3 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-82` safe shortcode/block frontend wrapper. Reviewed Phase 13 objective and child order, P13.1 registry service trusted rendering path, P13.2 admin placement management, Phase 12 widget-frame rules, existing White Label and Trip.com direct shortcodes, WordPress shortcode/block registration guidance, WordPress sanitization/escaping guidance, and Travelpayouts widget/White Label placement guidance.

Acceptance criteria result: Passed locally for the PR candidate. `[baf_travelpayouts_widget placement="..."]` and the `baf/travelpayouts-widget` dynamic block can render approved active placements by registry key. Disabled and missing placements render safe escaped states. Public output includes disclosure copy and visible partner handoff language where monetized. Editor/block content stores only placement/context attributes, while raw embed management stays in the capability-gated registry/admin layer.

Security review: Passed locally. The renderer uses `Travelpayouts_Widget_Registry_Service::get_for_rendering()` for trusted server-side reads and does not expose private registry values through public projections, REST routes, or editor attributes. All shortcode/block attributes are sanitized. Public markup escapes text, attributes, and URLs late. Provider output remains gated by `baf_consent_settings.allow_provider_requests`; when consent is disabled, public visitors receive no third-party provider output.

REST permission review: Passed. No REST route was added. The dynamic block renders server-side and stores only safe block attributes in post content.

Database/migration review: Passed. No schema or option contract changed. The existing non-autoloaded `baf_travelpayouts_widget_registry` option remains the storage contract.

UI review: Passed locally with runtime browser evidence. A temporary public smoke page rendered an active Hotels partner placement via the block, a disabled test placement via shortcode, and a missing placement via shortcode. Playwright desktop and mobile screenshots confirmed disclosure, the cropped Trip.com widget surface, visible `Open hotel search` handoff, disabled/missing states, and no horizontal overflow. Keyboard review confirmed focus reaches the Trip.com iframe and then the visible handoff link. A Codex review follow-up also rendered multiple White Label wrapper and legacy shortcode combinations in both DOM orders and confirmed there are no duplicate `tpwl-search`/`tpwl-tickets` IDs, unavailable fallbacks are visible, and unavailable placeholder nodes are skipped by keyboard navigation.

Regression review: Existing `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes remain registered for setup compatibility. The legacy White Label shortcode now shares the duplicate-ID conflict guard used by the registry wrapper so old and new White Label placements can coexist without duplicate mount IDs. The new Settings copy points editors to the registry wrapper while preserving the older direct setup wrappers. Temporary smoke pages and the temporary disabled placement were deleted after validation.

Validation performed: PHP syntax checks for changed PHP files; `node --check` for the block editor script; WP-CLI shortcode/block registration and render smoke using the Local MySQL socket; sensitive-term scan for rendered shortcode/block output; active, disabled, missing, disclosure, handoff, no-script, and SubID data checks; real Playwright Chromium screenshots at `1440x900` and `390x844`; keyboard focus review through iframe and handoff link; console/page-error capture; temporary smoke content cleanup; `git diff --check`. Codex review on PR #18 found White Label duplicate-ID risk, blank context default issues, missing public-surface enforcement, and a one-shot dashboard-script fallback check; follow-up patches reran PHP syntax, `git diff --check`, shortcode/block default-context smoke checks, allowlisted/disallowed surface smoke checks, script-widget delayed-load state checks, real page-level browser checks, and Playwright desktop/mobile/keyboard checks against new-first and legacy-first White Label combinations.

Bugs found: The Codex in-app Browser connected but rejected the selected tab as stale and then could not create a new active browser pane. Browser validation therefore used Playwright Chromium with the fallback reason recorded. Local review also found that no-script fallback should not turn widget script URLs into visible handoff links, so script-like fallback URLs are now ignored for handoff output. Codex review found that fixed White Label mount IDs could collide when the new wrapper is rendered more than once or alongside the legacy White Label shortcode. Codex review also found that blank `surface`/`slug` attributes from shortcode and block defaults could override the renderer's current page-context fallback. Codex review found that active placements could be rendered on surfaces not listed in their `public_surfaces` allowlist. Codex review found that dashboard-script widgets used a one-shot 2.5 second iframe check, which could leave a false unavailable state if a provider script initialized late.

Bugs fixed: Added the renderer guard that suppresses `.js`, `/content`, and `/wl_web/` URLs as visible handoff targets. Reworked the registry wrapper and legacy White Label shortcode to render unique placeholder IDs, claim the Travelpayouts-required fixed IDs only when no active instance already owns them, show a visible unavailable fallback for additional instances, and remove unavailable placeholders from the tab order. Updated attribute normalization so empty `surface` and `slug` values fall back to the current WordPress surface and slug before SubID generation. Added a renderer-side `public_surfaces` allowlist gate that returns a safe unavailable state before provider output when a placement is requested from an unapproved surface. Reworked dashboard-script widgets to create the provider script under a mutation observer, script load/error handlers, and repeated polling so late iframe initialization clears the fallback state. Retried browser validation with Playwright, captured desktop/mobile/keyboarding screenshots, and verified cleanup state.

Bugs deferred: P13.4 still owns richer centralized SubID mutation into provider URLs, fuller consent messaging, and the broader configured/loading/no-script state model. P13.5 still owns the full Phase 13 security, capability, nonce, and exposure review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Shortcode API.
- WordPress Block Editor Handbook: block registration.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: `ONE-82` can move to Done after PR review and merge. Keep Phase 13 `In Progress` until P13.4, P13.5, and P13.6 pass review and merge.

## Phase 13.4 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-83` SubID, disclosure, consent, disabled, loading, and no-script frontend states. Reviewed Phase 13 objective and child order, P13.1 registry service, P13.2 placement admin, P13.3 frontend renderer/block wrapper, WordPress shortcode output/security guidance, WordPress sanitization/escaping guidance, and Travelpayouts marker/SubID guidance.

Acceptance criteria result: Passed locally for the PR candidate. Runtime SubIDs are generated through `Travelpayouts_Widget_Subid_Service` using `{channel}_{surface}_{vertical}_{slug}_{placement}`, normalized to lowercase Latin letters, numbers, and underscores, and applied to iframe, dashboard-script, White Label results/script, handoff, official-shortcode, and no-script URLs. Disclosure appears for monetized placements. Configured, loading, consent-disabled, disabled, missing-configuration, unavailable, and no-script states render escaped, styled, accessible copy.

Security review: Passed locally. Consent-disabled rendering returns public state copy and disclosure without third-party iframe/script/handoff output. URL mutation preserves an existing `marker` partner ID and appends the normalized SubID as `marker=partner.subid`; otherwise it uses a `subid` query value without inventing provider credentials. Rendered smoke output did not contain API-token, authorization, bearer, secret, checkout, payment, or refund terms.

REST permission review: Not applicable. No REST route was added or changed.

Database/migration review: Passed. No custom table, migration, or option schema changed. Temporary validation placements were written through the existing capability-gated registry service and deleted afterward.

UI review: Passed locally with runtime browser evidence. Playwright desktop and mobile screenshots confirmed the wrapper rendered nonblank with no horizontal overflow; loading state showed a visible blue `role="status"` message; loaded state cleared the loading message; consent-disabled state showed the configured public notice without provider scripts/iframes; disabled and missing-configuration states remained visible. Keyboard review confirmed focus reaches the Trip.com iframe and then the visible `Open hotel search` handoff link.

Regression review: Existing P13.3 wrapper contracts remain intact: public-surface allowlist checks still run before provider output, no-script handoffs skip script-like URLs, dashboard-script widgets still recover from late provider iframe initialization, and White Label placeholder nodes remain out of keyboard order until provider content mounts. Existing legacy direct setup shortcodes remain available.

Validation performed: PHP syntax checks for changed PHP files; file-size review for changed PHP files; `git diff --check`; WP-CLI shortcode smoke checks for active iframe marker mutation, dashboard-script loading markup, consent-disabled rendering, disabled and missing-configuration states, no-script output, and sensitive-term scans; Playwright Chromium screenshots at desktop and mobile widths; keyboard focus path capture through iframe and handoff; consent-disabled browser screenshot confirming no provider iframe/script; temporary page/placement cleanup; `debug.log` tail review.

Bugs found: Initial renderer cleanup had indentation drift in `sprintf()` blocks from patching. Local review also found that the White Label loading state could clear when the vendor script loaded even if provider content had not mounted yet. The first browser timing attempt captured the dashboard-script widget after it had already loaded instead of during loading, so the loading screenshot was rerun with a deterministic delayed mock script.

Bugs fixed: Corrected renderer indentation. Updated White Label rendering so placeholder containers start out of tab order, loading remains until provider content appears, and a timeout/error moves to unavailable instead of falsely marking loaded. Reran runtime browser validation with a deterministic delayed script to prove the visible loading state.

Bugs deferred: P13.5 still owns the broader full Phase 13 security/capability/nonce/exposure review. Provider-owned Travelpayouts scripts may still emit their own console warnings when real external scripts are used, and the known WP-CLI/Travelpayouts PHP 8.5 deprecation noise remains an environment compatibility watch item.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Shortcode API.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: `ONE-83` moved to Done after PR #19 review and merge. Phase 13 remained `In Progress` until P13.5 and P13.6 completed.

## Phase 13.5 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-84` security, capability, nonce, and exposure review for the Phase 13 Travelpayouts widget registry and safe embed layer. Reviewed the registry service, placement admin screen, placement form, admin-post save/delete actions, Settings API provider fields, shortcode/block render path, frontend renderer, frontend block script, core `baf/v1` REST controllers, affiliate bridge public route boundaries, option storage, and documentation contracts.

Acceptance criteria result: Passed locally for the PR candidate. Protected private registry reads and writes require `manage_baf_affiliates` or `manage_baf_settings`; placement admin writes use admin-post handlers with nonces and capability checks; core REST endpoints have endpoint-specific permission callbacks; public placement projections and frontend output expose only safe placement metadata/output; and no new raw embed, token, secret, private note, or credential exposure was found.

Security review: Passed locally. Anonymous registry private reads, saves, and deletes return forbidden errors. Administrator probe saves are sanitized before storage. Public projections strip private `embed.reference`, `embed.url`, and `notes`. Missing admin-post nonce checks fail with the expected expired-link response and do not create a placement. Settings secret fields render saved API/AI credentials only as masked empty password inputs. Frontend widget output did not contain private admin notes, saved secret values, `api_token`, `api_key`, authorization, or bearer terms.

REST permission review: Passed locally for the current core plugin routes. `destinations`, `routes`, `affiliate/click`, and `ai/itinerary` use endpoint-specific permission callbacks. Anonymous AI itinerary POST is rejected, while the intended public destination collection remains readable. The existing affiliate bridge `/config` and `/postback` routes are outside the widget registry implementation but were inventoried because they share `baf/v1`: `/config` returned no secret-key names or values, and `/postback` rejected requests without the shared secret.

Database/migration review: Passed. No custom table, migration, schema, or option contract changed. Temporary validation placements and temporary option overrides were cleaned up.

UI review: Passed for security-review scope. No UI surface changed in this issue, so no new screenshot gate was required. Prior P13.2 admin and P13.3/P13.4 frontend runtime screenshots remain the current visual evidence for the affected surfaces.

Regression review: Existing registry storage, public projection, renderer consent gates, surface allowlist, SubID generation, secret masking, REST route contracts, and affiliate bridge public route boundaries remain consistent with `.plan/architecture-baseline.md` and `.plan/regression-watchlist.md`.

Validation performed: Source scans for REST routes, admin-post handlers, nonce checks, capability checks, registry option use, raw embed fields, and secret-related terms; WP-CLI registry permission/exposure probes; REST route inventory and permission probes; affiliate bridge config/postback exposure checks; Settings API secret masking check; frontend shortcode exposure check; missing-nonce admin-post failure check; temporary probe cleanup checks.

Bugs found: The first REST inventory script treated the WordPress namespace index route `/baf/v1` as a product endpoint and flagged its discovery handler. The first frontend exposure probe expected `Src <x>` to normalize to `src`, but the sanitizer correctly preserved the scalar `x` as `src_x`.

Bugs fixed: No production code bug was found. The validation scripts were narrowed to actual product endpoint routes and rerun with the correct SubID expectation. Documentation was updated with the Phase 13.5 security gate result and future regression checks.

Bugs deferred: The existing affiliate bridge public `/config` and `/postback` route contracts remain on the watchlist because they are intentionally public; future edits must preserve non-secret config output and shared-secret postback rejection. The known WP-CLI/PHP 8.5 deprecation noise remains an environment watch item.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Plugin Security Handbook.
- WordPress Plugin Handbook: Checking User Capabilities.
- WordPress Common APIs Handbook: Nonces.
- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: `ONE-84` moved to Done after PR #20 review and merge. Phase 13 remained `In Progress` until P13.6 completed.

## Phase 13.6 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-85` final Phase 13 review and documentation gate. Reviewed the Phase 13 objective, P13.1 through P13.5 implementation notes, registry storage contracts, admin placement management, frontend shortcode/block wrapper, SubID/disclosure/consent states, security/exposure review, REST route boundaries, Phase 14 consumption requirements, and current runtime behavior.

Acceptance criteria result: Passed. Registry and wrapper behavior is documented in `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, and `.plan/regression-watchlist.md`. Phase 14 has stable approved placement keys, the shortcode/block wrapper, the trusted renderer, public-safe projection boundaries, consent-disabled behavior, disclosure output, SubID metadata, and visible handoff behavior to consume. Bugs found, fixed, deferred, and watchlist items are recorded.

Security review: Passed locally. Anonymous users cannot read private registry data or save/delete placements. Missing admin nonces fail before writes and did not leave the probe placement behind. The runtime frontend wrapper did not expose private notes, saved secret values, `api_token`, `api_key`, authorization, bearer, raw registry notes, or raw private embed fields. Consent-disabled rendering returned disclosure plus a safe public status message with no provider iframe, provider script, or handoff link.

REST permission review: Passed locally for the current gate. Core `baf/v1` routes retain endpoint-specific permission callbacks; anonymous valid AI itinerary POST returned the expected forbidden response; public destination collection remained readable. The affiliate bridge public `/config` route returned no secret-key names or values, and `/postback` rejected a request without the shared secret.

Database/migration review: Passed. No custom table, migration, or schema contract changed in P13.6. Temporary runtime page and temporary consent override were cleaned up, and the registry probe placement was not created by the failed nonce check.

Admin UI review: Passed for the review-gate scope. The existing `baf-widget-placements` capability/nonce model remains documented and was rechecked through anonymous registry denial plus missing-nonce write failure. No new admin UI code changed in P13.6.

Frontend UI review: Passed with real runtime browser evidence. The Codex in-app Browser connected but lost its active pane during navigation, so the final required runtime pass used Playwright Chromium against the local WordPress page. Desktop and mobile screenshots showed nonblank configured output with disclosure, iframe, and visible `Open hotel search` handoff, no horizontal overflow, and no page errors. Keyboard review confirmed focus reaches the Trip.com iframe and then the visible handoff link. Consent-disabled screenshot confirmed no provider output.

Regression review: Existing Phase 13 watchlist items remain valid. Phase 14 should use `flights_white_label_search` and `hotels_partner_search` through the wrapper/renderer seam instead of raw embed snippets. Provider-owned Chromium WebGL performance warnings and known WP-CLI/PHP 8.5 deprecation noise remain environment/provider watch items, not production code blockers.

Validation performed: Full PHP syntax check for `plugins/bookings-flights-core`; `node --check` for `assets/js/travelpayouts-widget-block.js`; `git diff --check`; plugin active check; anonymous registry private read/save/delete denial; missing admin nonce failure with no placement write; REST public/protected route probes; affiliate bridge public config/postback exposure probes; real browser desktop/mobile screenshots; keyboard focus capture through iframe and handoff; consent-disabled screenshot and no-provider-output assertion; temporary page and option cleanup.

Bugs found: No production code bug was found in P13.6. The Codex in-app Browser surface lost its active pane during navigation after initially connecting, so the required runtime pass used standalone Chromium. Runtime console capture showed only Chromium WebGL performance warnings from the embedded provider context.

Bugs fixed: None in production code. Documentation was updated to mark Phase 13 complete, record final gate evidence, and clarify the Phase 14 consumption seam.

Bugs deferred: Provider-owned iframe/runtime warnings, the known WP-CLI/PHP 8.5 deprecation noise, and the affiliate bridge public route contracts remain on the regression watchlist. Homepage/template implementation remains Phase 14 scope.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Shortcode API.
- WordPress Plugin Security Handbook.
- WordPress Plugin Handbook: Checking User Capabilities.
- WordPress Common APIs Handbook: Nonces.
- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: Phase 13 passed the completion gate and is documented as `Completed`. `ONE-85` can move to Done after the PR is reviewed, merged, and Linear is synced.

## Phase 14.1 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-86` hero media, primary/mobile navigation, and homepage flight/hotel search shell. Reviewed Phase 14 objective, Phase 12 IA/design/wireframe outputs, Phase 13 widget registry consumption requirements, current static theme templates/assets, active stored WordPress menu state, local WordPress runtime, and provider-owned Flights/Hotels handoff pages.

Acceptance criteria result: Passed for P14.1 scope. Header output now includes planned product sections even when the stored WordPress menu is stale. The homepage first viewport uses local real travel media, visible product copy, and Flights/Hotels search forms first. Search forms carry Phase 13 placement-key metadata and submit to the existing Travelpayouts-controlled Flights and Hotels pages. Affiliate transparency is visible directly under the search shell.

Security review: Passed locally. Template output uses escaped URLs, the fallback hero image is local instead of hotlinked, no raw provider snippets or private registry values were copied into the theme, and the rendered homepage source did not expose API tokens, API keys, access tokens, authorization headers, bearer strings, postback secrets, or secret terms.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.1.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.1.

UI review: Passed locally with real runtime screenshots. Desktop, tablet, and mobile screenshots showed a nonblank image-led homepage, readable hero text, visible Flights and Hotels search controls, visible disclosure, product navigation, no horizontal overflow, and no obvious text overlap. Mobile menu review confirmed all seven product links plus the Plan trip CTA are keyboard reachable.

Regression review: The hotel search uses `travel_destination` rather than the reserved `destination` query var. The flight search uses the provider-style `origin`, `destination`, `depart_date`, and `return_date` keys while the `/flights/` request guard prevents WordPress from treating the destination query string as a destination CPT lookup. The menu fallback preserves Flights/Hotels links and uses homepage anchors for Explore, Deals, Trip Planner, and Saved Trips until those planned pages exist. The stored-menu eligibility check now requires top-level current-site targets before bypassing the fallback, without requiring exact menu labels. Provider-owned Flight page URL normalization and known Travelpayouts console warnings remain watchlist items for later search-surface work.

Validation performed: PHP syntax checks for `page-home.php`, `header.php`, and `functions.php`; `git diff --check`; WP-CLI product nav fallback smoke check; WP-CLI stored-menu eligibility checks for stale, customized-label, and external-host menus; `/flights/` provider-query HTTP smoke; live HTTP/source smoke check; Playwright desktop/tablet/mobile screenshots; Playwright desktop keyboard focus review; Playwright mobile menu keyboard trap review; post-review desktop/mobile menu screenshot rerun; flight and hotel search submit smoke; frontend secret-term scan.

Bugs found: The first implementation hotlinked Unsplash for fallback hero media, which would create an unnecessary third-party image request. Codex PR review found that the stored-menu eligibility check only matched labels, so nested or stale menu items could bypass the product fallback. Follow-up Codex review found that absolute URLs from an old or external host could still match by path/fragment. Final Codex review found that the homepage flight form used local query names that the flight/White Label surface did not consume, and that exact menu-label matching would override intentionally customized menus. The Codex in-app Browser surface had no active pane, so Playwright Chromium was used for the required real runtime review. Downstream Travelpayouts Flight page scripts still emit known provider-owned React source-map and duplicate GraphQL fragment warnings.

Bugs fixed: Moved the fallback hero media into `themes/bookings-and-flights-static/assets/images/home-hero-beach.jpg` and updated the template to load it locally. Tightened header link spacing and mobile navigation sizing so the expanded product menu remains usable. Updated the stored-menu eligibility guard to consider only top-level menu items and verify current-site host plus expected product targets before trusting a configured WordPress menu. Updated the homepage flight form to submit provider-style flight parameters and added a narrow `/flights/` request guard for the `destination` query-var conflict. Removed exact label matching from the stored-menu eligibility guard so managed menus can customize labels while still proving the intended targets.

Bugs deferred: Standalone `/explore/`, `/deals/`, `/trip-planner/`, and `/saved-trips/` pages remain future Phase 14+ scope; P14.1 uses homepage anchor entry points to avoid introducing new 404 paths in the header. Flights and Hotels still render through existing page content and provider-owned widgets until later dedicated experience tickets.

Documentation updated: `.plan/phased-implementation.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Theme Handbook: Including CSS and JavaScript.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: P14.1 local implementation and review gate passed. Keep Phase 14 overall `In Progress` for the remaining homepage modules and search-surface follow-ups.

## Phase 14.2 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-87` Travelpayouts-powered Flights and Hotels search placements. Reviewed Phase 14 objective, P14.1 homepage form contracts, Phase 13 placement registry/wrapper contracts, current theme routing/template behavior, active WordPress runtime pages, Travelpayouts White Label setup guidance, and Trip.com partner widget/handoff behavior.

Acceptance criteria result: Passed for P14.2 scope. `/flights/` and `/hotels/` now render dedicated WordPress page templates that preserve the Bookings and Flights shell and call the approved `[baf_travelpayouts_widget]` placement seam. Flights use `flights_white_label_search`; Hotels use `hotels_partner_search`. Both pages render sanitized submitted intent details, configured/missing fallback states, disclosures from the renderer, no-script support, and visible partner handoff links without claiming WordPress owns live inventory.

Security review: Passed locally. Theme templates sanitize query parameters before rendering, escape output through the shared template part, and do not copy raw provider snippets into theme files. Frontend HTTP source scans found no API tokens, API keys, authorization headers, bearer strings, access tokens, refresh tokens, client secrets, secret terms, checkout, payment, or refund terms on the rendered Flights and Hotels pages.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.2.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.2.

UI review: Passed locally with real runtime screenshots. Desktop `1440x960` and mobile `390x844` screenshots confirmed the Flights and Hotels first viewports and scrolled widget sections render nonblank, keep the dark header legible over light search pages, avoid horizontal overflow, expose configured widget state, and keep visible handoff links. Keyboard review confirmed Flights reaches the visible `Open flight search` handoff instead of trapping on the White Label placeholder, and Hotels reaches the Trip.com iframe followed by `Open hotel search`.

Regression review: The flight page preserves provider-style homepage query parameters only long enough to show sanitized intent details, then removes them from the browser URL before the Travelpayouts White Label script runs. This prevents the provider script from consuming the homepage query string, rewriting into its own `flightSearch` state, and showing the provider connection-lost overlay. Existing Phase 13 registry state, SubID mutation, disclosure output, consent-disabled/missing placement behavior, and partner handoff behavior remain delegated to the renderer.

Validation performed: PHP syntax checks for changed PHP files; JavaScript syntax check for `search-surface.js`; `git diff --check`; file-size checks; WP-CLI active theme/plugin and page existence checks; WP-CLI shortcode smoke checks for flight configured state, flight marker/SubID handoff, hotel configured state, and missing placement state; `bookings-flights-core` deactivate/reactivate; HTTP 200/source scans for Flights and Hotels; Playwright desktop/mobile screenshots, scrolled-widget screenshots, keyboard review, console/failure capture, header contrast checks, widget state checks, iframe/handoff checks, and horizontal-overflow checks. The Codex in-app Browser surface had no active pane, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: The initial flight page left homepage provider query parameters in the URL, allowing the Travelpayouts script to rewrite the page and display a provider-owned connection-lost overlay. The White Label script also marked the loaded provider state too narrowly, causing a false unavailable message after the provider script loaded, and the placeholder nodes were reachable repeatedly in keyboard order. Search page header styling also became unreadable after scrolling onto light content. Codex PR review found that the loaded White Label placeholders stayed under `aria-hidden` ancestors and that script `load` was not enough proof of rendered provider content.

Bugs fixed: Added a narrow flight intent cleanup script before the provider wrapper runs, marked White Label output loaded only after rendered provider content appears, kept White Label placeholder nodes out of sequential keyboard order, removed `aria-hidden` from loaded White Label placeholders so injected provider content remains exposed to assistive tech, added a visible White Label fallback/handoff from saved settings, hardened the shared template-part argument defaults, and added search-surface header styles for readable fixed navigation on light pages.

Bugs deferred: Provider-owned Travelpayouts scripts still emit React JSX source-map and duplicate GraphQL fragment warnings on the Flights page, and Chromium may emit WebGL performance warnings from the Trip.com provider context. These warnings are provider-owned and did not create page errors, failed requests, overlays, or broken keyboard behavior in the P14.2 runtime pass. Standalone Explore, Deals, Trip Planner, and Saved Trips pages remain later Phase 14+ scope.

Documentation updated: `.plan/phased-implementation.md`, `.plan/known-issues.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Theme Handbook: Page Templates.
- WordPress Shortcode API and `do_shortcode()` reference.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: P14.2 local implementation and review gate passed. Keep Phase 14 overall `In Progress` for the remaining homepage modules and search-surface follow-ups until their Linear issues pass review and merge.

## Phase 14.3 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-88` trending, explore-anywhere, flexible-month, and hotel city discovery modules. Reviewed Phase 14 objective, P14.1/P14.2 homepage/search contracts, current homepage template/CSS state, published CPT data availability, approved Flights/Hotels handoff routes, WordPress template and escaping guidance, and Travelpayouts widget/SubID/White Label handoff guidance.

Acceptance criteria result: Passed locally for the PR candidate. The homepage now renders four below-hero discovery groups: trending route starters, explore-anywhere prompts, flexible-month planning, and hotel city discovery. Each group uses clearly labeled editorial/static inspiration because no published destination, route, or travel deal posts exist yet. Cards link into the approved Flights or Hotels search pages and do not display static prices, fake deal labels, unsupported live availability claims, or raw provider snippets.

Security review: Passed locally. Homepage module output is built from server-side arrays and escaped at render time with `esc_html()` and `esc_url()`. Links are created with `add_query_arg()` against local `/flights/` and `/hotels/` surfaces. Source scans found no API token, API key, authorization, bearer, access-token, refresh-token, client-secret, postback, or secret terms.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.3.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.3.

UI review: Passed locally with real runtime screenshots. Desktop `1440x1000`, tablet `900x1024`, and mobile `390x844` screenshots confirmed the homepage first viewport and discovery modules render nonblank, remain scannable, keep card text inside containers, show the sponsored-search disclosure, and avoid horizontal overflow. Keyboard review confirmed route, flexible-month, and hotel discovery cards are reachable.

Regression review: Existing homepage flight and hotel forms still target the P14.2 `/flights/` and `/hotels/` surfaces. The new route cards pass provider-style IATA parameters to `/flights/`, where P14.2 cleanup prevents provider query-string rewrites. Hotel cards use the existing non-conflicting `travel_destination` parameter. No extra frontend JavaScript or provider embed code was added.

Validation performed: PHP syntax check for `page-home.php`; `git diff --check`; file-size check for `page-home.php`, `home.css`, and `functions.php`; WP-CLI CPT content availability check; HTTP 200 homepage smoke; source scans for fake prices, fake deal claims, broad guarantee terms, and secret-token patterns; Playwright desktop/tablet/mobile screenshots; Playwright module count, link, disclosure, no-overflow, console, failed-request, and keyboard reachability checks.

Bugs found: The first copy pass contained broad negation text such as live-price and guarantee language that made source scans noisy even though the copy did not claim live pricing. It also included a flexible-card sentence with the word prices.

Bugs fixed: Reworded module copy so broad source scans find no price, fare, cheap, guarantee, exclusive-deal, limited-time, or secret-token patterns while still telling users that provider pages handle current availability and reservation details.

Bugs deferred: Published destination, route, travel deal, and city editorial content remains absent. The modules intentionally use static inspiration until later content/SEO phases seed or publish real CPT records.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress WP_Query reference.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress `esc_url()` and `sanitize_text_field()` references.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: P14.3 local implementation and review gate passed. Keep Phase 14 overall `In Progress` until remaining Phase 14 issues pass review and merge.

## Phase 14.4 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-89` price alert CTA and AI planner entry. Reviewed Phase 14 objective, P14.1-P14.3 homepage/search contracts, current homepage template/CSS state, existing Trip Planner and Saved Trips placeholder behavior, approved Flights handoff route, WordPress template and escaping guidance, and Travelpayouts handoff guidance.

Acceptance criteria result: Passed locally for the PR candidate. The homepage now includes two retention/planning cards below the discovery modules: a price-alert preview CTA and an AI planner placeholder entry. The alert card states that alerts are not active yet and routes to the existing Flights handoff. The planner card states that AI itinerary generation is later-phase work, routes to the local `#trip-planner` placeholder, and does not send prompts to an AI provider.

Security review: Passed locally. No POST form, AJAX/fetch call, beacon, nonce-requiring write, prompt submission, local data capture, auto-booking, auto-publishing, or unsupported provider execution was added. New links are escaped with `esc_url()`, and output is static template text.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.4.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.4.

UI review: Passed locally with real runtime screenshots. Desktop `1440x1000` and mobile `390x844` screenshots confirmed the retention section renders two scannable cards, no horizontal overflow, and no text overlap. Keyboard review confirmed both new cards are reachable.

Regression review: Existing homepage search forms, discovery modules, and P14.2 Flights/Hotels handoff pages remain intact. The price alert CTA opens `/flights/?travel_focus=price_alert`, where no alert write is attempted. The AI planner entry moves to `#trip-planner`, which remains the documented placeholder until the later AI planner phase.

Validation performed: PHP syntax check for `page-home.php`; `git diff --check`; file-size check for `page-home.php`, `home.css`, and `functions.php`; HTTP 200 homepage smoke; source scans for unsupported claim terms and secret-token patterns; write-path scan confirming no new POST/fetch/beacon/nonce path beyond existing GET search forms; Playwright desktop/mobile screenshots; Playwright link smoke for price-alert and planner entries; Playwright no-overflow, console, failed-request, and keyboard reachability checks.

Bugs found: The first retention heading used the word live, which made the unsupported-claim source scan noisy.

Bugs fixed: Reworded the heading to avoid unsupported live-claim wording while preserving the limitation message.

Bugs deferred: Real price alert capture, saved alert storage, AI prompt submission, and AI-generated itinerary workflows remain later planned phases.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress `esc_url()` reference.
- WordPress Common APIs Handbook: Nonces.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: P14.4 local implementation and review gate passed. Keep Phase 14 overall `In Progress` until remaining Phase 14 issues pass review and merge.

## Phase 14.5 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-90` trust, disclosure, partner handoff, footer content, and legal-path clarity. Reviewed Phase 14 objective, P14.1-P14.4 homepage/search contracts, Phase 13 provider wrapper boundary, published page slugs, current footer layouts, legal template content, WordPress template/escaping guidance, nonce guidance for write-path scope, and Travelpayouts affiliate marker/White Label guidance.

Acceptance criteria result: Passed locally for the PR candidate. Homepage trust cards now explain affiliate commission, partner checkout/support ownership, local support, and destination-index entry. Flights and Hotels placement pages now display local support/disclosure language outside provider frames. Footer compliance now includes affiliate disclosure plus Terms, Privacy, Support, and Destination index links across layouts, and Terms points to the published `/terms-and-conditions/` page. Privacy and Terms legal content now covers Travelpayouts/partner handoff, affiliate tracking/disclosure, and support boundaries.

Security review: Passed locally. All changed template output uses escaped URLs/text, no raw provider snippets or credentials were added, no POST/AJAX/fetch/beacon/write path was added, and source scans found no API token, API key, authorization, bearer, access token, refresh token, client secret, postback secret, direct checkout, payment-with-Bookings, guaranteed, lowest-price, live-fare, or direct-booking claims.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.5.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.5.

UI review: Passed locally with real runtime screenshots. Desktop and mobile screenshots confirmed homepage trust cards and footer compliance render without text overflow, legal links are readable on the dark footer, and Flights/Hotels support notes render below the approved provider wrapper. Keyboard review confirmed Support, Destination index, Terms, Privacy, `Open flight search`, and `Open hotel search` are reachable. Footer legal link contrast is `11.76:1`; footer disclosure contrast is `5.28:1`.

Regression review: Existing homepage search forms, discovery modules, retention cards, P14.2 Flights/Hotels wrapper behavior, provider handoff links, and footer layouts remain intact. The previous footer `/terms/` slug regression is fixed to `/terms-and-conditions/`. Provider-owned Travelpayouts React/GraphQL warnings on Flights and browser WebGL provider-context warnings on Hotels remain watchlist items; there were no page errors or failed requests in the P14.5 runtime pass.

Validation performed: PHP syntax checks for changed PHP files; `git diff --check`; file-size checks; WP-CLI page/theme checks with the Local MySQL socket; HTTP 200 smoke for Home, Flights, Hotels, Contact, Privacy, and Terms; source scans for required trust/disclosure/link text; source scans for secret and unsupported direct-booking terms; Playwright desktop/mobile screenshots; Playwright link smoke for Terms, Privacy, Support, and Destination index; Playwright keyboard review; footer contrast checks. The Codex in-app Browser surface was attempted first but had no active pane, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: The footer still pointed Terms to `/terms/` even though the published page is `/terms-and-conditions/`. The first footer compliance pass inherited the older low-contrast legal link color on the dark footer.

Bugs fixed: Updated the footer Terms URL to `/terms-and-conditions/` and raised footer legal link contrast to `11.76:1` on the dark footer.

Bugs deferred: Standalone Explore, Deals, Trip Planner, and Saved Trips pages remain later Phase 14+ scope. Provider-owned Flights/Hotels runtime warnings remain watchlist items because they do not create page errors, failed requests, broken handoff links, or keyboard traps.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress `esc_url()` reference.
- WordPress Common APIs Handbook: Nonces.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: P14.5 local implementation and review gate passed. Keep Phase 14 overall `In Progress` until remaining Phase 14 issues pass review and merge.

## Phase 14.6 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-91` responsive, accessibility, performance/script-scope, screenshot, and keyboard-navigation pass. Reviewed Phase 14 objective, P14.1-P14.5 homepage/search/footer contracts, current theme CSS and asset enqueue behavior, official Travelpayouts plugin asset handles, approved Phase 13 widget-registry surfaces, WordPress enqueue/dequeue guidance, Travelpayouts widget/White Label guidance, and WCAG keyboard, target-size, and reduced-motion guidance.

Acceptance criteria result: Passed locally for the PR candidate. The public theme now has stable touch targets for the mobile header controls and footer navigation/legal links; reduced-motion users no longer get delayed mobile-menu item reveal timing; and official Travelpayouts plugin runtime assets are removed from public pages that do not contain official Travelpayouts shortcodes. The approved Phase 13/14 wrapper surfaces remain intact: Flights still loads the local search-surface behavior and White Label script, and Hotels still renders the Trip.com/widget handoff output without the Flight search-surface script.

Security review: Passed locally. No provider credentials, API tokens, POST writes, REST routes, database migrations, custom SQL, external AI calls, or new storage paths were added. The new asset-scope helper only dequeues/deregisters public official-plugin asset handles beginning with `travelpayouts-assets-` when the queried post does not contain an official Travelpayouts shortcode pattern. Rendered source checks confirmed no unexpected official plugin runtime assets on Home, Flights, or Hotels.

REST permission review: Not applicable. No REST routes or permission callbacks changed in P14.6.

Database/migration review: Not applicable. No custom tables, options, migrations, or data mutations changed in P14.6.

UI review: Passed locally with real runtime screenshots. Desktop `1440x1000`, tablet `900x1024`, and mobile `390x844` screenshots covered Home, mobile menu, reduced-motion mobile menu, Flights, and Hotels. Runtime checks found no horizontal overflow, blank pages, framework overlays, page errors, failed requests, unexpected official Travelpayouts plugin assets, or broken widget/handoff states. Keyboard review reached homepage header/search controls, mobile product links, the Flight handoff link, the Trip.com hotel iframe, and the Hotel handoff link.

Regression review: Existing homepage search modules, trust/disclosure copy, footer compliance links, P14.2 Flights/Hotels placement templates, Phase 13 widget-registry rendering, SubID/handoff behavior, and support notes remain intact. Provider-owned Flights React JSX-source warnings remain an existing Travelpayouts White Label watch item because they did not create page errors, failed requests, overlays, or broken keyboard behavior.

Validation performed: PHP syntax checks for changed PHP files; targeted `git diff --check`; file-size checks; Local-socket WP-CLI active-theme check; HTTP `200` smoke checks for Home, Flights, and Hotels; Playwright desktop/tablet/mobile screenshots; Playwright mobile-menu and reduced-motion screenshots; Playwright keyboard review; Playwright touch-target measurements; Playwright script-scope checks for official plugin assets, search-surface assets, White Label output, and Hotels widget output; console/failure capture. The Codex Browser surface was unavailable in the current tool set, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: The first runtime pass found delayed mobile-menu link transitions under reduced-motion, undersized mobile header/footer targets, and globally loaded official Travelpayouts plugin assets on public pages that did not need them.

Bugs fixed: Added a reduced-motion override for mobile nav link/CTA transition delays; raised header logo, theme toggle, mobile menu toggle, footer social, footer legal, and footer navigation target sizing; and added a scoped theme helper that removes official Travelpayouts plugin public asset handles from pages without official Travelpayouts shortcodes while preserving approved wrapper-owned Flights/Hotels output.

Bugs deferred: Provider-owned Travelpayouts White Label React JSX-source warnings remain a watch item. Standalone Explore, Deals, Trip Planner, and Saved Trips pages remain later Phase 14+ scope.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Developer Resources: `wp_enqueue_scripts()`.
- WordPress Developer Resources: `wp_enqueue_script()`.
- WordPress Developer Resources: `wp_dequeue_script()`.
- WordPress Developer Resources: `wp_dequeue_style()`.
- W3C WAI WCAG 2.2: Focus Visible.
- W3C WAI WCAG 2.2: Target Size (Minimum).
- W3C WAI WCAG technique C39 for `prefers-reduced-motion`.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: P14.6 local implementation and review gate passed. Keep Phase 14 overall `In Progress` until remaining Phase 14 issues pass review and merge.

P14.6 Codex review follow-up on 2026-05-12: PR #27 review found that the original asset-pruning guard only inspected queried singular post content, so official Travelpayouts shortcodes rendered from widget areas, non-singular templates, or template-level `do_shortcode()` calls could lose their required `travelpayouts-assets-*` runtime. The guard now preserves official assets in non-singular contexts by default, scans active widget instance content for official Travelpayouts shortcode/block patterns, and exposes the `bookings_and_flights_has_official_travelpayouts_output` filter so template-level official output can opt in before pruning. PHP syntax, targeted diff checks, Local-socket WP-CLI smoke checks for content/widget/non-singular/filter detection, and the Playwright responsive/script-scope pass passed after the patch.

## Phase 14.7 Final Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-92` final Phase 14 review and documentation gate. Reviewed Phase 14 objective, scope, acceptance criteria, P14.1 through P14.6 implementation/review notes, current runtime Home/Flights/Hotels/Privacy/Terms pages, Travelpayouts handoff boundaries, disclosure/legal copy, accessibility and responsive evidence, script enqueue scope, source-secret/unsupported-claim scans, and deferred-work documentation.

Acceptance criteria result: Passed locally for the PR candidate. The placeholder homepage has been replaced with an image-led travel search shell, Travelpayouts-powered flight/hotel entry points, discovery modules, flexible planning prompts, price-alert and AI-planner placeholder entries, trust/disclosure cards, footer compliance links, and visible partner-support boundaries. Search actions route through the Phase 13/14 placement seam into Travelpayouts-controlled White Label or approved partner widget/handoff surfaces, while the Bookings and Flights header/navigation shell remains visible on Home, Flights, and Hotels. Disclosures remain visible, and source scans found no fake prices, unsupported live-availability/fare guarantees, direct-checkout claims, auto-booking, auto-publishing, or secret exposure.

Security review: Passed locally. Phase 14 added no REST routes, custom SQL, database migrations, provider credential storage, AI provider calls, POST writes, alert capture, payment/checkout flows, or auto-publishing actions. Changed templates continue to escape output and route users to provider-owned booking surfaces. The final source scan found no API tokens, authorization/bearer strings, client/postback secrets, or direct Bookings-and-Flights checkout claims in the reviewed source/rendered pages.

REST permission review: Not applicable. No REST routes or permission callbacks changed in the Phase 14 public homepage/search-surface work.

Database/migration review: Not applicable. No custom tables, schema migrations, or persistent data mutations changed in Phase 14.

UI review: Passed locally with real runtime screenshots. Desktop, tablet, and mobile screenshots covered the homepage; mobile-menu screenshots confirmed product navigation; and desktop/mobile widget screenshots covered Flights and Hotels. Runtime checks found no horizontal overflow, blank page state, framework overlay, page error, failed request, relevant console error, unexpected official Travelpayouts plugin runtime on Home/Flights/Hotels, or broken widget/handoff state. Keyboard review confirmed Home reaches the search submit flow, the mobile menu reaches Plan trip, Flights reaches `Open flight search`, and Hotels reaches the Trip.com iframe followed by `Open hotel search`.

Regression review: Existing Phase 13 widget registry, SubID/handoff behavior, consent/disclosure boundaries, P14.2 Flights/Hotels shell templates, P14.5 legal/footer links, and P14.6 asset scoping remain intact. Provider-owned Flights React JSX-source warnings remain a watch item only; they did not produce page errors, failed requests, overlays, or broken keyboard behavior in the final gate.

Validation performed: PHP syntax checks for public theme templates/functions/helpers; Local-socket WP-CLI published-page inventory; HTTP `200` smoke for Home, Flights, Hotels, Privacy, and Terms; required disclosure/handoff/legal text scans; negative scans for secrets and unsupported booking/fare claims; `git diff --check`; file-size checks; Playwright Chromium desktop/tablet/mobile screenshots; Playwright mobile-menu, widget-handoff, keyboard, console/failure, horizontal-overflow, and script-scope checks. The Codex Browser surface was unavailable in this turn, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: No new production bug was found in the final P14.7 gate. The source-scan command initially ran in parallel before the temporary HTML files existed; it was rerun after the HTTP smoke wrote the files and then passed.

Bugs fixed: No code bug required a P14.7 patch. Documentation was updated to mark Phase 14 completed, record the final validation baseline, preserve regression watch items, and clarify the next planned phases.

Bugs deferred: Standalone Explore, Deals, Trip Planner, and Saved Trips pages remain later scope. Published destination, route, travel deal, and city editorial content remains absent until later SEO/content phases. Real price-alert capture, saved alert storage, AI prompt submission, and AI-generated itinerary workflows remain later planned phases. Provider-owned Travelpayouts runtime warnings remain watch items when they do not cause page errors, failed requests, overlays, broken handoffs, or keyboard traps.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Theme Handbook: Including CSS and JavaScript.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Developer Resources: `wp_enqueue_scripts()`, `wp_dequeue_script()`, and `wp_dequeue_style()`.
- W3C WAI WCAG 2.2: Focus Visible and Target Size (Minimum).
- W3C WAI WCAG technique C39 for `prefers-reduced-motion`.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: Phase 14 is complete after this review gate merges. Phase 15 may start from the completed homepage/search-surface baseline.

## Phase 15.1 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-93` Flights landing page and search module. Reviewed Phase 15 objective, Phase 14 homepage/search baseline, Phase 13 Travelpayouts placement registry contracts, current `/flights/` template and shared placement renderer, WordPress template/sanitizing/escaping guidance, Travelpayouts Widget-type White Label guidance, SubID marker guidance, and runtime Travelpayouts White Label behavior.

Acceptance criteria result: Passed locally for the PR candidate. `/flights/` now has a dedicated flight-intent module for origin, destination, depart date, return date, travelers, and cabin selection. Unsupported or provider-owned choices are not presented as local filters: direct-only, nearby-airport, flexible-date calendar, airline, baggage, and time filters are visibly marked as provider-controlled and set inside Travelpayouts. Submitted intent details render in the local shell, the approved `flights_white_label_search` placement remains the search/result handoff path, affiliate/provider support language remains visible, and mobile layout passed runtime review.

Security review: Passed locally. The template sanitizes GET parameters with scalar checks, IATA/date allowlists, `sanitize_key()`, `sanitize_text_field()`, `wp_unslash()`, and `absint()`, then escapes rendered values with `esc_html()`, `esc_attr()`, and `esc_url()`. No provider credentials, API tokens, REST routes, POST writes, custom SQL, database migrations, alert capture, payment/checkout path, or custom flight inventory API were added. Source scans found no API token, API key, authorization, bearer, access token, refresh token, client secret, postback secret, guaranteed-lowest-price claim, real-time fare claim, direct-checkout claim, book-directly claim, or Bookings-and-Flights payment claim.

REST permission review: Not applicable. P15.1 added no REST routes or permission callbacks.

Database/migration review: Not applicable. P15.1 added no custom tables, options, migrations, cron jobs, or persistent alert storage.

UI review: Passed locally with real runtime screenshots. Desktop `1440x1000` and mobile `390x844` screenshots confirmed the Flights landing page renders nonblank, the intent module is readable, provider-controlled options are visible, the Travelpayouts White Label module and `Open flight search` handoff remain visible, and there is no horizontal overflow or framework overlay. The desktop update-intent interaction changed the local form to `SEA` to `MIA`, `3` travelers, `Premium economy`, rendered the updated intent details, and cleaned provider query parameters from the visible URL. Keyboard review reached the local origin, destination, traveler, cabin, `Update flight intent`, and `Open flight search` targets on desktop and mobile.

Regression review: Existing Phase 13 widget-registry rendering, SubID marker output, visible handoff link, P14.2 query cleanup behavior, P14.5 provider-support disclosure, P14.6 search-surface asset scope, header continuity, and footer compliance remain intact. The Travelpayouts-injected widget uses overlapping field names such as `origin`, so local browser tests and future scripts must scope local form selectors to `.flight-intent__form`.

Validation performed: PHP syntax check for `page-flights.php`; JavaScript syntax check for `search-surface.js`; targeted `git diff --check`; file-size checks; HTTP `200` smoke for a submitted Flights intent URL; rendered/source scans for required intent/provider-controlled text, secrets, and unsupported booking/fare claims; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots; provider-section screenshots; update-intent interaction proof; keyboard navigation review; horizontal-overflow, blank-page, framework-overlay, console, failed-request, and handoff checks. The Codex in-app Browser path had no active pane in this thread, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: The first runtime interaction script targeted `input[name="origin"]` globally and conflicted with the Travelpayouts-injected provider field. The first desktop screenshot also showed the action button lower than ideal in the first viewport.

Bugs fixed: Scoped runtime interaction checks to `.flight-intent__form` and moved `Update flight intent` above the helper/disclosure note so the primary local form action is visible sooner. The product code already kept the local fields and provider fields separate.

Bugs deferred: Origin/route landing pages, low-price calendar widgets, popular route widgets, and price-alert signup remain later Phase 15 scope. Provider-owned Travelpayouts JSX-source and duplicate GraphQL-fragment console warnings remain watchlist-only because they did not create page errors, failed requests, overlays, blank states, or broken handoff/keyboard behavior.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Theme Handbook: Including Assets.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.
- Travelpayouts Help Center: Getting started with widgets.

Decision: P15.1 local implementation and review gate passed. Keep Phase 15 overall `In Progress` until the remaining flights experience issues pass review and merge.

## Phase 15.2 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-94` route detail and origin page templates. Reviewed Phase 15 objective, P15.1 Flights baseline, Phase 13 Travelpayouts widget-registry surface guard, current `route` CPT runtime behavior, WordPress custom post type template hierarchy, `WP_Query` usage, input sanitization, output escaping, and Travelpayouts White Label widget/page guidance.

Acceptance criteria result: Passed locally for the PR candidate. The static theme now has route archive and single-route templates plus a reusable route-card template part. `/routes/` lists published route guides, `/routes/?route_origin=JFK` renders an origin-filtered archive with sanitized query input, and single route pages render editable WordPress route meta, editorial content, related route cards, an alert handoff placeholder, and the approved `flights_white_label_search` Travelpayouts placement. Live search/results/booking/payment/support remain provider-owned and no provider inventory is stored as canonical WordPress data.

Security review: Passed locally. Public query input for `route_origin` is scalar-checked, unslashed, sanitized, normalized, and bounded. Route meta output and generated links are escaped. The registry migration only adds the `route` public surface to the existing starter flight placement once and keeps the surface allowlist guard active for disallowed contexts. No provider credentials, API tokens, REST routes, POST writes, custom SQL, database tables, alert storage, payment/checkout flow, custom inventory API, or auto-publishing path was added.

REST permission review: Not applicable. P15.2 added no REST routes or permission callbacks.

Database/migration review: Passed for option migration scope. No custom tables or schema migrations changed. The `baf_travelpayouts_widget_registry` option schema moved to `1.0.1` and one-time migration adds `route` to the starter `flights_white_label_search` public surfaces. Temporary route posts used for runtime validation were deleted after the browser pass.

UI review: Passed locally with real runtime screenshots. Desktop/mobile archive and route detail screenshots confirmed nonblank pages, visible Bookings and Flights header/footer shell, readable route cards, no horizontal overflow, no framework overlays, no duplicate IDs, and no relevant console errors or page errors. The Travelpayouts White Label widget renders on route detail pages, clears its loading state after provider shadow DOM content appears, and keeps the visible sponsored handoff/support copy. Keyboard review reached archive route links, route handoffs, `Browse routes`, provider `Open flight search`, and `Open alert handoff`.

Regression review: Existing P15.1 Flights page, Phase 13 widget registry surface guard, SubID generation, consent/disclosure boundaries, P14.2 search-surface shell, P14.5 footer/legal disclosure, and P14.6 asset scope remain intact. The route surface migration fixes the new route usage without reopening the broader surface allowlist bug that PR #18 previously patched.

Validation performed: PHP syntax checks for changed core/theme PHP files; targeted `git diff --check`; file-size checks; HTTP `200` smoke for route archive, origin-filter archive, and a temporary route detail URL; source scans for required route/disclosure/widget text and negative secret/unsupported-claim terms; WP-CLI route post inventory and cleanup; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots; provider-section screenshots; keyboard navigation review; horizontal-overflow, duplicate-ID, blank-page, framework-overlay, console, page-error, and widget loaded-state checks. The Codex in-app Browser path had no active pane in this thread, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: Runtime validation found that `flights_white_label_search` was configured but not approved for the new `route` surface, causing a safe `surface-unavailable` state on route pages. The widget wrapper also stayed visually in `is-loading` because Travelpayouts White Label renders usable content inside shadow DOM. The first desktop screenshot showed the related route card squeezed into one narrow grid column and the alert button stretching too tall inside the split panel.

Bugs fixed: Added a versioned registry migration to approve the `route` surface for the starter flight placement once; updated the White Label loaded-state check to recognize shadow-root provider content; tightened route copy away from stored-inventory/fare phrasing; and adjusted route panel/card CSS so related cards and alert buttons render at stable desktop/mobile sizes.

Bugs deferred: Low-price calendar widgets, popular route widgets, real alert capture/storage, final White Label continuity review, and broader Phase 15 SEO/content gate remain later Phase 15 scope.

Documentation updated: `.plan/phased-implementation.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/architecture-baseline.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Hierarchy.
- WordPress Developer Resources: `WP_Query`.
- WordPress Plugin Handbook: Securing Input.
- WordPress Plugin Handbook: Securing Output.
- WordPress Plugin Handbook: Custom Post Types.
- Travelpayouts Help Center: What is White Label Web by Travelpayouts.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P15.2 local implementation and review gate passed. Keep Phase 15 overall `In Progress` until the remaining flights experience issues pass review and merge.

## Phase 15.3 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-95` low-price calendar, popular routes, and route map placements. Reviewed Phase 15 objective, P15.1 Flights intent baseline, P15.2 route surface baseline, Phase 13 Travelpayouts widget-registry contracts, official Travelpayouts plugin shortcode availability, WordPress shortcode rendering behavior, input sanitization, output escaping, and runtime provider widget behavior.

Acceptance criteria result: Passed locally for the PR candidate. The widget registry now seeds approved `flights_low_price_calendar`, `flights_popular_routes`, and `flights_route_map` placements with official Travelpayouts plugin shortcode references. Flights and route pages render the discovery widgets inside responsive Bookings and Flights cards below the primary Travelpayouts White Label handoff, generate unique readable SubIDs per placement, reserve dimensions, and render visible missing-code states when origin or destination context is absent.

Security review: Passed locally. Runtime origin and destination values are scalar-checked, normalized to three-letter IATA codes, and escaped before entering shortcode output. The official-shortcode renderer only executes approved `tp_` references stored in the trusted registry path, keeps private embed metadata out of public projections, and adds no REST route, POST write, custom SQL, database table, alert storage, direct checkout, payment flow, API-token handling, custom inventory API, or auto-publishing path.

REST permission review: Not applicable. P15.3 added no REST routes or permission callbacks.

Database/migration review: Passed for option migration scope. No custom tables or destructive migrations changed. The `baf_travelpayouts_widget_registry` option schema moved to `1.0.2` and adds official shortcode starter placements for the low-price calendar, popular-routes widget, and route map on approved public surfaces. Temporary route post `297` was created for runtime validation and deleted afterward.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop and mobile Flights and route screenshots confirmed the calendar, popular-routes, and map widgets render visibly inside reserved cards with no horizontal overflow, duplicate document IDs, framework overlays, app page errors, or visible fallback states. The missing-code Flights page renders three visible status cards instead of broken widgets. Keyboard navigation reached public navigation, local intent controls, `Update flight intent`, `Open flight search`, route handoffs, and provider-owned widget focus points without trapping the page.

Regression review: Existing P15.1 Flights intent form, P15.2 route templates, Phase 13 registry surface guards, SubID generation, consent/disclosure boundaries, P14 search-surface shell, header/footer continuity, and provider-owned handoff language remain intact. The discovery section keeps Travelpayouts as owner of live widgets/results and does not turn WordPress into a fare, inventory, booking, or payment backend.

Validation performed: PHP syntax checks for changed core/theme PHP files; targeted `git diff --check`; file-size checks; WP-CLI registry migration check; WP-CLI shortcode smoke checks for `tp_calendar_widget`, `tp_popular_routes_widget`, `tp_map_widget`, and the `[baf_travelpayouts_widget]` wrapper; HTTP `200` smoke for configured Flights, missing-code Flights, route detail, and routes archive surfaces; source scans for required discovery placements, SubIDs, no-script states, secrets, and unsupported fare/booking claims; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots; missing-code screenshot; keyboard navigation review; horizontal-overflow, duplicate-ID, page-error, fallback-visibility, and widget loaded-state checks. The Codex in-app Browser request timed out in this thread, so Playwright Chromium was used for the required runtime browser screenshots and keyboard navigation review.

Bugs found: Runtime validation found that the first official-widget loaded detector could treat placeholder markup as loaded; after tightening that check, Travelpayouts calendar and popular-route widgets still rendered useful content inside shadow DOM while the wrapper fallback stayed visible; and the route-map iframe could initialize blank when it loaded below the fold. Self-review also found that if an approved official shortcode became unavailable, the renderer could return only no-script copy instead of the normal unavailable state. Codex PR review found that missing official shortcodes must fail as a non-configured state without deactivating saved placements before shortcode registration has completed.

Bugs fixed: Updated the official-shortcode renderer to require real provider content before loaded state, recognize provider shadow-root content, and refresh map iframes once when the wrapper enters the viewport. The main widget renderer now lets missing official shortcode output fall through to the normal unavailable wrapper state. Registry rendering now performs a render-time official-shortcode availability check and returns the missing-configuration state without mutating saved active placements during early `init`. The final browser pass confirmed all three discovery widgets rendered without visible fallback states on Flights and route surfaces.

Bugs deferred: Real alert capture/storage and the final Phase 15 White Label continuity/SEO gate remain later Phase 15 scope. Provider-owned Aviasales analytics `400` pixels, a provider image `404`, JSX-source/duplicate GraphQL warnings, and WebGL/map-image warnings remain watchlist-only because the widgets rendered and no app-owned source, layout, page, secret, or handoff failure was found.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Common APIs Handbook: Shortcode API.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Shortcodes of tables and widgets.
- Travelpayouts Help Center: Getting started with widgets.

Decision: P15.3 local implementation and review gate passed. Keep Phase 15 overall `In Progress` until alert storage and final flights review issues pass review and merge.

## Phase 15.4 Review - 2026-05-12

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-96` White Label result flow and header continuity. Reviewed Phase 15 objective, P15.1-P15.3 implementation notes, Phase 11 White Label Widget/Page decision, Phase 13 registry contracts, current Flights and route templates, WordPress template part and escaping guidance, and Travelpayouts White Label Web Widget/Page setup guidance.

Acceptance criteria result: Passed locally for the PR candidate. Flights and route detail pages now place a reusable White Label continuity band immediately before the approved `flights_white_label_search` module. The band preserves visible routes back to Home, Flights, route guides, and the current route guide; keeps WordPress as owner of SEO/editorial pages and the branded shell; and states that Travelpayouts or the partner provider owns live search, result filters, booking, payment, changes, and support. SEO landing pages remain WordPress-owned and no custom search/result backend was added.

Security review: Passed locally. The new template part escapes all labels, copy, URLs, and generated IDs. Runtime origin/destination values remain scalar-checked and normalized before being passed through the existing placement shell. No REST route, POST write, custom SQL, database migration, provider secret handling, direct checkout, payment flow, alert storage, auto-booking, or auto-publishing path was added. Public source scans found no API token, API key, access token, authorization, bearer, client secret, postback secret, password, or secret text.

REST permission review: Not applicable. P15.4 added no REST routes or permission callbacks.

Database/migration review: Not applicable. P15.4 added no custom tables, options, migrations, cron jobs, or persistent alert storage.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop and mobile screenshots covered Home, Flights, route detail, and the Flights/route provider sections. Header continuity checks confirmed Flights and route detail use the same Bookings and Flights logo text and primary navigation labels as Home. The embedded White Label provider modules reached `is-loaded`, continuity links stayed visible, and the final pass found no horizontal overflow, duplicate IDs, framework overlays, app page errors, secret terms, or keyboard misses. Keyboard review reached Home, Flights, Route guides/Route guide, All routes where present, and `Open flight search`.

Regression review: Existing P15.1 Flights intent details, P15.2 route archive/detail behavior, P15.3 discovery widgets, Phase 13 registry rendering, SubID output, consent/disclosure boundaries, header/footer continuity, and provider-owned handoff language remain intact. Provider-section scroll margin was added after visual review found the fixed header could cover the top of the continuity band when jumping directly to the provider area.

Validation performed: PHP syntax checks for changed theme PHP files; targeted `git diff --check`; file-size checks; HTTP `200` smoke for Home, Flights, Routes, and temporary route detail URLs; source scans for continuity copy, route-back links, secrets, and unsupported checkout/fare/payment claims; Playwright Chromium desktop/mobile screenshots; header-continuity comparison; keyboard navigation review; horizontal-overflow, duplicate-ID, page-error, provider-loaded-state, continuity-style-loaded, and secret-term checks. File-size review moved the continuity styles into a dedicated `white-label-continuity.css` asset instead of leaving `search-surface.css` at the 600-line ceiling. Temporary route posts were created for validation and deleted afterward.

Bugs found: The first runtime screenshot pass showed that anchored/jump navigation to the provider section could leave the continuity band partially hidden behind the fixed header.

Bugs fixed: Added provider-section scroll margin on Flights and route detail surfaces, split continuity styles into a focused CSS asset, patched the reusable template's optional-title ARIA fallback, then reran the browser screenshots and keyboard review.

Bugs deferred: Real alert capture/storage and the final Phase 15 flights release/SEO review remain later issues. Provider-owned Sentry/API aborts and Travelpayouts React/GraphQL warnings remain watchlist-only because the White Label modules loaded and no app-owned page, layout, handoff, secret, or keyboard failure was found.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: What is White Label Web by Travelpayouts.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P15.4 local implementation and review gate passed. Keep Phase 15 overall `In Progress` until alert storage and final flights review issues pass review and merge.

## Phase 15.5 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-97` price alert signup and local intent handling. Reviewed Phase 15 objective, P15.1-P15.4 implementation notes, existing `travel_alert` CPT/capability/cron contracts, current Flights and route templates, WordPress nonce/admin-post/post-meta/sanitization/escaping guidance, and the Travelpayouts-controlled live fare/search boundary.

Acceptance criteria result: Passed locally for the PR candidate. Flights and route detail pages now render local alert signup through `[baf_flight_alert_signup]`. The form captures email, origin, destination, frequency, explicit consent, and current route/search context, then stores a private `travel_alert` record as local watch intent. It does not claim live fare ownership or store provider inventory, booking, payment, reservation, or supplier result data.

Security review: Passed locally. The write path uses `admin-post.php` for logged-in and anonymous users, verifies a WordPress nonce with `check_admin_referer()`, requires consent, validates email, bounds route codes to three-letter IATA-style values, applies a short per-client/email/route transient throttle, sanitizes scalar POST input after `wp_unslash()`, uses safe redirects, registers private alert meta with `show_in_rest => false`, and escapes all rendered form output. Public source scans found no API token, API key, access token, authorization, bearer, client secret, postback secret, password, or secret text.

REST permission review: Passed for scope. P15.5 added no custom REST endpoint. The `travel_alert` CPT remains non-public, uses the `manage_baf_alerts` capability set for administration, and new alert meta is not exposed through REST.

Database/migration review: Passed. No custom tables or destructive migrations changed. The implementation stores local alert intent as private `travel_alert` posts with minimized meta: route, email, user ID when present, route post ID, traveler/cabin context, source surface/URL, consent timestamp, and requested status.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop/mobile Flights and route screenshots confirmed the alert form renders with consent and provider-limit copy, no horizontal overflow, no duplicate IDs, no framework overlays, no hidden consent text, and no app-owned page errors. A real browser form submission rendered the saved state. Keyboard review reached email, origin, destination, frequency, consent, and `Save alert intent` on both Flights and route pages.

Regression review: Existing P15.1 Flights intent form, P15.2 route archive/detail behavior, P15.3 discovery widgets, P15.4 White Label continuity, Phase 13 registry output, SubID/disclosure boundaries, header/footer continuity, and provider-owned handoff language remain intact. Existing search-surface URL cleanup removes the visible saved query string after the server-rendered success message loads, which is expected.

Validation performed: PHP syntax checks for changed plugin/theme PHP files; targeted `git diff --check`; file-size checks; WP-CLI shortcode, CPT, and meta registration checks; HTTP/source smoke for the Flights alert surface; admin-post happy-path, lowercase route-code, immediate duplicate/rate-limit, missing-nonce, invalid-email, and missing-consent probes; plugin deactivate/reactivate; Playwright Chromium desktop/mobile screenshots; real alert form submission; keyboard navigation review; horizontal-overflow, duplicate-ID, framework-overlay, source-secret, and app-owned error checks. The Codex in-app Browser plugin was attempted first, but its expected tab API was unavailable in this session, so Playwright Chromium was used for runtime screenshots and keyboard navigation review.

Bugs found: The first runtime QA assertion expected the saved query string to remain visible after submission, but the existing Flights search script correctly cleans query parameters from the visible URL after the server-rendered saved state loads. Codex PR review found that building the current form URL with raw `REQUEST_URI` could duplicate the WordPress home path on subdirectory installs, anonymous writes needed hardening beyond the shared guest nonce, and lowercase route-code submissions could be rejected even though the form pattern permits them.

Bugs fixed: Updated the runtime assertion and reran the browser pass with the expected query-cleanup behavior, plus a deeper route keyboard pass to confirm the lower alert form remains reachable after provider/discovery sections. Rebuilt current form URLs from parsed request paths before passing them through `home_url()`, added a rate-limited form state, added a per-client/email/route transient throttle before anonymous alert writes, and uppercased route codes before filtering to the three-letter IATA allowlist.

Bugs deferred: Final Phase 15 SEO metadata, source review, and route indexing remain `ONE-98`. Provider-owned Travelpayouts Sentry/analytics, duplicate GraphQL, JSX-source, and WebGL/map warnings remain watchlist-only because alert capture and page layout passed without app-owned failures.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Common APIs Handbook: Nonces.
- WordPress Code Reference: `admin_post_{$action}`.
- WordPress Code Reference: `register_post_meta()`.
- WordPress Code Reference: `sanitize_email()`.
- WordPress Code Reference: `wp_insert_post()`.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.

Decision: P15.5 local implementation and review gate passed. Keep Phase 15 overall `In Progress` until the final SEO/source/release review issue passes review and merge.

## Phase 15.6 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-98` SEO metadata, source review, and route indexing. Reviewed Phase 15 objective, P15.1-P15.5 implementation notes, current Flights and route templates, route CPT/meta contracts, WordPress title support, `wp_head()`, canonical output, `WP_Query` bounds, public REST/content behavior, and the Travelpayouts-controlled result/handoff boundary.

Acceptance criteria result: Passed locally for the PR candidate. `/routes/`, origin-filtered route archives, and route detail pages now have sensible WordPress-owned SEO metadata and indexable route behavior. `/flights/` remains the indexable handoff entry page, while transient flight-search query URLs render `noindex, follow` and canonicalize to `/flights/`. Travelpayouts White Label and widgets remain provider-owned result/search surfaces, not the SEO source of truth.

Security review: Passed locally. Public output escapes metadata and route values, normalizes route codes uppercase before filtering, and does not expose provider credentials, API tokens, authorization headers, postback secrets, private embed fields, private alert data, raw provider payloads, checkout/payment ownership, or fake fare/scarcity claims. No REST route, POST write, custom SQL, database migration, cron job, provider secret handling, direct checkout, payment flow, auto-booking, or auto-publishing path was added.

REST permission review: Passed for scope. P15.6 added no custom REST endpoint. Public WordPress route collection smoke returned a bounded collection, and route post meta remains private because the registered route meta fields continue to use `show_in_rest => false`.

Database/migration review: Not applicable. P15.6 added no custom tables, options, migrations, cron jobs, or persistent records.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop and mobile origin archive screenshots confirmed readable SEO headings, route cards, and handoff actions with no horizontal overflow, duplicate IDs, app-owned console errors, framework overlays, or visible warning output. A keyboard pass tabbed from the origin archive through the primary navigation and route actions to the first route card link, then pressed Enter into the route detail page. Route detail and Flights query screenshots confirmed metadata behavior, normalized route labels, related-route links, and no app-owned page failures.

Regression review: Existing P15.1 Flights intent details, P15.2 route archive/detail behavior, P15.3 discovery widgets, P15.4 White Label continuity, P15.5 alert signup, Phase 13 registry output, SubID/disclosure boundaries, header/footer continuity, and provider-owned handoff language remain intact. The lowercase route-code fix closes a source/relevance bug without changing provider ownership or storing provider inventory.

Validation performed: PHP syntax checks for changed plugin/theme PHP files; targeted `git diff --check`; file-size checks; plugin deactivate/reactivate; WP-CLI route CPT, shortcode, and route-code sanitizer smoke; HTTP/source smoke for `/flights/`, `/flights/?origin=nyc&destination=lax&depart_date=2026-08-01`, `/routes/`, `/routes/?route_origin=nyc`, and a temporary route detail URL; public REST/content smoke for route collection bounds; source scans for metadata, canonical output, noindex behavior, secrets, PHP warnings, unsupported price claims, fake scarcity, checkout, payment, and booking-owner leakage; Playwright Chromium desktop/mobile screenshots; keyboard navigation review. The Codex in-app Browser plugin was attempted first, but no active browser pane was available in this session, so Playwright Chromium was used for runtime screenshots and keyboard navigation review.

Bugs found: Source review found lowercase route-origin and route-meta values were filtered before uppercasing in the route archive/card/single path and in the core route-code sanitizer, which could drop lowercase airport codes from indexable route output.

Bugs fixed: Added a shared theme SEO helper module, routed origin filtering and route display through uppercase-first normalization, patched the core route-code sanitizer, added route/archive/Flights metadata behavior, added route archive canonical output, added noindex behavior for transient Flights search query URLs, and documented the route indexing boundary. Runtime review with lowercase query/meta values confirmed `NYC` route output, canonical `/routes/?route_origin=NYC`, route labels, route detail metadata, and keyboard navigation.

Bugs deferred: Provider-owned Travelpayouts React/GraphQL warnings and aborted analytics/image requests remain watchlist-only because app-owned console and failed-request buckets were empty, provider modules rendered, and no app source, layout, handoff, secret, or keyboard failure was found.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Code Reference: `add_theme_support()`.
- WordPress Code Reference: `wp_head()`.
- WordPress Code Reference: `rel_canonical()`.
- WordPress Code Reference: `WP_Query`.

Decision: P15.6 local implementation and review gate passed. Phase 15 Flights Experience is complete after PR review, merge, and Linear closeout for `ONE-98`.

## Phase 15.7 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-99` final Phase 15 review and documentation gate. Reviewed P15.1-P15.6 implementation notes, current Flights and route templates, Travelpayouts widget placement registry behavior, White Label continuity, alert intent handling, route SEO/indexing behavior, public REST route exposure, source output, runtime screenshots, keyboard navigation, and docs alignment before Phase 16 hotel/stays work starts.

Acceptance criteria result: Passed locally for the PR candidate. The Flights landing page, transient flight query behavior, route archive, origin-filtered route archive, route detail page, Travelpayouts White Label handoff, discovery widget shell, local alert intent permission failure, route SEO metadata, and public route REST collection all matched the Phase 15 objective and acceptance criteria.

Security review: Passed locally. Source scans found no app-owned PHP warnings, API keys, tokens, authorization headers, bearer strings, postback secrets, passwords, fake scarcity, direct-checkout, auto-booking, or stored-inventory leakage. Missing alert nonce returned `403`, and a follow-up query confirmed no alert record was created for the rejected submission. No code path was added, and no capability, nonce, settings, route, custom SQL, provider secret, direct checkout, payment, booking, or auto-publishing boundary changed in P15.7.

REST permission review: Passed for scope. P15.7 added no REST endpoints. The public WordPress route collection smoke returned `200` with `per_page=1`, and the existing route meta/private alert boundaries remain unchanged from earlier Phase 15 gates.

Database/migration review: Passed for scope. P15.7 added no tables, options, migrations, cron jobs, or persistent records. Temporary route post `312` was created for runtime validation, deleted after the browser pass, and confirmed at `remaining=0`.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium screenshots covered the Flights handoff page, White Label widget section, route detail page, mobile origin archive, and keyboard-focused route link. The runtime report found no blank pages, framework overlays, horizontal overflow, duplicate IDs, app-owned console errors, app-owned failed requests, or relevant failed requests. Keyboard navigation tabbed from the origin archive to the temporary route card link and pressing Enter opened the route detail page.

Regression review: Existing P15.1 Flights intent behavior, P15.2 route archive/detail templates, P15.3 discovery widgets, P15.4 White Label continuity, P15.5 alert intent capture, P15.6 SEO metadata/indexing, Phase 13 registry output, SubID/disclosure boundaries, and provider-owned booking/payment/support language remain intact. Provider-owned Travelpayouts warnings and external request noise remain watchlist-only because app-owned checks passed and widget/handoff output stayed usable.

Validation performed: HTTP `200` smoke for `/flights/`, `/flights/?origin=nyc&destination=lax&depart_date=2026-08-01`, `/routes/`, `/routes/?route_origin=nyc`, a temporary route detail URL, and `/wp-json/wp/v2/route?per_page=1`; source checks for metadata, canonical output, noindex behavior, route context, widget placement output, warnings, secrets, and unsupported ownership claims; missing-alert-nonce POST returning `403`; alert email creation check returning zero records; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots and keyboard navigation; temporary route cleanup; and `git diff --check`.

Bugs found: No app-owned Phase 15 bugs were found in the P15.7 local gate. The only runtime noise was provider-owned Travelpayouts console warnings and external request failures already tracked as non-blocking watchlist items.

Bugs fixed: None required in P15.7. Documentation was updated to record the completed gate and carry-forward watchlist.

Bugs deferred: Provider-owned Travelpayouts runtime warnings and external request noise remain a later-phase watch item. Phase 16 hotel/stays work still needs its own runtime validation and must preserve the Travelpayouts-controlled backend boundary.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Plugin Security Handbook: `https://developer.wordpress.org/plugins/security/`
- WordPress REST API Handbook: `https://developer.wordpress.org/rest-api/`
- WordPress Code Reference: `wp_head()`
- WordPress Code Reference: `rel_canonical()`
- WordPress Code Reference: `WP_Query`
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P15.7 local review gate passed. Phase 15 Flights Experience is ready to close after PR review, merge, and Linear sync; Phase 16 hotel/stays work may start after that closeout.

## Phase 16.1 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-100` Hotels landing page and search/widget module. Reviewed Phase 16 objective, Phase 11 hotel/Trip.com constraints, Phase 13 widget registry contracts, Phase 14 Hotels baseline, Phase 15 handoff/disclosure patterns, current Hotels template, shared placement shell, provider consent boundary, hotel widget registry placement, source output, desktop/mobile runtime screenshots, and keyboard navigation.

Acceptance criteria result: Passed locally for the PR candidate. The Hotels page now renders a local hotel-intent module for destination, check-in, check-out, guests, rooms, and stay focus before the approved `hotels_partner_search` placement. The partner iframe and visible `Open hotel search` handoff continue to render through the governed registry shell, with visible disclosure, no-script/missing-configuration behavior, and provider-owned live availability/booking copy.

Security review: Passed locally. Input reads check scalar query values, unslash and sanitize text, bound guests/rooms, validate dates, and escape rendered output. Source scans found no app-owned PHP warnings, API keys, tokens, authorization headers, bearer strings, postback secrets, passwords, guaranteed-rate claims, direct-checkout claims, auto-booking, stored-inventory claims, WordPress-owned hotel inventory claims, or Booking.com White Label inventory promises. No provider secret, REST route, POST write, custom SQL, option, table, cron job, direct checkout, payment path, booking backend, or auto-publishing path was added.

REST permission review: Not applicable. P16.1 added no REST routes or changed public REST exposure.

Database/migration review: Not applicable. P16.1 added no custom tables, options, registry schema migration, cron jobs, or persistent records. The existing `hotels_partner_search` placement remained active, approved for `home` and `hotels`, and rendered in `iframe` mode.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop, widget-section, mobile, updated-intent, and keyboard-handoff screenshots confirmed the page renders nonblank, the hotel CSS loads, the provider placement is configured, the handoff is visible, and there is no horizontal overflow, duplicate ID, framework overlay, app-owned console error, app-owned failed request, or relevant failed request. Keyboard review reached local hotel controls, `Update hotel intent`, the provider iframe, and `Open hotel search`.

Regression review: Existing Phase 13 registry output, Phase 14 Hotels placement shell, P15 provider-owned booking/payment/support language, shared header/footer shell, Flights query noindex behavior, and Travelpayouts-controlled backend boundary remain intact. The hotel-specific CSS is split from `search-surface.css` to keep the shared stylesheet stable, and `search-surface.js` now cleans hotel intent query keys without changing the Flights cleanup behavior.

Validation performed: PHP syntax for changed PHP files; JavaScript syntax for `search-surface.js`; file-size checks; HTTP/source smoke for Hotels default, Hotels intent, and Flights query regression URLs; widget-registry smoke for `hotels_partner_search`; source scans for required hotel UI/disclosure/handoff text and negative secret/unsupported-claim terms; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots, updated-intent interaction, clean URL check, console/request health, duplicate-ID and overflow checks, and keyboard navigation; `git diff --check`.

Bugs found: Runtime interaction review found that the shared search-surface cleanup script was only enqueued on Flights, so Hotels intent submissions rendered the correct summary but left hotel query parameters in the visible URL. The first keyboard assertion was also too broad and matched the header Hotels link; a focused inspection confirmed the actual tab path was correct.

Bugs fixed: Enqueued `search-surface.js` for both Flights and Hotels, added hotel intent query keys to the cleanup list, and reran the browser interaction so updated hotel intent now renders the summary while cleaning the visible URL back to `/hotels/`. The keyboard test was tightened to the visible `Open hotel search` handoff and passed.

Bugs deferred: Provider-owned Trip.com iframe behavior and content-blocking fallback remain a later-phase watch item. City hotel guide templates, hotel maps/tables, hotel-specific SubID expansion, and final Phase 16 review remain later Phase 16 issues.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Plugin Security Handbook: Securing Input.
- WordPress Plugin Security Handbook: Securing Output.
- WordPress Common APIs Handbook: Sanitizing Data.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P16.1 local implementation and review gate passed. Keep Phase 16 overall `In Progress` until the remaining hotel/stays issues pass review and merge.

## Phase 16.2 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-101` City hotel guide templates and editorial modules. Reviewed Phase 16 objective, P16.1 Hotels page baseline, destination CPT/meta contracts, route related-link behavior, Phase 13 hotel placement registry constraints, current theme template conventions, source output, desktop/mobile runtime screenshots, and keyboard navigation.

Acceptance criteria result: Passed locally for the PR candidate. Published `destination` posts now render a city hotel guide archive and single template with editable WordPress post content plus registered destination hotel-guide meta fields for summary, neighborhoods, best-fit guidance, family, luxury, budget, and landmark notes. The Hotels page also surfaces published city hotel guide cards. Editorial modules do not claim to filter live results; provider-owned live rates, map filters, booking, payment, changes, and support remain in the partner surface.

Security review: Passed locally. Registered hotel-guide meta uses `baf_` keys, destination-only registration, textarea sanitization, `show_in_rest => false`, and existing edit-meta authorization. Template output sanitizes or escapes meta, post, URL, and query values before render. Source scans found no app-owned PHP warnings, API keys, tokens, authorization headers, bearer strings, postback secrets, passwords, guaranteed-rate claims, direct-checkout claims, auto-booking, unavailable placement state, or WordPress-owned hotel inventory claims.

REST permission review: Passed for scope. P16.2 added no REST endpoints and did not change public REST exposure. Registered hotel-guide meta remains private from REST because it uses `show_in_rest => false`.

Database/migration review: Passed for scope. No custom tables, options, migrations, cron jobs, or destructive data changes were added. The implementation registers additional post meta contracts only. Temporary destination post `313` and route post `314` were used for runtime validation, removed after validation, and confirmed at `temporary_posts_remaining=0`.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop/mobile screenshots confirmed destination guide archive, city guide detail, guide modules, Hotels guide teaser, provider section, and keyboard focus states render without horizontal overflow, duplicate IDs, framework overlays, blank pages, or app-owned console/request failures. The city guide provider placement renders configured Trip.com/Travelpayouts output with visible disclosure and handoff.

Regression review: Existing P16.1 Hotels intent behavior, Phase 13 registry output, Phase 14 Hotels placement shell, Phase 15 provider-owned booking/payment/support language, destination and route CPT contracts, header/footer continuity, and Travelpayouts-controlled backend boundary remain intact. The city guide consumes the existing approved `hotels` placement surface and uses `destination_single` only for channel/SubID context.

Validation performed: PHP syntax for changed PHP files; file-size checks; `git diff --check`; registered post-meta checks; plugin deactivate/reactivate; HTTP/source smoke for temporary destination single, destination archive, Hotels, and Hotels city intent URLs; source scans for SEO metadata, guide modules, related links, disclosure, provider placement output, secrets, warnings, and unsupported claims; attempted Codex in-app Browser validation; Playwright Chromium desktop/mobile screenshots, provider screenshots, console/request health, duplicate-ID and overflow checks, and keyboard navigation.

Bugs found: The first runtime pass showed the destination single template rendering the shared unavailable-placement state because it requested a new `destination` placement surface. The governed `hotels_partner_search` placement is currently approved for `home` and `hotels`, not `destination`.

Bugs fixed: The destination single template now renders the approved hotel placement with `surface="hotels"` while preserving `channel="destination_single"` and the destination slug in SubID context. The browser pass was rerun and confirmed configured provider output plus visible disclosure and handoff. The pre-PR code review also tightened new city-guide title and permalink output to use explicit `esc_html( get_the_title() )` and `esc_url( get_permalink() )` calls.

Bugs deferred: Provider-owned Trip.com console/runtime warnings remain a later-phase watch item when app-owned checks pass. Hotel map/table expansion, hotel-specific SubID strategy, and final Phase 16 review remain later Phase 16 issues.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Files.
- WordPress Plugin Security Handbook: Securing Input.
- WordPress Plugin Security Handbook: Securing Output.
- WordPress Custom Fields and Post Meta registration references.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P16.2 local implementation and review gate passed. Keep Phase 16 overall `In Progress` until the remaining hotel/stays issues pass review and merge.

## Phase 16.3 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-102` Hotel widget/table/map placements and SubIDs. Reviewed Phase 16 objective, Travelpayouts widget/SubID guidance, P16.1 Hotels page, P16.2 city guide templates, Phase 13 registry contracts, current Trip.com hotel partner URL behavior, placement frame CSS, source output, desktop/mobile runtime screenshots, and keyboard navigation.

Acceptance criteria result: Passed locally for the PR candidate. The registry schema now seeds `hotels_map_handoff` and `hotels_listing_handoff` companion placements when the existing hotel partner URL is safe for direct handoff. `/hotels/` and destination guide pages render a reusable hotel partner tools section with visible disclosure, map/listing copy, provider-owned booking boundaries, and unique readable SubIDs. The compact Trip.com search iframe crop is now scoped to `hotels_partner_search` only, so future hotel map/listing iframes are not clipped by that search-bar treatment.

Security review: Passed locally. No public REST routes, custom SQL, provider API calls, custom tables, cron jobs, direct checkout, payment path, or private-data write path was added. Registry private URLs remain stored in `baf_travelpayouts_widget_registry` and are read only by trusted server-side rendering. Templates sanitize class/channel/slug values and escape rendered copy, URLs, attributes, and details. Source scans found no app-owned PHP warnings, API keys, tokens, authorization headers, bearer strings, postback secrets, passwords, direct-checkout claims, auto-booking, Booking.com White Label promises, unavailable-placement state, or WordPress-owned hotel inventory claim.

REST permission review: Not applicable. P16.3 added no REST endpoints and did not change public REST exposure.

Database/migration review: Passed for scope. No custom tables or destructive migrations were added. The non-autoloaded registry option migrates from schema `1.0.2` to `1.0.3` idempotently, seeding only missing hotel companion placements and preserving later admin edits. Local registry smoke confirmed schema `1.0.3`, active `hotels_map_handoff`, active `hotels_listing_handoff`, and configured Trip.com handoff URLs.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop/mobile screenshots confirmed Hotels and destination guide companion sections render without horizontal overflow, duplicate IDs, clipped handoff controls, blank pages, or app-owned console/request failures. Keyboard navigation reached `Open hotel map` and `Open hotel listings` with visible focus. Link-only companion cards were tightened after visual review so they no longer reserve a large blank map/table frame when rendering only a handoff link.

Regression review: Existing `hotels_partner_search` iframe/handoff behavior, P16.1 hotel intent module, P16.2 city guide provider placement, Phase 13 consent/missing/no-script states, public SubID convention, and the Travelpayouts-controlled backend boundary remain intact. The shared placement part now supports multiple CSS classes safely, and the renderer adds widget-family/placement-key classes without exposing private registry data.

Validation performed: PHP syntax for changed PHP files; file-size checks; `git diff --check`; Local-socket WP-CLI registry migration and plugin status checks; HTTP/source smoke for `/hotels/` and a temporary destination guide; source scans for companion placements, CSS handles, SubIDs, disclosure, secrets, app warnings, and unsupported hotel claims; Playwright Chromium desktop/mobile screenshots, companion-section screenshots, console/request health, duplicate-ID and overflow checks, card-height checks, and keyboard navigation; Playwright admin smoke for `hotels_listing_handoff` family editing after Codex PR review; temporary destination and temporary admin-user cleanup.

Bugs found: WP-CLI initially failed against the default `localhost` socket outside Local's wrapper; using the Local `qRHZasMmV` MySQL socket resolved validation. Code review found the shared placement part collapsed multi-class strings into a single sanitized class. Visual review found link-only handoff cards reserved too much empty space before the button. Codex PR review found that the admin widget-family dropdowns did not include the new `hotel_listing` value, so saving `hotels_listing_handoff` could silently rewrite its family.

Bugs fixed: The validation command now uses the Local MySQL socket. The shared placement part now sanitizes multi-class strings class-by-class. Link-only hotel companion cards now use compact body sizing while future map/listing iframes remain protected by placement-specific frame metadata. The admin placement form and placement page now expose `hotel_listing` in their widget-family choices. Browser and keyboard checks were rerun after the layout fix.

Bugs deferred: Provider-owned Trip.com runtime behavior remains watchlist-only when app-owned checks pass. Dedicated hotel map/listing/table embeds still depend on Travelpayouts or partner dashboard code being available; until then the companion placements are honest sponsored handoff links with SubID tracking.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Code Reference: `wp_enqueue_style()`.
- WordPress Code Reference: `esc_html()`.
- WordPress Code Reference: `esc_url()`.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P16.3 local implementation and review gate passed. Keep Phase 16 overall `In Progress` until handoff-language guardrails, mobile/source review, and final Phase 16 review issues pass and merge.

## Phase 16.4 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-103` Handoff language, disclosure, and unsupported-filter guardrails. Reviewed Phase 16 objective, current P16.1-P16.3 hotel pages and placement templates, Travelpayouts affiliate tool/White Label boundaries, source output, visible disclosure behavior, desktop/mobile runtime screenshots, and keyboard navigation.

Acceptance criteria result: Passed locally for the PR candidate. Hotel pages now use clearer sponsored partner handoff language, avoid vague local-search CTA labels, and keep current availability, room inventory, filters, map controls, rates, taxes, policies, booking terms, payment, changes, and support with Travelpayouts, Trip.com, or the partner provider. Unsupported filters are described as editorial context or provider-result controls, and no Booking.com White Label inventory is promised.

Security review: Passed locally. No REST routes, provider calls, custom SQL, options, tables, cron jobs, private-data writes, direct checkout, or payment paths were added. Template output continues to escape copy, URLs, attributes, classes, and intent details. The shared placement shell now renders visible `Affiliate disclosure:` copy and links each monetized placement section to that disclosure with `aria-describedby`.

REST permission review: Not applicable. P16.4 added no REST endpoints and did not change public REST exposure.

Database/migration review: Not applicable. No custom tables, migrations, options, or destructive data changes were added. Temporary destination posts `318` and `319` were used for runtime validation and removed after validation.

UI review: Passed locally with real runtime screenshots and keyboard review. Desktop/mobile screenshots confirmed Hotels, hotel-intent, destination archive, and destination guide surfaces render nonblank pages without horizontal overflow, duplicate IDs, missing labelled disclosures, hidden disclosure text, or app-owned console/request failures. Keyboard navigation reached `Open partner search`, `Open hotel map`, and `Open hotel listings`.

Regression review: Existing P16.1 hotel intent behavior, P16.2 city guide templates, P16.3 companion placements/SubIDs, Phase 13 registry missing/consent states, and the Travelpayouts-controlled backend boundary remain intact. Disclosure labelling is shared through `template-parts/travel-search-placement.php`, so Flights and route placements also get clearer visible disclosure labels without changing provider behavior.

Validation performed: PHP syntax for changed PHP files; file-size checks; `git diff --check`; source scans for Booking.com/White Label promises, direct checkout, auto-booking, guarantees, unsupported local filters, vague hotel CTA text, and disclosure language; Playwright Chromium desktop/mobile screenshots, labelled-disclosure checks, console/request health, duplicate-ID and overflow checks, bad-claim checks, companion-disclosure visual check, and keyboard navigation; temporary destination cleanup.

Bugs found: Visual review found that the companion placement card height rules partially overlapped the new yellow disclosure band after disclosure labelling was added.

Bugs fixed: `hotel-guide.css` now keeps companion placement widget wrappers at auto height while preserving compact handoff frames, so the disclosure band sits below each widget body. Follow-up DOM and screenshot checks confirmed the disclosure starts with `Affiliate disclosure:` and is visible below map/listing handoffs.

Bugs deferred: Provider-owned Chromium WebGL performance warnings may still appear around embedded hotel widgets. They remain watchlist-only when app-owned checks pass and visible handoffs/disclosures work.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Plugin Security Handbook: Securing Output.
- Travelpayouts: Affiliate Partnership Platform overview.
- Travelpayouts Help Center: How to use Travelpayouts Quick Start Guide.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P16.4 local implementation and review gate passed. Keep Phase 16 overall `In Progress` until the remaining mobile/source review and final Phase 16 review issues pass and merge.

## Phase 16.5 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-104` Mobile widget/map layout and source review. Reviewed Phase 16 objective, P16.1-P16.4 hotel page and guide surfaces, current Travelpayouts/Trip.com placement boundaries, hotel companion placements, disclosure behavior, source output, responsive screenshots, and keyboard navigation.

Acceptance criteria result: Passed locally for the PR candidate. Hotels, hotel-intent, destination archive, and destination guide surfaces render across desktop, tablet, mobile, and 320px narrow widths with readable widget/map/listing handoff layouts, no app-owned runtime findings, and no local hotel inventory or booking-owner overreach.

Security review: Passed locally. P16.5 added no REST routes, provider calls, custom SQL, options, tables, cron jobs, private-data writes, direct checkout, or payment paths. Source scans found no API key, authorization, bearer, postback secret, private key, unsupported Booking.com White Label promise, direct checkout, auto-booking, or live-rate claim in app-owned output.

REST permission review: Not applicable. P16.5 added no REST endpoints and did not change public REST exposure.

Database/migration review: Not applicable. No schema, option, custom table, cron, or destructive data changes were added. Temporary destination post `320` was used for runtime validation, removed after validation, and confirmed at `temporary_posts_remaining=0`.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium captured desktop, tablet, mobile, and 320px narrow screenshots for `/hotels/`, a hotel-intent URL, `/destinations/`, and the temporary destination guide. The final report found no horizontal overflow, duplicate IDs, clipping problems, missing disclosures, app-owned console errors, app-owned failed requests, unsupported copy, sensitive source terms, or small touch targets. Keyboard navigation reached `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search`.

Regression review: Existing P16.1 hotel intent behavior, P16.2 city guide templates, P16.3 companion placement/SubID behavior, P16.4 disclosure/copy guardrails, Phase 13 widget registry states, and the Travelpayouts-controlled backend boundary remain intact. The CSS touch-target fixes are limited to static theme header navigation, menu toggles, and hotel guide title links.

Validation performed: file-size checks; attempted Codex in-app Browser validation; Playwright Chromium responsive/source review across four route states and four viewport sizes; Playwright keyboard review for hotel intent, map, listing, and partner handoff links; visual screenshot review; `git diff --check`; temporary destination cleanup.

Bugs found: The strict runtime pass found that the header menu toggle could shrink below 44px at 320px width. A second stricter pass also surfaced sub-44px hit areas on desktop header nav links and hotel guide card title links.

Bugs fixed: `header.css` now prevents menu-toggle flex shrink and gives header nav links a 44px minimum block target. `hotel-guide.css` now gives hotel guide card title links a 44px minimum block target. The final Playwright report returned zero findings.

Bugs deferred: Provider-owned/Chromium WebGL performance warnings can still appear around embedded partner widgets. They remain watchlist-only when app-owned checks pass and visible handoffs/disclosures work.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Plugin Security Handbook: Securing Output.
- Travelpayouts Help Center: How to use Travelpayouts Quick Start Guide.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P16.5 local implementation and review gate passed. Keep Phase 16 overall `In Progress` until the final Phase 16 review issue passes review and merge.

## Phase 16.6 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-105` final Phase 16 review and documentation gate. Reviewed P16.1 through P16.5 implementation notes, Hotels landing intent behavior, city hotel guide templates, companion hotel placement/SubID strategy, disclosure and handoff language, mobile/source review findings, widget registry state, SEO metadata behavior, source output, real runtime screenshots, keyboard navigation, and documentation alignment before Phase 17 starts.

Acceptance criteria result: Passed locally for the PR candidate. Phase 16 now provides a Hotels landing page, city hotel guides, governed hotel search/map/listing partner handoffs, editorial stay-type context, and hotel SubID placement strategy without creating a custom hotel inventory, live-search, checkout, or supplier-result backend.

Security review: Passed locally. P16.6 added no REST routes, provider calls, custom SQL, options, tables, cron jobs, private-data writes, direct checkout, payment paths, or new public data exposure. The SEO fix reads bounded scalar query parameters, unslashes the request data, and keeps output handled by WordPress `wp_robots` and existing escaped metadata rendering.

REST permission review: Not applicable. P16.6 added no REST endpoints and did not change public REST exposure.

Database/migration review: Not applicable. No schema, option, custom table, cron, or destructive data changes were added. Temporary destination post `321` was used for runtime validation, removed after validation, and confirmed at `temporary_posts_remaining=0`.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium captured `/hotels/`, a hotel-intent URL, `/destinations/`, and the temporary destination guide across desktop, tablet, mobile, and 320px narrow widths. The final report found no app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, unsupported hotel copy, sensitive source terms, missing affiliate disclosures, clipping problems, small touch targets, missing canonical URLs, or missing hotel-intent noindex output. Keyboard navigation reached `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search`.

Regression review: Existing P16.1 hotel intent behavior, P16.2 city guide templates, P16.3 companion placement/SubID behavior, P16.4 disclosure/copy guardrails, P16.5 touch-target fixes, Phase 13 widget registry states, Phase 15 flight-query noindex behavior, and the Travelpayouts-controlled backend boundary remain intact. The robots change is scoped to `/hotels/` requests that include hotel intent query parameters, including WordPress installs served from a subdirectory.

Validation performed: PHP syntax for changed PHP file; file-size checks; HTTP/source smoke for Hotels, hotel-intent, destination archive, and temporary guide pages; registry smoke for hotel placements; plugin status check; source checks for canonical/robots/disclosure/provider-boundary markers; WP-CLI subdirectory-install simulation for `/blog/hotels/` hotel-intent robots behavior; Playwright Chromium responsive/source review across four route states and four viewport sizes; Playwright keyboard review for hotel intent, map, listing, and partner handoff links; visual screenshot review; `git diff --check`; temporary destination cleanup.

Bugs found: The first final-gate Playwright/source pass found that transient hotel-intent query URLs canonicalized to `/hotels/` but still rendered indexable robots output instead of `noindex, follow`. Codex PR review then found that the initial hotel path helper missed WordPress installs served from a subdirectory such as `/blog/hotels/`.

Bugs fixed: `seo-metadata.php` now detects `/hotels/` requests with hotel intent query parameters and applies `noindex, follow` through the existing `wp_robots` filter. The hotel path helper strips the site's `home_url()` path prefix before comparing `/hotels/`, matching the existing Flights helper. Follow-up source smoke confirmed hotel-intent URLs render noindex/follow while keeping canonical `/hotels/`, and a subdirectory-install simulation confirmed `/blog/hotels/` hotel-intent requests are also noindexed.

Bugs deferred: No app-owned Phase 16 blocker remains after the final gate. Provider-owned Trip.com iframe behavior, content-blocking, and Chromium WebGL performance warnings remain watchlist-only when app-owned rendering, disclosures, keyboard navigation, and source checks pass.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Code Reference: `wp_robots`.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Plugin Security Handbook: Plugin Security.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.
- Travelpayouts Help Center: Setting up a White Label with Widget type.

Decision: P16.6 local review gate passed. Phase 16 Hotels and Stays Experience is ready to close after PR review, merge, and Linear sync; Phase 17 may start after that closeout.

## Phase 17.1 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Scope reviewed: `ONE-106` destination single and archive templates. Reviewed Phase 17 objective, destination CPT and taxonomy contracts, destination/hotel guide history from Phase 16, Travelpayouts provider-owned boundaries, current SEO metadata behavior, source output, real runtime screenshots, keyboard navigation, and documentation alignment.

Acceptance criteria result: Passed locally for the PR candidate. `/destinations/` now renders editable destination guide cards, and destination singles render broader SEO guide content with post body, taxonomy labels, fact panels, best-time/activity/seasonal modules, provider handoff cards, hotel planning modules, related destination links, related route links, and visible affiliate disclosure boundaries.

Security review: Passed locally. P17.1 added no public REST endpoints, provider calls, custom SQL, options, custom tables, cron jobs, private-data writes, checkout, payment path, or auto-publishing behavior. New destination meta keys are sanitized textareas, private from REST by default, and protected by existing edit-meta authorization. Template output escapes copy, URLs, attributes, taxonomy names, and metadata.

REST permission review: Not applicable. P17.1 added no REST endpoints and did not change public REST exposure.

Database/migration review: Passed for scope. No schema migration, custom table, option migration, or destructive data change was added. The core plugin registers destination-only post meta keys through existing WordPress meta registration.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium captured archive and single pages across desktop, mobile, and 320px narrow widths. The final report at `/tmp/one106-destination-report.json` found no blank pages, framework overlays, horizontal overflow, clipped visible elements, numeric seed labels, or missing expected destination-guide content. Keyboard navigation reached `Open provider flight search`, `Open provider hotel search`, `Open hotel map`, `Open hotel listings`, and `ONE-106 Related Coast Guide`.

Regression review: Existing Phase 16 hotel guide modules, hotel partner placement rendering, companion map/listing handoffs, affiliate disclosures, Phase 15 flight handoff links, and destination SEO metadata continue to render inside the WordPress-owned shell. The hotel partner placement keeps `surface="hotels"` with `channel="destination_single"` context, avoiding a new unapproved destination placement surface.

Validation performed: PHP syntax for changed PHP files; file-size checks; `git diff --check`; WP-CLI Local-socket taxonomy/meta smoke; HTTP/source smoke for `/destinations/` and a temporary destination guide; source scans for taxonomy labels, destination modules, provider handoffs, sensitive strings, direct checkout, auto-booking, unsupported inventory claims, and numeric seed labels; Playwright Chromium responsive screenshots and keyboard review; visual screenshot review.

Bugs found: The first runtime evidence used temporary posts whose taxonomy assignment command created terms literally named `27`, `28`, and `29`, so the page looked like it was rendering numeric labels. Self-review also found destination SEO descriptions still prioritized the legacy `baf_hotel_guide_summary` meta over the post excerpt, which could preserve old city-hotel wording on broadened destination pages. Codex PR review found the related-destination query became unconstrained when the current destination had no region, style, or season taxonomy terms.

Bugs fixed: Validation content was reassigned by taxonomy slug to `ONE-106 Coast`, `ONE-106 Family`, and `ONE-106 Fall`; the numeric temporary terms were deleted; source and Playwright checks were rerun and confirmed readable labels with `findingCount=0`. Destination SEO descriptions now use the post excerpt first, then destination facts, then the legacy hotel summary as a fallback. The related-destination query now forces an empty result when no shared taxonomy terms are present, preserving the intended empty state instead of listing arbitrary destinations.

Bugs deferred: No app-owned P17.1 blocker remains after the local gate. Later Phase 17 work still needs route template expansion, deal/taxonomy template work, internal-linking refinement, and final SEO content review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Template Hierarchy.
- WordPress Plugin Developer Handbook: Custom Post Types and Post Meta.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.

Decision: P17.1 local implementation and review gate passed. Keep Phase 17 overall `In Progress` until the remaining route/deal/taxonomy/internal-linking/SEO review issues pass review and merge.

## Phase 17.2 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Linear issue: `ONE-107`

Scope reviewed: `ONE-107` route single and archive templates. Reviewed Phase 17 objective, Phase 15 route/template/SEO/widget baseline, route CPT and meta contracts, Travelpayouts-owned result boundary, current route archive/single behavior, source output, real runtime screenshots, keyboard navigation, and documentation alignment.

Acceptance criteria result: Passed locally for the PR candidate. `/routes/` now includes route SEO module context, route singles render editable travel-time, airport, flexible-date, and destination hotel/activity modules, and route pages keep White Label search, low-price calendar, popular-route, route-map, alert intent, related routes, and matching destination-guide links inside the established provider handoff boundary.

Security review: Passed locally. P17.2 added no public REST endpoints, provider API calls, custom SQL, options, custom tables, cron jobs, private-data writes, checkout, payment path, auto-booking, or auto-publishing behavior. New route meta keys are sanitized textarea fields, private from REST by default, and protected by existing edit-meta authorization. Template output escapes copy, URLs, attributes, route facts, destination links, and metadata.

REST permission review: Not applicable. P17.2 added no REST endpoints and did not change public REST exposure.

Database/migration review: Passed for scope. No custom tables, destructive migrations, cron jobs, or option migrations were added. The implementation registers route-only post meta contracts only. Temporary route posts `329`, `330`, `331`, and destination post `332` were used for runtime validation, removed after validation, and confirmed at zero remaining.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium screenshots covered route archive, origin-filter archive, route single, and no-context route states across desktop/mobile/320px where applicable. Keyboard navigation reached `Open flight handoff`, `Watch route`, `Review low-price calendar module`, `Open destination hotel handoff`, `Explore destination activity prompts`, a related route link, and a matching destination guide link with visible focus.

Regression review: Existing P15 route archive/detail behavior, P15 route SEO metadata, P15 discovery widgets, P15 alert signup, P16 hotel handoff language, P17.1 destination guides, Phase 13 registry output, SubID/disclosure boundaries, header/footer continuity, and provider-owned booking/payment/support language remain intact.

Validation performed: PHP syntax checks for changed PHP files; file-size checks; `git diff --check`; plugin active check; WP-CLI route meta registration smoke; Node source smoke for `/routes/`, `/routes/?route_origin=nyc`, a temporary route guide, and a no-context route; source scans for route modules, destination guide links, hotel/activity handoffs, no-context empty states, secrets, direct checkout, auto-booking, unsupported inventory claims, fake prices, and fake scarcity; Playwright Chromium responsive screenshots; keyboard navigation review; visual screenshot review; temporary route/destination cleanup.

Bugs found: Pre-implementation review found the existing related-route query could become unconstrained when a route had no origin/destination airport context. Codex PR review found taxonomy-based related-route matching was unreachable whenever the current route had origin or destination airport meta, so long-tail routes with no airport-overlap peers could miss valid taxonomy peers. The first runtime browser classifier also treated provider-owned Travelpayouts/Aviasales `@babel/plugin-transform-react-jsx-source` and `sentry.avs.io` noise as app-owned findings.

Bugs fixed: Related routes now require shared origin/destination airport meta or shared route taxonomy terms before rendering; airport-meta matches are collected first and then taxonomy matches fill remaining related slots. Context-free route posts show empty-state copy rather than arbitrary cards. The runtime classifier now documents and filters the known provider-owned warning/abort noise while keeping app-owned console and request checks active.

Bugs deferred: No app-owned P17.2 blocker remains after the local gate. Later Phase 17 work still needs deal templates, taxonomy archives, internal-linking/monetized module rules, editor workflow review, and final SEO content review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: custom post type template files and template hierarchy.
- WordPress Developer Resources: `WP_Query`.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.
- Travelpayouts Help Center: Getting started with widgets.

Decision: P17.2 local implementation and review gate passed. Keep Phase 17 overall `In Progress` until the remaining deal/taxonomy/internal-linking/editor-workflow/final-review issues pass review and merge.

## Phase 17.3 Review - 2026-05-13

Status: `Completed`

Reviewer: Codex

Linear issue: `ONE-108`

Scope reviewed: `ONE-108` deal templates and seasonal/editorial modules. Reviewed Phase 17 objective, `travel_deal` CPT and taxonomy contracts, Phase 15/16 Travelpayouts-owned handoff boundary, current widget registry behavior, deal archive/single source output, real runtime screenshots, keyboard navigation, and documentation alignment.

Acceptance criteria result: Passed locally for the PR candidate. `/travel-deals/` now renders editable deal cards, and travel deal singles render post content, taxonomy labels, budget/date context, seasonal/weekend/style/activity/source modules, sponsored partner cards, an approved `flights_white_label_search` deal-surface placement with SubID output, related deal links, matching route links, matching destination links, and visible affiliate disclosure boundaries.

Security review: Passed locally. P17.3 added no public REST endpoints, provider API calls, custom SQL, options, custom tables, cron jobs, private-data writes, checkout, payment path, auto-booking, or auto-publishing behavior. New deal meta keys are sanitized textarea fields, private from REST by default, and protected by existing edit-meta authorization. Template output escapes copy, URLs, attributes, taxonomy names, metadata, and query-derived handoff values.

REST permission review: Not applicable. P17.3 added no REST endpoints and did not change public REST exposure.

Database/migration review: Passed for scope. No custom tables, destructive migrations, cron jobs, or option migrations were added. The implementation registers deal-only post meta contracts and migrates the existing widget registry schema to allow the already-approved White Label search placement on the `deal` surface.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium screenshots covered deal archive, deal single, and no-context deal states across desktop/mobile/320px where applicable. Keyboard navigation reached `Open flight handoff`, `Review partner cards`, `Browse deal ideas`, `Open hotel handoff`, `Explore activity prompts`, a related deal link, a matching route link, and a matching destination guide link with visible focus.

Regression review: Existing Phase 15 flight handoff, Phase 16 hotel handoff language, P17.1 destination guides, P17.2 route guides, registry SubID generation, disclosure rendering, header/footer continuity, and provider-owned booking/payment/support language remain intact.

Validation performed: PHP syntax checks for changed PHP files; file-size checks; `git diff --check`; plugin active check; WP-CLI deal meta registration smoke; widget registry deal-surface smoke; Node source smoke for `/travel-deals/`, a temporary deal brief, and a no-context deal brief; source scans for deal modules, SubID-bearing placement output, disclosures, secrets, direct checkout, auto-booking, unsupported inventory claims, fake prices, and fake scarcity; Playwright Chromium responsive screenshots; keyboard navigation review; visual screenshot review; Codex review follow-up Playwright smoke proving a route sharing destination airport only appears on a deal single; temporary deal/route/destination cleanup confirmed at zero remaining.

Bugs found: Visual review found the new deal hero could tuck under the fixed header on mobile and 320px narrow widths. The first browser assertion also used labels that did not match rendered text after CSS text transforms, so the evidence script was tightened to assert visible page content instead of implementation labels. Code review found the deal single hero title used raw theme title output instead of an explicit escaped title. Codex PR review found matching route links required both origin and destination airport metadata instead of matching either shared airport.

Bugs fixed: `deal-surface.css` now adds fixed-header clearance before the first deal hero text on desktop, mobile, and 320px narrow widths. `single-travel_deal.php` now renders the hero title with `esc_html( get_the_title() )`, and related route matching now uses an `OR` relation across origin/destination airport metadata so one shared airport can populate internal links. The final Playwright report at `/tmp/one108-deal-report.json` returned `findingCount=0`, including no hero/header overlap.

Bugs deferred: No app-owned P17.3 blocker remains after the local gate. Later Phase 17 work still needs taxonomy archives/internal-linking rules, editor workflow review, accessibility/SEO/disclosure pass, and final review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: custom post type template files and template hierarchy.
- WordPress Developer Resources: `WP_Query`.
- WordPress Plugin Handbook: Custom Meta Boxes.
- WordPress Common APIs Handbook: Escaping Data.
- Travelpayouts Help Center: Travelpayouts White Label Web Setup Guide.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: ID and SubID affiliate marker and additional marker.

Decision: P17.3 local implementation and review gate passed. Keep Phase 17 overall `In Progress` until the remaining taxonomy/internal-linking/editor-workflow/accessibility/SEO/final-review issues pass review and merge.

## Phase 17.4 Review - 2026-05-13

Status: `In Review`

Reviewer: Codex

Linear issue: `ONE-109`

Scope reviewed: `ONE-109` taxonomy archives and internal-linking rules. Reviewed Phase 17 objective, custom taxonomy contracts, destination/route/deal template boundaries, public-only WordPress archive behavior, SEO metadata output, source smoke, real runtime screenshots, keyboard navigation, and documentation alignment.

Acceptance criteria result: Passed locally for the PR candidate. `travel_region`, `travel_style`, `travel_vertical`, and `travel_season` archives now render term-specific guide pages with term descriptions, internal-linking rule cards, Flights/Hotels/Destinations/Routes/Deals/Trip Planner handoffs, paginated public content cards, empty states, and taxonomy-aware SEO metadata.

Security review: Passed locally. P17.4 added no public REST endpoints, provider API calls, custom SQL, options, custom tables, cron jobs, private-data writes, checkout, payment path, auto-booking, or auto-publishing behavior. Template output escapes term names, descriptions, post titles, excerpts, URLs, attributes, and query-derived links. The archive query is bounded, paginated, and public-only.

REST permission review: Not applicable. P17.4 added no REST endpoints and did not change public REST exposure.

Database/migration review: Passed for scope. No schema migration, custom table, option migration, or destructive data change was added. Temporary validation posts `344` through `358` and temporary term `41` were removed after runtime validation and confirmed absent.

UI review: Passed locally with real runtime screenshots and keyboard review. Playwright Chromium screenshots covered the taxonomy archive across desktop, mobile, and 320px narrow widths. Keyboard navigation reached `Open flight handoff`, `Open hotel handoff`, `Destinations`, `Routes`, `Deals`, `Trip planner`, a destination guide link, and pagination `Next` with visible focus.

Regression review: Existing P17.1 destination guides, P17.2 route guides, P17.3 travel deal guides, Phase 15 Flights shell links, Phase 16 Hotels shell links, header/footer continuity, taxonomy label rendering, and provider-owned booking/payment/support language remain intact. Non-public `trip_plan`, `travel_alert`, and `travel_partner` content stays out of public taxonomy archive queries.

Validation performed: PHP syntax checks for changed PHP files; file-size checks; `git diff --check`; Node source smoke for temporary taxonomy archive page one and page two; source scans for internal-linking rules, public destination/route/deal cards, pagination, private/non-public content exclusion, secrets, direct checkout, auto-booking, and fake scarcity; Playwright Chromium responsive screenshots; keyboard navigation review; visual screenshot review; follow-up canonical smoke for taxonomy page one, page two, and tracked page-two requests after Codex review; main-query smoke for public/private/non-public taxonomy content; temporary taxonomy content cleanup.

Bugs found: The first runtime touch-target pass found mobile nav links could render below the 44px interactive target height when the taxonomy page loaded at mobile and 320px widths. Codex PR review found taxonomy archive page two and deeper pagination emitted a page-one canonical URL. A second Codex pass found the paginated canonical helper could inherit tracking query parameters from the current request URL. A third Codex pass found the template's secondary `WP_Query` could drift from WordPress main-query pagination and 404 handling.

Bugs fixed: `mobile-nav.css` now renders mobile nav links as centered inline-flex controls with a 44px minimum block size. `seo-metadata.php` now bounds the main taxonomy archive query with `pre_get_posts`, and `taxonomy.php` renders the main loop instead of a secondary paginated query. Taxonomy canonicals are built from the clean term URL; page two and deeper append the pagination path without preserving request tracking parameters. Follow-up Playwright validation returned `findingCount=0`, canonical smoke confirmed page two emits `/travel-styles/one109-canonical-smoke/page/2/` even when the request includes `?utm_source=codex`, and main-query smoke confirmed private destination plus published `trip_plan` validation content stays hidden.

Bugs deferred: No app-owned P17.4 blocker remains after the local gate. Later Phase 17 work still needs editor workflow review, accessibility/SEO/disclosure pass, and final review.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-review-log.md`.

Research consulted:
- WordPress Theme Handbook: Taxonomy Templates.
- WordPress Developer Resources: `WP_Query`.
- WordPress Developer Resources: `pre_get_posts`.
- WordPress Developer Resources: `paginate_links`.
- WordPress Developer Resources: `user_trailingslashit`.
- WordPress Common APIs Handbook: Escaping Data.

Decision: P17.4 local implementation and review gate passed. Keep Phase 17 overall `In Progress` until the remaining editor-workflow/accessibility/SEO/final-review issues pass review and merge.
