# Regression Watchlist

Use this file to track fragile areas future phases must re-check after changes.

## Travelpayouts-Controlled Backend Boundary

Fragile area: Official Travelpayouts plugin, Travelpayouts widgets, White Label, SubIDs, and WordPress wrapper code.

Why risky: The new product direction depends on Travelpayouts controlling monetized search/results/booking handoff while WordPress controls design, content, consent, saved-trip intent, and editorial workflows. Future implementation could accidentally introduce a custom OTA backend, expose widget secrets, break Travelpayouts tracking, or overclaim live inventory.

What to check after future changes:

- Official Travelpayouts plugin compatibility status remains documented.
- Backend mode remains explicit: official-plugin-first only for locally validated surfaces, with Travelpayouts dashboard-generated fallback embeds for inactive or unvalidated plugin surfaces.
- Flight and hotel search flows use Travelpayouts plugin/widgets/White Label or approved Travelpayouts embeds; Phase 11 clears only the official flight widget path for plugin-first use.
- Hotel surfaces continue using Travelpayouts dashboard-generated Trip.com or other Hotels & Accommodation brand widgets/links; do not treat legacy HotelLook activation as the completion target.
- `/search/flights` and `/search/hotels` do not become custom live inventory APIs without a new decision.
- `platform/` search adapters are not treated as the canonical WordPress backend unless a new decision approves that boundary change.
- WordPress does not store canonical live flight or hotel inventory. Any future cached-offer feature remains explicitly non-canonical, consent-aware, TTL/provenance documented, and separate from Travelpayouts revenue/reporting source of truth.
- Travelpayouts Partner ID, Token, marker, and API credentials are never exposed in frontend HTML, JavaScript, public REST responses, logs, or admin notices.
- SubIDs use lowercase Latin letters, numbers, and underscores and do not contain private user/trip data.
- PHP deprecation or warning output from bundled Travelpayouts dependencies does not print into public pages.
- Trip.com hotel surfaces keep a visible sponsored handoff link even when the embedded iframe is blocked or blank in a browser.
- Affiliate disclosure appears on every monetized widget, link, card, route, destination, and AI planner surface.
- White Label Widget type remains preferred when the search/result page should keep the WordPress theme header completely intact.
- Page-type White Label header customization continues to mirror the Bookings and Flights home-site header: logo, brand name, favicon, header color/image, search-heading copy, and approved menu/footer links.
- Page-type White Label includes an obvious route back to the main WordPress site.
- Search/result transitions do not expose an unrelated redirected-page look that breaks brand continuity.
- White Label SEO and Booking.com fare limitations remain documented where relevant.
- Local analytics treat Travelpayouts reports as the source of truth for affiliate revenue and conversion data.

Related files/routes/tables/settings:

- `.plan/travelpayouts-wordpress-booking-site-blueprint.md`
- `.plan/architecture-baseline.md`
- `.plan/decisions.md`
- `plugins/bookings-flights-core/`
- `plugins/bookings-and-flights-affiliate-bridge/`
- `themes/bookings-and-flights-static/`
- `baf_travelpayouts_settings`
- `baf_tracking_settings`
- `baf_consent_settings`
- `baf_travelpayouts_widget_registry`

## Phase 12 Public Information Architecture

Fragile area: Public sitemap, primary navigation, page ownership, CPT archive/single routing, and SEO page family ownership.

Why risky: The current site still contains boilerplate navigation and empty template bodies while the product direction requires a travel-search navigation model. Future frontend work could accidentally keep legacy pages as primary navigation, bypass WordPress-owned SEO pages, or move search/result ownership into custom inventory code.

What to check after future changes:

- Primary navigation includes Flights, Hotels, Explore, Deals, Trip Planner, and Saved Trips, with trust/legal links moved to secondary or footer navigation.
- The theme fallback menu matches the documented Phase 12 navigation target when no WordPress menu is assigned.
- Home, Flights, Hotels, Explore, Deals, Trip Planner, and Saved Trips have documented WordPress page owners before visual implementation.
- Destination, route, and deal SEO pages remain WordPress-owned CPT archives/singles or documented WordPress pages.
- Travelpayouts widgets, White Label surfaces, and partner handoffs remain embedded or linked from WordPress-owned shells rather than becoming custom inventory endpoints.
- About, Contact/Support, and Legal pages remain reachable and non-empty, but do not displace primary product navigation.
- `/deals/` and the existing `travel-deals` CPT archive are reconciled before public launch without renaming CPT contracts silently.

