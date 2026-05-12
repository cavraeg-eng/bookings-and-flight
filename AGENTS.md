# AGENTS.md

## Scope

These instructions govern all implementation, review, validation, and documentation work in this WordPress project under `wp-content/`.

They apply to:

- WordPress plugin code in `plugins/`
- WordPress theme code in `themes/`
- admin UI, frontend UI, blocks, shortcodes, and templates
- REST API routes, permissions, and response contracts
- custom post types, taxonomies, post meta, user meta, options, and settings
- activation, deactivation, uninstall, migration, and cron logic
- affiliate and Travelpayouts integrations
- AI provider integrations, AI middleware, agents, tools, prompts, and structured outputs
- the `platform/` Next.js/Fastify monorepo when it integrates with WordPress or affiliate/search workflows
- documentation and phase tracking in `.plan/`

Do not begin product implementation for a new phase until the project plan, architecture baseline, phase alignment, and applicable validation requirements have been reviewed.

## Product Direction

Bookings and Flights is a WordPress-native travel discovery, AI planning, SEO, and affiliate conversion platform. The product should not become a direct online travel agency in early phases. It should help users discover trips, compare options, create itineraries, save travel ideas, track deals, and click through to partner providers for booking.

WordPress remains the canonical system of record for content, editorial workflows, custom post types, taxonomies, post meta, settings, approval state, admin UI state, publishing workflows, affiliate tracking records when stored in WordPress, and documented architecture decisions unless `.plan/decisions.md` records an approved alternative.

The current workspace also contains `platform/`, a Next.js/Fastify affiliate meta-search monorepo. Treat it as an integration layer, not a replacement for the WordPress product baseline, unless a documented architecture decision changes that boundary.

## Mandatory Pre-Implementation Research

Before coding any product feature, consult the relevant official documentation and name it in the implementation summary.

At minimum, use the relevant sections from:

- WordPress Plugin Developer Handbook: `https://developer.wordpress.org/plugins/`
- WordPress Plugin Security Handbook: `https://developer.wordpress.org/plugins/security/`
- WordPress REST API Handbook: `https://developer.wordpress.org/rest-api/`
- WordPress Settings API Handbook: `https://developer.wordpress.org/plugins/settings/settings-api/`
- WordPress Custom Post Types Handbook: `https://developer.wordpress.org/plugins/post-types/`
- WordPress Creating Tables with Plugins: `https://developer.wordpress.org/plugins/creating-tables-with-plugins/`
- WordPress Cron Handbook: `https://developer.wordpress.org/plugins/cron/`

If working on AI orchestration, structured outputs, agents, tools, or provider abstractions, also consult:

- AI SDK documentation: `https://ai-sdk.dev/docs`
- Tool calling: `https://ai-sdk.dev/docs/ai-sdk-core/tools-and-tool-calling`
- Structured data generation: `https://ai-sdk.dev/docs/ai-sdk-core/generating-structured-data`

If using Travelpayouts or another provider, consult that provider's official documentation before implementation.

Every implementation summary and phase review must include:

```text
Research consulted:
- ...
```

Do not rely on memory alone for WordPress APIs, security practices, REST behavior, database migrations, cron behavior, settings handling, AI SDK behavior, or third-party provider behavior.

## Phase Alignment and State Reconciliation

Before starting any phase or feature task:

1. Read the relevant phase section in `.plan/phased-implementation.md`.
2. Inspect the current codebase for completed work, partial work, regressions, renamed contracts, and deviations from earlier plans.
3. Confirm that the task still aligns with the actual codebase state.
4. Verify prerequisite phases are complete enough for the current phase.
5. Check whether previous fixes changed planned architecture, data models, route names, capabilities, settings, hooks, provider contracts, or service boundaries.
6. If the phase prompt conflicts with implementation reality, adjust the implementation approach to the codebase and document the discrepancy before coding.

Never blindly implement from an old plan if the codebase has evolved.

## Phase Completion Gate

A phase is not complete when code is written. A phase is complete only after implementation, review, validation, and documentation are complete.

