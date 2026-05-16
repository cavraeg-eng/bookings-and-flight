"use client";

import { Suspense, useState, useEffect, useMemo } from "react";
import { useSearchParams } from "next/navigation";
import { Car, SearchX, ArrowUpDown } from "lucide-react";
import { ResultsHero } from "@/components/ResultsHero";
import { SearchForm } from "@/components/search/SearchForm";
import { CarCard } from "@/components/results/CarCard";
import { Skeleton } from "@/components/ui/Skeleton";
import { cn } from "@/lib/cn";
import { inDays } from "@/lib/search";
import type { Offer, SearchResponse } from "@baf/shared";

/* ------------------------------------------------------------------ */
/*  Root page                                                          */
/* ------------------------------------------------------------------ */

export default function CarsPage() {
    return (
        <Suspense>
            <CarsPageContent />
        </Suspense>
    );
}

/* ------------------------------------------------------------------ */
/*  Click-through handler                                              */
/* ------------------------------------------------------------------ */

async function handleBookClick(offer: Offer) {
    try {
        const res = await fetch("/api/clicks", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                supplier: offer.supplier,
                vertical: offer.vertical,
                offerRef: offer.supplierOfferRef,
                deeplink: offer.deeplink,
                price: offer.price.amount,
                currency: offer.price.currency,
            }),
        });
        if (res.ok) {
            const { redirectUrl } = await res.json();
            window.open(redirectUrl, "_blank", "noopener,noreferrer");
        } else {
            window.open(offer.deeplink, "_blank", "noopener,noreferrer");
        }
    } catch {
        window.open(offer.deeplink, "_blank", "noopener,noreferrer");
    }
}

/* ------------------------------------------------------------------ */
/*  Sort                                                               */
/* ------------------------------------------------------------------ */

type CarSortKey = "price-asc" | "price-desc" | "category";

const SORT_OPTIONS: { key: CarSortKey; label: string }[] = [
    { key: "price-asc", label: "Price: Low → High" },
    { key: "price-desc", label: "Price: High → Low" },
    { key: "category", label: "Category" },
];

function sortOffers(offers: Offer[], sort: CarSortKey): Offer[] {
    const sorted = [...offers];
    switch (sort) {
        case "price-asc":
            return sorted.sort((a, b) => a.price.amount - b.price.amount);
        case "price-desc":
            return sorted.sort((a, b) => b.price.amount - a.price.amount);
        case "category":
            return sorted.sort((a, b) => a.title.localeCompare(b.title));
        default:
            return sorted;
    }
}

/* ------------------------------------------------------------------ */
/*  Loading skeleton                                                   */
/* ------------------------------------------------------------------ */

