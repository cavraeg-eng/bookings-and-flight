import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

type Variant = "text" | "card" | "circle" | "custom";

export type SkeletonProps = {
    variant?: Variant;
    className?: string;
    /** Width override — only used with "custom" variant */
    width?: string | number;
    /** Height override — only used with "custom" variant */
    height?: string | number;
    /** Number of text lines to render (only for "text" variant) */
    lines?: number;
};

/* ------------------------------------------------------------------ */
/*  Style maps                                                         */
/* ------------------------------------------------------------------ */

const shimmerBase =
    "animate-pulse rounded bg-gradient-to-r from-cream-200 via-ink-50 to-cream-200 bg-[length:200%_100%]";

const variantStyles: Record<Variant, string> = {
    text: "h-4 w-full rounded",
    card: "h-40 w-full rounded-card",
    circle: "h-10 w-10 rounded-full",
    custom: "",
};

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function Skeleton({
    variant = "text",
    className,
    width,
    height,
    lines = 1,
}: SkeletonProps) {
    if (variant === "text" && lines > 1) {
        return (
            <div className={cn("flex flex-col gap-2", className)} role="status" aria-label="Loading">
                {Array.from({ length: lines }).map((_, i) => (
                    <div
                        key={i}
                        className={cn(
                            shimmerBase,
                            variantStyles.text,
                            i === lines - 1 && "w-3/4",
                        )}
                    />
                ))}
                <span className="sr-only">Loading…</span>
            </div>
        );
    }

    const style =
        variant === "custom"
            ? {
                  width: typeof width === "number" ? `${width}px` : width,
                  height: typeof height === "number" ? `${height}px` : height,
              }
            : undefined;

    return (
        <div
            role="status"
            aria-label="Loading"
            className={cn(shimmerBase, variantStyles[variant], className)}
            style={style}
        >
            <span className="sr-only">Loading…</span>
        </div>
    );
}
