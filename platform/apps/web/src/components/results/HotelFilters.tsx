"use client";

import { useState, useMemo, useCallback } from "react";
import { Star, X, Wifi, Car, UtensilsCrossed, Waves, Dumbbell, Sparkles } from "lucide-react";
import { cn } from "@/lib/cn";
import { Button } from "@/components/ui/Button";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface HotelFilters {
    stars: number[];
    priceMin: number | null;
    priceMax: number | null;
    amenities: string[];
    minRating: number | null;
}

export const EMPTY_FILTERS: HotelFilters = {
    stars: [],
    priceMin: null,
    priceMax: null,
    amenities: [],
    minRating: null,
};

export interface HotelFiltersProps {
    offers: Offer[];
    filters: HotelFilters;
    onFilterChange: (filters: HotelFilters) => void;
    resultCount: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Amenity config                                                     */
/* ------------------------------------------------------------------ */

const AMENITY_OPTIONS: { key: string; label: string; icon: typeof Wifi }[] = [
    { key: "wifi", label: "WiFi", icon: Wifi },
    { key: "pool", label: "Pool", icon: Waves },
    { key: "parking", label: "Parking", icon: Car },
    { key: "restaurant", label: "Restaurant", icon: UtensilsCrossed },
    { key: "spa", label: "Spa", icon: Sparkles },
    { key: "gym", label: "Gym", icon: Dumbbell },
];

const RATING_OPTIONS = [
    { label: "8+", value: 8 },
    { label: "7+", value: 7 },
    { label: "6+", value: 6 },
];

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function HotelFiltersPanel({
    offers,
    filters,
    onFilterChange,
    resultCount,
    className,
}: HotelFiltersProps) {
    const hasActiveFilters = useMemo(
        () =>
            filters.stars.length > 0 ||
            filters.priceMin !== null ||
            filters.priceMax !== null ||
            filters.amenities.length > 0 ||
            filters.minRating !== null,
        [filters],
    );

    const priceRange = useMemo(() => {
        if (offers.length === 0) return { min: 0, max: 1000 };
        const prices = offers.map((o) => o.price.amount);
        return { min: Math.floor(Math.min(...prices)), max: Math.ceil(Math.max(...prices)) };
    }, [offers]);

    const toggleStar = useCallback(
        (star: number) => {
            const next = filters.stars.includes(star)
                ? filters.stars.filter((s) => s !== star)
                : [...filters.stars, star];
            onFilterChange({ ...filters, stars: next });
        },
        [filters, onFilterChange],
    );

    const toggleAmenity = useCallback(
        (amenity: string) => {
            const next = filters.amenities.includes(amenity)
                ? filters.amenities.filter((a) => a !== amenity)
                : [...filters.amenities, amenity];
            onFilterChange({ ...filters, amenities: next });
        },
        [filters, onFilterChange],
    );

    const setMinRating = useCallback(
        (rating: number | null) => {
            onFilterChange({ ...filters, minRating: filters.minRating === rating ? null : rating });
        },
        [filters, onFilterChange],
    );

    return (
        <aside className={cn("rounded-2xl border border-cream-200/60 bg-white p-5 shadow-card", className)}>
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h2 className="font-display text-lg font-semibold tracking-tight text-ink-900">
                        Filters
                    </h2>
                    <p className="text-sm text-ink-400">
                        {resultCount} {resultCount === 1 ? "property" : "properties"} found
                    </p>
                </div>
                {hasActiveFilters && (
                    <button
                        type="button"
                        onClick={() => onFilterChange(EMPTY_FILTERS)}
                        className="inline-flex items-center gap-1 rounded-full bg-cream-100 px-3 py-1.5 text-xs font-semibold text-ink-500 hover:bg-cream-200 hover:text-ink-700 transition-colors"
                    >
                        <X className="h-3 w-3" />
                        Clear All
                    </button>
                )}
            </div>

            <hr className="my-4 border-cream-200/60" />

            {/* Star Rating */}
            <div>
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mb-2.5">
                    Star Rating
                </p>
                <div className="flex flex-wrap gap-2">
                    {[5, 4, 3, 2, 1].map((star) => {
                        const active = filters.stars.includes(star);
                        return (
                            <button
                                key={star}
                                type="button"
                                onClick={() => toggleStar(star)}
                                className={cn(
                                    "inline-flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium transition-all",
                                    active
                                        ? "bg-amber-50 text-amber-700 border border-amber-200/80 shadow-subtle"
                                        : "bg-cream-50 text-ink-500 border border-cream-200/60 hover:bg-cream-100 hover:text-ink-700",
                                )}
                            >
                                <Star className={cn("h-3.5 w-3.5", active ? "fill-amber-400 text-amber-400" : "text-ink-300")} />
                                {star}
                            </button>
                        );
                    })}
                </div>
            </div>

            <hr className="my-4 border-cream-200/60" />

            {/* Price Range */}
            <div>
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mb-2.5">
                    Price Range
                </p>
                <div className="flex items-center gap-2">
                    <div className="relative flex-1">
                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-300">$</span>
                        <input
                            type="number"
                            placeholder={String(priceRange.min)}
                            value={filters.priceMin ?? ""}
                            onChange={(e) =>
                                onFilterChange({
                                    ...filters,
                                    priceMin: e.target.value ? Number(e.target.value) : null,
                                })
                            }
                            className="w-full rounded-lg border border-cream-200/60 bg-cream-50 py-2 pl-7 pr-3 text-sm text-ink-900 placeholder:text-ink-300 focus:border-sky-300 focus:ring-1 focus:ring-sky-300 focus:outline-none"
                        />
                    </div>
                    <span className="text-ink-300 text-sm">–</span>
                    <div className="relative flex-1">
                        <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-300">$</span>
                        <input
                            type="number"
                            placeholder={String(priceRange.max)}
                            value={filters.priceMax ?? ""}
                            onChange={(e) =>
                                onFilterChange({
                                    ...filters,
                                    priceMax: e.target.value ? Number(e.target.value) : null,
                                })
                            }
                            className="w-full rounded-lg border border-cream-200/60 bg-cream-50 py-2 pl-7 pr-3 text-sm text-ink-900 placeholder:text-ink-300 focus:border-sky-300 focus:ring-1 focus:ring-sky-300 focus:outline-none"
                        />
                    </div>
                </div>
            </div>

            <hr className="my-4 border-cream-200/60" />

            {/* Amenities */}
            <div>
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mb-2.5">
                    Amenities
                </p>
                <div className="grid grid-cols-2 gap-2">
                    {AMENITY_OPTIONS.map(({ key, label, icon: Icon }) => {
                        const active = filters.amenities.includes(key);
                        return (
                            <label
                                key={key}
                                className={cn(
                                    "flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium cursor-pointer transition-all border",
                                    active
                                        ? "bg-sky-50 text-sky-700 border-sky-200/60"
                                        : "bg-cream-50 text-ink-500 border-cream-200/60 hover:bg-cream-100 hover:text-ink-700",
                                )}
                            >
                                <input
                                    type="checkbox"
                                    checked={active}
                                    onChange={() => toggleAmenity(key)}
                                    className="sr-only"
                                />
                                <Icon className="h-3.5 w-3.5 shrink-0" />
                                {label}
                            </label>
                        );
                    })}
                </div>
            </div>

            <hr className="my-4 border-cream-200/60" />

            {/* Guest Rating */}
            <div>
                <p className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mb-2.5">
                    Guest Rating
                </p>
                <div className="flex gap-2">
                    {RATING_OPTIONS.map(({ label, value }) => {
                        const active = filters.minRating === value;
                        return (
                            <button
                                key={value}
                                type="button"
                                onClick={() => setMinRating(value)}
                                className={cn(
                                    "flex-1 rounded-lg py-2 text-sm font-semibold transition-all border",
                                    active
                                        ? "bg-ink-900 text-cream-100 border-ink-900"
                                        : "bg-cream-50 text-ink-500 border-cream-200/60 hover:bg-cream-100 hover:text-ink-700",
                                )}
                            >
                                {label}
                            </button>
                        );
                    })}
                </div>
            </div>
        </aside>
    );
}

/* ------------------------------------------------------------------ */
/*  Filter logic (pure function)                                       */
/* ------------------------------------------------------------------ */

export function applyHotelFilters(offers: Offer[], filters: HotelFilters): Offer[] {
    return offers.filter((o) => {
        const meta = (o.metadata ?? {}) as Record<string, unknown>;

        // Star filter
        if (filters.stars.length > 0) {
            const stars = typeof meta.stars === "number" ? meta.stars : 0;
            if (!filters.stars.includes(stars)) return false;
        }

        // Price filter
        if (filters.priceMin !== null && o.price.amount < filters.priceMin) return false;
        if (filters.priceMax !== null && o.price.amount > filters.priceMax) return false;

        // Amenity filter
        if (filters.amenities.length > 0) {
            const offerAmenities = Array.isArray(meta.amenities) ? (meta.amenities as string[]).map((a) => a.toLowerCase()) : [];
            if (!filters.amenities.every((a) => offerAmenities.includes(a))) return false;
        }

        // Rating filter
        if (filters.minRating !== null) {
            const score = typeof meta.reviewScore === "number" ? meta.reviewScore : 0;
            if (score < filters.minRating) return false;
        }

        return true;
    });
}
