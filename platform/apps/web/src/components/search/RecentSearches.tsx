import { Plane, Hotel, Car, Compass, Clock, X } from "lucide-react";
import { cn } from "@/lib/cn";
import { removeRecentAt, type RecentSearch, type Vertical } from "./SearchFormSupport";

const TAB_ICONS: Record<Vertical, typeof Plane> = {
    flights: Plane,
    hotels: Hotel,
    cars: Car,
    activities: Compass,
};

interface RecentSearchesProps {
    compact: boolean;
    recentSearches: RecentSearch[];
    onOpen: (url: string) => void;
    onChange: (items: RecentSearch[]) => void;
}

export function RecentSearches({ compact, recentSearches, onOpen, onChange }: RecentSearchesProps) {
    if (recentSearches.length === 0 || compact) {
        return null;
    }

    return (
        <div className="mt-4 flex flex-wrap items-center gap-2">
            <span className="flex items-center gap-1.5 text-xs font-medium text-ink-400">
                <Clock className="h-3 w-3" />
                Recent:
            </span>
            {recentSearches.map((r, i) => {
                const TabIcon = TAB_ICONS[r.tab];
                return (
                    <button
                        key={r.ts}
                        type="button"
                        onClick={() => onOpen(r.url)}
                        className={cn(
                            "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium",
                            "bg-white/80 backdrop-blur-sm border border-ink-200/40 text-ink-600",
                            "hover:bg-white hover:border-ink-300 hover:text-ink-900 transition-all",
                            "shadow-subtle",
                        )}
                    >
                        <span className="text-ink-400">
                            <TabIcon className="h-3 w-3" />
                        </span>
                        <span className="truncate max-w-[180px]">{r.label}</span>
                        <span
                            role="button"
                            tabIndex={0}
                            onClick={(e) => {
                                e.stopPropagation();
                                onChange(removeRecentAt(i));
                            }}
                            onKeyDown={(e) => {
                                if (e.key === "Enter" || e.key === " ") {
                                    e.stopPropagation();
                                    onChange(removeRecentAt(i));
                                }
                            }}
                            className="text-ink-300 hover:text-ink-600"
                            aria-label="Remove recent search"
                        >
                            <X className="h-3 w-3" />
                        </span>
                    </button>
                );
            })}
        </div>
    );
}
