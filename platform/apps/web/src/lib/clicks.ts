import type { Offer } from "@baf/shared";

export async function openTrackedOffer(offer: Offer, searchId?: string) {
    const bookingWindow = window.open("about:blank", "_blank");
    if (bookingWindow) {
        bookingWindow.opener = null;
    }

    let redirectUrl = offer.deeplink;

    try {
        const res = await fetch("/api/clicks", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ offer, ...(searchId ? { searchId } : {}) }),
        });

        if (res.ok) {
            const data = await res.json();
            if (typeof data.redirectUrl === "string" && data.redirectUrl.length > 0) {
                redirectUrl = data.redirectUrl;
            }
        }
    } catch {
        // Tracking should never block the outbound supplier handoff.
    }

    if (bookingWindow && !bookingWindow.closed) {
        bookingWindow.location.href = redirectUrl;
    } else {
        window.location.href = redirectUrl;
    }
}
