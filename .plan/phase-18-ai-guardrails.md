# Phase 18.5 AI Planner Consent and Live-Provider Guardrails

Linear issue: `ONE-117`

Branch: `codex/one-117-ai-guardrails`

Status: `In Review`

## Scope

P18.5 hardens the existing AI planner guardrails before later saved-trip, alert, or release-review work. The slice keeps demo mode reliable, keeps live provider requests behind configuration plus consent gates, and keeps malformed request values from creating noisy PHP warnings before validation.

## Current Behavior

- `BAF\Core\AI\Provider_Factory::live_readiness()` returns a single live-mode readiness contract for frontend messages and backend provider selection.
- Live mode is considered not ready when the provider is missing, unsupported by the current PHP adapter, missing an API key, or missing saved External AI consent.
- The planner template and localized script data use the same readiness message as backend provider selection.
- The planner blocks live-mode form submission before `fetch()` when saved readiness is false, so prompt data does not hit the AI REST endpoint for known-misconfigured live states.
- A live request that is otherwise ready still requires the per-request `external_ai_consent` checkbox before submission.
- The backend still enforces the same boundaries through `Provider_Factory::make()` and `AI_Itinerary_Service::generate()`, so client-side checks are UX protection rather than the only security gate.
- REST request sanitizers now guard non-scalar values before calling text, textarea, integer, and boolean sanitizers.
- AI service and OpenAI prompt-payload normalization safely ignore or coerce malformed non-scalar values without exposing raw prompts, API keys, provider payloads, or secrets in errors.

## Boundaries

- This slice does not add new AI providers, saved-trip publication, alert sending, provider searches, Travelpayouts calls, booking, payment, live inventory storage, or public saved-trip records.
- Unsupported configured providers continue to fail safely until a documented provider adapter is implemented.
- Demo mode remains local and does not require live credentials.
- Future live-provider additions must update `Provider_Factory::supports_live_provider()`, the readiness contract, and the validation baseline before use.

## Validation Evidence

Local validation passed on 2026-05-13:

- PHP syntax checks passed for the changed provider factory, OpenAI adapter, itinerary service, itinerary REST controller, shared request parameters, frontend route, and planner template.
- `node --check plugins/bookings-flights-core/assets/js/ai-planner.js` passed.
- Focused WP-CLI REST/service smoke confirmed demo success, unauthenticated permission failure, unauthorized draft-save failure, missing per-request consent, missing provider key, unsupported provider, missing saved External AI consent, malformed non-scalar request rejection, provider error safety, and session error safety.
- Follow-up malformed-option smoke confirmed array-valued AI provider settings fail safely as `503:baf_ai_not_configured` without `Array to string conversion` warnings.
- Provider and session error checks confirmed fake API keys and raw prompt sentinels were not exposed in returned errors.
- Browser validation attempted the Codex in-app Browser first, then used Playwright Chromium because the Browser Node REPL invocation timed out after 15 seconds.
- Playwright authenticated to `/trip-planner/`, confirmed page identity, nonblank content, no framework overlay, keyboard focus through prompt, destination, save option, and submit controls, demo `POST /wp-json/baf/v1/ai/itinerary` returning `201`, and rendered demo output marked as not sent to a live provider.
- Playwright verified live missing-key, missing saved External AI consent, and missing per-request consent states display actionable messages and do not send an itinerary REST request.
- Playwright confirmed the fake live API key was not rendered in page source, there were no app-owned console errors or failed requests, and the mobile width had no horizontal overflow.

Evidence files:

- `/tmp/one117-guardrails-runtime-report.json`
- `/tmp/one117-demo-empty.png`
- `/tmp/one117-demo-success.png`
- `/tmp/one117-live-missing-key.png`
- `/tmp/one117-live-missing-consent.png`
- `/tmp/one117-live-request-consent.png`
- `/tmp/one117-mobile-live-guardrail.png`

Research consulted:

- WordPress Plugin Security Handbook.
- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Settings API documentation.
- WordPress Nonces documentation.
- WordPress Roles and Capabilities documentation.
- AI SDK Core: Generating Structured Data.
- OpenAI Chat Completions API reference.

Decision: PR candidate is ready for Codex review. Keep P18.5 and Phase 18 overall `In Progress` until the PR is reviewed, any actionable feedback is patched, the PR is merged, Linear is synced, and the feature branch is cleaned up.
