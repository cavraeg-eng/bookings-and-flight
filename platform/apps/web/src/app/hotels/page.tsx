"use client";

import { Suspense, useState, useEffect, useMemo } from "react";
import { useSearchParams } from "next/navigation";
import { ArrowUpDown, Hotel, SearchX } from "lucide-react";
import { ResultsHero } from "@/components/ResultsHero";
import { SearchForm } from "@/components/search/SearchForm";
import { HotelCard } from "@/components/results/HotelCard";
import { HotelFiltersPanel, applyHotelFilters, EMPTY_FILTERS } from "@/components/results/HotelFilters";
import { HotelResultsSkeleton } from "@/components/results/HotelResultsSkeleton";
import { cn } from "@/lib/cn";
import { inDays } from "@/lib/search";
import type { Offer, SearchResponse } from "@baf/shared";
import type { HotelFilters } from "@/components/results/HotelFilters";

/* ------------------------------------------------------------------ */
/*  Sort                                                               */
/* ------------------------------------------------------------------ */

type SortKey = "price-asc" | "price-desc" | "rating" | "popular";

const SORT_OPTIONS: { key: SortKey; label: string }[] = [
    { key: "popular", label: "Popular" },
    { key: "price-asc", label: "Price: Low → High" },
    { key: "price-desc", label: "Price: High → Low" },
    { key: "rating", label: "Rating" },
];

function sortOffers(offers: Offer[], sort: SortKey): Offer[] {
    const sorted = [...offers];
    switch (sort) {
        case "price-asc":
            return sorted.sort((a, b) => a.price.amount - b.price.amount);
        case "price-desc":
            return sorted.sort((a, b) => b.price.amount - a.price.amount);
        case "rating": {
            return sorted.sort((a, b) => {
                const ra = typeof (a.metadata as Record<string, unknown>)?.reviewScore === "number"
                    ? (a.metadata as Record<string, unknown>).reviewScore as number : 0;
                const rb = typeof (b.metadata as Record<string, unknown>)?.reviewScore === "number"
                    ? (b.metadata as Record<string, unknown>).reviewScore as number : 0;
                return rb - ra;
            });
        }
        case "popular":
        default:
            return sorted;
    }
}

/* ------------------------------------------------------------------ */
/*  Page (with Suspense boundary for useSearchParams)                  */
/* ------------------------------------------------------------------ */

export default function HotelsPage() {
    return (
        <Suspense>
            <HotelsPageContent />
        </Suspense>
    );
}

/* ------------------------------------------------------------------ */
/*  Content                                                            */
/* ------------------------------------------------------------------ */

