# Phase 12.1 Sitemap, Navigation, and Page Ownership

Status: `Completed`

Linear issue: `ONE-74`

Date: 2026-05-12

## Research Consulted

- WordPress Theme Handbook: Template Hierarchy, front page, page, archive, and single template resolution.
- WordPress Theme Handbook: Navigation Menus, registered menu locations, and `wp_nav_menu()` output.
- WordPress Block Editor Handbook: blocks as modular editable content stored in WordPress.
- WordPress Theme Handbook: Including CSS and JavaScript with enqueued, scoped assets.
- WordPress Theme Handbook: Accessibility requirements and designing accessible themes from the start.
- Travelpayouts Help Center: Getting started with widgets, including adaptive versus fixed-size widgets and SubID tracking.
- Travelpayouts Help Center: White Label Web and Widget-type setup, including branded on-site search/results behavior, booking handoff, and crawler limitations.

## Inputs Reviewed

- Phase 11 backend mode decision in `.plan/decisions.md`.
- Phase 11 validation evidence in `.plan/validation-baseline.md`.
- Phase 12 requirements in `.plan/phased-implementation.md`.
- Local blueprint source `.plan/travelpayouts-wordpress-booking-site-blueprint.md`, especially Primary Site Map, White Label Strategy, Design System Requirements, and Phase 12.
- Current static theme templates and assets under `themes/bookings-and-flights-static/`.
- Current WordPress pages, assigned front page, primary menu, and published Flight/Hotel shortcode content.
- Current core CPTs, public archives, and frontend shortcodes in `plugins/bookings-flights-core/`.

## Backend Boundary

Phase 12 must preserve the Phase 11 backend mode:

- WordPress owns the branded shell, sitemap, navigation, page templates, editable page content, CPT archives/singles, SEO landing pages, consent state, saved-trip intent, alert intent, affiliate disclosures, and local governance.
- Travelpayouts owns live flight and hotel search/results, widgets, White Label result behavior, partner handoff, booking/payment, supplier reservation changes, payout source of truth, and affiliate reporting.
- `platform/` remains an optional integration seam. It is not the canonical flight/hotel backend unless a new architecture decision changes that boundary.
- Planned `/search/flights` and `/search/hotels` routes may expose safe shell, placement, consent, disclosure, SubID, or handoff metadata only. They must not proxy or persist canonical supplier inventory.

## Primary Navigation Target

The public product navigation target is:

| Position | Label | Target | Owner | Notes |
| --- | --- | --- | --- | --- |
| Logo | Bookings and Flights | `/` | WordPress static front page | Always routes back to the WordPress-owned shell. |
| 1 | Flights | `/flights/` | WordPress page with Travelpayouts White Label Widget shortcode | Primary flight search/results entry. |
| 2 | Hotels | `/hotels/` | WordPress page with Travelpayouts/Trip.com hotel widget wrapper | Hotel search and partner handoff entry. |
| 3 | Explore | `/explore/` | Planned WordPress page | Flexible destination discovery and destination/route entry point. |
| 4 | Deals | `/deals/` | Planned WordPress page backed by `travel_deal` content | Curated deal hub; do not rename the existing `travel-deals` CPT archive without a separate decision. |
| 5 | Trip Planner | `/trip-planner/` | Planned WordPress page plus AI planning workflow | AI may draft plans and approved handoffs; it must not book or publish automatically. |
| 6 | Saved Trips | `/saved-trips/` | Planned WordPress page plus private saved intent | Member retention surface backed by local saved intent and fresh Travelpayouts handoff. |

Footer/support navigation should retain About, Contact or Support, Privacy Policy, Terms & Conditions, affiliate disclosure, destination index links, and route index links. These are important trust and SEO links, but they should not crowd the primary product navigation.

## Page Ownership Map

