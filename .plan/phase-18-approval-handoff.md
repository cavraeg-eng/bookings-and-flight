# Phase 18.4 Approval-Oriented AI Handoff

Linear issue: `ONE-116`

Branch: `codex/one-116-approval-handoff`

Status: `Completed`

## Scope

P18.4 prepares approved local handoff intents from validated `travelpayouts_opportunity_v1` recommendations on saved `trip_plan` drafts. It keeps the action editorial and local until a later phase explicitly turns those intents into placement records, saved-trip CTAs, alert CTAs, or provider-owned links.

## Current Behavior

- `POST /wp-json/baf/v1/ai/handoff` is registered under `baf/v1` by `BAF\Core\REST\AI_Handoff_Controller`.
- The route requires `edit_baf_content` and a valid `X-WP-Nonce` for `wp_rest` before request handling.
- The service also checks `edit_baf_content`, `edit_post` on the source `trip_plan`, global provider-request consent, and per-request provider handoff consent.
- Only saved `trip_plan` drafts with a validated `baf_itinerary_json` payload can be used.
- Only `travelpayouts` opportunities with `status=not_executed`, `requires_approval=true`, and `disclosure_required=true` can be prepared.
- Approved local intent records are stored in `baf_ai_handoff_intents` as `ai_handoff_intent_v1` payloads.
- Stored intents include provider, vertical, recommendation type, label, placement context, destination, route, suggested SubID, confidence, approval metadata, and blocked actions.
- Stored intents explicitly keep `provider_action=not_executed`, `provider_action_executed=false`, and `external_request_sent=false`.
- The planner renders editor-only handoff controls after saved opportunities. The control requires an approved draft, provider-request consent, and an explicit approval checkbox.
- The handoff UI can prepare `placement_card`, `placement_draft`, `saved_trip_cta`, or `alert_cta` local intents.

## Boundaries

- This slice does not call Travelpayouts, create live links, embed provider widgets, publish posts, book trips, pay providers, send alerts, or create public saved-trip records.
- Raw prompt text is not stored in the handoff intent.
- Provider links and live inventory claims are not accepted as handoff inputs.
- Future phases must still implement the actual placement/card/saved-trip/alert consumption flow with separate permission, consent, nonce, review, and browser validation gates.

## Validation Evidence

Local validation passed on 2026-05-13:

- PHP syntax checks passed for the new handoff service, new handoff REST controller, REST manager, post type registrar, and planner page localization.
- `node --check plugins/bookings-flights-core/assets/js/ai-planner.js` passed.
- `git diff --check` passed.
- File-size checks confirmed the new service, new controller, and changed planner CSS/JS remain below the 600-line guideline.
- Focused WP-CLI REST/service smoke confirmed missing nonce, limited-user permission failure, missing approval, missing per-request provider consent, missing global provider consent, and the happy path.
- Follow-up malformed REST argument smoke returned `400:rest_invalid_param` without app-owned PHP warnings after hardening controller sanitizers.
- The happy path returned `201`, stored one local intent with `provider_action=not_executed`, kept `external_request_sent=false`, and did not store the raw prompt sentinel or a provider link.
- `bookings-flights-core` deactivate/reactivate/is-active passed with known local WP-CLI PHP 8.5 deprecation noise only.
- `curl -I http://bookings-and-flights.local/trip-planner/` returned `200 OK`.
- Runtime browser validation attempted the Codex in-app Browser first, then used Playwright Chromium because no active Codex browser pane was available.
- Playwright authenticated to `/trip-planner/`, verified keyboard focus through planner controls, submitted a saved AI draft, prepared the first local handoff intent, observed `POST /wp-json/baf/v1/ai/itinerary` and `POST /wp-json/baf/v1/ai/handoff` both return `201`, confirmed the rendered `Provider action: not_executed` state, captured desktop/mobile screenshots, and deleted the temporary post/user after validation.
- App-owned console errors and `baf/v1` failed responses were absent in the browser report. Provider/admin aborted requests remained non-blocking because the app-owned API checks and visible handoff flow passed.

Evidence files:

- `/tmp/one116-handoff-runtime-report.json`
- `/tmp/one116-handoff-empty.png`
- `/tmp/one116-handoff-result.png`
- `/tmp/one116-handoff-prepared.png`
- `/tmp/one116-handoff-mobile.png`

Research consulted:

- WordPress Plugin Security Handbook.
- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Nonces documentation.
- WordPress Roles and Capabilities documentation.
- WordPress Post Meta registration documentation.
- AI SDK Core: Generating Structured Data.
- Travelpayouts Help Center: Affiliate programs tools.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.

Decision: Completed through PR #52, reviewed by Codex with no major issues, merged into `main` at `04124dbd5bc32d9fb86e3cfb4b44ea29847e45f2`, with the feature branch deleted/pruned. Keep Phase 18 overall `In Progress` until later saved-trip/alert/final review issues complete.
