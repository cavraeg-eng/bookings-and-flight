# Architecture Baseline

These contracts are shared across phases. Do not rename them without documenting the reason in `.plan/decisions.md`, reconciling affected code and docs, and reviewing downstream phase impact.

## Product Identity

| Contract | Value |
| --- | --- |
| Product name | Bookings and Flights |
| Product model | WordPress-native travel discovery, AI planning, SEO, and affiliate conversion platform |
| Monetization model | Travelpayouts-controlled affiliate handoff through the official plugin where compatible, Travelpayouts widgets, White Label Web, partner links, and SubID reporting |
| Booking model | No direct checkout in MVP; booking completes on partner sites |

## Travelpayouts-Controlled Backend Boundary

The current product direction is Travelpayouts-first for monetized search, widgets, results, partner links, and booking handoff.

Source-of-truth rules:

- WordPress owns content, editorial workflows, SEO pages, page templates, admin state, consent, saved-trip intent, alert intent, and local privacy-aware analytics.
- Travelpayouts owns live flight/hotel search surfaces, widgets, tables, partner links, White Label result pages, affiliate tracking, booking handoff, partner booking/payment, payout source of truth, and SubID performance reporting.
- `bookings-flights-core` may wrap approved Travelpayouts placements, generate consistent SubIDs, render affiliate disclosures, enforce consent, and track safe local placement events.
- `bookings-flights-core` must not become a custom OTA backend, live flight/hotel inventory database, booking engine, payment processor, or supplier reservation system.
- If the official Travelpayouts WordPress plugin is incompatible with the local WordPress version, the fallback is Travelpayouts dashboard-generated widgets/White Label embed code inside a secured WordPress wrapper, not a custom replacement backend.
- `platform/` remains an integration layer unless a documented architecture decision changes the WordPress and Travelpayouts source-of-truth boundary.

Travelpayouts implementation contracts:

- Official WordPress plugin slug: `travelpayouts` when installed from WordPress.org.
- P11.1 local compatibility result: official `travelpayouts` plugin version `1.2.2` is installed and active in this Local WordPress `6.9.4` workspace after a successful activate, deactivate, and reactivate gate on 2026-05-09.
- P11.2 local account setup result: the staged plugin account setup uses the `travelpayouts_admin_settings` option with `account_api_token`, `account_api_marker`, `account_platform`, `account_flights_domain`, and `account_hotels_domain`. Temporary-token smoke checks confirmed saved API tokens are masked in admin field HTML, preserved on blank submission, sanitized before storage, and represented in the Gutenberg token route only as non-secret configured state.
- P11.3 local test-surface result: the official flight shortcode `[tp_popular_routes_widget]` rendered on a temporary WordPress page at desktop and mobile viewport sizes with a Travelpayouts script source of `//www.travelpayouts.com/weedle/widget.js`, a temporary marker/SubID value in the expected `marker.subid` form, and default Aviasales/Travelpayouts handoff host `hydra.aviasales.ru`. Frontend source review found no Travelpayouts API token, postback secret, authorization string, direct checkout, payment, or WordPress-owned booking flow.
- P11.3 hotel surface result: official hotel widget/table shortcodes rendered empty locally because hotel widget models call `HotelLookWidgetShortcodeModel::isActive()`, which depends on `BrandSubscriptionService::isHotelLookAvailable()`, and the staged plugin returns `false` for that capability. Travelpayouts documentation now treats Hotellook tools as shut down, so production hotel placements must use a current Travelpayouts Hotels & Accommodation brand widget or link, such as the user's Trip.com hotel search setup, inside the governed WordPress wrapper.
- P11.3 White Label result: no real White Label domain was configured in the local smoke test. Official documentation confirms the plugin can direct widget/table/search results to configured White Label domains, while White Label Widget type keeps results on the embedded WordPress page and Page type requires domain/CNAME plus Travelpayouts header appearance customization. Keep Widget type preferred for full WordPress header continuity; use Page type only when its fuller result UX is required and the header assets are configured in Travelpayouts.
- P11.4 White Label continuity result: the current WordPress home shell exposes the Bookings and Flights home link, primary navigation, footer navigation, legal links, and route back to `/`. White Label Widget type is the preferred continuity mode because the Travelpayouts search/results module remains embedded inside that WordPress-owned shell. Page-type White Label is allowed only when a fuller Travelpayouts result UX is required and the Travelpayouts dashboard is configured to mirror the home shell.
- P11.4 required Page-type White Label inputs: logo URL, favicon URL, brand name `Bookings and Flights`, header background color or image treatment, heading/search copy, primary menu links for Home, About, Services, and Contact, footer links, legal links, and a visible route back to the main WordPress site.
- P11.5 backend mode decision: Phase 11 final mode is official-plugin-first only for locally validated flight widgets/search surfaces, with Travelpayouts dashboard-generated Hotels & Accommodation brand widgets/links, including Trip.com hotel search code, and White Label Widget/Page code wrapped by the future capability-gated `baf` placement registry when the official plugin path is unavailable or not fully configured.
- P11.5 search surface rule: planned `/search/flights` and `/search/hotels` may expose WordPress-owned shell configuration, approved placement metadata, disclosure copy, consent state, SubID/handoff metadata, or safe redirect information. They must not store or serve canonical live flight or hotel inventory without a new documented decision.
- P11.6 local review result: Phase 11 passed the local evidence gate on 2026-05-12 with no custom inventory backend, no direct checkout/payment flow, no secret exposure in the tested frontend source, and no new WordPress REST route, database table, or migration. The real Widget-type White Label ID, White Label results URL, and Trip.com hotel partner embed are saved, placed on published WordPress pages, and browser-validated on desktop and mobile-width views.
- P11.6 account/widget follow-up: `travelpayouts_admin_settings` now preserves real account values across partial account saves, exposes the hotels White Label domain field, and keeps a saved Project visible when the Travelpayouts traffic-source API cannot return choices. `baf_travelpayouts_settings` now stores `white_label_widget_id`, optional `white_label_results_url`, and `hotel_widget_script_url` for minimal Travelpayouts dashboard wrappers; raw White Label dashboard script is reduced to the extracted `wl_id`, and hotel widget code is reduced to an approved Travelpayouts script or Trip.com partner iframe URL for `[baf_travelpayouts_hotel_widget]`. The direct Trip.com partner iframe URL is preferred locally because the script-generated third-party iframe rendered as a blank box in Chrome with content-blocking extensions, while the direct partner handoff rendered the Trip.com search form.
- Local publish hardening redacted an embedded Airtable personal access token from the staged plugin package. The Airtable distribution script now fails closed unless a token is supplied outside Git through `TRAVELPAYOUTS_AIRTABLE_TOKEN`.
- WordPress.org still warns that `travelpayouts` has not been tested with the latest three major WordPress releases, so production use remains scoped: official plugin for the tested flight widget path, dashboard-generated fallback embeds for hotels and White Label until real account/domain validation proves a broader official-plugin path.
- Preferred placement path: official Travelpayouts plugin block/widget/table/link tools.
- Fallback placement path: capability-gated `baf` widget registry that renders Travelpayouts-provided embed code.
- SubID convention: lowercase Latin letters, numbers, and underscores in the pattern `{channel}_{surface}_{vertical}_{slug}_{placement}`.
- SubIDs are placement/reporting identifiers only. Do not include names, emails, IP addresses, raw prompt text, private trip details, or per-user identifiers in Travelpayouts SubIDs.
- White Label Web may be used for on-site search result experiences, but SEO landing pages should remain WordPress-owned because Travelpayouts documents crawler limitations for White Label result pages.
- White Label search/result pages must preserve home-site header continuity. Prefer White Label Widget type inside WordPress pages where possible; if Page type is required, configure the Travelpayouts White Label header logo, brand name, favicon, color/image treatment, heading copy, and header/footer menu links to match the Bookings and Flights theme as closely as Travelpayouts customization allows.
- Booking.com fares must not be promised inside White Label because Travelpayouts documents that Booking.com fares are unavailable there due to Booking.com policy.

