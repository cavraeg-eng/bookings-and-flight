# Known Issues

Track current bugs, severity, affected area, workaround, and planned fix phase.

## Platform API Port Documentation Mismatch

Severity: Low

Affected area: `platform/` local development and WordPress/platform integration.

Current issue: `platform/README.md` says `npm run dev` starts the search API on `:4000`, while `platform/INTEGRATIONS.md`, `platform/services/search-api/src/config/env.ts`, `platform/services/search-api/.env.example`, `platform/apps/web/.env.example`, and `platform/apps/web/next.config.mjs` use `http://localhost:4050`. Phase 2 updated the affiliate bridge activation/default/admin copy to `http://localhost:4050`; the platform README remains stale.

Workaround: Before running platform integration work, inspect the actual `platform/services/search-api` server configuration and update remaining platform docs consistently.

Planned fix phase: First platform or settings integration task.

## WordPress CLI Emits PHP 8.5 Deprecation Warning

Severity: Low unless command execution fails.

Affected area: Validation using WP-CLI.

Current issue: `wp --info` emits a PHP 8.5 deprecation warning from a bundled dependency.

Workaround: Treat as tooling noise if commands succeed. If commands fail, use the Local by Flywheel PHP version or a supported WP-CLI/PHP pairing.

Planned fix phase: Validation environment setup, if it blocks work.

## Travelpayouts WordPress Plugin Production Follow-Ups

Severity: Medium until production credentials, hotel fallback embeds, and real White Label domains are validated.

Affected area: Travelpayouts-controlled booking/search backend, widget placement, and production frontend pages.

Current issue: Phase 11 locally confirmed the Travelpayouts-controlled backend boundary. The official Travelpayouts WordPress plugin remains usable for the locally validated flight widget path only. P11.1 installed the official `travelpayouts` plugin version `1.2.2` from WordPress.org and validated local activate, deactivate, and reactivate behavior on WordPress `6.9.4`. P11.2 identified the account setup fields and cleared the missing/configured setup smoke check with temporary credentials: saved tokens render blank in admin HTML, account options are sanitized before storage, and the Gutenberg token route returns only non-secret configured state. A P11.6 follow-up fixed account persistence when the settings UI omits the API token/Partner ID/project/White Label fields or submits a blank project placeholder; saved account values are now preserved unless a replacement value is supplied, and saved Project IDs are available as a fallback select option when traffic-source lookup is unavailable. The account page now exposes the hotels White Label field even though official hotel widget/table shortcodes still render empty because the plugin's legacy HotelLook availability gate returns false. P11.3 and P11.6 validated a temporary flight widget test page on desktop and mobile: `[tp_popular_routes_widget]` rendered the Travelpayouts `weedle/widget.js` source with marker/SubID behavior and no source-level token, secret, checkout, payment, or WordPress-owned booking flow. Minimal Bookings and Flights wrappers now exist for Widget-type White Label setup through `baf_travelpayouts_settings.white_label_widget_id` and `[baf_travelpayouts_white_label]`, and for current hotel brand widget setup through `baf_travelpayouts_settings.hotel_widget_script_url` and `[baf_travelpayouts_hotel_widget]`. The real White Label Widget ID is configured on the published Flights page, and the real Trip.com partner embed/handoff is configured on the published Hotels page. Remaining production watch item: the Trip.com iframe may be blocked or blank inside some browsers with content-blocking extensions, so the Hotels page keeps a visible sponsored handoff button to the Trip.com partner search surface.

P16.1 update: the Hotels page now wraps the existing `hotels_partner_search` placement with a local hotel-intent module and clearer provider-owned live-search/booking boundaries. The provider iframe/handoff behavior remains the same underlying Travelpayouts/Trip.com path, so the content-blocking fallback watch item still applies.

P16.2 update: city hotel guide pages now reuse the approved `hotels_partner_search` placement with `surface="hotels"` and `channel="destination_single"` context. The first runtime pass caught an unavailable-placement state when the guide used a new `destination` surface, so future hotel guide work should keep new surfaces out of provider rendering until the placement registry explicitly approves them.

