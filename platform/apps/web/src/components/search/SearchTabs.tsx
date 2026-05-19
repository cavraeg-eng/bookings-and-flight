import { Plane, Hotel, Car, Compass } from "lucide-react";
import { cn } from "@/lib/cn";
import type { Vertical } from "./SearchFormSupport";

const TABS: { id: Vertical; label: string; Icon: typeof Plane }[] = [
    { id: "flights", label: "Flights", Icon: Plane },
    { id: "hotels", label: "Hotels", Icon: Hotel },
    { id: "cars", label: "Cars", Icon: Car },
    { id: "activities", label: "Activities", Icon: Compass },
];

interface SearchTabsProps {
    activeTab: Vertical;
    compact: boolean;
    onChange: (tab: Vertical) => void;
}

export function SearchTabs({ activeTab, compact, onChange }: SearchTabsProps) {
    return (
        <div
            role="tablist"
            aria-label="Search type"
            className={cn(
                "inline-flex flex-wrap items-center gap-1 p-1",
                compact
                    ? "rounded-full bg-white border border-ink-900/10 shadow-subtle"
                    : "rounded-full bg-white/80 backdrop-blur-sm border border-white/30 shadow-subtle",
            )}
        >
            {TABS.map((t) => {
                const active = activeTab === t.id;
                return (
                    <button
                        key={t.id}
                        role="tab"
                        aria-selected={active}
                        onClick={() => onChange(t.id)}
                        type="button"
                        className={cn(
                            "inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200",
                            active
                                ? "bg-ink-900 text-cream-100 shadow-card"
                                : "text-ink-500 hover:text-ink-900 hover:bg-ink-900/[0.04]",
                            active && !compact && "border-b-2 border-amber-500",
                        )}
                    >
                        <t.Icon className="h-4 w-4" strokeWidth={2.25} />
                        <span className="hidden sm:inline">{t.label}</span>
                    </button>
                );
            })}
        </div>
    );
}