Detailed execution blueprint: `.plan/travelpayouts-wordpress-booking-site-blueprint.md`.

## WordPress Plugins

| Contract | Value |
| --- | --- |
| Planned core plugin name | Bookings and Flights Core |
| Core plugin slug | `bookings-flights-core` |
| Core plugin main file | `plugins/bookings-flights-core/bookings-flights-core.php` |
| Core PHP namespace | `BAF\Core` |
| Core function prefix | `baf_` |
| Core plugin version constant | `BAF_CORE_VERSION` |
| Core database schema version constant | `BAF_CORE_DB_VERSION` |
| Core plugin path constants | `BAF_CORE_FILE`, `BAF_CORE_DIR`, `BAF_CORE_URL`, `BAF_CORE_BASENAME` |
| Core plugin version option | `baf_core_version` |
| Core lifecycle hooks | `baf_core_activated`, `baf_core_deactivated`, `baf_core_loaded` |
| Core post type registrar | `BAF\Core\Post_Types\Post_Type_Registrar` |
| Core taxonomy registrar | `BAF\Core\Taxonomies\Taxonomy_Registrar` |
| Core capability manager | `BAF\Core\Capabilities\Capability_Manager` |
| Existing affiliate bridge plugin slug | `bookings-and-flights-affiliate-bridge` |
| Existing affiliate bridge main file | `plugins/bookings-and-flights-affiliate-bridge/bookings-and-flights-affiliate-bridge.php` |
| Existing affiliate bridge namespace | `BAF\AffiliateBridge` |
| Existing content manager plugin slug | `bookings-and-flights-content-manager` |
| Existing content manager main file | `plugins/bookings-and-flights-content-manager/bookings-and-flights-content-manager.php` |
| Existing content manager prefix | `bookings_and_flights_` |

## WordPress Themes and Frontend

| Contract | Value |
| --- | --- |
| Existing static WordPress theme | `themes/bookings-and-flights-static` |
| Existing headless theme | `themes/bookings-and-flights-headless` |
| Existing platform monorepo | `platform/` |
| Platform web app | `platform/apps/web` |
| Platform search API | `platform/services/search-api` |
| Platform shared package | `platform/packages/shared` |
| Phase 12 IA map | `.plan/phase-12-sitemap-navigation-page-ownership.md` |
| Phase 12 design system inventory | `.plan/phase-12-design-system-component-inventory.md` |
| Phase 12 CSS architecture prep | `.plan/phase-12-css-split-theme-architecture.md` |
| Phase 12 widget frame rules | `.plan/phase-12-widget-frame-layout-rules.md` |
| Phase 12 page-level wireframes | `.plan/phase-12-page-level-wireframes.md` |
| Phase 12 completion gate | `.plan/phase-12-completion-gate.md` |
| Primary public nav target | Flights, Hotels, Explore, Deals, Trip Planner, Saved Trips |

Phase 12.1 assigns WordPress ownership for the branded shell, sitemap, navigation, editable pages, CPT archives/singles, SEO page families, saved-trip intent, and alert intent. Travelpayouts remains the owner of live flight/hotel search, result surfaces, widgets, partner handoff, booking/payment, supplier reservations, and affiliate reporting. The current published primary menu still needs to be reconciled with the Phase 12 nav target before visual/template implementation is complete.

Phase 12.2 assigns the design-token direction, real-media strategy, component inventory, disclosure treatment, accessibility requirements, and initial widget-frame guardrails. Future theme work should preserve WordPress-owned page shells, use real travel media instead of gradient-only hero treatment, keep search components dense and keyboard-accessible, and support Travelpayouts widget frames without hidden disclosures, clipping, or unreserved layout shifts.

Phase 12.3 prepares static theme CSS ownership for later design work. Current theme CSS loads in this order: `fonts.css`, `tokens.css`, `base.css`, `components.css`, `header.css`, `mobile-nav.css`, `footer.css`, then page-specific CSS discovered from `page-{slug}.php`. `components.css` owns shared primitives such as `.skip-link` and `.btn`; `header.css` owns header/nav layout only.

Phase 12.4 defines Travelpayouts widget-frame reservations, responsive constraints, public/admin state behavior, disclosure placement, performance rules, and Phase 13 registry metadata prerequisites. Affiliate disclosures and safe fallback handoff links must remain outside provider iframes/scripts, and third-party provider scripts must remain scoped to approved placements.

Phase 12.5 defines structured desktop, tablet, and mobile wireframes for home, flights, hotels, explore, destination detail, route detail, deals, AI planner, saved trips, about/legal, and admin widget placement surfaces. Future template work should follow the documented owner/template/phase mapping, keep monetized widgets inside governed frames, and use the screenshot plan before marking visual implementation complete.

Phase 12.6 records the final Phase 12 review, runtime screenshot, keyboard navigation, and documentation gate. The runtime keyboard follow-up keeps Travelpayouts White Label mount focus visible and routes keyboard hotel handoff through the visible Trip.com link instead of the provider iframe. Phase 13 can start from the documented IA, design system, CSS ownership, widget-frame, page-wireframe, known-issue, and validation baselines without rediscovering those decisions.

Phase 18.1 starts the AI planner surface on a WordPress-owned route at `/trip-planner/`. The route is registered by `BAF\Core\Frontend\AI_Planner_Page` with query var `baf_ai_planner`, template `plugins/bookings-flights-core/templates/ai-planner-page.php`, route-scoped assets `baf-ai-planner`, and one-time rewrite invalidation tracked by option `baf_ai_planner_rewrite_version`. Header, mobile nav, homepage, destination, and taxonomy planner CTAs should point to `/trip-planner/` once this route exists. The page may call the protected AI itinerary REST endpoint, but it must not publish, book, pay, execute provider searches, or store live inventory.

Phase 18.2 adds optional editor-controlled draft saving from the planner. The planner sends `save=true` only for users with `edit_baf_content`; the server also rejects unauthorized save requests before provider selection. Saved records are `draft` `trip_plan` posts with editable day-by-day post content and sanitized itinerary metadata. AI still cannot publish, book, pay, execute provider searches, or create Travelpayouts placement drafts in this slice.

Phase 18.3 defines the AI opportunity contract as `travelpayouts_opportunity_v1`: recommendation-only, `not_executed`, approval-required, disclosure-required, and blocked from provider-owned booking, payment, pricing, availability, confirmation, provider-link, live-inventory, published, or executed-action claims.