P16.3 update: hotel map/listing companion placements now exist as governed `handoff_link` placements using the existing approved Trip.com partner URL when a safe direct handoff URL is available. They intentionally do not claim WordPress-owned live maps, live hotel tables, or local filtering. If Travelpayouts later provides dedicated hotel map/listing/table embed code, add it through the placement registry and re-run the mobile iframe clipping and SubID checks.

P16.4 update: no new app-owned hotel handoff or disclosure blocker remains after the local gate. Provider-owned Chromium WebGL performance warnings can still appear around embedded partner widgets; keep treating them as watchlist-only when the page renders, disclosures are visible, handoffs work, and app-owned console/request checks pass.

P16.5 update: the mobile/source review found and fixed app-owned touch-target regressions in the static header and hotel guide cards. No new app-owned hotel mobile layout, source-exposure, disclosure, console, request, clipping, or keyboard blocker remains after the final Playwright pass. Continue treating provider-owned Chromium WebGL warnings as watchlist-only when app-owned checks pass.

P16.6 update: the final Phase 16 gate found and fixed one app-owned SEO bug where transient hotel-intent query URLs canonicalized to `/hotels/` but were still indexable, and Codex PR review found the initial fix missed WordPress subdirectory installs. Hotel-intent query URLs now render `noindex, follow`, including subdirectory paths such as `/blog/hotels/`; no remaining app-owned hotel discovery, source-exposure, disclosure, runtime, keyboard, or SEO blocker remains after the final Playwright/source pass.

Security watch item: GitHub push protection identified an embedded Airtable personal access token in the official plugin package during PR publication. The staged local package now redacts the hard-coded token and disables the Airtable distribution script unless a token is supplied outside Git through `TRAVELPAYOUTS_AIRTABLE_TOKEN`. Do not commit provider, analytics, or distribution tokens into the repository.

Compatibility watch item: Direct PHP syntax scanning of the official plugin passed with no syntax errors, but PHP `8.5.4` emitted deprecation warnings from bundled Redux/PHP-DI/Parsedown/Opis/Travelpayouts classes. A P11.6 browser pass found the PHP-DI `ReflectionProperty::setAccessible()` deprecation printing into the Flights page when local PHP displayed deprecations; the bundled PHP-DI resolver now skips `setAccessible()` on PHP 8.1+ and uses an explicit nullable type for the injected class name.

Workaround: Treat official-plugin usage as production-cleared only for the exact flight widget path validated in Phase 11. Use Travelpayouts dashboard-generated Trip.com or other Hotels & Accommodation brand widget/link code and White Label Widget/Page code inside secured, capability-gated WordPress wrappers. Keep planned `/search/flights` and `/search/hotels` routes limited to shell/configuration, placement, consent/disclosure, SubID, missing-configuration, or handoff metadata. Do not build a custom replacement flight/hotel inventory backend or promote `platform/` search adapters as the canonical WordPress backend without a new architecture decision.

Planned fix phase: Phase 13 Travelpayouts widget registry and the first real Travelpayouts account/domain setup pass.

## Oversized Static Theme Stylesheets

Severity: Resolved for currently staged static theme files.

Affected area: `themes/bookings-and-flights-static/assets/css/`.

Current issue: Phase 9 release review previously found oversized generated static theme stylesheets. P11.6 split `header-footer.css` into `header.css`, `mobile-nav.css`, and `footer.css` before publishing the static theme, and the currently staged theme CSS files are under the 600-line guideline.

Workaround: Keep future theme additions in focused stylesheets and re-check file sizes before publishing frontend changes.

Planned fix phase: Ongoing frontend maintenance.

## Phase 12 Runtime Template And Navigation Follow-Ups

Severity: Medium until the public visual implementation phases land.

Affected area: `themes/bookings-and-flights-static/`, primary menu, fallback menu, page templates, seed content, and Travelpayouts widget placement surfaces.

