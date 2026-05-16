"use client";

import { useState } from "react";
import { Compass, Clock, MapPin, Star, ExternalLink, Tag } from "lucide-react";
import { cn } from "@/lib/cn";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface ActivityCardProps {
    offer: Offer;
    onBookClick: (offer: Offer) => void;
    index?: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Category color map                                                 */
/* ------------------------------------------------------------------ */

const CATEGORY_COLORS: Record<string, string> = {
    tours: "bg-sky-50 text-sky-700 border border-sky-200/60",
    "day trips": "bg-emerald-50 text-emerald-700 border border-emerald-200/60",
    museums: "bg-amber-50 text-amber-700 border border-amber-200/60",
    adventure: "bg-rose-50 text-rose-700 border border-rose-200/60",
    food: "bg-orange-50 text-orange-700 border border-orange-200/60",
    culture: "bg-violet-50 text-violet-700 border border-violet-200/60",
};

function getCategoryStyle(cat: string): string {
    const key = cat.toLowerCase();
    for (const [k, v] of Object.entries(CATEGORY_COLORS)) {
        if (key.includes(k)) return v;
    }
    return "bg-ink-100 text-ink-700";
}

/* ------------------------------------------------------------------ */
/*  Gradient backgrounds for activity types                            */
/* ------------------------------------------------------------------ */

const CATEGORY_GRADIENTS: Record<string, string> = {
    tours: "from-sky-100 via-sky-50 to-cream-50",
    "day trips": "from-emerald-100 via-emerald-50 to-cream-50",
    museums: "from-amber-100 via-amber-50 to-cream-50",
    adventure: "from-rose-100 via-rose-50 to-cream-50",
    food: "from-orange-100 via-orange-50 to-cream-50",
};

function getGradient(cat: string): string {
    const key = cat.toLowerCase();
    for (const [k, v] of Object.entries(CATEGORY_GRADIENTS)) {
        if (key.includes(k)) return v;
    }
    return "from-sky-50 via-cream-50 to-amber-50";
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function ActivityCard({ offer, onBookClick, index = 0, className }: ActivityCardProps) {
    const [clicking, setClicking] = useState(false);

    const meta = (offer.metadata ?? {}) as Record<string, unknown>;
    const category = (meta.category as string) ?? "";
    const duration = (meta.duration as string) ?? "";
    const rating = typeof meta.rating === "number" ? meta.rating : null;
    const reviewCount = typeof meta.reviewCount === "number" ? meta.reviewCount : null;
    const supplier = (meta.supplierName as string) ?? offer.supplier;
    const location = (meta.location as string) ?? "";

    async function handleClick() {
        setClicking(true);
        try {
            await onBookClick(offer);
        } finally {
            setClicking(false);
        }
    }

    return (
        <article
            className={cn(
                "group relative flex flex-col overflow-hidden rounded-2xl border border-cream-200/60 bg-white shadow-card",
                "transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-cardHover",
                "animate-fade-up",
                className,
            )}
            style={{ animationDelay: `${index * 60}ms`, animationFillMode: "both" }}
        >
            {/* Image / gradient header */}
            <div className={cn(
                "relative aspect-[16/9] w-full overflow-hidden",
            )}>
                {offer.image ? (
                    <img
                        src={offer.image}
                        alt={offer.title}
                        className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        loading="lazy"
                    />
                ) : (
                    <div className={cn(
                        "h-full w-full bg-gradient-to-br flex items-center justify-center",
                        getGradient(category),
                    )}>
                        <Compass className="h-12 w-12 text-ink-200 transition-transform duration-300 group-hover:rotate-12" strokeWidth={1.25} />
                    </div>
                )}

                {/* Category badge overlay */}
                {category && (
                    <div className="absolute top-3 left-3 z-10">
                        <span className={cn(
                            "inline-flex items-center gap-1 rounded-pill px-2.5 py-1 text-xs font-semibold backdrop-blur-sm",
                            getCategoryStyle(category),
                        )}>
                            <Tag className="h-3 w-3" />
                            {category}
                        </span>
                    </div>
                )}

                {/* Badges overlay */}
                {offer.badges && offer.badges.length > 0 && (
                    <div className="absolute top-3 right-3 z-10 flex flex-wrap gap-1.5">
                        {offer.badges.map((b) => (
                            <Badge
                                key={b}
                                variant={
                                    b.toLowerCase().includes("popular") || b.toLowerCase().includes("best")
                                        ? "bestValue"
                                        : b.toLowerCase().includes("deal")
                                            ? "deal"
                                            : "default"
                                }
                            >
                                {b}
                            </Badge>
                        ))}
                    </div>
                )}

                {/* Rating pill */}
                {rating !== null && (
                    <div className="absolute bottom-3 right-3 flex items-center gap-1 rounded-lg bg-ink-900/80 backdrop-blur-sm px-2.5 py-1.5 text-cream-100">
                        <Star className="h-3 w-3 fill-amber-400 text-amber-400" />
                        <span className="text-sm font-bold">{rating.toFixed(1)}</span>
                        {reviewCount !== null && (
                            <span className="text-[0.6rem] text-cream-200/80">({reviewCount})</span>
                        )}
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="flex flex-1 flex-col p-5">
                <h3 className="font-display text-lg font-semibold tracking-tight text-ink-900 line-clamp-2 leading-snug">
                    {offer.title}
                </h3>

                {offer.subtitle && (
                    <p className="mt-1.5 text-sm text-ink-400 line-clamp-2 leading-relaxed">
                        {offer.subtitle}
                    </p>
                )}

                {/* Meta row */}
                <div className="mt-3 flex flex-wrap items-center gap-3 text-xs text-ink-400">
                    {duration && (
                        <span className="inline-flex items-center gap-1">
                            <Clock className="h-3 w-3" />
                            {duration}
                        </span>
                    )}
                    {location && (
                        <span className="inline-flex items-center gap-1">
                            <MapPin className="h-3 w-3" />
                            <span className="truncate max-w-[120px]">{location}</span>
                        </span>
                    )}
                </div>

                {/* Supplier */}
                <p className="mt-2 text-xs text-ink-300">
                    via <span className="font-medium text-ink-400">{supplier}</span>
                </p>

                {/* Price + CTA */}
                <div className="mt-auto flex items-end justify-between pt-4 border-t border-cream-200/60">
                    <div>
                        {offer.strikePrice && (
                            <p className="text-sm text-ink-300 line-through">
                                {offer.strikePrice.currency === "USD" ? "$" : offer.strikePrice.currency}{" "}
                                {offer.strikePrice.amount.toFixed(0)}
                            </p>
                        )}
                        <p className="font-display text-2xl font-bold tracking-tight text-ink-900">
                            {offer.price.currency === "USD" ? "$" : offer.price.currency}{" "}
                            {offer.price.amount.toFixed(0)}
                        </p>
                        <p className="text-[0.68rem] text-ink-400">per person</p>
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
