# Phase 12.4 Widget Frame, Loading, and Responsive Layout Rules

Status: `Completed`

Linear issue: `ONE-77`

Date: 2026-05-12

## Research Consulted

- Travelpayouts Help Center: [Getting started with widgets](https://support.travelpayouts.com/hc/en-us/articles/360031977872-Getting-started-with-widgets) for widget types, customization, SubID usage, adaptive widgets, and fixed-size widget cautions.
- Travelpayouts Help Center: [Setting up a White Label with Widget type](https://support.travelpayouts.com/hc/en-us/articles/26857907357458-Setting-up-a-White-Label-with-Widget-type) for embedded White Label search/results behavior.
- Travelpayouts Help Center: [What is White Label Web by Travelpayouts?](https://support.travelpayouts.com/hc/en-us/articles/203955753-What-is-White-Label-Web-by-Travelpayouts) for Widget versus Page type ownership, partner booking handoff, and White Label constraints.
- WordPress Theme Handbook: [Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/) for loading scripts/styles only where they are needed.
- WordPress Plugin Security Handbook: [Securing Input](https://developer.wordpress.org/plugins/security/securing-input/) and [Securing Output](https://developer.wordpress.org/plugins/security/securing-output/) for safe wrapper output and admin-managed embed data.

## Inputs Reviewed

- Phase 11 Travelpayouts compatibility and wrapper evidence in `.plan/validation-baseline.md`.
- Phase 12.1 page ownership map in `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- Phase 12.2 design-system inventory in `.plan/phase-12-design-system-component-inventory.md`.
- Phase 12.3 CSS split rules in `.plan/phase-12-css-split-theme-architecture.md`.
- Existing White Label and Trip.com shortcode wrappers in `plugins/bookings-flights-core/includes/frontend/`.
- Existing core frontend widget/card styles in `plugins/bookings-flights-core/assets/css/frontend.css`.
- Phase 13 registry requirements in `.plan/travelpayouts-wordpress-booking-site-blueprint.md`.

## Widget Frame Principles

- WordPress owns the frame, disclosure, loading state, missing-configuration state, consent-disabled state, no-script state, and fallback handoff.
- Travelpayouts, Trip.com, White Label, or another approved partner owns live search/results, inventory, booking handoff, booking/payment, and supplier-reservation behavior.
- Every widget frame must reserve stable space before provider code runs. A widget is allowed to fail gracefully; it is not allowed to collapse the page, hide disclosure, or push critical content after load.
- Affiliate disclosure and external handoff links must live outside provider iframes/scripts so they remain visible when provider content is blocked, compressed, or inaccessible.
- Widgets should load only on pages where the approved placement appears. Do not load Travelpayouts scripts site-wide unless the official plugin requires it for that specific surface.
- Do not modify Travelpayouts JavaScript, iframe contents, SubID values, or partner redirect behavior in a way that could break tracking or violate provider contracts.

## Frame Anatomy

All monetized widget placements should use this outer structure, whether implemented by shortcode, block, template part, or the future Phase 13 registry:

```text
Widget section
  Heading or accessible label
  Affiliate disclosure
  Reserved widget frame
    Loading state
    Provider script/iframe mount point
    No-script fallback
    Missing/disabled/error state
  Sponsored handoff link or safe retry action
```

Rules:

- The heading can be visually compact, but the region must have an accessible name.
- The disclosure appears before or immediately adjacent to the first monetized action, never only below a long result frame.
- The reserved frame receives min-height or aspect-ratio before script load.
- The fallback handoff is visible outside iframes and remains reachable on mobile.
- Admin-only missing-configuration notices may show only to users with the required capability; public visitors should see either no widget or a neutral unavailable state.

## Dimensions and Constraints

These are Bookings and Flights frame reservations, not claims about provider internals. Phase 13 may tune exact values after testing real placements.

| Widget family | Desktop reserve | Tablet reserve | Mobile reserve | Width rule | Notes |
| --- | ---: | ---: | ---: | --- | --- |
| White Label search only | 260px min-height | 280px min-height | 320px min-height | `width: 100%` | Mounts `tpwl-search`. Keep disclosure above the mount point. |
| White Label search plus results | 640px min-height | 700px min-height | 780px min-height | `width: 100%` | Mounts `tpwl-search` and `tpwl-tickets`; use where users should remain inside WordPress shell. |
| White Label results only | 520px min-height | 620px min-height | 720px min-height | `width: 100%` | Results can expand. Keep top disclosure visible and add bottom handoff/help affordance if frame is long. |
| Flight search form widget | 260px min-height | 300px min-height | 360px min-height | Prefer adaptive widget width | Official plugin or provider widget owns fields. WordPress owns only shell and state. |
| Popular routes widget/table | 360px min-height | 420px min-height | 520px min-height | Adaptive when available; otherwise horizontal scroll inside frame | Do not use unbounded tables without mobile overflow containment. |
| Low-price calendar | 420px min-height | 500px min-height | 580px min-height | Prefer aspect-ratio plus min-height | Calendar cells must not overflow viewport; fixed widgets need scroll/fallback. |
| Route map widget | 420px min-height | 420px min-height | 360px min-height | `aspect-ratio: 16 / 9` with min-height | Maps may need a caption and fallback route handoff below. |
| Hotel search widget | 220px min-height | 240px min-height | 300px min-height | Adaptive or iframe width 100% | Trip.com direct partner iframe may be cropped only after browser validation and with visible handoff. |
| Hotel map or selections widget | 520px min-height | 580px min-height | 680px min-height | Adaptive when possible; contain overflow | Do not hide map/list controls behind fixed headers or card clipping. |
| Compact affiliate card/link widget | 160px min-height | 180px min-height | 220px min-height | Card/grid responsive | Works for partner links and simple cards, not live result surfaces. |

Mobile frame rules:

- A page-level widget frame must not cause horizontal page overflow.
- If a provider widget is fixed-width, put horizontal scrolling inside the widget frame rather than on the whole page.
- Keep disclosure, error, and fallback handoff outside the horizontal scroll area when possible.
- Mobile iframes need an explicit title and a minimum touch-safe fallback link outside the iframe.
- Avoid `height: 100vh` for provider frames because fixed site headers and mobile browser chrome can hide content.

## Required States

| State | Trigger | Public behavior | Admin/editor behavior | Phase 13 registry need |
| --- | --- | --- | --- | --- |
| Configured/loading | Placement approved and provider requests allowed | Reserved frame and concise loading state before provider content loads. | Optional placement ID/status badge. | `loading_copy`, `min_height`, `timeout_ms`. |
| Loaded | Provider script or iframe mounted successfully | Frame displays provider-owned content plus visible disclosure and fallback/handoff. | Optional diagnostics hidden from public output. | `loaded_selector` or wrapper-specific loaded check. |
| Missing configuration | Widget ID, script URL, or placement metadata missing | Hide public widget or show neutral unavailable state if the page would otherwise look broken. | Capability-gated notice with exact missing field. | `required_fields`, `admin_notice`. |
| Consent disabled | `baf_consent_settings.allow_provider_requests` is false | Do not emit third-party scripts/iframes for public visitors. | Capability-gated notice explaining provider request consent is disabled. | `consent_required`, `disabled_copy`. |
| No JavaScript | Browser has JavaScript disabled or script blocked | Show no-script copy and sponsored handoff link when a safe handoff URL exists. | Same plus diagnostic hint. | `noscript_copy`, `fallback_url`. |
| Provider blocked/unavailable | Content blockers, network failure, provider timeout, or iframe blocked | Show unavailable state and a handoff/retry affordance outside iframe. | Optional diagnostic message without secrets. | `timeout_ms`, `blocked_copy`, `fallback_url`. |
| Empty result | Provider returns no usable results or an official widget displays empty content | Preserve frame, explain no results, offer broader search/handoff. | Optional placement analytics flag. | `empty_copy`, `alternate_placement`. |
| Error | Sanitized wrapper error, invalid mode, rejected host, or unsafe embed | Public output fails closed without leaking raw embed code. | Capability-gated error code and remediation. | `status`, `error_code`, `allowlist_reason`. |

## Disclosure Placement

- Disclosure must be outside provider iframes/scripts and near the first monetized interaction.
- Search widgets: place disclosure above the widget frame or directly under the widget heading, before the search button or provider mount.
- Result widgets: place disclosure above the result frame. If the frame is long, repeat compact disclosure or show a sticky in-frame shell note outside the provider iframe.
- Map/calendar widgets: place disclosure above the frame and keep a visible text fallback below the frame.
- AI itinerary and saved-trip widgets: place disclosure on the card or board section before any monetized handoff.
- Admin preview: show whether a disclosure will render, which copy variant is selected, and whether the placement has a sponsored handoff.

Disclosure copy should be concise, readable at mobile widths, and avoid implying WordPress sells tickets, hotel rooms, or partner inventory.

## Performance Rules

- Load provider scripts only on pages where the widget renders.
- Prefer adaptive Travelpayouts widgets where available.
- Use lazy loading for below-the-fold iframes and partner handoff frames when provider behavior supports it.
- Reserve dimensions before lazy loading so the page does not shift.
- Keep `data-noptimize`, `data-cfasync`, or similar provider-preserving attributes when the wrapper has validated they are needed.
- Do not prefetch or preload third-party provider scripts globally.
- Use SubIDs that identify the placement, not the visitor. Never include names, emails, IP addresses, private trip details, prompts, or per-user IDs in SubIDs.
- Treat Travelpayouts reports as the source of truth for monetized conversion/revenue reporting.

## Phase 13 Registry Prerequisites

The future registry should persist safe placement metadata, not arbitrary public script blobs. Each registered placement needs:

- Placement key and human-readable name.
- Vertical: flights, hotels, cars, activities, packages, route, destination, deal, AI, saved trip.
- Widget family: White Label search, White Label results, flight form, popular routes, low-price calendar, route map, hotel search, hotel map, partner link/card.
- Render mode: official plugin, dashboard script, iframe, safe handoff link, or disabled.
- Approved host allowlist and sanitized script/iframe URL or official shortcode reference.
- Required options and missing-configuration copy.
- Consent requirement and disabled-state copy.
- Disclosure copy and placement rule.
- SubID template using the documented lowercase/underscore convention.
- Reserved desktop/tablet/mobile min-heights.
- Loading, no-script, unavailable, empty, and error copy.
- Fallback handoff URL and link label when allowed.
- Lazy-load policy and timeout threshold.
- Admin capability required for edits.
- Public surfaces where the placement may render.

Phase 13 should also expose one wrapper API so templates do not need to know raw Travelpayouts code, consent state, disclosure rules, SubID construction, fallback behavior, or layout dimensions.

## Validation Performed

- Reviewed official Travelpayouts widget and White Label documentation.
- Reviewed official WordPress asset and plugin security documentation.
- Reviewed existing White Label and Trip.com shortcode wrappers.
- Reviewed current `baf-` frontend CSS for White Label, hotel iframe, loading/fallback, no-script, and handoff behavior.
- Reviewed Phase 13 registry requirements and translated them into required placement metadata.
- Performed responsive wireframe and layout-shift risk review in documentation.

## P12.4 Result

Widget frame dimensions, responsive constraints, loading/missing/disabled/no-script/error states, disclosure placement, performance rules, and Phase 13 registry prerequisites are documented. Implementation can proceed to Phase 12.5 page-level wireframes and Phase 13 registry work without inventing layout behavior per widget.

Codex PR review on PR #12 found no major issues.