Current issue: Phase 12 completed the information architecture, design-system, CSS ownership, widget-frame, page-wireframe, browser screenshot, and keyboard navigation gates, but the runtime public theme still has deferred implementation gaps. P14.1 fixed the old primary/fallback navigation behavior for header output, replaced the `Get Started` contact CTA with a trip-planner entry point, and replaced the generic homepage hero/features/about/testimonials/CTA layout with a local-media flight/hotel search shell. P14.2 added dedicated Flights and Hotels page templates that render approved Travelpayouts placement-registry outputs inside the Bookings and Flights shell, preserve sanitized homepage intent details, keep visible partner handoff links, and avoid custom inventory APIs. P14.3 added below-hero homepage discovery modules for route starters, explore-anywhere prompts, flexible-month planning, and hotel city prompts using static editorial inspiration because no published destination, route, or travel deal posts exist yet. P14.4 added honest homepage price-alert and AI-planner entries that route to existing handoff/placeholder surfaces without alert capture, prompt submission, auto-booking, or auto-publishing. P14.5 added homepage trust/disclosure cards, local Flights/Hotels support notes, footer affiliate disclosure plus legal/support/destination links, corrected the footer Terms link to `/terms-and-conditions/`, and updated legal copy for Travelpayouts/partner handoff boundaries. P14.6 fixed reduced-motion mobile-menu delays, header/footer touch-target sizing, and official Travelpayouts plugin asset scope on public pages while preserving the approved Flights White Label and Hotels Trip.com widget surfaces. P14.7 completed the final Phase 14 gate and cleared the homepage/search-surface baseline for Phase 15. Explore, Deals, Trip Planner, and Saved Trips are homepage anchors until their planned standalone pages are implemented; local destination, route, and deal seed content is absent for later screenshot-backed visual implementation; and provider-owned Travelpayouts console warnings remain a runtime watch item. The Phase 12 runtime validation pass captured Home, Flights, and Hotels at desktop, tablet, and mobile sizes, verified keyboard behavior, fixed the Travelpayouts widget focus-outline gap, and added a visible focus bridge for the keyboard-reachable Trip.com iframe while preserving the sponsored handoff link as the next keyboard stop.

Workaround: Treat `.plan/phase-12-completion-gate.md`, `.plan/phase-12-page-level-wireframes.md`, `.plan/phase-12-widget-frame-layout-rules.md`, and `.plan/phase-12-design-system-component-inventory.md` as the source of truth for later public UI implementation. Do not judge Phase 14+ visual work against the current generic runtime layout.

Planned fix phase: Continue with Phase 15 flights experience, Phase 16 hotels experience, Phase 17 SEO content surfaces, Phase 18 AI planner, and Phase 19 saved trips/alerts/release readiness.

## Phase 15 Flights Experience Closeout Watch

Severity: Resolved for Phase 15 after the P15.7 local review gate; keep provider runtime noise on the later-phase watchlist.

Affected area: `/flights/`, route/origin landing pages, Travelpayouts discovery widgets, White Label handoff, and price-alert signup.

Current issue: P15.1 added the dedicated Flights landing page intent module and preserved Travelpayouts-controlled White Label handoff behavior. P15.2 added route archive/detail templates, an origin-filtered route archive mode, related route cards, and the approved `flights_white_label_search` route surface through the Travelpayouts placement registry. P15.3 added approved low-price calendar, popular-routes, and route-map Travelpayouts discovery widgets on Flights and route pages, with responsive frames, SubIDs, missing-code states, and visible provider-owned handoff/disclosure boundaries. P15.4 added visible White Label continuity bands on Flights and route detail pages so the WordPress-owned shell, route back to WordPress, affiliate disclosure, and Travelpayouts-owned result/booking boundary stay clear before the provider module. P15.5 added local alert intent signup backed by private `travel_alert` posts, nonce/consent/email checks, a short per-client/email/route throttle, minimized route/watch metadata, and provider-owned live-fare/booking disclaimers. P15.6 added route/Flights SEO metadata, origin-filter canonical behavior, flight-query noindex handling, uppercase-first route-code normalization, source scans, REST/content smoke, and desktop/mobile keyboard-backed runtime screenshots. P15.7 completed the final review gate with HTTP/source smoke, public route REST smoke, missing-alert-nonce permission failure, real runtime screenshots, keyboard route navigation, and cleanup of temporary route content. Direct-only, nearby-airport, flexible-date, airline, baggage, and time filters are clearly marked as provider-controlled because the current local WordPress shell does not apply those filters to Travelpayouts results.