| Surface | URL / Rewrite | WordPress owner | Data source | Travelpayouts surface | Current state | Next implementation owner |
| --- | --- | --- | --- | --- | --- | --- |
| Home | `/` | Static front page using `page-home.php` | Content manager fields and future block/editor content | Flight/hotel search widgets or registry placements | Published front page ID `246`; template is still generic boilerplate. | Phase 14 homepage rebuild. |
| Flights | `/flights/` | WordPress page content rendered through `index.php` until a dedicated template exists | Published page ID `261`; shortcode `[baf_travelpayouts_white_label]` | White Label Widget type with `tpwl-search` and `tpwl-tickets` | Published and browser-validated in Phase 11. | Phase 12 wireframe, Phase 13 registry, Phase 15 flight experience. |
| Hotels | `/hotels/` | WordPress page content rendered through `index.php` until a dedicated template exists | Published page ID `262`; shortcode `[baf_travelpayouts_hotel_widget]` | Trip.com or approved Hotels & Accommodation widget/link wrapper | Published and browser-validated in Phase 11 with a visible handoff fallback. | Phase 12 wireframe, Phase 13 registry, Phase 16 hotel experience. |
| Explore | `/explore/` | Planned WordPress page | Editorial modules, destination CPT queries, route CPT queries, approved widgets | Popular destination widgets, route maps, contextual links | Missing from current pages and menu. | Phase 12.5 wireframes and Phase 14/15/16 content modules. |
| Destinations index | `/destinations/` | `destination` CPT archive | Public destination posts and taxonomies | Contextual flight/hotel/activity widgets by destination | CPT registered; no destination posts currently exist. | Phase 14/15/16 SEO and module work. |
| Destination detail | `/destinations/{slug}/` | `destination` CPT single | Destination post, excerpt, thumbnail, taxonomies, post meta | Destination-specific widgets/links and disclosures | Template falls back through theme hierarchy; no dedicated template yet. | Phase 12.5 wireframe, later destination template implementation. |
| Routes index | `/routes/` | `route` CPT archive | Public route posts and taxonomies | Route widgets, low-price calendars, alerts, White Label handoff | CPT registered; no route posts currently exist. | Phase 15 flight route work. |
| Route detail | `/routes/{slug}/` | `route` CPT single | Route post, origin/destination meta, taxonomies | Route-specific flight widgets, alert CTA, White Label link | Template falls back through theme hierarchy; no dedicated template yet. | Phase 12.5 wireframe and Phase 15 route implementation. |
| Deals hub | `/deals/` | Planned WordPress page | Editorial modules and selected `travel_deal` posts | Partner links/widgets with SubIDs and disclosures | Missing from current pages and menu. | Phase 12.5 wireframe, Phase 17/19 deal lifecycle. |
| Deals archive | `/travel-deals/` | `travel_deal` CPT archive | Public deal posts | Contextual partner widgets/links | CPT registered; no deal posts currently exist. | Later deal/content phase; keep slug unless documented. |
| Trip Planner | `/trip-planner/` | Planned WordPress page or block/shortcode surface | AI itinerary service, draft `trip_plan` records, editor-approved output | Approved Travelpayouts cards/widgets from generated or saved intent | Missing from current pages and menu. | Phase 18 AI opportunity handoff. |
| Saved Trips | `/saved-trips/` | Planned WordPress page | User meta and private `trip_plan` records | Fresh Travelpayouts handoff for saved intent | Missing from current pages and menu. | Phase 19 saved trips. |
| Alerts | `/alerts/` or route-level modules | Planned WordPress page/module | Alert intent and `travel_alert` records | Handoff only when a user clicks an approved offer/search | CPT is private; public page is not created. | Phase 19 alerts. |
| About | `/about/` | WordPress page with `page-about.php` | Editable page content/template fields | No inventory surface | Published but template body is effectively empty. | Trust/support content pass. |
| Contact / Support | `/contact/` or `/support/` | WordPress page with `page-contact.php` | Editable page content/template fields | No inventory surface | Published Contact page exists; Support does not. | Trust/support content pass. |
| Services | `/services/` | Legacy WordPress page with `page-services.php` | Boilerplate page content/template | No product-specific surface | Published, but conflicts with travel product IA. | Retire, redirect, or repurpose after content review. |
| Legal | `/privacy-policy/`, `/terms-and-conditions/` | WordPress pages with `page-legal.php` | Editable legal content | Affiliate disclosure only; no search inventory | Published. | Keep in footer/trust surfaces. |

