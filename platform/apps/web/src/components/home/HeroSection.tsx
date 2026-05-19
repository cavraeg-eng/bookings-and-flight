import { SearchForm } from "@/components/search";
import { cn } from "@/lib/cn";
import { ChevronDown } from "lucide-react";

export function HeroSection({ className }: { className?: string }) {
    return (
        <section
            className={cn(
                "relative overflow-hidden bg-ink-900 py-12 sm:py-16 lg:py-20",
                className,
            )}
        >
            {/* Gradient background layers */}
            <div
                className="pointer-events-none absolute inset-0"
                aria-hidden="true"
            >
                {/* Base gradient */}
                <div className="absolute inset-0 bg-gradient-to-b from-ink-900 via-ink-800 to-ink-900" />
                {/* Amber glow — animated pulse */}
                <div className="absolute left-1/2 top-0 -translate-x-1/2 h-[600px] w-[900px] rounded-full bg-amber-500/[0.07] blur-[120px] animate-[glow-pulse_5s_ease-in-out_infinite]" />
                {/* Sky accent glow — animated pulse (offset) */}
                <div className="absolute -left-40 bottom-0 h-[400px] w-[500px] rounded-full bg-sky-500/[0.05] blur-[100px] animate-[glow-pulse_6s_ease-in-out_infinite_1s]" />
                {/* Dots pattern overlay */}
                <div
                    className="absolute inset-0 opacity-[0.03]"
                    style={{
                        backgroundImage:
                            "radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px)",
                        backgroundSize: "32px 32px",
                    }}
                />
            </div>

            {/* Content */}
            <div className="container relative z-10">
                <div className="mx-auto max-w-4xl text-center">
                    {/* Headline */}
                    <h1 className="font-display text-display-xl font-bold text-cream-50 animate-fade-up">
                        Find Your Perfect Flight
                    </h1>

                    {/* Subtitle */}
                    <p className="mx-auto mt-4 max-w-xl text-body-lg text-ink-300 animate-fade-up [animation-delay:100ms]">
                        Compare prices across 500+ airlines and travel sites
                    </p>

                    {/* Search form */}
                    <div className="mt-8 sm:mt-10 animate-fade-up [animation-delay:200ms]">
                        <SearchForm />
                    </div>
                </div>
            </div>

            {/* Scroll to explore indicator */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-1 animate-fade-up [animation-delay:600ms]">
                <span className="text-[0.6rem] font-semibold uppercase tracking-[0.2em] text-cream-200/50">
                    Scroll to explore
                </span>
                <ChevronDown className="h-4 w-4 text-cream-200/40 animate-bounce" />
            </div>

            {/* Glow pulse keyframes */}
            <style>{`
                @keyframes glow-pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
            `}</style>
        </section>
    );
}
