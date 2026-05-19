# WordPress themes — ARCHIVED

As of April 2026, the Bookings and Flights frontend is **Next.js only**, served from
`/platform/apps/web/` (port 3000 in dev). The themes in this folder are retained for
reference / future use but are **not the production frontend**.

## Folder contents

- `bookings-and-flights-static/` — The original standalone WordPress mockup. Pure PHP +
  vanilla CSS/JS. Kept for historical reference.
- `bookings-and-flights-headless/` — Thin WordPress theme that redirects/proxies all WP
  traffic to the Next.js site on `:3000`. Kept in case you later want to run both WP
  (for blog/CMS content) and Next.js (for the public site) under one domain.
- `twentytwentyfive/` — Default WordPress theme. Shipped with core.

## Current architecture

```
User → Next.js (:3000) → Fastify search API (:4050) → Supplier APIs
```

WordPress at `:10019` is no longer part of the public site. It can still be used to
host admin tooling or a blog, but it is not required for the core product.

## If you want WordPress back

Activate the `bookings-and-flights-headless` theme from `wp-admin → Appearance → Themes`.
That theme forwards all public routes to the Next.js app. You can then use WordPress
for blog/CMS content and expose it via the WP REST API or headless fetch from Next.js.

## If you want to remove WordPress entirely

These theme folders (and `/plugins/bookings-and-flights-affiliate-bridge/`) can be
deleted without affecting the Next.js frontend. They are not imported or referenced
by any code in `/platform/`.