Related files/routes/tables/settings:

- `.plan/phase-12-sitemap-navigation-page-ownership.md`
- `.plan/phased-implementation.md`
- `themes/bookings-and-flights-static/functions.php`
- `themes/bookings-and-flights-static/header.php`
- `themes/bookings-and-flights-static/index.php`
- `themes/bookings-and-flights-static/page-home.php`
- `plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php`

## Phase 12 Design System And Widget Frames

Fragile area: Design-token aliases, real-media strategy, public component patterns, disclosure treatment, accessibility states, and Travelpayouts widget frames.

Why risky: The current public theme still has generic marketing sections, gradient fallback hero treatment, broad radius tokens, and provider-owned widget wrappers. Future visual work could become a one-note palette, hide affiliate disclosures, overload generic cards, introduce mobile overlap, or clip/fail Travelpayouts widgets without a visible fallback.

What to check after future changes:

- New public UI follows `.plan/phase-12-design-system-component-inventory.md`.
- New shared primitives go in `components.css`; header/nav-only styling stays in `header.css`.
- Home, search, destination, route, hotel, AI, and saved-trip surfaces use real travel media where the user needs to inspect a place, route, or product state.
- Search controls remain dense, labeled, keyboard-operable, and at least 44px tall.
- New cards, panels, and widget frames use restrained radius and avoid nested-card layouts.
- Pages do not drift into a one-note navy/slate/tan palette.
- Affiliate disclosures remain visible near monetized widgets, links, cards, route modules, destination modules, and AI handoffs on mobile and desktop.
- Travelpayouts widget frames reserve dimensions, avoid layout shift, expose no-script/missing-config/consent-disabled states, and keep a sponsored handoff fallback visible.
- Travelpayouts White Label mount points keep a visible focus outline, and Trip.com provider iframe output does not become an unstyleable sequential keyboard stop before the visible hotel handoff link.
- Widget dimensions, disclosure placement, state behavior, and Phase 13 registry prerequisites follow `.plan/phase-12-widget-frame-layout-rules.md`.
- Page templates and public visual work follow the Phase 12.5 owner/template/phase mapping and browser screenshot plan in `.plan/phase-12-page-level-wireframes.md`.
- Phase 13 work starts from the Phase 12 completion gate checklist in `.plan/phase-12-completion-gate.md`.
- Text resizing, mobile breakpoints, focus states, and reduced-motion behavior remain usable without overlap or hidden controls.

Related files/routes/tables/settings:

- `.plan/phase-12-design-system-component-inventory.md`
- `.plan/phase-12-css-split-theme-architecture.md`
- `.plan/phase-12-widget-frame-layout-rules.md`
- `.plan/phase-12-page-level-wireframes.md`
- `.plan/phase-12-completion-gate.md`
- `themes/bookings-and-flights-static/assets/css/components.css`
- `themes/bookings-and-flights-static/assets/css/tokens.css`
- `themes/bookings-and-flights-static/assets/css/base.css`
- `themes/bookings-and-flights-static/assets/css/home.css`
	- `themes/bookings-and-flights-static/assets/css/header.css`
	- `plugins/bookings-flights-core/assets/css/frontend.css`
	- `plugins/bookings-flights-core/includes/frontend/class-travelpayouts-white-label-shortcode.php`
	- `plugins/bookings-flights-core/includes/frontend/class-travelpayouts-hotel-widget-shortcode.php`

## Phase 13 Widget Placement Admin

Fragile area: `baf-widget-placements` admin screen, registry service writes, raw embed storage, and future shortcode/block renderers.

