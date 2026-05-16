import type { Metadata } from "next";
import { CheckCircle2, CircleDashed, ExternalLink, FileCode2, KeyRound, Wrench } from "lucide-react";
import { LinkButton } from "@/components/ui/Button";

export const metadata: Metadata = {
    title: "Program switchboard",
    description:
        "Operational switchboard for affiliate programs: env keys, adapter files, current status, and exact next steps to activate each supplier.",
    alternates: { canonical: "/integrations/switchboard" },
    robots: { index: false, follow: false },
};

type Vertical = "flights" | "hotels" | "cars" | "activities";

type IntegrationsPayload = {
    apiBase: string;
    activeByVertical: Record<Vertical, string[]>;
    credentials: {
        travelpayouts: { configured: boolean; tokenPresent: boolean; markerPresent: boolean };
        booking: {
            configured: boolean;
            affiliateIdPresent: boolean;
            apiTokenPresent: boolean;
            sandbox: boolean;
        };
        viator: { configured: boolean; apiKeyPresent: boolean; partnerIdPresent: boolean };
        discovercars: { configured: boolean; partnerIdPresent: boolean };
        kiwi: { configured: boolean; affiliateIdPresent: boolean };
    };
};

type ProgramSpec = {
    slug: "travelpayouts" | "booking" | "discovercars" | "viator" | "kiwi";
    name: string;
    vertical: Vertical;
    currentAdapter: string;
    replacementTarget?: string;
    adapterFile: string;
    registryFile: string;
    envKeys: string[];
    mode: "live" | "pending";
    notes: string;
    nextAction: string;
};

const SPECS: ProgramSpec[] = [
    {
        slug: "travelpayouts",
        name: "Travelpayouts / Aviasales",
        vertical: "flights",
        currentAdapter: "travelpayouts",
        adapterFile: "platform/services/search-api/src/adapters/travelpayouts.ts",
        registryFile: "platform/services/search-api/src/adapters/registry.ts",
        envKeys: ["TRAVELPAYOUTS_API_TOKEN", "TRAVELPAYOUTS_MARKER"],
        mode: "live",
        notes: "This is the current live flight integration.",
        nextAction: "Keep this as the primary live flights adapter unless you want to add Kiwi as a second source.",
    },
    {
        slug: "booking",
        name: "Booking.com",
        vertical: "hotels",
        currentAdapter: "booking-demand",
        replacementTarget: "booking-demand",
        adapterFile: "platform/services/search-api/src/adapters/booking-demand.ts",
        registryFile: "platform/services/search-api/src/adapters/registry.ts",
        envKeys: ["BOOKING_AFFILIATE_ID", "BOOKING_API_TOKEN", "BOOKING_USE_SANDBOX"],
        mode: "pending",
        notes: "The public hotels page now uses Trip.com white-label/custom-link search on the frontend. Booking Demand remains optional if you want a separate backend hotel feed later.",
        nextAction: "Only add Booking credentials if you want direct backend hotel inventory in addition to the frontend Trip.com flow.",
    },
    {
        slug: "discovercars",
        name: "DiscoverCars",
        vertical: "cars",
        currentAdapter: "not configured",
        replacementTarget: "discovercars-live (to be implemented)",
        adapterFile: "platform/services/search-api/src/adapters/discovercars.ts",
        registryFile: "platform/services/search-api/src/adapters/registry.ts",
        envKeys: ["DISCOVERCARS_PARTNER_ID"],
        mode: "pending",
        notes: "The demo car adapter has been removed. Cars stay offline until a real partner feed is implemented.",
        nextAction: "Add the partner ID, implement `discovercars.ts`, and register it in the adapter registry.",
    },
    {
        slug: "viator",
        name: "Viator",
        vertical: "activities",
        currentAdapter: "not configured",
        replacementTarget: "viator-live (to be implemented)",
        adapterFile: "platform/services/search-api/src/adapters/viator.ts",
        registryFile: "platform/services/search-api/src/adapters/registry.ts",
        envKeys: ["VIATOR_API_KEY", "VIATOR_PARTNER_ID"],
        mode: "pending",
        notes: "The demo activities adapter has been removed. Activities stay offline until the live tours feed is connected.",
        nextAction: "Once both keys are present, implement `viator.ts` and add it to the adapter registry.",
    },
    {
        slug: "kiwi",
        name: "Kiwi.com",
        vertical: "flights",
        currentAdapter: "travelpayouts",
        replacementTarget: "kiwi-live (secondary flights adapter)",
        adapterFile: "platform/services/search-api/src/adapters/kiwi.ts",
        registryFile: "platform/services/search-api/src/adapters/registry.ts",
        envKeys: ["KIWI_AFFILIATE_ID"],
        mode: "pending",
        notes: "Kiwi is not currently active. It would be an additional flights source, not a replacement for the whole flights system by default.",
        nextAction: "If you want Kiwi, add the affiliate ID and implement a dedicated flights adapter alongside Travelpayouts.",
    },
];

