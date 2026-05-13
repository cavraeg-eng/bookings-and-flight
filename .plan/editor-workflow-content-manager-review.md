# P17.5 Editor Workflow And Content Manager Field Pipeline Review

Linear issue: `ONE-110`

Status: `In Review`

## Scope

This review covers the editor path for Phase 17 destination, route, travel deal, and monetized module content:

- `destination`, `route`, and `travel_deal` post editor workflows.
- P17 `baf_*` meta persistence and template rendering.
- Travelpayouts placement usage on public guide templates.
- Legacy `bookings-and-flights-content-manager` field rendering, persistence, media portability, schema export/import, and admin notices.

## Editor Workflow

Editors maintain Phase 17 page families through normal WordPress content records:

1. Create or edit a `Destination`, `Route`, or `Travel Deal`.
2. Write the guide body in the WordPress editor.
3. Fill the structured "Destination Guide Modules", "Route Guide Modules", or "Travel Deal Modules" meta box.
4. Assign travel taxonomy terms for region, style, vertical, and season where applicable.
5. Preview or publish through the normal WordPress approval flow.
6. Manage Travelpayouts scripts, links, and iframes only through approved widget registry placements. Editor content should reference handoff intent and source notes, not raw provider scripts.

The core plugin now exposes structured meta boxes for the P17 module fields used by the public templates. This removes the need for editors to type `baf_*` custom-field keys by hand for destination, route, and deal modules.

## Field Pipeline State

Implemented in core:

- Structured editor fields for `destination`, `route`, and `travel_deal` guide modules.
- Nonce and `edit_post` capability checks on save.
- Sanitization for text, textarea, airport-code, and non-negative number fields.
- Empty values are removed from post meta to avoid stale generated-field clutter.
- No public REST exposure changed; existing registered P17 meta remains private from REST.

Current legacy content-manager state:

- The plugin exists locally at `plugins/bookings-and-flights-content-manager/`, but it is currently untracked in Git.
- It is page-template oriented and currently ships only a `page-home.php` field file.
- It has useful seams for field definitions, rendering, persistence, sanitization, revisions, global options, media upload UI, export/import, and admin notices.
- It does not currently declare P17 CPT export/import definitions for `destination`, `route`, or `travel_deal`.
- `class-meta-boxes.php` and `class-export-import.php` exceed the 600-line project guideline, so they should be split before the plugin is adopted into tracked Phase 17+ code.

## Raw Script Boundary

P17 templates do not require editors to paste raw Travelpayouts scripts into post content. Monetized modules use approved placement keys and server-rendered wrappers:

- Flight search and White Label handoffs route through the governed Travelpayouts placement registry.
- Hotel handoffs route through approved `hotels_partner_search`, `hotels_map_handoff`, and `hotels_listing_handoff` placements where applicable.
- Deal, route, destination, and taxonomy templates provide editorial context and local shell links, while live search, booking, payment, changes, support, and supplier inventory remain provider-owned.

## Deferred Content Manager Issues

These are not blockers for the P17.5 core editor workflow, but they should be addressed before using the legacy content-manager plugin as the primary tracked field system for large CPT content expansion:

- Split `class-export-import.php` into export builder, import validator, media resolver, CPT synchronizer, and notice presenter classes.
- Split `class-meta-boxes.php` into field renderer, section renderer, persistence handler, and media/repeater field helpers.
- Add declared CPT export/import definitions for `destination`, `route`, and `travel_deal` before relying on schema portability.
- Add import smoke tests for malformed JSON, newer schema rejection, repeated import idempotence, failed media sideload warning behavior, and skipped undeclared CPT meta.
- Decide whether the legacy plugin should become tracked code, be replaced by the core editor meta-box flow, or be reduced to page-template/global-option management.

## Validation Plan

- PHP syntax checks for changed PHP files.
- File-size check for changed files.
- Core plugin activation check.
- Editor meta-box registration smoke for destination, route, and travel deal post types.
- Save-handler smoke with nonce, capability, sanitization, and empty-value cleanup.
- Source scan confirming P17 templates do not require raw Travelpayouts scripts in editor content.
- Admin/editor browser smoke check with desktop and mobile-width screenshots.
- Keyboard navigation review through the editor fields and publish controls.

## Local Validation Result

Local validation passed on 2026-05-13.

- PHP syntax passed for changed core plugin files.
- Changed source files remain below the 600-line guideline.
- Core plugin is active in the local WordPress site.
- Editor meta-box registration smoke passed for `destination`, `route`, and `travel_deal`.
- Save-handler smoke passed for valid nonce saves, invalid nonce rejection, airport-code sanitization, budget sanitization, textarea sanitization, empty-value cleanup, and rendered raw-script boundary copy.
- Source review confirmed P17 templates use approved Travelpayouts placement wrappers and shell handoff links instead of requiring raw provider scripts in editor content.
- Playwright Chromium captured admin editor screenshots for destination, route, and deal edit screens plus a mobile destination editor view.
- Keyboard review confirmed focus on `baf_core_editor_meta[baf_destination]`, Tab to `baf_core_editor_meta[baf_destination_airport]`, and focus on the WordPress `Save draft` control.

Bugs found and fixed:

- CLI admin-context smoke initially exposed that `add_meta_box()` could be unavailable if the hook is triggered outside loaded admin includes. The registration method now safely returns when the admin function is unavailable.
- Browser keyboard review found Destination fields were grouped under a stale "Route context" heading and tabbed to travel style before airport code. Destination fields now use the "Guide context" section so the visible and keyboard order is destination, airport code, travel style.

Evidence:

- `/tmp/one110-editor-report.json`
- `/tmp/one110-editor-destination-desktop.png`
- `/tmp/one110-editor-route-desktop.png`
- `/tmp/one110-editor-deal-desktop.png`
- `/tmp/one110-editor-destination-mobile.png`
- `/tmp/one110-keyboard-destination-field.png`
- `/tmp/one110-keyboard-save-control.png`

## Research Consulted

- WordPress Plugin Handbook: Custom Meta Boxes.
- WordPress Plugin Security Handbook: Securing Input.
- WordPress Plugin Security Handbook: Nonces.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Developer Resources: `admin_url()`.
- WordPress Developer Resources: `admin_notices`.
