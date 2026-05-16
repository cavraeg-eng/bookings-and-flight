import type { Metadata } from "next";
import { AlertCircle, CheckCircle2, ChevronRight, ExternalLink, KeyRound, ServerCog, Sparkles } from "lucide-react";
import { LinkButton } from "@/components/ui/Button";

export const metadata: Metadata = {
    title: "Integrations dashboard",
    description:
        "Backend and affiliate program status for Bookings and Flights, including live adapters, frontend white-label search, and missing credential readiness.",
    alternates: { canonical: "/integrations" },
    robots: { index: false, follow: false },
};

type Vertical = "flights" | "hotels" | "cars" | "activities";

type IntegrationsPayload = {
    publicSite: string;
    apiBase: string;
    activeByVertical: Record<Vertical, string[]>;
    adapters: Array<{
        id: string;
        mode: "real" | "demo";
        verticals: Array<Vertical | "packages">;
        configured: boolean;
    }>;
    credentials: {
        travelpayouts: { configured: boolean; tokenPresent: boolean; markerPresent: boolean };
        booking: { configured: boolean; affiliateIdPresent: boolean; apiTokenPresent: boolean; sandbox: boolean };
        viator: { configured: boolean; apiKeyPresent: boolean; partnerIdPresent: boolean };
        discovercars: { configured: boolean; partnerIdPresent: boolean };
        kiwi: { configured: boolean; affiliateIdPresent: boolean };
    };
    notes: Record<Vertical, string>;
};

const STATUS_COPY: Record<Vertical, { label: string; target: string; cta: string }> = {
    flights: { label: "Flights", target: "/flights", cta: "Open flights search" },
    hotels: { label: "Hotels", target: "/hotels", cta: "Open hotels search" },
    cars: { label: "Cars", target: "/cars", cta: "Open cars search" },
    activities: { label: "Activities", target: "/activities", cta: "Open activities search" },
};

async function getIntegrations(): Promise<IntegrationsPayload | null> {
    const apiBase = process.env.NEXT_PUBLIC_API_BASE ?? "http://localhost:4050";

    try {
        const res = await fetch(`${apiBase}/integrations`, {
            cache: "no-store",
        });

        if (!res.ok) {
            return null;
        }

        return (await res.json()) as IntegrationsPayload;
    } catch {
        return null;
    }
}

