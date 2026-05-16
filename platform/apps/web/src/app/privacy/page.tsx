import type { Metadata } from "next";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "Privacy policy",
    description:
        "How Bookings and Flights collects, uses and protects your personal data — GDPR, CCPA and general data-protection practices.",
    alternates: { canonical: "/privacy" },
};

export default function PrivacyPage() {
    return (
        <ContentPage
            eyebrow="Legal"
            title="Privacy policy."
            lede="Plain-English summary: we collect the minimum data needed to run the service, we never sell it, and you can ask us to delete it anytime."
            updated="April 2026"
        >
            <h2>What we collect</h2>
            <ul>
                <li>
                    <strong>Search parameters</strong> — the origin, destination, dates and
                    traveler count you enter. These are used to fetch offers and are not tied to
                    your identity unless you&apos;re signed in.
                </li>
                <li>
                    <strong>Basic analytics</strong> — aggregated, anonymous page views and
                    click-through data so we can improve ranking and UX.
                </li>
                <li>
                    <strong>Account data (optional)</strong> — if you create an account, your email
                    address and any preferences you choose to save.
                </li>
                <li>
                    <strong>Cookies</strong> — strictly functional cookies and, with your consent,
                    analytics cookies. See the cookie banner.
                </li>
            </ul>

            <h2>What we don&apos;t collect</h2>
            <ul>
                <li>Payment card information. Bookings happen on the supplier&apos;s own website.</li>
                <li>Passport numbers, travel documents, or government IDs.</li>
                <li>Location data beyond the approximate region derived from your IP.</li>
            </ul>

            <h2>How we use data</h2>
            <p>We use the data we collect to:</p>
            <ul>
                <li>Return search results from our supplier partners.</li>
                <li>Improve ranking, speed, and relevance of results.</li>
                <li>Detect fraud and abuse.</li>
                <li>Comply with legal obligations.</li>
            </ul>

            <h2>Third parties</h2>
            <p>
                We share the minimum data required with supplier partners to fulfill your search
                (e.g., your search parameters, not your email). We use industry-standard processors
                for hosting, analytics and email. A full list is available on request.
            </p>

            <h2>Your rights (GDPR, CCPA, UK DPA)</h2>
            <ul>
                <li>Right to access a copy of your data.</li>
                <li>Right to correction and deletion.</li>
                <li>Right to data portability.</li>
                <li>Right to object to processing and to withdraw consent.</li>
            </ul>
            <p>
                To exercise any of these, email{" "}
                <a href="mailto:privacy@bookingsandflights.example">
                    privacy@bookingsandflights.example
                </a>
                . We respond within 30 days.
            </p>

            <h2>Data retention</h2>
            <p>
                Search history and analytics logs are retained for 90 days, then anonymized or
                deleted. Account data is retained until you delete your account.
            </p>

            <h2>Changes to this policy</h2>
            <p>
                We&apos;ll post any material changes on this page and, if you have an account,
                notify you by email at least 30 days before they take effect.
            </p>

            <h2>Contact</h2>
            <p>
                Data protection officer:{" "}
                <a href="mailto:privacy@bookingsandflights.example">
                    privacy@bookingsandflights.example
                </a>
            </p>
        </ContentPage>
    );
}
