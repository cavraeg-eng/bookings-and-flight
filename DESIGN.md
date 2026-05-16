---
version: alpha
name: Booking and flights
description: Single source of truth for a commission-based travel booking website covering flights, hotels, airport services, taxis, events, tours, and destination deals.
product:
  name: Booking and flights
  category: affiliate travel booking website
  businessModel: commission based
  affiliateEngine: Travelpayouts
  bookingModel: partner handoff
  promise: Help travelers compare options, understand the trip, and continue to trusted partners for final booking.
  mustSupport:
    - flight search
    - hotel search
    - airport information
    - airport transfer and taxi booking
    - event and activity booking
    - tour and destination deals
    - seasonal travel offers
    - travel guides and route content
  mustNotClaim:
    - direct airline checkout ownership
    - guaranteed final fares
    - direct hotel inventory ownership
    - direct payment processing unless implemented and legally verified
brand:
  essence: warm travel commerce with practical booking clarity
  mood: scenic, useful, confident, and calm
  personality: helpful travel expert, not luxury concierge and not generic search engine
  density: card-rich and scannable
  hierarchy: search first, destination imagery second, useful deal context third
  trustPosture: transparent about partner links and commission-based offers
  userFeeling:
    - I can search quickly.
    - I understand what the offer includes.
    - I can compare prices without feeling pushed.
    - I know when I am leaving for a partner booking page.
designPrinciples:
  searchFirst: Every primary page should make the relevant search or booking action obvious in the first viewport.
  imageLed: Use real travel, airport, hotel, city, event, and vehicle imagery as the main visual material.
  commerceClarity: Prices, dates, locations, ratings, inclusions, and partner handoffs must be easy to scan.
  unifiedSurfaces: Flights, hotels, taxis, events, and tours use the same card, badge, form, and CTA language.
  honestAffiliateUX: Affiliate disclosure is quiet but visible near partner-driven results and footer content.
  softTravelShapes: Use rounded cards, pill buttons, and gentle shadows for booking surfaces.
  restrainedColor: Orange is for primary action and deal emphasis. Blue is for booking-commerce action and selected states.
aiGuardrails:
  instructionStrength: hard
  sourceOfTruthRule: This file is the design source of truth. Do not look for or depend on an external visual reference to make interface decisions.
  consistencyRule: New components must inherit the tokens, shapes, spacing, card structures, and CTA hierarchy defined here.
  flexibilityRule: Adapt the same visual system across airport, hotel, taxi, event, tour, and flight surfaces without inventing separate styles for each domain.
  disclosureRule: Commission and partner-handoff language must stay present wherever offers or outbound booking actions appear.
  forbidden:
    - Do not use abstract gradient backgrounds where real travel imagery belongs.
    - Do not create a flat admin dashboard look for consumer booking pages.
    - Do not make a marketing-only homepage without a usable search path.
    - Do not use a separate visual language for hotels, taxis, events, or flights.
    - Do not hide affiliate disclosure.
    - Do not imply Booking and flights completes airline, hotel, taxi, or event payment unless that is actually implemented.
    - Do not use purple SaaS gradients, beige lifestyle palettes, or dark analytics-dashboard styling.
    - Do not use em dashes in user-facing copy.
domains:
  flights:
    primaryAction: Search flights
    secondaryAction: Explore dates
    cardName: flight-deal-card
    requiredFields:
      - from
      - to
      - departure date
      - return date
      - travelers
      - cabin
    cardSignals:
      - route
      - airline or partner
      - travel dates
      - stops
      - duration
      - starting price
      - old price when real
      - partner disclosure
  hotels:
    primaryAction: Compare hotels
    secondaryAction: View rooms
    cardName: hotel-card
    requiredFields:
      - destination
      - check-in
      - check-out
      - guests
      - rooms
    cardSignals:
      - hotel name
      - location
      - rating
      - amenities
      - nightly price
      - total price when available
      - free cancellation when real
      - partner disclosure
  airports:
    primaryAction: Explore airport
    secondaryAction: Find transfers
    cardName: airport-info-card
    requiredFields:
      - airport or city
      - arrival or departure
      - date
    cardSignals:
      - airport code
      - terminal
      - nearby hotels
      - transfer options
      - lounge or service notes
      - route links
  taxis:
    primaryAction: Book transfer
    secondaryAction: Compare rides
    cardName: transfer-card
    requiredFields:
      - pickup
      - dropoff
      - date
      - time
      - passengers
      - luggage
    cardSignals:
      - vehicle type
      - pickup location
      - estimated duration
      - passenger capacity
      - luggage capacity
      - starting price
      - partner disclosure
  events:
    primaryAction: Find events
    secondaryAction: View tickets
    cardName: event-card
    requiredFields:
      - destination
      - dates
      - category
      - travelers
    cardSignals:
      - event name
      - venue
      - date
      - time
      - category
      - starting price
      - availability when real
      - partner disclosure
  tours:
    primaryAction: View experience
    secondaryAction: Check dates
    cardName: activity-card
    requiredFields:
      - destination
      - date
      - travelers
      - activity type
    cardSignals:
      - activity name
      - destination
      - duration
      - rating
      - group size
      - starting price
      - included highlights
      - partner disclosure