Workaround: Use `/flights/` for the current Travelpayouts handoff path and `/routes/` plus route detail pages for indexable editorial route context. Keep unsupported filters as provider-controlled labels and treat alert signup as local intent capture rather than live fare monitoring.

Planned fix phase: No remaining Phase 15 implementation issue after `ONE-99`; continue monitoring provider-owned Travelpayouts runtime warnings during later phases.

## Phase 16 Hotels and Stays Experience Closeout Watch

Severity: Resolved for Phase 16 after the P16.6 local review gate; keep provider runtime and content-blocking behavior on the later-phase watchlist.

Affected area: `/hotels/`, hotel-intent query URLs, destination hotel guide archive/detail pages, hotel companion handoffs, Travelpayouts/Trip.com placement shells, disclosures, and hotel SEO metadata.

Current issue: P16.1 through P16.6 completed the app-owned hotel experience using WordPress-owned editorial intent, city guide content, governed partner placements, visible affiliate disclosure, responsive layouts, keyboard-reachable handoffs, SubID-aware map/listing companion links, and transient hotel-query noindex behavior. No app-owned Phase 16 blocker remains after the final gate.

Workaround: Use `/hotels/` and destination hotel guide pages for the current WordPress-owned hotel discovery shell. Keep live rates, room inventory, maps, filters, booking, payment, changes, support, and supplier availability with Travelpayouts, Trip.com, or the partner provider.

Planned fix phase: No remaining Phase 16 implementation issue after `ONE-105`; continue monitoring provider-owned Trip.com iframe/content-blocking and Chromium WebGL warnings during later phases.

## Phase 17 Content Engine Watch

Severity: Resolved for Phase 17 after the P17.7 local review gate; keep content richness, provider runtime warnings, and CSS file size on the later-phase watchlist.

Affected area: `/destinations/`, `/routes/`, destination single pages, route single pages, taxonomy labels, destination planning modules, route planning modules, related destination links, related route links, matching destination guide links, and monetized/provider handoffs embedded on content guides.

Current issue: P17.1 broadened destination pages from hotel-only city guides into editable SEO destination guides. P17.2 deepened route archive/single pages with editable travel-time, airport, flexible-date, destination hotel/activity, related-route, and matching destination-guide modules. P17.3 added editable travel deal archive/single templates with seasonal, weekend, travel-style, activity, source-note, partner-card, approved White Label handoff, related deal, matching route, and matching destination modules. P17.4 added public taxonomy archives for travel region/style/vertical/season terms with bounded public content queries, taxonomy-aware SEO metadata, internal-linking rule cards, and shell-surface handoff links. P17.5 adds structured core editor meta boxes for destination, route, and travel deal guide-module fields so editors do not need to type raw `baf_*` custom-field keys for P17 pages. P17.6 completed the local accessibility, responsive, SEO, and disclosure pass with real Playwright screenshots, keyboard review, source checks, and fixes for strict touch targets, provider iframe labelling, and availability-claim wording. P17.7 completed the final local Phase 17 review gate with CPT/meta registration smoke, final runtime screenshots, keyboard review, transient query SEO checks, private/non-public taxonomy leak checks, source/security checks, and fixture cleanup. P18.1 now starts the real `/trip-planner/` route and prompt-to-trip brief flow. No app-owned Phase 17 blocker remains after the local gate. The current templates still depend on published destination/route/deal content and editor-entered metadata for rich production coverage.

