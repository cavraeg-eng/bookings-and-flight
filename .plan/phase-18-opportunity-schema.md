# Phase 18.3 Travelpayouts Opportunity Schema

Linear issue: `ONE-115`

Branch: `codex/one-115-travelpayouts-opportunity-schema`

Status: `In Review`

## Scope

P18.3 defines the AI planner's Travelpayouts recommendation schema. The contract remains recommendation-only until a later approved handoff workflow consumes it.

## Current Behavior

- `BAF\Core\AI\Itinerary_Schema` adds `opportunity_schema = travelpayouts_opportunity_v1`.
- Each affiliate opportunity is normalized to `provider=travelpayouts`, `status=not_executed`, `requires_approval=true`, `disclosure_required=true`, and `approval_state=requires_editor_approval`.
- Opportunity fields include `vertical`, `recommendation_type`, `label`, `placement_context`, `destination`, `route`, sanitized `suggested_subid`, `confidence`, `limitations`, and fixed `blocked_actions`.
- Suggested SubIDs are lowercase, underscore-normalized, and limited before response or draft save.
- Schema validation rejects unsupported provider IDs, booked, paid, published, executed, reserved, ticketed, live-price, availability, confirmation, provider-link, and approval/disclosure-bypass claims.
- The demo provider and live provider prompt now describe the richer opportunity contract.
- Planner output renders the recommendation type, status, suggested SubID, confidence, and limitation copy without creating provider links or executing provider searches.

## Boundaries

- This slice does not create Travelpayouts placement drafts, provider links, partner widgets, saved-trip records, price alerts, bookings, payments, or published content.
- AI opportunities are structured handoff candidates only.
- Later Phase 18 work must still add explicit approval, nonce, capability, and consent checks before any local handoff artifact is prepared.

## Validation Evidence

Local validation passed on 2026-05-13:

- PHP syntax checks for changed AI/service PHP files.
- `node --check plugins/bookings-flights-core/assets/js/ai-planner.js`.
- Focused WP-CLI schema validation accepted a valid `travelpayouts_opportunity_v1` recommendation.
- Focused WP-CLI schema validation rejected executed status, provider price claim, approval bypass, disclosure bypass, and unsupported provider cases.
- Runtime browser validation attempted the Codex in-app Browser first through the existing workspace path, then used Playwright Chromium because no active browser pane was available.
- Playwright authenticated planner flow confirmed page identity, keyboard focus through prompt, save option, and submit, `POST /wp-json/baf/v1/ai/itinerary` returning `201`, rendered `flight_search` and `hotel_search` opportunities with safe suggested SubIDs and limitation copy, saved draft message, edit-link handoff, and no raw prompt echo.
- Saved draft meta included `baf_ai_opportunity_schema = travelpayouts_opportunity_v1`; the temporary draft/user were deleted.

Evidence files:

- `/tmp/one115-runtime-report.json`
- `/tmp/one115-planner-empty.png`
- `/tmp/one115-planner-result.png`
- `/tmp/one115-trip-plan-editor.png`

Research consulted:

- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Plugin Security Handbook.
- WordPress Common APIs Handbook: Sanitizing Data.
- WordPress Common APIs Handbook: Escaping Data.
- AI SDK Core: Generating Structured Data.
- Travelpayouts Help Center: Affiliate programs tools.
- Travelpayouts Help Center: ID and SubID affiliate marker guidance.
