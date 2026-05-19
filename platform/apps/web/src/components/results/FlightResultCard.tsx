"use client";

import { useState } from "react";
import { ExternalLink, Plane, Clock } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { Button } from "@/components/ui/Button";
import { cn } from "@/lib/cn";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface FlightResultCardProps {
    offer: Offer;
    onBookClick: (offer: Offer) => Promise<void>;
    index?: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Badge variant mapping                                              */
/* ------------------------------------------------------------------ */

const BADGE_VARIANT_MAP: Record<string, "cheapest" | "direct" | "deal" | "bestValue" | "default"> = {
    Cheapest: "cheapest",
    cheapest: "cheapest",
    Direct: "direct",
    direct: "direct",
    Nonstop: "direct",
    nonstop: "direct",
    Deal: "deal",
    deal: "deal",
    "Best Value": "bestValue",
    bestValue: "bestValue",
};

/* ------------------------------------------------------------------ */
/*  Stops color helper                                                 */
/* ------------------------------------------------------------------ */

function stopsColor(transfers: string): string {
    const lower = transfers.toLowerCase();
    if (lower === "nonstop" || lower === "direct" || lower === "0") {
        return "text-emerald-600 bg-emerald-50";
    }
    if (lower === "1 stop" || lower === "1") {
        return "text-amber-600 bg-amber-50";
    }
    return "text-ink-600 bg-ink-50";
}

function stopsLabel(transfers: string): string {
    const lower = transfers.toLowerCase();
    if (lower === "nonstop" || lower === "direct" || lower === "0") return "Nonstop";
    if (lower === "1 stop" || lower === "1") return "1 stop";
    return transfers;
}

/* ------------------------------------------------------------------ */
/*  Format currency                                                    */
/* ------------------------------------------------------------------ */

function formatPrice(amount: number, currency: string): string {
    try {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount);
    } catch {
        return `${currency} ${amount}`;
    }
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function FlightResultCard({ offer, onBookClick, index = 0, className }: FlightResultCardProps) {
    const [loading, setLoading] = useState(false);
    const meta = offer.metadata ?? {};
    const airline = (meta.airline as string) ?? offer.title;
    const flightNumber = (meta.flightNumber as string) ?? "";
    const departTime = (meta.departTime as string) ?? "";
    const returnTime = (meta.returnTime as string) ?? "";
    const duration = (meta.duration as string) ?? "";
    const transfers = (meta.transfers as string) ?? "";

    async function handleClick() {
        setLoading(true);
        try {
            await onBookClick(offer);
        } finally {
            setLoading(false);
        }
    }

    return (
        <Card
            hoverable
            padding="none"
            className={cn(
                "animate-fade-up group",
                className,
            )}
            style={{ animationDelay: `${index * 60}ms`, animationFillMode: "both" }}
        >
            <div className="flex flex-col sm:flex-row sm:items-center gap-4 p-4 sm:p-5">
                {/* Left — Airline info */}
                <div className="flex items-center gap-3 sm:w-[160px] shrink-0">
                    <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <Plane className="h-5 w-5" />
                    </div>
                    <div className="min-w-0">
                        <p className="text-sm font-semibold text-ink-900 truncate">{airline}</p>
                        {flightNumber && (
                            <p className="text-xs text-ink-400 mt-0.5">{flightNumber}</p>
                        )}
                    </div>
                </div>

                {/* Center — Times & duration */}
                <div className="flex-1 flex items-center gap-3 sm:gap-4 min-w-0">
                    <div className="text-center shrink-0">
                        <p className="text-lg font-bold text-ink-900 leading-tight">
                            {departTime || "—"}
                        </p>
                        <p className="text-[0.65rem] text-ink-400 uppercase tracking-wide mt-0.5">Depart</p>
                    </div>

                    <div className="flex-1 flex flex-col items-center gap-1 min-w-[80px]">
                        <div className="flex items-center gap-1.5 w-full">
                            <div className="h-px flex-1 bg-ink-200" />
                            <Plane className="h-3 w-3 text-ink-300 -rotate-12" />
                            <div className="h-px flex-1 bg-ink-200" />
                        </div>
                        {duration && (
                            <div className="flex items-center gap-1 text-[0.65rem] text-ink-400">
                                <Clock className="h-2.5 w-2.5" />
                                <span>{duration}</span>
                            </div>
                        )}
                        {transfers && (
                            <span
                                className={cn(
                                    "inline-flex items-center px-2 py-0.5 rounded-pill text-[0.6rem] font-semibold",
                                    stopsColor(transfers),
                                )}
                            >
                                {stopsLabel(transfers)}
                            </span>
                        )}
                    </div>

                    <div className="text-center shrink-0">
                        <p className="text-lg font-bold text-ink-900 leading-tight">
                            {returnTime || "—"}
                        </p>
                        <p className="text-[0.65rem] text-ink-400 uppercase tracking-wide mt-0.5">Arrive</p>
                    </div>
                </div>

                {/* Right — Price & CTA */}
                <div className="flex items-center gap-4 sm:flex-col sm:items-end sm:gap-2 sm:w-[140px] shrink-0">
                    <div className="flex items-baseline gap-2 sm:flex-col sm:items-end sm:gap-0">
                        {offer.strikePrice && (
                            <span className="text-sm text-ink-400 line-through">
                                {formatPrice(offer.strikePrice.amount, offer.strikePrice.currency)}
                            </span>
                        )}
                        <span className="text-2xl font-bold text-ink-900 tracking-tight">
                            {formatPrice(offer.price.amount, offer.price.currency)}
                        </span>
                    </div>
                    <Button
                        variant="primary"
                        size="sm"
                        onClick={handleClick}
                        loading={loading}
                        className="whitespace-nowrap"
                    >
                        View Deal
                        <ExternalLink className="h-3.5 w-3.5" />
                    </Button>
                </div>
            </div>

            {/* Badges */}
            {offer.badges && offer.badges.length > 0 && (
                <div className="flex flex-wrap gap-1.5 px-4 pb-4 sm:px-5 sm:pb-5 -mt-1">
                    {offer.badges.map((badge) => (
                        <Badge key={badge} variant={BADGE_VARIANT_MAP[badge] ?? "default"}>
                            {badge}
                        </Badge>
                    ))}
                </div>
            )}
        </Card>
    );
}
