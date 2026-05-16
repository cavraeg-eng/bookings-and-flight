import type { FastifyPluginAsync } from "fastify";
import { timingSafeEqual } from "node:crypto";
import {
    ClickRequestSchema,
    isAllowedSupplierUrl,
    mintClickId,
    verifyClickId,
} from "@baf/shared";
import { env } from "../config/env.js";
import { prisma } from "../infra/db.js";

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
        const record = await prisma.click.findUnique({ where: { clickId } });
        if (!record) {
            reply.code(404);
            return { error: "CLICK_NOT_FOUND" };
        }
        if (!isAllowedSupplierUrl(record.supplier as Parameters<typeof isAllowedSupplierUrl>[0], record.deeplink)) {
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

        const existing = await prisma.click.findUnique({ where: { clickId } });
        if (!existing) {
            reply.code(404);
            return { error: "CLICK_NOT_FOUND" };
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

        return { ok: true };
    });
};
