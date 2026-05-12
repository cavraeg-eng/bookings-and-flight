# Phase 12.2 Design Tokens, Media Strategy, and Component Inventory

Status: `Completed`

Linear issue: `ONE-75`

Date: 2026-05-12

## Research Consulted

- WordPress Theme Handbook: [Global Settings and Styles](https://developer.wordpress.org/themes/core-concepts/global-settings-and-styles/) for theme-level color, typography, spacing, and editor-aligned style configuration.
- WordPress Theme Handbook: [Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/) for scoped CSS, JavaScript, image, and font loading through WordPress APIs.
- WordPress Theme Handbook: [Accessibility](https://developer.wordpress.org/themes/classic-themes/functionality/accessibility/) for keyboard, contrast, resizing, image alt text, and mobile usability expectations.
- WordPress Theme Handbook: [Images](https://developer.wordpress.org/themes/classic-themes/functionality/media/images/) for WordPress media-library image rendering and generated responsive image markup.
- Travelpayouts Help Center: [Getting started with widgets](https://support.travelpayouts.com/hc/en-us/articles/360031977872-Getting-started-with-widgets) for widget types, customization, SubID usage, and adaptive versus fixed-size widgets.
- Travelpayouts Help Center: [Setting up a White Label with Widget type](https://support.travelpayouts.com/hc/en-us/articles/26857907357458-Setting-up-a-White-Label-with-Widget-type) for branded widget configuration, search/results placement, and White Label styling limits.
- Travelpayouts Help Center: [What is White Label Web by Travelpayouts?](https://support.travelpayouts.com/hc/en-us/articles/203955753-What-is-White-Label-Web-by-Travelpayouts) for Widget versus Page type ownership, booking handoff, and partner-site completion.

## Inputs Reviewed

- Phase 12.1 IA map in `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- Phase 12 requirements in `.plan/phased-implementation.md`.
- Product blueprint design-system requirements in `.plan/travelpayouts-wordpress-booking-site-blueprint.md`.
- Current static theme architecture, templates, JavaScript, and CSS in `themes/bookings-and-flights-static/`.
- Current core frontend styles and Travelpayouts shortcode wrappers in `plugins/bookings-flights-core/assets/css/frontend.css`.
- Current Phase 11 backend mode, consent, White Label, Trip.com hotel widget, and disclosure contracts in `.plan/architecture-baseline.md`, `.plan/decisions.md`, and `.plan/regression-watchlist.md`.

## Current Design Inventory

| Area | Current state | Phase 12.2 direction |
| --- | --- | --- |
| Theme token source | `tokens.css` defines primary, secondary, accent, neutral, semantic, spacing, radius, shadow, duration, font, grid, and z-index tokens. | Keep it as the implementation source for now, but map future work to clearer travel-product aliases before large CSS additions. |
| Palette | Primary is dark blue, secondary is muted blue, accent is tan/gold, with broad neutral ramps. Current public pages can read as cool blue/slate plus tan. | Preserve blue as the trust color, but add water, deal-green, coral/sun, and warm off-white roles so the product is not a one-note palette. |
| Typography | Inter and JetBrains Mono are loaded as theme fonts. Type and spacing scales use fluid `clamp()` values. | Use compact product typography for search controls and cards. Reserve large display type for true page heroes. Avoid new viewport-width font scaling in control-heavy modules. |
| Radius and cards | Theme radius tokens include large rounded values; plugin cards currently use 16px radius. | New product cards, panels, and widget frames should stay at 8px or less unless the component is a functional chip/pill or legacy surface being preserved. |
| Motion | `base.css` respects `prefers-reduced-motion`; `header.js` and `reveal.js` include accessible menu and animation behavior. | Keep motion subtle and functional. No search, widget, or booking-adjacent state may rely on animation alone. |
| Focus | `base.css` defines a global `:focus-visible`; plugin CTAs add explicit focus outlines. | Every tab, chip, picker, date control, widget handoff, and admin placement action needs a visible focus state with adequate contrast. |
| Media | Theme images directory is effectively empty. Home currently supports an image background but falls back to a gradient hero. | Use real travel media as the first-viewport signal. Gradients may support contrast, but cannot be the main visual asset for travel discovery pages. |
| Existing home layout | `home.css` is a generic hero, feature-card, about, testimonial, and CTA page. | Future home work should become a dense search and discovery surface, not a marketing-card stack. |
| Existing widget wrappers | White Label and Trip.com hotel wrappers exist in core frontend CSS. Hotel iframe is intentionally cropped to show the provider search control and includes a visible handoff fallback. | Phase 12.4 must deepen widget frame rules. Until then, reserve dimensions, keep disclosure and handoff visible, and do not hide provider-owned limitations with custom UI. |
| Admin design | `admin.css` already uses scoped `baf-admin` variables and WordPress-friendly surfaces. | Admin widget placement inventory should reuse restrained WordPress admin surfaces, not public marketing cards. |

## Token Direction

Future CSS work should introduce semantic aliases before broad visual implementation. Do not rename the existing root tokens until the migration is documented and downstream references are reconciled.

| Token role | Suggested alias | Use |
| --- | --- | --- |
| Brand ink | `--baf-color-ink` | Header text, primary page titles, dense search labels, high-trust anchors. |
| Brand sky | `--baf-color-sky` | Flight, route, map, and flexible-date affordances. |
| Brand water | `--baf-color-water` | Search actions, active tabs, safe Travelpayouts handoff CTAs. |
| Deal green | `--baf-color-deal` | Savings, price alerts, successful saved states, confirmed configuration. |
| Coral/sun | `--baf-color-sun` | Urgency, seasonal prompts, attention states, not primary UI chrome. |
| Warm surface | `--baf-color-surface-warm` | Editorial destination sections and media-adjacent panels. |
| Neutral surface | `--baf-color-surface` | Search panels, widget frames, cards, admin tables. |
| Border | `--baf-color-border` | Field groups, dividers, widget frames, card outlines. |
| Muted text | `--baf-color-text-muted` | Metadata, disclosure support copy, helper states. |
| Focus | `--baf-color-focus` | Keyboard focus ring for public and admin UI. |

Color rules:

- Avoid pages dominated by only navy, slate, tan, or beige.
- Use real media and neutral surfaces to carry visual richness instead of decorative gradient blobs.
- Keep semantic colors stable across public and admin contexts: green means deal/success, amber means caution, red means error, blue/teal means search/handoff.
- Affiliate disclosures and warning states must pass contrast requirements and remain readable on mobile.
- Do not encode status by color alone; pair color with text, icons, or labels.

Typography rules:

- Search inputs, chips, tabs, and filters use compact body-size text with explicit labels.
- Hero-scale type belongs only to true hero/page-title contexts, not widget frames, filter panels, cards, or admin tables.
- Button and chip text must wrap or size down within fixed controls instead of overflowing.
- Letter spacing stays at `0` for normal UI text. Uppercase eyebrows may use modest positive tracking only where already established.

Spacing, shape, and density rules:

- Primary touch targets must be at least 44px high.
- Search panels should be dense but scannable, using grouped fields, dividers, and tabs rather than nested cards.
- Cards should use 8px radius or less. Functional chips and pills may use full radius where that is the expected pattern.
- Reserve stable dimensions for widget frames, search modules, counters, date tiles, and board columns to avoid layout shift.
- Do not place UI cards inside other UI cards. Page sections should be full-width bands or unframed layouts.

## Media Strategy

Bookings and Flights should look like a real travel product, not a generic SaaS brochure.

Media requirements:

- Use real destination, route, hotel-area, airport, and travel-context imagery whenever a page is about an inspectable place or trip surface.
- Store or reference source, license, alt text, focal point, destination/route relation, season, and vertical in content-manager fields or documented media metadata.
- Use WordPress media-library rendering functions for WordPress-owned images so responsive image markup is available.
- Use `object-fit` and focal-point-safe cropping for cards and heroes; do not crop away the travel subject.
- Provide empty states when no real image is available. Empty states can use calm color and iconography, but should not become the primary visual direction.
- Avoid fake prices, fake urgency, fake partner availability, or stock-like visuals that imply live inventory outside Travelpayouts.

Hero and first-viewport rules:

- First viewport for home, flights, hotels, explore, routes, and destination surfaces must signal the actual product or place immediately.
- Home must show flight and hotel search intent in the first viewport.
- Hero text should sit directly over or alongside real media/search context, not inside decorative cards.
- Gradient overlays are allowed only for contrast over real media.

## Component Inventory

| Component | Primary owner | Required states | Accessibility requirements | Responsive and Travelpayouts notes |
| --- | --- | --- | --- | --- |
| Unified search panel | Theme plus future Phase 13 placement seam | Default, active vertical, missing provider config, consent disabled, loading, error | Real labels, keyboard tab order, 44px controls, visible focus, no icon-only unlabeled actions | Mobile stacks fields by intent; desktop may use compact segmented layout. Submits to Travelpayouts-owned widgets, White Label, or approved handoff metadata. |
| Vertical tabs | Theme | Default, active, hover, focus, disabled | `button` semantics or ARIA tab pattern; active state not color-only | Keep labels visible on mobile. Icons are optional but must not replace text. |
| Flight route picker | Theme plus future registry/search seam | Empty, origin selected, destination selected, invalid pair, recent/popular routes | Origin and destination labels, swap button label, focusable suggestions | Avoid using reserved `destination` query var for public search links. Route pages stay WordPress-owned. |
| Hotel destination picker | Theme plus Trip.com/Hotels placement | Empty, destination selected, unavailable provider, handoff fallback | Clear label, suggestions keyboard support, no hidden handoff | Hotel live availability remains provider-owned. WordPress filters are editorial unless explicitly supported by the widget. |
| Date range and flexible-date controls | Theme | Empty, start/end selected, flexible mode, invalid range, unavailable | Keyboard operation, date labels, invalid messages, 44px targets | Do not promise live prices in custom date UI unless a Travelpayouts widget supplies them. |
| Traveler, cabin, and room controls | Theme | Closed, open, changed, invalid | Button with `aria-expanded`, accessible increment/decrement names, Escape close | On mobile, use sheets/dialogs only if focus is trapped and body scroll behavior is controlled. |
| Filter chips | Theme | Available, active, focus, disabled, removable | Active state text or icon plus label, keyboard removable controls | Chips wrap without resizing the search container unexpectedly. |
| Price alert CTA | Core plus future alert service | Anonymous, logged-in, saved, consent missing, provider unavailable | Button/link purpose is explicit, status is announced in text | Saves local alert intent only; Travelpayouts owns live price result surfaces. |
| Travelpayouts widget frame | Core wrapper plus future registry | Loading, loaded, no-script, unavailable, consent disabled, missing config, provider blocked | Disclosure and handoff link visible, iframe title, no keyboard trap in wrapper | Reserve dimensions, avoid clipping, keep horizontal overflow contained, and keep fallback handoff available for Trip.com mobile/provider-owned compression. |
| Affiliate disclosure | Core reusable component | Standard, compact, admin preview | Text visible, contrast checked, not hidden behind toggles | Must appear near monetized widgets, links, cards, route pages, destination pages, and AI planner handoffs. |
| Destination card | Theme plus CPT | Image, no image, featured, seasonal, empty taxonomy | Alt text or decorative empty alt, heading link, readable metadata | Image-led, compact, no fake price claims. Links to WordPress-owned destination single. |
| Route card | Theme plus CPT | Origin/destination, no fares, alert available, widget available | Clear route text, airport labels, CTA focus | May include route-specific Travelpayouts widget placement later, not stored live inventory. |
| Hotel guide card | Theme plus destination/hotel content | Editorial guide, provider widget available, missing provider | Labels distinguish editorial copy from provider-owned hotel search | Hotel filters and property counts must not imply live direct inventory unless provider widget owns them. |
| AI itinerary card | Core AI workflow plus theme | Draft, needs approval, approved handoff, error, demo mode | Approval status text, no auto-publish or auto-booking action | AI recommends or drafts; Travelpayouts handoff stays explicit and user-approved. |
| Saved trip board | Core user workflow plus theme | Empty, saved items, stale handoff, alert active, auth required | Board columns and actions keyboard accessible, clear empty state | Stores trip intent and content references, not partner bookings or supplier reservation data. |
| Admin widget placement table | Core admin | Empty, configured, consent disabled, invalid code, copied SubID, error | Table caption, headers, row actions with text labels, focus states | Only authorized users manage raw widget/embed code. Public output must use approved placements through the future registry. |

## Disclosure and Trust Treatment

- Every monetized widget, affiliate card, partner link cluster, AI-generated travel opportunity, route module, and destination module needs a visible affiliate disclosure near the action.
- Disclosures should be concise, plain language, and readable at mobile widths.
- Do not hide disclosures in accordions, hover tooltips, modal-only text, or desktop-only sidebars.
- Sponsored handoff links should use `rel="nofollow sponsored noopener noreferrer"` when they open partner or provider destinations.
- Search-result and booking language must make clear that booking/payment happens on Travelpayouts White Label or partner sites, not directly in WordPress.

## Widget Frame Rules For Later Implementation

Phase 12.4 owns the detailed widget-frame ticket, but all Phase 12.2 component decisions must preserve these rules:

- Reserve a stable min-height for every Travelpayouts widget placement before the script or iframe loads.
- Provide no-script, missing-configuration, consent-disabled, and provider-unavailable states.
- Keep the affiliate disclosure and sponsored handoff visible outside provider-owned iframes.
- Do not load Travelpayouts scripts site-wide unless the official plugin requires it for that surface.
- Do not alter Travelpayouts widget JavaScript in a way that breaks tracking, SubIDs, or partner handoff.
- Fixed-size widgets need explicit overflow behavior and mobile fallback links before public release.
- Widget containers may crop only when the provider-owned embed has been browser-validated and a visible handoff fallback remains available. Cropping is not a generic pattern.

## Accessibility Checklist

- Keyboard: tabs, menus, route pickers, date controls, filter chips, modals, saved-trip boards, widget placement tables, and handoff CTAs must be reachable and operable without a mouse.
- Focus: use visible `:focus-visible` treatment on every interactive control, including chip remove buttons, icon buttons, admin row actions, and external handoff links.
- Labels: icon controls require accessible names; form groups need visible labels or persistent field labels.
- Touch: primary controls and handoff CTAs must be at least 44px tall.
- Motion: respect `prefers-reduced-motion`; transitions cannot hide state changes or required content.
- Text resizing: layout must remain usable with browser text increased up to 200%, without overlap or horizontal page overflow.
- Color: text and controls must meet contrast targets; color cannot be the only status indicator.
- Mobile: disclosures, errors, empty states, and partner handoff links must remain visible without overlapping controls.

## Validation Performed

- Reviewed official WordPress theme, asset, image, and accessibility documentation.
- Reviewed official Travelpayouts widget and White Label documentation.
- Reviewed Phase 12.1 IA map and current Phase 12 blueprint requirements.
- Inventoried current static theme CSS tokens, base focus/reduced-motion behavior, homepage styles, theme architecture, and CSS file sizes.
- Inventoried core frontend widget/card styles and existing White Label/Trip.com wrapper states.

## P12.2 Result

The Phase 12 design-token direction, real-media strategy, component inventory, disclosure treatment, accessibility checklist, and widget-frame guardrails are documented. Implementation can proceed to Phase 12.3 CSS split preparation before any major public visual rebuild.

Codex PR review found no major issues on PR #10.
