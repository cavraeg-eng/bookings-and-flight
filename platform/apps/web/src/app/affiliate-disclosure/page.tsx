import type { Metadata } from "next";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "Affiliate disclosure",
    description:
        "How Bookings and Flights earns money — full transparency on our partner network, affiliate commissions, and what it does (and doesn't) cost you.",
    alternates: { canonical: "/affiliate-disclosure" },
    robots: { index: true, follow: true },
};

export default function AffiliateDisclosurePage() {
    return (
        <ContentPage
            eyebrow="Required reading"
            title="Affiliate disclosure."
            lede="We believe transparency is a feature, not a footnote. Here's exactly how we make money and what that means for the price you pay."
            updated="April 2026"
        >
            <h2>The short version</h2>
            <p>
                Bookings and Flights participates in several affiliate marketing programs. When you
                click a link on our site and complete a booking with one of our partners, they pay
                us a small commission. <strong>This commission comes out of their margin and does
                not increase the price you pay.</strong>
            </p>

            <h2>Who our partners are</h2>
            <p>
                We work with — and may earn commissions from — the following partners and networks:
            </p>
            <ul>
                <li>
                    <strong>Travelpayouts / Trip.com</strong> — flight and hotel custom links plus
                    white-label search widgets
                </li>
                <li><strong>Booking.com Affiliate Partner Programme</strong> — hotels, apartments</li>
                <li><strong>Expedia Group Affiliate Program</strong> — flights, hotels, packages</li>
                <li><strong>Hotels.com Affiliate Network</strong> — hotel inventory</li>
                <li><strong>Agoda Partners</strong> — Asia-Pacific hotel inventory</li>
                <li><strong>Viator / Tripadvisor Experiences</strong> — tours and activities</li>
                <li><strong>GetYourGuide Partner Program</strong> — tours and activities</li>
                <li><strong>DiscoverCars</strong> / <strong>Rentalcars.com</strong> — car rentals</li>
                <li><strong>Hertz, Avis, Budget, Sixt, Enterprise</strong> — direct car-rental programs</li>
                <li><strong>Skyscanner Partner API</strong> — price comparison inventory</li>
            </ul>
            <p>
                We may add or remove partners over time. We will always display the supplier&apos;s
                own brand on the offer card, so you know exactly who you&apos;re booking with before
                you click.
            </p>

            <h2>What this means for you</h2>
            <ul>
                <li>
                    <strong>Price is identical.</strong> You pay the supplier&apos;s listed rate —
                    never a markup from us.
                </li>
                <li>
                    <strong>Your booking lives with the supplier.</strong> Cancellations, refunds,
                    changes, support — all handled by them, not by us.
                </li>
                <li>
                    <strong>We rank by value, not by commission.</strong> We don&apos;t hide cheaper
                    non-partner options to push you to higher-commission partners. Cheapest first,
                    always.
                </li>
            </ul>

            <h2>FTC / ASA disclosure</h2>
            <p>
                In compliance with the US Federal Trade Commission&apos;s 16 CFR Part 255 (&quot;Guides
                Concerning the Use of Endorsements and Testimonials in Advertising&quot;), the UK
                Advertising Standards Authority (ASA), and the EU Unfair Commercial Practices
                Directive: outbound booking links on this site are paid-commission partnerships.
            </p>

            <h2>Questions</h2>
            <p>
                Email us at{" "}
                <a href="mailto:disclosure@bookingsandflights.example">
                    disclosure@bookingsandflights.example
                </a>{" "}
                if you&apos;d like more detail on any specific partnership.
            </p>
        </ContentPage>
    );
}