Why risky: This screen is the first place administrators can manage approved Travelpayouts, White Label, Trip.com iframe, and handoff placement data. Future changes could accidentally expose raw embed URLs outside the capability-gated form, bypass nonce/capability checks, or split placement sanitization between controllers and renderers.

What to check after future changes:

- The Widget Placements submenu remains available to `manage_baf_affiliates` or `manage_baf_settings` users only.
- Save and delete actions continue using nonces, capability checks, `wp_unslash()`, sanitization, and registry service writes.
- Placement listing tables, notices, dashboard cards, public projections, REST responses, logs, and screenshots do not print private `embed.reference`, `embed.url`, API keys, tokens, credentials, private prompts, or customer data.
- Raw embed values appear only in the capability-gated edit form and are normalized by `Travelpayouts_Widget_Registry_Service` before storage.
- Missing or invalid admin-post nonces fail closed before placement writes, and failed nonce probes do not leave temporary placement records behind.
- Empty, missing-configuration, active, disabled, saved, and error states stay visible on desktop and mobile.
- Keyboard tab order reaches placement fields and write actions without focus traps or hidden focused controls.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/admin/class-admin-manager.php`
- `plugins/bookings-flights-core/includes/admin/class-widget-placements-page.php`
- `plugins/bookings-flights-core/includes/admin/class-widget-placement-form.php`
- `plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php`
- `plugins/bookings-flights-core/assets/css/admin.css`
- `baf_travelpayouts_widget_registry`

## Phase 13 Frontend Widget Wrapper

Fragile area: `[baf_travelpayouts_widget]`, `baf/travelpayouts-widget`, `Travelpayouts_Widget_Renderer`, frontend widget CSS, and registry rendering reads.

Why risky: This is the first public renderer that can turn approved registry records into provider scripts, iframes, and handoff links. Future changes could expose raw registry values, break provider consent, make disabled/missing states silent, or make the Trip.com iframe and handoff link unreachable by keyboard.

What to check after future changes:

- Shortcode and dynamic block attributes store only safe placement/context values, never raw embed snippets, private registry URLs, API tokens, admin notes, or credentials.
- Public output includes disclosure and safe handoff language outside provider-owned iframe/script content.
- Provider request consent blocks third-party output without leaking raw embed details.
- Missing, disabled, no-script, unavailable, and error states render escaped, useful copy.
- Frontend source and rendered widget output continue to avoid `api_token`, `api_key`, authorization, bearer, saved secret values, private admin notes, and raw registry embed fields.
- Runtime SubIDs remain centralized in `Travelpayouts_Widget_Subid_Service`, use the `{channel}_{surface}_{vertical}_{slug}_{placement}` convention, and contain only lowercase Latin letters, numbers, and underscores.
- Provider URL mutation preserves existing Travelpayouts `marker` partner IDs as `marker=partner.subid` instead of replacing tracking with a bare SubID.
- Loading states remain visible until provider content mounts or a safe unavailable fallback appears, and loading/unavailable/disabled/missing/consent states expose `role="status"` messaging where appropriate.
- Active placements cannot render provider output when requested from a surface outside their configured `public_surfaces` allowlist.
- Dashboard-script widgets do not show a false unavailable fallback when the provider iframe initializes after the first few seconds.
- White Label wrapper instances and the legacy White Label shortcode do not produce duplicate `tpwl-search` or `tpwl-tickets` IDs when rendered more than once or in mixed old/new shortcode order.
- Unavailable White Label conflict fallbacks are visible, and their empty placeholder containers are not left in sequential keyboard navigation.
- Keyboard navigation reaches the provider iframe when present and the visible handoff link immediately after it.
- Desktop and mobile screenshots show no horizontal overflow, clipped controls, or oversized provider header area returning above the intended widget frame.
- Phase 14 homepage/search templates consume the completed Phase 13 placement seam through approved placement keys, shortcode/block/PHP renderer calls, and public-safe metadata, never through copied raw embed snippets or private registry fields.
- Consent-disabled browser review continues to confirm no provider iframe, script, handoff URL, raw embed URL, or private note appears in public output.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-renderer.php`
- `plugins/bookings-flights-core/includes/frontend/class-travelpayouts-widget-shortcode.php`
- `plugins/bookings-flights-core/includes/frontend/class-frontend-manager.php`
- `plugins/bookings-flights-core/assets/js/travelpayouts-widget-block.js`
- `plugins/bookings-flights-core/assets/css/frontend.css`
- `plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-registry-service.php`
- `plugins/bookings-flights-core/includes/services/class-travelpayouts-widget-subid-service.php`
- `[baf_travelpayouts_widget]`
- `baf/travelpayouts-widget`
- `baf_travelpayouts_widget_registry`
- `baf_consent_settings`