## SEO Page Families

| Family | Canonical WordPress surface | Data model | Search/handoff rule | Notes |
| --- | --- | --- | --- | --- |
| Flights | `/flights/`, `/routes/`, `/routes/{slug}/` | WordPress pages plus `route` CPT | Use Travelpayouts White Label Widget, official flight widgets where compatible, and safe handoff metadata only. | Route pages can target origin-destination intent without storing live supplier inventory. |
| Hotels | `/hotels/`, future `/hotels/{city}/` or destination hotel sections | WordPress pages and `destination` CPT sections | Use Travelpayouts Hotels & Accommodation widgets/links such as Trip.com; keep a visible sponsored handoff fallback. | Do not promise Booking.com fares inside White Label. |
| Destinations | `/destinations/`, `/destinations/{slug}/`, travel region/style/season taxonomies | `destination` CPT and taxonomies | Add contextual flight, hotel, activity, and route placements through the future registry. | These pages should remain crawlable WordPress pages because Travelpayouts notes White Label crawler limits. |
| Routes | `/routes/`, `/routes/{slug}/` | `route` CPT and taxonomies | Route-specific flight widgets, alerts, and White Label handoff. | Use `baf_origin_*` and `baf_destination_*` meta; do not overload the reserved `destination` query var. |
| Deals | `/deals/`, `/travel-deals/`, seasonal/editorial landing pages | `travel_deal` CPT and taxonomies | Partner links/widgets with SubIDs and disclosures. | `/deals/` should be the consumer-friendly hub; `/travel-deals/` can remain the CPT archive unless a future rewrite decision changes it. |

## Current Conflicts To Resolve Before Visual Build

- Primary menu currently contains Home, Flights, Hotels, About, Contact, Services, Privacy Policy, and Terms & Conditions. It is missing Explore, Deals, Trip Planner, and Saved Trips.
- The theme fallback menu in `functions.php` still outputs Home, About, Services, and Contact. If a menu is unassigned, the site falls back to old boilerplate navigation.
- Header and mobile CTA text still says "Get Started" and points to `/contact/`, which does not match the travel search product path.
- Home template is still a generic hero/features/about/testimonials/CTA layout and does not yet signal flights and hotels in the first viewport.
- About, Contact, and Services templates render empty main bodies aside from comments.
- Flights and Hotels are currently generic WordPress pages rendered through `index.php`, not dedicated search templates.
- No `destination`, `route`, or `travel_deal` content exists locally, so archive/single template work needs seed content or empty-state requirements.
- `header.css` is 544 lines, close to the 600-line project limit. Phase 12.3 should split or protect this file before adding substantial navigation/search shell styles.
- Trip.com hotel content is provider-owned and may compress or be blocked inside the iframe on some browsers; the visible handoff button remains required.

## Validation Performed

- Reviewed current tracked `.plan/` files and the local blueprint source.
- Reviewed current static theme templates, CSS assets, and theme architecture.
- Reviewed current core CPT registrations, public archive slugs, and frontend shortcode registrations.
- Used WP-CLI to confirm published pages, front-page setting, primary menu assignment, menu items, and Flight/Hotel page shortcode content.
- Used WP-CLI to confirm there are currently no local `destination`, `route`, `travel_deal`, `trip_plan`, `travel_partner`, or `travel_alert` posts.
- Reviewed CSS/PHP file sizes and confirmed no currently tracked theme source file exceeds 600 lines.

WP-CLI emitted the known PHP 8.5 deprecation warning from the WP-CLI dependency and the Travelpayouts plugin, but the inventory commands completed successfully.

## P12.1 Result

The sitemap, primary navigation target, owner/data-source model, SEO page families, and current-template conflicts are now documented. PR #9 passed Codex review with no major issues. Visual/template implementation can proceed only after the follow-on Phase 12 tickets complete their design-token, component, CSS split, widget-frame, and wireframe gates.
