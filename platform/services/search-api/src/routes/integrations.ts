import type { FastifyPluginAsync } from "fastify";
import { allAdapters, configuredAdapters } from "../adapters/registry.js";
import { credentialStatus, env, syncSupplierCredentials } from "../config/env.js";
import { firstHeaderValue, secretsMatch } from "../infra/secrets.js";

type CredentialSyncBody = Parameters<typeof syncSupplierCredentials>[0];

const credentialSyncSchema = {
    body: {
        type: "object",
        additionalProperties: false,
        properties: {
            travelpayouts: {
                type: "object",
                additionalProperties: false,
                properties: {
                    token: { type: "string" },
                    marker: { type: "string" },
                },
            },
            booking: {
                type: "object",
                additionalProperties: false,
                properties: {
                    affiliateId: { type: "string" },
                    apiToken: { type: "string" },
                    useSandbox: { type: "boolean" },
                },
            },
            viator: {
                type: "object",
                additionalProperties: false,
                properties: {
                    apiKey: { type: "string" },
                    partnerId: { type: "string" },
                },
            },
            discovercars: {
                type: "object",
                additionalProperties: false,
                properties: {
                    partnerId: { type: "string" },
                },
            },
            kiwi: {
                type: "object",
                additionalProperties: false,
                properties: {
                    affiliateId: { type: "string" },
                },
            },
        },
    },
} as const;

export const integrationsRoutes: FastifyPluginAsync = async (app) => {
    app.get("/integrations", async () => {
        const activeAdapters = configuredAdapters();
        const activeByVertical = {
            flights: activeAdapters.filter((a) => a.verticals.includes("flights")).map((a) => a.id),
            hotels: activeAdapters.filter((a) => a.verticals.includes("hotels")).map((a) => a.id),
            cars: activeAdapters.filter((a) => a.verticals.includes("cars")).map((a) => a.id),
            activities: activeAdapters.filter((a) => a.verticals.includes("activities")).map((a) => a.id),
        };

        return {
            publicSite: env.webOrigin,
            apiBase: env.apiBase,
            activeByVertical,
            adapters: allAdapters.map((adapter) => ({
                id: adapter.id,
                mode: adapter.mode ?? "real",
                verticals: adapter.verticals,
                configured: adapter.isConfigured(),
            })),
            credentials: credentialStatus(),
            notes: {
                flights:
                    activeByVertical.flights.includes("travelpayouts")
                        ? "Live Travelpayouts/Aviasales adapter is active."
                        : "Frontend flight search uses the Trip.com white-label widget and Travelpayouts custom links.",
                hotels: activeByVertical.hotels.includes("booking-demand")
                    ? `Live Booking Demand API adapter is active${env.booking.useSandbox ? " (sandbox mode)." : "."}`
                    : "Frontend hotel search uses the Trip.com white-label widget and Travelpayouts custom links.",
                cars:
                    "No live backend car-rental adapter is configured right now.",
                activities:
                    "No live backend activities adapter is configured right now.",
            },
        };
    });

    app.post<{ Body: CredentialSyncBody }>(
        "/integrations/credentials",
        { schema: credentialSyncSchema },
        async (req, reply) => {
            const secret = firstHeaderValue(req.headers["x-postback-secret"]);
            if (!secretsMatch(secret, env.postbackSecret)) {
                reply.code(401);
                return { error: "UNAUTHORIZED" };
            }

            const syncResult = syncSupplierCredentials(req.body ?? {});
            app.log.info(
                {
                    configured: Object.fromEntries(
                        Object.entries(syncResult.credentials).map(([supplier, status]) => [supplier, status.configured]),
                    ),
                    persisted: syncResult.persisted,
                },
                "synced supplier credentials from WordPress bridge",
            );

            return {
                ok: true,
                credentials: syncResult.credentials,
                credentialStore: {
                    persisted: syncResult.persisted,
                },
                activeAdapters: configuredAdapters().map((adapter) => adapter.id),
            };
        },
    );
};
