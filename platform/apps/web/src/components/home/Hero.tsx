import { SearchForm } from "@/components/search";
import { Sparkles } from "lucide-react";

const TRUST_MARKERS = [
    "Trip.com custom links",
    "Travelpayouts widgets",
    "Live flight search",
    "Live hotel search",
];

export function Hero() {
    return (
        <section className="relative overflow-hidden">
            {/* Background: cream base + subtle sky wash + grain */}
            <div
                aria-hidden
                className="absolute inset-0 bg-cream-100 bg-sky-wash"
            />
            <div
                aria-hidden
                className="absolute inset-0 bg-grain opacity-[0.35] mix-blend-multiply pointer-events-none"
            />
            {/* Editorial horizontal lines */}
            <div
                aria-hidden
                className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-ink-900/10 to-transparent"
            />

            <div className="container relative pt-16 pb-12 sm:pt-20 sm:pb-16 lg:pt-28 lg:pb-24">
                {/* Eyebrow */}
                <p className="eyebrow text-ink-500 animate-fade-up">
                    <span />
                    Live search, no placeholder pricing
                </p>

                {/* Headline */}
                <h1 className="mt-5 font-display text-display-xl text-ink-900 text-balance max-w-[18ch] animate-fade-up" style={{ animationDelay: "80ms" }}>
                    Trip.com search,{" "}
                    <span className="relative inline-block">
                        styled to match.
                        <svg
                            aria-hidden
                            viewBox="0 0 200 12"
                            className="absolute left-0 -bottom-2 w-full h-3 text-amber-500"
                            preserveAspectRatio="none"
                        >
                            <path
                                d="M2 8 C 50 2, 120 2, 198 6"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="3"
                                strokeLinecap="round"
                            />
                        </svg>
                    </span>
                </h1>

                <p
                    className="mt-6 text-body-lg text-ink-500 max-w-2xl text-pretty animate-fade-up"
                    style={{ animationDelay: "160ms" }}
                >
                    Flights and hotels now run through Travelpayouts-tracked Trip.com custom links
                    and white-label widgets. Cars and activities come back only when their live
                    partner integrations are ready.
                </p>

                {/* Search widget */}
                <div
                    className="mt-10 lg:mt-12 animate-fade-up"
                    style={{ animationDelay: "240ms" }}
                >
                    <SearchForm />
                </div>

                {/* Trust row */}
                <div
                    className="mt-12 lg:mt-16 animate-fade-up"
                    style={{ animationDelay: "320ms" }}
                >
                    <p className="text-[0.7rem] uppercase tracking-[0.2em] text-ink-400 mb-4 flex items-center gap-2">
                        <Sparkles className="h-3 w-3" />
                        Live search powered by
                    </p>
                    <div className="flex flex-wrap items-center gap-x-8 gap-y-3">
                        {TRUST_MARKERS.map((name) => (
                            <span
                                key={name}
                                className="text-ink-400 font-display text-lg tracking-tight"
                            >
                                {name}
                            </span>
                        ))}
                    </div>
                </div>
            </div>

            {/* Decorative plane trail */}
            <svg
                aria-hidden
                className="hidden lg:block absolute top-20 right-0 w-[420px] h-[420px] text-amber-500/70"
                viewBox="0 0 400 400"
                fill="none"
            >
                <circle cx="200" cy="200" r="130" stroke="currentColor" strokeWidth="1" strokeDasharray="3 5" opacity="0.35" />
                <circle cx="200" cy="200" r="170" stroke="currentColor" strokeWidth="1" strokeDasharray="3 9" opacity="0.2" />
                <path
                    d="M80 300 Q 200 100 340 120"
                    stroke="currentColor"
                    strokeWidth="1.5"
                    strokeDasharray="4 6"
                    strokeLinecap="round"
                    opacity="0.5"
                />
                <g transform="translate(330 118) rotate(14)">
                    <path d="M0 0 L 24 -6 L 28 -2 L 14 4 L 24 14 L 20 16 L 10 8 L 2 10 Z" fill="currentColor" />
                </g>
            </svg>
        </section>
    );
}
