# Phase 18.6 AI REST, Schema, Prompt, and Secret Review

Date: 2026-05-13

Linear issue: `ONE-118`

Status: Completed

## Scope

Reviewed the Phase 18 AI planner privacy and security boundary across:

- `POST /wp-json/baf/v1/ai/itinerary`
- `POST /wp-json/baf/v1/ai/handoff`
- structured itinerary and Travelpayouts opportunity validation
- OpenAI provider response parsing
- AI settings sanitization and dashboard status rendering
- AI session logging behavior
- frontend planner rendering, source output, live-consent blocking, and keyboard access

## Findings Fixed

- Malformed non-scalar itinerary output fields such as `duration_days`, `day`, activity titles, activity verticals, and opportunity destination fallbacks now normalize through scalar-safe helpers before sanitization.
- Malformed live AI provider response content now fails as malformed data instead of risking PHP conversion warnings.
- Array-valued saved AI mode/provider settings now sanitize to safe defaults instead of being cast directly.
- Dashboard AI status rendering now guards malformed option values before comparisons and never renders the stored API key.
- Service live-mode consent checks now tolerate malformed option state before selecting a provider.

## Security and Privacy Review

- REST routes keep explicit permission callbacks and capability gates.
- Live AI still requires saved External AI consent plus per-request external AI consent before prompt data can leave WordPress.
- Browser live-mode consent blocking prevented a REST request from being sent when the checkbox was missing.
- Rendered demo output did not echo the raw private prompt sentinel.
- A fake API key configured for live-mode readiness did not appear in page source.
- Provider/session error smokes did not expose fake API keys, raw prompt sentinels, bearer tokens, provider payloads, or stored secrets.
- AI session logging still stores normalized request metadata and sanitized output/error summaries, not raw provider payloads or secrets.

## Runtime Evidence

Playwright Chromium was used after the Codex in-app Browser path reported no active browser pane.

Runtime report:

- `/tmp/one118-ai-review-runtime-report.json`

Screenshots:

- `/tmp/one118-ai-review-empty.png`
- `/tmp/one118-ai-review-success.png`
- `/tmp/one118-ai-review-live-consent.png`
- `/tmp/one118-ai-review-mobile.png`

The final runtime pass confirmed page identity, nonblank planner content, no framework overlay, keyboard reachability through prompt/destination/submit, demo `POST /wp-json/baf/v1/ai/itinerary` returning `201`, no raw prompt echo, `Not sent to a live provider` output, live missing per-request consent with zero additional itinerary REST requests, no fake API key in source, mobile no horizontal overflow, no app-owned console errors, and no app-owned failed requests.

Non-app request noise observed during navigation:

- `wp-json/wp/v2/users/me?context=edit&_locale=user` aborted during logged-in admin/browser lifecycle.
- `travelpayouts/assets/admin-gutenberg-modal...js` aborted during third-party plugin admin asset lifecycle.

These were kept in the runtime report but classified outside app-owned BAF request health.

## Validation

- PHP syntax checks passed for changed PHP files.
- `node --check` passed for `plugins/bookings-flights-core/assets/js/ai-planner.js`.
- `git diff --check` passed.
- Changed source files stayed below the 600-line guideline.
- `bookings-flights-core` deactivate/reactivate/is-active check passed with known local WP-CLI PHP 8.5 deprecation noise only.
- Focused backend/privacy smoke passed for route permissions, malformed schema output, malformed AI settings, dashboard rendering without secret exposure, provider error safety, and AI session log privacy.
- Playwright Chromium runtime browser screenshots and keyboard navigation review passed.

## Deferred

- The final Phase 18 review and documentation gate remains `ONE-119`.
- Saved trips, alert follow-up, analytics, and release-readiness work remain Phase 19 scope.

## Merge

PR #54 was reviewed by Codex with no major issues, had no unresolved review threads, and merged into `main` on 2026-05-13 at `833f013cefafff5c6845774300de1a2386d1edf9`.

Research consulted:
- WordPress Plugin Security Handbook: https://developer.wordpress.org/plugins/security/
- WordPress REST API Handbook: Adding Custom Endpoints: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- WordPress Settings API Handbook: https://developer.wordpress.org/plugins/settings/settings-api/
- WordPress Nonces documentation: https://developer.wordpress.org/apis/security/nonces/
- WordPress Roles and Capabilities documentation: https://developer.wordpress.org/plugins/users/roles-and-capabilities/
- AI SDK Core: Generating Structured Data: https://ai-sdk.dev/docs/ai-sdk-core/generating-structured-data
- OpenAI Chat Completions API reference: https://developers.openai.com/api/reference/resources/chat