## Affiliate Bridge Public REST Config And Postback

Fragile area: `GET /wp-json/baf/v1/config` and `GET|POST /wp-json/baf/v1/postback`

Why risky: It is intentionally public but must never expose supplier credentials, API tokens, postback secrets, or private settings.

What to check after future changes:

- Response contains only safe configuration.
- Enabled suppliers are derived without revealing field values.
- No secrets are rendered in REST responses, HTML, JavaScript, logs, or admin notices.
- `GET /wp-json/baf/v1/config` may remain public only while it returns supplier IDs, availability flags, and the configured search API URL without API tokens, postback secrets, authorization headers, or private settings.
- `GET|POST /wp-json/baf/v1/postback` may remain public only while it rejects requests without the shared secret and never returns the saved secret.

Related files/routes/tables/settings:

- `plugins/bookings-and-flights-affiliate-bridge/includes/class-rest.php`
- `plugins/bookings-and-flights-affiliate-bridge/includes/class-settings.php`
- `baf_supplier_credentials`
- `baf_search_api_url`
- `baf_postback_secret`

## Affiliate Bridge Status Route Permissions

Fragile area: `GET /wp-json/baf/v1/status`

Why risky: It checks backend connectivity and should remain protected.

What to check after future changes:

- Unauthenticated users cannot access protected operational status.
- Insufficient-capability users receive a safe failure response.
- Errors do not leak secrets or internal configuration.

Related files/routes/tables/settings:

- `plugins/bookings-and-flights-affiliate-bridge/includes/class-rest.php`
- `baf_search_api_url`

## Provider Credential Storage

Fragile area: Supplier/API credential settings.

Why risky: Travelpayouts and partner credentials must be stored server-side, sanitized, masked, and never exposed publicly.

What to check after future changes:

- Settings use registered sanitizers.
- Unknown suppliers and fields are stripped.
- Secret fields are masked in UI.
- Frontend and REST responses contain no raw credential values.

Related files/routes/tables/settings:

- `plugins/bookings-and-flights-affiliate-bridge/includes/class-settings.php`
- `plugins/bookings-and-flights-affiliate-bridge/views/settings.php`
- Future `baf_ai_settings`
- Future `baf_travelpayouts_settings`

## Core Admin Settings

Fragile area: `Bookings & Flights` admin pages, Settings API registration, and masked core provider settings.

Why risky: These pages gate future provider, AI, tracking, and consent workflows. Secrets must stay server-side while settings remain editable and sanitized.

What to check after future changes:

