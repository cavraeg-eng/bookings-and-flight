import { cn } from "@/lib/cn";
import { Search, BadgeCheck, ExternalLink } from "lucide-react";
import type { ElementType } from "react";

type ValueProp = {
    icon: ElementType;
    title: string;
    description: string;
};

const VALUE_PROPS: ValueProp[] = [
    {
        icon: Search,
        title: "Compare 500+ Sites",
        description:
            "We search across hundreds of travel sites to find you the best deals — so you don't have to.",
    },
    {
        icon: BadgeCheck,
        title: "No Hidden Fees",
        description:
            "The price you see is the price you pay. Always. No surprises at checkout.",
    },
    {
        icon: ExternalLink,
        title: "Book Direct",
        description:
            "We connect you directly with airlines and hotels for the best rates and full support.",
    },
];

export function WhyChooseUs({ className }: { className?: string }) {
    return (
        <section className={cn("py-0", className)}>
            <div className="container">
                {/* Section header */}
                <div className="mb-12 text-center">
                    <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-ink-400">
                        The Bookings & Flights Difference
                    </p>
                    <h2 className="font-display text-display-md font-bold text-ink-900">
                        Why Book With Us
                    </h2>
                </div>

                {/* Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    {VALUE_PROPS.map((prop) => {
                        const Icon = prop.icon;
                        return (
                            <div
                                key={prop.title}
                                className="flex flex-col items-center text-center rounded-card border border-cream-200/60 border-l-4 border-l-amber-400 bg-gradient-to-br from-white to-cream-50 p-8 shadow-subtle transition-all duration-300 hover:-translate-y-1 hover:shadow-card"
                            >
                                <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 mb-5 shadow-subtle">
                                    <Icon className="h-7 w-7" />
                                </div>
                                <h3 className="text-base font-bold text-ink-900 mb-2">
                                    {prop.title}
                                </h3>
                                <p className="text-sm leading-relaxed text-ink-500">
                                    {prop.description}
                                </p>
                            </div>
                        );
                    })}
                </div>
            </div>
        </section>
    );
}
