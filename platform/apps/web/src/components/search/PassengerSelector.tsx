"use client";

import { useState, useRef, useEffect } from "react";
import { Users, Minus, Plus, ChevronDown } from "lucide-react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface PassengerCounts {
    adults: number;
    children: number;
    infants: number;
}

export interface PassengerSelectorProps {
    adults: number;
    children: number;
    infants: number;
    onChange: (counts: PassengerCounts) => void;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function PassengerSelector({
    adults,
    children: childCount,
    infants,
    onChange,
    className,
}: PassengerSelectorProps) {
    const [isOpen, setIsOpen] = useState(false);
    const wrapperRef = useRef<HTMLDivElement>(null);

    // Close on outside click
    useEffect(() => {
        function handleClick(e: MouseEvent) {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setIsOpen(false);
            }
        }
        document.addEventListener("mousedown", handleClick);
        return () => document.removeEventListener("mousedown", handleClick);
    }, []);

    function update(field: keyof PassengerCounts, delta: number) {
        const next = { adults, children: childCount, infants };
        next[field] = Math.max(0, next[field] + delta);

        // Enforce limits
        if (next.adults < 1) next.adults = 1;
        if (next.adults > 9) next.adults = 9;
        if (next.children > 9) next.children = 9;
        if (next.infants > 4) next.infants = 4;
        // Infants can't exceed adults
        if (next.infants > next.adults) next.infants = next.adults;

        onChange(next);
    }

    const summaryParts: string[] = [];
    summaryParts.push(`${adults} Adult${adults !== 1 ? "s" : ""}`);
    if (childCount > 0) summaryParts.push(`${childCount} Child${childCount !== 1 ? "ren" : ""}`);
    if (infants > 0) summaryParts.push(`${infants} Infant${infants !== 1 ? "s" : ""}`);
    const summary = summaryParts.join(", ");

    return (
        <div ref={wrapperRef} className={cn("relative", className)}>
            <div className="flex flex-col gap-1.5">
                <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                    Passengers
                </span>
                <button
                    type="button"
                    onClick={() => setIsOpen(!isOpen)}
                    className={cn(
                        "flex items-center gap-2 text-left w-full",
                        "text-[0.95rem] font-semibold text-ink-900",
                        "focus:outline-none focus:ring-0",
                    )}
                >
                    <Users className="h-4 w-4 text-ink-400 shrink-0" />
                    <span className="truncate">{summary}</span>
                    <ChevronDown
                        className={cn(
                            "h-3.5 w-3.5 text-ink-400 shrink-0 transition-transform",
                            isOpen && "rotate-180",
                        )}
                    />
                </button>
            </div>

            {/* Hidden inputs for form submission */}
            <input type="hidden" name="adults" value={adults} />
            <input type="hidden" name="children" value={childCount} />
            <input type="hidden" name="infants" value={infants} />

            {/* Popover */}
            {isOpen && (
                <div
                    className={cn(
                        "absolute left-0 top-full z-50 mt-2 w-64",
                        "rounded-xl border border-ink-200/60 bg-white p-4",
                        "shadow-search animate-slideDown",
                    )}
                >
                    <CounterRow
                        label="Adults"
                        subtitle="12+ years"
                        value={adults}
                        min={1}
                        max={9}
                        onDecrement={() => update("adults", -1)}
                        onIncrement={() => update("adults", 1)}
                    />
                    <CounterRow
                        label="Children"
                        subtitle="2–11 years"
                        value={childCount}
                        min={0}
                        max={9}
                        onDecrement={() => update("children", -1)}
                        onIncrement={() => update("children", 1)}
                    />
                    <CounterRow
                        label="Infants"
                        subtitle="Under 2"
                        value={infants}
                        min={0}
                        max={Math.min(4, adults)}
                        onDecrement={() => update("infants", -1)}
                        onIncrement={() => update("infants", 1)}
                        last
                    />
                    <button
                        type="button"
                        onClick={() => setIsOpen(false)}
                        className="mt-3 w-full rounded-lg bg-ink-900 py-2 text-sm font-semibold text-cream-100 hover:bg-ink-800 transition-colors"
                    >
                        Done
                    </button>
                </div>
            )}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Counter row                                                        */
/* ------------------------------------------------------------------ */

function CounterRow({
    label,
    subtitle,
    value,
    min,
    max,
    onDecrement,
    onIncrement,
    last = false,
}: {
    label: string;
    subtitle: string;
    value: number;
    min: number;
    max: number;
    onDecrement: () => void;
    onIncrement: () => void;
    last?: boolean;
}) {
    return (
        <div
            className={cn(
                "flex items-center justify-between py-3",
                !last && "border-b border-ink-100/50",
            )}
        >
            <div>
                <p className="text-sm font-semibold text-ink-900">{label}</p>
                <p className="text-xs text-ink-400">{subtitle}</p>
            </div>
            <div className="flex items-center gap-3">
                <button
                    type="button"
                    onClick={onDecrement}
                    disabled={value <= min}
                    className={cn(
                        "flex h-8 w-8 items-center justify-center rounded-full border border-ink-200",
                        "text-ink-600 transition-colors",
                        "hover:bg-ink-50 hover:border-ink-300",
                        "disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent",
                    )}
                    aria-label={`Decrease ${label}`}
                >
                    <Minus className="h-3.5 w-3.5" />
                </button>
                <span className="w-6 text-center text-sm font-bold text-ink-900 tabular-nums">
                    {value}
                </span>
                <button
                    type="button"
                    onClick={onIncrement}
                    disabled={value >= max}
                    className={cn(
                        "flex h-8 w-8 items-center justify-center rounded-full border border-ink-200",
                        "text-ink-600 transition-colors",
                        "hover:bg-ink-50 hover:border-ink-300",
                        "disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent",
                    )}
                    aria-label={`Increase ${label}`}
                >
                    <Plus className="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    );
}
