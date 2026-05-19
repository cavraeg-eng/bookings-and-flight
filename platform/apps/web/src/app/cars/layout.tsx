import type { Metadata } from "next";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Compare Car Rentals — Best Prices From Top Suppliers",
    description:
        "Compare car rental prices from Hertz, Avis, Enterprise and more. Pick up at airports, train stations, or city locations worldwide.",
    path: "/cars",
});

export default function CarsLayout({ children }: { children: React.ReactNode }) {
    return children;
}