colors:
  primary: "#FF9D3D"
  primary-hover: "#E8892E"
  primary-soft: "#FFF1E2"
  secondary: "#015FC9"
  secondary-hover: "#014FA8"
  secondary-soft: "#E8F1FC"
  heading: "#16243D"
  footer-bg: "#16243D"
  footer-deep: "#101A2C"
  overlay-dark: "#16243D"
  text: "#69727D"
  text-muted: "#82828A"
  text-soft-on-dark: "#8297B0"
  text-strong: "#27333F"
  white: "#FFFFFF"
  black: "#000000"
  surface: "#FFFFFF"
  surface-muted: "#F2F5F9"
  surface-warm: "#FDF8F4"
  surface-neutral: "#F1F1F1"
  border: "#DFDFDF"
  border-soft: "#E1DFDF"
  media-placeholder: "#CCCCCC"
  success: "#21855A"
  warning: "#D68A00"
  danger: "#C43D3D"
typography:
  body:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.8
    letterSpacing: 0px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: 400
    lineHeight: 1.7
    letterSpacing: 0px
  label:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0px
  nav:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: 500
    lineHeight: 1.8
    letterSpacing: 0px
  button:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: 0px
  button-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: 0px
  h1:
    fontFamily: Plus Jakarta Sans
    fontSize: 64px
    fontWeight: 700
    lineHeight: 1.12
    letterSpacing: 0px
  h1-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: 700
    lineHeight: 1.16
    letterSpacing: 0px
  h2:
    fontFamily: Plus Jakarta Sans
    fontSize: 50px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: 0px
  h2-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 35px
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: 0px
  h3:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: 0px
  card-title:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: 0px
  price:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: 0px
  eyebrow:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: 0px
  helper:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0px
  script-accent:
    fontFamily: Covered By Your Grace
    fontSize: 50px
    fontWeight: 400
    lineHeight: 1
    letterSpacing: 0px
rounded:
  none: 0px
  xs: 4px
  sm: 5px
  md: 8px
  lg: 10px
  xl: 12px
  card: 16px
  section: 20px
  feature: 30px
  pill: 30px
  circle: 999px
spacing:
  none: 0px
  xxs: 4px
  xs: 8px
  sm: 12px
  md: 15px
  lg: 20px
  xl: 30px
  xxl: 40px
  xxxl: 60px
  section-sm: 50px
  section-md: 80px
  section-lg: 90px
  section-xl: 120px
  container-gutter: 15px
  container-max: 1200px
elevation:
  card: "0 10px 20px rgba(0, 0, 0, 0.10)"
  toolbar: "0 10px 30px rgba(0, 0, 0, 0.05)"
  floatingPanel: "0 10px 60px rgba(0, 0, 0, 0.10)"
  hoverLift: "0 8px 20px -2px rgba(15, 29, 35, 0.24)"
  softBlueCard: "0 0 20px 0 rgba(6, 30, 98, 0.08)"
layout:
  container:
    maxWidth: 1200px
    gutter: 15px
  sections:
    desktopPaddingY: 120px
    tabletPaddingY: 80px
    mobilePaddingY: 50px
  grids:
    cardGap: 30px
    promoGap: 40px
    desktopColumns: 3
    tabletColumns: 2
    mobileColumns: 1
  overlap:
    searchPanelLift: -50px
    cardBandLift: -120px
    mobileSearchPanelLift: -15px
