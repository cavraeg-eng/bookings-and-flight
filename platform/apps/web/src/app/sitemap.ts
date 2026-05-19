import type { MetadataRoute } from "next";
import { SITE_URL } from "@/lib/seo";

export default function sitemap(): MetadataRoute.Sitemap {
    const now = new Date();
    const routes: Array<{ path: string; priority: number; changeFrequency: MetadataRoute.Sitemap[number]["changeFrequency"] }> = [
        { path: "/", priority: 1.0, changeFrequency: "daily" },
        { path: "/flights", priority: 0.9, changeFrequency: "daily" },
        { path: "/hotels", priority: 0.9, changeFrequency: "daily" },
        { path: "/cars", priority: 0.8, changeFrequency: "daily" },
        { path: "/activities", priority: 0.8, changeFrequency: "daily" },
        { path: "/deals", priority: 0.9, changeFrequency: "daily" },
        { path: "/guides", priority: 0.7, changeFrequency: "weekly" },
        { path: "/guides/best-time-to-book-flights", priority: 0.6, changeFrequency: "monthly" },
        { path: "/guides/how-to-avoid-hidden-hotel-fees", priority: 0.6, changeFrequency: "monthly" },
        { path: "/guides/first-trip-to-japan", priority: 0.6, changeFrequency: "monthly" },
        { path: "/integrations", priority: 0.2, changeFrequency: "weekly" },
        { path: "/integrations/switchboard", priority: 0.2, changeFrequency: "weekly" },
        { path: "/how-it-works", priority: 0.6, changeFrequency: "monthly" },
        { path: "/about", priority: 0.5, changeFrequency: "monthly" },
        { path: "/affiliate-disclosure", priority: 0.4, changeFrequency: "yearly" },
        { path: "/privacy", priority: 0.3, changeFrequency: "yearly" },
        { path: "/terms", priority: 0.3, changeFrequency: "yearly" },
    ];

    return routes.map((r) => ({
        url: `${SITE_URL}${r.path}`,
        lastModified: now,
        changeFrequency: r.changeFrequency,
        priority: r.priority,
    }));
}
