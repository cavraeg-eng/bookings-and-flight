"use client";

import { Suspense, useState, useEffect, useMemo } from "react";
import { useSearchParams } from "next/navigation";
import { Compass, SearchX, ArrowUpDown, Filter, X } from "lucide-react";
import { ResultsHero } from "@/components/ResultsHero";
import { SearchForm } from "@/components/search/SearchForm";
import { ActivityCard } from "@/components/results/ActivityCard";
import { Skeleton } from "@/components/ui/Skeleton";
import { cn } from "@/lib/cn";
import { openTrackedOffer } from "@/lib/clicks";
import { inDays } from "@/lib/search";
import type { Offer, SearchResponse } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Root page                                                          */
/* ------------------------------------------------------------------ */

export default function ActivitiesPage() {
    return (
        <Suspense>
            <ActivitiesPageContent />
        </Suspense>
    );
}

/* ------------------------------------------------------------------ */
/*  Click-through handler                                              */
/* ------------------------------------------------------------------ */

async function handleBookClick(offer: Offer) {
    await openTrackedOffer(offer);
}

/* ------------------------------------------------------------------ */
/*  Sort                                                               */
/* ------------------------------------------------------------------ */

type ActivitySortKey = "popular" | "price-asc" | "price-desc" | "category";

const SORT_OPTIONS: { key: ActivitySortKey; label: string }[] = [
    { key: "popular", label: "Popular" },
    { key: "price-asc", label: "Price: Low → High" },
    { key: "price-desc", label: "Price: High → Low" },
    { key: "category", label: "Category" },
];

function sortOffers(offers: Offer[], sort: ActivitySortKey): Offer[] {
    const sorted = [...offers];
    switch (sort) {
        case "price-asc":
            return sorted.sort((a, b) => a.price.amount - b.price.amount);
        case "price-desc":
            return sorted.sort((a, b) => b.price.amount - a.price.amount);
        case "category": {
            const cat = (o: Offer) => ((o.metadata as Record<string, unknown>)?.category as string) ?? "";
            return sorted.sort((a, b) => cat(a).localeCompare(cat(b)));
        }
        case "popular":
        default:
            return sorted;
    }
}

/* ------------------------------------------------------------------ */
/*  Category filter                                                    */
/* ------------------------------------------------------------------ */

const FILTER_CATEGORIES = ["Tours", "Day Trips", "Museums", "Adventure", "Food"] as const;

function extractCategories(offers: Offer[]): string[] {
    const cats = new Set<string>();
    for (const o of offers) {
        const cat = ((o.metadata as Record<string, unknown>)?.category as string) ?? "";
        if (cat) cats.add(cat);
    }
    return Array.from(cats).sort();
}

function matchesCategory(offer: Offer, filterCat: string): boolean {
    const cat = (((offer.metadata as Record<string, unknown>)?.category as string) ?? "").toLowerCase();
    return cat.includes(filterCat.toLowerCase());
}

/* ------------------------------------------------------------------ */
/*  Loading skeleton                                                   */
/* ------------------------------------------------------------------ */

