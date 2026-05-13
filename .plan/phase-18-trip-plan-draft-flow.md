# Phase 18.2 Trip Plan Draft Flow

Linear issue: `ONE-114`

Branch: `codex/one-114-day-by-day-trip-plan-draft`

Status: `In Review`

## Scope

P18.2 extends the `/trip-planner/` flow so an editor can optionally save a generated itinerary as an editable WordPress `trip_plan` draft. The save path remains capability-gated and never publishes content automatically.

## Current Behavior

- The planner form includes an editor-only `Save as an editable WordPress Trip Plan draft` checkbox.
- The frontend sends `save=true` only when the user has `edit_baf_content` and checks the draft option.
- `AI_Itinerary_Service` rejects `save=true` requests from users without `edit_baf_content` before provider selection.
- Saved trip plans are created with `post_status=draft`, editable day-by-day post content, sanitized excerpt, author, date/origin/destination/style meta, source AI session UUID, and `baf_itinerary_json`.
- The REST response includes legacy `trip_plan_id` / `saved_status` fields and a `trip_plan` object containing the draft ID, draft status, and edit URL for users who can edit the post.
- Raw prompt text is not written into the editable post content or returned as visible output.

## Boundaries

- AI may generate and save a draft, but it cannot publish, book, pay, execute provider searches, create live inventory, or create Travelpayouts placement handoffs in this slice.
- Affiliate opportunities remain recommendation-only with `not_executed` status and `requires_approval=true`.
- Public unauthenticated users cannot run the endpoint or save drafts.

## Validation Evidence

Local validation passed on 2026-05-13:

- PHP syntax checks for changed PHP/template files.
- `node --check plugins/bookings-flights-core/assets/js/ai-planner.js`.
- REST admin save smoke created a draft `trip_plan`, confirmed draft status, editable day content, `baf_itinerary_json`, departure meta, and no raw prompt echo, then deleted the draft.
- REST limited-user smoke created a temporary user with `run_baf_ai` only and confirmed `save=true` returns `403:baf_ai_save_forbidden`.
- Runtime browser validation attempted the Codex in-app Browser first, then used Playwright Chromium because no active browser pane was available.
- The final Playwright authenticated planner flow confirmed the save checkbox is enabled for an editor, keyboard focus reaches prompt, save option, and submit, the AI itinerary REST request returns `201`, a saved draft result appears, the edit link opens `wp-admin/post.php?post=<id>&action=edit`, four day cards render, two recommendation-only opportunities render, no raw prompt is echoed, and the temporary draft/user were deleted.
- The final browser rerun found and fixed a frontend submit regression where a response-payload variable shadowed the request payload and prevented the REST request from firing.

Evidence files:

- `/tmp/one114-runtime-report.json`
- `/tmp/one114-final-runtime-report.json`
- `/tmp/one114-planner-save-empty.png`
- `/tmp/one114-planner-save-result.png`
- `/tmp/one114-trip-plan-editor.png`
- `/tmp/one114-final-planner-empty.png`
- `/tmp/one114-final-planner-result.png`
- `/tmp/one114-final-trip-plan-editor.png`

Research consulted:

- WordPress REST API Handbook: Adding Custom Endpoints.
- WordPress Plugin Security Handbook.
- WordPress Plugin Handbook: Registering Custom Post Types.
- WordPress Code Reference: `wp_insert_post()`.
- AI SDK Core: Generating Structured Data.
