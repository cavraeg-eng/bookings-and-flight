import type { Metadata } from "next";
import Link from "next/link";
import { MapPin, ArrowRight, Plane } from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Badge } from "@/components/ui/Badge";
import { destinations } from "@/data/destinations";

/* ------------------------------------------------------------------ */
/*  Metadata                                                           */
/* ------------------------------------------------------------------ */

export const metadata: Metadata = {
  title: "Popular Travel Destinations — Flight Deals & City Guides",
  description:
    "Explore 20 of the world's most popular travel destinations. Find cheap flights, travel tips, best times to visit, and average prices for London, Paris, Tokyo, and more.",
  alternates: { canonical: "/destinations" },
  openGraph: {
    title: "Popular Travel Destinations — Bookings and Flights",
    description:
      "Discover cheap flights and travel guides for the world's top destinations.",
    url: "/destinations",
    type: "website",
  },
};

/* ------------------------------------------------------------------ */
/*  Page                                                               */
/* ------------------------------------------------------------------ */

export default function DestinationsIndexPage() {
  return (
    <main className="bg-cream-100">
      {/* ── Hero ─────────────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-gradient-to-br from-ink-900 via-ink-800 to-sky-800">
        <div className="absolute inset-0 bg-grain opacity-30 mix-blend-overlay pointer-events-none" />
        <div className="relative container py-20 lg:py-28">
          <div className="max-w-3xl animate-fade-up">
            <Badge variant="deal" icon={MapPin} className="mb-5">
              {destinations.length} destinations
            </Badge>
            <h1 className="font-display text-display-xl text-white tracking-tight text-balance">
              Where will you go next?
            </h1>
            <p className="mt-5 text-xl text-white/70 leading-relaxed max-w-2xl font-sans">
              Explore the world&rsquo;s most popular destinations. Compare flight
              prices, discover the best time to visit, and start planning your
              next adventure.
            </p>
          </div>
        </div>
      </section>

      {/* ── Grid ─────────────────────────────────────────────────── */}
      <section className="container py-16 lg:py-24">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
          {destinations.map((dest, i) => (
            <Link
              key={dest.slug}
              href={`/destinations/${dest.slug}`}
              className="group no-underline animate-fade-up"
              style={{
                animationDelay: `${Math.min(i * 50, 600)}ms`,
                animationFillMode: "both",
              }}
            >
              <Card hoverable padding="none" className="h-full flex flex-col">
                {/* Gradient header */}
                <div
                  className={`relative h-36 bg-gradient-to-br ${dest.gradient}`}
                >
                  <div className="absolute inset-0 bg-grain opacity-20 mix-blend-overlay" />
                  <div className="absolute top-3 right-3">
                    <Badge className="bg-white/20 text-white backdrop-blur-sm border-0 text-[0.65rem]">
                      {dest.airportCode}
                    </Badge>
                  </div>
                  <div className="absolute bottom-3 left-4">
                    <p className="text-xs font-semibold text-white/70 flex items-center gap-1">
                      <MapPin className="h-3 w-3" />
                      {dest.country}
                    </p>
                  </div>
                </div>

                {/* Body */}
                <div className="flex flex-col flex-1 p-5">
                  <h2 className="font-display text-xl font-semibold text-ink-900 group-hover:text-amber-600 transition-colors">
                    {dest.name}
                  </h2>
                  <p className="mt-1.5 text-sm text-ink-500 line-clamp-2 flex-1">
                    {dest.tagline}
                  </p>
                  <div className="mt-4 flex items-center justify-between pt-4 border-t border-cream-200/60">
                    <div>
                      <p className="text-[0.65rem] uppercase tracking-wider text-ink-400 font-semibold">
                        From
                      </p>
                      <p className="text-lg font-display font-bold text-ink-900">
                        ${dest.avgFlightPrice}
                      </p>
                    </div>
                    <span className="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 group-hover:gap-2.5 transition-all">
                      <Plane className="h-3.5 w-3.5" />
                      Explore
                      <ArrowRight className="h-3.5 w-3.5" />
                    </span>
                  </div>
                </div>
              </Card>
            </Link>
          ))}
        </div>
      </section>
    </main>
  );
}
