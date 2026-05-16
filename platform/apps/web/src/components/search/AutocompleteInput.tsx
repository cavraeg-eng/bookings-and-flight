"use client";

import { useState, useRef, useCallback, useEffect, type KeyboardEvent } from "react";
import { Plane, Loader2, MapPin } from "lucide-react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export interface AirportSuggestion {
    code: string;
    name: string;
    city: string;
    country: string;
    type?: "airport" | "city";
}

export interface AutocompleteInputProps {
    label: string;
    value: AirportSuggestion | null;
    onChange: (airport: AirportSuggestion) => void;
    placeholder?: string;
    className?: string;
    /** Hidden input name for form submission */
    name?: string;
    /** Show city-style results (no IATA code emphasis) */
    cityMode?: boolean;
    /** Mirrors raw input text so parent forms can validate typed-but-unselected values. */
    onInputValueChange?: (value: string) => void;
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function AutocompleteInput({
    label,
    value,
    onChange,
    placeholder = "Search airports...",
    className,
    name,
    cityMode = false,
    onInputValueChange,
}: AutocompleteInputProps) {
    const [query, setQuery] = useState(value ? displayValue(value, cityMode) : "");
    const [suggestions, setSuggestions] = useState<AirportSuggestion[]>([]);
    const [isOpen, setIsOpen] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const [activeIndex, setActiveIndex] = useState(-1);

    const wrapperRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const listRef = useRef<HTMLUListElement>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout>>();

    // Sync display when value changes externally
    useEffect(() => {
        if (value) setQuery(displayValue(value, cityMode));
    }, [value, cityMode]);

    const fetchSuggestions = useCallback(
        async (q: string) => {
            if (q.length < 1) {
                setSuggestions([]);
                setIsOpen(false);
                return;
            }
            setIsLoading(true);
            try {
                const typeParam = cityMode ? "&type=city" : "";
                const res = await fetch(`/api/autocomplete?q=${encodeURIComponent(q)}${typeParam}`);
                if (res.ok) {
                    const data: AirportSuggestion[] = await res.json();
                    setSuggestions(data);
                    setIsOpen(data.length > 0 || q.length >= 2);
                    setActiveIndex(-1);
                }
            } catch {
                setSuggestions([]);
            } finally {
                setIsLoading(false);
            }
        },
        [cityMode],
    );

    function handleInputChange(val: string) {
        setQuery(val);
        onInputValueChange?.(val);
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => fetchSuggestions(val), 300);
    }

    function selectSuggestion(s: AirportSuggestion) {
        const nextValue = displayValue(s, cityMode);
        onChange(s);
        setQuery(nextValue);
        setIsOpen(false);
        setSuggestions([]);
        setActiveIndex(-1);
        inputRef.current?.blur();
    }

    function handleKeyDown(e: KeyboardEvent<HTMLInputElement>) {
        if (!isOpen) return;

        switch (e.key) {
            case "ArrowDown":
                e.preventDefault();
                setActiveIndex((prev) => (prev < suggestions.length - 1 ? prev + 1 : 0));
                break;
            case "ArrowUp":
                e.preventDefault();
                setActiveIndex((prev) => (prev > 0 ? prev - 1 : suggestions.length - 1));
                break;
            case "Enter":
                e.preventDefault();
                if (activeIndex >= 0 && suggestions[activeIndex]) {
                    selectSuggestion(suggestions[activeIndex]);
                }
                break;
            case "Escape":
                setIsOpen(false);
                setActiveIndex(-1);
                break;
        }
    }

    // Scroll active item into view
    useEffect(() => {
        if (activeIndex >= 0 && listRef.current) {
            const item = listRef.current.children[activeIndex] as HTMLElement | undefined;
            item?.scrollIntoView({ block: "nearest" });
        }
    }, [activeIndex]);