Workaround: Use `/destinations/`, destination singles, `/routes/`, origin-filtered route archives, route singles, `/travel-deals/`, travel deal singles, and public travel taxonomy archives for the current editable SEO guide shell. Keep live provider availability, fares, search results, booking, payment, changes, support, and supplier inventory with Travelpayouts, Trip.com, or the partner provider.

Planned fix phase: Phase 17 is closed. Continue Phase 18 AI planner work after `ONE-113` is reviewed, merged, and synced.

Deferred editor/content-manager follow-up: The local `bookings-and-flights-content-manager` plugin remains untracked and page-template oriented. Its `class-meta-boxes.php` and `class-export-import.php` files exceed the 600-line guideline, and it has no declared P17 CPT export/import definitions for `destination`, `route`, or `travel_deal`. Before using that plugin as the tracked field system for large CPT expansion, split the rendering, persistence, media portability, schema export/import, and notice seams documented in `.plan/editor-workflow-content-manager-review.md`.

Deferred CSS follow-up: `plugins/bookings-flights-core/assets/css/frontend.css` is 590 lines after the narrow P17.6 touch-target fix. It remains under the 600-line guideline, but future substantial core frontend CSS work should split the file first.

## Phase 18 AI Planner Watch

Severity: Resolved for Phase 18 after the P18.7 local review gate; keep live-provider, handoff-consumption, and release-readiness behavior on the Phase 19 watchlist.

Affected area: `/trip-planner/`, AI itinerary generation, editable `trip_plan` drafts, `travelpayouts_opportunity_v1` recommendations, local AI handoff intents, AI settings, consent settings, and AI session privacy.

Current issue: P18.1 through P18.7 completed the app-owned AI planner baseline using a WordPress-owned planner route, protected REST generation, demo/live provider abstraction, explicit consent gates, structured schema validation, editor-only draft save, approval-oriented local handoff intent storage, secret/raw-prompt privacy checks, real runtime screenshots, keyboard review, and final documentation gate. No app-owned Phase 18 blocker remains after the final local gate.

Workaround: Use `/trip-planner/` for editable AI-assisted trip briefs only. Keep live availability, prices, booking, payment, provider links, provider search execution, changes, and support with Travelpayouts or partner providers. Treat `baf_ai_handoff_intents` as local approved metadata until a later phase explicitly consumes it with new consent, capability, validation, and documentation gates.

Planned fix phase: Phase 18 is closed after PR #55 merged. Continue with Phase 19 saved trips, alerts, analytics, and release readiness.

## Phase 19 Retention Follow-Ups

Severity: Low after the P19.2 local gate; keep privacy export/erase, analytics, and release-readiness surfaces on the Phase 19 watchlist.

Affected area: `/saved-trips/`, `baf/v1/saved-trips`, private `trip_plan` saved-trip records, planner resume prefill, `[baf_flight_alert_signup]`, private `travel_alert` records, `baf_process_travel_alerts`, and local Travelpayouts placement/SubID context.

Current issue: P19.1 added the WordPress-owned saved-trip board, member save/list/resume/update/delete REST workflow, anonymous sign-in handoff, minimized saved-trip metadata, and browser-verified desktop/mobile UI. P19.2 finalized local alert intent limits, dedupe, queued confirmation/manage email, and signed alert deletion. No app-owned P19.1 or P19.2 blocker remains after the local gates. WordPress personal-data exporter/eraser hooks are still deferred to P19.3, where user data export/delete behavior is the explicit child issue.

Workaround: Use `/saved-trips/` for immediate member-owned saved-trip deletion and the alert email delete link to open the nonce-confirmed alert deletion form. Keep export/erase integration tracked for P19.3 before launch-readiness completion.

Planned fix phase: Continue with P19.3 resume/export/erase behavior after `ONE-121` is reviewed, merged, and synced.
