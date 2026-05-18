import type { FastifyPluginAsync } from "fastify";
import { timingSafeEqual } from "node:crypto";
import {
    type ClickRequest,
    ClickRequestSchema,
    isAllowedSupplierUrl,
    mintClickId,
    type Offer,
    verifyClickId,
} from "@baf/shared";
import { env } from "../config/env.js";
import { clickStore } from "../infra/click-store.js";
import { prisma } from "../infra/db.js";

type StoredClick = {
    supplier: Offer["supplier"];
    deeplink: string;
};

function firstHeaderValue(value: string | string[] | undefined): string {
    return Array.isArray(value) ? value[0] ?? "" : value ?? "";
}

function secretsMatch(received: string, expected: string): boolean {
    if (!received || !expected) return false;

    const receivedBuffer = Buffer.from(received);
    const expectedBuffer = Buffer.from(expected);

    return receivedBuffer.length === expectedBuffer.length && timingSafeEqual(receivedBuffer, expectedBuffer);
}

export const clickRoutes: FastifyPluginAsync = async (app) => {
    /**
     * POST /clicks
     * Browser posts the offer it's about to click.
     * We validate the deeplink host, mint an HMAC clickId, log it,
     * and return the internal redirect URL the browser should navigate to.
     */
    app.post("/clicks", async (req, reply) => {
        const parsed = ClickRequestSchema.safeParse(req.body);
        if (!parsed.success) {
            reply.code(400);
            return { error: "INVALID_REQUEST", details: parsed.error.flatten() };
        }
        const { offer, searchId, visitor } = parsed.data;

        if (!isAllowedSupplierUrl(offer.supplier, offer.deeplink)) {
            reply.code(400);
            return { error: "UNSAFE_DEEPLINK", supplier: offer.supplier };
        }

        const clickId = mintClickId(env.clickHmacSecret);

        await persistClick(app, clickId, offer, searchId, visitor);

        return {
            clickId,
            redirectUrl: `/go/${encodeURIComponent(clickId)}`,
        };
    });

    /**
     * GET /go/:clickId
     * Verify HMAC, re-check host allowlist, 302 to the supplier deeplink.
     * Double-gate defense: even if a forged click somehow reached the store,
     * the allowlist re-check would stop the redirect.
     */
    app.get<{ Params: { clickId: string } }>("/go/:clickId", async (req, reply) => {
        const { clickId } = req.params;
        if (!verifyClickId(env.clickHmacSecret, clickId)) {
            reply.code(400);
            return { error: "INVALID_CLICK_ID" };
        }
        const record = await findClick(app, clickId);
        if (!record) {
            reply.code(404);
            return { error: "CLICK_NOT_FOUND" };
        }
        if (!isAllowedSupplierUrl(record.supplier, record.deeplink)) {
            reply.code(400);
            return { error: "UNSAFE_DEEPLINK" };
        }
        reply.header("Cache-Control", "no-store");
        return reply.redirect(record.deeplink, 302);
    });

    /**
     * POST /postbacks/supplier-conversion
     * Called by the WP affiliate-bridge plugin when a supplier postback
     * arrives. Trusts a shared secret header.
     */
    app.post<{
        Body: { clickId: string; value: number; currency: string };
    }>("/postbacks/supplier-conversion", async (req, reply) => {
        const secret = firstHeaderValue(req.headers["x-postback-secret"]);
        if (!secretsMatch(secret, env.postbackSecret)) {
            reply.code(401);
            return { error: "UNAUTHORIZED" };
        }
        const { clickId, value, currency } = req.body ?? ({} as never);
        if (!clickId || typeof value !== "number" || !currency) {
            reply.code(400);
            return { error: "INVALID_PAYLOAD" };
        }

        const converted = await markClickConverted(app, clickId, value, currency);
        if (!converted) {
            reply.code(404);
            return { error: "CLICK_NOT_FOUND" };
        }

        return { ok: true };
    });
};

async function persistClick(
    app: Parameters<FastifyPluginAsync>[0],
    clickId: string,
    offer: Offer,
    searchId?: string,
    visitor?: ClickRequest["visitor"],
) {
    try {
        await prisma.click.create({
            data: {
                clickId,
                supplier: offer.supplier,
                vertical: offer.vertical,
                offerRef: offer.supplierOfferRef,
                deeplink: offer.deeplink,
                price: offer.price.amount,
                currency: offer.price.currency,
            },
        });
    } catch (err) {
        app.log.warn({ err }, "prisma click storage unavailable; using in-memory click store");
        clickStore.put({
            clickId,
            createdAt: Date.now(),
            offer,
            searchId,
            visitor,
            converted: false,
        });
    }
}

async function findClick(app: Parameters<FastifyPluginAsync>[0], clickId: string): Promise<StoredClick | null> {
    const fallback = clickStore.get(clickId);

    try {
        const record = await prisma.click.findUnique({ where: { clickId } });
        if (record) {
            return {
                supplier: record.supplier as Offer["supplier"],
                deeplink: record.deeplink,
            };
        }
    } catch (err) {
        app.log.warn({ err }, "prisma click lookup unavailable; using in-memory click store");
    }

    if (!fallback) {
        return null;
    }

    return {
        supplier: fallback.offer.supplier,
        deeplink: fallback.offer.deeplink,
    };
}

async function markClickConverted(
    app: Parameters<FastifyPluginAsync>[0],
    clickId: string,
    value: number,
    currency: string,
): Promise<boolean> {
    try {
        const existing = await prisma.click.findUnique({ where: { clickId } });
        if (!existing) {
            return Boolean(clickStore.markConverted(clickId, value, currency));
        }

        await prisma.click.update({
            where: { clickId },
            data: {
                converted: true,
                convertedAt: new Date(),
                convValue: value,
                convCurrency: currency,
            },
        });
        return true;
    } catch (err) {
        app.log.warn({ err }, "prisma conversion update unavailable; using in-memory click store");
        return Boolean(clickStore.markConverted(clickId, value, currency));
    }
}