- Pages require `manage_baf_settings`.
- Settings POSTs use WordPress Settings API nonces and capability checks.
- `baf_settings`, `baf_travelpayouts_settings`, `baf_ai_settings`, `baf_consent_settings`, and `baf_tracking_settings` remain registered with sanitizers.
- Secret fields render as blank masked inputs and preserve saved values when left blank.
- Admin assets only load on Bookings and Flights admin screens.
- Missing configuration, configured, success, and warning states remain clear.
- Admin dashboard shortcuts, status badges, and job-status table remain keyboard-accessible and responsive.
- Job-status table retains a screen-reader caption and mobile `data-label` values.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/admin/class-admin-manager.php`
- `plugins/bookings-flights-core/includes/settings/class-settings-manager.php`
- `plugins/bookings-flights-core/assets/css/admin.css`
- `baf_settings`
- `baf_travelpayouts_settings`
- `baf_ai_settings`
- `baf_consent_settings`
- `baf_tracking_settings`

## Core UI Accessibility and Responsive Behavior

Fragile area: Admin dashboard/jobs UI, affiliate card shortcodes, and static theme navigation.

Why risky: These surfaces are shared across earlier workflows and future phases. Small markup or CSS changes can break keyboard navigation, mobile layouts, status visibility, or accessible names.

What to check after future changes:

- `[baf_travel_cards]` keeps an accessible heading association, visible affiliate disclosure, clear configuration notices, and a 44px-minimum call-to-action target.
- Frontend affiliate card styles remain scoped to `baf-` classes and do not leak provider secrets.
- Admin status cards expose clear ready/warning state text and retain escaped output.
- Background Jobs table remains usable on narrow screens and preserves table semantics for larger screens.
- Static theme mobile menu toggles retain `aria-controls`, `aria-expanded`, focus trapping, Escape close behavior, and readable mobile sizing.
- Theme color toggle keeps `aria-pressed` in sync with the persisted color mode.
- Homepage destination cards, search form, price-watch CTA, and AI-planner CTA resolve to implemented frontend routes and do not produce 404s.
- Public search links use `travel_destination` rather than the reserved `destination` query var to avoid conflicting with the registered `destination` CPT query var.
- About, Services, and Contact pages retain non-empty public main content.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/admin/class-admin-manager.php`
- `plugins/bookings-flights-core/includes/frontend/class-travel-cards-shortcode.php`
- `plugins/bookings-flights-core/assets/css/admin.css`
- `plugins/bookings-flights-core/assets/css/frontend.css`
- `themes/bookings-and-flights-static/header.php`
- `themes/bookings-and-flights-static/functions.php`
- `themes/bookings-and-flights-static/page-about.php`
- `themes/bookings-and-flights-static/page-contact.php`
- `themes/bookings-and-flights-static/page-home.php`
- `themes/bookings-and-flights-static/page-services.php`
- `themes/bookings-and-flights-static/assets/css/accessibility.css`

## Phase 14 Homepage Search Shell

Fragile area: `page-home.php`, `home.css`, product fallback navigation, and the homepage search forms.

Why risky: The homepage now owns the first public search entry point for Flights and Hotels while live availability and booking remain provider-owned. Future edits can accidentally reintroduce fake inventory claims, break the mobile menu tab trap, lose affiliate disclosure, or bypass the Phase 13 placement-key seam.

What to check after future changes:

- Header output still includes Home, Flights, Hotels, Explore, Deals, Trip Planner, and Saved Trips when the stored WordPress menu is stale.
- Homepage search forms keep `data-baf-placement-key="flights_white_label_search"` and `data-baf-placement-key="hotels_partner_search"` metadata and do not copy raw provider snippets into the theme.
- Search fields use non-conflicting query names such as `travel_destination`, not the reserved `destination` query var.
- Affiliate disclosure remains visible near the search shell at desktop, tablet, and mobile widths.
- Desktop and mobile keyboard order reaches the menu, CTA, search fields, search buttons, entry cards, and mobile menu links without escaping the open mobile menu.
- Frontend source does not expose API tokens, API keys, authorization headers, bearer tokens, postback secrets, raw registry embed URLs, or private registry notes.
- Local hero media stays local or WordPress-owned; do not hotlink third-party hero images from the public template.

Related files/routes/tables/settings:

- `themes/bookings-and-flights-static/page-home.php`
- `themes/bookings-and-flights-static/functions.php`
- `themes/bookings-and-flights-static/header.php`
- `themes/bookings-and-flights-static/assets/css/home.css`
- `themes/bookings-and-flights-static/assets/css/header.css`
- `themes/bookings-and-flights-static/assets/css/mobile-nav.css`
- `themes/bookings-and-flights-static/assets/images/home-hero-beach.jpg`
- `themes/bookings-and-flights-static/assets/css/content-pages.css`
- `themes/bookings-and-flights-static/assets/js/dark-mode.js`
- `themes/bookings-and-flights-static/assets/js/home.js`
- `[baf_travel_cards]`
- `[baf_affiliate_disclosure]`

## Existing Theme Security Hardening

Fragile area: Static WordPress theme hardening and rendering.