function HotelsPageContent() {
    const params = useSearchParams();

    const destination = params.get("destination") ?? "";
    const checkIn = params.get("checkIn") ?? "";
    const checkOut = params.get("checkOut") ?? "";
    const adults = params.get("adults") ?? "";
    const children = params.get("children") ?? "";
    const rooms = params.get("rooms") ?? "";

    const hasSearch = Boolean(destination && checkIn && checkOut);

    /* ---- Search state ---- */
    const [offers, setOffers] = useState<Offer[]>([]);
    const [searchId, setSearchId] = useState<string>("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [filters, setFilters] = useState<HotelFilters>(EMPTY_FILTERS);
    const [sort, setSort] = useState<SortKey>("popular");

    /* ---- Fetch ---- */
    useEffect(() => {
        if (!hasSearch) return;

        const ctrl = new AbortController();
        setLoading(true);
        setError(null);
        setOffers([]);
        setFilters(EMPTY_FILTERS);

        (async () => {
            try {
                const res = await fetch("/api/search/hotels", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        destination,
                        checkIn,
                        checkOut,
                        adults: Number(adults) || 2,
                        children: Number(children) || 0,
                        rooms: Number(rooms) || 1,
                    }),
                    signal: ctrl.signal,
                });
                if (!res.ok) throw new Error(`Search failed (${res.status})`);
                const data: SearchResponse = await res.json();
                setSearchId(data.searchId);
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
    }, [destination, checkIn, checkOut, adults, children, rooms, hasSearch]);

    /* ---- Derived ---- */
    const filtered = useMemo(() => applyHotelFilters(offers, filters), [offers, filters]);
    const sorted = useMemo(() => sortOffers(filtered, sort), [filtered, sort]);

    /* ---- No search params → show full search form ---- */
    if (!hasSearch) {
        return (
            <main className="bg-cream-100">
                <section className="relative border-b border-ink-900/10">
                    <div aria-hidden className="absolute inset-0 bg-sky-wash opacity-70 pointer-events-none" />
                    <div className="container relative pt-16 sm:pt-24 pb-16">
                        <p className="text-[0.68rem] uppercase tracking-[0.22em] text-ink-400 font-semibold mb-2">
                            Hotel search
                        </p>
                        <h1 className="font-display text-display-lg text-ink-900 text-balance">
                            Find your perfect stay.
                        </h1>
                        <p className="mt-4 max-w-2xl text-lg text-ink-500 leading-relaxed">
                            Compare hotels across top suppliers. Best prices, no booking fees.
                        </p>
                        <div className="mt-8">
                            <SearchForm initialTab="hotels" />
                        </div>
                    </div>
                </section>
            </main>
        );
    }

    const nightCount = (() => {
        try {
            const d1 = new Date(checkIn);
            const d2 = new Date(checkOut);
            return Math.max(1, Math.round((d2.getTime() - d1.getTime()) / 86400000));
        } catch { return 1; }
    })();

    return (
        <main className="bg-cream-100 min-h-screen">
            {/* Hero */}
            <ResultsHero
                eyebrow={`${checkIn} → ${checkOut} · ${nightCount} night${nightCount !== 1 ? "s" : ""} · ${Number(adults) || 2} guest${(Number(adults) || 2) === 1 ? "" : "s"}`}
                title={
                    <>
                        Hotels in{" "}
                        <em className="italic font-normal text-ink-500">{destination}</em>
                    </>
                }
                searchWidget={
                    <SearchForm
                        initialTab="hotels"
                        compact
                        initialValues={{
                            destination,
                            checkIn,
                            checkOut,
                            adults: adults || "2",
                            children: children || "0",
                            rooms: rooms || "1",
                        }}
                    />
                }
            />

            {/* Results */}
            <section className="container py-8 pb-20">
                {loading ? (
                    <HotelResultsSkeleton />
                ) : error ? (
                    <div className="rounded-2xl border border-cream-200/60 bg-white p-10 text-center shadow-card">
                        <SearchX className="mx-auto h-12 w-12 text-ink-200" />
                        <h2 className="mt-4 font-display text-2xl text-ink-900">Search failed</h2>
                        <p className="mt-2 text-ink-500">{error}</p>
                    </div>
                ) : offers.length === 0 ? (
                    <div className="rounded-2xl border border-cream-200/60 bg-white p-10 text-center shadow-card">
                        <Hotel className="mx-auto h-12 w-12 text-ink-200" />
                        <h2 className="mt-4 font-display text-2xl text-ink-900">
                            No hotels found
                        </h2>
                        <p className="mt-2 text-ink-500 max-w-md mx-auto">
                            We couldn&apos;t find any hotels in {destination} for your dates.
                            Try adjusting your search.
                        </p>
                    </div>
                ) : (
                    <div className="grid gap-6 lg:grid-cols-[280px_1fr]">
                        {/* Filters sidebar */}
                        <div className="lg:sticky lg:top-4 lg:self-start">
                            <HotelFiltersPanel
                                offers={offers}
                                filters={filters}
                                onFilterChange={setFilters}
                                resultCount={sorted.length}
                            />
                        </div>

                        {/* Main content */}
                        <div>
                            {/* Sort bar */}
                            <div className="mb-5 flex items-center justify-between rounded-xl border border-cream-200/60 bg-white px-4 py-3 shadow-subtle">
                                <p className="text-sm text-ink-500">
                                    <span className="font-semibold text-ink-900">{sorted.length}</span>{" "}
                                    {sorted.length === 1 ? "property" : "properties"}
                                </p>
                                <div className="flex items-center gap-2">
                                    <ArrowUpDown className="h-4 w-4 text-ink-400" />
                                    <select
                                        value={sort}
                                        onChange={(e) => setSort(e.target.value as SortKey)}
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
                                <div className="rounded-2xl border border-cream-200/60 bg-white p-10 text-center shadow-card">
                                    <SearchX className="mx-auto h-10 w-10 text-ink-200" />
                                    <h3 className="mt-3 font-display text-xl text-ink-900">
                                        No matches
                                    </h3>
                                    <p className="mt-2 text-sm text-ink-500">
                                        Try adjusting your filters to see more results.
                                    </p>
                                </div>
                            ) : (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {sorted.map((offer) => (
                                        <HotelCard
                                            key={offer.supplierOfferRef}
                                            offer={offer}
                                            searchId={searchId}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </section>
        </main>
    );
}
