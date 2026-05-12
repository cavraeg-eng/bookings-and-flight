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

Status: `In Review`

Reviewer: Codex

Scope reviewed: `ONE-80` placement data model and registry service. Reviewed Phase 11 backend mode, Phase 12 widget-frame prerequisites, Phase 13 objective and child order, current `bookings-flights-core` settings/options/capability/service patterns, existing White Label and Trip.com shortcode wrappers, and Travelpayouts SubID/widget guidance.

Acceptance criteria result: Passed for P13.1 implementation scope. The registry data model includes placement key, name, vertical, context, widget family, render mode, embed source/mode/reference/url, status, SubID pattern, public surfaces, consent flag, disclosure copy/rule, frame reservations, fallback metadata, and notes. The service sanitizes writes, installs an idempotent non-autoloaded option, and seeds current Flights/Hotels placements from existing safe settings. Admin UI and frontend rendering remain in later Phase 13 child issues.

Security review: Passed for the service seam. Private embed references, embed URLs, and admin notes are available through capability-gated admin service calls requiring `manage_baf_affiliates` or `manage_baf_settings`; active configured placements also have a trusted server-side rendering read path so shortcodes/blocks do not bypass the registry. Public placement projections strip private embed values and notes. Pasted Travelpayouts script/iframe/link snippets are reduced to approved URLs or references rather than storing arbitrary raw embed code.

REST permission review: Not applicable. No REST route was added in P13.1. Future REST or block-editor consumers must use the public projection unless the request is already capability-gated; the rendering read path is for trusted PHP renderers only.

Database/migration review: Passed. No custom table was introduced. `baf_travelpayouts_widget_registry` is installed as a WordPress option using the Options API, is non-autoloaded, is normalized non-destructively on bootstrap/activation, and preserves malformed stored placements during normalization so a page load cannot silently delete recoverable registry data.

UI review: Not applicable for runtime UI. P13.1 creates the storage/service layer only; admin management UI remains in `ONE-81`.

Regression review: Existing `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes remain unchanged. Existing `baf_travelpayouts_settings` fields remain intact and are used only to seed the first registry records when the registry option is missing.

Validation performed: PHP syntax checks for the new service, plugin bootstrap, and activator; option/service smoke check for install, public/private projection, trusted rendering read path, capability gate, sanitizer, malformed placement preservation, non-approved iframe path rejection, dashboard script URL rejection in iframe mode, temporary administrator save, temporary delete, SubID normalization, and idempotent registry normalization; plugin deactivate/reactivate smoke check; `git diff --check`.

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

Decision: `ONE-80` can move to PR review after Codex checks the implementation branch. Phase 13 remains `In Progress` for the remaining registry UI, wrapper, state, security, and review child issues.
