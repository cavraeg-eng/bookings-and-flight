# Backend and affiliate integrations

This project is a **Next.js site + Fastify backend**.

## Local URLs

- Public site: `http://localhost:3000`
- Backend API: `http://localhost:4050`
- Integrations status: `http://localhost:4050/integrations`
- Health check: `http://localhost:4050/health`

## Start the stack

From `platform/`:

```bash
npm run dev
```

Or run them separately:

```bash
npm run dev:api
npm run dev:web
```

## Where supplier programs are configured

All partner credentials live in:

`platform/services/search-api/.env`

Relevant variables:

- `TRAVELPAYOUTS_API_TOKEN`
- `TRAVELPAYOUTS_MARKER`
- `BOOKING_AFFILIATE_ID`
- `BOOKING_API_TOKEN`
- `BOOKING_USE_SANDBOX`
- `VIATOR_API_KEY`
- `VIATOR_PARTNER_ID`
- `DISCOVERCARS_PARTNER_ID`
- `KIWI_AFFILIATE_ID`

## How the current setup works

### Flights

- **Live**
- Adapter: `platform/services/search-api/src/adapters/travelpayouts.ts`
- Uses Travelpayouts / Aviasales API

### Hotels

- **Frontend live now via Trip.com white-label + Travelpayouts custom links**
- Widget script: `promo_id=4038`
- Optional backend adapter: `platform/services/search-api/src/adapters/booking-demand.ts`
- Booking Demand API is no longer required for the public hotel search page to function
- If you later add Booking credentials, the backend adapter can power a separate direct hotel inventory feed

### Cars

- **No demo inventory anymore**
- No backend adapter is currently active
- Public cars page now stays offline until a real partner program is wired

### Activities

- **No demo inventory anymore**
- No backend adapter is currently active
- Public activities page now stays offline until a real partner program is wired

## How to switch or add programs

1. Get approved for the partner program
2. Add the credential(s) to `platform/services/search-api/.env`
3. Create or update the adapter in `platform/services/search-api/src/adapters/`
4. Register it in `platform/services/search-api/src/adapters/registry.ts`
5. Restart the API
6. Check `http://localhost:4050/integrations`

## Important note about WordPress / Local by Flywheel

You do **not** need to update Local by Flywheel for the main site anymore if you are staying Next.js-only.

WordPress on `:10019` is now optional and can be used later for:

- blog / CMS content
- admin tools
- old-theme reference

The public product site is the Next.js app on `:3000`.