export default async function IntegrationsPage() {
    const data = await getIntegrations();

    return (
        <main className="bg-cream-100">
            <section className="border-b border-ink-900/5 bg-gradient-to-b from-sky-50 to-cream-100">
                <div className="container py-20 lg:py-24">
                    <p className="eyebrow mb-5">
                        <span />
                        Operations dashboard
                    </p>
                    <div className="grid gap-8 lg:grid-cols-[1.5fr_0.9fr] lg:items-end">
                        <div>
                            <h1 className="font-display text-display-lg text-ink-900 tracking-tight text-balance">
                                Backend, supplier programs, and credential readiness — in one place.
                            </h1>
                            <p className="mt-6 max-w-3xl text-xl text-ink-500 leading-relaxed text-balance">
                                This page reads the live Fastify backend status and shows which
                                verticals are running real adapters, which are already handled on
                                the frontend via Trip.com white-label/custom-link search, and what
                                credentials are still missing before you can switch on Booking.com,
                                DiscoverCars, Viator, or Kiwi.
                            </p>
                        </div>

                        <div className="rounded-3xl border border-ink-900/10 bg-white p-6 shadow-subtle">
                            <div className="flex items-center gap-3 text-ink-900">
                                <ServerCog className="h-5 w-5 text-amber-600" />
                                <h2 className="font-display text-2xl tracking-tight">Endpoints</h2>
                            </div>
                            <dl className="mt-5 space-y-4 text-sm">
                                <div>
                                    <dt className="uppercase tracking-[0.14em] text-ink-400 text-[0.68rem]">
                                        Public site
                                    </dt>
                                    <dd className="mt-1 font-mono text-ink-900">
                                        {data?.publicSite ?? "http://localhost:3000"}
                                    </dd>
                                </div>
                                <div>
                                    <dt className="uppercase tracking-[0.14em] text-ink-400 text-[0.68rem]">
                                        Backend API
                                    </dt>
                                    <dd className="mt-1 font-mono text-ink-900">
                                        {data?.apiBase ?? "http://localhost:4050"}
                                    </dd>
                                </div>
                                <div className="flex flex-wrap gap-3 pt-3">
                                    <LinkButton
                                        href={data?.apiBase ? `${data.apiBase}/health` : "http://localhost:4050/health"}
                                        target="_blank"
                                        rel="noreferrer"
                                        variant="ghost"
                                        size="sm"
                                    >
                                        Health
                                        <ExternalLink className="h-4 w-4" />
                                    </LinkButton>
                                    <LinkButton
                                        href={data?.apiBase ? `${data.apiBase}/integrations` : "http://localhost:4050/integrations"}
                                        target="_blank"
                                        rel="noreferrer"
                                        variant="ghost"
                                        size="sm"
                                    >
                                        Raw JSON
                                        <ExternalLink className="h-4 w-4" />
                                    </LinkButton>
                                    <LinkButton href="/integrations/switchboard" variant="accent" size="sm">
                                        Open switchboard
                                        <ChevronRight className="h-4 w-4" />
                                    </LinkButton>
                                    <LinkButton href="/how-it-works" variant="ghost" size="sm">
                                        Why this architecture
                                    </LinkButton>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </section>

            <section className="container py-14 lg:py-20">
                {!data ? (
                    <div className="rounded-3xl border border-red-200 bg-red-50 p-7 text-red-900 shadow-subtle">
                        <div className="flex items-start gap-3">
                            <AlertCircle className="mt-0.5 h-5 w-5 shrink-0" />
                            <div>
                                <h2 className="font-display text-2xl tracking-tight">
                                    Backend unavailable
                                </h2>
                                <p className="mt-3 leading-relaxed">
                                    I couldn&apos;t reach the Fastify backend. Start it from
                                    <code className="mx-1">platform/</code>
                                    with <code>npm run dev:api</code> or <code>npm run dev</code>,
                                    then reload this page.
                                </p>
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="space-y-14">
                        <section>
                            <div className="max-w-2xl">
                                <p className="eyebrow mb-4">
                                    <span />
                                    Live vertical status
                                </p>
                                <h2 className="font-display text-display-md text-ink-900 tracking-tight text-balance">
                                    Which travel verticals are live, frontend-powered, or still waiting on credentials.
                                </h2>
                            </div>
                            <div className="mt-8 grid gap-5 lg:grid-cols-2">
                                {(Object.keys(STATUS_COPY) as Vertical[]).map((vertical) => {
                                    const active = data.activeByVertical[vertical];
                                    const isLive = active.some((id) => !id.includes("demo"));
                                    const isFrontendLive =
                                        active.length === 0 &&
                                        (vertical === "flights" || vertical === "hotels");
                                    const isPending = active.length === 0 && !isFrontendLive;
                                    const statusTone = isLive
                                        ? "bg-emerald-50 text-emerald-800 border-emerald-200"
                                        : isFrontendLive
                                            ? "bg-sky-50 text-sky-800 border-sky-200"
                                            : "bg-amber-50 text-amber-800 border-amber-200";

                                    return (
                                        <article
                                            key={vertical}
                                            className="rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle"
                                        >
                                            <div className="flex items-start justify-between gap-4">
                                                <div>
                                                    <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                                                        {STATUS_COPY[vertical].label}
                                                    </p>
                                                    <h3 className="mt-3 font-display text-3xl tracking-tight text-ink-900">
                                                        {isLive
                                                            ? "Live adapter active"
                                                            : isFrontendLive
                                                                ? "Frontend white-label live"
                                                                : "Waiting on live partner"}
                                                    </h3>
                                                </div>
                                                <span
                                                    className={`inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] ${statusTone}`}
                                                >
                                                    {isLive ? (
                                                        <CheckCircle2 className="h-3.5 w-3.5" />
                                                    ) : (
                                                        <Sparkles className="h-3.5 w-3.5" />
                                                    )}
                                                    {isLive ? "Live" : isFrontendLive ? "Widget" : "Pending"}
                                                </span>
                                            </div>

                                            <p className="mt-5 text-ink-500 leading-relaxed">
                                                {data.notes[vertical]}
                                            </p>

                                            <div className="mt-6 flex flex-wrap gap-2">
                                                {active.length > 0 ? (
                                                    active.map((id) => (
                                                        <span
                                                            key={id}
                                                            className="rounded-full bg-ink-900/5 px-3 py-1.5 text-sm font-medium text-ink-700"
                                                        >
                                                            {id}
                                                        </span>
                                                    ))
                                                ) : (
                                                    <span className="rounded-full bg-ink-900/5 px-3 py-1.5 text-sm font-medium text-ink-700">
                                                        {isFrontendLive ? "Trip.com white-label + custom links" : "No adapter configured yet"}
                                                    </span>
                                                )}
                                            </div>

                                            <div className="mt-6 pt-6 border-t border-ink-900/10 flex flex-wrap gap-3">
                                                <LinkButton
                                                    href={STATUS_COPY[vertical].target}
                                                    variant="accent"
                                                    size="sm"
                                                >
                                                    {STATUS_COPY[vertical].cta}
                                                </LinkButton>
                                            </div>
                                        </article>
                                    );
                                })}
                            </div>
                        </section>

                        <section>
                            <div className="max-w-2xl">
                                <p className="eyebrow mb-4">
                                    <span />
                                    Credential readiness
                                </p>
                                <h2 className="font-display text-display-md text-ink-900 tracking-tight text-balance">
                                    What you have connected today, and what still needs approval or keys.
                                </h2>
                            </div>

                            <div className="mt-8 grid gap-5 lg:grid-cols-2">
                                <CredentialCard
                                    name="Travelpayouts / Aviasales"
                                    description="Main live flight program. Requires API token + marker."
                                    configured={data.credentials.travelpayouts.configured}
                                    checks={[
                                        ["API token", data.credentials.travelpayouts.tokenPresent],
                                        ["Marker", data.credentials.travelpayouts.markerPresent],
                                    ]}
                                    nextStep="Already live for flights."
                                />
                                <CredentialCard
                                    name="Booking.com"
                                    description="Optional if you want direct backend hotel inventory in addition to the live Trip.com frontend hotel search."
                                    configured={data.credentials.booking.configured}
                                    checks={[
                                        ["Affiliate ID", data.credentials.booking.affiliateIdPresent],
                                        ["API token", data.credentials.booking.apiTokenPresent],
                                    ]}
                                    nextStep={`Best next step: keep publishing original guides and re-apply once the site is older and more content-rich. Current mode: ${data.credentials.booking.sandbox ? "sandbox" : "production"}.`}
                                />
                                <CredentialCard
                                    name="DiscoverCars"
                                    description="Needed before bringing the cars page back online with a real partner integration."
                                    configured={data.credentials.discovercars.configured}
                                    checks={[
                                        ["Partner ID", data.credentials.discovercars.partnerIdPresent],
                                    ]}
                                    nextStep="Once approved, add the partner ID to the API .env and implement the live adapter."
                                />
                                <CredentialCard
                                    name="Viator"
                                    description="Needed before bringing activities back online with a real tours/experiences adapter."
                                    configured={data.credentials.viator.configured}
                                    checks={[
                                        ["API key", data.credentials.viator.apiKeyPresent],
                                        ["Partner ID", data.credentials.viator.partnerIdPresent],
                                    ]}
                                    nextStep="When both values are present, we can wire the real activities feed."
                                />
                            </div>
                        </section>

                        <section className="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
                            <div className="rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle">
                                <div className="flex items-center gap-3 text-ink-900">
                                    <KeyRound className="h-5 w-5 text-amber-600" />
                                    <h2 className="font-display text-3xl tracking-tight">
                                        Where to manage it
                                    </h2>
                                </div>
                                <div className="mt-6 space-y-5 text-ink-500 leading-relaxed">
                                    <p>
                                        Add or update partner credentials in
                                        <code className="mx-1">platform/services/search-api/.env</code>.
                                        That is the single source of truth for affiliate programs.
                                    </p>
                                    <p>
                                        If you want to replace a demo vertical with a live supplier,
                                        the code lives in
                                        <code className="mx-1">platform/services/search-api/src/adapters/</code>
                                        and the active wiring is decided in
                                        <code className="mx-1">platform/services/search-api/src/adapters/registry.ts</code>.
                                    </p>
                                    <p>
                                        I also added
                                        <code className="mx-1">platform/INTEGRATIONS.md</code>
                                        so you have a plain-English reference inside the repo.
                                    </p>
                                </div>
                            </div>

                            <div className="rounded-3xl border border-ink-900/10 bg-ink-900 p-7 text-cream-100 shadow-subtle">
                                <p className="text-[0.68rem] uppercase tracking-[0.18em] text-cream-200/60">
                                    Booking.com approval plan
                                </p>
                                <h2 className="mt-3 font-display text-3xl tracking-tight text-balance">
                                    Content age is the one requirement you can&apos;t shortcut.
                                </h2>
                                <ul className="mt-6 space-y-3 text-cream-200/80 leading-relaxed">
                                    <li>• Keep the guides section growing weekly</li>
                                    <li>• Publish 2 original travel posts per week</li>
                                    <li>• Link naturally into flights and hotels pages</li>
                                    <li>• Keep legal/disclosure pages visible</li>
                                    <li>• Re-apply once the domain/site history is mature enough</li>
                                </ul>
                                <div className="mt-8 flex flex-wrap gap-3">
                                    <LinkButton href="/integrations/switchboard" variant="accent" size="sm">
                                        Open switchboard
                                    </LinkButton>
                                    <LinkButton href="/guides" variant="ghost" size="sm">
                                        Open guides
                                    </LinkButton>
                                    <LinkButton href="/affiliate-disclosure" variant="ghost" size="sm">
                                        Read disclosure
                                    </LinkButton>
                                </div>
                            </div>
                        </section>
                    </div>
                )}
            </section>
        </main>
    );
}

