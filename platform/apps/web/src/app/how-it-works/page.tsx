import type { Metadata } from "next";
import Link from "next/link";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "How it works",
    description:
        "Search 500+ travel suppliers in one place, compare prices, and book direct with the supplier — zero booking fees, zero middlemen.",
    alternates: { canonical: "/how-it-works" },
};

export default function HowItWorksPage() {
    return (
        <ContentPage
            eyebrow="How it works"
            title="One search, every supplier, booked direct."
            lede="We query every major travel aggregator and supplier in parallel, normalize their results, and hand you off directly — we never hold your booking."
        >
            <h2>1. You search once</h2>
            <p>
                Tell us where and when you want to go. We fan out that query to every major flight,
                hotel, car-rental and activity supplier in the background — no tabs, no copy-paste,
                no 30 browser windows.
            </p>

            <h2>2. We compare in real time</h2>
            <p>
                Offers come back from different suppliers in different shapes. We normalize them —
                same currency, same date format, same fee disclosure — so you can compare{" "}
                <strong>apples to apples</strong>, not &quot;$149 (+hidden fees)&quot; vs
                &quot;$159 all-in.&quot;
            </p>

            <h2>3. You book direct with the supplier</h2>
            <p>
                Click <strong>Book</strong> and you&apos;re handed off to the supplier&apos;s own
                website — the airline, the hotel chain, Booking.com, Expedia, Viator, etc. Your
                reservation lives with them, your card is charged by them, your cancellation rules
                come from them.
            </p>
            <p>
                That matters because when you need to change a flight or dispute a charge, you talk
                directly to the company that can actually help — not a reseller middleman who has to
                forward your email.
            </p>

            <h2>4. We earn a small commission — from the supplier, not you</h2>
            <p>
                When you book through one of our links, the supplier pays us an affiliate
                commission. You pay the same price you&apos;d pay going direct. We disclose every
                partner we work with on our{" "}
                <a href="/affiliate-disclosure">affiliate disclosure page</a>.
            </p>

            <h2>What we don&apos;t do</h2>
            <ul>
                <li>We don&apos;t hold your money or your booking.</li>
                <li>We don&apos;t charge booking fees, service fees, or &quot;convenience&quot; fees.</li>
                <li>We don&apos;t sell your email to marketers.</li>
                <li>We don&apos;t rank sponsored results above honest ones — cheapest is cheapest.</li>
            </ul>

            <h2>Ready to try it?</h2>
            <p>
                Start with <Link href="/flights">flights</Link>,{" "}
                <Link href="/hotels">hotels</Link>, <Link href="/cars">car rentals</Link>, or{" "}
                <Link href="/activities">things to do</Link>.
            </p>
        </ContentPage>
    );
}
