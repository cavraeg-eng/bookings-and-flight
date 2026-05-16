import type { FastifyPluginAsync } from "fastify";
import { FlightSearchRequestSchema } from "@baf/shared";
import { runSearch } from "./_runSearch.js";
import { prisma } from "../infra/db.js";

export const flightRoutes: FastifyPluginAsync = async (app) => {
    app.post("/search/flights", async (req, reply) => {
        const parsed = FlightSearchRequestSchema.safeParse(req.body);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }
        const result = await runSearch("flights", parsed.data, app.log);

        // Fire-and-forget: log search session without blocking the response
        prisma.searchSession
            .create({
                data: {
                    vertical: "flights",
                    origin: parsed.data.origin,
                    destination: parsed.data.destination,
                    departDate: parsed.data.depart,
                    returnDate: parsed.data.return,
                    adults: parsed.data.adults,
                    resultCount: result.offers.length,
                },
            })
            .catch((err: unknown) => app.log.warn({ err }, "failed to log search session"));

        return result;
    });
};
