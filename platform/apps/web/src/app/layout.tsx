import type { Metadata } from "next";
import { Fraunces, Plus_Jakarta_Sans, JetBrains_Mono } from "next/font/google";
import { SiteHeader } from "@/components/layout/SiteHeader";
import { SiteFooter } from "@/components/layout/SiteFooter";
import {
    SITE_NAME,
    SITE_URL,
    SITE_DESCRIPTION,
    organizationJsonLd,
    websiteJsonLd,
    JsonLd,
} from "@/lib/seo";
import "./globals.css";

const display = Fraunces({
    subsets: ["latin"],
    variable: "--font-display",
    weight: ["400", "500", "600", "700"],
    display: "swap",
});
const sans = Plus_Jakarta_Sans({
    subsets: ["latin"],
    variable: "--font-sans",
    weight: ["400", "500", "600", "700", "800"],
    display: "swap",
});
const mono = JetBrains_Mono({
    subsets: ["latin"],
    variable: "--font-mono",
    weight: ["400", "500", "600", "700"],
    display: "swap",
});

export const metadata: Metadata = {
    metadataBase: new URL(SITE_URL),
    title: {
        default: `${SITE_NAME} — Compare Flights, Hotels & Travel Deals`,
        template: `%s | ${SITE_NAME}`,
    },
    description: SITE_DESCRIPTION,
    applicationName: SITE_NAME,
    keywords: [
        "cheap flights",
        "hotel deals",
        "car rentals",
        "travel activities",
        "compare travel prices",
        "flight search",
        "hotel search",
        "travel meta-search",
    ],
    openGraph: {
        type: "website",
        siteName: SITE_NAME,
        title: `${SITE_NAME} — Compare Flights, Hotels & Travel Deals`,
        description: SITE_DESCRIPTION,
        url: SITE_URL,
        locale: "en_US",
    },
    twitter: {
        card: "summary_large_image",
        title: SITE_NAME,
        description: SITE_DESCRIPTION,
    },
    robots: {
        index: true,
        follow: true,
    },
    alternates: {
        canonical: "/",
    },
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
    return (
        <html lang="en" className={`${display.variable} ${sans.variable} ${mono.variable} scroll-smooth`}>
            <body className="font-sans min-h-dvh flex flex-col bg-cream-100 text-ink-900">
                <JsonLd data={organizationJsonLd()} />
                <JsonLd data={websiteJsonLd()} />
                <SiteHeader />
                <div className="flex-1">{children}</div>
                <SiteFooter />
            </body>
        </html>
    );
}
