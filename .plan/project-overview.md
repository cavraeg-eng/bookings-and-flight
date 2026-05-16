# Project Overview

## Product Vision

Bookings and Flights is an AI-assisted travel discovery, planning, SEO, and affiliate conversion platform built around WordPress. The product should help users find where to go, when to travel, which flights/hotels/activities fit their intent, and how to continue planning through saved trips and alerts.

The launch strategy is not to build a direct Expedia or Booking.com clone with checkout. The MVP should be a high-quality travel discovery and affiliate handoff engine using WordPress, Travelpayouts, partner widgets/deep links, and AI-assisted itinerary generation.

## Target Users

- Travelers searching for flight, hotel, destination, car, activity, and trip-planning ideas.
- Flexible travelers asking questions such as "Where should I go?", "When is cheapest?", or "Plan a budget trip."
- SEO visitors landing on route, destination, hotel, activity, seasonal, and itinerary pages.
- Site administrators managing settings, providers, affiliate disclosures, content, and generated assets.
- Editors publishing destination guides, route pages, deals, itineraries, and travel content.

## User Roles

- Anonymous visitor: searches, reads travel pages, clicks affiliate links, starts AI planning, and optionally signs up for alerts.
- Subscriber/member: saves trips, tracks alerts, resumes planning, and receives follow-up emails.
- Editor: creates and edits destination, route, deal, itinerary, and travel guide content.
- Administrator: manages plugins, provider credentials, API settings, capabilities, integrations, and publishing workflows.
- Future analyst/manager: reviews click, alert, conversion, AI session, and provider performance reports.

## Primary Workflows

1. Visitor searches for flights, hotels, cars, activities, or flexible destination ideas.
2. Visitor opens a destination, route, hotel, activity, seasonal, or itinerary SEO page.
3. AI planner turns natural-language intent into an itinerary and monetized travel cards.
4. User clicks through affiliate links or widgets to complete booking on a partner site.
5. User saves a trip, signs up for price alerts, or continues planning later.
6. Editors create and optimize destination/route/deal/itinerary content.
7. Administrators configure Travelpayouts and other provider credentials.
8. Reporting surfaces show searches, affiliate clicks, alerts, AI sessions, and provider performance.

## Core Data Model

WordPress remains the canonical system of record for editorial and workflow data. CPTs and post meta should model public travel entities. Custom tables should store high-volume or relational operational data such as searches, clicks, alerts, cached offers, AI sessions, and provider statistics.

Current workspace note: existing WordPress plugins and a `platform/` Next.js/Fastify monorepo already exist. Future phases must reconcile with those assets before implementation.

## Custom Post Types

Planned CPTs:

| Key | Purpose |
| --- | --- |
| `destination` | City, country, and region guides |
| `route` | Origin-destination flight pages |
| `travel_deal` | Editorial or cached deal posts |
| `trip_plan` | AI-generated and saved itineraries |
| `travel_partner` | Travelpayouts and provider records |
| `travel_alert` | Price alert landing records or editorial alert pages |

## Taxonomies

Planned taxonomies:

| Key | Purpose |
| --- | --- |
| `travel_region` | Continent, country, region, or destination grouping |
| `travel_style` | Budget, family, luxury, beach, business, adventure, culture |
| `travel_vertical` | Flights, hotels, cars, activities, insurance, eSIM, transfers |
| `travel_season` | Month, season, holiday, or timing intent |

## Custom Tables

Planned tables use `$wpdb->prefix . 'bf_'`:

| Table | Purpose |
| --- | --- |
| `bf_searches` | Flight, hotel, activity, car, and flexible search records |
| `bf_clicks` | Affiliate click tracking and SubID attribution |
| `bf_alerts` | Price alert subscriptions and route watchlists |
| `bf_cached_offers` | Cached offer metadata from APIs/widgets |
| `bf_ai_sessions` | AI chat/planning sessions and run metadata |
| `bf_provider_stats` | Provider-level reporting and conversion summaries |

Database implementation must be idempotent, non-destructive, and reviewed before activation.

## REST API Namespace and Planned Routes

Canonical WordPress REST namespace:

```text
baf/v1
```

Existing routes from `plugins/bookings-and-flights-affiliate-bridge/`:

- `GET /wp-json/baf/v1/config`
- `GET /wp-json/baf/v1/status`

Planned routes:

- `POST /wp-json/baf/v1/ai/chat`
- `POST /wp-json/baf/v1/ai/itinerary`
- `POST /wp-json/baf/v1/trips/save`
- `GET /wp-json/baf/v1/trips/:id`
- `POST /wp-json/baf/v1/alerts`
- `POST /wp-json/baf/v1/affiliate/link`
- `POST /wp-json/baf/v1/affiliate/click`
- `GET /wp-json/baf/v1/destinations`
- `GET /wp-json/baf/v1/routes`
- `GET /wp-json/baf/v1/search/flights`
- `GET /wp-json/baf/v1/search/hotels`

All protected routes require real permission callbacks, validation, sanitization, and permission-failure tests.

## Admin UI Structure

Planned admin areas:

- Bookings and Flights dashboard
- Settings and provider credentials
- Travelpayouts configuration and status
- Affiliate links and SubID tools
- Destination, route, deal, trip, partner, and alert content management
- AI planner runs and saved trip review
- Search, click, alert, and provider reporting
- Validation/status screen for integration health

Existing admin surface:

