"use client";

import { useState } from "react";
import { cn } from "@/lib/cn";
import { Send, CheckCircle } from "lucide-react";

export function NewsletterSignup({ className }: { className?: string }) {
    const [email, setEmail] = useState("");
    const [submitted, setSubmitted] = useState(false);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!email) return;
        setSubmitted(true);
    }

    return (
        <section
            className={cn(
                "relative overflow-hidden py-16 sm:py-20",
                className,
            )}
        >
            {/* Gradient background */}
            <div className="absolute inset-0 bg-gradient-to-br from-amber-50 via-cream-100 to-amber-100/50" aria-hidden="true" />
            <div className="absolute inset-0 bg-grain opacity-30" aria-hidden="true" />

            <div className="container relative z-10">
                <div className="mx-auto max-w-2xl text-center">
                    <h2 className="font-display text-display-md font-bold text-ink-900">
                        Get Exclusive Travel Deals
                    </h2>
                    <p className="mt-3 text-body-lg text-ink-500">
                        Join 50,000+ travelers who get our best deals first
                    </p>

                    {submitted ? (
                        <div className="mt-8 flex items-center justify-center gap-2 text-emerald-600 animate-fade-up">
                            <CheckCircle className="h-5 w-5" />
                            <span className="text-base font-semibold">
                                You&apos;re in! Check your inbox for a welcome email.
                            </span>
                        </div>
                    ) : (
                        <form
                            onSubmit={handleSubmit}
                            className="mt-8 flex flex-col sm:flex-row items-center gap-3 max-w-lg mx-auto"
                        >
                            <input
                                type="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="your@email.com"
                                className={cn(
                                    "w-full flex-1 rounded-xl border border-ink-200/60 bg-white px-5 py-3.5",
                                    "text-sm text-ink-900 placeholder:text-ink-300",
                                    "shadow-subtle focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 focus:outline-none",
                                    "transition-all duration-200",
                                )}
                            />
                            <button
                                type="submit"
                                className={cn(
                                    "inline-flex items-center justify-center gap-2 rounded-xl px-7 py-3.5",
                                    "bg-amber-500 hover:bg-amber-400 active:bg-amber-600 active:scale-[0.97]",
                                    "text-ink-900 font-semibold text-sm",
                                    "shadow-card hover:shadow-float transition-all duration-200",
                                    "w-full sm:w-auto",
                                )}
                            >
                                <Send className="h-4 w-4" />
                                Subscribe
                            </button>
                        </form>
                    )}

                    <p className="mt-4 text-xs text-ink-300">
                        No spam, ever. Unsubscribe anytime.
                    </p>
                </div>
            </div>
        </section>
    );
}
