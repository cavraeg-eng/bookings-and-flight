# Phase 19.3 Privacy Export/Erase Review

Date: 2026-05-13

Linear issue: `ONE-122`

Status: In Review

## Scope

P19.3 implements WordPress personal-data export and erasure behavior for retention data introduced in Phase 18 and Phase 19:

- member-owned saved-trip records in private `trip_plan` posts
- email-owned travel alert records in private `travel_alert` posts
- AI-generated trip-plan draft posts
- AI session log rows in `$wpdb->prefix . 'bf_ai_sessions'`

## Implementation

`BAF\Core\Privacy\Personal_Data_Manager` registers WordPress privacy exporters and erasers, while `BAF\Core\Privacy\Personal_Data_Records` owns the shared query, link, formatting, and erasure helpers for:

- `baf-saved-trips`
- `baf-travel-alerts`
- `baf-ai-trip-plans`
- `baf-ai-sessions`

Saved-trip exports include safe local resume links for Flights, Hotels, and Trip Planner. Alert exports include a local Flights price-alert resume link. AI trip-plan exports include a local WordPress draft edit link when available. AI session exports include minimized run metadata and summaries only.

Saved-trip, alert, and AI draft erasers permanently delete matching posts. AI session erasure anonymizes rows by clearing user ID, source post ID, run UUID, request/output hashes, output summary, and error message while leaving non-user aggregate fields available for operational reporting.

## Security And Privacy

Passed locally. Export callbacks are scoped to the requester email and do not expose unrelated users' records. Export payloads omit raw AI prompts, itinerary JSON, provider payloads, request/output hashes, provider credentials, booking IDs, payment data, confirmation numbers, and live inventory.

Existing saved-trip REST routes remain logged-in, nonce-protected, owner-scoped, and consent-gated for writes. P19.3 adds no new REST endpoint.

## Validation

Passed locally:

- PHP syntax checks for changed PHP files.
- File-size review: `class-personal-data-manager.php` is 310 lines and `class-personal-data-records.php` is 280 lines.
- `git diff --check`.
- `bookings-flights-core` deactivate/reactivate/is-active check.
- WP-CLI privacy smoke with 54 assertions.
- WP-CLI retained-record erasure smoke with 31 assertions.
- WP-CLI published-AI privacy smoke with 5 assertions.
- Playwright Chromium runtime screenshots and keyboard review after the Codex in-app Browser connection timed out.

Runtime evidence:

- `/tmp/one122-runtime-review-report.json`
- `/tmp/one122-saved-trips-resume-links-desktop.png`
- `/tmp/one122-planner-resume-desktop.png`
- `/tmp/one122-alert-resume-flight-desktop.png`
- `/tmp/one122-saved-trips-mobile.png`

## Bugs

Found and fixed:

- Temporary privacy-smoke assertions were too literal about JSON-escaped URLs; the validation harness was tightened.
- Codex review found that retained saved-trip records could stop erasure pagination too early; the eraser now reports retained records without marking the batch done when more records remain.
- Codex review found that published AI-attributed trip-plan posts could be included in draft privacy erasure; AI trip-plan export/erase now stays scoped to draft/private non-public records.
- Provider-owned widget console noise, including `tpembars.com` CORS/config messages, was classified as watchlist-only after app-owned request checks passed.

Deferred:

- None for P19.3 privacy/export/delete scope. Remaining analytics and release-readiness work stays in later Phase 19 issues.

## Research Consulted

- WordPress Plugin Handbook: Personal Data Exporter.
- WordPress Plugin Handbook: Personal Data Eraser.
- WordPress Code Reference: `wp_privacy_personal_data_exporters`.
- WordPress Code Reference: `wp_privacy_personal_data_erasers`.
- WordPress Plugin Security Handbook: sanitizing, escaping, capability, and privacy boundaries.