responsiveContract:
  desktop:
    minWidth: 1025px
    containerMax: 1200px
    sectionPaddingTop: 120px
    sectionPaddingBottom: 90px
  tablet:
    minWidth: 768px
    maxWidth: 1024px
    sectionPaddingTop: 80px
    sectionPaddingBottom: 50px
  mobile:
    maxWidth: 767px
    sectionPaddingTop: 50px
    sectionPaddingBottom: 50px
    tapTargetMin: 44px
    searchPanelLayout: stacked full-width controls
imagery:
  requiredStyle: real travel imagery with clear subject matter
  hero:
    aspect: wide scenic photo or video still
    overlay: deep navy or black at low opacity for readability
    textPlacement: directly over image or in a clean open area, not inside a decorative card
  cards:
    aspectRatio: "6 / 5"
    treatment: rounded top image, full-card image background, or circular cropped image only where specified
    overlay: deep navy gradient for text-on-image cards
  domainGuidance:
    flights: airports, cabins, aircraft, skyline arrivals, passport and gate moments
    hotels: exterior, room, pool, lobby, breakfast, local neighborhood
    taxis: vehicle, airport pickup, luggage, curbside, driver handoff
    events: venue, crowd, stage, local festival, attraction
    destinations: city, beach, mountain, landmark, street, food, and culture imagery
  forbidden:
    - abstract travel icons as primary hero art
    - blurred stock-like photos that hide the destination or service
    - decorative gradient-only hero backgrounds
    - tiny thumbnails for primary deal cards
icons:
  style: rounded line icons or simple filled travel pictograms
  defaultColor: "{colors.primary}"
  selectedColor: "{colors.secondary}"
  containerBackground: "{colors.surface-muted}"
  containerRounded: "{rounded.circle}"
  useFor:
    - airport
    - plane
    - hotel
    - taxi
    - event ticket
    - calendar
    - users
    - luggage
    - location
    - price
    - rating
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.heading}"
    typography: "{typography.button}"
    rounded: "{rounded.pill}"
    padding: 18px
    height: 56px
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
    textColor: "{colors.heading}"
    typography: "{typography.button}"
    rounded: "{rounded.pill}"
    padding: 18px
    height: 56px
  button-primary-sm:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.heading}"
    typography: "{typography.button-sm}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 40px
  button-secondary:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.white}"
    typography: "{typography.button-sm}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 40px
  button-secondary-hover:
    backgroundColor: "{colors.secondary-hover}"
    textColor: "{colors.white}"
    typography: "{typography.button-sm}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 40px
  button-light:
    backgroundColor: "{colors.white}"
    textColor: "{colors.heading}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 12px
    height: 42px
  header-nav-link:
    backgroundColor: "{colors.white}"
    textColor: "{colors.heading}"
    typography: "{typography.nav}"
    rounded: "{rounded.none}"
    padding: 25px
    height: 79px
  search-submit:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.heading}"
    typography: "{typography.button}"
    rounded: "{rounded.xs}"
    padding: 15px
    height: 58px
  form-input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-strong}"
    typography: "{typography.body}"
    rounded: "{rounded.sm}"
    padding: 15px
    height: 46px
  domain-card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text}"
    typography: "{typography.body}"
    rounded: "{rounded.lg}"
    padding: 30px
  featured-card:
    backgroundColor: "{colors.overlay-dark}"
    textColor: "{colors.white}"
    typography: "{typography.body}"
    rounded: "{rounded.section}"
    padding: 30px
  card-meta:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-strong}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.xl}"
    padding: 18px
  destination-card:
    backgroundColor: "{colors.overlay-dark}"
    textColor: "{colors.white}"
    typography: "{typography.card-title}"
    rounded: "{rounded.feature}"
    padding: 30px
  icon-card:
    backgroundColor: "{colors.surface-muted}"
    textColor: "{colors.heading}"
    typography: "{typography.card-title}"
    rounded: "{rounded.feature}"
    padding: 40px
  toolbar:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-strong}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.lg}"
    padding: 20px
    height: 46px
  footer:
    backgroundColor: "{colors.footer-bg}"
    textColor: "{colors.white}"
    typography: "{typography.body}"
    rounded: "{rounded.none}"
    padding: 90px
  warm-section:
    backgroundColor: "{colors.surface-warm}"
    textColor: "{colors.heading}"
    typography: "{typography.body}"
    rounded: "{rounded.none}"
    padding: 120px
  neutral-section:
    backgroundColor: "{colors.surface-neutral}"
    textColor: "{colors.heading}"
    typography: "{typography.body}"
    rounded: "{rounded.none}"
    padding: 120px
  border-subtle:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-strong}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    padding: 20px
  media-placeholder:
    backgroundColor: "{colors.media-placeholder}"
    textColor: "{colors.black}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.lg}"
    padding: 20px
  divider:
    backgroundColor: "{colors.border}"
    textColor: "{colors.heading}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.none}"
    height: 1px
  divider-soft:
    backgroundColor: "{colors.border-soft}"
    textColor: "{colors.heading}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.none}"
    height: 1px
  filter-chip-selected:
    backgroundColor: "{colors.primary-soft}"
    textColor: "{colors.heading}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 12px
    height: 40px
  tab-selected:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.white}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 12px
    height: 42px
  info-chip:
    backgroundColor: "{colors.secondary-soft}"
    textColor: "{colors.secondary}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 36px
  helper-note:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text}"
    typography: "{typography.helper}"
    rounded: "{rounded.none}"
    padding: 0px
  muted-swatch:
    backgroundColor: "{colors.text-muted}"
    textColor: "{colors.black}"
    typography: "{typography.helper}"
    rounded: "{rounded.none}"
    padding: 0px
  footer-subfooter:
    backgroundColor: "{colors.footer-deep}"
    textColor: "{colors.text-soft-on-dark}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.none}"
    padding: 20px
  success-badge:
    backgroundColor: "{colors.success}"
    textColor: "{colors.white}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 34px
  warning-badge:
    backgroundColor: "{colors.warning}"
    textColor: "{colors.heading}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 10px
    height: 34px
  error-message:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.danger}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.md}"
    padding: 12px
