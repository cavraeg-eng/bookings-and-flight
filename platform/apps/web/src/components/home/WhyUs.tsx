import { Section } from "@/components/ui/Section";
import { Shield, Zap, HeartHandshake, Compass } from "lucide-react";

const POINTS = [
    {
        Icon: Zap,
        title: "No demo cards",
        copy: "The fake fare boards are gone. Flights and hotels now point into live Trip.com search flows instead of placeholder inventory.",
    },
    {
        Icon: Shield,
        title: "Same branded shell",
        copy: "The white-label search experience now sits inside the same header, spacing, and visual language as the rest of the site.",
    },
    {
        Icon: Compass,
        title: "Tracked custom links",
        copy: "Every live CTA runs through Travelpayouts so Trip.com clicks stay attributed without relying on a separate demo offer layer.",
    },
    {
        Icon: HeartHandshake,
        title: "Next partners, not placeholders",
        copy: "Cars and activities stay offline until their real partner programs are ready. Nothing goes back live with mock inventory.",
    },
];

export function WhyUs() {
    return (
        <Section
            eyebrow="Why meta-search wins"
            title={
                <>
                    The search flow is now{" "}
                    <em className="italic font-normal text-ink-500">real.</em>
                </>
            }
            description="This pass replaces placeholder search results with live Trip.com widgets and Travelpayouts custom links, while keeping the site shell visually consistent."
        >
            <div className="grid gap-px bg-ink-900/10 rounded-3xl overflow-hidden border border-ink-900/10 sm:grid-cols-2 lg:grid-cols-4">
                {POINTS.map((p) => (
                    <div
                        key={p.title}
                        className="bg-cream-50 p-7 sm:p-9 flex flex-col gap-3 transition-colors hover:bg-white"
                    >
                        <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-ink-900 text-amber-400 mb-1">
                            <p.Icon className="h-5 w-5" strokeWidth={2.25} />
                        </span>
                        <h3 className="font-display text-xl sm:text-2xl text-ink-900 tracking-tight">
                            {p.title}
                        </h3>
                        <p className="text-ink-500 leading-relaxed text-[0.95rem]">
                            {p.copy}
                        </p>
                    </div>
                ))}
            </div>
        </Section>
    );
}
