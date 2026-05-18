import "node:process";

const port = Number(process.env.PORT ?? 4050);
const nodeEnv = process.env.NODE_ENV ?? "development";
const clickHmacSecret =
    process.env.CLICK_HMAC_SECRET ??
    // Safe default for local dev only — production fails at startup if unset.
    (nodeEnv === "production" ? "" : "dev-secret-change-me");
const postbackSecret =
    process.env.BAF_POSTBACK_SECRET ??
    process.env.POSTBACK_SECRET ??
    // Local-only fallback keeps dev setup simple; production must configure the WordPress bridge secret.
    (nodeEnv === "production" ? "" : clickHmacSecret);

type SupplierCredentialSyncPayload = {
    travelpayouts?: {
        token?: unknown;
        marker?: unknown;
    };
    booking?: {
        affiliateId?: unknown;
        apiToken?: unknown;
        useSandbox?: unknown;
    };
    viator?: {
        apiKey?: unknown;
        partnerId?: unknown;
    };
    discovercars?: {
        partnerId?: unknown;
    };
    kiwi?: {
        affiliateId?: unknown;
    };
};

type CredentialStatus = {
    travelpayouts: {
        configured: boolean;
        tokenPresent: boolean;
        markerPresent: boolean;
    };
    booking: {
        configured: boolean;
        affiliateIdPresent: boolean;
        apiTokenPresent: boolean;
        sandbox: boolean;
    };
    viator: {
        configured: boolean;
        apiKeyPresent: boolean;
        partnerIdPresent: boolean;
    };
    discovercars: {
        configured: boolean;
        partnerIdPresent: boolean;
    };
    kiwi: {
        configured: boolean;
        affiliateIdPresent: boolean;
    };
};

/** Typed runtime env for the search-api. Fails fast on missing critical vars. */
export const env = {
    port,
    nodeEnv,
    clickHmacSecret,
    postbackSecret,
    webOrigin: process.env.WEB_ORIGIN ?? "http://localhost:3000",
    apiBase: process.env.PUBLIC_API_BASE ?? process.env.API_BASE ?? `http://localhost:${port}`,

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

if (env.nodeEnv === "production" && !env.postbackSecret) {
    throw new Error("BAF_POSTBACK_SECRET must be set in production");
}

function stringValue(value: unknown): string {
    return typeof value === "string" ? value.trim() : "";
}

export function syncSupplierCredentials(payload: SupplierCredentialSyncPayload): CredentialStatus {
    if (payload.travelpayouts) {
        env.travelpayouts.token = stringValue(payload.travelpayouts.token);
        env.travelpayouts.marker = stringValue(payload.travelpayouts.marker);
    }

    if (payload.booking) {
        env.booking.affiliateId = stringValue(payload.booking.affiliateId);
        env.booking.apiToken = stringValue(payload.booking.apiToken);
        env.booking.useSandbox = payload.booking.useSandbox === true;
    }

    if (payload.viator) {
        env.viator.apiKey = stringValue(payload.viator.apiKey);
        env.viator.partnerId = stringValue(payload.viator.partnerId);
    }

    if (payload.discovercars) {
        env.discovercars.partnerId = stringValue(payload.discovercars.partnerId);
    }

    if (payload.kiwi) {
        env.kiwi.affiliateId = stringValue(payload.kiwi.affiliateId);
    }

    return credentialStatus();
}

export function credentialStatus(): CredentialStatus {
    return {
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
    };
}
