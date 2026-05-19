/**
 * Supplier identifiers — single source of truth across the monorepo.
 * Adding a supplier: add the slug here, the host(s) to HOST_ALLOWLIST, and an adapter.
 */

export const SUPPLIERS = [
    "travelpayouts",
    "kiwi",
    "booking",
    "viator",
    "discovercars",
] as const;

export type SupplierId = (typeof SUPPLIERS)[number];

export const VERTICALS = ["flights", "hotels", "cars", "activities", "packages"] as const;
export type Vertical = (typeof VERTICALS)[number];

/**
 * Open-redirect allowlist.
 *
 * Every host a `supplierDeeplink` is ALLOWED to redirect to must appear here.
 * Checked at both POST /clicks (pre-persist) and GET /go/:clickId (pre-302).
 *
 * Subdomains are matched by suffix: e.g. "booking.com" matches "www.booking.com"
 * and "secure.booking.com" but NOT "mybooking.com".
 *
 * NOTE: `tp.media` is Travelpayouts' own redirector — allowed for suppliers
 * brokered via TP (Booking.com, DiscoverCars, Viator). TP is trusted to only
 * 302 onward to legitimate partner URLs.
 */
export const SUPPLIER_HOST_ALLOWLIST: Readonly<Record<SupplierId, readonly string[]>> = {
    travelpayouts: ["tp.media", "travelpayouts.com", "aviasales.com", "hotellook.com"],
    kiwi: ["kiwi.com"],
    booking: ["booking.com", "tp.media"],
    viator: ["viator.com", "tp.media"],
    discovercars: ["discovercars.com", "tp.media"],
};

/**
 * Returns true if a URL's host is permitted for the named supplier.
 * Rejects non-https, malformed URLs, and any host not on the list.
 */
export function isAllowedSupplierUrl(supplier: SupplierId, rawUrl: string): boolean {
    let url: URL;
    try {
        url = new URL(rawUrl);
    } catch {
        return false;
    }
    if (url.protocol !== "https:") return false;
    const hostLower = url.hostname.toLowerCase();
    const allowed = SUPPLIER_HOST_ALLOWLIST[supplier];
    if (!allowed) return false;
    return allowed.some((h) => hostLower === h || hostLower.endsWith("." + h));
}
