"use client";

import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type SortOption = "best" | "cheapest" | "fastest" | "earliest";

export interface SortControlsProps {
    sortBy: SortOption;
    onSortChange: (sort: SortOption) => void;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Options                                                            */
/* ------------------------------------------------------------------ */

const SORT_OPTIONS: { id: SortOption; label: string }[] = [
    { id: "best", label: "Best" },
    { id: "cheapest", label: "Cheapest" },
    { id: "fastest", label: "Fastest" },
    { id: "earliest", label: "Earliest" },
];

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function SortControls({ sortBy, onSortChange, className }: SortControlsProps) {
    return (
        <div className={cn("flex items-center gap-1.5 flex-wrap", className)}>
            <span className="text-[0.68rem] font-semibold uppercase tracking-[0.14em] text-ink-400 mr-1">
                Sort by
            </span>
            {SORT_OPTIONS.map((opt) => {
                const active = sortBy === opt.id;
                return (
                    <button
                        key={opt.id}
                        type="button"
                        onClick={() => onSortChange(opt.id)}
                        className={cn(
                            "px-3.5 py-1.5 rounded-pill text-xs font-semibold transition-all duration-200",
                            active
                                ? "bg-amber-500 text-ink-900 shadow-subtle"
                                : "bg-white border border-ink-200/60 text-ink-500 hover:border-ink-300 hover:text-ink-700",
                        )}
                    >
                        {opt.label}
                    </button>
                );
            })}
        </div>
    );
}
