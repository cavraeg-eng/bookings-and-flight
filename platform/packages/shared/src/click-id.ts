import { createHmac, randomBytes, timingSafeEqual } from "node:crypto";

/**
 * HMAC-signed click IDs.
 *
 * Format: <12-byte random base64url>.<16-char hex MAC>
 * - Random payload prevents guessing.
 * - MAC prevents tampering and lets /go/:clickId reject fabricated IDs
 *   without hitting a DB (fail fast on bad input).
 * - timingSafeEqual prevents timing oracles.
 *
 * The full clickId is stored in the DB / click log so the redirect
 * handler can look up the original destination.
 */

const MAC_LEN = 16; // hex chars, i.e. 8 bytes of HMAC-SHA256 truncated

function mac(secret: string, payload: string): string {
    return createHmac("sha256", secret).update(payload).digest("hex").slice(0, MAC_LEN);
}

export function mintClickId(secret: string): string {
    if (!secret) throw new Error("CLICK_HMAC_SECRET is required to mint click IDs");
    const payload = randomBytes(12).toString("base64url");
    const tag = mac(secret, payload);
    return `${payload}.${tag}`;
}

export function verifyClickId(secret: string, clickId: string): boolean {
    if (!secret || !clickId) return false;
    const dot = clickId.lastIndexOf(".");
    if (dot <= 0 || dot >= clickId.length - 1) return false;
    const payload = clickId.slice(0, dot);
    const tag = clickId.slice(dot + 1);
    if (tag.length !== MAC_LEN) return false;

    const expected = mac(secret, payload);
    const a = Buffer.from(tag, "hex");
    const b = Buffer.from(expected, "hex");
    if (a.length !== b.length || a.length === 0) return false;
    try {
        return timingSafeEqual(a, b);
    } catch {
        return false;
    }
}
