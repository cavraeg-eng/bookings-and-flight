import Link from "next/link";
import { Card } from "@/components/ui/Card";
import { cn } from "@/lib/cn";
import { ArrowRight, BookOpen } from "lucide-react";

type Guide = {
    title: string;
    excerpt: string;
    href: string;
};

const GUIDES: Guide[] = [
    {
        title: "Best Time to Book Flights",
        excerpt:
            "Discover the sweet spot for booking domestic and international flights. Our data shows when prices drop the most — and when to avoid booking.",
        href: "/guides/best-time-to-book-flights",
    },
    {
        title: "First Trip to Japan",
        excerpt:
            "Everything you need to know for an unforgettable first visit — from rail passes and pocket Wi-Fi to hidden temples and the best ramen shops.",
        href: "/guides/first-trip-to-japan",
    },
    {
        title: "How to Avoid Hidden Hotel Fees",
        excerpt:
            "Resort fees, parking charges, early check-in costs — learn the tactics seasoned travelers use to dodge surprise charges at hotels worldwide.",
        href: "/guides/how-to-avoid-hidden-hotel-fees",
    },
];

export function GuidesTeaser({ className }: { className?: string }) {
    return (
        <section className={cn("py-0", className)}>
            <div className="container">
                {/* Section header */}
                <div className="mb-10 flex items-end justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-3">
                            <BookOpen className="h-4 w-4 text-amber-500" />
                            <span className="text-xs font-semibold uppercase tracking-widest text-amber-500">
                                Learn & Save
                            </span>
                        </div>
                        <h2 className="font-display text-display-md font-bold text-ink-900">
                            Travel Guides & Tips
                        </h2>
                    </div>
                    <Link
                        href="/guides"
                        className="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-500 transition-colors"
                    >
                        All Guides
                        <ArrowRight className="h-4 w-4" />
                    </Link>
                </div>

                {/* Guide cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {GUIDES.map((guide) => (
                        <Link key={guide.href} href={guide.href}>
                            <Card hoverable padding="md" className="h-full flex flex-col transition-all duration-300 ease-out hover:scale-[1.02] hover:shadow-cardHover active:scale-[0.98]">
                                <h3 className="font-display text-lg font-bold text-ink-900 mb-2">
                                    {guide.title}
                                </h3>
                                <p className="text-sm leading-relaxed text-ink-500 flex-1 line-clamp-3">
                                    {guide.excerpt}
                                </p>
                                <span className="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 group-hover:text-amber-500 transition-colors">
                                    Read More
                                    <ArrowRight className="h-3.5 w-3.5" />
                                </span>
                            </Card>
                        </Link>
                    ))}
                </div>
            </div>
        </section>
    );
}