componentPatterns:
  bookingSearch:
    purpose: universal search surface for flights, hotels, taxis, airport services, events, tours, and deals
    structure:
      - segmented domain tabs
      - primary fields
      - optional advanced fields
      - orange submit button
      - quiet affiliate note when partner-powered
    style:
      backgroundColor: "{colors.surface}"
      rounded: "{rounded.lg}"
      shadow: "{elevation.floatingPanel}"
      controlHeight: 58px
      desktopLayout: single row when possible
      mobileLayout: stacked full-width controls
  resultCard:
    purpose: reusable commerce card for any bookable or partner-linked item
    requiredElements:
      - image or icon
      - title
      - location or route
      - date or time context
      - key metadata row
      - price block
      - partner or disclosure note
      - blue action button
    style:
      imageRadius: "{rounded.lg}"
      contentPadding: 30px
      metaPanelRadius: "{rounded.xl}"
      shadow: "{elevation.card}"
  featuredDealCard:
    purpose: high-emphasis visual card for hero-adjacent and blue-band offers
    requiredElements:
      - large image
      - dark overlay
      - category or discount badge
      - title
      - metadata
      - price
      - action
    style:
      rounded: "{rounded.section}"
      imageHeightDesktop: 390px
      imageHeightLarge: 500px
      textColor: "{colors.white}"
      overlayGradient: "linear-gradient(0deg, #16243D 0%, rgba(27, 31, 46, 0) 80%)"
  filterSidebar:
    purpose: narrow results on listing pages
    groups:
      - price
      - dates
      - location
      - rating
      - stops or duration
      - amenities
      - vehicle type
      - event category
      - partner
    style:
      backgroundColor: "{colors.surface}"
      rounded: "{rounded.lg}"
      borderColor: "{colors.border}"
      groupTitleTypography: "{typography.h3}"
  comparisonToolbar:
    purpose: sort, result count, view mode, and compact filters
    style:
      backgroundColor: "{colors.surface}"
      rounded: "{rounded.lg}"
      shadow: "{elevation.toolbar}"
      height: 46px
  priceBlock:
    purpose: show starting price, old price, fee hint, or total estimate
    rules:
      - Label starting prices as From when exact total is not known.
      - Use strikethrough old prices only when sourced from real partner data.
      - Keep price in heading navy or white depending on card background.
      - Keep disclosure near partner-driven prices.
  disclosureNote:
    purpose: make affiliate model clear without overwhelming the interface
    defaultText: Booking and flights may earn a commission when you book through partner links.
    style:
      typography: "{typography.helper}"
      textColor: "{colors.text-muted}"
      placement: below CTA, in result footer, or in page footer
