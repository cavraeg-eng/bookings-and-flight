"use client";

import { forwardRef, useId, type InputHTMLAttributes, type ElementType } from "react";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type InputProps = Omit<InputHTMLAttributes<HTMLInputElement>, "size"> & {
    label?: string;
    error?: string;
    icon?: ElementType;
    /** Visual size of the input */
    inputSize?: "sm" | "md" | "lg";
    className?: string;
    wrapperClassName?: string;
};

/* ------------------------------------------------------------------ */
/*  Style helpers                                                      */
/* ------------------------------------------------------------------ */

const sizeStyles = {
    sm: "h-9 text-sm",
    md: "h-11 text-sm",
    lg: "h-13 text-base",
} as const;

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export const Input = forwardRef<HTMLInputElement, InputProps>(
    (
        {
            label,
            error,
            icon: Icon,
            inputSize = "md",
            className,
            wrapperClassName,
            id: externalId,
            disabled,
            ...rest
        },
        ref,
    ) => {
        const autoId = useId();
        const id = externalId ?? autoId;
        const errorId = error ? `${id}-error` : undefined;

        return (
            <div className={cn("flex flex-col gap-1.5", wrapperClassName)}>
                {label && (
                    <label
                        htmlFor={id}
                        className="text-sm font-medium text-ink-700"
                    >
                        {label}
                    </label>
                )}

                <div className="relative">
                    {Icon && (
                        <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-400">
                            <Icon className="h-4 w-4" aria-hidden="true" />
                        </span>
                    )}

                    <input
                        ref={ref}
                        id={id}
                        disabled={disabled}
                        aria-invalid={error ? true : undefined}
                        aria-describedby={errorId}
                        className={cn(
                            "w-full rounded-search border bg-white px-3 font-sans",
                            "transition-all duration-200 ease-out",
                            "placeholder:text-ink-300",
                            "focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400",
                            "disabled:cursor-not-allowed disabled:opacity-50 disabled:bg-cream-50",
                            error
                                ? "border-red-400 focus:ring-red-400 focus:border-red-400"
                                : "border-ink-200 hover:border-ink-300",
                            Icon && "pl-9",
                            sizeStyles[inputSize],
                            className,
                        )}
                        {...rest}
                    />
                </div>

                {error && (
                    <p id={errorId} className="text-xs text-red-500 font-medium" role="alert">
                        {error}
                    </p>
                )}
            </div>
        );
    },
);
Input.displayName = "Input";