function CredentialCard({
    name,
    description,
    configured,
    checks,
    nextStep,
}: {
    name: string;
    description: string;
    configured: boolean;
    checks: Array<[label: string, ok: boolean]>;
    nextStep: string;
}) {
    return (
        <article className="rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle">
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                        Program
                    </p>
                    <h3 className="mt-3 font-display text-3xl tracking-tight text-ink-900">
                        {name}
                    </h3>
                </div>
                <span
                    className={`inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] ${
                        configured
                            ? "border-emerald-200 bg-emerald-50 text-emerald-800"
                            : "border-amber-200 bg-amber-50 text-amber-800"
                    }`}
                >
                    {configured ? <CheckCircle2 className="h-3.5 w-3.5" /> : <AlertCircle className="h-3.5 w-3.5" />}
                    {configured ? "Configured" : "Missing setup"}
                </span>
            </div>

            <p className="mt-5 text-ink-500 leading-relaxed">{description}</p>

            <div className="mt-6 space-y-3">
                {checks.map(([label, ok]) => (
                    <div
                        key={label}
                        className="flex items-center justify-between rounded-2xl bg-ink-900/[0.035] px-4 py-3"
                    >
                        <span className="text-sm font-medium text-ink-700">{label}</span>
                        <span
                            className={`text-xs font-semibold uppercase tracking-[0.12em] ${
                                ok ? "text-emerald-700" : "text-amber-700"
                            }`}
                        >
                            {ok ? "Present" : "Missing"}
                        </span>
                    </div>
                ))}
            </div>

            <p className="mt-6 border-t border-ink-900/10 pt-6 text-sm leading-relaxed text-ink-500">
                <strong className="text-ink-900">Next step:</strong> {nextStep}
            </p>
        </article>
    );
}