import { travelpayoutsAdapter } from "./travelpayouts.js";
import { bookingDemandAdapter } from "./booking-demand.js";
import { hotellookAdapter } from "./hotellook.js";
import { carsAdapter } from "./cars.js";
import { activitiesAdapter } from "./activities.js";
import type { SupplierAdapter } from "./types.js";

/**
 * Central adapter registry.
 *
 * Policy
 *   • Every active backend adapter is listed in `realAdapters`.
 *   • Frontend white-label Trip.com search pages now handle the customer-facing
 *     flight and hotel search experience, while backend adapters remain
 *     available for direct supplier integrations where configured.
 *   • Add a new supplier:
 *       1. Implement it in `adapters/<supplier>.ts` with the `SupplierAdapter` contract.
 *       2. Import and push it to `realAdapters` below.
 *       3. Make sure its hosts are in SUPPLIER_HOST_ALLOWLIST in packages/shared.
 */

const realAdapters: SupplierAdapter[] = [
    travelpayoutsAdapter,
    bookingDemandAdapter,
    hotellookAdapter,
    carsAdapter,
    activitiesAdapter,
];

export const adapters: SupplierAdapter[] = realAdapters.filter((a) => a.isConfigured());

export const allAdapters: SupplierAdapter[] = [
    travelpayoutsAdapter,
    bookingDemandAdapter,
    hotellookAdapter,
    carsAdapter,
    activitiesAdapter,
];

export function adaptersFor(
    vertical: "flights" | "hotels" | "cars" | "activities" | "packages",
): SupplierAdapter[] {
    return adapters.filter((a) => a.verticals.includes(vertical));
}
