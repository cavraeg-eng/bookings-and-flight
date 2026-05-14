# Phase 19.7 Performance, Accessibility, Responsive, and Browser Regression Review

Date: 2026-05-14

Linear issue: `ONE-126`

Status: In Review

Scope reviewed: P19.7 release-readiness browser regression pass across public travel surfaces, authenticated saved trips, and Bookings & Flights admin screens. Reviewed frontend responsive behavior, home/Flights/Hotels search-to-provider anchors, Travelpayouts White Label route hints, keyboard traversal, app-owned console/request failures, Settings API labels, admin/widget nonces, visible touch targets, source warnings, and provider-owned widget noise classification.

Acceptance criteria result: Passed locally for the PR candidate. The final runtime report at `/tmp/one126-runtime-review-report-final.json` returned `status=pass` with `findingCount=0`, `criticalFindingCount=0`, `screenshotCount=27`, and `keyboardReviewCount=8`.

Functional and responsive review: Passed locally. Playwright Chromium checked homepage, Flights, Hotels, destination archive/single, route archive/single, travel deal archive/single, travel-region taxonomy, Trip Planner, About, Privacy, Terms, authenticated Saved Trips, Integrations, Widget Placements, Reports, and Settings across desktop/mobile/narrow viewports where applicable. The pass confirmed nonblank content, no framework overlays, no horizontal overflow, no app-owned failed requests, no visible PHP warning/fatal/deprecated output, no duplicate IDs in app-owned scopes, no unlabeled app-owned controls, and no app-owned targets below 24px.

Keyboard review: Passed locally. Keyboard traversal covered Flights, Hotels, route single, Trip Planner, Saved Trips, Integrations, Widget Placements, and Settings. Focus moved through the skip link, primary navigation, form controls, widget handoff controls, saved-trip actions, and protected admin controls without traps or zero-sized focused controls.

Security and source review: Passed locally. The strict source token scan found no API keys, bearer tokens, authorization headers, OpenAI-style keys, Slack-style tokens, provider secrets, postback secrets, raw prompts, provider payloads, booking IDs, payment identifiers, confirmation numbers, or live inventory in app-owned rendered output. Provider-owned CORS/pixel/WebGL noise was recorded separately as provider noise, not an app-owned blocker.

Bugs found:

- Core cron scheduling ran too early during plugin bootstrap and could print a WordPress `_load_textdomain_just_in_time` notice before widget output on public Flights/Hotels surfaces.
- Home, Flights, and Hotels search submissions could leave users at the top/local intent area instead of moving them to the configured provider search section with the submitted intent visible.
- Settings API fields on core Settings and Integrations screens lacked `label_for` metadata for the WordPress-rendered field labels.
- Multiple Widget Placements delete forms reused the default `_wpnonce` input ID.
- The app-owned flight-alert consent checkbox and admin checkboxes were below the strict 24px target-size threshold.

Bugs fixed:

- `Cron_Manager::schedule_events()` now runs on `init` priority 20 instead of during plugin bootstrap.
- Search-surface forms now target `#flights-provider-search` or `#hotels-provider-search`, highlight submitted provider sections, normalize route codes, and pass flight route hints into the Travelpayouts White Label configuration.
- Settings fields now share `Settings_Manager::field_args()` so every registered field includes `label_for`.
- Widget placement delete forms now use per-placement nonce field names while keeping nonce verification capability-gated.
- Flight-alert and scoped Bookings & Flights admin checkbox/button hit areas now meet the strict target-size pass.

Bugs deferred:

- Provider-owned `tpembars.com`, `avsplow.com`, Travelpayouts, Trip.com, and Chromium WebGL runtime warnings remain watchlist-only because app-owned pages, controls, requests, source, and keyboard behavior passed. The active local affiliate bridge/content manager plugins remain runtime context but untracked source.

Validation performed:

- PHP syntax checks for changed PHP files.
- JavaScript syntax check for `themes/bookings-and-flights-static/assets/js/search-surface.js`.
- File-size review for changed PHP/CSS files.
- `bookings-flights-core` deactivate/reactivate/is-active with the LocalWP MySQL socket.
- Settings API registration smoke confirming no missing `label_for` values.
- Widget placement registry smoke with an administrator context.
- Public `/flights/` and `/hotels/` source scans for textdomain notices and PHP warning/fatal output.
- Codex in-app Browser attempted first and reported no active browser pane; Playwright Chromium was used for real runtime screenshots and keyboard review.
- Playwright Chromium report `/tmp/one126-runtime-review-report-final.json` with 27 screenshots under `/tmp/one126-screenshots-final`.
- Temporary browser-test posts, term, and admin user were deleted after the final runtime pass.
- `git diff --check`.

Documentation updated: `.plan/phased-implementation.md`, `.plan/architecture-baseline.md`, `.plan/validation-baseline.md`, `.plan/regression-watchlist.md`, `.plan/known-issues.md`, `.plan/phase-19-performance-accessibility-review.md`, `.plan/phase-review-log.md`.

Research consulted:

- WordPress Theme Handbook: Accessibility.
- WordPress Plugin Handbook: Enqueuing scripts and styles.
- WordPress Plugin Security Handbook: sanitizing, escaping, capabilities, and nonces.
- WordPress REST API Handbook: permission callbacks.
- WordPress Cron Handbook.
- W3C WCAG 2.2: keyboard, focus, and target-size guidance.
- Travelpayouts official Help Center: widgets, White Label, ID, and SubID guidance.

Decision: P19.7 is ready for PR review. Keep Phase 19 `In Progress` until the remaining final launch-readiness Phase 19 gate passes.
