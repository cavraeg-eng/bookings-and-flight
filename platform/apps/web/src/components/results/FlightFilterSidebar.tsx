"use client";

import { useMemo } from "react";
import { Filter, X } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { cn } from "@/lib/cn";
import type { Offer } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface FlightFilters {
    stops: Set<string>;
    priceMin: number;
    priceMax: number;
    airlines: Set<string>;
    departureTimes: Set<string>;
}

export const EMPTY_FILTERS: FlightFilters = {
    stops: new Set(),
    priceMin: 0,
    priceMax: Infinity,
    airlines: new Set(),
    departureTimes: new Set(),
};

export interface FlightFilterSidebarProps {
    offers: Offer[];
    filters: FlightFilters;
    onFilterChange: (filters: FlightFilters) => void;
    matchCount: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Departure time bucket helpers                                      */
/* ------------------------------------------------------------------ */

const TIME_BUCKETS = [
    { id: "morning", label: "Morning", range: "6 AM – 12 PM" },
    { id: "afternoon", label: "Afternoon", range: "12 PM – 6 PM" },
    { id: "evening", label: "Evening", range: "6 PM – 12 AM" },
    { id: "night", label: "Night", range: "12 AM – 6 AM" },
] as const;

export function getTimeBucket(time: string): string {
    const hour = parseInt(time.split(":")[0], 10);
    if (isNaN(hour)) return "";
    if (hour >= 6 && hour < 12) return "morning";
    if (hour >= 12 && hour < 18) return "afternoon";
    if (hour >= 18 && hour < 24) return "evening";
    return "night";
}

/* ------------------------------------------------------------------ */
/*  Stops normalization                                                */
/* ------------------------------------------------------------------ */

export function normalizeStops(transfers: string): string {
    const lower = (transfers ?? "").toLowerCase().trim();
    if (lower === "nonstop" || lower === "direct" || lower === "0") return "nonstop";
    if (lower === "1 stop" || lower === "1") return "1stop";
    return "2+stops";
}

const STOPS_OPTIONS = [
    { id: "nonstop", label: "Nonstop" },
    { id: "1stop", label: "1 Stop" },
    { id: "2+stops", label: "2+ Stops" },
] as const;

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function FlightFilterSidebar({
    offers,
    filters,
    onFilterChange,
    matchCount,
    className,
}: FlightFilterSidebarProps) {
    /* Derived data from all offers */
    const { airlines, priceRange } = useMemo(() => {
        const airlineSet = new Set<string>();
        let min = Infinity;
        let max = 0;

        for (const o of offers) {
            const airline = (o.metadata?.airline as string) ?? o.title;
            airlineSet.add(airline);
            if (o.price.amount < min) min = o.price.amount;
            if (o.price.amount > max) max = o.price.amount;
        }

        return {
            airlines: Array.from(airlineSet).sort(),
            priceRange: { min: Math.floor(min), max: Math.ceil(max) },
        };
    }, [offers]);

    const hasActiveFilters =
        filters.stops.size > 0 ||
        filters.airlines.size > 0 ||
        filters.departureTimes.size > 0 ||
        filters.priceMin > priceRange.min ||
        filters.priceMax < priceRange.max;

    function toggleSet<T>(set: Set<T>, value: T): Set<T> {
        const next = new Set(set);
        if (next.has(value)) next.delete(value);
        else next.add(value);
        return next;
    }

    function handleClearAll() {
        onFilterChange({
            ...EMPTY_FILTERS,
            priceMin: priceRange.min,
            priceMax: priceRange.max,
        });
    }

    return (
        <aside className={cn("space-y-6", className)}>
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <Filter className="h-4 w-4 text-ink-400" />
                    <span className="text-sm font-semibold text-ink-900">Filters</span>
                </div>
                {hasActiveFilters && (
                    <button
                        onClick={handleClearAll}
                        className="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-700 transition-colors"
                    >
                        <X className="h-3 w-3" />
                        Clear all
                    </button>
                )}
            </div>

            <p className="text-xs text-ink-400">
                {matchCount} {matchCount === 1 ? "flight" : "flights"} found
            </p>

            {/* Stops */}
            <FilterSection title="Stops">
                {STOPS_OPTIONS.map((opt) => (
                    <Checkbox
                        key={opt.id}
                        label={opt.label}
                        checked={filters.stops.has(opt.id)}
                        onChange={() =>
                            onFilterChange({ ...filters, stops: toggleSet(filters.stops, opt.id) })
                        }
                    />
                ))}
            </FilterSection>

            {/* Price Range */}
            <FilterSection title="Price Range">
                <div className="flex items-center gap-2">
                    <PriceInput
                        label="Min"
                        value={filters.priceMin || priceRange.min}
                        onChange={(v) => onFilterChange({ ...filters, priceMin: v })}
                    />
                    <span className="text-ink-300 mt-4">–</span>
                    <PriceInput
                        label="Max"
                        value={filters.priceMax === Infinity ? priceRange.max : filters.priceMax}
                        onChange={(v) => onFilterChange({ ...filters, priceMax: v })}
                    />
                </div>
            </FilterSection>

            {/* Airlines */}
            {airlines.length > 1 && (
                <FilterSection title="Airlines">
                    {airlines.map((a) => (
                        <Checkbox
                            key={a}
                            label={a}
                            checked={filters.airlines.has(a)}
                            onChange={() =>
                                onFilterChange({ ...filters, airlines: toggleSet(filters.airlines, a) })
                            }
                        />
                    ))}
                </FilterSection>
            )}

            {/* Departure Time */}
            <FilterSection title="Departure Time">
                {TIME_BUCKETS.map((b) => (
                    <Checkbox
                        key={b.id}
                        label={`${b.label}`}
                        sublabel={b.range}
                        checked={filters.departureTimes.has(b.id)}
                        onChange={() =>
                            onFilterChange({
                                ...filters,
                                departureTimes: toggleSet(filters.departureTimes, b.id),
                            })
                        }
                    />
                ))}
            </FilterSection>
        </aside>
    );
}

/* ------------------------------------------------------------------ */
/*  Sub-components                                                     */
/* ------------------------------------------------------------------ */

function FilterSection({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <div>
            <h3 className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mb-3">
                {title}
            </h3>
            <div className="space-y-2">{children}</div>
        </div>
    );
}

function Checkbox({
    label,
    sublabel,
    checked,
    onChange,
}: {
    label: string;
    sublabel?: string;
    checked: boolean;
    onChange: () => void;
}) {
    return (
        <label className="flex items-center gap-2.5 cursor-pointer group">
            <input
                type="checkbox"
                checked={checked}
                onChange={onChange}
                className="h-4 w-4 rounded border-ink-300 text-amber-500 focus:ring-amber-500/30"
            />
            <span className="flex flex-col">
                <span className="text-sm text-ink-700 group-hover:text-ink-900 transition-colors">
                    {label}
                </span>
                {sublabel && (
                    <span className="text-[0.65rem] text-ink-400">{sublabel}</span>
                )}
            </span>
        </label>
    );
}

function PriceInput({
    label,
    value,
    onChange,
}: {
    label: string;
    value: number;
    onChange: (v: number) => void;
}) {
    return (
        <div className="flex-1">
            <span className="text-[0.6rem] font-semibold uppercase tracking-wider text-ink-400">
                {label}
            </span>
            <div className="mt-1 flex items-center rounded-lg border border-ink-200 bg-white px-2 py-1.5">
                <span className="text-xs text-ink-400 mr-1">$</span>
                <input
                    type="number"
                    value={value}
                    onChange={(e) => onChange(Number(e.target.value) || 0)}
                    className="w-full border-0 bg-transparent p-0 text-sm font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                    min={0}
                />
            </div>
        </div>
    );
}