Phase 18.4 adds local approval-oriented handoff preparation from saved `trip_plan` drafts. Editors can prepare `ai_handoff_intent_v1` records only after capability, nonce, source-post, global provider-consent, and per-request handoff-consent checks pass. The stored intent remains local with `provider_action=not_executed`, `provider_action_executed=false`, and `external_request_sent=false`; it does not call Travelpayouts, create provider links, publish posts, book, pay, or expose raw prompts.

## REST Namespace

| Contract | Value |
| --- | --- |
| WordPress REST namespace | `baf/v1` |
| Public REST base | `/wp-json/baf/v1/` |
| Existing public config route | `GET /config` |
| Existing protected status route | `GET /status` |

Implemented core routes:

- `GET /destinations`
- `GET /routes`
- `GET /affiliate/click`
- `POST /ai/itinerary`
- `POST /ai/handoff`

Phase 8 reporting is admin-only through `admin.php?page=baf-reports`; no reporting REST endpoint is exposed.

Core Phase 3 routes are read-only public discovery collections. They only return published `destination` or `route` posts and expose safe fields: `id`, `type`, `slug`, `link`, `date`, `modified`, rendered `title`, and rendered `excerpt`. They do not expose registered private post meta, provider credentials, drafts, private posts, partner records, alerts, trip plans, or AI/session data.

Core route parameters:

- `page`: positive integer, default `1`.
- `per_page`: integer from `1` to `50`, default `10`.
- `search`: sanitized text search string.
- `orderby`: one of `date`, `modified`, `title`, `menu_order`.
- `order`: one of `ASC`, `DESC`.

Planned routes:

- `POST /ai/chat`
- `POST /trips/save`
- `GET /trips/:id`
- `POST /alerts`
- `POST /affiliate/link`
- `GET /search/flights`
- `GET /search/hotels`

Under the Travelpayouts-controlled backend boundary, planned `/search/flights` and `/search/hotels` routes must not become custom live inventory APIs. If implemented, they should expose only safe WordPress-owned shell configuration, placement metadata, status, or redirect/handoff information needed to render Travelpayouts-controlled widgets, links, or White Label surfaces.

P11.5 confirms this route boundary as part of the final backend mode decision. Existing `platform/` Fastify search routes and adapters are optional integration infrastructure, not the canonical WordPress booking/search backend. Activating them as user-facing WordPress search/result backends requires a new documented decision, explicit privacy/security review, and proof they do not store canonical supplier inventory in WordPress.

## Phase 4 Affiliate Workflow

Phase 4 implements a WordPress-native destination-to-affiliate-card workflow. Editors can place `[baf_travel_cards]` in editable content and render a standalone disclosure with `[baf_affiliate_disclosure]`.

Workflow behavior:

- Affiliate cards are generated by `BAF\Core\Services\Affiliate_Link_Service`.
- Initial provider support is Travelpayouts/Aviasales handoff links only.
- Booking remains on partner sites; the plugin does not collect payment or perform checkout.
- Provider request consent (`baf_consent_settings.allow_provider_requests`) and a Travelpayouts marker are required before monetized links render.
- Missing consent or missing marker renders an escaped frontend notice rather than a broken or secret-bearing link.
- Generated Travelpayouts links use the documented `marker` value with the generated SubID appended as `marker.subid`.
- SubIDs are lowercase, underscore-normalized, and built from `baf_tracking_settings.subid_prefix`, shortcode/source context, and post ID where available.
- Handoff links point to `GET /wp-json/baf/v1/affiliate/click`, include a signed target payload, expire after 24 hours, restrict redirects to `aviasales.com`/`www.aviasales.com`, and never expose provider API tokens.
- Click tracking is optional through `baf_tracking_settings.enable_click_tracking`; when enabled, only event metadata and hashed request identifiers are stored.

## Custom Post Types

Registered by `BAF\Core\Post_Types\Post_Type_Registrar` on `init`.

| CPT Key | Purpose | Public | Archive/Rewrite | Primary Capability Set |
| --- | --- | --- | --- | --- |
| `destination` | City, country, and region guides | Yes | `destinations` | `edit_baf_content`, `publish_baf_content` |
| `route` | Origin-destination flight pages | Yes | `routes` | `edit_baf_content`, `publish_baf_content` |
| `travel_deal` | Editorial or cached deal posts | Yes | `travel-deals` | `edit_baf_content`, `publish_baf_content` |
| `trip_plan` | AI-generated and saved itineraries | No | Disabled | `edit_baf_content`, `publish_baf_content` |
| `travel_partner` | Travelpayouts and provider records | No | Disabled | `manage_baf_affiliates` |
| `travel_alert` | Price alert landing records or editorial alert pages | No | Disabled | `manage_baf_alerts` |

All registered CPTs use `show_in_rest => true` for block editor compatibility and WordPress core REST behavior. No custom `baf/v1` routes are introduced in Phase 1.

## Taxonomies

Registered by `BAF\Core\Taxonomies\Taxonomy_Registrar` on `init`.

| Taxonomy Key | Purpose | Hierarchical | Attached CPTs |
| --- | --- | --- | --- |
| `travel_region` | Continent, country, region, and destination grouping | Yes | `destination`, `route`, `travel_deal` |
| `travel_style` | Budget, family, luxury, beach, business, adventure, culture | No | `destination`, `route`, `travel_deal`, `trip_plan` |
| `travel_vertical` | Flights, hotels, cars, activities, insurance, eSIM, transfers | Yes | `route`, `travel_deal`, `travel_partner` |
| `travel_season` | Month, season, holiday, or timing intent | No | `destination`, `route`, `travel_deal`, `trip_plan`, `travel_alert` |

Taxonomy term management maps to `edit_baf_content`; assignment also requires `edit_baf_content`.

## Post Meta Keys

Use the `baf_` prefix for new meta. Phase 1 registers the following post meta keys with `show_in_rest => false`, sanitization callbacks, and edit authorization through `edit_post`/dedicated Bookings and Flights capabilities:

- `baf_origin`
- `baf_destination`
- `baf_origin_airport`
- `baf_destination_airport`
- `baf_destination_best_time`
- `baf_destination_facts`
- `baf_destination_activities`
- `baf_destination_seasonal`
- `baf_route_travel_time`
- `baf_route_airport_notes`
- `baf_route_flexible_dates`
- `baf_route_destination_notes`
- `baf_deal_seasonal_context`
- `baf_deal_weekend_ideas`
- `baf_deal_theme_notes`
- `baf_deal_activity_notes`
- `baf_deal_partner_notes`
- `baf_deal_source_note`
- `baf_departure_window`
- `baf_return_window`
- `baf_budget_min`
- `baf_budget_max`
- `baf_travel_style`
- `baf_hotel_guide_summary`
- `baf_hotel_neighborhoods`
- `baf_hotel_best_for`
- `baf_hotel_family_notes`
- `baf_hotel_luxury_notes`
- `baf_hotel_budget_notes`
- `baf_hotel_landmark_notes`
- `baf_affiliate_vertical`
- `baf_provider_ids`
- `baf_subid_template`
- `baf_ai_source_session_id`
- `baf_itinerary_json`
- `baf_ai_opportunity_schema`
- `baf_ai_handoff_intents`
- `baf_alert_route`
- `baf_alert_frequency`
- `baf_alert_email`
- `baf_alert_user_id`
- `baf_alert_route_post_id`
- `baf_alert_travelers`
- `baf_alert_cabin`
- `baf_alert_surface`
- `baf_alert_source_url`
- `baf_alert_consent_at`
- `baf_alert_status`
- `baf_partner_apply_url`
- `baf_partner_status`

