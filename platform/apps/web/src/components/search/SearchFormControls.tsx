"use client";

import { Search } from "lucide-react";
import { cn } from "@/lib/cn";

export function GuestSelector({
    adults,
    children: childCount,
    onChange,
    className,
    showRooms = false,
    rooms = 1,
    onRoomsChange,
}: {
    adults: number;
    children: number;
    onChange: (v: { adults: number; children: number }) => void;
    className?: string;
    showRooms?: boolean;
    rooms?: number;
    onRoomsChange?: (v: number) => void;
}) {
    return (
        <div className={cn("flex flex-col gap-1.5", className)}>
            <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                {showRooms ? "Guests & Rooms" : "Travelers"}
            </span>
            <div className="flex items-center gap-3 flex-wrap">
                <label className="flex items-center gap-1.5 text-sm">
                    <span className="text-ink-500">Adults</span>
                    <select
                        name="adults"
                        value={adults}
                        onChange={(e) => onChange({ adults: Number(e.target.value), children: childCount })}
                        className="border-0 bg-transparent p-0 pr-5 text-[0.95rem] font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                    >
                        {Array.from({ length: 9 }, (_, i) => (
                            <option key={i + 1} value={i + 1}>{i + 1}</option>
                        ))}
                    </select>
                </label>
                <label className="flex items-center gap-1.5 text-sm">
                    <span className="text-ink-500">Children</span>
                    <select
                        name="children"
                        value={childCount}
                        onChange={(e) => onChange({ adults, children: Number(e.target.value) })}
                        className="border-0 bg-transparent p-0 pr-5 text-[0.95rem] font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                    >
                        {Array.from({ length: 9 }, (_, i) => (
                            <option key={i} value={i}>{i}</option>
                        ))}
                    </select>
                </label>
                {showRooms && onRoomsChange && (
                    <label className="flex items-center gap-1.5 text-sm">
                        <span className="text-ink-500">Rooms</span>
                        <select
                            name="rooms"
                            value={rooms}
                            onChange={(e) => onRoomsChange(Number(e.target.value))}
                            className="border-0 bg-transparent p-0 pr-5 text-[0.95rem] font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                        >
                            {Array.from({ length: 5 }, (_, i) => (
                                <option key={i + 1} value={i + 1}>{i + 1}</option>
                            ))}
                        </select>
                    </label>
                )}
            </div>
        </div>
    );
}

export function SearchButton({ label }: { label: string }) {
    return (
        <button
            type="submit"
            className={cn(
                "inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl",
                "bg-amber-500 hover:bg-amber-400 active:bg-amber-600 active:scale-[0.97]",
                "text-ink-900 font-semibold text-sm",
                "shadow-card hover:shadow-float transition-all duration-200",
                "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2",
            )}
        >
            <Search className="h-4 w-4" strokeWidth={2.5} />
            <span>{label}</span>
        </button>
    );
}
