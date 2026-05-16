import Fastify from "fastify";
import cors from "@fastify/cors";
import rateLimit from "@fastify/rate-limit";
import { env } from "./config/env.js";
import { healthRoutes } from "./routes/health.js";
import { flightRoutes } from "./routes/flights.js";
import { hotelRoutes } from "./routes/hotels.js";
import { carRoutes } from "./routes/cars.js";
import { activityRoutes } from "./routes/activities.js";
import { clickRoutes } from "./routes/clicks.js";
import { integrationsRoutes } from "./routes/integrations.js";
import { autocompleteRoutes } from "./routes/autocomplete.js";
import { adapters } from "./adapters/registry.js";

export function buildApp() {
    const app = Fastify({
        logger: {
            level: env.nodeEnv === "production" ? "info" : "debug",
            transport:
                env.nodeEnv === "production"
                    ? undefined
                    : { target: "pino-pretty", options: { colorize: true, translateTime: "HH:MM:ss" } },
        },
        trustProxy: true,
        disableRequestLogging: false,
    });

    app.register(cors, {
        origin: [env.webOrigin, /\.local$/, /localhost(:\d+)?$/],
        credentials: true,
    });

    app.register(rateLimit, {
        max: 120,
        timeWindow: "1 minute",
    });

    app.register(healthRoutes);
    app.register(flightRoutes);
    app.register(hotelRoutes);
    app.register(carRoutes);
    app.register(activityRoutes);
    app.register(clickRoutes);
    app.register(integrationsRoutes);
    app.register(autocompleteRoutes);

    app.log.info(
        { active: adapters.map((a) => a.id) },
        `active supplier adapters: ${adapters.map((a) => a.id).join(", ") || "none"}`,
    );

    app.setErrorHandler((err, _req, reply) => {
        app.log.error({ err }, "unhandled error");
        reply.status(err.statusCode ?? 500).send({
            error: err.name ?? "INTERNAL_ERROR",
            message: err.message,
        });
    });

    return app;
}
