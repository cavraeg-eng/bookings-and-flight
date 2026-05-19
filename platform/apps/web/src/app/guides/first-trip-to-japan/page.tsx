import type { Metadata } from "next";
import Link from "next/link";
import { ContentPage } from "@/components/content/ContentPage";

export const metadata: Metadata = {
    title: "First trip to Japan",
    description:
        "A practical first-timer guide to flights, airports, neighborhoods, and booking strategy for a first trip to Japan.",
    alternates: { canonical: "/guides/first-trip-to-japan" },
};

export default function FirstTripToJapanGuide() {
    return (
        <ContentPage
            eyebrow="Destination guide"
            title="First trip to Japan: flights, neighborhoods, and common mistakes to avoid."
            lede="Japan is one of the easiest countries in the world to travel well — once you make a few key decisions correctly at the start."
            updated="April 2026"
        >
            <h2>Choose the right airport</h2>
            <p>
                If you are heading into Tokyo itself, Haneda is usually the smoother arrival.
                Narita often has more long-haul options, but ground transfer is longer and can add
                cost and fatigue after a long flight.
            </p>

            <h2>Do not overpack the itinerary</h2>
            <p>
                First-time visitors often try to do Tokyo, Kyoto, Osaka, Hakone, Hiroshima, and a
                day trip or two in one week. That turns a good trip into a train schedule. Fewer
                bases usually means a better trip.
            </p>

            <h2>Book hotels by neighborhood, not by city name alone</h2>
            <p>
                In Tokyo, the difference between Shinjuku, Ueno, Ginza, and Asakusa changes your
                daily experience more than star level does. Pick based on transport convenience and
                the kind of evenings you want.
            </p>

            <h2>Flights: watch early, book before seasonal spikes</h2>
            <p>
                Japan sees demand spikes around cherry blossom season, Golden Week, summer travel,
                and autumn foliage. If you are traveling during one of those windows, start
                watching earlier than you would for an off-peak trip.
            </p>

            <h2>Build in one “nothing” afternoon</h2>
            <p>
                The most memorable travel days are often the least overplanned. Leave room for a
                neighborhood walk, a small museum, or the best meal of the trip that you did not
                plan in advance.
            </p>

            <h2>Use search tools for the expensive decisions</h2>
            <p>
                Compare routes on our <Link href="/flights?origin=JFK&destination=HND">Tokyo
                flights search</Link> and compare neighborhoods and rates on our{" "}
                <Link href="/hotels?destination=Tokyo">Tokyo hotels search</Link>.
            </p>
        </ContentPage>
    );
}