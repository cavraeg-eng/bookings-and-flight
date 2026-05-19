import type { Metadata } from "next";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Compare Cheap Flights — Search 500+ Airlines",
    description:
        "Compare flight prices across 500+ airlines and booking sites. Find the cheapest flights for any route and book direct with no hidden fees.",
    path: "/flights",
});

export default function FlightsLayout({ children }: { children: React.ReactNode }) {
    return children;
}
