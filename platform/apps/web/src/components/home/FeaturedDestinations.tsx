import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import { Section } from "@/components/ui/Section";
import { cn } from "@/lib/cn";

type Destination = {
    city: string;
    country: string;
    image: string;
    iata?: string;
    from: string;
    currency: string;
    tag?: string;
    vertical: "flights" | "hotels" | "activities";
    href: string;
};

const DESTINATIONS: Destination[] = [
    {
        city: "Tokyo",
        country: "Japan",
        image: "https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1200&q=70",
        iata: "HND",
        from: "487",
        currency: "USD",
        tag: "Editor's pick",
        vertical: "flights",
        href: "/flights?origin=JFK&destination=HND",
    },
    {
        city: "Lisbon",
        country: "Portugal",
        image: "https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=1200&q=70",
        iata: "LIS",
        from: "318",
        currency: "USD",
        tag: "Trending",
        vertical: "flights",
        href: "/flights?origin=JFK&destination=LIS",
    },
    {
        city: "Reykjavík",
        country: "Iceland",
        image: "https://images.unsplash.com/photo-1500043357865-c6b8827edf10?auto=format&fit=crop&w=1200&q=70",
        iata: "KEF",
        from: "412",
        currency: "USD",
        vertical: "flights",
        href: "/flights?origin=JFK&destination=KEF",
    },
    {
        city: "Bali",
        country: "Indonesia",
        image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=70",
        from: "89",
        currency: "USD",
        tag: "Villas from",
        vertical: "hotels",
        href: "/hotels?destination=Bali",
    },
    {
        city: "Marrakech",
        country: "Morocco",
        image: "https://images.unsplash.com/photo-1489749798305-4fea3ae63d43?auto=format&fit=crop&w=1200&q=70",
        from: "112",
        currency: "USD",
        tag: "Riads from",
        vertical: "hotels",
        href: "/hotels?destination=Marrakech",
    },
    {
        city: "Mexico City",
        country: "Mexico",
        image: "https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?auto=format&fit=crop&w=1200&q=70",
        from: "42",
        currency: "USD",
        tag: "Tours from",
        vertical: "activities",
        href: "/activities?destination=Mexico%20City",
    },
];

export function FeaturedDestinations() {
    return (
        <Section
            id="destinations"
            eyebrow="Where we'd go next"
            title={
                <>
                    Destinations we&apos;re<br className="hidden sm:block" />{" "}
                    <em className="italic text-ink-500 font-normal">watching right now.</em>
                </>
            }
            description="Hand-picked cities where our search engine is seeing the sharpest price drops this month. Click any card to jump straight into a live search."
            tone="alt"
        >
            <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-6 lg:grid-rows-2 lg:auto-rows-[minmax(240px,auto)]">
                {DESTINATIONS.map((d, i) => (
                    <DestinationCard
                        key={d.city}
                        destination={d}
                        // Editorial mosaic: first card spans 2 rows + 2 cols on large
                        className={cn(
                            i === 0 && "lg:col-span-3 lg:row-span-2",
                            i === 1 && "lg:col-span-3",
                            i === 2 && "lg:col-span-2",
                            i === 3 && "lg:col-span-2",
                            i === 4 && "lg:col-span-2",
                            i === 5 && "lg:col-span-3",
                        )}
                        large={i === 0}
                    />
                ))}
            </div>
        </Section>
    );
}

function DestinationCard({
    destination: d,
    className,
    large = false,
}: {
    destination: Destination;
    className?: string;
    large?: boolean;
}) {
    return (
        <Link
            href={d.href}
            className={cn(
                "group relative overflow-hidden rounded-3xl bg-ink-900 shadow-card isolate min-h-[260px]",
                "transition-all duration-500 hover:shadow-float focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-4 focus-visible:ring-offset-cream-50",
                className,
            )}
        >
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
                src={d.image}
                alt={`${d.city}, ${d.country}`}
                loading="lazy"
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-[1600ms] group-hover:scale-[1.06]"
            />
            {/* Vignette */}
            <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-t from-ink-950/90 via-ink-950/30 to-transparent"
            />
            <div
                aria-hidden
                className="absolute inset-0 bg-gradient-to-r from-ink-950/50 via-transparent to-transparent"
            />

            {/* Tag */}
            {d.tag ? (
                <div className="absolute top-5 left-5 z-10">
                    <span className="chip-amber backdrop-blur-sm">
                        {d.tag}
                    </span>
                </div>
            ) : null}

            {/* Arrow */}
            <div
                aria-hidden
                className="absolute top-5 right-5 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-cream-100/20 text-cream-100 backdrop-blur-sm transition-all duration-300 group-hover:bg-amber-400 group-hover:text-ink-900 group-hover:rotate-[12deg]"
            >
                <ArrowUpRight className="h-5 w-5" strokeWidth={2.25} />
            </div>

            {/* Content */}
            <div className="relative z-10 h-full flex flex-col justify-end p-6 sm:p-7 text-cream-100">
                <p className="text-[0.68rem] uppercase tracking-[0.22em] text-cream-200/80">
                    {d.country}
                    {d.iata ? <span className="ml-2 font-board">· {d.iata}</span> : null}
                </p>
                <h3
                    className={cn(
                        "mt-1 font-display leading-[0.95] tracking-tight text-balance",
                        large ? "text-5xl sm:text-6xl" : "text-3xl sm:text-4xl",
                    )}
                >
                    {d.city}
                </h3>
                <p className="mt-4 flex items-baseline gap-2 text-cream-200/80">
                    <span className="text-[0.68rem] uppercase tracking-[0.18em]">From</span>
                    <span className="font-display text-2xl text-cream-100 font-board">
                        ${d.from}
                    </span>
                    <span className="text-xs uppercase tracking-[0.14em] text-cream-200/60">
                        {d.currency}
                    </span>
                </p>
            </div>
        </Link>
    );
}
