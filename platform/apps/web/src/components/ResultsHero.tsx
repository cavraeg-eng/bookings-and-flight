"use client";

import { useRouter } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import type { ReactNode } from "react";

interface ResultsHeroProps {
    eyebrow: string;
    title: ReactNode;
    searchWidget: ReactNode;
}

export function ResultsHero({ eyebrow, title, searchWidget }: ResultsHeroProps) {
    const router = useRouter();
    return (
        <section className="relative border-b border-ink-900/10">
            <div
                aria-hidden
                className="absolute inset-0 bg-sky-wash opacity-70 pointer-events-none"
            />
            <div className="container relative pt-12 sm:pt-16 pb-8">
                <button
                    onClick={() => router.push("/")}
                    className="inline-flex items-center gap-2 text-sm font-medium text-ink-500 hover:text-ink-900 mb-6 group"
                >
                    <ArrowLeft className="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                    New search
                </button>
                <p className="text-[0.68rem] uppercase tracking-[0.22em] text-ink-400 font-semibold">
                    {eyebrow}
                </p>
                <h1 className="mt-2 font-display text-display-lg text-ink-900 text-balance">
                    {title}
                </h1>
                <div className="mt-8">{searchWidget}</div>
            </div>
        </section>
    );
}