Phase 15.5 adds local flight alert intent capture through the `[baf_flight_alert_signup]` shortcode and `admin-post.php` actions `baf_save_flight_alert` / `admin_post_nopriv_baf_save_flight_alert`. Alert signup stores private `travel_alert` posts with route codes, optional date/traveler/cabin context, contact email, source surface, consent timestamp, and local status only. It does not store provider fare inventory, partner booking records, payment data, reservation state, or supplier result payloads. Public writes require a WordPress nonce, explicit consent, email validation, bounded route-code input, and a short per-client/email/route transient throttle; alert administration remains capability-gated by `manage_baf_alerts`.

Phase 15.6 documents the flight SEO/indexing boundary. WordPress owns indexable `/routes/`, sanitized origin-filter archives such as `/routes/?route_origin=NYC`, and individual `route` posts as editorial SEO pages. The Flights page remains an indexable search-handoff entry point, but transient flight-search query URLs are not SEO landing pages: they render `noindex, follow` and canonicalize to `/flights/`. Travelpayouts White Label, official widgets, and partner scripts remain provider-owned result/handoff surfaces and must not become the canonical SEO source, local fare inventory, booking backend, or source of fake price/scarcity claims.

Phase 15.7 completes the local Flights Experience review gate. The validated Phase 15 boundary is: WordPress owns the Flights landing page, route/origin editorial pages, route SEO metadata, local alert intent capture, disclosure copy, and governed widget placement shells; Travelpayouts and partner providers own live result data, filters, fares, booking, payment, changes, support, affiliate payout state, and external provider runtime behavior. Phase 16 hotel/stays work should reuse these boundaries rather than creating a custom inventory, checkout, or supplier-result backend.

Phase 16.1 starts the hotel/stays implementation on the same boundary. WordPress owns the `/hotels/` shell, local hotel intent fields, display of selected destination/date/guest/room/stay-focus intent, disclosure copy, and approved `hotels_partner_search` placement frame. Trip.com, Travelpayouts, or the partner provider owns live hotel results, rates, room inventory, taxes, policies, maps, amenity filters, booking, payment, changes, support, and supplier availability. The Hotels page must not claim Booking.com White Label inventory or a WordPress-owned hotel inventory/search backend.

Phase 16.3 extends the hotel placement strategy without changing the backend boundary. The registry seeds `hotels_map_handoff` and `hotels_listing_handoff` as hotel companion placements using `render_mode="handoff_link"` and the existing approved hotel partner URL when that URL is safe for direct handoff. These placements provide readable SubIDs for hotel map and listing entry points while the partner provider continues to own live map controls, hotel listings, rates, taxes, policies, booking, payment, changes, and support.

Phase 16.4 strengthens the hotel disclosure and handoff boundary without adding routes, provider contracts, options, tables, cron jobs, REST endpoints, shortcodes, or blocks. Hotel/stays pages must describe WordPress hotel controls as intent or editorial context unless the rendered Travelpayouts/partner surface owns the actual result filtering. Every monetized hotel placement rendered through `template-parts/travel-search-placement.php` exposes visible `Affiliate disclosure:` copy and links the section to that disclosure with `aria-describedby`. Hotel surfaces must not promise Booking.com White Label inventory, WordPress-owned live hotel inventory, direct checkout, auto-booking, guaranteed rates, or unsupported local hotel filters.

Phase 16.5 confirms the hotel mobile/source review boundary without adding routes, provider contracts, options, tables, cron jobs, REST endpoints, shortcodes, blocks, or registry schema changes. WordPress-owned hotel pages must keep header navigation, local intent controls, guide cards, partner handoff buttons, companion placement cards, and disclosure bands responsive through 320px narrow viewports with no horizontal overflow, no clipped handoff controls, no missing disclosures, and no sub-44px interactive targets. Provider-owned WebGL/runtime warnings remain separate from app-owned console and request failures.

Phase 16.6 confirms the final Hotels and Stays boundary. WordPress owns the Hotels shell, city guide/editorial context, partner placement shells, affiliate disclosures, SubID context, source-safe metadata, and transient hotel-intent SEO behavior. Hotel-intent query URLs are not indexable landing pages: they render `noindex, follow` and canonicalize to `/hotels/`, while `/hotels/` without transient query state remains the indexable hotel entry surface. The hotel path helper strips the site's `home_url()` path prefix so subdirectory installs preserve the same transient-query robots behavior. Travelpayouts, Trip.com, and partner providers continue to own live inventory, rates, maps, filters, taxes, room policies, booking, payment, changes, support, and supplier availability.

Phase 17.1 starts the destination content engine on the existing `destination` CPT. WordPress owns the `/destinations/` archive, destination single guide layout, editable post content, taxonomy labels, destination fact modules, related destination links, related route links, and SEO title/description copy. The destination template may link to existing `/flights/`, `/hotels/`, and `/trip-planner/` shell surfaces, but it must not create live provider inventory, custom search APIs, booking, payment, or auto-publishing behavior. Monetized hotel modules continue to render the existing approved `hotels_partner_search`, `hotels_map_handoff`, and `hotels_listing_handoff` placements with `surface="hotels"` and `channel="destination_single"` context rather than a new unapproved destination placement surface.

Phase 17.2 deepens the route content engine on the existing `route` CPT. WordPress owns the `/routes/` archive, origin-filtered route archives, route single guide layout, editable post content, route fact panels, travel-time/airport/flexible-date/destination notes, related route links, matching destination guide links, destination hotel/activity handoff modules, local alert intent links, and SEO copy. New route-only private meta keys are `baf_route_travel_time`, `baf_route_airport_notes`, `baf_route_flexible_dates`, and `baf_route_destination_notes`; they use textarea sanitization, edit-meta authorization, and `show_in_rest => false`. Route templates may render existing approved `flights_white_label_search`, `flights_low_price_calendar`, `flights_popular_routes`, and `flights_route_map` placements on the `route` surface, and may link to existing `/hotels/` and `/#explore` shell surfaces, but they must not create live provider inventory, custom fare APIs, fake prices, booking, payment, or auto-publishing behavior. Related routes require shared airport meta or shared route taxonomy terms before rendering.

