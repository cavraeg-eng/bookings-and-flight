import type { Metadata } from "next";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Tours, Day Trips & Things to Do — Compare Activities",
    description:
        "Browse and compare tours, day trips, museum tickets, and unique experiences worldwide. Book directly from top activity providers.",
    path: "/activities",
});

export default function ActivitiesLayout({ children }: { children: React.ReactNode }) {
    return children;
}
