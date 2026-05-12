# Decisions Log

Use this format for future decisions:

```markdown
## YYYY-MM-DD - Decision Title

Decision:

Context:

Alternatives considered:

Consequences:

Affected contracts:
```

## 2026-04-27 - REST Namespace

Decision: Use `baf/v1` as the canonical WordPress REST namespace.

Context: The existing affiliate bridge plugin already registers routes under `baf/v1`, and the initial product plan recommends `/wp-json/bf/v1/`. The existing implementation is the current source of truth.

Alternatives considered: `bf/v1`, `bookings-flights/v1`.

Consequences: Future WordPress REST routes should use `/wp-json/baf/v1/` unless a documented migration is approved. Existing `/config` and `/status` routes must be preserved or migrated intentionally.

Affected contracts: REST namespace, route documentation, platform integration.

## 2026-04-27 - Plugin Prefix and Namespace

Decision: Use `BAF\Core` and `baf_` for the planned core plugin while preserving existing plugin namespaces and prefixes.

Context: Existing affiliate bridge code uses `BAF\AffiliateBridge`; generated content manager code uses `Bookings_And_Flights_` and `bookings_and_flights_`.

Alternatives considered: `BF`, `BookingsFlights`, or reusing the content manager prefix.

Consequences: New core product code should use `BAF\Core` and `baf_`. Existing plugins should not be renamed without migration review.

Affected contracts: PHP namespace, function prefix, hooks, options, capabilities, REST fields.

## 2026-04-27 - Data Model Approach

Decision: Use WordPress CPTs and post meta for editorial travel entities; use custom tables for high-volume operational records.

Context: WordPress should remain the system of record for content and workflow state. Search, clicks, alerts, cached offers, AI sessions, and provider stats are likely higher-volume and relational.

Alternatives considered: Store everything in post meta; move all records to the `platform/` service database.

Consequences: CPT phases must keep content editable in WordPress. Custom tables require idempotent migrations, prepared SQL, and review gates.

Affected contracts: CPT keys, table names, repository boundaries.

## 2026-04-27 - Settings Approach

Decision: Use the WordPress Settings API or documented equivalents for plugin settings.

Context: Existing affiliate bridge settings already use `register_setting()`. Provider credentials and AI settings require sanitization, masking, and capability restrictions.

Alternatives considered: Direct option writes from custom forms; external-only `.env` configuration.

Consequences: Sensitive settings must be administrator-gated, sanitized, masked, and never exposed to frontend or public REST responses.

Affected contracts: Option keys, admin pages, capability model.

## 2026-04-27 - Custom Tables

Decision: Planned custom tables use `$wpdb->prefix . 'bf_'` names: `bf_searches`, `bf_clicks`, `bf_alerts`, `bf_cached_offers`, `bf_ai_sessions`, and `bf_provider_stats`.

Context: The initial plan proposed `wp_bf_*`, but project rules require `$wpdb->prefix` rather than hard-coded `wp_`.

Alternatives considered: Hard-coded `wp_bf_*`; CPT/meta-only storage.

Consequences: Migration code must use the runtime prefix, site charset/collation, idempotent schema updates, and non-destructive changes.

Affected contracts: Database table names, repositories, migration validation.

## 2026-04-27 - Provider Abstraction

Decision: Use provider abstractions for affiliate providers and AI providers.

Context: The product needs Travelpayouts, widgets, partner links, possible direct APIs, demo mode, Vercel AI SDK/Claude/OpenAI compatibility, and future provider replacement.

Alternatives considered: Hard-code Travelpayouts and AI provider calls directly in controllers or UI.

Consequences: REST controllers, admin pages, cron callbacks, and UI code must delegate provider work to services/adapters. Demo mode remains required.

Affected contracts: Provider interfaces, AI tool names, settings, REST endpoints.

## 2026-04-27 - Background Job Strategy

Decision: Use WP-Cron or a documented server cron bridge for WordPress-owned background jobs.

Context: Cached offers, price alerts, provider stats, AI cleanup, and follow-up emails should not run during normal page render.

Alternatives considered: Run provider calls during page render; move all jobs to `platform/` immediately.

Consequences: Cron hooks must be documented, unscheduled on deactivation, bounded, retry-safe, and observable in admin/status surfaces.

Affected contracts: Cron hook names, job handlers, validation baseline.

## 2026-04-27 - Capability Model

Decision: Start with administrator-only `manage_options` where needed, but move toward dedicated `manage_baf_*`, `edit_baf_content`, `publish_baf_content`, `view_baf_reports`, and `run_baf_ai` capabilities.

Context: Early plugin setup may be admin-only, but long-term editor/member workflows require finer permissions.

Alternatives considered: Use only `manage_options`; make content features broadly available to editors immediately.

Consequences: Capability mapping must be explicit before expanding access beyond administrators.

Affected contracts: Capabilities, admin pages, REST permission callbacks.

## 2026-04-27 - Major UI Architecture

Decision: Build WordPress admin UI as scoped plugin screens and keep frontend surfaces compatible with WordPress blocks/shortcodes and the existing platform UI boundary.

Context: The workspace includes WordPress themes/plugins and a Next.js app. Future phases must reconcile whether each UI belongs in WordPress, a block/shortcode, theme template, or `platform/`.

Alternatives considered: Replace WordPress UI with Next.js-only; build all UX in WordPress theme templates.

Consequences: UI ownership must be decided per phase. Scoped assets, nonces, capability gates, and accessibility reviews are required.

Affected contracts: Admin slugs, asset handles, blocks, shortcodes, platform integration.

## 2026-04-27 - Core Plugin Continuation Strategy

Decision: Continue with the phased `bookings-flights-core` implementation as the canonical WordPress-native path; do not restart from scratch or delete existing plugins by default.