Phase 17.3 adds the deal content engine on the existing `travel_deal` CPT. WordPress owns the `/travel-deals/` archive, travel deal single layout, editable post content, taxonomy labels, editorial budget/date context, seasonal/weekend/theme/activity/source/partner notes, sponsored partner cards, related deal links, matching route links, matching destination guide links, and SEO copy. New deal-only private meta keys are `baf_deal_seasonal_context`, `baf_deal_weekend_ideas`, `baf_deal_theme_notes`, `baf_deal_activity_notes`, `baf_deal_partner_notes`, and `baf_deal_source_note`; they use textarea sanitization, edit-meta authorization, and `show_in_rest => false`. Deal singles may render the existing approved `flights_white_label_search` placement on the `deal` surface and may link to existing `/flights/`, `/hotels/`, `/#explore`, `/routes/`, and `/destinations/` shell surfaces, but they must not create live provider inventory, custom package APIs, fake urgency, unverified prices, booking, payment, or auto-publishing behavior. Related deal links require shared travel taxonomy terms before rendering.

Phase 17.4 adds shared taxonomy archives for `travel_region`, `travel_style`, `travel_vertical`, and `travel_season` through the static theme `taxonomy.php` template. WordPress owns the term hero, term description, taxonomy-aware SEO title/description/canonical metadata, internal-linking rule cards, public content card query, and links to existing Flights, Hotels, Destinations, Routes, Deals, and Trip Planner shell surfaces. Taxonomy archive queries are bounded, paginated, and public-only: they include published `destination`, `route`, and `travel_deal` posts, while `travel_vertical` archives are limited to published `route` and `travel_deal` posts. Archives must not surface private posts, non-public `trip_plan`, `travel_alert`, or `travel_partner` records, custom provider inventory, live results, checkout, payment, supplier availability, or auto-publishing behavior.

## User Meta Keys

Planned keys:

- `baf_home_airport`
- `baf_travel_preferences`
- `baf_saved_trip_ids`
- `baf_alert_preferences`
- `baf_ai_consent_at`

## Option Keys

Existing:

- `baf_supplier_credentials`
- `baf_search_api_url`
- `baf_postback_secret`

Implemented:

- `baf_core_version`
- `baf_db_version`
- `baf_db_version_clicks`
- `baf_db_version_ai_sessions`
- `baf_db_version_provider_stats`
- `baf_settings`
- `baf_travelpayouts_settings`
  - `marker`
  - `api_token`
  - `white_label_widget_id`
  - `white_label_results_url`
  - `hotel_widget_script_url`
- `baf_travelpayouts_widget_registry`
- `baf_ai_settings`
- `baf_consent_settings`
- `baf_tracking_settings`
- `baf_job_status`

Secrets must remain server-side and masked in admin UI.

`baf_travelpayouts_widget_registry` is installed idempotently by `BAF\Core\Services\Travelpayouts_Widget_Registry_Service` as a non-autoloaded option. It stores:

- `schema_version`
- `placements`, keyed by sanitized placement key
- each placement's `key`, `name`, `vertical`, `context`, `widget_family`, `render_mode`, `status`, `subid_pattern`, `public_surfaces`, `consent_required`, `disclosure`, `frame`, `fallback`, private `embed`, admin `notes`, `created_at`, and `updated_at`

Private `embed.reference`, `embed.url`, and admin notes are available through capability-gated service reads and writes. Trusted server-side WordPress renderers may use the service's rendering read path for active, configured placements; REST, JavaScript, block-editor previews, and other untrusted/public responses must use the service's public placement shape, which strips private embed values and notes while retaining safe metadata such as status, placement family, SubID pattern, disclosure requirements, frame reservations, fallback metadata, and whether the placement is configured.

The `baf-widget-placements` admin screen is implemented by `BAF\Core\Admin\Widget_Placements_Page` and `BAF\Core\Admin\Widget_Placement_Form`. It is visible only to users who can `manage_baf_affiliates` or `manage_baf_settings`, writes through the `baf_save_widget_placement` and `baf_delete_widget_placement` `admin-post.php` actions, uses WordPress nonces for writes, and renders raw embed references/URLs only inside the capability-gated edit form. Affiliate-only managers receive the Bookings & Flights parent menu with Widget Placements as the available destination, while settings-only sections remain hidden unless `manage_baf_settings` is present. Placement listing rows, notices, dashboard shortcuts, and public placement reads must not print private embed data.

