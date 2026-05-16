"use client";

import { cn } from "@/lib/cn";
import { Star } from "lucide-react";
import { useEffect, useRef, useState } from "react";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type SocialProofProps = {
    className?: string;
    /** Show testimonial cards below the stats (default: true) */
    showTestimonials?: boolean;
};

/* ------------------------------------------------------------------ */
/*  Stats data                                                         */
/* ------------------------------------------------------------------ */

type Stat = {
    value: number;
    suffix: string;
    prefix?: string;
    label: string;
};

const STATS: Stat[] = [
    { value: 2, suffix: "M+", label: "Travelers trust us" },
    { value: 500, suffix: "+", label: "Travel partners" },
    { value: 150, suffix: "+", label: "Countries covered" },
    { value: 4.8, suffix: "/5", label: "Average rating" },
];

/* ------------------------------------------------------------------ */
/*  Testimonials data                                                  */
/* ------------------------------------------------------------------ */

type Testimonial = {
    quote: string;
    name: string;
    trip: string;
    rating: number;
};

const TESTIMONIALS: Testimonial[] = [
    {
        quote: "Saved over $400 on flights to Tokyo. The price comparison tool showed me options I never would have found on my own.",
        name: "Sarah M.",
        trip: "New York → Tokyo",
        rating: 5,
    },
    {
        quote: "Booked a last-minute hotel in Barcelona for half the price I found on other sites. Will definitely use this for every trip now.",
        name: "James K.",
        trip: "London → Barcelona",
        rating: 5,
    },
    {
        quote: "The interface is incredibly clean and fast. Found connecting flights through Dubai that cut my travel cost by 30%.",
        name: "Priya R.",
        trip: "Mumbai → Paris",
        rating: 5,
    },
    {
        quote: "Planning a family trip used to be stressful. This site compared every option in seconds — we saved $600 on our Caribbean cruise package.",
        name: "Michael T.",
        trip: "Atlanta → Cancún",
        rating: 5,
    },
];

/* ------------------------------------------------------------------ */
/*  Animated counter hook                                              */
/* ------------------------------------------------------------------ */

function useCountUp(target: number, duration = 1800, active = false) {
    const [current, setCurrent] = useState(0);

    useEffect(() => {
        if (!active) return;

        const isDecimal = target % 1 !== 0;
        const steps = 40;
        const increment = target / steps;
        let frame = 0;

        const interval = setInterval(() => {
            frame++;
            if (frame >= steps) {
                setCurrent(target);
                clearInterval(interval);
            } else {
                const progress = frame / steps;
                // Ease-out quad
                const eased = 1 - (1 - progress) * (1 - progress);
                const val = eased * target;
                setCurrent(isDecimal ? Math.round(val * 10) / 10 : Math.round(val));
            }
        }, duration / steps);

        return () => clearInterval(interval);
    }, [target, duration, active]);

    return current;
}

/* ------------------------------------------------------------------ */
/*  Stat card                                                          */
/* ------------------------------------------------------------------ */

function StatItem({ stat, active }: { stat: Stat; active: boolean }) {
    const count = useCountUp(stat.value, 1600, active);

    return (
        <div className="flex flex-col items-center gap-1 px-4 py-4">
            <span className="font-display text-display-md font-bold text-ink-900">
                {stat.prefix}
                {stat.value % 1 !== 0 ? count.toFixed(1) : count}
                {stat.suffix}
            </span>
            <span className="text-xs font-medium uppercase tracking-wider text-ink-400">
                {stat.label}
            </span>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Stars                                                              */
/* ------------------------------------------------------------------ */

function StarRating({ count }: { count: number }) {
    return (
        <div className="flex gap-0.5" aria-label={`${count} out of 5 stars`}>
            {Array.from({ length: 5 }).map((_, i) => (
                <Star
                    key={i}
                    className={cn(
                        "h-3.5 w-3.5",
                        i < count ? "fill-amber-400 text-amber-400" : "fill-ink-100 text-ink-200",
                    )}
                    aria-hidden="true"
                />
            ))}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function SocialProof({ className, showTestimonials = true }: SocialProofProps) {
    const sectionRef = useRef<HTMLElement>(null);
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const el = sectionRef.current;
        if (!el) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.2 },
        );

        observer.observe(el);
        return () => observer.disconnect();
    }, []);

    return (
        <section
            ref={sectionRef}
            className={cn("py-0", className)}
            aria-label="Social proof and testimonials"
        >
            {/* Stats row */}
            <div className="mx-auto grid max-w-3xl grid-cols-2 gap-2 sm:grid-cols-4">
                {STATS.map((stat) => (
                    <StatItem key={stat.label} stat={stat} active={isVisible} />
                ))}
            </div>

            {/* Testimonials */}
            {showTestimonials && (
                <div className="mx-auto mt-10 grid max-w-5xl gap-5 px-4 grid-cols-1 sm:grid-cols-2">
                    {TESTIMONIALS.map((t) => (
                        <figure
                            key={t.name}
                            className={cn(
                                "flex flex-col justify-between rounded-card border border-cream-200/60 bg-white p-6 shadow-subtle",
                                "transition-all duration-300 hover:-translate-y-0.5 hover:shadow-card",
                                isVisible && "animate-fade-up",
                            )}
                        >
                            <blockquote className="text-sm leading-relaxed text-ink-600">
                                &ldquo;{t.quote}&rdquo;
                            </blockquote>

                            <figcaption className="mt-5 flex items-center justify-between border-t border-cream-200/60 pt-4">
                                <div>
                                    <p className="text-sm font-semibold text-ink-800">{t.name}</p>
                                    <p className="text-xs text-ink-400">{t.trip}</p>
                                </div>
                                <StarRating count={t.rating} />
                            </figcaption>
                        </figure>
                    ))}
                </div>
            )}
        </section>
    );
}
