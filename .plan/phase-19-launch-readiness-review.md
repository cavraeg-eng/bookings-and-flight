# Phase 19 Launch Readiness Review

Date: 2026-05-14

Linear issue: `ONE-127`

Status: Ready for PR review.

## Scope Reviewed

- Phase 11 through Phase 19 delivery notes and review gates.
- Travelpayouts-controlled backend boundary, widget registry, White Label, Trip.com hotel handoff, and SubID reporting.
- Saved trips, alerts, privacy export/erase, AI planner, local handoff intent, admin reports, security/source exposure, browser regression, known issues, and regression watchlist.
- Launch validation checklist, architecture baseline, validation baseline, and current LocalWP runtime state.

## Result

No app-owned local launch blocker remains after the Phase 19 gate. The project is ready for internal release-candidate review after this branch is reviewed, merged, and Linear is synced.

Public production launch still needs environment-specific validation with the real deployed domain, Travelpayouts account/domain configuration, White Label setup, Trip.com or other hotel partner embed behavior, caching/content-security settings, consent settings, and the target browser mix.

## Validation Performed

- `bookings-flights-core` deactivate/reactivate/is-active check passed with the LocalWP MySQL socket.
- Core route inventory confirmed callable permission callbacks for `/baf/v1/destinations`, `/baf/v1/routes`, `/baf/v1/affiliate/click`, `/baf/v1/ai/itinerary`, `/baf/v1/ai/handoff`, and `/baf/v1/saved-trips`.
- Cron schedule check confirmed `baf_refresh_cached_offers`, `baf_process_travel_alerts`, `baf_sync_provider_stats`, and `baf_cleanup_job_records` are scheduled.
- Custom table check confirmed `bf_clicks`, `bf_ai_sessions`, and `bf_provider_stats` are present.
- Privacy hook check confirmed `wp_privacy_personal_data_exporters` and `wp_privacy_personal_data_erasers` are registered.
- HTTP checks confirmed `/flights/` and `/hotels/` return `200`.
- Public source scans across `/flights/`, `/hotels/`, `/trip-planner/`, and `/saved-trips/` found no PHP warnings, fatals, parse errors, deprecated output, textdomain timing notices, API key patterns, bearer/authorization strings, postback secret patterns, raw prompt strings, or request/output hash field names.
- File-size scan found no tracked core plugin or static theme PHP/CSS/JS source file above 600 lines; `themes/bookings-and-flights-static/functions.php` is exactly 600 lines and should be split before more theme bootstrap logic is added.
- `git diff --check` passed.

WP-CLI emitted the known PHP 8.5 deprecation warnings from bundled WP-CLI dependencies, and full active-plugin checks can still print known official Travelpayouts PHP 8.5 deprecations in CLI/admin contexts. Commands completed successfully.

## Bugs Found

- No new app-owned production-code blocker was found in the P19.8 gate.
- The launch checklist was stale from the Phase 9 review and did not reflect Phase 11-19 Travelpayouts, AI, retention, privacy, report, security, and browser-regression evidence.

## Bugs Fixed

- Updated the launch checklist from the old Phase 9 release note into the current Phase 19 launch-readiness gate.
- Reconciled phase plan, known issues, regression watchlist, validation baseline, and architecture notes with the final Phase 19 status and deferred production-launch checks.

## Deferred Items

- Production Travelpayouts account/domain, White Label, partner embed, consent, caching/content-security, and browser-mix validation must be completed on the deployed environment before public go-live.
- The official Travelpayouts plugin continues to produce PHP 8.5 deprecation warnings in CLI/admin contexts; public source scans for the validated app-owned launch pages are clean.
- The currently untracked affiliate bridge/content manager plugin source should not be modified or staged without a separate tracking decision.
- `themes/bookings-and-flights-static/functions.php` is at the 600-line limit.

## Research Consulted

- WordPress Plugin Security Handbook: https://developer.wordpress.org/plugins/security/
- WordPress REST API Handbook: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- WordPress Cron Handbook: https://developer.wordpress.org/plugins/cron/
- WordPress Plugin Privacy Handbook: https://developer.wordpress.org/plugins/privacy/
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: White Label Web Setup Guide.
- Travelpayouts Help Center: ID and SubID.

## Decision

P19.8 can move to PR review. Move Linear `ONE-127` and parent `ONE-67` to Done only after PR review, merge, and branch cleanup are complete.
