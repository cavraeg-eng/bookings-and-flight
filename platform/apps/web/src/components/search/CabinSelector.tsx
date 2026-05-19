"use client";

import { useState, useRef, useEffect } from "react";
import { ChevronDown } from "lucide-react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type CabinClass = "economy" | "premium" | "business" | "first";

export interface CabinSelectorProps {
    value: CabinClass;
    onChange: (cabin: CabinClass) => void;
    className?: string;
}

const CABIN_OPTIONS: { value: CabinClass; label: string }[] = [
    { value: "economy", label: "Economy" },
    { value: "premium", label: "Premium Economy" },
    { value: "business", label: "Business" },
    { value: "first", label: "First Class" },
];

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function CabinSelector({ value, onChange, className }: CabinSelectorProps) {
    const [isOpen, setIsOpen] = useState(false);
    const wrapperRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        function handleClick(e: MouseEvent) {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setIsOpen(false);
            }
        }
        document.addEventListener("mousedown", handleClick);
        return () => document.removeEventListener("mousedown", handleClick);
    }, []);

    const currentLabel = CABIN_OPTIONS.find((o) => o.value === value)?.label ?? "Economy";

    return (
        <div ref={wrapperRef} className={cn("relative", className)}>
            <div className="flex flex-col gap-1.5">
                <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                    Cabin
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
                    <span className="truncate">{currentLabel}</span>
                    <ChevronDown
                        className={cn(
                            "h-3.5 w-3.5 text-ink-400 shrink-0 transition-transform",
                            isOpen && "rotate-180",
                        )}
                    />
                </button>
            </div>

            {/* Hidden input for form submission */}
            <input type="hidden" name="cabin" value={value} />

            {isOpen && (
                <div
                    className={cn(
                        "absolute left-0 top-full z-50 mt-2 w-52",
                        "rounded-xl border border-ink-200/60 bg-white py-1",
                        "shadow-search animate-slideDown",
                    )}
                >
                    {CABIN_OPTIONS.map((opt) => (
                        <button
                            key={opt.value}
                            type="button"
                            onClick={() => {
                                onChange(opt.value);
                                setIsOpen(false);
                            }}
                            className={cn(
                                "w-full px-4 py-2.5 text-left text-sm transition-colors",
                                opt.value === value
                                    ? "bg-sky-50 text-sky-700 font-semibold"
                                    : "text-ink-700 hover:bg-cream-50 font-medium",
                            )}
                        >
                            {opt.label}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