Context: The workspace includes previous implementation artifacts and a `platform/` Next.js/Fastify monorepo. The current core plugin now contains a WordPress-native Phase 0/1 implementation with bootstrap, CPTs, taxonomies, capabilities, post meta, and repository/service boundaries. A Phase 1 audit confirmed the current implementation aligns with the phased plan and is not a direct TypeScript port.

Alternatives considered: Rewrite the plugin from scratch; refactor the existing affiliate bridge/content manager as the core plugin; continue by editing the current core plugin phase-by-phase.

Consequences: Future work should proceed phase-by-phase against `bookings-flights-core`, using review gates to refactor surgically when needed. Existing plugins remain in place until a planned phase explicitly migrates, integrates, or deprecates their responsibilities. `platform/` remains an integration layer unless a later architecture decision changes that boundary.

Affected contracts: Core plugin slug, existing plugin boundaries, platform integration boundary, phase review workflow.

## 2026-05-09 - Travelpayouts-Controlled Booking Backend

Decision: Use Travelpayouts as the controlling backend for monetized flight, hotel, car, activity, widget, White Label, partner-link, and booking-handoff flows. WordPress owns the branded frontend, content, SEO, consent, saved-trip intent, alert intent, AI planning, and admin governance.

Context: The product direction now prioritizes a WordPress website whose frontend competes with leading booking sites while the backend is fully controlled by Travelpayouts through its official plugin and widgets. Existing project code already supports WordPress-native CPTs, settings, affiliate links, AI planning, and reports, but future search/results work must not drift into a custom OTA backend.

Alternatives considered: Build custom flight and hotel search APIs in `platform/`; make `bookings-flights-core` store or serve live supplier inventory; replace Travelpayouts widgets with a custom booking flow; use only static affiliate links.

Consequences: Future flight and hotel search work should use the official Travelpayouts WordPress plugin when compatible, or Travelpayouts dashboard-generated widget/White Label embeds inside secured WordPress wrappers if plugin compatibility fails. `/search/flights` and `/search/hotels` must not become custom live inventory endpoints without a new documented decision. Booking, payment, refunds, supplier reservations, and revenue source of truth stay outside WordPress.

Affected contracts: Travelpayouts integration boundary, frontend search pages, widget registry, SubID generation, REST search route plans, `platform/` boundary, affiliate disclosures, phase plan.

## 2026-05-09 - Architecture Deepening Priority Plan

Decision: Future phase work should deepen five architecture areas before broad product expansion: Travelpayouts placement registry, search surface mode seam, brand continuity module, AI opportunity handoff, and content manager field pipeline. These are planning priorities, not final PHP or TypeScript interface names.

Context: An architecture review found that the Travelpayouts-first product direction is clear in `.plan/`, but important behavior is still scattered across the core plugin, static theme, affiliate bridge, content manager, and `platform/`. The strongest friction appears where callers must know too much about consent, SubIDs, disclosure, widget scripts, White Label continuity, safe redirects, AI opportunity approval, or template field rendering.

Alternatives considered: Leave each phase to rediscover these seams independently; fully design final interfaces before compatibility research; move the search/product shell back into `platform/`.

Consequences: Phase 11 should confirm the official-plugin or fallback Adapter path. Phase 12 should define shared brand continuity inputs and content-manager refactor needs. Phase 13 should make the placement registry the primary seam for Travelpayouts widgets, White Label placements, SubIDs, disclosure, consent, and safe embed output. Phase 18 should use an AI opportunity handoff module so AI can recommend approved placement drafts without booking, publishing, or silently executing provider work. `platform/` search routes must remain optional integration infrastructure unless a new decision explicitly approves direct inventory APIs.

Affected contracts: Phase 11-19 roadmap, Travelpayouts placement registry, search surface routing, brand/header continuity, AI opportunity workflow, content manager maintenance boundary, `platform/` integration boundary.

## 2026-05-12 - Phase 11 Backend Mode Decision

Decision: Phase 11 backend mode is Travelpayouts-controlled with official-plugin-first placement only for surfaces that pass local compatibility validation. The P11.3 flight widget path is cleared at the local validation level. Hotel widgets/tables and production White Label result surfaces must use Travelpayouts dashboard-generated widget or White Label embed code inside the secured WordPress wrapper until real configured-domain/widget validation passes or a future official plugin capability change is documented.

Context: P11.1 validated official plugin activation/deactivation on the local WordPress environment. P11.2 validated safe account setup and non-secret token-state behavior. P11.3 validated the official flight widget path, found the official hotel widget/table path inactive in the staged plugin, and documented White Label Widget/Page behavior without a real configured domain. P11.4/P11.6 documentation now records header-continuity requirements and local evidence-gate results, but production hotel fallback and White Label domain validation remain future setup prerequisites.

Alternatives considered: Declare the entire official plugin production-ready; build custom `/search/flights` or `/search/hotels` inventory APIs; promote the existing `platform/` Fastify search adapters to the canonical backend; delay all search placement work until real White Label domains are configured.

Consequences: Phase 12 may design frontend search shells around Travelpayouts-owned placements, but Phase 13 must implement a governed placement registry/search-surface mode seam before raw fallback embeds are broadly reusable. Planned `/search/flights` and `/search/hotels` routes may return safe shell/configuration, placement, consent/disclosure, SubID, missing-configuration, or handoff metadata only. They must not proxy supplier inventory, persist canonical live offers, or replace Travelpayouts reporting as the affiliate source of truth without a new decision.

Affected contracts: Phase 11 completion gate, Phase 12 design prerequisites, Phase 13 placement registry, `baf/v1` search route boundary, SubID strategy, Travelpayouts fallback embeds, White Label continuity validation, `platform/` integration boundary.
