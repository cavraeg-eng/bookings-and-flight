import type { Metadata } from "next";
import Link from "next/link";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "How to avoid hidden hotel fees",
    description:
        "An original traveler checklist for spotting resort fees, parking fees, prepaid restrictions, and other hotel pricing traps.",
    alternates: { canonical: "/guides/how-to-avoid-hidden-hotel-fees" },
};

export default function HiddenHotelFeesGuide() {
    return (
        <ContentPage
            eyebrow="Hotels"
            title="How to avoid hidden hotel fees before you click book."
            lede="Hotel pricing looks simple until checkout adds resort fees, parking, breakfast, taxes, prepaid restrictions, and cancellation penalties."
            updated="April 2026"
        >
            <h2>Always compare the final stay total</h2>
            <p>
                Nightly rate is not the price. The real number is the total stay cost once taxes,
                mandatory fees, and any unavoidable extras are included.
            </p>

            <h2>Watch for these common traps</h2>
            <ul>
                <li>Resort or destination fees not included in the headline rate</li>
                <li>Breakfast that sounds included but only applies to one guest</li>
                <li>Parking fees that make suburban hotels less attractive than city-center options</li>
                <li>Prepaid rates with no cancellation flexibility</li>
                <li>Room upgrades that hide bed-type or occupancy restrictions</li>
            </ul>

            <h2>Read the cancellation line before the gallery</h2>
            <p>
                Beautiful photos are designed to sell emotion. The cancellation line determines
                risk. If your dates may move, a slightly higher flexible rate can be the cheaper
                decision in real life.
            </p>

            <h2>Check neighborhood, not just star rating</h2>
            <p>
                A four-star hotel in the wrong area can cost more overall once transport time,
                taxis, and meals are factored in. The right three-star in the right neighborhood
                often wins.
            </p>

            <h2>Use meta-search to compare the same property across suppliers</h2>
            <p>
                Different partners can surface the same hotel with different inclusions and
                cancellation rules. Use our <Link href="/hotels">hotel search</Link> to compare,
                then book direct with the listing that gives you the best total value.
            </p>
        </ContentPage>
    );
}