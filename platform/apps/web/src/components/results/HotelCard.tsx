"use client";

import { useState } from "react";
import { Star, Wifi, Car, UtensilsCrossed, Waves, Dumbbell, Sparkles, ExternalLink, MapPin } from "lucide-react";
import { cn } from "@/lib/cn";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface HotelCardProps {
    offer: Offer;
    searchId?: string;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Amenity config                                                     */
/* ------------------------------------------------------------------ */

const AMENITY_ICONS: Record<string, { icon: typeof Wifi; label: string }> = {
    wifi: { icon: Wifi, label: "WiFi" },
    parking: { icon: Car, label: "Parking" },
    restaurant: { icon: UtensilsCrossed, label: "Restaurant" },
    pool: { icon: Waves, label: "Pool" },
    gym: { icon: Dumbbell, label: "Gym" },
    spa: { icon: Sparkles, label: "Spa" },
};

const DEFAULT_AMENITIES = ["wifi", "parking"];

/* ------------------------------------------------------------------ */
/*  Badge variant mapper                                               */
/* ------------------------------------------------------------------ */

function badgeVariant(label: string): "cheapest" | "deal" | "direct" | "bestValue" | "default" {
    const l = label.toLowerCase();
    if (l.includes("best price") || l.includes("cheapest")) return "cheapest";
    if (l.includes("deal") || l.includes("discount")) return "deal";
    if (l.includes("popular") || l.includes("best value")) return "bestValue";
    return "default";
}

/* ------------------------------------------------------------------ */
/*  Star rating component                                              */
/* ------------------------------------------------------------------ */

function StarRating({ stars }: { stars: number }) {
    return (
        <div className="flex items-center gap-0.5" aria-label={`${stars} star hotel`}>
            {Array.from({ length: 5 }, (_, i) => (
                <Star
                    key={i}
                    className={cn(
                        "h-3.5 w-3.5",
                        i < stars
                            ? "fill-amber-400 text-amber-400"
                            : "fill-cream-200 text-cream-300",
                    )}
                />
            ))}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function HotelCard({ offer, searchId, className }: HotelCardProps) {
    const [clicking, setClicking] = useState(false);

    const meta = (offer.metadata ?? {}) as Record<string, unknown>;
    const stars = typeof meta.stars === "number" ? meta.stars : 0;
    const location = (meta.location as string) ?? offer.subtitle ?? "";
    const reviewScore = typeof meta.reviewScore === "number" ? meta.reviewScore : null;
    const amenities = Array.isArray(meta.amenities) ? (meta.amenities as string[]) : DEFAULT_AMENITIES;

    async function handleClick() {
        setClicking(true);
        const bookingWindow = window.open("about:blank", "_blank");
        if (bookingWindow) {
            bookingWindow.opener = null;
        }
        let redirectUrl = offer.deeplink;

        try {
            const res = await fetch("/api/clicks", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ offer, searchId }),
            });
            if (res.ok) {
                const data = await res.json();
                if (typeof data.redirectUrl === "string" && data.redirectUrl.length > 0) {
                    redirectUrl = data.redirectUrl;
                }
            }
        } catch {
            redirectUrl = offer.deeplink;
        } finally {
            if (bookingWindow && !bookingWindow.closed) {
                bookingWindow.location.href = redirectUrl;
            } else {
                window.location.href = redirectUrl;
            }
            setClicking(false);
        }
    }

    return (
        <article
            className={cn(
                "group relative flex flex-col overflow-hidden rounded-2xl border border-cream-200/60 bg-white shadow-card",
                "transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-cardHover",
                className,
            )}
        >
            {/* Badges overlay */}
            {offer.badges && offer.badges.length > 0 && (
                <div className="absolute top-3 left-3 z-10 flex flex-wrap gap-1.5">
                    {offer.badges.map((b) => (
                        <Badge key={b} variant={badgeVariant(b)}>
                            {b}
                        </Badge>
                    ))}
                </div>
            )}

            {/* Image */}
            <div className="relative aspect-[16/10] w-full overflow-hidden">
                {offer.image ? (
                    <img
                        src={offer.image}
                        alt={offer.title}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy"
                    />
                ) : (
                    <div className="h-full w-full bg-gradient-to-br from-sky-100 via-cream-100 to-amber-50 flex items-center justify-center">
                        <MapPin className="h-10 w-10 text-ink-200" />
                    </div>
                )}
                {/* Review score pill */}
                {reviewScore !== null && (
                    <div className="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-lg bg-ink-900/80 backdrop-blur-sm px-2.5 py-1.5 text-cream-100">
                        <span className="text-sm font-bold">{reviewScore.toFixed(1)}</span>
                        <span className="text-[0.65rem] text-cream-200/80">
                            {reviewScore >= 9 ? "Exceptional" : reviewScore >= 8 ? "Excellent" : reviewScore >= 7 ? "Very Good" : "Good"}
                        </span>
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="flex flex-1 flex-col p-5">
                {/* Stars + name */}
                <div className="space-y-1.5">
                    {stars > 0 && <StarRating stars={stars} />}
                    <h3 className="font-display text-lg font-semibold tracking-tight text-ink-900 line-clamp-2 leading-snug">
                        {offer.title}
                    </h3>
                    {location && (
                        <p className="flex items-center gap-1 text-sm text-ink-400">
                            <MapPin className="h-3 w-3 shrink-0" />
                            <span className="truncate">{location}</span>
                        </p>
                    )}
                </div>

                {/* Amenities */}
                <div className="mt-3 flex flex-wrap gap-2">
                    {amenities.slice(0, 4).map((a) => {
                        const key = a.toLowerCase();
                        const config = AMENITY_ICONS[key];
                        if (!config) {
                            return (
                                <span key={a} className="inline-flex items-center gap-1 rounded-full bg-cream-100 px-2.5 py-1 text-[0.7rem] font-medium text-ink-500">
                                    {a}
                                </span>
                            );
                        }
                        const Icon = config.icon;
                        return (
                            <span key={a} className="inline-flex items-center gap-1 rounded-full bg-cream-100 px-2.5 py-1 text-[0.7rem] font-medium text-ink-500">
                                <Icon className="h-3 w-3" />
                                {config.label}
                            </span>
                        );
                    })}
                </div>

                {/* Price + CTA */}
                <div className="mt-auto flex items-end justify-between pt-4 border-t border-cream-200/60">
                    <div>
                        {offer.strikePrice && (
                            <p className="text-sm text-ink-300 line-through">
                                {offer.strikePrice.currency} {offer.strikePrice.amount.toFixed(0)}
                            </p>
                        )}
                        <p className="font-display text-2xl font-bold tracking-tight text-ink-900">
                            {offer.price.currency === "USD" ? "$" : offer.price.currency}{" "}
                            {offer.price.amount.toFixed(0)}
                        </p>
                        <p className="text-[0.68rem] text-ink-400">per night</p>
                    </div>
                    <Button
                        variant="primary"
                        size="sm"
                        loading={clicking}
                        onClick={handleClick}
                        className="gap-1.5"
                    >
                        View Deal
                        <ExternalLink className="h-3.5 w-3.5" />
                    </Button>
                </div>
            </div>
        </article>
    );
}
