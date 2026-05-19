import { buildApp } from "./app.js";
import { env } from "./config/env.js";

const app = buildApp();

app.listen({ port: env.port, host: "0.0.0.0" })
    .then((addr) => {
        app.log.info(`search-api listening on ${addr}`);
    })
    .catch((err) => {
        app.log.error(err, "startup failed");
        process.exit(1);
    });

for (const sig of ["SIGINT", "SIGTERM"] as const) {
    process.on(sig, () => {
        app.log.info(`received ${sig}, shutting down`);
        app.close().finally(() => process.exit(0));
    });
}
