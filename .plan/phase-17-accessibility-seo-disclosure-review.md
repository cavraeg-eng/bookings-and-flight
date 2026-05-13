# Phase 17.6 Accessibility, SEO, and Disclosure Review

Date: 2026-05-13

Linear issue: `ONE-111`

Status: `Completed`

## Scope

P17.6 reviews the public Phase 17 SEO content surfaces after destination, route, deal, taxonomy, and editor workflow work:

- `/destinations/`
- destination singles
- `/routes/`
- route singles
- `/travel-deals/`
- travel deal singles
- public travel taxonomy archives
- transient Flights and Hotels query URLs that should remain handoff/search surfaces
- Travelpayouts widget shells, disclosures, keyboard paths, source output, and responsive behavior

The review did not add new provider contracts, custom inventory APIs, REST routes, options, tables, cron jobs, checkout, payment, booking, or auto-publishing behavior.

## Bugs Found And Fixed

- The route alert consent checkbox rendered below the 44px interactive target threshold in the strict runtime pass. `plugins/bookings-flights-core/assets/css/frontend.css` now gives the consent row a 44px minimum block size and increases the checkbox control size.
- The official Travelpayouts route-map iframe could mount without an accessible name because the provider script owns the iframe markup. `plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php` now applies a generated `title` to child provider iframes when they appear, including late MutationObserver inserts and visible-refresh paths.
- Deal and taxonomy card title links could render below the 44px target threshold on strict mobile checks. `themes/bookings-and-flights-static/assets/css/deal-surface.css` and `themes/bookings-and-flights-static/assets/css/taxonomy-surface.css` now give those title links inline-flex layout and a 44px minimum block size.
- The default destination seasonal guidance used "guaranteed availability" language. `themes/bookings-and-flights-static/single-destination.php` now uses safer copy that avoids live availability claims.

## Local Review Result

Passed locally for the PR candidate.

Runtime Playwright Chromium validation returned `status=pass` and `findingCount=0` after the fixes. Destination, route, deal, taxonomy, destination archive, routes archive, and travel-deals archive pages returned `200`, rendered one H1, had visible disclosures, had no horizontal overflow, no unnamed links, no missing image alt findings, no app-owned console errors, no blocking failed requests, and no small visible interactive targets.

SEO source checks confirmed:

- Indexable destination, route, deal, archive, and taxonomy pages render canonical URLs for their owned WordPress surfaces.
- `/flights/?origin=MIA&destination=LIS&baf_surface=route_single` renders `noindex, follow` and canonicalizes to `/flights/`.
- `/hotels/?travel_destination=Lisbon%20Coast&stay_focus=family&baf_surface=destination` renders `noindex, follow` and canonicalizes to `/hotels/`.
- Public taxonomy archives remain indexable WordPress-owned internal-linking surfaces.

Provider-owned console warnings were separated from app-owned failures:

- Chromium WebGL performance warnings appeared around embedded provider content.
- Travelpayouts/Aviasales React/Babel and GraphQL fragment warnings appeared from provider-owned widget scripts.
- These are watchlist-only while pages render, disclosures remain visible, keyboard paths work, and app-owned console/request checks pass.

## Browser Evidence

Report:

- `/tmp/one111-accessibility-seo-report.json`

Screenshots:

- `/tmp/one111-destination-desktop.png`
- `/tmp/one111-route-desktop.png`
- `/tmp/one111-deal-desktop.png`
- `/tmp/one111-taxonomy-desktop.png`
- `/tmp/one111-destinations-archive-desktop.png`
- `/tmp/one111-routes-archive-desktop.png`
- `/tmp/one111-deals-archive-desktop.png`
- `/tmp/one111-destination-mobile.png`
- `/tmp/one111-route-narrow.png`
- `/tmp/one111-deal-mobile.png`
- `/tmp/one111-taxonomy-narrow.png`
- `/tmp/one111-keyboard-destination.png`
- `/tmp/one111-keyboard-route.png`
- `/tmp/one111-keyboard-deal.png`
- `/tmp/one111-keyboard-taxonomy.png`
- `/tmp/one111-destination-copy-followup-mobile.png`

The Codex in-app Browser path was attempted earlier in this phase family but did not expose an active pane in this local thread, so Playwright Chromium was used for real runtime screenshots, console/request inspection, responsive checks, and keyboard navigation review.

After the destination fallback copy was tightened from "confirmed availability" to "availability commitments", a focused mobile Playwright follow-up confirmed the temporary destination page returned `200`, rendered the new copy, did not render "guaranteed availability" or "confirmed availability", kept affiliate disclosure visible, had no horizontal overflow, and had no app-owned console errors. The follow-up temporary destination was deleted and confirmed absent.

## Validation Commands

```bash
php -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php
php -l plugins/bookings-flights-core/includes/frontend/class-flight-alert-signup-shortcode.php
php -l themes/bookings-and-flights-static/single-destination.php
wc -l plugins/bookings-flights-core/includes/frontend/class-official-shortcode-renderer.php plugins/bookings-flights-core/assets/css/frontend.css themes/bookings-and-flights-static/assets/css/deal-surface.css themes/bookings-and-flights-static/assets/css/taxonomy-surface.css themes/bookings-and-flights-static/single-destination.php
node Playwright Chromium accessibility, responsive, SEO source, console/request, and keyboard review for P17 public surfaces
git diff --check
wp plugin activate bookings-flights-core
wp post/term cleanup for temporary ONE-111 fixtures
```

WP-CLI emitted the known local PHP 8.5 deprecation noise from WP-CLI internals and the third-party Travelpayouts plugin, but the plugin activation and cleanup commands completed successfully.

## Deferred Watch Items

- `plugins/bookings-flights-core/assets/css/frontend.css` is now 590 lines. It remains under the 600-line guideline after this narrow fix, but future substantial core frontend CSS work should split the file before adding more behavior.
- Provider-owned Travelpayouts/Aviasales console warnings remain a watchlist item when embedded widgets load. They are not app-owned blockers while rendering, disclosure, keyboard navigation, source scans, and request checks pass.

## Research Consulted

- WordPress Coding Standards Handbook: Accessibility Coding Standards.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Developer Resources: `wp_robots`.
- WordPress Theme Handbook: Template Hierarchy.
- Travelpayouts Help Center: What is White Label Web by Travelpayouts?
- Travelpayouts Help Center: Setting up a White Label with Widget type.
- Travelpayouts Help Center: ID and SubID (Affiliate marker and additional marker).
- Travelpayouts Help Center: Getting started with widgets.
- FTC Business Guidance: Disclosures 101 for Social Media Influencers.
