"use client";

import { useState, useMemo } from "react";
import Link from "next/link";
import {
  Flame,
  Clock,
  Plane,
  Hotel,
  ArrowRight,
  Star,
  Bell,
  MapPin,
  Filter,
  Tag,
  Zap,
  TrendingDown,
  Send,
} from "lucide-react";
import { Card } from "@/components/ui/Card";
import { Button } from "@/components/ui/Button";
import { Badge } from "@/components/ui/Badge";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

interface FlightDeal {
  id: string;
  origin: string;
  originCode: string;
  destination: string;
  destinationCode: string;
  price: number;
  airline: string;
  region: string;
}

interface HotelDeal {
  id: string;
  city: string;
  hotelName: string;
  pricePerNight: number;
  stars: number;
  region: string;
}

interface FlashDeal {
  id: string;
  origin: string;
  originCode: string;
  destination: string;
  destinationCode: string;
  price: number;
  originalPrice: number;
  airline: string;
  endsIn: string;
}

/* ------------------------------------------------------------------ */
/*  Static deal data                                                   */
/* ------------------------------------------------------------------ */

const DEPARTURE_CITIES = [
  { label: "All Cities", value: "all" },
  { label: "New York (JFK)", value: "JFK" },
  { label: "Los Angeles (LAX)", value: "LAX" },
  { label: "Chicago (ORD)", value: "ORD" },
  { label: "Miami (MIA)", value: "MIA" },
  { label: "Dallas (DFW)", value: "DFW" },
  { label: "San Francisco (SFO)", value: "SFO" },
  { label: "Seattle (SEA)", value: "SEA" },
  { label: "Boston (BOS)", value: "BOS" },
  { label: "Atlanta (ATL)", value: "ATL" },
  { label: "Denver (DEN)", value: "DEN" },
];

const REGIONS = [
  { label: "All Regions", value: "all" },
  { label: "Europe", value: "europe" },
  { label: "Asia", value: "asia" },
  { label: "Caribbean", value: "caribbean" },
  { label: "South America", value: "south-america" },
];

const FLASH_DEALS: FlashDeal[] = [
  {
    id: "f1",
    origin: "New York",
    originCode: "JFK",
    destination: "London",
    destinationCode: "LHR",
    price: 289,
    originalPrice: 485,
    airline: "British Airways",
    endsIn: "Ends in 2 days",
  },
  {
    id: "f2",
    origin: "Los Angeles",
    originCode: "LAX",
    destination: "Tokyo",
    destinationCode: "NRT",
    price: 449,
    originalPrice: 780,
    airline: "ANA",
    endsIn: "Ends in 18 hours",
  },
  {
    id: "f3",
    origin: "Miami",
    originCode: "MIA",
    destination: "Cancún",
    destinationCode: "CUN",
    price: 129,
    originalPrice: 275,
    airline: "JetBlue",
    endsIn: "Ends in 3 days",
  },
  {
    id: "f4",
    origin: "Chicago",
    originCode: "ORD",
    destination: "Paris",
    destinationCode: "CDG",
    price: 339,
    originalPrice: 610,
    airline: "Air France",
    endsIn: "Ends tomorrow",
  },
];

const FLIGHT_DEALS: FlightDeal[] = [
  { id: "fd1", origin: "New York", originCode: "JFK", destination: "Barcelona", destinationCode: "BCN", price: 312, airline: "Iberia", region: "europe" },
  { id: "fd2", origin: "Los Angeles", originCode: "LAX", destination: "Bangkok", destinationCode: "BKK", price: 425, airline: "EVA Air", region: "asia" },
  { id: "fd3", origin: "Miami", originCode: "MIA", destination: "Montego Bay", destinationCode: "MBJ", price: 198, airline: "American Airlines", region: "caribbean" },
  { id: "fd4", origin: "San Francisco", originCode: "SFO", destination: "Singapore", destinationCode: "SIN", price: 478, airline: "Singapore Airlines", region: "asia" },
  { id: "fd5", origin: "Chicago", originCode: "ORD", destination: "Amsterdam", destinationCode: "AMS", price: 345, airline: "KLM", region: "europe" },
  { id: "fd6", origin: "Dallas", originCode: "DFW", destination: "Rio de Janeiro", destinationCode: "GIG", price: 389, airline: "LATAM", region: "south-america" },
  { id: "fd7", origin: "Boston", originCode: "BOS", destination: "Reykjavik", destinationCode: "KEF", price: 219, airline: "Icelandair", region: "europe" },
  { id: "fd8", origin: "Seattle", originCode: "SEA", destination: "Bali", destinationCode: "DPS", price: 510, airline: "Cathay Pacific", region: "asia" },
  { id: "fd9", origin: "Atlanta", originCode: "ATL", destination: "Punta Cana", destinationCode: "PUJ", price: 235, airline: "Delta", region: "caribbean" },
  { id: "fd10", origin: "Denver", originCode: "DEN", destination: "Lisbon", destinationCode: "LIS", price: 365, airline: "TAP Portugal", region: "europe" },
  { id: "fd11", origin: "New York", originCode: "JFK", destination: "Istanbul", destinationCode: "IST", price: 395, airline: "Turkish Airlines", region: "europe" },
  { id: "fd12", origin: "Miami", originCode: "MIA", destination: "Buenos Aires", destinationCode: "EZE", price: 420, airline: "Aerolíneas Argentinas", region: "south-america" },
];

