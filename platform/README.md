# Bookings and Flights — Platform Monorepo

> **Model**: Pure affiliate / meta-search. We never hold money, never issue tickets. Users click through to the supplier and complete their purchase there. We earn commission on completed bookings via supplier postbacks.

## Workspaces

```
platform/
├── apps/
│   └── web/                  # Next.js App Router — visitor-facing search UI
├── services/
│   └── search-api/           # Fastify — supplier fan-out, clicks, redirects, postbacks
└── packages/
    └── shared/               # Zod schemas, Offer model, supplier allowlist, HMAC clickId helpers
```

## Quick start

```bash
# from platform/
npm install
npm run build:shared    # compiles @baf/shared once so downstream workspaces can import it
npm run typecheck       # runs tsc --noEmit on every workspace
npm run dev             # starts search-api on :4050 AND Next.js on :3000
```

## Environment

Each service carries its own `.env.example`. Copy each to `.env` in the matching folder and fill in only the keys you have:

```bash
cp services/search-api/.env.example services/search-api/.env
cp apps/web/.env.example apps/web/.env.local
```

The monorepo is **fail-closed**: supplier adapters that don't have credentials throw `SUPPLIER_UNAVAILABLE` at runtime, so search still returns a clean (empty) result rather than an error — you can demo the click-through flow end-to-end with zero real supplier credentials.

## Click → redirect flow

1. Browser `POST /clicks` with the normalized offer metadata.
2. API HMAC-signs a short `clickId`, logs a row, returns `{ clickId, redirectUrl }`.
3. Browser navigates to `GET /go/:clickId` (on the API or via Next.js rewrite).
4. API verifies the HMAC, checks the destination host is on the supplier allowlist, then 302s the user to the supplier deeplink.
5. (Later) The supplier calls our WP plugin postback endpoint when the booking completes; WordPress forwards the normalized conversion to the API with `x-postback-secret`, and the API validates it against `BAF_POSTBACK_SECRET` before marking the click converted.

## Location note

This monorepo lives at `wp-content/platform/` because Local by Flywheel scopes its workspace to the `wp-content` tree. In production the `platform/` folder should move to the repository root alongside `wp-content/`; the imports and package names already assume that.

## Not included yet

- Real supplier adapter implementations (stubbed — `buildDeepLink` works, `search*` throws `SUPPLIER_UNAVAILABLE`).
- Prisma / Postgres / TimescaleDB — the MVP uses in-memory click storage so you can see the flow without a DB running. Swap in Prisma once Postgres is provisioned.
- Redis cache — same reason; `cache.ts` is an in-memory LRU stub with the same interface.
