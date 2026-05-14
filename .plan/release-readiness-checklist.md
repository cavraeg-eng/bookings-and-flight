# Release Readiness Checklist

Phase 19 launch gate review date: 2026-05-14

Status: Ready for internal release-candidate review after PR review and merge. No app-owned launch blocker remains in the local WordPress/Travelpayouts-first product gate. Public production launch still needs environment-specific Travelpayouts account/domain validation on the deployed site.

## Checklist

- [x] Phase 0-19 status trail reviewed through the current phase review log.
- [x] Architecture baseline reviewed; no contract renames required.
- [x] Applicable `AGENTS.md` project instructions confirmed.
- [x] Official WordPress documentation consulted for plugin security, REST endpoints, privacy hooks, cron behavior, and launch-gate validation.
- [x] Official Travelpayouts documentation consulted for widgets, White Label, ID, and SubID reporting boundaries.
- [x] Security review completed for capabilities, nonces, sanitization, escaping, secret masking, external provider gates, saved trips, alerts, AI, reports, and private data handling.
- [x] REST route inventory completed for concrete app-owned `/wp-json/baf/v1/*` routes.
- [x] Protected route permission failures and explicit permission callbacks were reviewed in P19.6 and rechecked for current core routes in P19.8.
- [x] Public routes and public page source were reviewed for safe response contracts and secret exposure.
- [x] Activation/deactivation lifecycle tested for `bookings-flights-core` in the final gate.
- [x] Custom tables verified: `bf_clicks`, `bf_ai_sessions`, and `bf_provider_stats`.
- [x] Cron scheduling verified for `baf_refresh_cached_offers`, `baf_process_travel_alerts`, `baf_sync_provider_stats`, and `baf_cleanup_job_records`.
- [x] WordPress privacy exporter and eraser hooks verified as registered.
- [x] Admin settings, integrations, widget placements, reports, and core settings surfaces reviewed for capability gates, escaped output, labels, nonces, and target sizes.
- [x] Frontend Flights, Hotels, Trip Planner, Saved Trips, destination, route, deal, taxonomy, and homepage surfaces reviewed across Phase 14-19 runtime passes.
- [x] Travelpayouts widget, White Label, Trip.com hotel handoff, SubID, disclosure, and provider-owned result/booking boundaries reviewed.
- [x] AI demo/live readiness, consent gates, schema validation, draft saving, and local handoff-intent boundaries reviewed.
- [x] Saved trip delete/export/erase behavior reviewed.
- [x] Alert dedupe, limit, email queue, signed delete-confirmation, cron, and privacy behavior reviewed.
- [x] Admin report privacy guardrails and Travelpayouts Performance report source-of-truth copy reviewed.
- [x] P19.7 runtime browser regression covered 27 screenshots and 8 keyboard paths with 0 app-owned findings.
- [x] File-size scan completed for tracked core plugin and static theme source; no source file exceeds 600 lines. `themes/bookings-and-flights-static/functions.php` is exactly 600 lines and should be split before further theme bootstrap expansion.
- [x] Known issues triaged.
- [x] Regression watchlist updated.

## Launch Blockers

No app-owned local launch blocker remains after the Phase 19 gate.

Public production launch still requires:

- Deploy-environment validation with the real Travelpayouts account, domain, White Label settings, and production browser mix.
- Confirmation that Trip.com or other hotel partner embeds are not blocked by the production content-security, caching, consent, or browser-extension environment; keep the visible sponsored handoff link as the fallback.
- A separate tracking decision before staging or modifying the currently untracked affiliate bridge/content manager plugin source as part of launch hardening.

## Deferred Non-Blocking Items

- Platform API port documentation mismatch remains low severity until platform integration work resumes.
- WP-CLI emits PHP 8.5 deprecation warnings from bundled WP-CLI dependencies; commands succeeded.
- The official Travelpayouts plugin still emits PHP 8.5 deprecation warnings in WP-CLI/admin contexts. Public source scans for the app-owned launch surfaces did not show those warnings.
- Provider-owned Travelpayouts, Trip.com, pixel, iframe, CORS, WebGL, and browser-extension noise remains watchlist-only when app-owned rendering, disclosure, source, keyboard, and handoff checks pass.
- `themes/bookings-and-flights-static/functions.php` is at the 600-line limit; split it before adding more theme bootstrap logic.
- The untracked local `bookings-and-flights-content-manager` plugin remains a deferred field-pipeline candidate and should not be treated as tracked launch code without a separate decision.

## Release Notes Draft

Bookings and Flights is ready for internal release-candidate review as a WordPress-native travel discovery, AI planning, SEO, retention, and affiliate handoff product.

The MVP remains an affiliate discovery and handoff platform, not a direct OTA. WordPress owns content, search shells, saved-trip intent, alert intent, AI planning drafts, privacy/export behavior, local analytics, and admin reporting. Travelpayouts, Trip.com, White Label, and partner providers own live search results, availability, bookings, payments, changes, support, affiliate attribution, and definitive revenue/conversion reporting.

Research consulted:
- WordPress Plugin Security Handbook: https://developer.wordpress.org/plugins/security/
- WordPress REST API Handbook: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- WordPress Cron Handbook: https://developer.wordpress.org/plugins/cron/
- WordPress Plugin Privacy Handbook: https://developer.wordpress.org/plugins/privacy/
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: White Label Web Setup Guide.
- Travelpayouts Help Center: ID and SubID.
