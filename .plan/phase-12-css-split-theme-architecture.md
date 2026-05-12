# Phase 12.3 CSS Split and Theme Architecture Preparation

Status: `In Review`

Linear issue: `ONE-76`

Date: 2026-05-12

## Research Consulted

- WordPress Theme Handbook: [Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/) for `wp_enqueue_style()`, dependency handles, `wp_enqueue_scripts`, and theme asset URLs.
- WordPress Theme Handbook: [Theme Structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/) for standard theme folders such as `assets/css/` and the role of `functions.php`.
- WordPress Theme Handbook: [Global Settings and Styles](https://developer.wordpress.org/themes/core-concepts/global-settings-and-styles/) for keeping design tokens aligned with WordPress theme style configuration.
- Travelpayouts Help Center: [Getting started with widgets](https://support.travelpayouts.com/hc/en-us/articles/360031977872-Getting-started-with-widgets) for preserving provider widget behavior and avoiding layout breakage for adaptive or fixed-size widgets.

## Inputs Reviewed

- Phase 12.1 sitemap and ownership map in `.plan/phase-12-sitemap-navigation-page-ownership.md`.
- Phase 12.2 design-system inventory in `.plan/phase-12-design-system-component-inventory.md`.
- Current theme architecture in `themes/bookings-and-flights-static/ARCHITECTURE.md`.
- Current theme asset enqueue logic in `themes/bookings-and-flights-static/functions.php`.
- Current theme CSS files under `themes/bookings-and-flights-static/assets/css/`.
- Current Travelpayouts frontend wrapper styles in `plugins/bookings-flights-core/assets/css/frontend.css`.

## CSS File-Size State

| File | Lines after P12.3 split | Owner | Notes |
| --- | ---: | --- | --- |
| `assets/css/fonts.css` | 17 | Font declarations | Keep first in enqueue order. |
| `assets/css/tokens.css` | 388 | Design tokens | Remains the source for existing CSS variables until a documented token migration happens. |
| `assets/css/base.css` | 256 | Reset, base typography, media reset, focus, reduced motion, utilities | No split needed. |
| `assets/css/components.css` | 85 | Shared theme UI primitives | New file. Owns `.skip-link`, `.btn`, button variants, and shared icon motion. |
| `assets/css/header.css` | 464 | Desktop header, logo, nav, submenu, theme toggle, menu toggles | Reduced from 544 by moving global components out of header ownership. |
| `assets/css/mobile-nav.css` | 70 | Mobile navigation overlay | No split needed yet. |
| `assets/css/footer.css` | 279 | Footer layout and fallback content styling | No split needed yet. |
| `assets/css/home.css` | 373 | Current home template sections | Remains generic and gradient-heavy; rebuild belongs to later visual phases. |
| `plugins/bookings-flights-core/assets/css/frontend.css` | 224 | Core plugin frontend shortcodes/widgets | Kept separate and `baf-` scoped to avoid theme leakage into Travelpayouts wrappers. |
| `plugins/bookings-flights-core/assets/css/admin.css` | 252 | Core plugin admin screens | Kept separate and `baf-admin` scoped. |

No tracked CSS source file exceeds the 600-line project limit after this split. `header.css` now has enough room for Phase 12 navigation fixes without becoming a catch-all file, but major search, card, widget, and page experience styles should use dedicated modules.

## Implemented Split

`header.css` previously owned two global component groups:

- `.skip-link`
- `.btn`, `.btn--primary`, `.btn--outline`, `.btn--large`, and `.btn__icon`

Those rules now live in `assets/css/components.css`. `functions.php` enqueues the new stylesheet between `base.css` and `header.css`:

```text
fonts -> tokens -> base -> components -> header -> mobile-nav -> footer -> page-specific
```

The change is mechanical. It does not redesign the header, buttons, mobile navigation, home page, or Travelpayouts widget output.

## Module Ownership Rules

Use this ownership map before adding new public UI CSS:

| Module | Owns | Does not own |
| --- | --- | --- |
| `tokens.css` | Global CSS custom properties, design-token aliases, theme color/type/spacing primitives. | Component-specific selectors or page layout. |
| `base.css` | Reset, base elements, global accessibility utilities, media reset, reduced-motion handling. | Page sections, product cards, search controls, widgets. |
| `components.css` | Reusable theme primitives shared by multiple templates, such as buttons, skip link, future chips, badges, notices, and compact form primitives. | Header layout, footer layout, page-specific composition, provider iframe styles. |
| `header.css` | Site header, desktop nav, logo variants, submenu, header actions, theme toggle, and menu toggle buttons. | Mobile overlay content, shared `.btn`, page hero/search styles, footer. |
| `mobile-nav.css` | Mobile overlay layout, mobile nav links, mobile CTA animation. | Desktop header grid, global buttons, page content. |
| `footer.css` | Footer columns, footer links, footer contact/social/legal fallback styling. | Primary navigation, mobile navigation, page content. |
| `home.css` | Current `page-home.php` sections only. | Search shell components that will be reused on Flights, Hotels, Explore, Routes, and Deals. |
| Future `search.css` | Unified search panel, vertical tabs, route/hotel/date/traveler controls, filters, and alert CTA shell. | Travelpayouts raw widget iframe internals or provider-owned markup. |
| Future `cards.css` | Destination, route, hotel guide, deal, AI itinerary, and saved-trip card primitives if shared across pages. | Single-page-only layout decisions. |
| Future `widgets.css` or Phase 13 registry CSS | Theme-level widget frames, disclosure placement, loading/no-script/missing-config shells where they are theme-owned. | Provider-owned iframe contents or plugin admin CSS. |
| Core plugin `frontend.css` | `baf-` scoped shortcode and widget wrapper output, including consent/missing-config/fallback states. | Theme-global `.btn`, header, footer, or homepage styling. |

## Enqueueing Rules

- Add every new theme CSS module through `wp_enqueue_style()` in `functions.php`; do not hard-code stylesheet tags in templates.
- Give every style a prefixed handle using `bookings_and_flights-`.
- Express dependency order through enqueue dependencies, not selector luck.
- Continue using `filemtime()` cache busting for local theme CSS.
- Keep page-specific CSS auto-discovered from `page-{slug}.php -> assets/css/{slug}.css` unless a later architecture decision replaces that convention.
- Enqueue shared product modules only when they are needed by more than one template or by global theme chrome.
- Keep plugin frontend/widget CSS in the core plugin unless the behavior is purely theme presentation and does not affect shortcode portability.
- Do not load Travelpayouts scripts or provider widget assets site-wide unless the official plugin or documented placement contract requires it.

## Future Split Watchlist

- `home.css` should be replaced or split during the homepage rebuild. It should not absorb shared search, card, or widget styles.
- `tokens.css` should get semantic travel aliases before large visual work, but existing token names should not be renamed without a documented migration.
- `mobile-nav.css` currently has fixed 3rem links and hardcoded transition delays for five items. When the Phase 12 navigation target adds more primary items, mobile text sizing and delay behavior need review.
- `header.css` should stay under 500 lines after P12.3. If Phase 12.4 or Phase 14 adds substantial nav/search-shell behavior, create a dedicated module instead.
- Travelpayouts widget frames should keep visible fallback handoff links outside provider-owned iframes.

## Validation Performed

- Reviewed official WordPress theme asset and theme-structure documentation.
- Reviewed Travelpayouts widget documentation for adaptive and fixed-size widget caution.
- Inventoried tracked theme and plugin CSS file sizes.
- Split global component primitives out of `header.css` into `components.css`.
- Updated the theme enqueue graph in `functions.php` and `ARCHITECTURE.md`.
- Ran PHP syntax check for `themes/bookings-and-flights-static/functions.php`.
- Confirmed `components.css` returns `200` locally and is present in the rendered home page source before `header.css`, `mobile-nav.css`, and `footer.css`.
- Browser-smoked the home page at default desktop size and a 390px mobile viewport; the new stylesheet loaded, shared controls were present, and no console errors were reported.

## P12.3 Result

The static theme CSS architecture now has a dedicated shared-components layer, a smaller header module, documented ownership boundaries, and clear rules for future search/card/widget modules. No unrelated visual redesign was included.
