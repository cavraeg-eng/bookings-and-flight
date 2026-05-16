import { ReactNode } from "react";

interface ContentPageProps {
    eyebrow?: string;
    title: string;
    lede?: string;
    updated?: string;
    children: ReactNode;
}

export function ContentPage({ eyebrow, title, lede, updated, children }: ContentPageProps) {
    return (
        <main className="bg-cream-100">
            {/* Header block */}
            <section className="bg-gradient-to-b from-sky-50 to-cream-100 border-b border-ink-900/5">
                <div className="container py-20 lg:py-28 max-w-3xl">
                    {eyebrow && (
                        <p className="eyebrow mb-5">
                            <span />
                            {eyebrow}
                        </p>
                    )}
                    <h1 className="font-display text-display-lg text-ink-900 tracking-tight text-balance">
                        {title}
                    </h1>
                    {lede && (
                        <p className="mt-6 text-xl text-ink-500 leading-relaxed max-w-2xl text-balance">
                            {lede}
                        </p>
                    )}
                    {updated && (
                        <p className="mt-8 text-sm font-mono uppercase tracking-[0.12em] text-ink-400">
                            Last updated · {updated}
                        </p>
                    )}
                </div>
            </section>

            {/* Body */}
            <section className="container py-16 lg:py-24 max-w-3xl">
                <div className="prose-content">{children}</div>
            </section>
        </main>
    );
}