Before marking any phase `Completed`, perform and document:

- scope review against objective, requirements, deliverables, prerequisites, blockers, and acceptance criteria
- functional happy-path checks
- error, empty-state, missing-configuration, and permission-failure checks
- security and data review
- regression review for earlier phases touched by the work
- plugin activation check when plugin code changes
- REST permission checks for new or changed endpoints
- migration/table verification when schema changes
- UI review for admin screens, frontend surfaces, shortcodes, or blocks
- documentation of bugs found, fixed, or deferred

Allowed phase statuses:

- `Not Started`
- `In Progress`
- `In Review`
- `Completed`
- `Blocked`

Move a phase to `Completed` only after the review gate passes.

## Linear Synchronization

Linear is the project-tracking source of truth for active phase work. When a phase, subphase, or implementation task starts, update the matching Linear issue and the Bookings and Flights Linear project to `In Progress` in the same work session. When the work passes its completion gate, update the matching Linear issue to `Done` and add a concise comment with the branch, commit or PR if available, validation performed, and any remaining blockers.

Do not leave a local phase status ahead of Linear. If a PR, merge, GitHub remote, review, validation, or environment blocker prevents full completion, keep the affected Linear issue or project in progress and document the blocker in a Linear comment.

## Architecture and Naming Contracts

Canonical contracts are recorded in `.plan/architecture-baseline.md`. Do not rename shared contracts without documenting the reason and reconciling affected phases.

Current primary contracts:

- Product: Bookings and Flights
- Planned core plugin slug: `bookings-flights-core`
- Existing affiliate bridge plugin: `bookings-and-flights-affiliate-bridge`
- Existing content manager plugin: `bookings-and-flights-content-manager`
- Existing static theme: `bookings-and-flights-static`
- Existing platform monorepo: `platform/`
- PHP namespace/prefix for new core plugin: `BAF\Core` and `baf_`
- Existing affiliate bridge namespace: `BAF\AffiliateBridge`
- REST namespace: `baf/v1`
- Planned CPT keys: `destination`, `route`, `travel_deal`, `trip_plan`, `travel_partner`, `travel_alert`
- Planned table prefix: `$wpdb->prefix . 'bf_'`
- Existing key options: `baf_supplier_credentials`, `baf_search_api_url`, `baf_postback_secret`

Future agents must not rename CPTs, taxonomies, REST namespaces, table names, post meta keys, option keys, capability names, route contracts, asset handles, cron hooks, blocks, shortcodes, or provider IDs without updating `.plan/architecture-baseline.md`, `.plan/decisions.md`, and affected phase notes.

## Code Size and Modularity

No source file may exceed 600 lines of code. If a source file approaches 500 lines, split it before adding substantial logic.

Prefer small, focused modules:

- plugin bootstrap
- activation/deactivation/uninstall
- CPT registration
- taxonomy registration
- REST controllers
- services
- repositories/data access
- settings
- admin UI enqueue/bootstrap
- provider adapters
- validation and sanitization helpers
- capability helpers
- template/rendering code
- cron/background job handlers
- asset/build tooling

Do not place unrelated features in the same file for convenience.

## WordPress Coding Standards

- Use tabs for PHP indentation.
- Use WordPress spacing style: `if ( $condition ) {`.
- Prefer Yoda conditions where practical: `if ( true === $value ) {`.
- Use strict comparisons.
- Prefix PHP functions, hooks, options, transients, cron events, asset handles, capabilities, REST fields, post meta, user meta, shortcodes, and block names.
- Use `wp_date()` instead of PHP `date()`.
- Use WordPress APIs instead of raw superglobals or SQL when a safe API exists.
- Use `$wpdb->prepare()` for all dynamic custom SQL.
- Use `wp_unslash()` before sanitizing data from WordPress superglobals.
- Sanitize input on write.
- Escape output on render.
- Use nonces for admin and frontend write actions.
- Check capabilities before protected reads, writes, exports, approval actions, publishing actions, settings changes, AI generation, provider calls, or private data access.

