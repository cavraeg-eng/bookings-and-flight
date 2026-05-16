import type {
    ActivitySearchRequest,
    CarSearchRequest,
    FlightSearchRequest,
    HotelSearchRequest,
    Offer,
} from "@baf/shared";

/**
 * Contract every supplier adapter implements.
 * Each vertical method is optional — an adapter only implements the
 * verticals it actually supports (listed in `verticals`).
 */
export interface SupplierAdapter {
    id: string;
    mode?: "real" | "demo";
    verticals: readonly ("flights" | "hotels" | "cars" | "activities" | "packages")[];
    isConfigured(): boolean;
    searchFlights?(req: FlightSearchRequest): Promise<Offer[]>;
    searchHotels?(req: HotelSearchRequest): Promise<Offer[]>;
    searchCars?(req: CarSearchRequest): Promise<Offer[]>;
    searchActivities?(req: ActivitySearchRequest): Promise<Offer[]>;
}

export class SupplierUnavailableError extends Error {
    statusCode = 503;
    code = "SUPPLIER_UNAVAILABLE";
    constructor(public supplier: string, message = "Supplier not configured") {
        super(`[${supplier}] ${message}`);
        this.name = "SupplierUnavailableError";
    }
}
