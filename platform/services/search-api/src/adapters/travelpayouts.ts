import type { FlightSearchRequest, Offer } from "@baf/shared";
import { env } from "../config/env.js";
import * as cache from "../infra/cache.js";
import type { SupplierAdapter } from "./types.js";

/**
 * Travelpayouts (Aviasales) Data API v3 — flight price search.
 *
 *   GET https://api.travelpayouts.com/aviasales/v3/prices_for_dates
 *     ?origin=JFK&destination=LHR&departure_at=2026-05-20
 *     &currency=usd&one_way=true&sorting=price&limit=30
 *   Header: X-Access-Token: <token>
 *
 * Docs: https://support.travelpayouts.com/hc/en-us/articles/203956163
 *
 * Notes
 *   • This is Travelpayouts' cached data API — results are fast but not always
 *     available for obscure routes. Empty `data` is normal, not an error.
 *   • The returned `link` is a path on aviasales.com. We prepend the host and
 *     inject our affiliate `marker` so commissions are attributed.
 *   • `aviasales.com` is already on the open-redirect allowlist in
 *     packages/shared/src/suppliers.ts.
 */

const API_URL = "https://api.travelpayouts.com/aviasales/v3/prices_for_dates";
const AVIASALES_BASE = "https://www.aviasales.com";
const TIMEOUT_MS = 8_000;
const CACHE_TTL_MS = 15 * 60 * 1_000; // 15 minutes

// IATA airline codes → display names. Unknown codes render as the code itself.
const AIRLINES: Record<string, string> = {
    AA: "American Airlines",
    AC: "Air Canada",
    AF: "Air France",
    AS: "Alaska",
    AY: "Finnair",
    AZ: "ITA Airways",
    BA: "British Airways",
    CX: "Cathay Pacific",
    DL: "Delta",
    EI: "Aer Lingus",
    EK: "Emirates",
    EY: "Etihad",
    IB: "Iberia",
    JL: "JAL",
    KE: "Korean Air",
    KL: "KLM",
    LH: "Lufthansa",
    LX: "Swiss",
    NH: "ANA",
    QF: "Qantas",
    QR: "Qatar Airways",
    SK: "SAS",
    SQ: "Singapore Airlines",
    TK: "Turkish Airlines",
    TP: "TAP Portugal",
    UA: "United",
    VS: "Virgin Atlantic",
    WN: "Southwest",
};

interface TPPrice {
    origin: string;
    destination: string;
    price: number;
    airline: string;
    flight_number?: string;
    departure_at: string;
    return_at?: string;
    transfers: number;
    return_transfers?: number;
    duration: number;
    duration_to?: number;
    duration_back?: number;
    link: string;
}

interface TPResponse {
    success: boolean;
    data: TPPrice[];
    currency?: string;
    error?: string;
}

export const travelpayoutsAdapter: SupplierAdapter = {
    id: "travelpayouts",
    mode: "real",
    verticals: ["flights"],

    isConfigured: () =>
        Boolean(env.travelpayouts.token && env.travelpayouts.marker),

    async searchFlights(req: FlightSearchRequest, log?: { info: (...a: unknown[]) => void }): Promise<Offer[]> {
        const cacheKey = cache.generateKey(
            "flights",
            req.origin,
            req.destination,
            req.depart,
            req.return ?? "",
            req.currency,
        );

        const cached = cache.get<Offer[]>(cacheKey);
        if (cached) {
            log?.info({ cacheKey, ...cache.stats() }, "travelpayouts cache HIT");
            return cached;
        }
        log?.info({ cacheKey }, "travelpayouts cache MISS — calling API");

        const url = new URL(API_URL);
        url.searchParams.set("origin", req.origin);
        url.searchParams.set("destination", req.destination);
        url.searchParams.set("departure_at", req.depart);
        if (req.return) url.searchParams.set("return_at", req.return);
        url.searchParams.set("currency", req.currency.toLowerCase());
        url.searchParams.set("one_way", req.return ? "false" : "true");
        url.searchParams.set("sorting", "price");
        url.searchParams.set("direct", "false");
        url.searchParams.set("limit", "30");

        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
        let resp: Response;
        try {
            resp = await fetch(url, {
                signal: controller.signal,
                headers: {
                    "X-Access-Token": env.travelpayouts.token,
                    "Accept-Encoding": "gzip",
                },
            });
        } finally {
            clearTimeout(timer);
        }

        if (!resp.ok) {
            throw new Error(`travelpayouts HTTP ${resp.status}`);
        }
        const json = (await resp.json()) as TPResponse;
        if (!json.success) {
            throw new Error(`travelpayouts error: ${json.error ?? "unknown"}`);
        }

        const offers = json.data.map((p, i) => {
            const carrier = AIRLINES[p.airline] ?? p.airline;
            const dur = p.duration_to ?? p.duration ?? 0;
            const durStr = formatDuration(dur);
            const stopsStr =
                p.transfers === 0
                    ? "Nonstop"
                    : `${p.transfers} stop${p.transfers === 1 ? "" : "s"}`;
            const cabin = req.cabin[0].toUpperCase() + req.cabin.slice(1);

            const badges: string[] = [];
            if (i === 0) badges.push("Cheapest");
            if (p.transfers === 0) badges.push("Direct");

            return {
                supplier: "travelpayouts",
                vertical: "flights",
                supplierOfferRef: `tp-${p.origin}-${p.destination}-${p.airline}${
                    p.flight_number ?? ""
                }-${p.departure_at}`,
                title: `${carrier} · ${p.origin} → ${p.destination}`,
                subtitle: `${durStr} · ${stopsStr} · ${cabin}`,
                price: { amount: p.price, currency: req.currency },
                badges: badges.length ? badges : undefined,
                deeplink: buildDeeplink(p.link, env.travelpayouts.marker),
                metadata: {
                    airline: carrier,
                    carrier,
                    airlineCode: p.airline,
                    flightNumber: p.flight_number,
                    duration: durStr,
                    transfers: stopsStr,
                    stops: p.transfers,
                    departTime: formatTime(p.departure_at),
                    departureAt: p.departure_at,
                },
            } satisfies Offer;
        });

        cache.set(cacheKey, offers, CACHE_TTL_MS);
        return offers;
    },
};

function formatDuration(minutes: number): string {
    if (minutes <= 0) return "—";
    const h = Math.floor(minutes / 60);
    const m = minutes % 60;
    return h > 0 ? `${h}h ${m}m` : `${m}m`;
}

function formatTime(value: string): string {
    const timeMatch = value.match(/T(\d{2}:\d{2})/);
    if (timeMatch) return timeMatch[1];

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";

    const hours = String(date.getHours()).padStart(2, "0");
    const minutes = String(date.getMinutes()).padStart(2, "0");
    return `${hours}:${minutes}`;
}

function buildDeeplink(link: string, marker: string): string {
    // `link` is a path such as "/search/JFK2005LHR1?..." relative to aviasales.com.
    const u = new URL(link, AVIASALES_BASE);
    u.searchParams.set("marker", marker);
    return u.toString();
}