## Quality Standards

All code must be scalable, maintainable, secure, and performant.

Required practices:

- keep functions and classes focused on one responsibility
- prefer explicit dependencies over hidden globals where practical
- avoid duplicating business logic across REST controllers, admin screens, AJAX handlers, cron callbacks, blocks, shortcodes, and services
- validate external API responses before storage
- fail safely with actionable errors
- avoid storing sensitive prompts, provider keys, tokens, or private customer data in logs
- add extension points for providers and integrations without overbuilding early phases
- keep generated content editable
- never auto-publish without explicit approval and permissions

## WordPress Security Requirements

Every implementation phase must follow WordPress security best practices.

Mandatory requirements:

- add a real `permission_callback` to every REST route
- check capabilities before reading, writing, generating, approving, publishing, exporting, viewing private data, or changing settings
- use nonces for logged-in admin and frontend requests
- sanitize all input on write
- escape all output on render
- use `wp_unslash()` before sanitizing request data from WordPress superglobals
- store options through the Settings API or a documented equivalent
- use prepared SQL for custom queries
- restrict provider/API-key settings to administrators or the agreed capability
- never expose API keys in HTML, JavaScript, REST responses, logs, admin notices, or errors
- require consent before sending website, brand, customer, content, analytics, or private site data to external providers
- treat business, user, customer, analytics, billing, and private content data as private by default

## REST API Requirements

REST controllers must be thin. Business rules belong in services. Data access belongs in repositories or dedicated helpers.

Every endpoint must:

- use the documented `baf/v1` REST namespace unless a new namespace is documented first
- declare a real `permission_callback`
- validate and sanitize request parameters
- return `WP_REST_Response`, `WP_Error`, or equivalent WordPress-native responses consistently
- avoid exposing private data to unauthorized users
- support clear error responses for missing records, invalid state transitions, malformed provider output, missing configuration, and missing consent
- paginate list endpoints
- avoid unbounded queries

## Database and Data Model Requirements

WordPress is the canonical system of record. Use CPTs and post meta for WordPress-native entities unless the phase explicitly calls for custom tables.

Use custom tables for high-volume, relational, analytics, logging, queue, alert, AI session, click, cached offer, or integration records when appropriate and documented.

Database changes must:

- use `$wpdb->prefix`
- use the site's charset and collation
- use `dbDelta()` or the agreed migration strategy
- be idempotent
- preserve existing data
- include validation steps in phase review
- use prepared queries for custom SQL
- avoid destructive migrations unless explicitly approved

## Settings Requirements

Settings must:

- use the WordPress Settings API or a documented equivalent
- sanitize values before saving
- avoid exposing secrets in rendered HTML, JavaScript, REST responses, logs, or admin notices
- mask secret values in UI
- restrict sensitive settings to administrators or documented capabilities
- document option names in `.plan/architecture-baseline.md`

## Admin UI Requirements

Admin UI should feel like a polished WordPress product, not a generic settings dump.

UI work must:

- use scoped styles to avoid WordPress admin, theme, or plugin conflicts
- escape rendered data
- include loading, empty, error, and success states
- keep generated content editable
- gate destructive, approval, publishing, billing, and integration actions behind permissions and confirmations
- gracefully handle missing configuration, empty data, and failed API responses
- preserve accessibility and responsive behavior
- use nonces for admin and frontend actions
- enqueue assets only where needed

## AI Implementation Requirements

AI features must use a provider abstraction. Do not scatter provider calls across REST controllers, admin pages, frontend code, or cron callbacks.

Required AI rules:

- demo mode must remain available for local development and sales demos
- live AI requests must be logged through an agreed AI run logging system
- structured outputs must be schema-validated before saving
- freeform model output should be limited to final copy fields
- AI may recommend or prepare actions, but WordPress capability checks and approval workflows must execute actions
- the model must not directly publish content
- provider errors must not leak secrets, raw prompts, or sensitive customer data
- prompt, schema, and tool-calling changes must be version-aware or documented clearly
- require consent before sending site, business, brand, user, customer, analytics, or private content data to external AI providers

