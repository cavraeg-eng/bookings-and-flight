import type { ClickRequest, Offer } from "@baf/shared";

/**
 * Simple in-memory click log for the MVP.
 * Swap with Prisma + Postgres once a DB is wired up — same shape.
 */

export type ClickRecord = {
    clickId: string;
    createdAt: number;
    offer: Offer;
    searchId?: string;
    visitor?: ClickRequest["visitor"];
    converted: boolean;
    convertedAt?: number;
    conversionValue?: number;
    conversionCurrency?: string;
};

const store = new Map<string, ClickRecord>();

export const clickStore = {
    put(record: ClickRecord) {
        store.set(record.clickId, record);
    },
    get(clickId: string): ClickRecord | undefined {
        return store.get(clickId);
    },
    markConverted(
        clickId: string,
        value: number,
        currency: string,
    ): ClickRecord | undefined {
        const r = store.get(clickId);
        if (!r) return undefined;
        r.converted = true;
        r.convertedAt = Date.now();
        r.conversionValue = value;
        r.conversionCurrency = currency;
        return r;
    },
    size(): number {
        return store.size;
    },
};
