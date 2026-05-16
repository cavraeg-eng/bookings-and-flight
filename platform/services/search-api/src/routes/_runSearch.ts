import type { FastifyBaseLogger } from "fastify";
import { randomUUID } from "node:crypto";
import type { Offer } from "@baf/shared";
import { adaptersFor } from "../adapters/registry.js";
import { SupplierUnavailableError, type SupplierAdapter } from "../adapters/types.js";

type Vertical = "flights" | "hotels" | "cars" | "activities";

/**
 * Generic fan-out: calls each configured adapter's vertical method in parallel,
 * collects offers, logs failures, and returns a merged, price-sorted list.
 *
 * Why a helper? All four vertical routes do exactly the same dance; extracting
 * it keeps each route file under 30 lines and means one place to add tracing.
 */
export async function runSearch<Req>(
    vertical: Vertical,
    req: Req,
    log: FastifyBaseLogger,
): Promise<{ searchId: string; partial: boolean; offers: Offer[]; warnings?: string[] }> {
    const searchId = randomUUID();
    const allAdapters = adaptersFor(vertical);
    const usable = allAdapters.filter((a) => a.isConfigured() && hasVerticalMethod(a, vertical));

    const warnings: string[] = [];
    const offers: Offer[] = [];

    const results = await Promise.allSettled(
        usable.map((a) => callVertical(a, vertical, req)),
    );

    for (const [i, r] of results.entries()) {
        const a = usable[i];
        if (r.status === "fulfilled") {
            offers.push(...r.value);
        } else if (r.reason instanceof SupplierUnavailableError) {
            warnings.push(`${a.id}: unavailable`);
        } else {
            log.warn({ adapter: a.id, vertical, err: r.reason }, "adapter failed");
            warnings.push(`${a.id}: error`);
        }
    }

    offers.sort((a, b) => a.price.amount - b.price.amount);

    return {
        searchId,
        partial: warnings.length > 0,
        offers,
        warnings: warnings.length ? warnings : undefined,
    };
}

function hasVerticalMethod(a: SupplierAdapter, vertical: Vertical): boolean {
    switch (vertical) {
        case "flights":
            return typeof a.searchFlights === "function";
        case "hotels":
            return typeof a.searchHotels === "function";
        case "cars":
            return typeof a.searchCars === "function";
        case "activities":
            return typeof a.searchActivities === "function";
    }
}

function callVertical<Req>(a: SupplierAdapter, vertical: Vertical, req: Req): Promise<Offer[]> {
    switch (vertical) {
        case "flights":
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            return a.searchFlights!(req as any);
        case "hotels":
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            return a.searchHotels!(req as any);
        case "cars":
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            return a.searchCars!(req as any);
        case "activities":
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            return a.searchActivities!(req as any);
    }
}
