# Phase 18.1 AI Planner Brief Flow

Status: `In Review`

Linear issue: `ONE-113`

## Scope

P18.1 implements the first user-facing AI planner slice:

- `/trip-planner/` WordPress-owned frontend route.
- Prompt-to-trip brief form.
- Demo-mode itinerary generation through the existing protected AI itinerary REST endpoint.
- Live-mode per-request external AI consent gate before provider selection.
- Sanitized structured brief output plus day-by-day cards and recommendation-only handoff opportunities.
- Public planner CTAs routed away from the old homepage placeholder anchor.

Out of scope for this slice:

- Saved trip board.
- Public draft saving from the planner page.
- Price-alert capture from generated plans.
- Approval workflow that converts AI opportunities into Travelpayouts placement drafts.
- Live provider search execution, booking, payment, publishing, or inventory storage.

## Current Contracts

- Route: `/trip-planner/`
- Query var: `baf_ai_planner`
- Rewrite version option: `baf_ai_planner_rewrite_version`
- Template: `plugins/bookings-flights-core/templates/ai-planner-page.php`
- Assets: `baf-ai-planner` CSS/JS
- REST endpoint: `POST /wp-json/baf/v1/ai/itinerary`
- Required REST capability: `run_baf_ai`
- Public page request behavior: sends `save=false`
- Live provider request gate: saved external AI consent plus per-request `external_ai_consent`

## Review Notes

Security and privacy:

- Planner output is rendered with created DOM nodes and `textContent`.
- Raw prompt text is used only as request input and is not returned as rendered trip-brief output.
- Live AI mode is blocked server-side unless the planner request includes explicit `external_ai_consent`.
- Existing AI session logging stores request/output hashes and sanitized summaries/errors, not raw prompts or raw provider payloads.
- Affiliate opportunities remain `not_executed` and `requires_approval`.

UI and accessibility:

- The first focusable planner control is the prompt textarea.
- The status region uses `role="status"` and `aria-live="polite"`.
- The result surface has an empty state and success state.
- Desktop and mobile screenshots showed no horizontal overflow.
- The consent checkbox input is a 44px control inside the explanatory consent row.

## Validation

Local validation passed on 2026-05-13:

- PHP syntax checks for changed PHP/template files.
- `node --check` for `plugins/bookings-flights-core/assets/js/ai-planner.js`.
- WP-CLI REST registration, unauthenticated permission, demo happy path, and live missing per-request consent checks.
- HTTP `200` smoke for `/trip-planner/`.
- Playwright Chromium screenshots and keyboard review after the Codex in-app Browser path had no active pane.
- Source/runtime checks for no app-owned console failures, no relevant failed responses, no raw prompt echo, and no exposed secrets.
- First browser pass found the checkbox visual target below threshold and a false source-secret match on WordPress core's `luminous-dusk` preset name; the checkbox was enlarged and the source scan was narrowed before the final report returned `findingCount=0`.
- Codex PR review found impossible date strings and unsupported live-provider readiness; follow-up fixes use `checkdate()` and `Provider_Factory::supports_live_provider()`.
- Post-review runtime validation reused Playwright Chromium after the Codex in-app Browser pane remained unavailable; the authenticated planner flow generated a four-day demo brief, keyboard focus reached the form controls through submit, and screenshots were saved at `/tmp/one113-after-codex-authenticated-result.png` and `/tmp/one113-after-codex-keyboard-auth.png`.
- Temporary admin user cleanup.
- `git diff --check`.

Evidence:

- `/tmp/one113-ai-planner-browser-report.json`
- `/tmp/one113-ai-planner-empty-desktop.png`
- `/tmp/one113-ai-planner-keyboard.png`
- `/tmp/one113-ai-planner-success-desktop.png`
- `/tmp/one113-ai-planner-success-mobile.png`

Research consulted:

- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Plugin Security Handbook.
- WordPress Common APIs Handbook: Nonces.
- WordPress Settings API Handbook.
- AI SDK Core: Generating Structured Data.
- AI SDK Core: Tools and Tool Calling.
- Travelpayouts Help Center: Getting started with widgets.
- Travelpayouts Help Center: Affiliate programs tools.
