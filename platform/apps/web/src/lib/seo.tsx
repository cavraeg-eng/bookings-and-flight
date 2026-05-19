import type { Metadata } from "next";

/* ------------------------------------------------------------------ */
/*  Site-wide constants                                                */
/* ------------------------------------------------------------------ */

export const SITE_NAME = "Bookings & Flights";
export const SITE_URL =
    process.env.NEXT_PUBLIC_SITE_URL ?? "https://bookingsandflights.com";
export const SITE_DESCRIPTION =
    "Compare flights, hotels, car rentals and activities across 500+ travel sites. Find the best deals and book direct.";

/* ------------------------------------------------------------------ */
/*  JSON-LD helpers                                                    */
/* ------------------------------------------------------------------ */

/** Organization structured data */
export function organizationJsonLd(): object {
    return {
        "@context": "https://schema.org",
        "@type": "Organization",
        name: SITE_NAME,
        url: SITE_URL,
        logo: `${SITE_URL}/logo.png`,
        sameAs: [],
        description: SITE_DESCRIPTION,
    };
}

/** WebSite structured data with SearchAction for sitelinks search box */
export function websiteJsonLd(): object {
    return {
        "@context": "https://schema.org",
        "@type": "WebSite",
        name: SITE_NAME,
        url: SITE_URL,
        description: SITE_DESCRIPTION,
        potentialAction: {
            "@type": "SearchAction",
            target: {
                "@type": "EntryPoint",
                urlTemplate: `${SITE_URL}/flights?origin={origin}&destination={destination}`,
            },
            "query-input": "required name=destination",
        },
    };
}

/** FAQPage JSON-LD from array of Q&A pairs */
export function faqJsonLd(
    faqs: { question: string; answer: string }[],
): object {
    return {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        mainEntity: faqs.map((faq) => ({
            "@type": "Question",
            name: faq.question,
            acceptedAnswer: {
                "@type": "Answer",
                text: faq.answer,
            },
        })),
    };
}

/** BreadcrumbList JSON-LD from ordered name/url pairs */
export function breadcrumbJsonLd(
    items: { name: string; url: string }[],
): object {
    return {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        itemListElement: items.map((item, index) => ({
            "@type": "ListItem",
            position: index + 1,
            name: item.name,
            item: item.url.startsWith("http") ? item.url : `${SITE_URL}${item.url}`,
        })),
    };
}

/* ------------------------------------------------------------------ */
/*  JSON-LD React component                                            */
/* ------------------------------------------------------------------ */

/**
 * Render structured data as a <script type="application/ld+json"> tag.
 * Safe to include in server components.
 */
export function JsonLd({ data }: { data: object }) {
    return (
        <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
        />
    );
}

/* ------------------------------------------------------------------ */
/*  Page-level metadata helper                                         */
/* ------------------------------------------------------------------ */

interface PageMetaOpts {
    title: string;
    description: string;
    path: string;
    ogImage?: string;
}

/**
 * Generate a Next.js `Metadata` object for an individual page.
 * The root layout provides the title template, so `title` here is the
 * page-specific segment (e.g. "Cheap Flights to London").
 */
export function generatePageMeta({
    title,
    description,
    path,
    ogImage,
}: PageMetaOpts): Metadata {
    const url = path.startsWith("/") ? path : `/${path}`;
    const image = ogImage ?? `${SITE_URL}/og-default.png`;

    return {
        title,
        description,
        alternates: { canonical: url },
        openGraph: {
            title,
            description,
            url,
            siteName: SITE_NAME,
            locale: "en_US",
            type: "website",
            images: [{ url: image, width: 1200, height: 630, alt: title }],
        },
        twitter: {
            card: "summary_large_image",
            title,
            description,
            images: [image],
        },
    };
}