    // Close dropdown on outside click
    useEffect(() => {
        function handleClick(e: MouseEvent) {
            if (wrapperRef.current && !wrapperRef.current.contains(e.target as Node)) {
                setIsOpen(false);
            }
        }
        document.addEventListener("mousedown", handleClick);
        return () => document.removeEventListener("mousedown", handleClick);
    }, []);

    return (
        <div ref={wrapperRef} className={cn("relative", className)}>
            <label className="flex flex-col gap-1.5">
                <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                    {label}
                </span>
                <div className="relative">
                    <input
                        ref={inputRef}
                        type="text"
                        role="combobox"
                        aria-expanded={isOpen}
                        aria-autocomplete="list"
                        aria-activedescendant={activeIndex >= 0 ? `suggestion-${activeIndex}` : undefined}
                        value={query}
                        onChange={(e) => handleInputChange(e.target.value)}
                        onFocus={() => {
                            if (suggestions.length > 0) setIsOpen(true);
                        }}
                        onKeyDown={handleKeyDown}
                        placeholder={placeholder}
                        className={cn(
                            "w-full border-0 bg-transparent p-0 pr-8 text-[0.95rem] font-semibold",
                            "focus:ring-0 focus:outline-none",
                            "text-ink-900 placeholder:text-ink-300",
                        )}
                    />
                    {/* Hidden input for form submission */}
                    {name && <input type="hidden" name={name} value={value?.code ?? query} />}

                    {/* Loading spinner */}
                    {isLoading && (
                        <span className="absolute inset-y-0 right-0 flex items-center">
                            <Loader2 className="h-4 w-4 animate-spin text-ink-300" />
                        </span>
                    )}
                </div>
            </label>

            {/* Dropdown */}
            {isOpen && (
                <ul
                    ref={listRef}
                    role="listbox"
                    className={cn(
                        "absolute left-0 right-0 top-full z-50 mt-2",
                        "max-h-64 overflow-y-auto rounded-xl border border-ink-200/60 bg-white",
                        "shadow-search animate-slideDown",
                    )}
                >
                    {suggestions.length === 0 && !isLoading && (
                        <li className="px-4 py-3 text-sm text-ink-400">
                            No results found
                        </li>
                    )}
                    {suggestions.map((s, i) => (
                        <li
                            key={`${s.code}-${i}`}
                            id={`suggestion-${i}`}
                            role="option"
                            aria-selected={i === activeIndex}
                            onMouseDown={(e) => {
                                e.preventDefault();
                                selectSuggestion(s);
                            }}
                            onMouseEnter={() => setActiveIndex(i)}
                            className={cn(
                                "flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors",
                                i === activeIndex
                                    ? "bg-sky-50 text-ink-900"
                                    : "text-ink-700 hover:bg-cream-50",
                                i < suggestions.length - 1 && "border-b border-ink-100/50",
                            )}
                        >
                            <span
                                className={cn(
                                    "flex h-8 w-8 shrink-0 items-center justify-center rounded-lg",
                                    i === activeIndex ? "bg-sky-100 text-sky-600" : "bg-cream-100 text-ink-400",
                                )}
                            >
                                {cityMode ? (
                                    <MapPin className="h-4 w-4" />
                                ) : (
                                    <Plane className="h-4 w-4" />
                                )}
                            </span>
                            <div className="min-w-0 flex-1">
                                <div className="flex items-center gap-2">
                                    {!cityMode && (
                                        <span className="font-mono text-sm font-bold text-ink-900 tracking-wide">
                                            {s.code}
                                        </span>
                                    )}
                                    <span className="truncate text-sm font-medium text-ink-700">
                                        {s.name}
                                    </span>
                                </div>
                                <p className="text-xs text-ink-400 truncate">
                                    {s.city}, {s.country}
                                </p>
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}

function displayValue(s: AirportSuggestion, cityMode: boolean): string {
    if (cityMode) {
        return [s.city || s.name || s.code, s.country].filter(Boolean).join(", ");
    }

    return s.city ? `${s.code} — ${s.city}` : s.code;
}
