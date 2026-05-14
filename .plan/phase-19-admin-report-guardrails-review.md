# Phase 19.5 Admin Report Guardrails Review

Date: 2026-05-14

Linear issue: `ONE-124`

Status: In Review

## Summary

P19.5 hardens the admin Reports flow so reporting stays useful without leaking private data or overstating partner revenue authority. The Reports dashboard and CSV export now include explicit guardrail rows for:

- `view_baf_reports` capability gating
- approved 7, 30, 90, and 365 day windows
- bounded report rows
- private-data exclusions
- Travelpayouts Performance reports as the source of truth for revenue, bookings, searches, conversion, and earnings

Report-only users who have `view_baf_reports` but not settings or widget-placement capabilities now land on the Reports page from the top-level Bookings & Flights admin menu.

## Review Gate

- Scope review: Passed. The implementation matches P19.5: capability gate, bounded analytics copy, privacy-aware report output, and revenue/conversion source-of-truth guardrails.
- Permission review: Passed. No-cap users are denied. Report-only users can access Reports through the admin menu without gaining settings or widget-placement access.
- Query bounds review: Passed. Invalid date-window requests fall back to 30 days; dashboard copy documents approved windows and bounded row limits.
- Privacy/source review: Passed. Reports and CSV export document that raw IPs, user agents, referrers, prompts, provider payloads, and secrets are excluded, and that Travelpayouts Performance reports remain the provider source of truth.
- UI review: Passed with Playwright Chromium desktop and mobile screenshots plus keyboard review after the Codex in-app Browser connection timed out.

## Validation

- PHP syntax:
  - `plugins/bookings-flights-core/includes/admin/class-admin-manager.php`
  - `plugins/bookings-flights-core/includes/reports/class-reporting-service.php`
  - `plugins/bookings-flights-core/includes/admin/class-reports-page.php`
- File-size review: changed PHP files remain under the 600-line project limit.
- WP-CLI permission smoke: no-cap denial, report-only admin menu routing, fallback 30-day report window, guardrail render, forbidden internal-field absence, guardrail CSV rows.
- Runtime browser review: `/tmp/one124-runtime-review-report.json` returned `status=pass` and `findingCount=0`.
- Screenshots:
  - `/tmp/one124-reports-desktop.png`
  - `/tmp/one124-reports-mobile.png`
- Cleanup: temporary report-only users were deleted after validation.

## Bugs

Found:

- Report-only users with `view_baf_reports` could render Reports directly, but the top-level admin menu still routed non-settings users toward widget placement behavior instead of Reports.
- The first browser keyboard traversal used an administrator account and did not reach report controls within the initial tab window because the WordPress admin menu was intentionally long.

Fixed:

- `Admin_Manager` now routes report-only top-level menu access to `Reports_Page::render()`.
- Runtime validation now uses a report-only user and the WordPress skip-link path to verify keyboard access to the Reports date selector, Apply button, and Export CSV link.

Deferred:

- None for P19.5 admin report guardrails scope. Remaining full security/release-readiness gates stay in later Phase 19 issues.

## Research Consulted

- WordPress Plugin Security Handbook: checking user capabilities, sanitizing, and escaping.
- WordPress Roles and Capabilities documentation.
- Travelpayouts Help Center: ID and SubID affiliate marker and additional marker.
