import Link from "next/link";
import { cn } from "@/lib/cn";
import { MapPin } from "lucide-react";

type Destination = {
    city: string;
    country: string;
    code: string;
    tagline: string;
    gradient: string;
};

const DESTINATIONS: Destination[] = [
    {
        city: "Paris",
        country: "France",
        code: "CDG",
        tagline: "City of Lights",
        gradient: "from-sky-600 via-ink-700 to-sky-800",
    },
    {
        city: "Tokyo",
        country: "Japan",
        code: "NRT",
        tagline: "Where Tradition Meets Future",
        gradient: "from-rose-700 via-ink-800 to-rose-900",
    },
    {
        city: "London",
        country: "United Kingdom",
        code: "LHR",
        tagline: "The Royal Capital",
        gradient: "from-ink-600 via-ink-700 to-ink-800",
    },
    {
        city: "Rome",
        country: "Italy",
        code: "FCO",
        tagline: "The Eternal City",
        gradient: "from-amber-700 via-amber-800 to-ink-800",
    },
    {
        city: "Barcelona",
        country: "Spain",
        code: "BCN",
        tagline: "Gaudí's Masterpiece",
        gradient: "from-orange-700 via-red-800 to-ink-800",
    },
    {
        city: "New York",
        country: "United States",
        code: "JFK",
        tagline: "The City That Never Sleeps",
        gradient: "from-sky-800 via-ink-800 to-ink-900",
    },
    {
        city: "Dubai",
        country: "United Arab Emirates",
        code: "DXB",
        tagline: "City of Gold",
        gradient: "from-amber-600 via-amber-700 to-ink-800",
    },
    {
        city: "Cancún",
        country: "Mexico",
        code: "CUN",
        tagline: "Caribbean Paradise",
        gradient: "from-emerald-600 via-teal-700 to-ink-800",
    },
];

export function PopularDestinations({ className }: { className?: string }) {
    return (
        <section className={cn("py-0", className)}>
            <div className="container">
                {/* Section header */}
                <div className="mb-10 text-center">
                    <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-ink-400">
                        Explore the World
                    </p>
                    <h2 className="font-display text-display-md font-bold text-ink-900">
                        Popular Destinations
                    </h2>
                </div>

                {/* Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {DESTINATIONS.map((dest) => (
                        <Link
                            key={dest.code}
                            href={`/flights?destination=${dest.code}`}
                            className="group"
                        >
                            <div
                                className={cn(
                                    "relative h-52 rounded-card overflow-hidden bg-gradient-to-br transition-all duration-300 ease-out",
                                    "hover:scale-[1.02] hover:shadow-cardHover active:scale-[0.98]",
                                    dest.gradient,
                                )}
                            >
                                {/* Subtle pattern overlay */}
                                <div
                                    className="absolute inset-0 opacity-[0.04]"
                                    aria-hidden="true"
                                    style={{
                                        backgroundImage:
                                            "radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px)",
                                        backgroundSize: "24px 24px",
                                    }}
                                />

                                {/* Content overlay */}
                                <div className="absolute inset-0 flex flex-col justify-end p-5 bg-gradient-to-t from-black/40 to-transparent">
                                    <div className="flex items-center gap-1.5 mb-1">
                                        <MapPin className="h-3 w-3 text-amber-300" />
                                        <span className="text-xs font-medium text-amber-200">
                                            {dest.tagline}
                                        </span>
                                    </div>
                                    <h3 className="text-lg font-bold text-white">
                                        {dest.city}
                                    </h3>
                                    <p className="text-xs text-white/70">
                                        {dest.country}
                                    </p>
                                </div>
                            </div>
                        </Link>
                    ))}
                </div>
            </div>
        </section>
    );
}
