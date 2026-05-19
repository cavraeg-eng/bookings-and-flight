import type { Metadata } from "next";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "Terms & conditions",
    description:
        "Terms governing use of Bookings and Flights — what we are (a meta-search service, not a travel seller) and what that means for you.",
    alternates: { canonical: "/terms" },
};

export default function TermsPage() {
    return (
        <ContentPage
            eyebrow="Legal"
            title="Terms & conditions."
            lede="Short version: we compare prices from other travel sites, we don't sell travel ourselves, and your booking contract is with the supplier — not us."
            updated="April 2026"
        >
            <h2>1. What this service is</h2>
            <p>
                Bookings and Flights is a <strong>meta-search service</strong>. We aggregate public
                pricing and availability from third-party travel suppliers and display it in one
                interface. We are <strong>not</strong> a travel agent, tour operator, airline, hotel,
                or rental car company. We do not take reservations, collect payment, or issue
                tickets.
            </p>

            <h2>2. Your booking contract</h2>
            <p>
                When you click a <strong>Book</strong> button on our site, you leave our site and
                enter the supplier&apos;s own booking flow. Your contract for travel services is
                formed entirely with that supplier under <em>their</em> terms, privacy policy,
                cancellation rules and refund policy. We are not a party to that contract.
            </p>

            <h2>3. Accuracy of information</h2>
            <p>
                We make reasonable efforts to display accurate prices and availability as returned
                by our suppliers, but inventory changes in real time. Prices, fees, taxes, seat
                availability, cancellation terms and any other trip details shown on our site are
                indicative. The supplier&apos;s own checkout page is the authoritative source.
            </p>

            <h2>4. Prohibited use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>Scrape, crawl, or systematically download results from our service.</li>
                <li>Interfere with the operation of the site.</li>
                <li>Use the service for unlawful purposes or in breach of applicable sanctions.</li>
                <li>Attempt to circumvent rate limits or authentication.</li>
            </ul>

            <h2>5. Liability</h2>
            <p>
                We are not liable for the acts or omissions of any supplier. We are not liable for
                cancelled flights, overbookings, closed hotels, mechanical breakdowns, weather
                events, or any other aspect of your actual travel experience. Disputes about travel
                services must be raised directly with the supplier.
            </p>
            <p>
                To the maximum extent permitted by law, our total liability to you for any claim
                arising out of your use of the service is limited to USD 100.
            </p>

            <h2>6. Intellectual property</h2>
            <p>
                All content on this site — other than supplier-provided offer data — is © Bookings
                and Flights. You may view and share links to pages but may not copy, reproduce, or
                redistribute the content without permission.
            </p>

            <h2>7. Changes</h2>
            <p>
                We may update these terms from time to time. Material changes will be posted on this
                page at least 30 days before they take effect.
            </p>

            <h2>8. Governing law</h2>
            <p>
                These terms are governed by the laws of the jurisdiction in which Bookings and
                Flights is established, without regard to conflict-of-laws principles. Disputes will
                be resolved in the courts of that jurisdiction.
            </p>

            <h2>9. Contact</h2>
            <p>
                Legal notices:{" "}
                <a href="mailto:legal@bookingsandflights.example">
                    legal@bookingsandflights.example
                </a>
            </p>
        </ContentPage>
    );
}
