import { cn } from "@/lib/cn";
import type { ReactNode, ElementType } from "react";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

type Variant = "default" | "cheapest" | "direct" | "deal" | "bestValue";

export type BadgeProps = {
    variant?: Variant;
    icon?: ElementType;
    children: ReactNode;
    className?: string;
};

/* ------------------------------------------------------------------ */
/*  Style maps                                                         */
/* ------------------------------------------------------------------ */

const variantStyles: Record<Variant, string> = {
    default:
        "bg-ink-100 text-ink-700",
    cheapest:
        "bg-emerald-50 text-emerald-700 border border-emerald-200/60",
    direct:
        "bg-sky-50 text-sky-700 border border-sky-200/60",
    deal:
        "bg-amber-50 text-amber-700 border border-amber-200/60",
    bestValue:
        "bg-gradient-to-r from-amber-400 to-sky-400 text-white border-0",
};

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function Badge({ variant = "default", icon: Icon, children, className }: BadgeProps) {
    return (
        <span
            className={cn(
                "inline-flex items-center gap-1 rounded-pill px-2.5 py-0.5 text-xs font-semibold leading-5 whitespace-nowrap",
                variantStyles[variant],
                className,
            )}
        >
            {Icon && <Icon className="h-3 w-3 shrink-0" aria-hidden="true" />}
            {children}
        </span>
    );
}
