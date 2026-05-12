# Phase 12 Completion Gate

Status: `Completed`

Linear issue: `ONE-79`

Date: 2026-05-12

## Research Consulted

- WordPress Theme Handbook: [Template Hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/) for page, archive, single, front-page, and fallback template ownership.
- WordPress Theme Handbook: [Templates](https://developer.wordpress.org/themes/templates/templates/) for template organization and the role of reusable template parts.
- WordPress Theme Handbook: [Theme Structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/) for standard theme folders, parts, patterns, and asset organization.
- WordPress Theme Handbook: [Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/) for scoped style/script loading through WordPress enqueue APIs.
- Travelpayouts Help Center: [Getting started with widgets](https://support.travelpayouts.com/hc/en-us/articles/360031977872-Getting-started-with-widgets) for adaptive and fixed-size widget behavior, SubID tracking, and provider-owned widget setup.
- Travelpayouts Help Center: [Setting up a White Label with Widget type](https://support.travelpayouts.com/hc/en-us/articles/26857907357458-Setting-up-a-White-Label-with-Widget-type) for embedded White Label search/results behavior and branded configuration constraints.

## Inputs Reviewed

- `.plan/phased-implementation.md`.
- `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- `.plan/phase-12-design-system-component-inventory.md`.
- `.plan/phase-12-css-split-theme-architecture.md`.
- `.plan/phase-12-widget-frame-layout-rules.md`.
- `.plan/phase-12-page-level-wireframes.md`.
- `.plan/architecture-baseline.md`.
- `.plan/regression-watchlist.md`.
- `.plan/known-issues.md`.
- Static theme templates and assets in `themes/bookings-and-flights-static/`.
- Core frontend shortcode/widget ownership in `plugins/bookings-flights-core/`.

## Scope Review

Phase 12 objective:

```text
Rework the public WordPress frontend architecture so it feels like a modern travel search product rather than a static affiliate brochure.
```

Phase 12 is complete as an information architecture, competitive design-system, CSS architecture, widget-frame, page-wireframe, runtime screenshot, and keyboard-navigation gate. Runtime public UI implementation gaps remain mapped to Phase 13 and later visual/product phases.

| Phase item | Result | Evidence |
| --- | --- | --- |
| Sitemap and navigation ownership | Completed | `.plan/phase-12-sitemap-navigation-page-ownership.md`, PR #9 |
| Design tokens, media strategy, component inventory | Completed | `.plan/phase-12-design-system-component-inventory.md`, PR #10 |
| CSS split and theme architecture prep | Completed | `.plan/phase-12-css-split-theme-architecture.md`, PR #11 |
| Widget frame, loading, responsive rules | Completed | `.plan/phase-12-widget-frame-layout-rules.md`, PR #12 |
| Page-level wireframes | Completed | `.plan/phase-12-page-level-wireframes.md`, PR #13 |
| Final review and documentation gate | Completed | PR #14 established the gate and this follow-up executed browser screenshot plus keyboard navigation validation for `ONE-79`. |

## Acceptance Criteria Review

| Acceptance criterion | Phase 12 gate result | Follow-up |
| --- | --- | --- |
| First viewport clearly supports flights and hotels | Passed for design documentation. Home runtime is still generic and must be rebuilt. | Phase 14 homepage competitive rebuild. |
| Real travel media replaces gradient-only hero treatment | Passed for design direction and wireframe requirement. Runtime media assets are not implemented yet. | Phase 14 and later template work. |
| Search controls are dense, accessible, mobile-first, and compatible with Travelpayouts widgets | Passed for component inventory and wireframe rules. | Phase 13 registry and Phase 14/15/16 templates. |
| Affiliate handoff and disclosures are visible | Passed for all Phase 12 docs and widget-frame rules. | Phase 13 reusable disclosure/placement behavior. |
| Header and White Label continuity inputs are defined once and reused by later phases | Passed for IA, architecture baseline, and widget-frame docs. | Phase 13 registry and any Page-type White Label setup. |
| Content manager refactor needs are documented before template scope expands | Passed at planning level. Field-pipeline implementation is deferred. | Future content manager maintenance pass. |

## Static Template Review

Current tracked static theme templates:

| File | Phase 12 review result |
| --- | --- |
| `page-home.php` | Still a generic hero/features/about/testimonials/CTA layout. Rebuild belongs to Phase 14. |
| `index.php` | Still renders generic page content and currently covers Flights and Hotels pages until dedicated templates exist. |
| `header.php` | Brand shell exists, but primary nav/menu fallback and `Get Started` CTA still need travel-product reconciliation. |
| `footer.php` | Footer has production-safe fallback copy and legal/trust links, but final footer nav should reflect Phase 12 IA. |
| `page-about.php`, `page-contact.php`, `page-services.php`, `page-legal.php` | Trust/support/legal surfaces need a later content pass; Services remains a legacy page to retire or repurpose. |
| `404.php` | No Phase 12 blocker found. |

No static template file changed in Phase 12.6. Runtime template implementation remains intentionally deferred.

## CSS File-Size Review

No tracked public or core frontend CSS source file exceeds the 600-line project guideline after Phase 12.3.

| File | Lines | Result |
| --- | ---: | --- |
| `themes/bookings-and-flights-static/assets/css/base.css` | 256 | Pass |
| `themes/bookings-and-flights-static/assets/css/components.css` | 85 | Pass |
| `themes/bookings-and-flights-static/assets/css/fonts.css` | 17 | Pass |
| `themes/bookings-and-flights-static/assets/css/footer.css` | 279 | Pass |
| `themes/bookings-and-flights-static/assets/css/header.css` | 464 | Pass |
| `themes/bookings-and-flights-static/assets/css/home.css` | 373 | Pass |
| `themes/bookings-and-flights-static/assets/css/mobile-nav.css` | 70 | Pass |
| `themes/bookings-and-flights-static/assets/css/tokens.css` | 388 | Pass |
| `plugins/bookings-flights-core/assets/css/admin.css` | 252 | Pass |
| `plugins/bookings-flights-core/assets/css/frontend.css` | 239 | Pass |

## Responsive Wireframe Review

Responsive behavior is documented for:

- Home, flights, hotels, explore, destination detail, route detail, deals, AI planner, saved trips, about/legal, and admin widget placement surfaces.
- Mobile, tablet, and desktop layout rules.
- Widget frame reservations and no-horizontal-overflow rules.
- Disclosure placement before monetized actions.
- Screenshot sizes and seed-content prerequisites for later UI implementation.

Result: Passed for Phase 12 documentation and current runtime surfaces. Runtime screenshots were captured for Home, Flights, and Hotels at desktop `1440x1000`, tablet `1024x900`, and mobile `390x844`. Runtime template rebuild work remains deferred to the mapped implementation phases.

## Runtime Browser And Keyboard Review

Runtime URL: `http://bookings-and-flights.local`

Browser path: The Codex in-app Browser captured an initial live pass and then became unavailable after a tab lifecycle error (`No active Codex browser pane available`). The final public validation used Playwright against the same local WordPress runtime with clean, anonymous contexts.

| Surface | Viewports | Result |
| --- | --- | --- |
| Home | `1440x1000`, `1024x900`, `390x844` | Passed load, blank-page, framework-overlay, console, and no-horizontal-overflow checks. Runtime copy and layout remain generic and are deferred to Phase 14. |
| Flights | `1440x1000`, `1024x900`, `390x844` | Passed load, blank-page, framework-overlay, and no-horizontal-overflow checks. Travelpayouts White Label console warnings come from provider scripts and are tracked as provider-owned noise. |
| Hotels | `1440x1000`, `1024x900`, `390x844` | Passed load, blank-page, framework-overlay, console, no-horizontal-overflow, and handoff-link checks. |
| Explore, Deals, Trip Planner, Saved Trips | `1440x1000` route check | Returned WordPress `404` pages, matching the deferred implementation state recorded by P12.1 and P12.5. |

Keyboard review result:

- Desktop header focus order reaches skip link, logo, primary nav, header CTA, and theme toggle with visible focus states.
- Mobile header focus order reaches skip link, logo, theme toggle, and menu toggle with visible focus states.
- Mobile menu opens by keyboard, sets `aria-hidden="false"` and `aria-expanded="true"`, moves focus into menu links, traps focus through the menu, and closes with `Escape`.
- The Travelpayouts flight widget mount now receives a visible WordPress-owned focus outline when provider code focuses `#tpwl-search` or `#tpwl-tickets`.
- The Trip.com iframe remains keyboard reachable, and the WordPress wrapper adds a visible `is-keyboard-focused` outline while iframe focus is active; the visible sponsored `Open hotel search` link remains the next keyboard-accessible handoff path.

## Documentation Completeness Review

| Document | Result |
| --- | --- |
| `.plan/phased-implementation.md` | Current Phase 12 child results are summarized. |
| `.plan/architecture-baseline.md` | Phase 12 docs and frontend/widget contracts are linked. |
| `.plan/validation-baseline.md` | P12.1 through P12.6 validation commands/results are recorded. |
| `.plan/regression-watchlist.md` | IA, design system, widget-frame, and wireframe watch items are recorded. |
| `.plan/known-issues.md` | Runtime visual/template follow-ups are recorded as deferred Phase 13+ work. |
| `.plan/phase-review-log.md` | P12.1 through P12.6 review records are present. |

## Bugs And Deferred Work

These are not blockers for the Phase 12 design/documentation gate, but they must stay visible for later implementation:

- Primary menu and fallback menu still reflect older boilerplate navigation.
- Header and mobile CTA still say `Get Started` and route to `/contact/`.
- Home runtime remains generic and gradient-heavy.
- Flights and Hotels still use `index.php` rather than dedicated search templates.
- Explore, Deals, Trip Planner, and Saved Trips pages are not yet implemented.
- Destination, route, and deal seed content is absent, so later screenshot validation needs seed data or documented empty states.
- `home.css` remains generic until the homepage rebuild.
- Mobile nav text sizing and hardcoded transition delays need review when the final primary nav is implemented.
- Phase 13 widget registry, reusable disclosure output, SubID builder, placement preview, and wrapper API remain unimplemented.
- Runtime browser screenshots and keyboard review are complete for the currently published Home, Flights, and Hotels surfaces.
- Travelpayouts White Label scripts emit provider-owned console warnings about React JSX source maps and a duplicate GraphQL fragment name. No WordPress-owned framework overlay or console error was observed.

## Phase 13 Start Checklist

Phase 13 can start without rediscovering Phase 12 decisions if it follows this checklist:

- Use the page ownership and sitemap in `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- Use component, media, accessibility, and disclosure rules from `.plan/phase-12-design-system-component-inventory.md`.
- Keep theme CSS additions inside the module ownership rules from `.plan/phase-12-css-split-theme-architecture.md`.
- Use widget dimensions, states, disclosure placement, performance rules, and registry metadata from `.plan/phase-12-widget-frame-layout-rules.md`.
- Use page/template/phase mapping and screenshot prerequisites from `.plan/phase-12-page-level-wireframes.md`.
- Preserve the Travelpayouts-controlled backend boundary from `.plan/architecture-baseline.md` and `.plan/decisions.md`.
- Do not introduce custom live flight or hotel inventory endpoints.
- Keep provider request consent, capability gates, nonces, sanitization, escaping, and secret masking in scope for all admin and public placement work.

## Validation Performed

- Static template review.
- Responsive wireframe review.
- CSS file-size review.
- Documentation review across Phase 12 outputs and shared baselines.
- Codex in-app Browser screenshot pass on the local runtime before fallback.
- Playwright screenshot pass on Home, Flights, and Hotels at `1440x1000`, `1024x900`, and `390x844`.
- Playwright keyboard navigation review for desktop header, mobile header, mobile menu, Flights widget focus, and Hotels handoff focus.
- PHP syntax checks for changed shortcode files.
- `bookings-flights-core` deactivate/reactivate smoke check.
- Source scan confirming the Hotels iframe focus bridge, Flights `tpwl-search`, and no sensitive token/authorization/payment/checkout/refund output beyond the public theme `tokens.css` design-token asset.
- Git diff whitespace check.

## Phase 12 Result

Phase 12 is complete. The information architecture, competitive design-system direction, CSS module ownership, widget-frame rules, page-level wireframes, runtime screenshot evidence, keyboard navigation review, deferred implementation risks, and Phase 13 prerequisites are documented and linked from shared project baselines.

Codex PR review on PR #14 found a P2 consistency issue on the attempted `Completed` status because browser screenshots and keyboard review had not yet been executed. This follow-up executed those checks, fixed the widget keyboard-focus bug found during review, patched the follow-up Codex PR feedback to keep the Trip.com iframe keyboard-reachable, and leaves later visual/template implementation to Phase 13 and subsequent mapped phases.