## Performance Requirements

Implementation must avoid unnecessary load on WordPress admin, frontend, and external APIs.

Required practices:

- do not perform long-running provider calls, ingestion, publishing, analytics syncs, or large migrations during normal page render
- use background jobs for durable generation, publishing, sync, import, export, retry, and alert workflows when required
- cache or persist expensive derived data where appropriate
- paginate list endpoints
- avoid unbounded queries
- avoid loading large prompts, source documents, analytics payloads, or generated assets unless needed
- enqueue scripts/styles only on relevant admin or frontend screens

## Validation Requirements

Before ending any code-changing task, run the most targeted validation available for the changed area.

Minimum expectations when applicable:

- PHP syntax check for changed PHP files
- plugin activation check for bootstrap, migration, CPT, settings, hook, or dependency changes
- REST route smoke test for endpoint changes
- permission failure test for protected endpoints
- migration/table verification when database schema changes
- build/lint/test command for frontend or Node changes
- manual admin/UI review for UI-facing changes

Maintain known validation commands in `.plan/validation-baseline.md`.

If a validation command is unavailable, document the reason and any manual checks performed.

## Troubleshooting and Issue Resolution Workflow

Agents are expected to be resourceful troubleshooters for this WordPress environment. When an error, broken UI flow, failed request, activation problem, PHP warning, JavaScript error, REST failure, or unexpected behavior is encountered, investigate and resolve the root cause instead of stopping at the symptom.

When troubleshooting, agents should:

1. Reproduce the issue in the local WordPress environment whenever feasible.
2. Check WordPress and PHP error logs, including `debug.log` in `wp-content/` when available, without exposing secrets or private data in summaries.
3. Use browser automation through the Playwright MCP or available browser tooling for admin UI, frontend UI, console, network, and REST-flow inspection.
4. Inspect relevant plugin, theme, `mu-plugins/`, REST, AJAX, cron, database, and platform integration code paths.
5. Identify the contributing factors and root cause before changing code.
6. Fix the issue in the responsible code path with the smallest maintainable change.
7. Re-run the failing flow, targeted validation, and any relevant permission or regression checks.
8. Document bugs found, bugs fixed, bugs deferred, contributing factors, validation performed, and any regression-watchlist updates in `.plan/` when the issue affects phase scope or future work.

Do not ignore warnings or errors that are directly related to the current task. If an issue cannot be fully resolved in the current scope, document the blocker, impact, workaround, and recommended next fix in `.plan/known-issues.md`.

## Documentation Requirements

Keep project documentation useful for downstream phases.

Update documentation when:

- a phase starts, enters review, completes, or becomes blocked
- shared contracts change
- new routes, tables, capabilities, settings, hooks, cron jobs, blocks, shortcodes, or integration boundaries are added
- bugs or regressions change expected behavior of later phases
- validation commands are discovered or changed
- architecture decisions are made

Documentation must be concise, factual, and tied to current codebase state.

## Out of Scope Without Explicit Approval

Do not implement these without explicit scope or phase authorization:

- destructive data migrations
- large framework rewrites
- direct auto-publishing without human approval
- external publishing integrations before their planned phase
- SaaS billing or payment integrations before their planned phase
- renaming core plugin contracts after downstream phases depend on them
- storing raw sensitive prompts or private customer data by default
- removing established security gates
- replacing the architecture baseline without documenting the reason

## Recommended Future Skills and Workflows

Recommended reusable skills for this project:

- `wordpress-plugin-architect`
- `wordpress-security-reviewer`
- `wordpress-rest-controller-builder`
- `wordpress-phase-reviewer`
- `wordpress-db-migration-reviewer`
- `wordpress-admin-ui-reviewer`
- `ai-wordpress-integration-reviewer`

Until those exist, use the documented phase prompts in `.plan/phased-implementation.md`, the review gate in `.plan/phase-review-log.md`, and available general skills for deep research, responsive design, frontend design, browser/Playwright testing, and web app testing.
