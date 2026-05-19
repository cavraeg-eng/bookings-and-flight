import Link from "next/link";
import { ArrowUpRight, Clock3 } from "lucide-react";
import { Section } from "@/components/ui/Section";

const GUIDES = [
    {
        href: "/guides/best-time-to-book-flights",
        category: "Flight strategy",
        title: "When to book flights: a practical timing guide for domestic and long-haul trips",
        excerpt:
            "How far out to book, when fares usually move, and how to avoid buying too early or too late.",
        readTime: "6 min read",
    },
    {
        href: "/guides/how-to-avoid-hidden-hotel-fees",
        category: "Hotels",
        title: "How to avoid hidden hotel fees before you click book",
        excerpt:
            "A traveler-first checklist for spotting resort fees, breakfast traps, parking add-ons, and prepaid restrictions.",
        readTime: "5 min read",
    },
    {
        href: "/guides/first-trip-to-japan",
        category: "Destination guide",
        title: "First trip to Japan: flights, neighborhoods, and common mistakes to avoid",
        excerpt:
            "An original starter guide for choosing airports, booking timing, and where to stay on a first visit.",
        readTime: "7 min read",
    },
];

export function GuidesSection() {
    return (
        <Section
            eyebrow="Original travel guides"
            title={
                <>
                    Useful content written for travelers,
                    <br className="hidden sm:block" /> not search engines.
                </>
            }
            description="These guides are here to help people actually plan better trips — and to give partner programs a clear signal that this is a real travel site with original, regularly updated content."
        >
            <div className="grid gap-5 lg:grid-cols-3">
                {GUIDES.map((guide) => (
                    <Link
                        key={guide.href}
                        href={guide.href}
                        className="group rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle transition-all duration-300 hover:-translate-y-1 hover:shadow-float"
                    >
                        <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                            {guide.category}
                        </p>
                        <h3 className="mt-4 font-display text-2xl text-ink-900 tracking-tight text-balance">
                            {guide.title}
                        </h3>
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
            <div className="mt-10">
                <Link href="/guides" className="btn-ghost btn-md">
                    Browse all guides
                </Link>
            </div>
        </Section>
    );
}