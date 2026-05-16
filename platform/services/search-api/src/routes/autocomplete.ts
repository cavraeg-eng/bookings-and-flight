import type { FastifyPluginAsync } from "fastify";
import { z } from "zod";
import { airports, type Airport } from "../data/airports.js";

const QuerySchema = z.object({
    q: z.string().min(1).max(50),
    type: z.enum(["airport", "city"]).optional(),
});

interface AutocompleteResult {
    code: string;
    name: string;
    city: string;
    country: string;
    countryCode: string;
    type: "airport";
}

/* Pre-compute lower-case lookup fields once at startup. */
const indexedAirports = airports.map((a) => ({
    ...a,
    _codeLower: a.code.toLowerCase(),
    _cityLower: a.city.toLowerCase(),
    _nameLower: a.name.toLowerCase(),
    _countryLower: a.country.toLowerCase(),
}));

export const autocompleteRoutes: FastifyPluginAsync = async (app) => {
    app.get("/autocomplete", async (req, reply) => {
        const parsed = QuerySchema.safeParse(req.query);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }

        const { q } = parsed.data;
        const needle = q.toLowerCase().trim();
        if (!needle) {
            return { results: [] };
        }

        const exactCode: AutocompleteResult[] = [];
        const cityPrefix: (Airport & { _score: number })[] = [];
        const namePrefix: (Airport & { _score: number })[] = [];

        for (const a of indexedAirports) {
            // Exact IATA code match (highest priority)
            if (a._codeLower === needle) {
                exactCode.push(toResult(a));
                continue;
            }

            // City name prefix match (second priority)
            if (a._cityLower.startsWith(needle)) {
                cityPrefix.push({ ...a, _score: a.popularity });
                continue;
            }

            // Airport name prefix match (third priority)
            if (a._nameLower.startsWith(needle)) {
                namePrefix.push({ ...a, _score: a.popularity });
                continue;
            }

            // Also match code prefix (e.g. "jf" matches "JFK")
            if (needle.length < 3 && a._codeLower.startsWith(needle)) {
                namePrefix.push({ ...a, _score: a.popularity });
                continue;
            }

            // Country name prefix (lower priority)
            if (a._countryLower.startsWith(needle)) {
                namePrefix.push({ ...a, _score: a.popularity });
            }
        }

        // Sort secondary buckets by popularity descending
        cityPrefix.sort((a, b) => b._score - a._score);
        namePrefix.sort((a, b) => b._score - a._score);

        const results: AutocompleteResult[] = [
            ...exactCode,
            ...cityPrefix.map(toResult),
            ...namePrefix.map(toResult),
        ].slice(0, 10);

        return { results };
    });
};

function toResult(a: Airport | (Airport & Record<string, unknown>)): AutocompleteResult {
    return {
        code: a.code,
        name: a.name,
        city: a.city,
        country: a.country,
        countryCode: a.countryCode,
        type: "airport",
    };
}
