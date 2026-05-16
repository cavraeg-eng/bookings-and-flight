import type { FastifyPluginAsync } from "fastify";
import { allAdapters, adapters } from "../adapters/registry.js";
import { env } from "../config/env.js";

export const integrationsRoutes: FastifyPluginAsync = async (app) => {
    app.get("/integrations", async () => {
        const activeByVertical = {
            flights: adapters.filter((a) => a.verticals.includes("flights")).map((a) => a.id),
            hotels: adapters.filter((a) => a.verticals.includes("hotels")).map((a) => a.id),
            cars: adapters.filter((a) => a.verticals.includes("cars")).map((a) => a.id),
            activities: adapters.filter((a) => a.verticals.includes("activities")).map((a) => a.id),
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
            credentials: {
                travelpayouts: {
                    configured: Boolean(env.travelpayouts.token && env.travelpayouts.marker),
                    tokenPresent: Boolean(env.travelpayouts.token),
                    markerPresent: Boolean(env.travelpayouts.marker),
                },
                booking: {
                    configured: Boolean(env.booking.affiliateId && env.booking.apiToken),
                    affiliateIdPresent: Boolean(env.booking.affiliateId),
                    apiTokenPresent: Boolean(env.booking.apiToken),
                    sandbox: env.booking.useSandbox,
                },
                viator: {
                    configured: Boolean(env.viator.apiKey && env.viator.partnerId),
                    apiKeyPresent: Boolean(env.viator.apiKey),
                    partnerIdPresent: Boolean(env.viator.partnerId),
                },
                discovercars: {
                    configured: Boolean(env.discovercars.partnerId),
                    partnerIdPresent: Boolean(env.discovercars.partnerId),
                },
                kiwi: {
                    configured: Boolean(env.kiwi.affiliateId),
                    affiliateIdPresent: Boolean(env.kiwi.affiliateId),
                },
            },
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
};