function ActivityResultsSkeleton() {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" role="status" aria-label="Loading activity results">
            {Array.from({ length: 6 }, (_, i) => (
                <div key={i} className="overflow-hidden rounded-2xl border border-cream-200/60 bg-white shadow-subtle">
                    <Skeleton variant="custom" width="100%" height="180px" className="rounded-none" />
                    <div className="p-5 space-y-3">
                        <Skeleton variant="text" className="w-4/5" />
                        <Skeleton variant="text" className="w-full" />
                        <Skeleton variant="text" className="w-2/3" />
                        <div className="flex justify-between items-end pt-3 border-t border-cream-200/60">
                            <Skeleton variant="custom" width="90px" height="32px" />
                            <Skeleton variant="custom" width="110px" height="36px" className="rounded-lg" />
                        </div>
                    </div>
                </div>
            ))}
            <span className="sr-only">Loading...</span>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Content                                                            */
/* ------------------------------------------------------------------ */

function ActivitiesPageContent() {
    const params = useSearchParams();

    const destination = params.get("destination") ?? "";
    const from = params.get("from") ?? "";
    const to = params.get("to") ?? undefined;
    const adults = Number(params.get("adults") ?? "2");
    const children = Number(params.get("children") ?? "0");

    const hasSearch = Boolean(destination && from);

    /* ---- State ---- */
    const [offers, setOffers] = useState<Offer[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [sort, setSort] = useState<ActivitySortKey>("popular");
    const [activeCategory, setActiveCategory] = useState<string | null>(null);

    /* ---- Fetch ---- */
    useEffect(() => {
        if (!hasSearch) return;

        const ctrl = new AbortController();
        setLoading(true);
        setError(null);
        setOffers([]);
        setActiveCategory(null);

        (async () => {
            try {
                const res = await fetch("/api/search/activities", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        destination,
                        from,
                        ...(to ? { to } : {}),
                        adults,
                        children,
                    }),
                    signal: ctrl.signal,
                });
                if (!res.ok) throw new Error(`Search failed (${res.status})`);
                const data: SearchResponse = await res.json();
                setOffers(data.offers);
            } catch (err: unknown) {
                if ((err as Error).name !== "AbortError") {
                    setError((err as Error).message ?? "Something went wrong");
                }
            } finally {
                setLoading(false);
            }
        })();

        return () => ctrl.abort();
    }, [destination, from, to, adults, children, hasSearch]);

    /* ---- Derived ---- */
    const availableCategories = useMemo(() => extractCategories(offers), [offers]);

    const filtered = useMemo(() => {
        if (!activeCategory) return offers;
        return offers.filter((o) => matchesCategory(o, activeCategory));
    }, [offers, activeCategory]);

    const sorted = useMemo(() => sortOffers(filtered, sort), [filtered, sort]);

    /* ---- No search params → landing ---- */
    if (!hasSearch) {
        return (
            <main>
                <section className="relative">
                    <div aria-hidden className="absolute inset-0 bg-sky-wash opacity-70 pointer-events-none" />
                    <div className="container relative pt-20 sm:pt-28 pb-16 sm:pb-24">
                        <p className="text-[0.68rem] uppercase tracking-[0.22em] text-ink-400 font-semibold">
                            Things to do
                        </p>
                        <h1 className="mt-2 font-display text-display-lg text-ink-900 text-balance">
                            Discover amazing activities.
                        </h1>
                        <p className="mt-4 max-w-xl text-ink-500 leading-relaxed">
                            Browse tours, day trips, museum tickets, and unique experiences at your destination. Book with confidence from top providers.
                        </p>
                        <div className="mt-10">
                            <SearchForm initialTab="activities" />
                        </div>
                    </div>
                </section>
            </main>
        );
    }

    /* ---- Results layout ---- */
    return (
        <main>
            <ResultsHero
                eyebrow={`${from}${to ? ` → ${to}` : ""} · ${adults} traveler${adults === 1 ? "" : "s"}${children > 0 ? ` · ${children} child${children === 1 ? "" : "ren"}` : ""}`}
                title={
                    <>
                        Things to do in{" "}
                        <em className="italic font-normal text-ink-500">{destination}</em>
                    </>
                }
                searchWidget={
                    <SearchForm
                        initialTab="activities"
                        compact
                        initialValues={{
                            destination,
                            from,
                            to: to ?? "",
                            adults: String(adults),
                            ...(children > 0 ? { children: String(children) } : {}),
                        }}
                    />
                }
            />

            <section className="container py-8 pb-20">
                {/* Loading */}
                {loading && <ActivityResultsSkeleton />}

                {/* Error */}
                {!loading && error && (
                    <div className="flex flex-col items-center justify-center py-20 text-center animate-fadeIn">
                        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-500 mb-4">
                            <SearchX className="h-8 w-8" />
                        </div>
                        <h2 className="font-display text-2xl text-ink-900">Something went wrong</h2>
                        <p className="mt-2 text-ink-500 max-w-md">{error}</p>
                    </div>
                )}

                {/* No results */}
                {!loading && !error && offers.length === 0 && (
                    <div className="flex flex-col items-center justify-center py-20 text-center animate-fadeIn">
                        <div className="flex h-16 w-16 items-center justify-center rounded-full bg-sky-50 text-sky-500 mb-4">
                            <Compass className="h-8 w-8" />
                        </div>
                        <h2 className="font-display text-2xl text-ink-900">No activities found</h2>
                        <p className="mt-2 text-ink-500 max-w-md">
                            We couldn&apos;t find any activities in {destination} for your dates. Try adjusting your search — different dates or a broader location often reveal more options.
                        </p>
                    </div>
                )}

                {/* Results */}
                {!loading && !error && offers.length > 0 && (
                    <div>
                        {/* Sort + filter bar */}
                        <div className="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-xl border border-cream-200/60 bg-white px-4 py-3 shadow-subtle">
                            <div className="flex items-center gap-3 flex-wrap">
                                <p className="text-sm text-ink-500">
                                    <span className="font-semibold text-ink-900">{sorted.length}</span>{" "}
                                    {sorted.length === 1 ? "activity" : "activities"}
                                </p>

                                {/* Category filter pills */}
                                {availableCategories.length > 0 && (
                                    <div className="flex items-center gap-1.5 flex-wrap">
                                        <Filter className="h-3.5 w-3.5 text-ink-300" />
                                        {availableCategories.map((cat) => (
                                            <button
                                                key={cat}
                                                type="button"
                                                onClick={() => setActiveCategory(activeCategory === cat ? null : cat)}
                                                className={cn(
                                                    "px-3 py-1.5 rounded-pill text-xs font-semibold transition-all duration-200",
                                                    activeCategory === cat
                                                        ? "bg-amber-500 text-ink-900 shadow-subtle"
                                                        : "bg-cream-100 text-ink-500 hover:bg-cream-200 hover:text-ink-700",
                                                )}
                                            >
                                                {cat}
                                            </button>
                                        ))}
                                        {activeCategory && (
                                            <button
                                                type="button"
                                                onClick={() => setActiveCategory(null)}
                                                className="text-ink-400 hover:text-ink-600 ml-1"
                                                aria-label="Clear filter"
                                            >
                                                <X className="h-4 w-4" />
                                            </button>
                                        )}
                                    </div>
                                )}
                            </div>

                            <div className="flex items-center gap-2 shrink-0">
                                <ArrowUpDown className="h-4 w-4 text-ink-400" />
                                <select
                                    value={sort}
                                    onChange={(e) => setSort(e.target.value as ActivitySortKey)}
                                    className="border-0 bg-transparent p-0 pr-6 text-sm font-semibold text-ink-900 focus:ring-0 focus:outline-none cursor-pointer"
                                >
                                    {SORT_OPTIONS.map((o) => (
                                        <option key={o.key} value={o.key}>{o.label}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Cards grid */}
                        {sorted.length === 0 ? (
                            <div className="flex flex-col items-center justify-center py-16 text-center animate-fadeIn">
                                <p className="text-ink-500">
                                    No activities match this category.{" "}
                                    <button
                                        onClick={() => setActiveCategory(null)}
                                        className="text-amber-600 font-semibold hover:underline"
                                    >
                                        Clear filter
                                    </button>
                                </p>
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                {sorted.map((offer, i) => (
                                    <ActivityCard
                                        key={offer.supplierOfferRef}
                                        offer={offer}
                                        onBookClick={handleBookClick}
                                        index={i}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </section>
        </main>
    );
}
