import { forwardRef, type HTMLAttributes, type ReactNode } from "react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

type Variant = "default" | "glass" | "outlined";
type Padding = "none" | "sm" | "md" | "lg";

export type CardProps = HTMLAttributes<HTMLDivElement> & {
    variant?: Variant;
    hoverable?: boolean;
    padding?: Padding;
    children: ReactNode;
};

/* ------------------------------------------------------------------ */
/*  Style maps                                                         */
/* ------------------------------------------------------------------ */

const variantStyles: Record<Variant, string> = {
    default:
        "bg-white border border-cream-200/60 shadow-card",
    glass:
        "bg-white/60 backdrop-blur-md border border-white/30 shadow-glass",
    outlined:
        "bg-transparent border-2 border-ink-200 shadow-none",
};

const paddingStyles: Record<Padding, string> = {
    none: "",
    sm: "p-4",
    md: "p-6",
    lg: "p-8",
};

const hoverStyles =
    "transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-cardHover";

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export const Card = forwardRef<HTMLDivElement, CardProps>(
    ({ variant = "default", hoverable = false, padding = "md", className, children, ...rest }, ref) => {
        return (
            <div
                ref={ref}
                className={cn(
                    "rounded-card overflow-hidden",
                    variantStyles[variant],
                    paddingStyles[padding],
                    hoverable && hoverStyles,
                    className,
                )}
                {...rest}
            >
                {children}
            </div>
        );
    },
);
Card.displayName = "Card";
