"use client";

import { Suspense, useState, useEffect, useMemo, useCallback } from "react";
import { useSearchParams } from "next/navigation";
import { Plane, SearchX, SlidersHorizontal, X } from "lucide-react";
import { ResultsHero } from "@/components/ResultsHero";
import { SearchForm } from "@/components/search/SearchForm";
import { FlightResultCard } from "@/components/results/FlightResultCard";
import { FlightFilterSidebar, EMPTY_FILTERS, getTimeBucket, normalizeStops } from "@/components/results/FlightFilterSidebar";
import type { FlightFilters } from "@/components/results/FlightFilterSidebar";
import { FlightResultsSkeleton } from "@/components/results/FlightResultsSkeleton";
import { SortControls } from "@/components/results/SortControls";
import type { SortOption } from "@/components/results/SortControls";
import type { Offer } from "@baf/shared";
import { cn } from "@/lib/cn";
import { openTrackedOffer } from "@/lib/clicks";
import { inDays } from "@/lib/search";
import { formatCabinLabel } from "@/lib/trip";

/* ------------------------------------------------------------------ */
/*  Root page (Suspense boundary for useSearchParams)                  */
/* ------------------------------------------------------------------ */

export default function FlightsPage() {
    return (
        <Suspense>
            <FlightsPageContent />
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
/*  Sorting logic                                                      */
/* ------------------------------------------------------------------ */

function sortOffers(offers: Offer[], sortBy: SortOption): Offer[] {
    const sorted = [...offers];
    switch (sortBy) {
        case "cheapest":
            return sorted.sort((a, b) => a.price.amount - b.price.amount);
        case "fastest": {
            const dur = (o: Offer) => parseDuration((o.metadata?.duration as string) ?? "");
            return sorted.sort((a, b) => dur(a) - dur(b));
        }
        case "earliest": {
            const dep = (o: Offer) => (o.metadata?.departTime as string) ?? "99:99";
            return sorted.sort((a, b) => dep(a).localeCompare(dep(b)));
        }
        case "best":
        default:
            // Best = cheapest nonstop first, then cheapest 1-stop, then rest
            return sorted.sort((a, b) => {
                const stopsA = stopsScore(a);
                const stopsB = stopsScore(b);
                if (stopsA !== stopsB) return stopsA - stopsB;
                return a.price.amount - b.price.amount;
            });
    }
}

function stopsScore(o: Offer): number {
    const t = normalizeStops((o.metadata?.transfers as string) ?? "");
    if (t === "nonstop") return 0;
    if (t === "1stop") return 1;
    return 2;
}

function parseDuration(dur: string): number {
    // Accepts "2h 30m", "2h30m", "150m", "2h", etc.
    const hMatch = dur.match(/(\d+)\s*h/i);
    const mMatch = dur.match(/(\d+)\s*m/i);
    const hours = hMatch ? parseInt(hMatch[1], 10) : 0;
    const mins = mMatch ? parseInt(mMatch[1], 10) : 0;
    return hours * 60 + mins || 9999;
}

/* ------------------------------------------------------------------ */
/*  Filtering logic                                                    */
/* ------------------------------------------------------------------ */

function filterOffers(offers: Offer[], filters: FlightFilters): Offer[] {
    return offers.filter((o) => {
        const meta = o.metadata ?? {};

        // Stops
        if (filters.stops.size > 0) {
            const stops = normalizeStops((meta.transfers as string) ?? "");
            if (!filters.stops.has(stops)) return false;
        }

        // Price
        if (o.price.amount < filters.priceMin) return false;
        if (filters.priceMax !== Infinity && o.price.amount > filters.priceMax) return false;

        // Airlines
        if (filters.airlines.size > 0) {
            const airline = (meta.airline as string) ?? o.title;
            if (!filters.airlines.has(airline)) return false;
        }

        // Departure time
        if (filters.departureTimes.size > 0) {
            const departTime = (meta.departTime as string) ?? "";
            const bucket = getTimeBucket(departTime);
            if (!bucket || !filters.departureTimes.has(bucket)) return false;
        }

        return true;
    });
}

/* ------------------------------------------------------------------ */
/*  Main content                                                       */
/* ------------------------------------------------------------------ */

function FlightsPageContent() {
    const params = useSearchParams();

    const origin = (params.get("origin") ?? "").toUpperCase();
    const destination = (params.get("destination") ?? "").toUpperCase();
    const depart = params.get("depart") ?? "";
    const ret = params.get("return") ?? undefined;
    const cabin = (params.get("cabin") ?? "economy") as "economy" | "premium" | "business" | "first";
    const adults = params.get("adults") ?? "1";
    const children = params.get("children") ?? "0";
    const infants = params.get("infants") ?? "0";

    const flightInitialValues = {
        origin,
        destination,
        ...(depart ? { depart } : {}),
        ...(ret ? { return: ret } : {}),
        cabin,
        adults,
        children,
        infants,
    };

    const hasSearch = !!origin && !!destination && !!depart;

    const [offers, setOffers] = useState<Offer[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [sortBy, setSortBy] = useState<SortOption>("best");
    const [filters, setFilters] = useState<FlightFilters>(EMPTY_FILTERS);
    const [mobileFiltersOpen, setMobileFiltersOpen] = useState(false);

    /* ---- Fetch results ---- */
    useEffect(() => {
        if (!hasSearch) return;

        const controller = new AbortController();
        setLoading(true);
        setError(null);
        setOffers([]);
        setFilters(EMPTY_FILTERS);

        fetch("/api/search/flights", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                origin,
                destination,
                depart,
                ...(ret ? { return: ret } : {}),
                adults: Number(adults),
                children: Number(children),
                infants: Number(infants),
                cabin,
            }),
            signal: controller.signal,
        })
            .then((r) => {
                if (!r.ok) throw new Error(`Search failed (${r.status})`);
                return r.json();
            })
            .then((data) => {
                const results: Offer[] = data.offers ?? [];
                setOffers(results);

                // Initialize price range from results
                if (results.length > 0) {
                    const prices = results.map((o) => o.price.amount);
                    setFilters((prev) => ({
                        ...prev,
                        priceMin: Math.floor(Math.min(...prices)),
                        priceMax: Math.ceil(Math.max(...prices)),
                    }));
                }
            })
            .catch((err) => {
                if (err.name !== "AbortError") {
                    setError(err.message || "Something went wrong");
                }
            })
            .finally(() => setLoading(false));

        return () => controller.abort();
    }, [origin, destination, depart, ret, adults, children, infants, cabin, hasSearch]);

    /* ---- Derived: filtered + sorted offers ---- */
    const processedOffers = useMemo(
        () => sortOffers(filterOffers(offers, filters), sortBy),
        [offers, filters, sortBy],
    );

    /* ---- No search params — show search form only ---- */
    if (!hasSearch) {
        return (
            <main>
                <section className="relative">
                    <div
                        aria-hidden
                        className="absolute inset-0 bg-sky-wash opacity-70 pointer-events-none"
                    />
                    <div className="container relative pt-20 sm:pt-28 pb-16 sm:pb-24">
                        <p className="text-[0.68rem] uppercase tracking-[0.22em] text-ink-400 font-semibold">
                            Search flights
                        </p>
                        <h1 className="mt-2 font-display text-display-lg text-ink-900 text-balance">
                            Find your perfect flight
                        </h1>
                        <p className="mt-4 max-w-xl text-ink-500 leading-relaxed">
                            Compare prices across airlines and booking sites to find the best deals on flights worldwide.
                        </p>
                        <div className="mt-10">
                            <SearchForm
                                initialTab="flights"
                                initialValues={flightInitialValues}
                            />
                        </div>
                    </div>
                </section>
            </main>
        );
    }

    /* ---- Search results layout ---- */
    return (
        <main>
            {/* Hero with compact search form */}
            <ResultsHero
                eyebrow={`${depart}${ret ? ` → ${ret}` : " · one-way"} · ${formatCabinLabel(cabin)}`}
                title={
                    <span className="font-display tracking-tight">
                        {origin}
                        <span className="text-amber-500 mx-3">→</span>
                        {destination}
                    </span>
                }
                searchWidget={
                    <SearchForm
                        initialTab="flights"
                        compact
                        initialValues={flightInitialValues}
                    />
                }
            />

            <section className="container py-8 pb-20">
                {/* Loading */}
                {loading && (
                    <div className="grid gap-6 lg:grid-cols-[280px_1fr]">
                        <div className="hidden lg:block" />
                        <FlightResultsSkeleton />
                    </div>
                )}

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
                            <Plane className="h-8 w-8" />
                        </div>
                        <h2 className="font-display text-2xl text-ink-900">No flights found</h2>
                        <p className="mt-2 text-ink-500 max-w-md">
                            We couldn&apos;t find any flights for this route and date. Try adjusting your
                            search — different dates or nearby airports often reveal more options.
                        </p>
                    </div>
                )}

                {/* Results */}
                {!loading && !error && offers.length > 0 && (
                    <div className="grid gap-6 lg:grid-cols-[280px_1fr]">
                        {/* Mobile filter toggle */}
                        <div className="lg:hidden">
                            <button
                                onClick={() => setMobileFiltersOpen(!mobileFiltersOpen)}
                                className={cn(
                                    "inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all",
                                    "border border-ink-200/60 bg-white text-ink-700 hover:border-ink-300",
                                )}
                            >
                                <SlidersHorizontal className="h-4 w-4" />
                                Filters
                                {filters.stops.size + filters.airlines.size + filters.departureTimes.size > 0 && (
                                    <span className="flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[0.6rem] font-bold text-ink-900">
                                        {filters.stops.size + filters.airlines.size + filters.departureTimes.size}
                                    </span>
                                )}
                            </button>
                        </div>

                        {/* Mobile filter drawer */}
                        {mobileFiltersOpen && (
                            <div className="lg:hidden animate-slideDown">
                                <div className="rounded-2xl border border-ink-200/60 bg-white p-5 shadow-card">
                                    <div className="flex items-center justify-between mb-4">
                                        <span className="text-sm font-semibold text-ink-900">Filters</span>
                                        <button
                                            onClick={() => setMobileFiltersOpen(false)}
                                            className="text-ink-400 hover:text-ink-600"
                                        >
                                            <X className="h-5 w-5" />
                                        </button>
                                    </div>
                                    <FlightFilterSidebar
                                        offers={offers}
                                        filters={filters}
                                        onFilterChange={setFilters}
                                        matchCount={processedOffers.length}
                                    />
                                </div>
                            </div>
                        )}

                        {/* Desktop sidebar */}
                        <div className="hidden lg:block">
                            <div className="sticky top-6 rounded-2xl border border-ink-200/60 bg-white p-5 shadow-subtle">
                                <FlightFilterSidebar
                                    offers={offers}
                                    filters={filters}
                                    onFilterChange={setFilters}
                                    matchCount={processedOffers.length}
                                />
                            </div>
                        </div>

                        {/* Main results area */}
                        <div>
                            <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
                                <SortControls sortBy={sortBy} onSortChange={setSortBy} />
                                <span className="text-sm text-ink-400">
                                    {processedOffers.length} of {offers.length} flights
                                </span>
                            </div>

                            {processedOffers.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-16 text-center animate-fadeIn">
                                    <p className="text-ink-500">
                                        No flights match your filters. Try adjusting or{" "}
                                        <button
                                            onClick={() => {
                                                const prices = offers.map((o) => o.price.amount);
                                                setFilters({
                                                    ...EMPTY_FILTERS,
                                                    priceMin: Math.floor(Math.min(...prices)),
                                                    priceMax: Math.ceil(Math.max(...prices)),
                                                });
                                            }}
                                            className="text-amber-600 font-semibold hover:underline"
                                        >
                                            clearing all filters
                                        </button>
                                        .
                                    </p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {processedOffers.map((offer, i) => (
                                        <FlightResultCard
                                            key={offer.supplierOfferRef}
                                            offer={offer}
                                            onBookClick={handleBookClick}
                                            index={i}
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
