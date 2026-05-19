import type { Metadata } from "next";
import Link from "next/link";
import {
  MapPin,
  Sun,
  DollarSign,
  Sparkles,
  ArrowRight,
  Plane,
  Calendar,
} from "lucide-react";
import { notFound } from "next/navigation";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";
import { SearchForm } from "@/components/search/SearchForm";
import {
  destinations,
  getDestination,
  getDestinationsBySlug,
  type Destination,
} from "@/data/destinations";

/* ------------------------------------------------------------------ */
/*  Static params                                                      */
/* ------------------------------------------------------------------ */

export function generateStaticParams() {
  return destinations.map((d) => ({ slug: d.slug }));
}

/* ------------------------------------------------------------------ */
/*  Metadata                                                           */
/* ------------------------------------------------------------------ */

type PageProps = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { slug } = await params;
  const dest = getDestination(slug);
  if (!dest) return {};

  return {
    title: `Cheap Flights to ${dest.name} (${dest.airportCode}) — Deals & Tips`,
    description: `Find the best flight deals to ${dest.name}, ${dest.country}. ${dest.tagline}. Average fares from $${dest.avgFlightPrice}. Best time to visit: ${dest.bestTimeToVisit}.`,
    alternates: { canonical: `/destinations/${dest.slug}` },
    openGraph: {
      title: `Flights to ${dest.name} — Bookings and Flights`,
      description: dest.description,
      url: `/destinations/${dest.slug}`,
      type: "article",
    },
  };
}

/* ------------------------------------------------------------------ */
/*  Page                                                               */
/* ------------------------------------------------------------------ */

