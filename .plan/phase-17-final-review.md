# Phase 17 Final Review

Date: 2026-05-13

Linear issue: `ONE-112`

Status: `Completed`

## Scope

P17.7 closes the Destination, Route, and SEO Content Engine after P17.1 through P17.6 merged.

Reviewed surfaces:

- `/destinations/` and destination singles
- `/routes/`, origin route behavior, and route singles
- `/travel-deals/` and travel deal singles
- `travel_region`, `travel_style`, `travel_vertical`, and `travel_season` taxonomy archives
- transient `/flights/` and `/hotels/` query URLs
- structured editor meta fields for `destination`, `route`, and `travel_deal`
- Travelpayouts placement wrappers, visible disclosures, SubID boundaries, source output, and keyboard navigation

## Result

Passed locally for the PR candidate. Phase 18 can start after this final review PR is reviewed, merged, and Linear is synced.

The final runtime report is saved at `/tmp/one112-phase17-final-report.json` and returned `status=pass` with `findingCount=0`.

## Final Validation Evidence

Temporary validation content:

- destination `ONE112 Lisbon Coast Final Review Guide`
- route `ONE112 Miami to Lisbon Final Review Route`
- travel deal `ONE112 Lisbon Family Final Review Deal Brief`
- public `one112-region/style/vertical/season` taxonomy terms
- private destination and non-public `trip_plan` records assigned to the same region for leak checks

Temporary content was deleted after validation and confirmed absent.

Screenshots:

- `/tmp/one112-destinations_archive-desktop.png`
- `/tmp/one112-destination_single-desktop.png`
- `/tmp/one112-routes_archive-desktop.png`
- `/tmp/one112-route_single-desktop.png`
- `/tmp/one112-deals_archive-desktop.png`
- `/tmp/one112-deal_single-desktop.png`
- `/tmp/one112-taxonomy_region-desktop.png`
- `/tmp/one112-flights_query-desktop.png`
- `/tmp/one112-hotels_query-desktop.png`
- `/tmp/one112-keyboard-destination_single.png`
- `/tmp/one112-keyboard-route_single.png`
- `/tmp/one112-keyboard-deal_single.png`
- `/tmp/one112-keyboard-taxonomy_region.png`

## Checks

- PHP syntax passed for P17 CPT/meta classes, editor meta boxes, archive/single templates, taxonomy template, and SEO metadata.
- Changed Phase 17 source files remain under the 600-line guideline. `frontend.css` remains at 590 lines and is documented for future splitting before substantial expansion.
- `bookings-flights-core` is active.
- Public P17 CPTs are still public with expected archives: `destination`, `route`, and `travel_deal`.
- Non-public workflow/storage CPTs remain private: `trip_plan`, `travel_alert`, and `travel_partner`.
- Destination, route, and deal editor meta keys are registered with `show_in_rest=false`, sanitization callbacks, and edit-meta authorization callbacks.
- Runtime browser pages returned `200`, rendered one H1, kept visible affiliate disclosure text, had no horizontal overflow, no unnamed visible links, no small visible targets after accounting for label-sized checkbox hit areas, no app-owned console errors, no app-owned failed requests, and no forbidden source terms.
- Transient `/flights/?origin=MIA&destination=LIS&baf_surface=phase17_final` rendered `noindex, follow` and canonicalized to `/flights/`.
- Transient `/hotels/?travel_destination=Lisbon%20Coast&stay_focus=family&baf_surface=phase17_final` rendered `noindex, follow` and canonicalized to `/hotels/`.
- The taxonomy archive did not expose the private destination or non-public `trip_plan` test records.
- Keyboard review reached interactive controls and handoff/internal-link paths on destination, route, deal, and taxonomy pages.

## Bugs Found And Fixed

No new app-owned bug was found in P17.7.

The first final-gate report flagged two route/Flights findings that were reviewed and classified correctly before rerun:

- The alert consent checkbox is visually 22px, but it is inside a 582px by 154px label hit area. The final report accounts for the label as the user target.
- A `400` console error came from an Aviasales/Travelpayouts provider analytics pixel at `avsplow.com`, not an app-owned request. The final report classifies that with provider-owned widget noise.

## Deferred Watch Items

- `plugins/bookings-flights-core/assets/css/frontend.css` is 590 lines. Split it before future substantial core frontend CSS expansion.
- Provider-owned Travelpayouts/Aviasales WebGL, Babel, GraphQL, `tp.media`, and `avsplow.com` warnings remain watchlist-only when app-owned rendering, disclosure, keyboard navigation, source scans, and request checks pass.
- Production richness still depends on published destination, route, and travel deal content plus editor-entered metadata. The templates are validated, but empty production content will still produce sparse pages.
- The local untracked `bookings-and-flights-content-manager` plugin remains outside the tracked P17 field pipeline and still needs the split documented in `.plan/editor-workflow-content-manager-review.md` before large tracked expansion.

## Research Consulted

- WordPress Theme Handbook: Template Hierarchy.
- WordPress Plugin Handbook: Custom Post Types.
- WordPress Plugin Handbook: Custom Meta Boxes.
- WordPress Plugin Security Handbook: Securing Input.
- WordPress Common APIs Handbook: Escaping Data.
- WordPress Developer Resources: `wp_robots`.
- Travelpayouts Help Center: White Label Web, Widget setup, SubID, and widgets.
- FTC Business Guidance: Disclosures 101 for Social Media Influencers.
