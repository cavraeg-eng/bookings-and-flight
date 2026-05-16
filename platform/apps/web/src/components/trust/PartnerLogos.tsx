import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type PartnerLogosProps = {
    className?: string;
    /** Show fewer logos for compact placements */
    compact?: boolean;
};

/* ------------------------------------------------------------------ */
/*  Partner data                                                       */
/* ------------------------------------------------------------------ */

type Partner = { name: string; category: "airline" | "hotel" | "platform" };

const ALL_PARTNERS: Partner[] = [
    { name: "American Airlines", category: "airline" },
    { name: "Delta", category: "airline" },
    { name: "United", category: "airline" },
    { name: "Emirates", category: "airline" },
    { name: "British Airways", category: "airline" },
    { name: "Lufthansa", category: "airline" },
    { name: "Air France", category: "airline" },
    { name: "Qatar Airways", category: "airline" },
    { name: "Marriott", category: "hotel" },
    { name: "Hilton", category: "hotel" },
    { name: "Hyatt", category: "hotel" },
    { name: "IHG", category: "hotel" },
    { name: "Accor", category: "hotel" },
    { name: "Trip.com", category: "platform" },
    { name: "Booking.com", category: "platform" },
    { name: "Viator", category: "platform" },
    { name: "Expedia", category: "platform" },
];

const COMPACT_PARTNERS: Partner[] = ALL_PARTNERS.filter(
    (_, i) => i % 2 === 0,
);

/* ------------------------------------------------------------------ */
/*  Marquee row (duplicated for seamless loop)                         */
/* ------------------------------------------------------------------ */

function LogoTrack({ partners }: { partners: Partner[] }) {
    return (
        <div className="flex items-center gap-10 whitespace-nowrap">
            {partners.map((p) => (
                <span
                    key={p.name}
                    className={cn(
                        "inline-block select-none tracking-[0.05em] uppercase",
                        "text-ink-400 opacity-40 transition-opacity duration-300 hover:opacity-70",
                        p.category === "airline" && "text-sm font-bold",
                        p.category === "hotel" && "text-xs font-extrabold tracking-[0.08em]",
                        p.category === "platform" && "text-[0.8rem] font-black",
                    )}
                >
                    {p.name}
                </span>
            ))}
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function PartnerLogos({ className, compact = false }: PartnerLogosProps) {
    const partners = compact ? COMPACT_PARTNERS : ALL_PARTNERS;

    return (
        <section
            className={cn("w-full overflow-hidden py-5", className)}
            aria-label="Trusted travel partners"
        >
            {/* Heading */}
            <p className="mb-3 text-center text-xs font-semibold uppercase tracking-widest text-ink-300">
                Trusted by 500+ Travel Partners
            </p>

            {/* Marquee wrapper — pauses on hover */}
            <div className="group relative">
                {/* Fade edges */}
                <div className="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-cream-100 to-transparent" />
                <div className="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-cream-100 to-transparent" />

                <div
                    className={cn(
                        "flex w-max animate-[marquee_40s_linear_infinite] gap-10",
                        "group-hover:[animation-play-state:paused]",
                    )}
                >
                    {/* Two copies for seamless loop */}
                    <LogoTrack partners={partners} />
                    <LogoTrack partners={partners} />
                </div>
            </div>

            {/* Inline keyframes — keeps this component self-contained */}
            <style>{`
                @keyframes marquee {
                    0%   { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                @media (prefers-reduced-motion: reduce) {
                    .animate-\\[marquee_40s_linear_infinite\\] {
                        animation: none !important;
                    }
                }
            `}</style>
        </section>
    );
}
