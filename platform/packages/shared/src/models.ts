import { z } from "zod";
import { SUPPLIERS, VERTICALS } from "./suppliers.js";

/**
 * Normalized offer model. Every supplier adapter maps into this shape
 * so the UI can rank and compare apples to apples.
 */
export const MoneySchema = z.object({
    amount: z.number().nonnegative(),
    currency: z.string().length(3),
});
export type Money = z.infer<typeof MoneySchema>;

export const OfferSchema = z.object({
    supplier: z.enum(SUPPLIERS),
    vertical: z.enum(VERTICALS),
    supplierOfferRef: z.string().min(1).max(512),
    title: z.string().min(1).max(240),
    subtitle: z.string().max(240).optional(),
    image: z.string().url().optional(),
    price: MoneySchema,
    strikePrice: MoneySchema.optional(),
    badges: z.array(z.string().max(40)).max(6).optional(),
    deeplink: z.string().url(),
    metadata: z.record(z.string(), z.unknown()).optional(),
});
export type Offer = z.infer<typeof OfferSchema>;

const DateStr = z.string().regex(/^\d{4}-\d{2}-\d{2}$/, "YYYY-MM-DD required");

/* -------------------------------------------------------------------------- */
/* FLIGHTS                                                                    */
/* -------------------------------------------------------------------------- */

export const FlightSearchRequestSchema = z.object({
    origin: z.string().min(3).max(8).toUpperCase(),
    destination: z.string().min(3).max(8).toUpperCase(),
    depart: DateStr,
    return: DateStr.optional(),
    adults: z.number().int().min(1).max(9).default(1),
    children: z.number().int().min(0).max(8).default(0),
    infants: z.number().int().min(0).max(4).default(0),
    cabin: z.enum(["economy", "premium", "business", "first"]).default("economy"),
    currency: z.string().length(3).default("USD"),
    locale: z.string().min(2).max(10).default("en-US"),
});
export type FlightSearchRequest = z.infer<typeof FlightSearchRequestSchema>;

/* -------------------------------------------------------------------------- */
/* HOTELS                                                                     */
/* -------------------------------------------------------------------------- */

export const HotelSearchRequestSchema = z.object({
    /** City, region, or place name (e.g. "Paris", "New York", "London"). */
    destination: z.string().min(2).max(80),
    checkIn: DateStr,
    checkOut: DateStr,
    adults: z.number().int().min(1).max(9).default(2),
    children: z.number().int().min(0).max(8).default(0),
    rooms: z.number().int().min(1).max(5).default(1),
    currency: z.string().length(3).default("USD"),
    locale: z.string().min(2).max(10).default("en-US"),
});
export type HotelSearchRequest = z.infer<typeof HotelSearchRequestSchema>;

/* -------------------------------------------------------------------------- */
/* CARS                                                                       */
/* -------------------------------------------------------------------------- */

export const CarSearchRequestSchema = z.object({
    /** Pickup IATA airport code or city name. */
    pickupLocation: z.string().min(3).max(80),
    /** If omitted, dropoff is same as pickup. */
    dropoffLocation: z.string().min(3).max(80).optional(),
    pickupDate: DateStr,
    dropoffDate: DateStr,
    /** 24h time "HH:MM", default 10:00. */
    pickupTime: z.string().regex(/^\d{2}:\d{2}$/).default("10:00"),
    dropoffTime: z.string().regex(/^\d{2}:\d{2}$/).default("10:00"),
    driverAge: z.number().int().min(18).max(99).default(30),
    currency: z.string().length(3).default("USD"),
    locale: z.string().min(2).max(10).default("en-US"),
});
export type CarSearchRequest = z.infer<typeof CarSearchRequestSchema>;

/* -------------------------------------------------------------------------- */
/* ACTIVITIES                                                                 */
/* -------------------------------------------------------------------------- */

export const ActivitySearchRequestSchema = z.object({
    destination: z.string().min(2).max(80),
    /** Optional date range; if only `from` is given, show activities on that day. */
    from: DateStr,
    to: DateStr.optional(),
    adults: z.number().int().min(1).max(9).default(2),
    children: z.number().int().min(0).max(8).default(0),
    currency: z.string().length(3).default("USD"),
    locale: z.string().min(2).max(10).default("en-US"),
});
export type ActivitySearchRequest = z.infer<typeof ActivitySearchRequestSchema>;

/* -------------------------------------------------------------------------- */
/* SHARED RESPONSE TYPES                                                      */
/* -------------------------------------------------------------------------- */

export const SearchResponseSchema = z.object({
    searchId: z.string().min(1),
    partial: z.boolean(),
    offers: z.array(OfferSchema),
    warnings: z.array(z.string()).optional(),
});
export type SearchResponse = z.infer<typeof SearchResponseSchema>;

/** POST /clicks — browser hands us the offer it wants to click. */
export const ClickRequestSchema = z.object({
    offer: OfferSchema,
    searchId: z.string().optional(),
    visitor: z
        .object({
            utm: z.record(z.string(), z.string()).optional(),
            referrer: z.string().max(2048).optional(),
        })
        .optional(),
});
export type ClickRequest = z.infer<typeof ClickRequestSchema>;

export const ClickResponseSchema = z.object({
    clickId: z.string().min(1),
    redirectUrl: z.string().refine(
        (value) => value.startsWith("/") || z.string().url().safeParse(value).success,
        "Absolute URL or site-relative path required",
    ),
});
export type ClickResponse = z.infer<typeof ClickResponseSchema>;
