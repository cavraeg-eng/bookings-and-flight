import type { Metadata } from "next";
import Link from "next/link";
import { ArrowUpRight, Clock3 } from "lucide-react";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "Travel guides",
    description:
        "Original travel guides, destination advice, booking tips, and practical planning content from Bookings and Flights.",
    alternates: { canonical: "/guides" },
};

const GUIDES = [
    {
        href: "/guides/best-time-to-book-flights",
        category: "Flight strategy",
        title: "When to book flights: a practical timing guide for domestic and long-haul trips",
        excerpt:
            "A clear framework for when to book, when to wait, and what usually causes prices to jump.",
        readTime: "6 min read",
    },
    {
        href: "/guides/how-to-avoid-hidden-hotel-fees",
        category: "Hotels",
        title: "How to avoid hidden hotel fees before you click book",
        excerpt:
            "A practical traveler checklist for comparing hotel rates and spotting the real total cost.",
        readTime: "5 min read",
    },
    {
        href: "/guides/first-trip-to-japan",
        category: "Destination guide",
        title: "First trip to Japan: flights, neighborhoods, and common mistakes to avoid",
        excerpt:
            "An original first-timer's guide to airports, neighborhoods, timing, and common booking mistakes.",
        readTime: "7 min read",
    },
];

export default function GuidesPage() {
    return (
        <ContentPage
            eyebrow="Editorial"
            title="Travel guides and booking advice."
            lede="Original travel content matters — for users, for SEO, and for affiliate approvals. This section is the foundation for that."
        >
            <div className="grid gap-5">
                {GUIDES.map((guide) => (
                    <Link
                        key={guide.href}
                        href={guide.href}
                        className="group rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle transition-all duration-300 hover:-translate-y-1 hover:shadow-float no-underline"
                    >
                        <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                            {guide.category}
                        </p>
                        <h2 className="!mt-3 !mb-0 font-display text-2xl text-ink-900 tracking-tight text-balance">
                            {guide.title}
                        </h2>
                        <p className="mt-4 text-ink-500 leading-relaxed">{guide.excerpt}</p>
                        <div className="mt-6 flex items-center justify-between text-sm text-ink-400">
                            <span className="inline-flex items-center gap-2">
                                <Clock3 className="h-4 w-4" />
                                {guide.readTime}
                            </span>
                            <span className="inline-flex items-center gap-2 font-semibold text-ink-900 group-hover:text-amber-600">
                                Read guide
                                <ArrowUpRight className="h-4 w-4" />
                            </span>
                        </div>
                    </Link>
                ))}
            </div>
        </ContentPage>
    );
}