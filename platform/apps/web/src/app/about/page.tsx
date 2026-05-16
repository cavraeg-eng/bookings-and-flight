import type { Metadata } from "next";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "About us",
    description:
        "Bookings and Flights is a meta-search service comparing flights, hotels, cars and activities across every major supplier — zero booking fees, zero middlemen.",
    alternates: { canonical: "/about" },
};

export default function AboutPage() {
    return (
        <ContentPage
            eyebrow="Who we are"
            title="We don't sell travel. We find it, compare it, and send you to the best price."
            lede="Bookings and Flights is an independent meta-search site built by travelers who got tired of hidden fees, dark patterns, and suppliers holding your booking hostage."
        >
            <h2>The problem we&apos;re solving</h2>
            <p>
                Most travel sites today are resellers. They sit between you and the airline, hotel,
                or car-rental supplier, add a markup, charge a &quot;service fee,&quot; bury the
                real cancellation rules, and lock your booking in their own account so you can&apos;t
                easily change it with the supplier directly.
            </p>
            <p>
                That&apos;s the opposite of what the internet was supposed to do for travel. Pricing
                should be transparent. Cancellation rules should come from the airline, not a layer on
                top of it. And your booking should live with the company actually flying the plane or
                handing over the keys.
            </p>

            <h2>What we actually do</h2>
            <p>
                We compare live inventory across every major supplier and meta-search aggregator —
                Expedia, Booking.com, Skyscanner, Kayak, Hotels.com, Viator, Rentalcars, Hertz, Avis,
                Google Flights, and dozens more — and surface the best price for your trip. When you
                click <strong>Book</strong>, we hand you off directly to the supplier&apos;s own
                checkout. No reseller layer. No ghost-fees.
            </p>

            <h2>How we make money</h2>
            <p>
                When you book through one of our partner links, the supplier pays us a small
                affiliate commission. That commission comes out of their margin — it does not
                increase the price you pay. You&apos;ll never pay more by starting your search here
                than you would going direct.
            </p>
            <p>
                See our full <a href="/affiliate-disclosure">affiliate disclosure</a> for details.
            </p>

            <h2>Who&apos;s behind it</h2>
            <p>
                A small, independent team of developers and travelers. No VC money telling us to
                stuff sponsored results above honest ones. No upsells, no newsletter-only prices, no
                &quot;members save more&quot; gimmicks. Just the best public price we can find,
                delivered fast.
            </p>

            <h2>Get in touch</h2>
            <p>
                Questions, partnership inquiries, or a route we should cover better? Email{" "}
                <a href="mailto:hello@bookingsandflights.example">hello@bookingsandflights.example</a>.
            </p>
        </ContentPage>
    );
}