function CarResultsSkeleton() {
    return (
        <div className="space-y-4" role="status" aria-label="Loading car results">
            {Array.from({ length: 4 }, (_, i) => (
                <div key={i} className="flex flex-col sm:flex-row overflow-hidden rounded-2xl border border-cream-200/60 bg-white shadow-subtle">
                    <div className="sm:w-48 md:w-56 shrink-0">
                        <Skeleton variant="custom" width="100%" height="140px" className="rounded-none" />
                    </div>
                    <div className="flex-1 p-5 space-y-3">
                        <Skeleton variant="text" className="w-2/3" />
                        <div className="flex gap-2">
                            <Skeleton variant="custom" width="80px" height="28px" className="rounded-full" />
                            <Skeleton variant="custom" width="80px" height="28px" className="rounded-full" />
                            <Skeleton variant="custom" width="80px" height="28px" className="rounded-full" />
                        </div>
                        <Skeleton variant="text" className="w-1/3" />
                        <div className="flex justify-between items-end pt-3 border-t border-cream-200/60">
                            <Skeleton variant="custom" width="100px" height="32px" />
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

function CarsPageContent() {
    const params = useSearchParams();

    const pickupLocation = params.get("pickupLocation") ?? "";
    const dropoffLocation = params.get("dropoffLocation") ?? undefined;
    const pickupDate = params.get("pickupDate") ?? "";
    const dropoffDate = params.get("dropoffDate") ?? "";
    const pickupTime = params.get("pickupTime") ?? "10:00";
    const dropoffTime = params.get("dropoffTime") ?? "10:00";

    const hasSearch = Boolean(pickupLocation && pickupDate && dropoffDate);

    /* ---- State ---- */
    const [offers, setOffers] = useState<Offer[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [sort, setSort] = useState<CarSortKey>("price-asc");

    /* ---- Fetch ---- */
    useEffect(() => {
        if (!hasSearch) return;

        const ctrl = new AbortController();
        setLoading(true);
        setError(null);
        setOffers([]);

        (async () => {
            try {
                const res = await fetch("/api/search/cars", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        pickupLocation,
                        ...(dropoffLocation ? { dropoffLocation } : {}),
                        pickupDate,
                        dropoffDate,
                        pickupTime,
                        dropoffTime,
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
    }, [pickupLocation, dropoffLocation, pickupDate, dropoffDate, pickupTime, dropoffTime, hasSearch]);

    /* ---- Derived ---- */
    const sorted = useMemo(() => sortOffers(offers, sort), [offers, sort]);

    /* ---- Rental days ---- */
    const dayCount = (() => {
        try {
            const d1 = new Date(pickupDate);
            const d2 = new Date(dropoffDate);
            return Math.max(1, Math.round((d2.getTime() - d1.getTime()) / 86400000));
        } catch {
            return 1;
        }
    })();

    /* ---- No search params → landing ---- */
    if (!hasSearch) {
        return (
            <main>
                <section className="relative">
                    <div aria-hidden className="absolute inset-0 bg-sky-wash opacity-70 pointer-events-none" />
                    <div className="container relative pt-20 sm:pt-28 pb-16 sm:pb-24">
                        <p className="text-[0.68rem] uppercase tracking-[0.22em] text-ink-400 font-semibold">
                            Car rentals
                        </p>
                        <h1 className="mt-2 font-display text-display-lg text-ink-900 text-balance">
                            Find the best car rental deals.
                        </h1>
                        <p className="mt-4 max-w-xl text-ink-500 leading-relaxed">
                            Compare car rental prices from top suppliers worldwide. Pick up at airports, train stations, or city locations.
                        </p>
                        <div className="mt-10">
                            <SearchForm initialTab="cars" />
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
                eyebrow={`${pickupDate} ${pickupTime} → ${dropoffDate} ${dropoffTime} · ${dayCount} day${dayCount !== 1 ? "s" : ""}`}
                title={
                    <>
                        Car rentals{" "}
                        <span className="text-amber-500">·</span>{" "}
                        <em className="italic font-normal text-ink-500 not-italic">{pickupLocation}</em>
                    </>
                }
                searchWidget={
                    <SearchForm
                        initialTab="cars"
                        compact
                        initialValues={{
                            pickupLocation,
                            ...(dropoffLocation ? { dropoffLocation } : {}),
                            pickupDate,
                            dropoffDate,
                            pickupTime,
                            dropoffTime,
                        }}
                    />
                }
            />

            <section className="container py-8 pb-20">
                {/* Loading */}
                {loading && <CarResultsSkeleton />}

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
                            <Car className="h-8 w-8" />
                        </div>
                        <h2 className="font-display text-2xl text-ink-900">No cars found</h2>
                        <p className="mt-2 text-ink-500 max-w-md">
                            We couldn&apos;t find any car rentals for this location and dates. Try adjusting your search — different dates or nearby locations often reveal more options.
                        </p>
                    </div>
                )}

                {/* Results */}
                {!loading && !error && offers.length > 0 && (
                    <div>
                        {/* Sort bar */}
                        <div className="mb-5 flex items-center justify-between rounded-xl border border-cream-200/60 bg-white px-4 py-3 shadow-subtle">
                            <p className="text-sm text-ink-500">
                                <span className="font-semibold text-ink-900">{sorted.length}</span>{" "}
                                {sorted.length === 1 ? "car" : "cars"} available
                            </p>
                            <div className="flex items-center gap-2">
                                <ArrowUpDown className="h-4 w-4 text-ink-400" />
                                <select
                                    value={sort}
                                    onChange={(e) => setSort(e.target.value as CarSortKey)}
                                    className="border-0 bg-transparent p-0 pr-6 text-sm font-semibold text-ink-900 focus:ring-0 focus:outline-none cursor-pointer"
                                >
                                    {SORT_OPTIONS.map((o) => (
                                        <option key={o.key} value={o.key}>{o.label}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Cards */}
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                            {sorted.map((offer, i) => (
                                <CarCard
                                    key={offer.supplierOfferRef}
                                    offer={offer}
                                    onBookClick={handleBookClick}
                                    index={i}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </section>
        </main>
    );
}
