import type { ActivitySearchRequest, Offer } from "@baf/shared";
import { env } from "../config/env.js";
import * as cache from "../infra/cache.js";
import type { SupplierAdapter } from "./types.js";

/**
 * Activities / tours deep-link adapter via Travelpayouts.
 *
 * No real-time pricing API is available without Viator credentials,
 * so we generate curated offers for popular activity categories with
 * affiliate deep links through the Travelpayouts redirector (tp.media).
 *
 * Each offer links to a search for that activity type in the requested
 * destination city.
 */

const TP_REDIRECT = "https://tp.media/r";
const VIATOR_BASE = "https://www.viator.com";
const CACHE_TTL_MS = 30 * 60 * 1_000; // 30 minutes

interface ActivityCategory {
    name: string;
    slug: string;
    subtitle: string;
    estimatePrice: number; // USD baseline per person
    badge?: string;
    viatorPath: string; // path segment on viator.com
}

const ACTIVITY_CATEGORIES: ActivityCategory[] = [
    {
        name: "City Tours",
        slug: "city-tours",
        subtitle: "Guided sightseeing & walking tours",
        estimatePrice: 45,
        badge: "Popular",
        viatorPath: "/searchResults/all",
    },
    {
        name: "Day Trips",
        slug: "day-trips",
        subtitle: "Full-day excursions to nearby attractions",
        estimatePrice: 85,
        viatorPath: "/searchResults/all",
    },
    {
        name: "Museum Passes",
        slug: "museum-passes",
        subtitle: "Skip-the-line tickets & combo passes",
        estimatePrice: 35,
        badge: "Skip the line",
        viatorPath: "/searchResults/all",
    },
    {
        name: "Adventure Activities",
        slug: "adventure",
        subtitle: "Outdoor adventures & extreme sports",
        estimatePrice: 75,
        viatorPath: "/searchResults/all",
    },
    {
        name: "Food Tours",
        slug: "food-tours",
        subtitle: "Local cuisine & culinary experiences",
        estimatePrice: 65,
        badge: "Trending",
        viatorPath: "/searchResults/all",
    },
];

export const activitiesAdapter: SupplierAdapter = {
    id: "viator",
    mode: "real",
    verticals: ["activities"],

    isConfigured: () => Boolean(env.travelpayouts.marker),

    async searchActivities(req: ActivitySearchRequest): Promise<Offer[]> {
        const cacheKey = cache.generateKey(
            "activities",
            req.destination,
            req.from,
            req.to ?? "",
            String(req.adults),
            req.currency,
        );

        const cached = cache.get<Offer[]>(cacheKey);
        if (cached) return cached;

        const offers = ACTIVITY_CATEGORIES.map((cat) =>
            buildCategoryOffer(cat, req),
        );

        cache.set(cacheKey, offers, CACHE_TTL_MS);
        return offers;
    },
};

function buildCategoryOffer(
    cat: ActivityCategory,
    req: ActivitySearchRequest,
): Offer {
    const totalGuests = req.adults + req.children;
    const estimateTotal = Math.round(cat.estimatePrice * totalGuests);
    const deeplink = buildDeeplink(cat, req);

    const badges: string[] = [];
    if (cat.badge) badges.push(cat.badge);

    const dateRange = req.to
        ? `${req.from} → ${req.to}`
        : req.from;

    return {
        supplier: "viator",
        vertical: "activities",
        supplierOfferRef: `viator-${cat.slug}-${slug(req.destination)}-${req.from}`,
        title: `${cat.name} in ${req.destination}`,
        subtitle: `${cat.subtitle} · ${dateRange} · ${totalGuests} guest${totalGuests !== 1 ? "s" : ""}`,
        price: { amount: estimateTotal, currency: req.currency },
        badges: badges.length ? badges : undefined,
        deeplink,
        metadata: {
            category: cat.slug,
            perPerson: cat.estimatePrice,
            destination: req.destination,
        },
    } satisfies Offer;
}

function buildDeeplink(cat: ActivityCategory, req: ActivitySearchRequest): string {
    const marker = env.travelpayouts.marker;

    // Build the Viator search URL
    const viatorUrl = new URL(cat.viatorPath, VIATOR_BASE);
    viatorUrl.searchParams.set("text", `${cat.name} ${req.destination}`);
    viatorUrl.searchParams.set("destId", req.destination);
    if (req.from) viatorUrl.searchParams.set("startDate", req.from);
    if (req.to) viatorUrl.searchParams.set("endDate", req.to);
    viatorUrl.searchParams.set("adults", String(req.adults));

    // Wrap through Travelpayouts redirector
    const tp = new URL(TP_REDIRECT);
    tp.searchParams.set("marker", marker);
    tp.searchParams.set("trs", "265825");
    tp.searchParams.set("p", "6587");
    tp.searchParams.set("u", viatorUrl.toString());
    return tp.toString();
}

function slug(s: string): string {
    return s.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
}
