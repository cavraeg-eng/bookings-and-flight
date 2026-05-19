import Link from "next/link";
import { Plane, Instagram, Twitter, Facebook, Youtube } from "lucide-react";
import { NewsletterForm } from "./NewsletterForm";

const COLUMNS = [
    {
        heading: "Travel",
        links: [
            { href: "/flights", label: "Flights" },
            { href: "/hotels", label: "Hotels" },
            { href: "/cars", label: "Car rentals" },
            { href: "/activities", label: "Activities" },
        ],
    },
    {
        heading: "Company",
        links: [
            { href: "/about", label: "About us" },
            { href: "/how-it-works", label: "How it works" },
            { href: "/guides", label: "Guides" },
            { href: "/integrations", label: "Integrations" },
            { href: "/integrations/switchboard", label: "Switchboard" },
            { href: "/affiliate-disclosure", label: "Affiliate disclosure" },
        ],
    },
    {
        heading: "Legal",
        links: [
            { href: "/privacy", label: "Privacy policy" },
            { href: "/terms", label: "Terms & conditions" },
            { href: "/affiliate-disclosure", label: "Disclosure" },
            { href: "/privacy#cookies", label: "Cookie policy" },
        ],
    },
];

export function SiteFooter() {
    return (
        <footer className="bg-ink-900 text-cream-200 mt-auto">
            <div className="container pt-20 pb-10">
                {/* Newsletter CTA */}
                <div className="grid gap-10 md:grid-cols-2 md:gap-16 mb-16 pb-16 border-b border-cream-100/10">
                    <div>
                        <p className="eyebrow !text-amber-400 mb-4">
                            <span className="!bg-amber-400" />
                            Postcards, not spam
                        </p>
                        <h2 className="font-display text-display-md text-cream-100 text-balance">
                            Get deal drops before they take off.
                        </h2>
                        <p className="mt-4 text-cream-200/70 max-w-md">
                            One email every Tuesday. Mistake fares, flash sales and insider routes
                            — curated by real humans, never generated. Unsubscribe anytime.
                        </p>
                    </div>
                    <NewsletterForm />
                </div>

                {/* Main footer grid */}
                <div className="grid gap-10 md:grid-cols-[2fr_1fr_1fr_1fr]">
                    <div>
                        <Link href="/" className="inline-flex items-center gap-2.5">
                            <span className="flex h-9 w-9 items-center justify-center rounded-full bg-amber-400 text-ink-900">
                                <Plane className="h-4 w-4" strokeWidth={2.5} />
                            </span>
                            <span className="font-display text-xl leading-none text-cream-100 tracking-tight">
                                Bookings<span className="text-amber-400">&</span>Flights
                            </span>
                        </Link>
                        <p className="mt-4 max-w-xs text-sm text-cream-200/60 leading-relaxed">
                            A branded travel search experience with Trip.com-powered flight and
                            hotel pages, plus live partner rollouts still in progress for the rest
                            of the site.
                        </p>
                        <div className="mt-6 flex items-center gap-2" aria-label="Social links">
                            {[
                                { label: "Instagram", Icon: Instagram },
                                { label: "Twitter", Icon: Twitter },
                                { label: "Facebook", Icon: Facebook },
                                { label: "YouTube", Icon: Youtube },
                            ].map(({ label, Icon }) => (
                                <Link
                                    key={label}
                                    href="#"
                                    aria-label={label}
                                    className="flex h-9 w-9 items-center justify-center rounded-full border border-cream-100/10 hover:border-amber-400 hover:text-amber-400 transition-colors"
                                >
                                    <Icon className="h-4 w-4" />
                                </Link>
                            ))}
                        </div>
                    </div>

                    {COLUMNS.map((col) => (
                        <div key={col.heading}>
                            <h3 className="text-xs font-semibold uppercase tracking-[0.16em] text-cream-100">
                                {col.heading}
                            </h3>
                            <ul className="mt-5 space-y-3">
                                {col.links.map((l) => (
                                    <li key={l.label}>
                                        <Link
                                            href={l.href}
                                            className="text-sm text-cream-200/70 hover:text-amber-400 transition-colors"
                                        >
                                            {l.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                {/* Legal */}
                <div className="mt-16 pt-8 border-t border-cream-100/10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-xs text-cream-200/50">
                    <p>
                        © {new Date().getFullYear()} Bookings & Flights. Meta-search, not a travel
                        seller. We may earn a commission when you book with a partner.
                    </p>
                    <p className="flex items-center gap-4">
                        <Link href="/privacy" className="hover:text-cream-100">Privacy</Link>
                        <Link href="/terms" className="hover:text-cream-100">Terms</Link>
                        <Link href="/affiliate-disclosure" className="hover:text-cream-100">Disclosure</Link>
                    </p>
                </div>
            </div>
        </footer>
    );
}
