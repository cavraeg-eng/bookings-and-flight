# Phase 19.1 Saved Trips Review

Date: 2026-05-13

Status: In Review

Linear issue: `ONE-120`

Scope reviewed: Saved-trip board and anonymous-to-member save flow for Phase 19.1. Reviewed Phase 18 completion docs, `trip_plan` privacy boundaries, Travelpayouts placement registry/SubID behavior, frontend route ownership, REST permissions, member/session behavior, deletion, and browser accessibility/responsive behavior.

Acceptance criteria result: Passed locally for the PR candidate. Users can save, resume/edit, continue to local flight/hotel/planner paths, and delete member-owned trip intent. Anonymous visitors are shown a clear sign-in handoff before any storage. Saved data is minimized to local intent plus public placement/SubID context and does not claim or store partner-owned booking inventory.

Security and privacy review: Passed locally. The saved-trip REST routes require a logged-in user with `read` plus an explicit REST nonce. Create/update requires local-storage consent. Other users receive `404` for item read/delete attempts. Saved context omits provider URLs, booking IDs, payment identifiers, confirmation numbers, live availability, API keys, raw AI prompts, AI itinerary payloads, and AI handoff intents. Immediate user deletion is available through REST and the board; WordPress personal-data exporter/eraser hooks remain planned for P19.3.

REST review: Passed locally. `GET|POST /baf/v1/saved-trips` and item read/update/delete routes are registered with explicit permission callbacks. The WP-CLI smoke covered anonymous denial, missing-nonce denial, missing-consent denial, valid save, list, cross-user read/delete denial, invalid date rejection, valid update, owner resume payload, owner delete, and cleanup.

Database/migration review: No custom table, migration, cron, or destructive schema change was added. Saved trips use the existing private `trip_plan` CPT plus registered private post meta keys: `baf_saved_trip_context`, `baf_saved_trip_saved_at`, `baf_saved_trip_status`, `baf_saved_trip_travelers`, and `baf_saved_trip_user_id`.

UI review: Passed locally with real runtime screenshots and keyboard review. The Codex in-app Browser connection timed out after 15 seconds, so Playwright Chromium was used. Screenshots covered logged-out sign-in handoff, logged-in empty board, saved-trip card with SubID context, AI planner resume prefill, and mobile layout. Keyboard review reached the saved-trip form fields, consent checkbox, save button, resume/edit action, local handoff links, and delete action with visible focus and no horizontal overflow.

Regression review: Existing Phase 18 AI planner protections remain intact. The AI planner can prefill from a member-owned saved trip without weakening `run_baf_ai`, editor-only draft saving, or AI handoff permissions. Homepage and fallback navigation now point Saved Trips to `/saved-trips/` instead of the old homepage/hotel placeholder path. Travelpayouts and partner-owned search/booking remain outside WordPress.

Validation performed:
- PHP syntax checks for changed plugin service, REST, frontend, template, post-type, and theme PHP files.
- `node --check` for `saved-trips.js` and `ai-planner.js`.
- `git diff --check`.
- File-size review for changed PHP, JS, CSS, and theme files.
- WP-CLI saved-trip REST smoke with 25 assertions.
- Playwright Chromium runtime screenshots and keyboard review after Browser fallback.
- Cleanup check for temporary users and active saved-trip records.

Bugs found:
- The first runtime script used an overly broad text locator for "Trip intent"; the script was corrected and rerun.
- The second runtime script used the wrong viewport API; the script was corrected and rerun.
- Login initially passed through the WordPress profile page and captured an unrelated admin page JavaScript error; the login flow was redirected directly to `/saved-trips/` and rerun.
- The first screenshot pass exposed low-contrast public header text on the new pale saved-trips route and a mobile admin-bar/header overlap for logged-in users.
- Code review found that the item update route advertised editable methods while the service treated updates as full replacements, which could make partial update clients fail or clear omitted fields.

Bugs fixed:
- Added a route-specific body class and scoped header styles for `/saved-trips/`.
- Added admin-bar-aware header offsets on the saved-trips route.
- Added the AI planner resume link to saved-trip cards.
- Added owner-scoped merge behavior for saved-trip updates so partial update payloads preserve omitted fields while still requiring local-storage consent.

Bugs deferred:
- WordPress personal-data exporter/eraser hooks are deferred to `ONE-122` / P19.3, where export/delete behavior is the explicit child issue. P19.1 provides immediate user deletion through the saved-trip board and REST delete endpoint.

Documentation updated:
- `.plan/phased-implementation.md`
- `.plan/architecture-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/regression-watchlist.md`
- `.plan/validation-baseline.md`
- `.plan/known-issues.md`
- `.plan/phase-19-saved-trips-review.md`

Research consulted:
- WordPress REST API Handbook: Adding Custom Endpoints — https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- WordPress Plugin Security Handbook: Nonces — https://developer.wordpress.org/plugins/security/nonces/
- WordPress Plugin Security Handbook: Securing Input — https://developer.wordpress.org/plugins/security/securing-input/
- WordPress Plugin Security Handbook: Securing Output — https://developer.wordpress.org/plugins/security/securing-output/
- WordPress Plugin Handbook: Adding the Personal Data Exporter to Your Plugin — https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-exporter-to-your-plugin/
- WordPress Plugin Handbook: Adding the Personal Data Eraser to Your Plugin — https://developer.wordpress.org/plugins/privacy/adding-the-personal-data-eraser-to-your-plugin/
- WordPress Plugin Handbook: Privacy Related Options, Hooks and Capabilities — https://developer.wordpress.org/plugins/privacy/privacy-related-options-hooks-and-capabilities/
- Travelpayouts Help Center: ID and SubID — https://support.travelpayouts.com/hc/en-us/articles/203955653-ID-and-SubID-Affiliate-marker-and-additional-marker

Decision: P19.1 is ready for PR review. Keep Phase 19 `In Progress` until the remaining child issues pass their own gates and final release readiness is complete.