Why risky: Existing guidance says static/brochure sites must keep comments disabled, pingbacks disabled, WordPress version meta removed, and XML-RPC disabled.

What to check after future changes:

- Security hardening remains present.
- Menus and templates escape output.
- Frontend scripts are enqueued, not inline.
- Mobile navigation remains accessible.

Related files/routes/tables/settings:

- `themes/bookings-and-flights-static/functions.php`
- `themes/bookings-and-flights-static/header.php`
- `themes/bookings-and-flights-static/footer.php`

## Platform and WordPress Integration Boundary

Fragile area: `platform/` Next.js/Fastify app and WordPress plugin settings.

Why risky: The platform README currently says WordPress is optional, while the WordPress product setup treats WordPress as the system of record. API port documentation also appears inconsistent.

What to check after future changes:

- Source of truth for settings, providers, and content is documented.
- API base URLs are consistent.
- WordPress REST routes and platform API routes do not duplicate sensitive responsibilities.
- No secrets cross into browser bundles.

Related files/routes/tables/settings:

- `platform/README.md`
- `platform/INTEGRATIONS.md`
- `platform/package.json`
- `platform/services/search-api/`
- `baf_search_api_url`

## Release Readiness Gate

Fragile area: Full release validation across WordPress plugins, theme, REST routes, migrations, cron jobs, admin screens, shortcodes, and platform type contracts.

Why risky: Later fixes can pass targeted checks while regressing activation, protected route permissions, secret masking, custom table availability, cron scheduling, or platform integration assumptions.

What to check after future changes:

- PHP syntax remains clean for active Bookings and Flights plugins and the active static theme.
- Core plugin deactivate/activate remains safe; deactivation unschedules cron without deleting permanent data; activation recreates schedules and custom tables.
- All `baf/v1` REST routes remain registered with explicit `permission_callback` values.
- Protected routes/actions return safe permission failures for unauthenticated users.
- Public routes continue returning only safe public data and never expose API keys, raw prompts, postback secrets, private post meta, raw request identifiers, or conversion identifiers.
- Admin reports, settings, integrations, and jobs pages remain capability gated and escape output.
- Frontend shortcodes render safe empty/configured states and do not expose secrets.
- Platform shared/API/web typechecks pass when platform integration files are touched.

Related files/routes/tables/settings:

- `.plan/release-readiness-checklist.md`
- `plugins/bookings-flights-core/`
- `plugins/bookings-and-flights-affiliate-bridge/`
- `plugins/bookings-and-flights-content-manager/`
- `themes/bookings-and-flights-static/`
- `platform/`
- `/wp-json/baf/v1/config`
- `/wp-json/baf/v1/status`
- `/wp-json/baf/v1/postback`
- `/wp-json/baf/v1/destinations`
- `/wp-json/baf/v1/routes`
- `/wp-json/baf/v1/affiliate/click`
- `/wp-json/baf/v1/ai/itinerary`

## Core Plugin Bootstrap

Fragile area: `bookings-flights-core` activation, deactivation, and bootstrap.

Why risky: Future phases will attach CPTs, settings, REST routes, migrations, and cron hooks to the core plugin lifecycle.

What to check after future changes:

