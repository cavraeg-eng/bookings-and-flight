import { cn } from "@/lib/cn";
import { Search, Shield, BadgeCheck, Headphones } from "lucide-react";
import type { ElementType } from "react";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type TrustBadgesProps = {
    className?: string;
    /** "full" shows icon + title + description; "compact" shows icon + title only */
    variant?: "full" | "compact";
};

type TrustItem = {
    icon: ElementType;
    title: string;
    description: string;
};

/* ------------------------------------------------------------------ */
/*  Data                                                               */
/* ------------------------------------------------------------------ */

const TRUST_ITEMS: TrustItem[] = [
    {
        icon: Search,
        title: "Price Comparison",
        description: "Compare prices across 500+ suppliers",
    },
    {
        icon: Shield,
        title: "Secure Booking",
        description: "Your data is protected",
    },
    {
        icon: BadgeCheck,
        title: "Best Price Guarantee",
        description: "We find the lowest fares",
    },
    {
        icon: Headphones,
        title: "24/7 Support",
        description: "Help when you need it",
    },
];

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function TrustBadges({ className, variant = "full" }: TrustBadgesProps) {
    const isCompact = variant === "compact";

    return (
        <section
            className={cn(
                "grid gap-3",
                isCompact
                    ? "grid-cols-2 sm:grid-cols-4"
                    : "grid-cols-2 md:grid-cols-4",
                className,
            )}
            aria-label="Trust and security guarantees"
        >
            {TRUST_ITEMS.map((item) => {
                const Icon = item.icon;
                return (
                    <div
                        key={item.title}
                        className={cn(
                            "flex items-start gap-3 rounded-card border border-cream-200/60 bg-ink-50 transition-colors duration-200",
                            isCompact ? "px-3 py-2" : "px-4 py-3.5",
                        )}
                    >
                        <div
                            className={cn(
                                "flex shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500",
                                isCompact ? "h-8 w-8" : "h-10 w-10",
                            )}
                            aria-hidden="true"
                        >
                            <Icon className={isCompact ? "h-4 w-4" : "h-5 w-5"} />
                        </div>

                        <div className="min-w-0">
                            <p
                                className={cn(
                                    "font-semibold text-ink-800",
                                    isCompact ? "text-xs" : "text-sm",
                                )}
                            >
                                {item.title}
                            </p>
                            {!isCompact && (
                                <p className="mt-0.5 text-xs leading-relaxed text-ink-400">
                                    {item.description}
                                </p>
                            )}
                        </div>
                    </div>
                );
            })}
        </section>
    );
}
