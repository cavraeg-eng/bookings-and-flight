import type { Metadata } from "next";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Today's Best Travel Deals — Cheap Flights & Hotel Discounts",
    description:
        "Hand-picked flight and hotel deals updated daily. Flash sales, price drops, and exclusive discounts on flights to Europe, Asia, the Caribbean, and more.",
    path: "/deals",
});

export default function DealsLayout({ children }: { children: React.ReactNode }) {
    return children;
}
