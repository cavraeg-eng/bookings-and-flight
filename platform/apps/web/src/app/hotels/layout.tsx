import type { Metadata } from "next";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Compare Hotel Deals — Best Rates Across Top Booking Sites",
    description:
        "Search and compare hotel prices across Booking.com, Expedia, Hotels.com and more. Find the best rates for any destination — no booking fees.",
    path: "/hotels",
});

export default function HotelsLayout({ children }: { children: React.ReactNode }) {
    return children;
}