- Plugin activates without fatal errors.
- Autoloading resolves `BAF\Core` classes.
- Activation remains idempotent.
- Deactivation does not delete permanent data.
- Core CPTs, taxonomies, and capabilities remain registered on the intended hooks.
- No product workflows, custom `baf/v1` REST routes, cron hooks, or provider calls are added before their planned phases.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/bookings-flights-core.php`
- `plugins/bookings-flights-core/includes/class-activator.php`
- `plugins/bookings-flights-core/includes/class-deactivator.php`
- `plugins/bookings-flights-core/includes/class-plugin.php`
- `baf_core_version`

## Core REST Foundation

Fragile area: `GET /wp-json/baf/v1/destinations` and `GET /wp-json/baf/v1/routes`

Why risky: These are the first core REST routes and establish shared conventions for permissions, request validation, pagination, response fields, and public data exposure.

What to check after future changes:

- Routes remain registered under `baf/v1`.
- Every route has an explicit permission callback.
- Collection params are validated and sanitized.
- `per_page` remains capped at 50.
- Responses include pagination headers.
- Responses only include published public `destination` or `route` posts.
- Responses do not expose private post meta, drafts, private posts, provider credentials, alerts, trip plans, partner records, AI/session data, or settings secrets.
- REST controllers remain thin and route business/data access through services and repositories.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/rest/class-base-controller.php`
- `plugins/bookings-flights-core/includes/rest/class-permissions.php`
- `plugins/bookings-flights-core/includes/rest/class-request-parameters.php`
- `plugins/bookings-flights-core/includes/rest/class-rest-manager.php`
- `plugins/bookings-flights-core/includes/rest/class-travel-entity-controller.php`
- `plugins/bookings-flights-core/includes/services/class-travel-entity-service.php`
- `/wp-json/baf/v1/destinations`
- `/wp-json/baf/v1/routes`

## Core Data Model

Fragile area: Phase 1 CPTs, taxonomies, post meta, capabilities, and repository/service boundaries.

Why risky: Later REST, admin, AI, affiliate, and frontend phases depend on these keys and capability mappings remaining stable.

What to check after future changes:

- CPT keys remain `destination`, `route`, `travel_deal`, `trip_plan`, `travel_partner`, and `travel_alert`.
- Taxonomy keys remain `travel_region`, `travel_style`, `travel_vertical`, and `travel_season`.
- Dedicated capabilities remain administrator-only unless a future phase explicitly documents broader grants.
- Public CPTs remain appropriate for frontend discovery, while `trip_plan`, `travel_partner`, and `travel_alert` stay non-public until UI/API workflows require otherwise.
- Registered meta remains hidden from REST unless a future endpoint explicitly reviews privacy and permissions.
- Repository queries remain bounded and use WordPress APIs, not direct SQL.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php`
- `plugins/bookings-flights-core/includes/taxonomies/class-taxonomy-registrar.php`
- `plugins/bookings-flights-core/includes/capabilities/class-capability-manager.php`
- `plugins/bookings-flights-core/includes/repositories/class-repository-interface.php`
- `plugins/bookings-flights-core/includes/repositories/class-travel-entity-repository.php`
- `plugins/bookings-flights-core/includes/services/class-travel-entity-service.php`

## Custom Table Migrations

Fragile area: Future `bf_*` custom tables.

Why risky: Migrations can cause data loss or activation failures if not idempotent.

What to check after future changes:

- Uses `$wpdb->prefix`.
- Uses site charset/collation.
- Uses `dbDelta()` or documented migration strategy.
- Preserves data.
- Re-running activation is safe.
- Dynamic SQL is prepared.

Related files/routes/tables/settings:

- Future `bf_searches`
- Future `bf_clicks`
- Future `bf_alerts`
- Future `bf_cached_offers`
- `bf_ai_sessions`
- `bf_provider_stats`

## Core Reporting Dashboard

Fragile area: Admin reports, bounded analytics queries, CSV export, and provider status snapshots.

Why risky: Reporting aggregates monetization, AI, provider, and content signals. Future changes could expose private data, create unbounded database queries, or weaken capability gates.

What to check after future changes:

- `baf-reports` remains gated by `view_baf_reports`.
- CSV export requires a valid nonce and the same capability.
- Report queries remain bounded to approved date ranges and limited result counts.
- Reports do not expose API keys, raw provider payloads, raw prompts, raw IP addresses, user agents, referrers, full target URLs, private customer data, or conversion identifiers.
- Revenue/conversion reporting remains a safe unavailable state until postback/provider conversion data is implemented and reviewed.
- Provider status sync failures record safe failures without secrets.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/admin/class-reports-page.php`
- `plugins/bookings-flights-core/includes/reports/class-reporting-service.php`
- `plugins/bookings-flights-core/includes/reports/class-reporting-repository.php`
- `plugins/bookings-flights-core/includes/reports/class-provider-stats-repository.php`
- `plugins/bookings-flights-core/includes/migrations/class-provider-stats-table.php`
- `plugins/bookings-flights-core/includes/jobs/class-job-runner.php`
- `plugins/bookings-flights-core/assets/css/admin.css`
- `bf_clicks`
- `bf_ai_sessions`
- `bf_provider_stats`
- `baf-reports`

