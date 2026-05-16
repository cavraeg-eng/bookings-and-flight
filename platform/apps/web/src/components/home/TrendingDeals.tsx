import Link from "next/link";
import { Card } from "@/components/ui/Card";
import { cn } from "@/lib/cn";
import { ArrowRight, TrendingUp } from "lucide-react";

/** Compute an ISO date string N days from now. */
function inDays(n: number): string {
    const d = new Date();
    d.setDate(d.getDate() + n);
    return d.toISOString().slice(0, 10);
}

type Deal = {
    origin: string;
    originCode: string;
    destination: string;
    destinationCode: string;
    country: string;
    flag: string;
    price: number;
    gradient: string;
};

const DEALS: Deal[] = [
    {
        origin: "New York",
        originCode: "JFK",
        destination: "London",
        destinationCode: "LHR",
        country: "United Kingdom",
        flag: "🇬🇧",
        price: 289,
        gradient: "from-ink-700 via-ink-600 to-sky-700",
    },
    {
        origin: "Los Angeles",
        originCode: "LAX",
        destination: "Tokyo",
        destinationCode: "NRT",
        country: "Japan",
        flag: "🇯🇵",
        price: 419,
        gradient: "from-rose-900 via-ink-800 to-rose-800",
    },
    {
        origin: "Chicago",
        originCode: "ORD",
        destination: "Paris",
        destinationCode: "CDG",
        country: "France",
        flag: "🇫🇷",
        price: 347,
        gradient: "from-sky-800 via-ink-700 to-sky-900",
    },
    {
        origin: "Miami",
        originCode: "MIA",
        destination: "Cancún",
        destinationCode: "CUN",
        country: "Mexico",
        flag: "🇲🇽",
        price: 189,
        gradient: "from-amber-800 via-amber-900 to-ink-800",
    },
    {
        origin: "San Francisco",
        originCode: "SFO",
        destination: "Barcelona",
        destinationCode: "BCN",
        country: "Spain",
        flag: "🇪🇸",
        price: 372,
        gradient: "from-orange-900 via-ink-800 to-amber-900",
    },
    {
        origin: "Seattle",
        originCode: "SEA",
        destination: "Rome",
        destinationCode: "FCO",
        country: "Italy",
        flag: "🇮🇹",
        price: 398,
        gradient: "from-emerald-900 via-ink-800 to-emerald-800",
    },
];

export function TrendingDeals({ className }: { className?: string }) {
    return (
        <section className={cn("py-0", className)}>
            <div className="container">
                {/* Section header */}
                <div className="mb-10 flex items-end justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-3">
                            <TrendingUp className="h-4 w-4 text-amber-500" />
                            <span className="text-xs font-semibold uppercase tracking-widest text-amber-500">
                                Hot Right Now
                            </span>
                        </div>
                        <h2 className="font-display text-display-md font-bold text-ink-900">
                            Trending Deals
                        </h2>
                    </div>
                    <Link
                        href="/flights"
                        className="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-500 transition-colors"
                    >
                        View All
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </div>

                {/* Deals grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {DEALS.map((deal) => (
                        <Link
                            key={`${deal.originCode}-${deal.destinationCode}`}
                            href={`/flights?origin=${deal.originCode}&destination=${deal.destinationCode}&depart=${inDays(14)}&adults=1&cabin=economy`}
                        >
                            <Card hoverable padding="none" className="group transition-all duration-300 ease-out hover:scale-[1.02] hover:shadow-cardHover active:scale-[0.98]">
                                {/* Gradient visual */}
                                <div
                                    className={cn(
                                        "relative h-36 bg-gradient-to-br",
                                        deal.gradient,
                                    )}
                                >
                                    {/* Route overlay */}
                                    <div className="absolute inset-0 flex items-center justify-center gap-3 text-white/80">
                                        <span className="text-lg font-bold tracking-wide">
                                            {deal.originCode}
                                        </span>
                                        <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />
                                        <span className="text-lg font-bold tracking-wide">
                                            {deal.destinationCode}
                                        </span>
                                    </div>
                                    {/* Flag badge */}
                                    <span className="absolute top-3 right-3 text-xl">
                                        {deal.flag}
                                    </span>
                                </div>

                                {/* Info */}
                                <div className="px-5 py-4">
                                    <div className="flex items-start justify-between gap-2">
                                        <div>
                                            <p className="font-semibold text-ink-900">
                                                {deal.origin} → {deal.destination}
                                            </p>
                                            <p className="mt-0.5 text-xs text-ink-400">
                                                {deal.country}
                                            </p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-xs text-ink-400">from</p>
                                            <p className="text-lg font-bold text-amber-600">
                                                ${deal.price}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </Card>
                        </Link>
                    ))}
                </div>

                {/* Mobile "View All" link */}
                <div className="mt-6 text-center sm:hidden">
                    <Link
                        href="/flights"
                        className="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-500 transition-colors"
                    >
                        View All Deals
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>
    );
}
