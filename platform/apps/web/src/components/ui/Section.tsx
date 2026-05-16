import { cn } from "@/lib/cn";
import type { ReactNode } from "react";

type SectionProps = {
    id?: string;
    eyebrow?: string;
    title?: ReactNode;
    description?: ReactNode;
    align?: "left" | "center";
    children: ReactNode;
    className?: string;
    containerClassName?: string;
    /** Visual density of vertical padding */
    padding?: "sm" | "md" | "lg";
    /** Optional alt background */
    tone?: "default" | "ink" | "alt";
};

const padMap = {
    sm: "py-14 sm:py-16",
    md: "py-20 sm:py-24",
    lg: "py-24 sm:py-32",
};

const toneMap = {
    default: "bg-cream-100 text-ink-900",
    alt: "bg-cream-50 text-ink-900",
    ink: "bg-ink-900 text-cream-100",
};

export function Section({
    id,
    eyebrow,
    title,
    description,
    align = "left",
    children,
    className,
    containerClassName,
    padding = "md",
    tone = "default",
}: SectionProps) {
    return (
        <section id={id} className={cn(padMap[padding], toneMap[tone], className)}>
            <div className={cn("container", containerClassName)}>
                {(eyebrow || title || description) && (
                    <header
                        className={cn(
                            "mb-10 sm:mb-14 max-w-3xl",
                            align === "center" && "mx-auto text-center",
                        )}
                    >
                        {eyebrow ? <p className="eyebrow mb-4">{eyebrow}</p> : null}
                        {title ? (
                            <h2 className="font-display text-display-lg text-balance">{title}</h2>
                        ) : null}
                        {description ? (
                            <p
                                className={cn(
                                    "mt-4 text-body-lg text-pretty",
                                    tone === "ink" ? "text-cream-200" : "text-ink-500",
                                )}
                            >
                                {description}
                            </p>
                        ) : null}
                    </header>
                )}
                {children}
            </div>
        </section>
    );
}