## AI Consent and Logging

Fragile area: AI itinerary/chat features.

Why risky: AI calls may include private site, business, brand, user, analytics, or travel preference data.

What to check after future changes:

- Consent is required before live provider calls using private data.
- Demo mode remains available.
- Structured outputs are validated before save.
- Raw sensitive prompts and secrets are not logged.
- AI cannot directly publish content.

Related files/routes/tables/settings:

- `baf_ai_settings`
- `baf_consent_settings`
- `bf_ai_sessions`
- `plugins/bookings-flights-core/includes/ai/`
- `plugins/bookings-flights-core/includes/rest/class-ai-itinerary-controller.php`
- `plugins/bookings-flights-core/includes/services/class-ai-itinerary-service.php`
- Future `/wp-json/baf/v1/ai/chat`
- `/wp-json/baf/v1/ai/itinerary`

## Core Background Jobs

Fragile area: WP-Cron scheduling, job status records, and admin job visibility.

Why risky: Background automation must not trigger provider calls during page render, create duplicate cron events, leave orphaned schedules after deactivation, or expose secrets in job status.

What to check after future changes:

- Activation schedules `baf_refresh_cached_offers`, `baf_process_travel_alerts`, `baf_sync_provider_stats`, and `baf_cleanup_job_records` once.
- Deactivation unschedules Phase 5 hooks without deleting permanent content or settings.
- Job handlers remain bounded and avoid unbounded queries.
- Failures and deferrals are recorded without secrets, raw prompts, API tokens, or private customer data.
- Admin job status page remains gated by `manage_baf_settings` and escapes all output.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/cron/class-cron-manager.php`
- `plugins/bookings-flights-core/includes/jobs/class-job-runner.php`
- `plugins/bookings-flights-core/includes/jobs/class-job-repository.php`
- `plugins/bookings-flights-core/includes/admin/class-admin-manager.php`
- `baf_job_status`
- `baf_refresh_cached_offers`
- `baf_process_travel_alerts`
- `baf_sync_provider_stats`
- `baf_cleanup_job_records`

## Core Affiliate Card and Click Handoff

Fragile area: `[baf_travel_cards]`, `[baf_affiliate_disclosure]`, signed affiliate handoff links, and `GET /wp-json/baf/v1/affiliate/click`.

Why risky: This is the first monetized frontend workflow. It must preserve affiliate disclosure, avoid direct checkout, avoid secret exposure, and only redirect to verified partner targets.

What to check after future changes:

- Missing provider consent or missing marker fails with a safe escaped notice.
- Travelpayouts marker/SubID behavior remains documented and predictable.
- API tokens are never rendered in frontend HTML, REST responses, logs, or click records.
- Handoff URLs remain signed, time-limited, and restricted to approved partner hosts.
- Click tracking remains optional and stores only safe metadata plus hashed request identifiers.
- The card remains responsive with usable mobile touch targets.

Related files/routes/tables/settings:

- `plugins/bookings-flights-core/includes/services/class-affiliate-link-service.php`
- `plugins/bookings-flights-core/includes/services/class-click-tracking-service.php`
- `plugins/bookings-flights-core/includes/repositories/class-click-tracking-repository.php`
- `plugins/bookings-flights-core/includes/rest/class-affiliate-click-controller.php`
- `plugins/bookings-flights-core/includes/frontend/class-travel-cards-shortcode.php`
- `plugins/bookings-flights-core/includes/frontend/class-affiliate-disclosure-shortcode.php`
- `plugins/bookings-flights-core/assets/css/frontend.css`
- `/wp-json/baf/v1/affiliate/click`
- `[baf_travel_cards]`
- `[baf_affiliate_disclosure]`
- `$wpdb->prefix . 'bf_clicks'`
- `baf_travelpayouts_settings`
- `baf_consent_settings`
- `baf_tracking_settings`
