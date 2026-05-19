import type { HotelSearchRequest, Offer } from "@baf/shared";
import { env } from "../config/env.js";
import * as cache from "../infra/cache.js";
import type { SupplierAdapter } from "./types.js";

/**
 * Hotellook adapter — Travelpayouts hotel vertical.
 *
 * Strategy:
 *   1. Try the Hotellook cache API to get real pricing data.
 *      - Lookup: GET https://engine.hotellook.com/api/v2/lookup.json
 *      - Prices: GET https://engine.hotellook.com/api/v2/cache.json
 *   2. If the API call fails or returns no results, return no offers
 *      instead of inventing prices that would mislead sorting and filters.
 */

const LOOKUP_URL = "https://engine.hotellook.com/api/v2/lookup.json";
const CACHE_URL = "https://engine.hotellook.com/api/v2/cache.json";
const HOTELLOOK_SEARCH = "https://search.hotellook.com/hotels";
const TIMEOUT_MS = 8_000;
const CACHE_TTL_MS = 30 * 60 * 1_000; // 30 minutes

interface HLLookupResult {
    results?: {
        locations?: { id: string; cityName?: string; fullName?: string }[];
    };
}

interface HLCacheHotel {
    hotelId: number;
    hotelName: string;
    stars: number;
    priceFrom: number;
    priceAvg?: number;
    locationId?: number;
    location?: {
        name?: string;
        country?: string;
        lat?: number;
        lon?: number;
    };
}

export const hotellookAdapter: SupplierAdapter = {
    id: "travelpayouts",
    mode: "real",
    verticals: ["hotels"],

    isConfigured: () => Boolean(env.travelpayouts.token && env.travelpayouts.marker),

    async searchHotels(req: HotelSearchRequest): Promise<Offer[]> {
        const cacheKey = cache.generateKey(
            "hotels",
            req.destination,
            req.checkIn,
            req.checkOut,
            String(req.adults),
            req.currency,
        );

        const cached = cache.get<Offer[]>(cacheKey);
        if (cached) return cached;

        try {
            const offers = await fetchHotellookAPI(req);
            cache.set(cacheKey, offers, CACHE_TTL_MS);
            return offers;
        } catch {
            return [];
        }
    },
};

/* ------------------------------------------------------------------ */
/*  Hotellook Cached-Price API                                        */
/* ------------------------------------------------------------------ */

async function fetchHotellookAPI(req: HotelSearchRequest): Promise<Offer[]> {
    // Step 1: resolve city → locationId
    const locationId = await resolveLocationId(req.destination);
    if (!locationId) return [];

    // Step 2: fetch cached hotel prices
    const url = new URL(CACHE_URL);
    url.searchParams.set("location", locationId);
    url.searchParams.set("currency", req.currency.toLowerCase());
    url.searchParams.set("checkIn", req.checkIn);
    url.searchParams.set("checkOut", req.checkOut);
    url.searchParams.set("limit", "30");
    url.searchParams.set("token", env.travelpayouts.token);

    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
    let resp: Response;
    try {
        resp = await fetch(url, {
            signal: controller.signal,
            headers: { "Accept-Encoding": "gzip" },
        });
    } finally {
        clearTimeout(timer);
    }

    if (!resp.ok) {
        throw new Error(`hotellook cache HTTP ${resp.status}`);
    }

    const hotels = (await resp.json()) as HLCacheHotel[];
    if (!Array.isArray(hotels)) {
        throw new Error("hotellook returned malformed cache response");
    }
    if (hotels.length === 0) return [];

    return hotels.map((h, i) => {
        const starsStr = h.stars > 0 ? "★".repeat(h.stars) : "";
        const badges: string[] = [];
        if (i === 0) badges.push("Cheapest");
        if (h.stars >= 5) badges.push("Luxury");

        const deeplink = buildHotelDeeplink(req, h.hotelId);

        return {
            supplier: "travelpayouts",
            vertical: "hotels",
            supplierOfferRef: `hl-${h.hotelId}-${req.checkIn}-${req.checkOut}`,
            title: h.hotelName,
            subtitle: [starsStr, req.destination].filter(Boolean).join(" · "),
            price: { amount: h.priceFrom, currency: req.currency },
            badges: badges.length ? badges : undefined,
            deeplink,
            metadata: {
                hotelId: h.hotelId,
                stars: h.stars,
                priceAvg: h.priceAvg,
            },
        } satisfies Offer;
    });
}

async function resolveLocationId(city: string): Promise<string | null> {
    const url = new URL(LOOKUP_URL);
    url.searchParams.set("query", city);
    url.searchParams.set("lang", "en");
    url.searchParams.set("lookFor", "city");
    url.searchParams.set("limit", "1");
    url.searchParams.set("token", env.travelpayouts.token);

    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
    let resp: Response;
    try {
        resp = await fetch(url, {
            signal: controller.signal,
            headers: { "Accept-Encoding": "gzip" },
        });
    } finally {
        clearTimeout(timer);
    }

    if (!resp.ok) {
        throw new Error(`hotellook lookup HTTP ${resp.status}`);
    }

    const data = (await resp.json()) as HLLookupResult;
    const locations = data.results?.locations;
    if (!locations || locations.length === 0) return null;
    return locations[0].id;
}

function buildHotelDeeplink(req: HotelSearchRequest, hotelId: number): string {
    const url = new URL(HOTELLOOK_SEARCH);
    url.searchParams.set("destination", req.destination);
    url.searchParams.set("checkIn", req.checkIn);
    url.searchParams.set("checkOut", req.checkOut);
    url.searchParams.set("adults", String(req.adults));
    url.searchParams.set("hotelId", String(hotelId));
    url.searchParams.set("marker", env.travelpayouts.marker);
    return url.toString();
}