- `Bookings and Flights — Affiliate Bridge` settings and status UI.
- Generated content manager plugin for theme content editing.

## Capabilities and Permissions

Planned capabilities:

- `manage_baf_settings`
- `edit_baf_content`
- `publish_baf_content`
- `manage_baf_affiliates`
- `view_baf_reports`
- `run_baf_ai`
- `manage_baf_alerts`

Early phases may map administrator-only actions to `manage_options`, but long-term phases should introduce dedicated capabilities and document role mapping.

## Settings and Options

Existing options:

- `baf_supplier_credentials`
- `baf_search_api_url`
- `baf_postback_secret`

Planned options:

- `baf_core_version`
- `baf_db_version`
- `baf_settings`
- `baf_travelpayouts_settings`
- `baf_ai_settings`
- `baf_consent_settings`
- `baf_tracking_settings`

Secrets must be stored server-side only and never rendered into HTML, JavaScript, REST responses, logs, admin notices, or frontend payloads.

## Activation and Deactivation Behavior

Activation should:

- register CPTs, taxonomies, capabilities, routes, and tables as needed
- create or update custom tables idempotently
- seed safe default options
- schedule cron hooks only when required
- flush rewrite rules only when rewrite registrations change

Deactivation should:

- unschedule project cron hooks
- flush rewrite rules when needed
- avoid deleting user data

Uninstall behavior must be explicitly approved before destructive cleanup.

## Background Jobs and Cron

Planned cron hooks:

- `baf_refresh_cached_offers`
- `baf_process_price_alerts`
- `baf_sync_provider_stats`
- `baf_cleanup_ai_sessions`
- `baf_send_saved_trip_followups`

Long-running provider calls, AI generation, reporting syncs, and alert processing must not run during normal page render.

## AI and Provider Abstraction

AI features should use a provider abstraction suitable for Vercel AI SDK, Claude, OpenAI, or future providers. AI tooling should support:

- natural-language intent extraction
- itinerary generation
- travel card generation
- affiliate link creation
- destination guide retrieval
- budget estimation
- saved trip creation
- price alert creation

Required guardrails:

- demo mode for local development
- schema-validated structured outputs
- consent before sending site, user, analytics, or private content to external AI providers
- no direct auto-publishing by the model
- no raw secrets or private prompts in logs

## Security Model

Security baseline:

- capability checks for all protected actions
- nonces for admin and frontend writes
- REST permission callbacks for all endpoints
- input validation and sanitization before storage
- output escaping before rendering
- prepared SQL for custom tables
- no frontend exposure of provider credentials
- affiliate disclosure on monetized pages
- privacy and terms updates for analytics, AI, affiliate tracking, and partner handoff

## Validation Strategy

Targeted validation should run before every implementation summary:

- PHP syntax checks for changed PHP files
- WordPress plugin activation checks when bootstrap, hooks, CPTs, settings, or migrations change
- REST smoke and permission-failure tests for route changes
- database migration verification for table changes
- `npm run typecheck` for `platform/` TypeScript changes
- manual admin/frontend UI review for UI changes

Known commands and gaps are tracked in `.plan/validation-baseline.md`.

## Documentation Strategy

Keep `.plan/` current:

- `.plan/project-overview.md`
- `.plan/architecture-baseline.md`
- `.plan/phased-implementation.md`
- `.plan/phase-review-log.md`
- `.plan/validation-baseline.md`
- `.plan/decisions.md`
- `.plan/known-issues.md`
- `.plan/regression-watchlist.md`

Update documentation whenever contracts, routes, tables, settings, capabilities, hooks, cron jobs, provider boundaries, validation commands, issues, or phase status changes.

## Phase Breakdown

1. Phase 0: Architecture Baseline and Plugin Skeleton
2. Phase 1: Core Data Model
3. Phase 2: Settings and Admin Foundation
4. Phase 3: REST API Foundation
5. Phase 4: Primary Product Workflow
6. Phase 5: Background Jobs and Automation
7. Phase 6: AI and External Provider Integration
8. Phase 7: UI Polish, Accessibility, and Responsive Behavior
9. Phase 8: Analytics, Reporting, and Optimization
10. Phase 9: Hardening, Regression Review, and Release Readiness

## Recommended Reusable Rules, Skills, and Workflows

Recommended future project-specific skills:

- `wordpress-plugin-architect`
- `wordpress-security-reviewer`
- `wordpress-rest-controller-builder`
- `wordpress-phase-reviewer`
- `wordpress-db-migration-reviewer`
- `wordpress-admin-ui-reviewer`
- `ai-wordpress-integration-reviewer`

Recommended recurring workflow:

1. Run the phase consultant/alignment prompt.
2. Reconcile codebase reality with `.plan/`.
3. Consult official WordPress/provider docs.
4. Implement the smallest reviewable slice.
5. Run targeted validation.
6. Run the phase review prompt.
7. Update documentation and phase status.

## Research Consulted

- WordPress Plugin Developer Handbook: plugin structure, hooks, activation, security, and documentation.
- WordPress Plugin Security Handbook: capabilities, validation, sanitization, nonces, and escaping.
- WordPress REST API Handbook: custom endpoint planning, authentication restrictions, JSON responses, and route/schema references.
- AI SDK documentation: provider abstraction, streaming, tool calling, and structured output capabilities.
- Existing project plan: `.plan/travel-affiliate-ai-wordpress-platform-plan.md`.