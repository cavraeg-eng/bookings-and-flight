# Phase 12.5 Page-Level Wireframes For Core Surfaces

Status: `Completed`

Linear issue: `ONE-78`

Date: 2026-05-12

## Research Consulted

- WordPress Theme Handbook: [Template Hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/) for page, archive, single, search, and front-page template ownership.
- WordPress Theme Handbook: [Templates](https://developer.wordpress.org/themes/templates/templates/) for template organization and the relationship between theme templates, database-saved templates, and reusable template parts.
- WordPress Theme Handbook: [Theme Structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/) for keeping reusable parts, patterns, and assets in predictable theme folders.
- WordPress Theme Handbook: [Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/) for loading page or shared assets through WordPress enqueue APIs instead of hard-coded tags.
- Travelpayouts Help Center: [Getting started with widgets](https://support.travelpayouts.com/hc/en-us/articles/360031977872-Getting-started-with-widgets) for adaptive versus fixed-size widgets, SubID tracking, and provider-owned widget setup.
- Travelpayouts Help Center: [Setting up a White Label with Widget type](https://support.travelpayouts.com/hc/en-us/articles/26857907357458-Setting-up-a-White-Label-with-Widget-type) for embedded White Label search/results behavior and brand-controlled widget setup.

## Inputs Reviewed

- Phase 12 requirements in `.plan/phased-implementation.md`.
- Phase 12.1 sitemap and ownership map in `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- Phase 12.2 design-system and component inventory in `.plan/phase-12-design-system-component-inventory.md`.
- Phase 12.3 CSS split and theme architecture rules in `.plan/phase-12-css-split-theme-architecture.md`.
- Phase 12.4 widget-frame layout rules in `.plan/phase-12-widget-frame-layout-rules.md`.
- Travelpayouts-first product blueprint in `.plan/travelpayouts-wordpress-booking-site-blueprint.md`.
- Current static theme templates in `themes/bookings-and-flights-static/`.
- Current `destination`, `route`, `travel_deal`, `trip_plan`, `travel_partner`, and `travel_alert` ownership in `plugins/bookings-flights-core/`.

## Global Wireframe Rules

- Keep the WordPress header, primary navigation, footer, affiliate disclosure, and trust/legal links visible around Travelpayouts-owned widgets and handoffs.
- Put monetized widgets and partner links inside reserved widget frames from `.plan/phase-12-widget-frame-layout-rules.md`.
- Every monetized surface needs affiliate disclosure before or immediately adjacent to the first monetized action.
- Primary search and handoff actions must use real labels, visible focus states, and touch targets at least 44px high.
- WordPress-owned filters are editorial until a Travelpayouts widget explicitly supports live filtering. Do not imply WordPress filters live supplier inventory.
- Use real travel media on public discovery pages. Gradients may support contrast but must not be the primary visual asset.
- Empty and missing-content states must be part of the layout, especially because local `destination`, `route`, and `travel_deal` content can be absent.
- Templates should call a future placement wrapper or registry rather than raw provider scripts once Phase 13 exists.
- Search/result routes may return shell, placement, disclosure, consent, SubID, or handoff metadata only. They must not become live inventory APIs.

## Responsive Model

| Viewport | Layout rule | Search behavior | Navigation and disclosure |
| --- | --- | --- | --- |
| Mobile, up to 767px | One-column page flow, sticky or near-top primary action only when it does not hide disclosure. | Vertical search fields, segmented vertical selector, one primary CTA per section. | Header remains compact; disclosures remain visible above widgets and are not hidden in accordions. |
| Tablet, 768px to 1023px | Two-column only for low-risk editorial sections; widgets stay full-width unless provider frame is validated. | Search panel can use two-column field groups. | Secondary CTAs can sit beside primary CTAs when labels fit. |
| Desktop, 1024px and up | Dense product layout with controlled sidebars, comparison bands, and editorial rails. | Search can use horizontal groups and compact tabs. | Disclosures sit above widget frames or beside compact widget headings, never below long result frames only. |

## Surface Coverage Matrix

| Surface | WordPress owner | Future template target | Primary phase mapping | Travelpayouts or partner surface |
| --- | --- | --- | --- | --- |
| Home | Static front page | `front-page.php` or revised `page-home.php` | Phase 14 | Unified widget/White Label entry plus partner handoff modules |
| Flights | WordPress page | `page-flights.php` | Phase 15 | White Label Widget, flight form, low-price calendar, route map |
| Hotels | WordPress page | `page-hotels.php` | Phase 16 | Trip.com or approved hotel widget, hotel map/list, handoff link |
| Explore | WordPress page | `page-explore.php` | Phase 14 and Phase 17 | Popular destination widgets, route maps, editorial cards |
| Destination index | CPT archive | `archive-destination.php` | Phase 17 | Contextual destination widgets and partner links |
| Destination detail | CPT single | `single-destination.php` | Phase 17 | Destination flight, hotel, activities, and itinerary handoffs |
| Route index | CPT archive | `archive-route.php` | Phase 15 | Popular routes, low-price widgets, alert CTA |
| Route detail | CPT single | `single-route.php` | Phase 15 | Route-specific flight widget, White Label link, price alert |
| Deals | WordPress hub plus CPT archive | `page-deals.php` and later `archive-travel_deal.php` | Phase 17 and Phase 19 | Partner link/card widgets with SubIDs |
| AI Trip Planner | WordPress page or app-like shortcode/block | `page-trip-planner.php` | Phase 18 | Approved Travelpayouts placement cards from AI output |
| Saved Trips | WordPress private member page | `page-saved-trips.php` | Phase 19 | Fresh handoff widgets from saved local intent |
| About/legal | WordPress pages | Existing page templates plus legal pages | Trust/support pass | Disclosure only, no live search inventory |
| Admin widget placement | Core admin screen | `bookings-flights-core` admin module | Phase 13 | Registry preview, SubID, consent, disclosure, status |

## Home Wireframe

Desktop structure:

```text
Header and primary nav
Full-bleed real travel media hero
  Compact product value copy
  Unified search panel with Flights and Hotels first
  Affiliate disclosure directly above search action
  Secondary AI planning prompt
Trending destinations and routes band
Flexible dates and cheap-month module
Hotel city discovery module
Explore-anywhere module
Price alert CTA
AI itinerary teaser
Trust/support band
Footer with legal and disclosure links
```

Mobile structure:

```text
Compact header
Hero image crop with product title
Vertical search panel
Affiliate disclosure
Primary search CTA
AI prompt entry
Stacked discovery modules
Trust/support band
Footer
```

Widget/disclosure/CTA placement:

- Search panel uses future Phase 13 placement data and can route to the White Label Widget, hotel widget, or safe handoff depending on selected vertical.
- Disclosure appears above the search CTA and stays visible before any provider content.
- Primary CTA copy should be search-oriented, such as `Search flights` or `Search hotels`, not generic `Get Started`.

Follow-up phase mapping:

- Phase 14 implements the visual homepage rebuild.
- Phase 13 supplies widget registry placement data before raw embeds are reused broadly.

## Flights Wireframe

Desktop structure:

```text
Header
Flight search hero
  Origin, destination, dates, travelers, cabin, direct, flexible dates
  Disclosure and White Label handoff clarity
White Label search/results widget frame
Low-price calendar frame
Popular routes or route map frame
Price alert CTA
Editorial route/destination guidance
Footer
```

Mobile structure:

```text
Header
Flight search title
Stacked flight fields
Disclosure
Search CTA
Reserved White Label frame
Calendar and popular routes stacked
Price alert CTA
Footer
```

Widget/disclosure/CTA placement:

- Use the White Label search plus results frame when the user should stay inside the WordPress shell.
- Low-price calendar and route-map frames use Phase 12.4 dimensions and must not cause horizontal page overflow.
- Price alert CTA saves local alert intent only; it must not claim to book or guarantee fares.

Follow-up phase mapping:

- Phase 15 implements the flight experience, route modules, and White Label continuity checks.

## Hotels Wireframe

Desktop structure:

```text
Header
Hotel search hero
  Destination, dates, guests, rooms
  Disclosure and external booking language
Hotel search widget frame
Map or selections widget frame when configured
Editorial hotel guide cards
City/neighborhood discovery modules
Partner handoff fallback
Footer
```

Mobile structure:

```text
Header
Hotel search title
Stacked destination/date/guest controls
Disclosure
Reserved hotel widget frame
Fallback partner handoff button
Editorial stay guides
Footer
```

Widget/disclosure/CTA placement:

- Trip.com or another approved Hotels & Accommodation widget owns hotel search/results behavior.
- WordPress-owned budget, family, luxury, neighborhood, and amenity chips are editorial guide filters unless supported by the provider widget.
- The fallback handoff button remains visible outside iframes on mobile.

Follow-up phase mapping:

- Phase 16 implements hotel and stays surfaces after Phase 13 provides governed placement metadata.

## Explore Wireframe

Desktop structure:

```text
Header
Explore hero with real destination media
Flexible destination finder
  Budget, trip style, season, origin, duration
Destination idea grid
Route idea grid
Travelpayouts popular destination or route widgets
AI planner CTA
Footer
```

Mobile structure:

```text
Header
Explore title
Stacked intent filters
Destination cards
Route cards
AI planner CTA
Footer
```

Widget/disclosure/CTA placement:

- Widgets appear after editorial context so users understand whether they are browsing ideas or entering a monetized provider flow.
- Every partner card cluster needs a compact disclosure above the first CTA.

Follow-up phase mapping:

- Phase 14 can add homepage/explore teaser modules.
- Phase 17 owns SEO destination and route content depth.

## Destination Detail Wireframe

Desktop structure:

```text
Header
Destination hero image, title, facts, best time to visit
Quick planning rail
  Flight widget or handoff
  Hotel widget or handoff
  AI itinerary CTA
Editorial guide sections
Neighborhood/stay recommendations
Activities and partner links
Related routes and nearby destinations
Footer
```

Mobile structure:

```text
Header
Destination hero
Planning CTAs
Disclosure
Flight and hotel widget frames stacked
Guide content
Related cards
Footer
```

Widget/disclosure/CTA placement:

- Disclosure appears before the first flight, hotel, activity, or partner CTA.
- Destination-specific widgets must use destination-scoped SubIDs without private user data.
- Empty local destination content gets a helpful editorial empty state, not fake destination facts.

Follow-up phase mapping:

- Phase 17 implements destination archive/single templates and SEO families.

## Route Detail Wireframe

Desktop structure:

```text
Header
Route summary hero
  Origin, destination, airport labels, travel time notes
Route-specific flight widget or White Label entry
Low-price calendar
Flexible date guidance
Price alert CTA
Destination hotel/activity module
Related routes
Footer
```

Mobile structure:

```text
Header
Route title
Disclosure
Flight widget frame
Calendar frame
Price alert CTA
Destination modules
Footer
```

Widget/disclosure/CTA placement:

- Route widgets use route-specific placement keys and SubIDs.
- Flexible-date guidance must be labeled editorial guidance unless it comes from a provider widget or validated trend source.
- Price alert CTA stores local alert intent and leads back to a Travelpayouts handoff when clicked.

Follow-up phase mapping:

- Phase 15 owns route templates, route modules, price alerts, and flight handoff behavior.

## Deals Wireframe

Desktop structure:

```text
Header
Deals hub hero
Deal filters by vertical, destination, season, style
Editorial deal cards
Partner link or compact widget frames
Seasonal landing sections
Disclosure and trust band
Footer
```

Mobile structure:

```text
Header
Deals title
Scrollable but contained filter chips
Deal cards
Disclosure before first monetized CTA
Footer
```

Widget/disclosure/CTA placement:

- Deal cards may use partner links, compact affiliate card widgets, or future Travelpayouts placements.
- Cards must not show fake live prices or urgency unless the source and freshness are documented.
- `/deals/` is the consumer hub; `travel-deals` CPT archive remains a separate contract until a rewrite decision changes it.

Follow-up phase mapping:

- Phase 17 handles SEO/content modules.
- Phase 19 handles analytics, SubID reporting, and release readiness for deal handoffs.

## AI Trip Planner Wireframe

Desktop structure:

```text
Header
Planner workspace
  Natural language prompt
  Trip brief fields
  Consent and provider-use messaging
AI-generated itinerary draft
Editable day cards
Approved affiliate opportunity cards
Save trip and alert CTAs
Footer
```

Mobile structure:

```text
Header
Prompt input
Trip brief fields
Consent messaging
Draft itinerary cards
Approved handoff CTAs
Footer
```

Widget/disclosure/CTA placement:

- AI can recommend Travelpayouts widgets/cards, but the user or editor approves handoff actions.
- Affiliate opportunity cards include disclosure before monetized CTAs.
- No AI state may auto-book, auto-publish, or claim live availability.

Follow-up phase mapping:

- Phase 18 implements AI planner conversion and approved handoff flow.

## Saved Trips Wireframe

Desktop structure:

```text
Header
Private saved-trip dashboard
  Saved trip cards
  Alert status
  Fresh search/handoff actions
  Empty state for anonymous or new users
Trip detail drawer or page
Footer
```

Mobile structure:

```text
Header
Saved trips title
Auth or empty state
Saved trip cards
Fresh handoff CTAs
Footer
```

Widget/disclosure/CTA placement:

- Saved trips store local intent and content references, not partner booking records.
- Stale handoff states should prompt a fresh Travelpayouts search rather than showing outdated offers.
- Logged-out users get a clear auth or save-later prompt without exposing private trip data.

Follow-up phase mapping:

- Phase 19 implements saved trips, alerts, analytics, and release readiness.

## About And Legal Wireframes

Desktop structure:

```text
Header
Trust page title
Plain-language product explanation
Affiliate disclosure and booking handoff explanation
Support/contact path
Privacy, terms, and accessibility links
Footer
```

Mobile structure:

```text
Header
Trust/legal title
Readable single-column copy
Disclosure
Support link
Footer
```

Widget/disclosure/CTA placement:

- No live inventory widgets are needed.
- Affiliate disclosure and external booking language should be explicit and easy to find.
- Legal pages must not be crowded by product search CTAs.

Follow-up phase mapping:

- Trust/support content pass can update existing `page-about.php`, `page-contact.php`, and `page-legal.php`.

## Admin Widget Placement Wireframe

Desktop structure:

```text
Bookings and Flights admin page
Registry status summary
Placement table
  Placement key, surface, provider, status, consent, disclosure, SubID
Preview panel
  Public frame state, missing config, no-script, fallback link
Actions
  Save, disable, copy shortcode/block, validate
```

Mobile structure:

```text
Admin status cards
Stacked placement rows with data labels
Preview panel
Primary save/validate actions
```

Widget/disclosure/CTA placement:

- Administrators see exact missing fields and validation status.
- Public visitors see only neutral unavailable states or hidden widgets when configuration is missing.
- Raw widget code and provider credentials remain capability-gated and never appear in public output.

Follow-up phase mapping:

- Phase 13 implements the registry, wrapper API, SubID builder, consent checks, disclosure preview, and safe render states.

## Browser Screenshot Plan For Later UI Implementation

When the visual templates are implemented, capture and compare screenshots at these minimum sizes:

| Viewport | Pages | Checks |
| --- | --- | --- |
| 1440 x 1000 desktop | `/`, `/flights/`, `/hotels/`, `/explore/`, `/deals/`, `/trip-planner/`, `/saved-trips/` | First viewport shows real product intent, nav fits, disclosures visible, widget frames reserve space. |
| 1024 x 900 tablet | Same public pages plus destination and route singles with seeded content | Search layout does not overlap; cards and widget frames remain stable. |
| 390 x 844 mobile | Same public pages | No horizontal overflow; touch targets fit; disclosures precede monetized actions; fallback handoff remains reachable. |
| Admin mobile and desktop | Widget placement screen after Phase 13 | Capability-gated fields, table/card responsive behavior, missing-config states, and preview states are readable. |

Seed content needed before full screenshot validation:

- One destination post with image, region, travel style, season, and affiliate vertical metadata.
- One route post with origin, destination, airport codes, and season metadata.
- One travel deal post or documented empty state.
- One AI trip plan draft and one saved-trip empty state.

## Validation Performed

- Reviewed official WordPress template hierarchy, templates, theme structure, asset loading, and Travelpayouts widget documentation.
- Reviewed Phase 12.1 through Phase 12.4 outputs and current Phase 12 acceptance criteria.
- Reviewed current static theme templates, header/footer shell, generic homepage, fallback `index.php`, and published Flights/Hotels page ownership.
- Reviewed current CPT and shortcode ownership in `bookings-flights-core`.
- Cross-checked each requested core surface against the blueprint and Phase 12 sitemap.
- Performed responsive wireframe review for mobile, tablet, and desktop behavior.
- Documented browser screenshot plan and seed-content prerequisites for later UI implementation.

## P12.5 Result

Implementation-ready structured wireframes now exist for home, flights, hotels, explore, destination detail, route detail, deals, AI planner, saved trips, about/legal, and admin widget placement surfaces. Template ownership, Travelpayouts placement rules, disclosure/CTA placement, responsive behavior, follow-up phase mapping, and later screenshot validation are documented before Phase 13 and public visual implementation begin.

Codex PR review on PR #13 found no major issues.
