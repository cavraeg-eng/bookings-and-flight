# Booking.com Review Readiness

Date reviewed: 2026-05-14

## Current Travelpayouts State

- Travelpayouts source checked: `bookingsandflights.com` (`source=47745`).
- Booking.com program checked: program `84`.
- Booking.com is visible in the catalog, but it is listed as unavailable for the current project/source and requires brand pre-approval.
- The authenticated Booking.com program page states that the project must have original travel content, be at least two months old, be regularly updated, contain current information, allow organic affiliate-link integration, and avoid offensive, illegal, or harmful content.
- Travelpayouts Help Center guidance says declined projects commonly fail because they have too little original travel content, use the wrong project type, do not match the program's audience/topic, violate program rules, or cannot prove ownership.

## Blocking Findings

### Public Production URL

`https://bookingsandflights.com/` currently resolves to a Squarespace placeholder-style page instead of the local WordPress travel site. A Booking.com reviewer evaluating the Travelpayouts source will see the thin public page rather than the WordPress implementation, so the current source does not present enough travel content or site ownership context.

### Current Source Fit

The current product and domain are structurally hard to approve for Booking.com:

- The public domain contains `bookings`, which is likely to trigger Booking.com brand-name/domain restrictions.
- The site direction is a travel discovery, flight/hotel search, comparison, and partner-handoff product, while Booking.com approvals are stricter for metasearch and price-comparison style projects.
- The local WordPress site has good platform shells but still needs a public history of original, regularly updated travel guides before it can look like an established organic content source.

## Implemented Locally

- Filled the About page with public editorial purpose, partner-boundary, and disclosure content.
- Filled the Contact page with site-support, affiliate-review, legal-page, disclosure, and provider-boundary information.
- Added a `Hello Travelpayouts` ownership verification comment to the public footer source.

## Recommended Approval Path

Do not keep resubmitting the current `bookingsandflights.com` source to Booking.com until the public-source mismatch is fixed. The next viable paths are:

1. Deploy the WordPress site publicly so reviewers see real travel pages, legal pages, disclosures, and contact details instead of the Squarespace placeholder.
2. Configure a public support email in WordPress that matches the Travelpayouts profile/contact email before reapplying.
3. Publish original destination, route, hotel-neighborhood, and travel-planning guides over time so the source is clearly active and useful.
4. If Booking.com remains the target, create a separate blog-only project/domain without `booking` or other travel-brand terms in the domain, then add it as a separate Travelpayouts source and apply with that project.
5. Keep the current Bookings and Flights source monetized through Travelpayouts programs that allow widgets, White Label, and travel search handoffs for this product shape.

## Resubmission Gate

Before another Booking.com review request:

- Public URL shows the WordPress site, not a placeholder.
- Contact page shows a real public support email.
- About, Contact, Privacy, Terms, and affiliate disclosure pages are public.
- The source has a visible library of original travel content.
- The Travelpayouts project type and promotion method match the Booking.com program page.
- No paid-search, brand-name, or metasearch-restricted promotion method is used for Booking.com.
