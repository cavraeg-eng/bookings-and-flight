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