const HOTEL_DEALS: HotelDeal[] = [
  { id: "h1", city: "Paris", hotelName: "Le Marais Boutique Hotel", pricePerNight: 129, stars: 4, region: "europe" },
  { id: "h2", city: "Bangkok", hotelName: "Riverside Grand Bangkok", pricePerNight: 65, stars: 5, region: "asia" },
  { id: "h3", city: "Cancún", hotelName: "Playa del Carmen Resort", pricePerNight: 145, stars: 4, region: "caribbean" },
  { id: "h4", city: "Barcelona", hotelName: "Gothic Quarter Inn", pricePerNight: 110, stars: 4, region: "europe" },
  { id: "h5", city: "Rio de Janeiro", hotelName: "Ipanema Beachfront", pricePerNight: 95, stars: 4, region: "south-america" },
  { id: "h6", city: "Singapore", hotelName: "Marina Bay Suites", pricePerNight: 185, stars: 5, region: "asia" },
];

/* ------------------------------------------------------------------ */
/*  Page                                                               */
/* ------------------------------------------------------------------ */

export default function DealsPage() {
  const [departure, setDeparture] = useState("all");
  const [region, setRegion] = useState("all");
  const [email, setEmail] = useState("");
  const [subscribed, setSubscribed] = useState(false);

  const filteredFlights = useMemo(() => {
    return FLIGHT_DEALS.filter((d) => {
      if (departure !== "all" && d.originCode !== departure) return false;
      if (region !== "all" && d.region !== region) return false;
      return true;
    });
  }, [departure, region]);

  const filteredHotels = useMemo(() => {
    return HOTEL_DEALS.filter((d) => {
      if (region !== "all" && d.region !== region) return false;
      return true;
    });
  }, [region]);

  return (
    <main className="bg-cream-100">
      {/* ── Hero ─────────────────────────────────────────────────── */}
      <section className="relative overflow-hidden bg-gradient-to-br from-amber-600 via-orange-700 to-ink-900">
        <div className="absolute inset-0 bg-grain opacity-30 mix-blend-overlay pointer-events-none" />
        <div className="relative container py-20 lg:py-28">
          <div className="max-w-3xl animate-fade-up">
            <Badge variant="deal" icon={Flame} className="mb-5">
              Updated daily
            </Badge>
            <h1 className="font-display text-display-xl text-white tracking-tight text-balance">
              Today&rsquo;s Best Travel Deals
            </h1>
            <p className="mt-5 text-xl text-white/75 leading-relaxed max-w-2xl font-sans">
              Hand-picked flight and hotel deals with real savings. Filter by
              your departure city and dream destination.
            </p>
          </div>
        </div>
      </section>

      {/* ── Filter Bar ───────────────────────────────────────────── */}
      <section className="container -mt-7 relative z-10">
        <Card variant="glass" padding="md" className="shadow-float">
          <div className="flex flex-wrap items-center gap-4">
            <div className="flex items-center gap-2 text-ink-500">
              <Filter className="h-4 w-4" />
              <span className="text-sm font-semibold">Filter deals:</span>
            </div>

            <label className="flex flex-col gap-1">
              <span className="text-[0.6rem] font-semibold uppercase tracking-wider text-ink-400">
                Departure city
              </span>
              <select
                value={departure}
                onChange={(e) => setDeparture(e.target.value)}
                className="rounded-lg border border-ink-200/60 bg-white px-3 py-2 text-sm font-medium text-ink-900 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none"
              >
                {DEPARTURE_CITIES.map((c) => (
                  <option key={c.value} value={c.value}>
                    {c.label}
                  </option>
                ))}
              </select>
            </label>

            <label className="flex flex-col gap-1">
              <span className="text-[0.6rem] font-semibold uppercase tracking-wider text-ink-400">
                Destination region
              </span>
              <select
                value={region}
                onChange={(e) => setRegion(e.target.value)}
                className="rounded-lg border border-ink-200/60 bg-white px-3 py-2 text-sm font-medium text-ink-900 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 focus:outline-none"
              >
                {REGIONS.map((r) => (
                  <option key={r.value} value={r.value}>
                    {r.label}
                  </option>
                ))}
              </select>
            </label>
          </div>
        </Card>
      </section>

      {/* ── Flash Deals ──────────────────────────────────────────── */}
      <section className="container py-16 lg:py-20">
        <div className="flex items-center gap-3 mb-8">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600">
            <Zap className="h-5 w-5" />
          </div>
          <div>
            <h2 className="font-display text-display-md text-ink-900 tracking-tight">
              Flash Deals
            </h2>
            <p className="text-sm text-ink-500">Limited-time fares — grab them before they&rsquo;re gone</p>
          </div>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {FLASH_DEALS.map((deal, i) => (
            <Link
              key={deal.id}
              href={`/flights?origin=${deal.originCode}&destination=${deal.destinationCode}`}
              className="group no-underline animate-fade-up"
              style={{ animationDelay: `${i * 80}ms`, animationFillMode: "both" }}
            >
              <Card hoverable padding="md" className="h-full relative border-red-200/60">
                {/* Urgency badge */}
                <Badge className="bg-red-600 text-white border-0 mb-4" icon={Clock}>
                  {deal.endsIn}
                </Badge>

                <p className="text-sm font-semibold text-ink-900">
                  {deal.origin} → {deal.destination}
                </p>
                <p className="text-xs text-ink-400 mt-1">{deal.airline}</p>

                <div className="mt-4 flex items-baseline gap-2">
                  <span className="text-2xl font-display font-bold text-ink-900">
                    ${deal.price}
                  </span>
                  <span className="text-sm text-ink-400 line-through">
                    ${deal.originalPrice}
                  </span>
                  <Badge variant="cheapest" className="ml-auto text-[0.6rem]">
                    {Math.round(((deal.originalPrice - deal.price) / deal.originalPrice) * 100)}% off
                  </Badge>
                </div>

                <div className="mt-4 pt-3 border-t border-cream-200/60">
                  <span className="inline-flex items-center gap-1.5 text-sm font-semibold text-amber-600 group-hover:text-amber-500 transition-colors">
                    <Plane className="h-3.5 w-3.5" />
                    View Deal
                    <ArrowRight className="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" />
                  </span>
                </div>
              </Card>
            </Link>
          ))}
        </div>
      </section>

      {/* ── Flight Deals Grid ────────────────────────────────────── */}
      <section className="bg-cream-50 border-y border-ink-900/5">
        <div className="container py-16 lg:py-20">
          <div className="flex items-center gap-3 mb-8">
            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
              <Plane className="h-5 w-5" />
            </div>
            <div>
              <h2 className="font-display text-display-md text-ink-900 tracking-tight">
                Flight Deals
              </h2>
              <p className="text-sm text-ink-500">
                {filteredFlights.length} deal{filteredFlights.length !== 1 ? "s" : ""} found
              </p>
            </div>
          </div>

          {filteredFlights.length === 0 ? (
            <Card padding="lg" className="text-center">
              <TrendingDown className="h-10 w-10 text-ink-300 mx-auto mb-3" />
              <p className="text-ink-500 font-medium">
                No flight deals match your filters. Try broadening your search.
              </p>
            </Card>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
              {filteredFlights.map((deal, i) => (
                <Link
                  key={deal.id}
                  href={`/flights?origin=${deal.originCode}&destination=${deal.destinationCode}`}
                  className="group no-underline animate-fade-up"
                  style={{ animationDelay: `${i * 60}ms`, animationFillMode: "both" }}
                >
                  <Card hoverable padding="md" className="h-full">
                    <div className="flex items-center gap-2 mb-3">
                      <Badge variant="default" className="text-[0.6rem]">
                        {deal.airline}
                      </Badge>
                    </div>
                    <p className="text-base font-semibold text-ink-900">
                      {deal.origin}
                      <span className="mx-2 text-ink-300">→</span>
                      {deal.destination}
                    </p>
                    <p className="text-xs text-ink-400 mt-1 flex items-center gap-1">
                      <MapPin className="h-3 w-3" />
                      {deal.originCode} → {deal.destinationCode}
                    </p>

                    <div className="mt-4 flex items-center justify-between pt-3 border-t border-cream-200/60">
                      <span className="text-xl font-display font-bold text-ink-900">
                        ${deal.price}
                      </span>
                      <span className="inline-flex items-center gap-1 text-sm font-semibold text-amber-600 group-hover:text-amber-500 transition-colors">
                        View Deal
                        <ArrowRight className="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" />
                      </span>
                    </div>
                  </Card>
                </Link>
              ))}
            </div>
          )}
        </div>
      </section>

      {/* ── Hotel Deals ──────────────────────────────────────────── */}
      <section className="container py-16 lg:py-20">
        <div className="flex items-center gap-3 mb-8">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
            <Hotel className="h-5 w-5" />
          </div>
          <div>
            <h2 className="font-display text-display-md text-ink-900 tracking-tight">
              Hotel Deals
            </h2>
            <p className="text-sm text-ink-500">
              {filteredHotels.length} hotel deal{filteredHotels.length !== 1 ? "s" : ""} found
            </p>
          </div>
        </div>

        {filteredHotels.length === 0 ? (
          <Card padding="lg" className="text-center">
            <Hotel className="h-10 w-10 text-ink-300 mx-auto mb-3" />
            <p className="text-ink-500 font-medium">
              No hotel deals match your filters. Try a different region.
            </p>
          </Card>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            {filteredHotels.map((deal, i) => (
              <Link
                key={deal.id}
                href={`/hotels?destination=${deal.city}`}
                className="group no-underline animate-fade-up"
                style={{ animationDelay: `${i * 80}ms`, animationFillMode: "both" }}
              >
                <Card hoverable padding="md" className="h-full">
                  <div className="flex items-center justify-between mb-3">
                    <Badge variant="deal" icon={Tag} className="text-[0.6rem]">
                      Hotel Deal
                    </Badge>
                    <div className="flex items-center gap-0.5">
                      {Array.from({ length: deal.stars }).map((_, s) => (
                        <Star key={s} className="h-3.5 w-3.5 fill-amber-400 text-amber-400" />
                      ))}
                    </div>
                  </div>

                  <h3 className="font-semibold text-ink-900 group-hover:text-amber-600 transition-colors">
                    {deal.hotelName}
                  </h3>
                  <p className="text-sm text-ink-500 mt-1 flex items-center gap-1">
                    <MapPin className="h-3 w-3" />
                    {deal.city}
                  </p>

                  <div className="mt-4 flex items-center justify-between pt-3 border-t border-cream-200/60">
                    <div>
                      <span className="text-xl font-display font-bold text-ink-900">
                        ${deal.pricePerNight}
                      </span>
                      <span className="text-xs text-ink-400 ml-1">/night</span>
                    </div>
                    <span className="inline-flex items-center gap-1 text-sm font-semibold text-amber-600 group-hover:text-amber-500 transition-colors">
                      View Deal
                      <ArrowRight className="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" />
                    </span>
                  </div>
                </Card>
              </Link>
            ))}
          </div>
        )}
      </section>

      {/* ── Price Drop Alerts CTA ────────────────────────────────── */}
      <section className="bg-ink-900 text-cream-100">
        <div className="container py-16 lg:py-24">
          <div className="max-w-2xl mx-auto text-center animate-fade-up">
            <div className="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500/20 text-amber-400">
              <Bell className="h-7 w-7" />
            </div>
            <h2 className="font-display text-display-md text-cream-100 tracking-tight text-balance">
              Never Miss a Deal
            </h2>
            <p className="mt-4 text-body-lg text-cream-300 leading-relaxed">
              Get the best flight and hotel deals delivered to your inbox.
              We&rsquo;ll alert you when prices drop on your favorite routes.
            </p>

            {subscribed ? (
              <div className="mt-8 animate-slideUp">
                <Badge variant="cheapest" className="text-sm px-4 py-2">
                  You&rsquo;re subscribed! Watch your inbox for deals.
                </Badge>
              </div>
            ) : (
              <form
                onSubmit={(e) => {
                  e.preventDefault();
                  if (email) setSubscribed(true);
                }}
                className="mt-8 flex flex-col sm:flex-row items-center gap-3 max-w-md mx-auto"
              >
                <input
                  type="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="your@email.com"
                  className="w-full flex-1 rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm px-4 py-3 text-sm text-cream-100 placeholder:text-cream-400 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:outline-none"
                />
                <Button type="submit" variant="primary" size="lg" className="w-full sm:w-auto whitespace-nowrap">
                  <Send className="h-4 w-4" />
                  Get Alerts
                </Button>
              </form>
            )}
          </div>
        </div>
      </section>
    </main>
  );
}