export default async function DestinationPage({ params }: PageProps) {
  const { slug } = await params;
  const dest = getDestination(slug);
  if (!dest) notFound();

  const nearby = getDestinationsBySlug(dest.nearbyDestinations);

  return (
    <main className="bg-cream-100">
      {/* ── Hero ─────────────────────────────────────────────────── */}
      <section
        className={`relative overflow-hidden bg-gradient-to-br ${dest.gradient}`}
      >
        {/* Grain overlay */}
        <div className="absolute inset-0 bg-grain opacity-30 mix-blend-overlay pointer-events-none" />
        <div className="relative container py-20 lg:py-32">
          <div className="max-w-3xl animate-fade-up">
            <Badge
              variant="deal"
              icon={MapPin}
              className="mb-5"
            >
              {dest.country} · {dest.countryCode}
            </Badge>
            <h1 className="font-display text-display-xl text-white tracking-tight text-balance">
              {dest.name}
            </h1>
            <p className="mt-4 text-xl text-white/80 leading-relaxed max-w-2xl font-sans">
              {dest.tagline}
            </p>
          </div>
        </div>
      </section>

      {/* ── Search Form ──────────────────────────────────────────── */}
      <section className="container -mt-8 relative z-10">
        <Card variant="glass" padding="lg" className="shadow-float">
          <p className="text-sm font-semibold text-ink-500 mb-3">
            Search flights to {dest.name}
          </p>
          <SearchForm
            initialTab="flights"
            initialValues={{ destination: dest.airportCode }}
            compact
          />
        </Card>
      </section>

      {/* ── About Section ────────────────────────────────────────── */}
      <section className="container py-16 lg:py-24">
        <div className="grid gap-8 lg:grid-cols-3 lg:gap-12">
          {/* Description — spans 2 cols */}
          <div className="lg:col-span-2 animate-fade-up">
            <p className="eyebrow mb-4">
              <span />
              About {dest.name}
            </p>
            <h2 className="font-display text-display-md text-ink-900 tracking-tight text-balance">
              Discover {dest.name}, {dest.country}
            </h2>
            <p className="mt-6 text-body-lg text-ink-500 leading-relaxed">
              {dest.description}
            </p>
          </div>

          {/* Info cards — right column */}
          <div className="flex flex-col gap-5">
            {/* Best time */}
            <Card padding="md" hoverable className="animate-fade-up">
              <div className="flex items-start gap-4">
                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                  <Calendar className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-xs font-semibold uppercase tracking-wider text-ink-400">
                    Best Time to Visit
                  </p>
                  <p className="mt-1 text-sm font-semibold text-ink-900">
                    {dest.bestTimeToVisit}
                  </p>
                </div>
              </div>
            </Card>

            {/* Avg price */}
            <Card padding="md" hoverable className="animate-fade-up">
              <div className="flex items-start gap-4">
                <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                  <DollarSign className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-xs font-semibold uppercase tracking-wider text-ink-400">
                    Average Flight Price
                  </p>
                  <p className="mt-1 text-2xl font-display font-bold text-ink-900">
                    ${dest.avgFlightPrice}
                    <span className="text-sm font-sans font-normal text-ink-400 ml-1">
                      round-trip
                    </span>
                  </p>
                  <Link
                    href={`/flights?destination=${dest.airportCode}`}
                    className="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 hover:text-amber-500 transition-colors"
                  >
                    <Plane className="h-3.5 w-3.5" />
                    Search Flights
                    <ArrowRight className="h-3.5 w-3.5" />
                  </Link>
                </div>
              </div>
            </Card>
          </div>
        </div>
      </section>

      {/* ── Top Highlights ───────────────────────────────────────── */}
      <section className="bg-cream-50 border-y border-ink-900/5">
        <div className="container py-16 lg:py-24">
          <p className="eyebrow mb-4">
            <span />
            Top highlights
          </p>
          <h2 className="font-display text-display-md text-ink-900 tracking-tight mb-10">
            Things to see in {dest.name}
          </h2>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {dest.highlights.map((highlight, i) => (
              <Card
                key={highlight}
                padding="md"
                hoverable
                className="text-center animate-fade-up"
                style={{ animationDelay: `${i * 80}ms`, animationFillMode: "both" }}
              >
                <div className="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                  <Sparkles className="h-5 w-5" />
                </div>
                <p className="text-sm font-semibold text-ink-900">{highlight}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* ── Nearby Destinations ──────────────────────────────────── */}
      {nearby.length > 0 && (
        <section className="container py-16 lg:py-24">
          <p className="eyebrow mb-4">
            <span />
            Explore nearby
          </p>
          <h2 className="font-display text-display-md text-ink-900 tracking-tight mb-10">
            Destinations near {dest.name}
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {nearby.map((nd, i) => (
              <NearbyCard key={nd.slug} dest={nd} index={i} />
            ))}
          </div>
        </section>
      )}
    </main>
  );
}

/* ------------------------------------------------------------------ */
/*  Nearby destination card                                            */
/* ------------------------------------------------------------------ */

function NearbyCard({ dest, index }: { dest: Destination; index: number }) {
  return (
    <Link
      href={`/destinations/${dest.slug}`}
      className="group no-underline animate-fade-up"
      style={{ animationDelay: `${index * 100}ms`, animationFillMode: "both" }}
    >
      <Card hoverable padding="none" className="overflow-hidden">
        {/* Gradient header */}
        <div
          className={`h-28 bg-gradient-to-br ${dest.gradient} relative`}
        >
          <div className="absolute inset-0 bg-grain opacity-20 mix-blend-overlay" />
          <div className="absolute bottom-3 left-4">
            <span className="text-xs font-semibold text-white/70">{dest.country}</span>
          </div>
        </div>
        {/* Body */}
        <div className="p-4">
          <h3 className="font-display text-lg font-semibold text-ink-900 group-hover:text-amber-600 transition-colors">
            {dest.name}
          </h3>
          <p className="mt-1 text-sm text-ink-500 line-clamp-1">{dest.tagline}</p>
          <div className="mt-3 flex items-center justify-between">
            <span className="text-sm font-semibold text-ink-900">
              From ${dest.avgFlightPrice}
            </span>
            <ArrowRight className="h-4 w-4 text-ink-300 group-hover:text-amber-500 transition-colors group-hover:translate-x-0.5" />
          </div>
        </div>
      </Card>
    </Link>
  );
}
