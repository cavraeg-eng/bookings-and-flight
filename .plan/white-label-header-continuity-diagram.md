# White Label Header Continuity Diagram

This diagram shows how Bookings and Flights should keep one branded header and navigation experience while Travelpayouts controls the search/results and booking handoff layer.

```mermaid
flowchart TD
    Home["Bookings and Flights Home Page<br/>WordPress theme header + nav"]
    SearchPanel["Unified Search Panel<br/>Flights, Hotels, Explore"]
    Decision{"Which White Label surface<br/>best preserves brand continuity?"}

    Home --> SearchPanel --> Decision

    Decision -->|"Preferred when UX is sufficient"| WidgetPage["WordPress Search Results Page<br/>Same theme header, nav, footer, disclosure"]
    WidgetPage --> Widget["Travelpayouts White Label Widget<br/>Embedded search/results module"]
    Widget --> PartnerClick["Partner result click"]

    Decision -->|"Use when fuller results UX is required"| PageType["Travelpayouts White Label Page<br/>Subdomain or configured result page"]
    PageType --> WLHeader["Customized White Label Header<br/>Logo, favicon, brand colors, menus, heading copy"]
    WLHeader --> WLResults["Travelpayouts Search Results<br/>Controlled by Travelpayouts"]
    WLResults --> PartnerClick

    PartnerClick --> Partner["Partner Site<br/>Booking and payment happen off WordPress"]

    Admin["WordPress Admin<br/>Bookings and Flights settings"]
    Admin --> Registry["Widget / White Label Registry<br/>Approved placements, SubIDs, disclosures"]
    Registry --> WidgetPage
    Registry --> PageType

    BrandAssets["Shared Brand Assets<br/>Logo, favicon, colors, nav links, header copy"]
    BrandAssets --> Home
    BrandAssets --> WidgetPage
    BrandAssets --> WLHeader

    SubIDs["SubID Strategy<br/>channel_surface_vertical_slug_placement"]
    SubIDs --> Widget
    SubIDs --> WLResults

    Guardrails["Guardrails<br/>No direct checkout, no exposed secrets,<br/>visible affiliate disclosure"]
    Guardrails --> WidgetPage
    Guardrails --> PageType
    Guardrails --> Partner
```

## Implementation Rule

Prefer the embedded White Label Widget path because it keeps the WordPress header completely intact. Use the Page-type White Label path only when the result experience requires it, and then configure the Travelpayouts header to visually match the Bookings and Flights home site as closely as Travelpayouts allows.
