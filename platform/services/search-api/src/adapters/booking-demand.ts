import type { HotelSearchRequest, Offer } from "@baf/shared";
import { env } from "../config/env.js";
import { SupplierUnavailableError, type SupplierAdapter } from "./types.js";

const PROD_BASE = "https://demandapi.booking.com/3.1";
const SANDBOX_BASE = "https://demandapi-sandbox.booking.com/3.1";
const SEARCH_URL = "/accommodations/search";
const CITIES_URL = "/common/locations/cities";
const TIMEOUT_MS = 10_000;

type BookingSearchResponse = {
    data?: BookingAccommodation[];
};

type BookingAccommodation = {
    id?: number | string;
    name?: string;
    review_score?: number;
    review_count?: number;
    currency?: string;
    price?: {
        total?: number;
        book?: number;
        base?: number;
        currency?: string;
    };
    cheapest_product?: {
        price?: {
            total?: number;
            book?: number;
            base?: number;
            currency?: string;
        };
        url?: string;
        deep_link?: string;
    };
    url?: string;
    deep_link?: string;
    address?: string;
    city?: string;
    district?: string;
    accommodation_type_name?: string;
    checkin?: { from?: string; until?: string };
    checkout?: { from?: string; until?: string };
};

type BookingCityLookupResponse = {
    data?: BookingCity[];
};

type BookingCity = {
    id?: number;
    name?: string;
};

export const bookingDemandAdapter: SupplierAdapter = {
    id: "booking-demand",
    mode: "real",
    verticals: ["hotels"],

    isConfigured: () => Boolean(env.booking.affiliateId && env.booking.apiToken),

    async searchHotels(req: HotelSearchRequest): Promise<Offer[]> {
        const cityId = await resolveCityId(req.destination);
        if (!cityId) {
            throw new SupplierUnavailableError(
                "booking-demand",
                `Could not resolve Booking city for "${req.destination}"`,
            );
        }

        const response = await postJson<BookingSearchResponse>(
            `${baseUrl()}${SEARCH_URL}`,
            buildSearchBody(req, cityId),
            authHeaders(),
        );

        const rows = response.data ?? [];

        return rows
            .map((row, index) => normalizeAccommodation(row, req, index))
            .filter((row): row is Offer => Boolean(row))
            .sort((a, b) => a.price.amount - b.price.amount);
    },
};

function baseUrl(): string {
    return env.booking.useSandbox ? SANDBOX_BASE : PROD_BASE;
}

function authHeaders(): Record<string, string> {
    return {
        Authorization: `Bearer ${env.booking.apiToken}`,
        "X-Affiliate-Id": env.booking.affiliateId,
        "Content-Type": "application/json",
    };
}

async function resolveCityId(destination: string): Promise<number | null> {
    const response = await postJson<BookingCityLookupResponse>(
        `${baseUrl()}${CITIES_URL}`,
        {
            rows: 20,
            languages: ["en-gb"],
        },
        authHeaders(),
    );

    const target = destination.trim().toLowerCase();
    const exact = (response.data ?? []).find((city) => city.name?.trim().toLowerCase() === target);
    if (exact?.id) return exact.id;

    const partial = (response.data ?? []).find((city) =>
        city.name?.trim().toLowerCase().includes(target),
    );
    return partial?.id ?? null;
}

function buildSearchBody(req: HotelSearchRequest, cityId: number) {
    return {
        city: cityId,
        checkin: req.checkIn,
        checkout: req.checkOut,
        currency: req.currency,
        booker: {
            country: "us",
            platform: "desktop",
        },
        guests: {
            number_of_rooms: req.rooms,
            adults: req.adults,
            children: Array.from({ length: req.children }, () => 8),
        },
    };
}

function normalizeAccommodation(
    row: BookingAccommodation,
    req: HotelSearchRequest,
    index: number,
): Offer | null {
    const pricing = row.cheapest_product?.price ?? row.price;
    const amount = pickPrice(pricing);
    const deeplink =
        row.cheapest_product?.deep_link ??
        row.cheapest_product?.url ??
        row.deep_link ??
        row.url;

    if (!row.name || !amount || !deeplink) {
        return null;
    }

    const reviewScore = typeof row.review_score === "number" ? row.review_score : undefined;
    const reviewCount = typeof row.review_count === "number" ? row.review_count : undefined;
    const typeName = row.accommodation_type_name ?? "Hotel";
    const locationBits = [row.city, row.district].filter(Boolean).join(" · ");
    const subtitleParts = [
        typeName,
        locationBits || req.destination,
        reviewScore ? `${reviewScore.toFixed(1)}/10` : undefined,
        reviewCount ? `(${reviewCount.toLocaleString()} reviews)` : undefined,
    ].filter(Boolean);

    const badges: string[] = [];
    if (index === 0) badges.push("Cheapest live");
    if (reviewScore && reviewScore >= 9) badges.push("Top rated");

    return {
        supplier: "booking",
        vertical: "hotels",
        supplierOfferRef: `booking-${row.id ?? slug(row.name)}-${req.checkIn}-${req.checkOut}`,
        title: `${row.name} · ${req.destination}`,
        subtitle: subtitleParts.join(" · "),
        price: {
            amount,
            currency: pricing?.currency ?? row.currency ?? req.currency,
        },
        badges: badges.length ? badges : undefined,
        deeplink,
        metadata: {
            bookingId: row.id,
            reviewScore,
            reviewCount,
            address: row.address,
            city: row.city,
            district: row.district,
            typeName,
            checkinWindow: row.checkin,
            checkoutWindow: row.checkout,
        },
    } satisfies Offer;
}

function pickPrice(
    price:
        | {
              total?: number;
              book?: number;
              base?: number;
              currency?: string;
          }
        | undefined,
): number | null {
    if (!price) return null;
    const candidate = [price.total, price.book, price.base].find(
        (value) => typeof value === "number",
    );
    return typeof candidate === "number" ? candidate : null;
}

async function postJson<T>(
    url: string,
    body: unknown,
    headers: Record<string, string>,
): Promise<T> {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), TIMEOUT_MS);
    try {
        const resp = await fetch(url, {
            method: "POST",
            headers,
            body: JSON.stringify(body),
            signal: controller.signal,
        });

        if (!resp.ok) {
            throw new Error(`booking-demand HTTP ${resp.status}`);
        }

        return (await resp.json()) as T;
    } finally {
        clearTimeout(timer);
    }
}

function slug(s: string): string {
    return s.toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "");
}