function getApiBase(): string {
    return process.env.NEXT_PUBLIC_API_BASE ?? "http://localhost:4050";
}

async function getIntegrations(): Promise<IntegrationsPayload | null> {
    const apiBase = getApiBase();

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

export default async function ProgramSwitchboardPage() {
    const data = await getIntegrations();
    const rawBackendStatusUrl = `${data?.apiBase ?? getApiBase()}/integrations`;

    return (
        <main className="bg-cream-100">
            <section className="border-b border-ink-900/5 bg-gradient-to-b from-sky-50 to-cream-100">
                <div className="container py-20 lg:py-24">
                    <p className="eyebrow mb-5">
                        <span />
                        Supplier operations
                    </p>
                    <h1 className="max-w-4xl font-display text-display-lg text-ink-900 tracking-tight text-balance">
                        Program switchboard: exactly what to edit when you want to turn a supplier on.
                    </h1>
                    <p className="mt-6 max-w-3xl text-xl text-ink-500 leading-relaxed text-balance">
                        This page is meant for operations, not marketing. It shows the env keys,
                        current adapter, replacement target, and file paths you’ll touch when you
                        add Booking.com, DiscoverCars, Viator, or Kiwi.
                    </p>
                    <div className="mt-8 flex flex-wrap gap-3">
                        <LinkButton href="/integrations" variant="ghost" size="sm">
                            Back to dashboard
                        </LinkButton>
                        <LinkButton
                            href={rawBackendStatusUrl}
                            target="_blank"
                            rel="noreferrer"
                            variant="accent"
                            size="sm"
                        >
                            Open raw backend status
                            <ExternalLink className="h-4 w-4" />
                        </LinkButton>
                    </div>
                </div>
            </section>

            <section className="container py-14 lg:py-20">
                {!data ? (
                    <div className="rounded-3xl border border-red-200 bg-red-50 p-7 text-red-900 shadow-subtle">
                        The backend status endpoint is unavailable. Start `npm run dev:api` from
                        `platform/` and reload this page.
                    </div>
                ) : (
                    <div className="grid gap-6">
                        {SPECS.map((spec) => (
                            <ProgramCard key={spec.slug} spec={spec} data={data} />
                        ))}
                    </div>
                )}
            </section>
        </main>
    );
}

function ProgramCard({
    spec,
    data,
}: {
    spec: ProgramSpec;
    data: IntegrationsPayload;
}) {
    const credentialChecks = getCredentialChecks(spec.slug, data);
    const activeAdapters = data.activeByVertical[spec.vertical];
    const liveOnVertical = activeAdapters.includes(spec.currentAdapter);

    return (
        <article className="rounded-3xl border border-ink-900/10 bg-white p-7 shadow-subtle">
            <div className="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <div className="flex flex-wrap items-center gap-3">
                        <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                            {spec.vertical}
                        </p>
                        <ProgramStateBadge
                            mode={spec.mode}
                            active={liveOnVertical}
                            configured={credentialChecks.every(([, ok]) => ok)}
                        />
                    </div>
                    <h2 className="mt-3 font-display text-4xl tracking-tight text-ink-900 text-balance">
                        {spec.name}
                    </h2>
                    <p className="mt-4 text-ink-500 leading-relaxed">{spec.notes}</p>

                    <div className="mt-6 grid gap-4 sm:grid-cols-2">
                        <InfoBox
                            icon={<Wrench className="h-4 w-4" />}
                            label="Current adapter"
                            value={spec.currentAdapter}
                        />
                        <InfoBox
                            icon={<CircleDashed className="h-4 w-4" />}
                            label="Replacement target"
                            value={spec.replacementTarget ?? "No change needed right now"}
                        />
                        <InfoBox
                            icon={<FileCode2 className="h-4 w-4" />}
                            label="Adapter file"
                            value={spec.adapterFile}
                        />
                        <InfoBox
                            icon={<FileCode2 className="h-4 w-4" />}
                            label="Registry file"
                            value={spec.registryFile}
                        />
                    </div>
                </div>

                <div className="rounded-3xl bg-ink-900/[0.03] p-6">
                    <div className="flex items-center gap-2 text-ink-900">
                        <KeyRound className="h-4 w-4 text-amber-600" />
                        <h3 className="font-display text-2xl tracking-tight">Switch checklist</h3>
                    </div>

                    <div className="mt-5 space-y-3">
                        {credentialChecks.map(([key, ok]) => (
                            <div
                                key={key}
                                className="flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-subtle"
                            >
                                <span className="font-mono text-sm text-ink-800">{key}</span>
                                <span
                                    className={`inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] ${
                                        ok ? "text-emerald-700" : "text-amber-700"
                                    }`}
                                >
                                    {ok ? <CheckCircle2 className="h-3.5 w-3.5" /> : <CircleDashed className="h-3.5 w-3.5" />}
                                    {ok ? "Present" : "Missing"}
                                </span>
                            </div>
                        ))}
                    </div>

                    <div className="mt-6 rounded-2xl border border-ink-900/10 bg-white p-4">
                        <p className="text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                            Exact next action
                        </p>
                        <p className="mt-3 text-sm leading-relaxed text-ink-600">{spec.nextAction}</p>
                    </div>
                </div>
            </div>
        </article>
    );
}

function ProgramStateBadge({
    mode,
    active,
    configured,
}: {
    mode: ProgramSpec["mode"];
    active: boolean;
    configured: boolean;
}) {
    const tone =
        mode === "live"
            ? "border-emerald-200 bg-emerald-50 text-emerald-800"
            : active
              ? "border-sky-200 bg-sky-50 text-sky-800"
              : configured
              ? "border-sky-200 bg-sky-50 text-sky-800"
              : "border-amber-200 bg-amber-50 text-amber-800";

    const label =
        mode === "live"
            ? "Live"
            : active
              ? "Active"
              : configured
                ? "Ready to wire"
                : "Pending";

    return (
        <span className={`inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] ${tone}`}>
            {label}
        </span>
    );
}

function InfoBox({
    icon,
    label,
    value,
}: {
    icon: React.ReactNode;
    label: string;
    value: string;
}) {
    return (
        <div className="rounded-2xl border border-ink-900/10 bg-white p-4">
            <p className="flex items-center gap-2 text-[0.68rem] uppercase tracking-[0.18em] text-ink-400">
                {icon}
                {label}
            </p>
            <p className="mt-3 break-all font-mono text-sm text-ink-800">{value}</p>
        </div>
    );
}

function getCredentialChecks(
    slug: ProgramSpec["slug"],
    data: IntegrationsPayload,
): Array<[string, boolean]> {
    switch (slug) {
        case "travelpayouts":
            return [
                ["TRAVELPAYOUTS_API_TOKEN", data.credentials.travelpayouts.tokenPresent],
                ["TRAVELPAYOUTS_MARKER", data.credentials.travelpayouts.markerPresent],
            ];
        case "booking":
            return [
                ["BOOKING_AFFILIATE_ID", data.credentials.booking.affiliateIdPresent],
                ["BOOKING_API_TOKEN", data.credentials.booking.apiTokenPresent],
                ["BOOKING_USE_SANDBOX", true],
            ];
        case "discovercars":
            return [["DISCOVERCARS_PARTNER_ID", data.credentials.discovercars.partnerIdPresent]];
        case "viator":
            return [
                ["VIATOR_API_KEY", data.credentials.viator.apiKeyPresent],
                ["VIATOR_PARTNER_ID", data.credentials.viator.partnerIdPresent],
            ];
        case "kiwi":
            return [["KIWI_AFFILIATE_ID", data.credentials.kiwi.affiliateIdPresent]];
    }
}
