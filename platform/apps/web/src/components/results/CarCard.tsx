"use client";

import { useState } from "react";
import { Car, Fuel, Snowflake, Users, Cog, ExternalLink, Briefcase } from "lucide-react";
import { cn } from "@/lib/cn";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface CarCardProps {
    offer: Offer;
    onBookClick: (offer: Offer) => void;
    index?: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Feature config                                                     */
/* ------------------------------------------------------------------ */

const FEATURE_ICONS: Record<string, { icon: typeof Car; label: string }> = {
    ac: { icon: Snowflake, label: "A/C" },
    automatic: { icon: Cog, label: "Automatic" },
    manual: { icon: Cog, label: "Manual" },
    seats: { icon: Users, label: "Seats" },
    fuel: { icon: Fuel, label: "Fuel incl." },
    bags: { icon: Briefcase, label: "Bags" },
};

const DEFAULT_FEATURES = ["ac", "automatic"];

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function CarCard({ offer, onBookClick, index = 0, className }: CarCardProps) {
    const [clicking, setClicking] = useState(false);

    const meta = (offer.metadata ?? {}) as Record<string, unknown>;
    const category = (meta.category as string) ?? offer.title;
    const supplier = (meta.supplierName as string) ?? offer.supplier;
    const seats = typeof meta.seats === "number" ? meta.seats : 4;
    const transmission = (meta.transmission as string) ?? "automatic";
    const bags = typeof meta.bags === "number" ? meta.bags : 2;
    const fuelPolicy = (meta.fuelPolicy as string) ?? "";
    const pricePerDay = typeof meta.pricePerDay === "number" ? meta.pricePerDay : null;

    // Build feature list
    const features: { icon: typeof Car; label: string }[] = [
        { icon: Snowflake, label: "A/C" },
        { icon: Cog, label: transmission === "manual" ? "Manual" : "Automatic" },
        { icon: Users, label: `${seats} Seats` },
        { icon: Briefcase, label: `${bags} Bags` },
    ];
    if (fuelPolicy) {
        features.push({ icon: Fuel, label: fuelPolicy });
    }

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
                "group relative flex flex-col sm:flex-row overflow-hidden rounded-2xl border border-cream-200/60 bg-white shadow-card",
                "transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-cardHover",
                "animate-fade-up",
                className,
            )}
            style={{ animationDelay: `${index * 60}ms`, animationFillMode: "both" }}
        >
            {/* Car image placeholder */}
            <div className="relative flex items-center justify-center sm:w-48 md:w-56 shrink-0 bg-gradient-to-br from-sky-50 via-cream-50 to-amber-50 p-6">
                <Car className="h-16 w-16 text-ink-200 transition-transform duration-300 group-hover:scale-110" strokeWidth={1.25} />
                {offer.badges && offer.badges.length > 0 && (
                    <div className="absolute top-3 left-3 flex flex-wrap gap-1.5">
                        {offer.badges.map((b) => (
                            <Badge key={b} variant={b.toLowerCase().includes("deal") ? "deal" : b.toLowerCase().includes("cheap") ? "cheapest" : "default"}>
                                {b}
                            </Badge>
                        ))}
                    </div>
                )}
            </div>

            {/* Content */}
            <div className="flex flex-1 flex-col p-5">
                {/* Header */}
                <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <h3 className="font-display text-lg font-semibold tracking-tight text-ink-900 line-clamp-1 leading-snug">
                            {category}
                        </h3>
                        {offer.subtitle && (
                            <p className="mt-0.5 text-sm text-ink-400 line-clamp-1">{offer.subtitle}</p>
                        )}
                    </div>
                </div>

                {/* Features */}
                <div className="mt-3 flex flex-wrap gap-2">
                    {features.slice(0, 5).map((f, i) => {
                        const Icon = f.icon;
                        return (
                            <span
                                key={i}
                                className="inline-flex items-center gap-1 rounded-full bg-cream-100 px-2.5 py-1 text-[0.7rem] font-medium text-ink-500"
                            >
                                <Icon className="h-3 w-3" />
                                {f.label}
                            </span>
                        );
                    })}
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
                        <p className="text-[0.68rem] text-ink-400">
                            {pricePerDay != null
                                ? `$${pricePerDay.toFixed(0)}/day · total`
                                : "total price"}
                        </p>
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
