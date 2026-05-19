import type { CarSearchRequest, Offer } from "@baf/shared";
import { env } from "../config/env.js";
import * as cache from "../infra/cache.js";
import type { SupplierAdapter } from "./types.js";

/**
 * Car rental deep-link adapter via Travelpayouts / DiscoverCars.
 *
 * No real-time pricing API is available, so we generate curated offers
 * for common rental categories with affiliate deep links through the
 * Travelpayouts redirector (tp.media).
 *
 * URL pattern:
 *   https://tp.media/r?marker={MARKER}&trs=...&p=4114&u=<encoded DiscoverCars URL>
 */

const TP_REDIRECT = "https://tp.media/r";
const DISCOVERCARS_BASE = "https://www.discovercars.com";
const CACHE_TTL_MS = 30 * 60 * 1_000; // 30 minutes

interface CarCategory {
    name: string;
    slug: string;
    subtitle: string;
    estimatePerDay: number; // USD baseline
    badge?: string;
}

const CAR_CATEGORIES: CarCategory[] = [
    { name: "Economy", slug: "economy", subtitle: "Compact & fuel-efficient", estimatePerDay: 25, badge: "Best value" },
    { name: "Compact", slug: "compact", subtitle: "Perfect for city driving", estimatePerDay: 35 },
    { name: "SUV", slug: "suv", subtitle: "Spacious & versatile", estimatePerDay: 55 },
    { name: "Luxury", slug: "luxury", subtitle: "Premium comfort", estimatePerDay: 95, badge: "Premium" },
    { name: "Van / Minivan", slug: "van", subtitle: "Great for groups & families", estimatePerDay: 65 },
];

export const carsAdapter: SupplierAdapter = {
    id: "discovercars",
    mode: "real",
    verticals: ["cars"],

    isConfigured: () => Boolean(env.travelpayouts.marker),

    async searchCars(req: CarSearchRequest): Promise<Offer[]> {
        const cacheKey = cache.generateKey(
            "cars",
            req.pickupLocation,
            req.dropoffLocation ?? "",
            req.pickupDate,
            req.dropoffDate,
            req.pickupTime,
            req.dropoffTime,
            String(req.driverAge),
            req.currency,
        );

        const cached = cache.get<Offer[]>(cacheKey);
        if (cached) return cached;

        const days = computeDays(req.pickupDate, req.dropoffDate);
        const offers = CAR_CATEGORIES.map((cat, i) =>
            buildCategoryOffer(cat, req, days, i),
        );

        cache.set(cacheKey, offers, CACHE_TTL_MS);
        return offers;
    },
};

function buildCategoryOffer(
    cat: CarCategory,
    req: CarSearchRequest,
    days: number,
    _index: number,
): Offer {
    const totalEstimate = Math.round(cat.estimatePerDay * Math.max(days, 1));
    const deeplink = buildDeeplink(req);

    const badges: string[] = [];
    if (cat.badge) badges.push(cat.badge);

    return {
        supplier: "discovercars",
        vertical: "cars",
        supplierOfferRef: `dc-${cat.slug}-${slug(req.pickupLocation)}-${req.pickupDate}`,
        title: `${cat.name} Car Rental · ${req.pickupLocation}`,
        subtitle: `${cat.subtitle} · ${days} day${days !== 1 ? "s" : ""} · ${req.pickupDate} → ${req.dropoffDate}`,
        price: { amount: totalEstimate, currency: req.currency },
        badges: badges.length ? badges : undefined,
        deeplink,
        metadata: {
            category: cat.slug,
            perDay: cat.estimatePerDay,
            days,
            pickupLocation: req.pickupLocation,
            dropoffLocation: req.dropoffLocation ?? req.pickupLocation,
        },
    } satisfies Offer;
}

function buildDeeplink(req: CarSearchRequest): string {
    const marker = env.travelpayouts.marker;

    // Build the DiscoverCars search URL
    const dcUrl = new URL("/search", DISCOVERCARS_BASE);
    dcUrl.searchParams.set("pickup", req.pickupLocation);
    if (req.dropoffLocation) {
        dcUrl.searchParams.set("dropoff", req.dropoffLocation);
    }
    dcUrl.searchParams.set("pickup_date", req.pickupDate);
    dcUrl.searchParams.set("dropoff_date", req.dropoffDate);
    dcUrl.searchParams.set("pickup_time", req.pickupTime);
    dcUrl.searchParams.set("dropoff_time", req.dropoffTime);
    dcUrl.searchParams.set("driver_age", String(req.driverAge));

    // Wrap through Travelpayouts redirector
    const tp = new URL(TP_REDIRECT);
    tp.searchParams.set("marker", marker);
    tp.searchParams.set("trs", "265825");
    tp.searchParams.set("p", "4114");
    tp.searchParams.set("u", dcUrl.toString());
    return tp.toString();
}

function computeDays(pickup: string, dropoff: string): number {
    const d1 = new Date(pickup);
    const d2 = new Date(dropoff);
    const diff = Math.ceil((d2.getTime() - d1.getTime()) / (1_000 * 60 * 60 * 24));
    return Math.max(diff, 1);
}

function slug(s: string): string {
    return s.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
}