fragments:
  header:
    role: global navigation and fast search entry
    requiredElements:
      - logo
      - primary nav
      - help or contact link
      - optional account link
      - search icon
      - primary CTA
    primaryCta: Search flights
    style:
      backgroundColor: "{colors.surface}"
      textColor: "{colors.heading}"
      activeColor: "{colors.primary}"
      navHeight: 79px
  heroSearch:
    role: first viewport conversion surface
    requiredElements:
      - scenic image
      - h1
      - short supporting copy
      - domain-aware search form
      - primary CTA
    preferredHeadlines:
      - Search flights, hotels, taxis, and things to do in one place.
      - Plan the route, compare the stay, and book through trusted partners.
    style:
      backgroundColor: "{colors.surface-neutral}"
      minHeightDesktop: 631px
      overlay: readable dark layer when text sits over media
  serviceGrid:
    role: expose the breadth of Booking and flights
    cards:
      - Flights
      - Hotels
      - Airport transfers
      - Taxis
      - Events
      - Tours
    style:
      layoutDesktop: 3 or 4 columns
      layoutTablet: 2 columns
      layoutMobile: 1 column
      cardType: image-led or icon-plus-image hybrid
  destinationHub:
    role: route, city, and destination discovery
    requiredElements:
      - destination image
      - destination name
      - route or deal count
      - starting price when real
      - full-card link
    style:
      imageOverlay: dark readable overlay
      rounded: "{rounded.feature}"
  featuredDealsBand:
    role: highest-value commission offers
    requiredElements:
      - blue background band
      - white heading
      - carousel or grid of featured cards
      - visible prices
      - blue or orange CTAs depending on background
    style:
      backgroundColor: "{colors.secondary}"
      sectionPaddingTop: 120px
      bottomOverlap: 120px
  airportServices:
    role: airport-specific planning and conversion
    cards:
      - airport hotels
      - transfers
      - taxis
      - lounge or services
      - nearby events
    style:
      cardType: icon-card with image support
      highlightColor: "{colors.primary}"
  hotelResults:
    role: hotel listing and comparison
    requiredElements:
      - hotel image
      - hotel name
      - neighborhood
      - rating
      - amenities
      - cancellation signal when real
      - price
      - compare hotels CTA
  transferResults:
    role: airport taxi and transfer comparison
    requiredElements:
      - vehicle image or icon
      - pickup and dropoff
      - date and time
      - passenger and luggage capacity
      - estimated duration
      - price
      - book transfer CTA
  eventResults:
    role: event and attraction discovery
    requiredElements:
      - event image
      - event name
      - venue
      - date and time
      - category
      - starting price
      - view tickets CTA
  articleGrid:
    role: SEO, planning, and route guidance
    contentTypes:
      - route guides
      - airport guides
      - hotel area guides
      - taxi and transfer explainers
      - event weekend guides
      - destination deal roundups
    style:
      imageLed: true
      titleTypography: "{typography.card-title}"
      cardGap: 30px
  footer:
    role: navigation, newsletter, support, contact, and affiliate disclosure
    requiredGroups:
      - Company
      - Explore
      - Booking services
      - Contact
      - Newsletter
      - Affiliate disclosure
    style:
      backgroundColor: "{colors.footer-bg}"
      subfooterColor: "{colors.footer-deep}"
      textColor: "{colors.white}"
      mutedTextColor: "{colors.text-soft-on-dark}"
states:
  hover:
    primaryButton: darken to primary-hover and lift slightly
    secondaryButton: darken to secondary-hover
    cards: use hoverLift shadow and subtle image scale
    links: color primary
  selected:
    tabs: secondary background with white text
    filters: primary-soft background, heading text, primary border
  loading:
    cards: surface-muted skeleton blocks with same radius as final content
    search: keep submit visible but disabled with muted opacity
  empty:
    tone: helpful and specific
    action: suggest changing dates, destination, budget, or service type
  error:
    tone: calm and recoverable
    color: "{colors.danger}"
