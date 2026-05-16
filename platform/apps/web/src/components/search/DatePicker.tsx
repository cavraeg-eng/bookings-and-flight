"use client";

import { useId } from "react";
import { Calendar } from "lucide-react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface DatePickerProps {
    label: string;
    value: string; // ISO date string YYYY-MM-DD
    onChange: (date: string) => void;
    minDate?: string;
    maxDate?: string;
    className?: string;
    name?: string;
    required?: boolean;
}

/* ------------------------------------------------------------------ */
/*  Helpers                                                            */
/* ------------------------------------------------------------------ */

function todayISO(): string {
    return new Date().toISOString().slice(0, 10);
}

function oneYearFromNow(): string {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    return d.toISOString().slice(0, 10);
}

function formatDisplay(iso: string): string {
    if (!iso) return "";
    try {
        const d = new Date(iso + "T00:00:00");
        return d.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
        });
    } catch {
        return iso;
    }
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function DatePicker({
    label,
    value,
    onChange,
    minDate,
    maxDate,
    className,
    name,
    required = false,
}: DatePickerProps) {
    const id = useId();

    return (
        <div className={cn("flex flex-col gap-1.5", className)}>
            <label
                htmlFor={id}
                className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400"
            >
                {label}
            </label>
            <div className="relative">
                <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center text-ink-300">
                    <Calendar className="h-4 w-4" />
                </span>
                <input
                    type="date"
                    id={id}
                    name={name}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    min={minDate ?? todayISO()}
                    max={maxDate ?? oneYearFromNow()}
                    required={required}
                    className={cn(
                        "w-full border-0 bg-transparent py-0 pl-6 pr-0 text-[0.95rem] font-semibold",
                        "text-ink-900 focus:ring-0 focus:outline-none",
                        "[color-scheme:light]",
                        !value && "text-ink-300",
                    )}
                />
            </div>
            {value && (
                <span className="text-xs text-ink-400 -mt-0.5">
                    {formatDisplay(value)}
                </span>
            )}
        </div>
    );
}
