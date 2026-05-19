import type { FastifyPluginAsync } from "fastify";
import { HotelSearchRequestSchema } from "@baf/shared";
import { runSearch } from "./_runSearch.js";

export const hotelRoutes: FastifyPluginAsync = async (app) => {
    app.post("/search/hotels", async (req, reply) => {
        const parsed = HotelSearchRequestSchema.safeParse(req.body);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }
        return runSearch("hotels", parsed.data, app.log);
    });
};
