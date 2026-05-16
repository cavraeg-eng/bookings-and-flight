import "node:process";

/** Typed runtime env for the search-api. Fails fast on missing critical vars. */
export const env = {
    port: Number(process.env.PORT ?? 4050),
    nodeEnv: process.env.NODE_ENV ?? "development",
    clickHmacSecret:
        process.env.CLICK_HMAC_SECRET ??
        // Safe default for local dev only — production fails at startup if unset.
        (process.env.NODE_ENV === "production" ? "" : "dev-secret-change-me"),
    webOrigin: process.env.WEB_ORIGIN ?? "http://localhost:3000",

    travelpayouts: {
        token: process.env.TRAVELPAYOUTS_API_TOKEN ?? "",
        marker: process.env.TRAVELPAYOUTS_MARKER ?? "",
    },
    booking: {
        affiliateId: process.env.BOOKING_AFFILIATE_ID ?? "",
        apiToken: process.env.BOOKING_API_TOKEN ?? "",
        useSandbox: process.env.BOOKING_USE_SANDBOX === "true",
    },
    viator: {
        apiKey: process.env.VIATOR_API_KEY ?? "",
        partnerId: process.env.VIATOR_PARTNER_ID ?? "",
    },
    discovercars: {
        partnerId: process.env.DISCOVERCARS_PARTNER_ID ?? "",
    },
    kiwi: {
        affiliateId: process.env.KIWI_AFFILIATE_ID ?? "",
    },
};

if (env.nodeEnv === "production" && !env.clickHmacSecret) {
    throw new Error("CLICK_HMAC_SECRET must be set in production");
}
