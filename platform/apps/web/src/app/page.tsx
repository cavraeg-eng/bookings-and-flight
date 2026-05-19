import type { Metadata } from "next";
import {
    HeroSection,
    TrendingDeals,
    PopularDestinations,
    WhyChooseUs,
    GuidesTeaser,
} from "@/components/home";
import { PartnerLogos, TrustBadges, SocialProof } from "@/components/trust";
import { generatePageMeta } from "@/lib/seo";

export const metadata: Metadata = generatePageMeta({
    title: "Compare Flights, Hotels, Cars & Activities",
    description:
        "Compare flights, hotels, car rentals and activities across 500+ travel sites. Find the best deals and book direct — zero fees, zero middlemen.",
    path: "/",
});

export default function HomePage() {
    return (
        <main>
            {/* 1. Hero with search form */}
            <HeroSection />

            {/* 2. Partner logos marquee */}
            <section aria-label="Trusted travel partners" className="bg-cream-100 border-t border-ink-100/40">
                <PartnerLogos />
            </section>

            {/* 3. Trending deals */}
            <section aria-label="Trending flight deals" className="bg-white py-12 lg:py-16">
                <TrendingDeals />
            </section>

            {/* 4. Popular destinations */}
            <section aria-label="Popular destinations" className="bg-cream-50 py-12 lg:py-16 border-t border-ink-100/30">
                <PopularDestinations />
            </section>

            {/* 5. Trust badges */}
            <section aria-label="Trust and credibility" className="py-8 lg:py-12 bg-white border-t border-ink-100/30">
                <div className="container">
                    <TrustBadges />
                </div>
            </section>

            {/* 6. Why choose us */}
            <section aria-label="Why book with us" className="bg-ink-50 py-12 lg:py-16 border-t border-ink-100/30">
                <WhyChooseUs />
            </section>

            {/* 7. Travel guides teaser */}
            <section aria-label="Travel guides and tips" className="bg-white py-12 lg:py-16">
                <GuidesTeaser />
            </section>

            {/* 8. Social proof / testimonials */}
            <section aria-label="Customer testimonials" className="bg-cream-50 py-12 lg:py-16 border-t border-ink-100/30">
                <SocialProof />
            </section>
        </main>
    );
}
