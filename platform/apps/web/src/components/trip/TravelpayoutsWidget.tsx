"use client";

import { useEffect, useRef } from "react";
import { cn } from "@/lib/cn";

export function TravelpayoutsWidget({
    scriptSrc,
    minHeight,
    className,
}: {
    scriptSrc: string;
    minHeight: number;
    className?: string;
}) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const container = containerRef.current;
        if (!container) return;

        container.innerHTML = "";

        const script = document.createElement("script");
        script.async = true;
        script.src = scriptSrc;
        script.charset = "utf-8";
        container.appendChild(script);

        return () => {
            container.innerHTML = "";
        };
    }, [scriptSrc]);

    return (
        <div
            ref={containerRef}
            className={cn("w-full overflow-x-auto", className)}
            style={{ minHeight }}
        />
    );
}