copyRules:
  ctaStyle: short verb-first labels
  preferredCtas:
    - Search flights
    - Compare hotels
    - Book transfer
    - View tickets
    - Check prices
    - Explore dates
    - View deal
  avoidCtas:
    - Buy ticket now
    - Guaranteed fare
    - Complete payment
    - Official checkout
  affiliateCopy:
    short: Partner links may earn us a commission.
    full: Booking and flights may earn a commission when you book through partner links.
  punctuation:
    noEmDash: true
---

## Overview

`Booking and flights` is a unified travel-commerce design system for a commission-based website powered by partner booking links. It covers flights, hotels, airports, taxis, transfers, events, tours, destinations, and seasonal deals without splitting each domain into a different visual style.

The site should feel visual and useful at the same time. Every page should make the next travel action clear: search, compare, check dates, view a partner deal, or keep planning. The design uses scenic imagery, rounded booking surfaces, orange primary actions, blue commerce actions, navy headings, and soft shadows.

This file is the single source of truth. An agent should be able to build new components and fragments from these tokens and patterns without leaving this document.

## Colors

Use `#FF9D3D` orange for the main action path: search buttons, active highlights, deal badges, discount states, and important icons. Use it with navy text on normal-size buttons for stronger readability.

Use `#015FC9` blue for commerce actions and selected booking states: `View deal`, `Compare hotels`, selected tabs, media strips, and secondary buttons.

Use `#16243D` for headings, dark overlays, footer backgrounds, and high-contrast text areas. Body copy should use `#69727D`; quieter metadata can use `#82828A`.

Use `#F2F5F9` and `#FDF8F4` for calm section bands. Avoid one-note pages that are all orange, all blue, or all gray.

## Typography

Use Plus Jakarta Sans for the interface. It should carry body copy, headings, cards, navigation, forms, buttons, filters, and metadata.

Use Covered By Your Grace only as a rare accent for a short promotional phrase. Do not use it for navigation, forms, prices, body copy, or booking cards.

Headings should be bold and compact. Body copy should stay readable with generous line height. Do not use negative letter spacing.

## Layout

Use a `1200px` max container with `15px` gutters. Most desktop sections use `120px` top padding and `90px` to `120px` bottom padding. Tablet sections usually compress to `80px`, and mobile sections to `50px`.

Use `30px` grid gaps for normal cards and `40px` gaps for larger promotional rows. Search panels may overlap hero sections by about `50px` on desktop and should stack into full-width controls on mobile.

## Components

### Search Surfaces

Search is the heart of the product. The same shell should support flights, hotels, taxis, airport services, events, tours, and packages through segmented tabs or a domain selector.

The search shell uses a white background, soft shadow, 10px radius, 58px controls, dark labels, and an orange submit button. On mobile, each field becomes full-width with at least a 44px tap target.

### Result Cards

All bookable and partner-linked items use the same structure: image, title, location or route, date or time context, metadata row, price block, disclosure note, and CTA.

Flight cards show route, airline or partner, dates, stops, duration, and price. Hotel cards show location, rating, amenities, cancellation signal when real, and price. Taxi cards show pickup, dropoff, time, capacity, luggage, duration, and price. Event cards show venue, date, category, availability when real, and price.

### Featured Cards

Use large image cards for high-emphasis deals. They should have a 20px radius, dark gradient overlay, white text, a badge, metadata, price, and a clear action. These are for destination deals, flight offers, hotel packages, airport transfer promos, and event weekends.

### Filters And Toolbars

Filter groups should be quiet and practical: price, dates, location, rating, stops, duration, amenities, vehicle type, event category, and partner. Keep filter titles navy and compact.

Toolbars use a white surface, 10px radius, soft shadow, result count, sort control, and view toggles.

### Footer

The footer uses deep navy, white text, muted support text, orange hover states, newsletter signup, service links, contact links, and affiliate disclosure. The disclosure should be visible without shouting.

## Do's and Don'ts

Do build every new booking surface from the same card, form, button, badge, typography, and spacing rules.

Do use real travel imagery wherever the user needs to understand a place, service, hotel, event, airport, or vehicle.

Do keep orange focused on search and deal emphasis.

Do keep blue focused on partner-booking and selected commerce actions.

Do make prices and partner handoffs clear.

Don't create separate visual systems for flights, hotels, taxis, events, or tours.

Don't use generic abstract decoration where service imagery belongs.

Don't claim final booking ownership unless the feature truly does that.

Don't hide affiliate language.

Don't turn consumer travel pages into admin dashboards.
