import type { AirportSuggestion } from "./AutocompleteInput";

export type Vertical = "flights" | "hotels" | "cars" | "activities";

export interface SearchFormProps {
    initialTab?: Vertical;
    initialValues?: Partial<Record<string, string>>;
    compact?: boolean;
    className?: string;
}

export interface RecentSearch {
    tab: Vertical;
    label: string;
    url: string;
    ts: number;
}

const RECENT_KEY = "bf-recent-searches";
const MAX_RECENT = 5;

export function loadRecent(): RecentSearch[] {
    if (typeof window === "undefined") return [];
    try {
        return JSON.parse(localStorage.getItem(RECENT_KEY) ?? "[]");
    } catch {
        return [];
    }
}

export function saveRecent(search: RecentSearch) {
    const list = loadRecent().filter((r) => r.url !== search.url);
    list.unshift(search);
    localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, MAX_RECENT)));
}

export function removeRecentAt(index: number): RecentSearch[] {
    const updated = loadRecent().filter((_, idx) => idx !== index);
    localStorage.setItem(RECENT_KEY, JSON.stringify(updated));
    return updated;
}

export function suggestionDisplay(s: AirportSuggestion, cityMode = false): string {
    if (cityMode) {
        return [s.city || s.name || s.code, s.country].filter(Boolean).join(", ");
    }

    return s.city ? `${s.code} — ${s.city}` : s.code;
}

export function selectedFlightCode(selection: AirportSuggestion | null, query: string): string | null {
    const typed = query.trim();
    if (selection && typed === suggestionDisplay(selection)) {
        return selection.code;
    }

    return null;
}

export function searchLocationValue(selection: AirportSuggestion | null, query: string, cityMode = false): string {
    const typed = query.trim();
    if (selection && typed === suggestionDisplay(selection, cityMode)) {
        return cityMode ? selection.city || selection.name || selection.code : selection.code || selection.city;
    }

    return typed;
}
