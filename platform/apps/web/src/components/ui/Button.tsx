"use client";

import { forwardRef, type ButtonHTMLAttributes, type AnchorHTMLAttributes, type ReactNode } from "react";
import { cn } from "@/lib/cn";
import { Loader2 } from "lucide-react";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

type Variant = "primary" | "secondary" | "outline" | "ghost" | "accent";
type Size = "sm" | "md" | "lg";

type ButtonBaseProps = {
    variant?: Variant;
    size?: Size;
    loading?: boolean;
    children: ReactNode;
    className?: string;
};

export type ButtonProps = ButtonBaseProps & ButtonHTMLAttributes<HTMLButtonElement>;

type LinkButtonBaseProps = ButtonBaseProps &
    AnchorHTMLAttributes<HTMLAnchorElement> & { href: string };

/* ------------------------------------------------------------------ */
/*  Style maps                                                         */
/* ------------------------------------------------------------------ */

const base =
    "inline-flex items-center justify-center font-sans font-semibold rounded-lg " +
    "transition-all duration-200 ease-out select-none " +
    "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-cream-100 " +
    "disabled:pointer-events-none disabled:opacity-50 " +
    "active:scale-[0.97]";

const variantStyles: Record<Variant, string> = {
    primary:
        "bg-amber-500 text-ink-900 hover:bg-amber-400 hover:shadow-cardHover hover:scale-[1.02]",
    secondary:
        "bg-ink-900 text-cream-100 hover:bg-ink-800 hover:shadow-cardHover hover:scale-[1.02]",
    outline:
        "border-2 border-ink-200 text-ink-900 bg-transparent hover:border-ink-400 hover:bg-ink-50 hover:scale-[1.02]",
    ghost:
        "text-ink-700 bg-transparent hover:bg-ink-50 hover:text-ink-900",
    // Legacy compat — maps to primary behavior
    accent:
        "bg-amber-500 text-ink-900 hover:bg-amber-400 hover:shadow-cardHover hover:scale-[1.02]",
};

const sizeStyles: Record<Size, string> = {
    sm: "h-8 px-3 text-sm gap-1.5",
    md: "h-10 px-5 text-sm gap-2",
    lg: "h-12 px-7 text-base gap-2.5",
};

/* Legacy class-name maps (kept for backward-compat with globals.css) */
const legacyVariantClass: Record<string, string> = {
    primary: "btn-primary",
    accent: "btn-accent",
    ghost: "btn-ghost",
};
const legacySizeClass: Record<string, string> = {
    sm: "btn-sm",
    md: "btn-md",
    lg: "btn-lg",
};

/* ------------------------------------------------------------------ */
/*  Button                                                             */
/* ------------------------------------------------------------------ */

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
    ({ variant = "primary", size = "md", loading = false, className, children, disabled, ...rest }, ref) => {
        return (
            <button
                ref={ref}
                disabled={disabled || loading}
                aria-busy={loading || undefined}
                className={cn(base, variantStyles[variant], sizeStyles[size], className)}
                {...rest}
            >
                {loading && <Loader2 className="h-4 w-4 animate-spin" aria-hidden="true" />}
                {children}
            </button>
        );
    },
);
Button.displayName = "Button";

/* ------------------------------------------------------------------ */
/*  LinkButton (backward-compatible)                                   */
/* ------------------------------------------------------------------ */

export function LinkButton({
    variant = "primary",
    size = "md",
    className,
    children,
    href,
    ...rest
}: LinkButtonBaseProps) {
    return (
        <a
            href={href}
            className={cn(
                legacyVariantClass[variant] ?? variantStyles[variant],
                legacySizeClass[size] ?? sizeStyles[size],
                className,
            )}
            {...rest}
        >
            {children}
        </a>
    );
}
