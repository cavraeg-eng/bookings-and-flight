import type { FastifyPluginAsync } from "fastify";
import { CarSearchRequestSchema } from "@baf/shared";
import { runSearch } from "./_runSearch.js";

export const carRoutes: FastifyPluginAsync = async (app) => {
    app.post("/search/cars", async (req, reply) => {
        const parsed = CarSearchRequestSchema.safeParse(req.body);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }
        return runSearch("cars", parsed.data, app.log);
    });
};
