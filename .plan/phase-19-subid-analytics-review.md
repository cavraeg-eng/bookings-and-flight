# Phase 19.4 SubID Analytics Review

Date: 2026-05-14

Linear issue: `ONE-123`

Status: In Review

## Summary

P19.4 adds a readable Travelpayouts SubID reporting map to the admin Reports screen and CSV export. The map covers major home, Flights, Hotels, destination guide, route guide, AI planner, saved-trip, and alert surfaces. It uses existing registry placements where configured and mapped-only entries where a later surface needs stable reporting language before it has a live provider placement.

The Reports screen also shows observed local SubID click rows from signed affiliate handoff events in `bf_clicks`. Those rows are explicitly local operational analytics only. Travelpayouts Performance reports remain the source of truth for partner clicks, searches, bookings, conversion, and earnings by SubID.

## Review Gate

- Scope review: Passed. The implementation matches P19.4: SubID reporting map, local click analytics, provider-source-of-truth copy, CSV export rows, and privacy-aware reporting.
- Functional happy path: Passed. Admin Reports renders the map and observed local SubID table when local click rows exist.
- Empty/unavailable states: Passed. Empty local SubID rows explain unavailable local records, not zero Travelpayouts revenue or conversion. Null provider metrics render as `Unavailable`.
- Security/data review: Passed. SubID examples contain placement context only and no names, emails, IP addresses, raw prompts, private trip details, per-user identifiers, provider credentials, raw payloads, booking IDs, payment data, or live inventory.
- UI review: Passed with Playwright Chromium desktop and mobile screenshots plus keyboard review after the Codex in-app Browser connection timed out.

## Validation

- PHP syntax:
  - `plugins/bookings-flights-core/includes/services/class-travelpayouts-subid-map-service.php`
  - `plugins/bookings-flights-core/includes/reports/class-reporting-repository.php`
  - `plugins/bookings-flights-core/includes/reports/class-reporting-service.php`
  - `plugins/bookings-flights-core/includes/admin/class-reports-page.php`
  - `plugins/bookings-flights-core/includes/settings/class-settings-manager.php`
- File-size review: new and changed PHP files remain under the 600-line project limit.
- WP-CLI smoke: temporary local click row verified map count, observed row hydration, SubID character constraints, provider-owned conversion messaging, and cleanup.
- Runtime browser review: `/tmp/one123-runtime-review-report.json` returned `status=pass` and `findingCount=0`.
- Screenshots:
  - `/tmp/one123-reports-desktop.png`
  - `/tmp/one123-reports-mobile.png`
- Cleanup: temporary click, provider-stat, and admin-user fixtures were deleted after validation.

## Bugs

Found:

- The first temporary validation click UUID exceeded the `bf_clicks.event_uuid` column length.
- Mobile Reports tables could overflow the admin panel once the SubID map added long placement and source-of-truth copy.

Fixed:

- The validation fixture now uses a valid UUID-sized event identifier.
- Admin report panels now constrain grid tracks, allow table overflow inside the panel, and wrap code values so the Reports page has no mobile horizontal overflow.

Deferred:

- None for P19.4 SubID analytics scope. Remaining release-readiness and final Phase 19 gate work stay in later Phase 19 issues.

## Research Consulted

- Travelpayouts Help Center: ID and SubID affiliate marker and additional marker.
- WordPress Plugin Security Handbook: sanitizing, escaping, capability, and privacy boundaries.
- WordPress REST API Handbook: permission callback guidance.
- WordPress Plugin Handbook: Creating Tables with Plugins.
- WordPress Plugin Handbook: Personal Data Exporter and Personal Data Eraser behavior for privacy-aware analytics context.
