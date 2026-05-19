# Travelpayouts Active Program Opportunities

Last reviewed: 2026-05-14

Source reviewed: Travelpayouts project/source `47745` for `bookingsandflights.com`.

## Connected Programs

The Travelpayouts dashboard currently shows these programs as available for the source and useful for near-term monetization:

- Trip.com: hotel and flight search widgets for provider-owned availability and booking handoff.
- Aviasales: flight search, popular routes, pricing calendar, schedule, and map widgets.
- Kiwitaxi: airport transfer widgets, reviews, and White Label transfer surfaces.
- Economybookings.com: rental car banner and search form widgets.
- TicketNetwork: event schedule and ticket search widgets.
- EKTA: travel insurance partner link handoff.

## Widget Catalog Notes

Available widget/tool entries observed in the dashboard:

- Trip.com Hotel Search Form widget `4038`.
- Trip.com Flights Search Form widget `4132`.
- Aviasales Flights Search Form widget `7879`.
- Aviasales Schedule Widget `2811`.
- Aviasales Pricing Calendar Widget `4041`.
- Aviasales Popular Routes Widget `4044`.
- Aviasales Prices on Map Widget `4054`.
- Kiwitaxi White Label `691`.
- Kiwitaxi Short and Tidy Shuttles Search Form `1486`.
- Kiwitaxi Reviews Widget `2948`.
- Kiwitaxi Shuttles Search Form `2949`.
- Kiwitaxi White Label 2.0 `3879`.
- Economybookings.com Rental Cars Dynamic Banner `2082`.
- Economybookings.com Rental Cars Search Form `4480`.
- TicketNetwork Events Schedule with Search Filters `6086`.
- TicketNetwork Tickets Search form `8505`.

## Homepage Implementation

The homepage now includes an `Active Travelpayouts tools` section that surfaces connected programs without changing the WordPress-owned product boundary:

- `flights_white_label_search` renders the configured Travelpayouts/Aviasales flight search placement.
- `hotels_partner_search` renders the configured Trip.com hotel placement.
- `flights_popular_routes` is now approved for the `home` surface and renders the Aviasales popular-routes placement.
- Kiwitaxi, Economybookings.com, TicketNetwork, and EKTA are presented as sponsored partner handoff cards.

Generated dashboard links and SubID intent:

- Kiwitaxi transfer card: `home_transfers_card`.
- Economybookings.com rental car card: `home_cars_card`.
- TicketNetwork events card: `home_events_card`.
- EKTA insurance card: `home_insurance_card`.

These partner cards use provider-owned handoff links with `nofollow sponsored noopener noreferrer` and visible affiliate disclosure. WordPress does not claim live inventory, checkout, payment, cancellation, reservation changes, or provider support for these programs.

## Booking.com Readiness

Booking.com should remain out of active homepage monetization until the approval blockers in `.plan/bookingcom-review-readiness.md` are resolved. The public source must show the real travel site, original travel content, contact/trust/legal pages, and a Booking.com-compatible project profile before another review request.

## Next Opportunities

- Configure Kiwitaxi and Economybookings.com widget embed code from the dashboard builder once the Travelpayouts builder is usable at a wider browser viewport.
- Add dedicated content modules for airport transfers, rental cars, events, and insurance after the first production content set exists.
- Use SubID conventions by page and component so Travelpayouts Performance reports can separate homepage widgets from route, hotel, destination, and planner handoffs.
- Keep unavailable catalog programs out of public UI until the dashboard shows them connected for the source.
