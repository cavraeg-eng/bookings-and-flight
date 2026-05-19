import type { FastifyPluginAsync } from "fastify";
import { ActivitySearchRequestSchema } from "@baf/shared";
import { runSearch } from "./_runSearch.js";

export const activityRoutes: FastifyPluginAsync = async (app) => {
    app.post("/search/activities", async (req, reply) => {
        const parsed = ActivitySearchRequestSchema.safeParse(req.body);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }
        return runSearch("activities", parsed.data, app.log);
    });
};