Frontend placement rendering is implemented by `BAF\Core\Frontend\Travelpayouts_Widget_Renderer`, `[baf_travelpayouts_widget]`, and the dynamic `baf/travelpayouts-widget` block. Shortcode and block attributes store only placement key, surface, channel, slug, and optional class data; raw embed references and URLs stay in the registry option and are read only by trusted server-side rendering. `BAF\Core\Services\Travelpayouts_Widget_Subid_Service` owns the runtime SubID convention `{channel}_{surface}_{vertical}_{slug}_{placement}`, normalizes generated SubIDs to lowercase Latin letters, numbers, and underscores, and mutates provider URLs without dropping existing Travelpayouts partner markers. Public output may contain approved provider script or iframe URLs as rendered widget markup, but public REST-style placement projections and editor attributes must not expose private `embed.reference`, private `embed.url`, or admin notes. Public widget frames expose safe state metadata through `data-baf-state` and `data-baf-render-mode`; consent-disabled, disabled, missing-configuration, loading, no-script, unavailable, and configured states must render escaped copy and must not print provider output when consent or configuration is missing. Existing `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes remain available for validated setup checks and backward compatibility; new page/template work should prefer `[baf_travelpayouts_widget placement="flights_white_label_search"]`, `[baf_travelpayouts_widget placement="hotels_partner_search"]`, or the governed hotel companion placement keys.

Phase 15.2 migrated the widget registry schema to `1.0.1`. The migration adds `route` to the starter `flights_white_label_search` placement's `public_surfaces` once so route detail templates can render the approved Travelpayouts White Label widget while preserving the surface allowlist guard for disallowed contexts. Future admin edits to the placement allowlist remain respected after the one-time migration has run.

Phase 15.3 migrated the widget registry schema to `1.0.2`. The migration seeds approved official Travelpayouts plugin shortcode placements for `flights_low_price_calendar` (`tp_calendar_widget`), `flights_popular_routes` (`tp_popular_routes_widget`), and `flights_route_map` (`tp_map_widget`) on the `flights` and `route` public surfaces. These placements use trusted server-side rendering only: `BAF\Core\Frontend\Official_Shortcode_Renderer` checks the approved `tp_` shortcode reference, passes sanitized runtime `origin` and `destination` IATA codes only where available, attaches the generated SubID, and wraps provider output with reserved frame dimensions, loading, fallback, no-script, shadow-DOM loaded-state detection, and one-time visible iframe refresh for map widgets. Public placement projections and shortcode/block attributes still must not expose raw registry embed data or admin notes.

Phase 16.3 migrated the widget registry schema to `1.0.3`. The migration seeds `hotels_map_handoff` and `hotels_listing_handoff` only if they do not already exist, preserving later admin edits. The renderer adds `baf-travelpayouts-widget--{widget_family}` and `baf-travelpayouts-widget--placement-{key}` classes so CSS can scope the current compact Trip.com search iframe treatment to `hotels_partner_search` without clipping future hotel map/listing iframes or link-card placements.

Phase 17.3 migrated the widget registry schema to `1.0.4`. The migration adds `deal` to the existing `flights_white_label_search` placement's `public_surfaces` once so travel deal singles can render the approved Travelpayouts White Label widget with deal-specific SubIDs while preserving the surface allowlist guard for other contexts and respecting future admin edits.

Phase 17.4 adds taxonomy archive templates and internal-linking rule cards without a widget registry schema change, provider contract change, REST route, option, table, cron hook, shortcode, block, or custom inventory backend. Taxonomy handoff links pass local intent context to existing shell surfaces only; Travelpayouts, Trip.com, and partner providers continue to own live search results, filters, booking, payment, changes, support, and supplier availability.

Phase 15.4 preserves White Label result-flow continuity at the theme shell level. Flights and route detail templates render `template-parts/white-label-continuity.php` before `flights_white_label_search`, keeping the Bookings and Flights header, primary navigation, footer disclosure, WordPress-owned route/SEO links, and visible provider handoff language adjacent to the embedded Widget-type White Label module. Widget type remains the preferred continuity mode because the provider result module loads inside the WordPress-owned shell. Page-type White Label is still allowed only when the Travelpayouts dashboard mirrors the Bookings and Flights logo, brand name, menu, footer/legal links, and route back to WordPress.

Phase 15.7 confirms the final Flights Experience gate passed locally with no new routes, tables, options, capabilities, cron hooks, shortcodes, blocks, provider contracts, or public REST exposure. The Phase 16 start point is the completed flight search/handoff, route guide, discovery widget, White Label continuity, alert intent, and SEO boundary documented above.

Phase 16.1 confirms the first Hotels surface consumes the existing `hotels_partner_search` placement key and adds no new registry schema version, provider contract, REST route, option, table, cron hook, shortcode, or block. Hotel intent query parameters are local display state only and are removed from the visible browser URL by the shared search-surface cleanup script after the server-rendered summary loads.

Phase 16.6 confirms the final Hotels gate adds no new registry schema version, provider contract, REST route, option, table, cron hook, shortcode, block, or custom inventory backend. The only code contract change is the SEO metadata helper recognizing transient hotel-intent query parameters on `/hotels/` for `wp_robots` noindex/follow output, matching the existing transient flight-query boundary and its subdirectory path handling.

Phase 13.5 security review confirmed that the current registry service, placement admin form/actions, Settings API provider fields, shortcode/block renderer, frontend output, and core `baf/v1` routes preserve the intended capability, nonce, sanitization, escaping, and exposure boundaries. Anonymous users cannot read private registry records or write placements, public placement projections strip raw embed fields and notes, missing admin nonces fail before writes, saved API/AI secrets render only as masked empty fields, core REST endpoints use endpoint-specific permission callbacks, and the affiliate bridge public `config` and `postback` routes remain acceptable only under their documented non-secret/secret-validated contracts.

Phase 13.6 completed the final registry and safe embed gate. Phase 14 and later public templates should consume approved placements through `[baf_travelpayouts_widget]`, `baf/travelpayouts-widget`, or trusted PHP calls to `Travelpayouts_Widget_Renderer`; they should not copy raw provider snippets, private registry URLs, dashboard notes, or saved settings into templates. The stable starter keys are `flights_white_label_search` for Travelpayouts White Label flight search and `hotels_partner_search` for the Trip.com/Hotels partner iframe plus visible sponsored handoff. Consent-disabled rendering must remain a first-class public state with no third-party iframe, script, or handoff output.

The aggregate `baf_db_version` records the current core schema version for quick status checks. Each custom table also keeps its own table-specific schema version option so a successful upgrade for one table cannot cause another table's `dbDelta()` pass to be skipped during the same release.

## Custom Tables

Use `$wpdb->prefix` and the site's charset/collation.

| Logical Name | Runtime Name |
| --- | --- |
| Searches | `$wpdb->prefix . 'bf_searches'` |
| Clicks | `$wpdb->prefix . 'bf_clicks'` |
| Alerts | `$wpdb->prefix . 'bf_alerts'` |
| Cached offers | `$wpdb->prefix . 'bf_cached_offers'` |
| AI sessions | `$wpdb->prefix . 'bf_ai_sessions'` |
| Provider stats | `$wpdb->prefix . 'bf_provider_stats'` |

Implemented Phase 6 AI session table:

- `$wpdb->prefix . 'bf_ai_sessions'`
- Created by `BAF\Core\Migrations\AI_Sessions_Table` using `dbDelta()`.
- Stores run UUID, timestamps, user ID, provider, mode, status, prompt version, optional source post ID, request/output hashes, output summary, and sanitized error code/message.
- Does not store raw prompts, raw model responses, provider API keys, private customer data, IP addresses, full provider payloads, or generated itinerary JSON.

Implemented Phase 8 provider stats table:

- `$wpdb->prefix . 'bf_provider_stats'`
- Created by `BAF\Core\Migrations\Provider_Stats_Table` using `dbDelta()`.
- Stores snapshot UUID, timestamp, provider key, safe status, metric key/value, sanitized message, and source label.
- Does not store provider API keys, raw provider responses, raw request payloads, private customer data, raw IP addresses, user agents, referrers, revenue details, or conversion identifiers.

## Capabilities

Dedicated capabilities are registered for administrators by `BAF\Core\Capabilities\Capability_Manager`:

- `manage_baf_settings`
- `edit_baf_content`
- `publish_baf_content`
- `manage_baf_affiliates`
- `view_baf_reports`
- `run_baf_ai`
- `manage_baf_alerts`

Initial Phase 1 mapping is administrator-only. Future broader editor/member workflows must explicitly grant only the minimum needed dedicated capabilities.

Dedicated primitive capabilities are mapped directly by `BAF\Core\Capabilities\Capability_Manager::map_dedicated_capabilities()` to avoid WordPress treating unknown primitive capabilities as denied meta capabilities.

## Repository and Service Contracts

| Contract | Purpose |
| --- | --- |
| `BAF\Core\AI\AI_Provider_Interface` | AI provider contract for itinerary generation adapters |
| `BAF\Core\AI\Demo_AI_Provider` | Offline demo itinerary provider that requires no live credentials |
| `BAF\Core\AI\OpenAI_Provider` | Live OpenAI Chat Completions adapter gated by configuration and consent |
| `BAF\Core\AI\Provider_Factory` | Selects demo/live provider based on `baf_ai_settings` and `baf_consent_settings`; exposes supported live-provider checks for UI readiness |
| `BAF\Core\AI\Itinerary_Schema` | Validates and sanitizes structured itinerary output before save/response |
| `BAF\Core\Repositories\Repository_Interface` | Base interface for WordPress-backed travel entity repositories |
| `BAF\Core\Repositories\Travel_Entity_Repository` | Bounded CPT-backed data access helper for Phase 1 travel entities |
| `BAF\Core\Repositories\Click_Tracking_Repository` | Stores safe affiliate click event metadata in `$wpdb->prefix . 'bf_clicks'` |
| `BAF\Core\Repositories\AI_Session_Repository` | Stores hashed, non-secret AI generation run metadata in `$wpdb->prefix . 'bf_ai_sessions'` |
| `BAF\Core\Reports\Provider_Stats_Repository` | Stores and reads bounded provider status snapshots in `$wpdb->prefix . 'bf_provider_stats'` |
| `BAF\Core\Reports\Reporting_Repository` | Aggregates bounded click, AI session, provider, and content report data |
| `BAF\Core\Services\Travel_Entity_Service` | Capability-aware service boundary for reading/writing travel entities |
| `BAF\Core\Services\Affiliate_Link_Service` | Builds Travelpayouts affiliate cards, SubIDs, disclosures, and signed handoff URLs |
| `BAF\Core\Services\Click_Tracking_Service` | Gates optional click tracking before repository writes |
| `BAF\Core\Services\AI_Itinerary_Service` | Orchestrates provider selection, schema validation, run logging, and optional draft trip-plan save |
| `BAF\Core\Services\AI_Opportunity_Handoff_Service` | Prepares approved local handoff intents from validated Travelpayouts AI opportunities without executing provider actions |
| `BAF\Core\Frontend\AI_Planner_Page` | Registers the `/trip-planner/` frontend route, route-scoped planner assets, safe localized REST configuration, and the planner template override |
| `BAF\Core\Services\Travelpayouts_Widget_Registry_Service` | Stores approved Travelpayouts placement metadata, sanitizes private embed references, gates admin reads/writes by affiliate/settings capability, preserves malformed stored placements during normalization, exposes safe public placement metadata, and gives trusted server-side renderers active configured embed data |
| `BAF\Core\Admin\Widget_Placements_Page` | Renders the capability-gated Travelpayouts placement management screen and handles nonce-protected admin-post save/delete actions |
| `BAF\Core\Admin\Widget_Placement_Form` | Renders escaped placement form controls and sanitizes posted placement payloads before registry writes |
| `BAF\Core\Reports\Reporting_Service` | Builds capability-gated admin report summaries and CSV rows |
| `BAF\Core\Cron\Cron_Manager` | Registers, schedules, and unschedules core WP-Cron automation hooks |
| `BAF\Core\Jobs\Job_Repository` | Stores bounded non-secret background job status records in `baf_job_status` |
| `BAF\Core\Jobs\Job_Runner` | Handles safe background job execution, deferrals, failures, and cleanup |
| `BAF\Core\REST\Base_Controller` | Shared REST namespace, collection params, pagination headers, and collection links |
| `BAF\Core\REST\Request_Parameters` | Shared REST request validation and sanitization callbacks |
| `BAF\Core\REST\Permissions` | Shared REST permission callback helpers |
| `BAF\Core\REST\Travel_Entity_Controller` | Thin read-only public collection controller for destinations and routes |
| `BAF\Core\REST\Affiliate_Click_Controller` | Thin signed affiliate click handoff endpoint |
| `BAF\Core\REST\AI_Itinerary_Controller` | Protected `POST /ai/itinerary` endpoint requiring `run_baf_ai` |
| `BAF\Core\REST\AI_Handoff_Controller` | Protected `POST /ai/handoff` endpoint requiring `edit_baf_content` and a valid REST nonce |
| `BAF\Core\REST\Rest_Manager` | Registers core REST controllers on `rest_api_init` |

Repository list queries cap `posts_per_page` at 50 and use WordPress APIs rather than direct SQL. `Travel_Entity_Service::list_public()` is the Phase 3 public read boundary for published destinations and routes.

## Phase 6 AI Workflow

`POST /wp-json/baf/v1/ai/itinerary` is protected by `run_baf_ai`.

Request parameters:

- `destination`: required sanitized text, max 120 characters.
- `origin`: optional sanitized text.
- `prompt`: optional sanitized textarea, max 1200 characters. The frontend planner requires it before submitting the prompt-to-brief flow.
- `departure_date`: optional `YYYY-MM-DD` date string.
- `return_date`: optional `YYYY-MM-DD` date string.
- `days`: integer from `1` to `21`, default `3`.
- `travelers`: integer from `1` to `12`, default `2`.
- `travel_style`: optional sanitized text.
- `budget`: optional sanitized text.
- `preferences`: optional sanitized textarea.
- `source_post_id`: optional non-negative integer.
- `save`: optional boolean. When true, the validated itinerary is saved as a `draft` `trip_plan`; it is never published automatically.
- `external_ai_consent`: optional boolean. In live mode this must be true for the request before provider selection can occur.

Response behavior:

- Demo mode (`baf_ai_settings.mode = demo`) generates local deterministic itinerary drafts without external calls or credentials.
- Live mode requires `baf_ai_settings.provider`, `baf_ai_settings.api_key`, `baf_consent_settings.allow_external_ai`, and per-request `external_ai_consent`.
- Current live PHP adapter support is `openai`; unsupported configured providers fail safely without sending data externally.
- All AI outputs are validated by `BAF\Core\AI\Itinerary_Schema` before response or draft save.
- Responses include a sanitized `trip_brief` with destination, origin, dates, day count, traveler count, style, budget, mode, and whether data was sent externally. Raw prompt text is not returned in the planner response.
- When `save=true` succeeds, responses include `trip_plan_id`, `saved_status=draft`, and a `trip_plan` object with the draft ID, draft status, and an edit URL for users who can edit the post.
- Affiliate/provider tool opportunities use `opportunity_schema = travelpayouts_opportunity_v1` and are recommendations only: `status = not_executed`, `requires_approval = true`, `disclosure_required = true`, and `approval_state = requires_editor_approval`. Each opportunity may include `provider`, `vertical`, `recommendation_type`, `label`, `placement_context`, `destination`, `route`, `suggested_subid`, `confidence`, `limitations`, and fixed `blocked_actions`. The schema rejects unsupported provider IDs plus provider-owned booking, payment, price, availability, confirmation, link, and published-action claims; no provider search, link creation, booking, payment, or publishing action is executed by AI.
- Provider errors return safe WordPress errors and do not expose API keys, raw prompts, raw provider payloads, or secrets.
- `bf_ai_sessions` records hashed request/output metadata and sanitized summaries/errors only.

`POST /wp-json/baf/v1/ai/handoff` is protected by `edit_baf_content` and an explicit `X-WP-Nonce` REST nonce.

Handoff request parameters:

- `trip_plan_id`: required positive integer for the saved `trip_plan` source.
- `opportunity_index`: required non-negative integer into the validated `affiliate_opportunities` array.
- `action_type`: one of `placement_card`, `placement_draft`, `saved_trip_cta`, or `alert_cta`.
- `approved`: required boolean that must be true.
- `provider_request_consent`: required boolean that must be true for the specific handoff preparation.
- `approval_note`: optional sanitized textarea, max 500 characters.

Handoff response and storage behavior:

- The source post must be a `trip_plan` the current user can edit.
- The source `baf_itinerary_json` is decoded and revalidated through `Itinerary_Schema` before an opportunity is used.
- The selected opportunity must be a `travelpayouts` `not_executed` recommendation with approval and disclosure guardrails.
- Successful responses return `201` with `status=prepared`, `provider_action=not_executed`, `external_data_sent=false`, the prepared local intent, and stored intent count.
- The local intent is stored in `baf_ai_handoff_intents` and explicitly records `provider_action_executed=false` and `external_request_sent=false`.
- The handoff route does not send external provider requests, create live links, publish content, book trips, pay providers, store raw prompts, or store provider-owned live inventory.

## Admin Page Slugs

Implemented by `BAF\Core\Admin\Admin_Manager`:

- `baf-dashboard`
- `baf-settings`
- `baf-integrations`
- `baf-widget-placements`
- `baf-jobs`
- `baf-reports`

Planned slugs:

- `baf-affiliates`
- `baf-ai-planner`
- `baf-alerts`

Existing affiliate bridge settings page remains at `options-general.php?page=baf-affiliate-bridge`; Phase 2 integrates status links without renaming or duplicating the bridge page.

## Phase 8 Reporting

`admin.php?page=baf-reports` is protected by `view_baf_reports`.

Reports include:

- affiliate click totals, provider breakdowns, and top clicked content from `bf_clicks`;
- AI session totals, status breakdowns, and mode breakdowns from `bf_ai_sessions`;
- latest local provider status snapshots from `bf_provider_stats`;
- content inventory counts for registered Bookings and Flights CPTs;
- CSV export guarded by a WordPress nonce and `view_baf_reports`.

Reporting queries are bounded to approved date ranges (`7`, `30`, `90`, or `365` days) and limited result sets. Admin reports and exports minimize private data: they do not show raw IP addresses, user agents, referrers, raw prompts, API tokens, provider payloads, full target URLs, private customer data, or revenue/conversion identifiers. Revenue and conversion reporting displays a safe unavailable state until postback/provider conversion data exists.

## Asset Handles

Implemented handles:

- `baf-admin`
- `baf-frontend`
- `baf-ai-planner`

Planned handles:

- `baf-admin-settings`
- `baf-admin-reports`
- `baf-search`
- `baf-travel-cards`

Existing theme/plugin handles beginning with `bookings_and_flights-` must not be renamed without review.

## Cron Hooks

Implemented by `BAF\Core\Cron\Cron_Manager`:

| Hook | Recurrence | Purpose |
| --- | --- | --- |
| `baf_refresh_cached_offers` | `baf_ten_minutes` | Checks eligible cached offer/deal refresh work outside page render; defers provider work unless consent is enabled. |
| `baf_process_travel_alerts` | `hourly` | Checks eligible travel alert work outside page render. |
| `baf_sync_provider_stats` | `hourly` | Records safe provider configuration/status telemetry for future reporting. |
| `baf_cleanup_job_records` | `daily` | Removes old successful job records and stale job locks. |

Planned hooks:

- `baf_cleanup_ai_sessions`
- `baf_send_saved_trip_followups`
- `baf_send_saved_trip_followups`

## Action and Filter Prefixes

Use:

- Actions: `baf_*`
- Filters: `baf_*`
- Existing affiliate bridge namespace/prefixes must remain compatible.

Implemented filters:

- `baf_core_post_type_args`
- `baf_core_taxonomy_args`

Implemented actions:

- `baf_after_affiliate_click_logged`
- `baf_ai_handoff_prepared`

Planned examples:

- `baf_before_ai_itinerary_saved`
- `baf_travel_card_data`
- `baf_provider_registry`

## Shortcodes

Implemented shortcodes:

- `[baf_travel_cards]`
- `[baf_affiliate_disclosure]`
- `[baf_travelpayouts_white_label]`
- `[baf_travelpayouts_hotel_widget]`
- `[baf_travelpayouts_widget]`

Planned shortcodes:

- `[baf_search]`
- `[baf_ai_planner]`

## Blocks

Planned block names:

- `baf/search`
- `baf/ai-planner`
- `baf/travel-card`
- `baf/destination-guide`
- `baf/route-search`
- `baf/affiliate-disclosure`
- `baf/travelpayouts-widget`

## Planned Architecture Deepening

These are planning labels from the 2026-05-09 architecture review, not final class or Interface names until a phase implementation updates this baseline with concrete contracts.

- Travelpayouts placement registry: one deep module for approved placement metadata, consent, disclosure, SubID generation, safe embed output, missing-configuration states, and official-plugin-versus-fallback Adapter selection.
- Search surface mode seam: one place for choosing Travelpayouts widget, White Label page, safe redirect metadata, or an explicitly approved `platform/` Adapter without exposing custom live inventory by default.
- Brand continuity module: one WordPress-owned source for logo, favicon, brand name, colors, primary nav, footer links, disclosure copy, and White Label heading copy.
- AI opportunity handoff: one approval-oriented module that turns validated `not_executed` AI opportunities into approved Travelpayouts placement drafts only after consent and capability checks.
- Content manager field pipeline: P17.5 adds tracked core editor meta boxes for `destination`, `route`, and `travel_deal` guide-module fields so editors no longer need raw `baf_*` custom-field keys for Phase 17 page families. The legacy local `bookings-and-flights-content-manager` plugin still needs a split along field rendering, field persistence, media portability, schema export/import, and admin notice seams before it becomes the tracked field system for large CPT expansion.
- Accessibility, SEO, and disclosure gate: P17.6 confirms Phase 17 public content surfaces remain WordPress-owned SEO/editorial pages with visible affiliate/provider boundaries, 44px minimum visible interactive targets on reviewed controls, labelled provider iframes where the wrapper can safely apply labels, and noindex/canonical behavior for transient Flights and Hotels query URLs. This does not create new provider contracts, result storage, booking, payment, or custom inventory ownership.
- Phase 17 final gate: P17.7 closes the destination, route, deal, taxonomy, and editor field baseline for Phase 18. WordPress owns editable SEO/editorial content and internal links; Travelpayouts or partner providers own live search results, filters, fares, hotel availability, booking, payment, changes, and support.
- Phase 18 AI planner entry: P18.1 adds the real `/trip-planner/` route and prompt-to-trip-brief flow while keeping AI opportunities recommendation-only and blocking live provider requests without saved plus per-request consent.

## Provider Interfaces

Planned PHP service contracts:

- `BAF\Core\Providers\Provider_Interface`
- `BAF\Core\Providers\Affiliate_Link_Provider_Interface`
- `BAF\Core\AI\AI_Provider_Interface`
- `BAF\Core\Repositories\Repository_Interface`

Planned AI tool names:

- `searchFlights`
- `searchHotels`
- `searchActivities`
- `createAffiliateLink`
- `getDestinationGuide`
- `buildItinerary`
- `estimateBudget`
- `saveTrip`
- `createPriceAlert`
- `recommendInsurance`
- `recommendTransport`
- `generateSeoDraft`

## Existing Integration Boundary

`platform/` currently contains:

- Next.js visitor-facing app on port `3000`
- Fastify search API on port `4050` by default, from `platform/services/search-api/src/config/env.ts` and `.env.example`.
- `platform/README.md` still says `npm run dev` starts the search API on `:4000`; this is stale documentation and should be corrected before or during platform integration work.
- Shared TypeScript package `@baf/shared`
- Search adapters for Travelpayouts, Hotellook, Booking Demand, activities, and cars.

WordPress remains optional in the current platform README, but the project direction for this setup treats WordPress as the system of record for the WordPress-native product.

## Research Consulted

- WordPress Plugin Developer Handbook.
- WordPress Plugin Security Handbook.
- WordPress REST API Handbook.
- WordPress Plugin Activation and Deactivation Hooks documentation.
- AI SDK documentation.
- Existing project plan and workspace files.
