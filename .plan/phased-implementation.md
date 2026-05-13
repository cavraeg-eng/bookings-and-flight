# Phased Implementation Plan

## Phase 0: Architecture Baseline and Plugin Skeleton

Status: `Completed`

Objective: Establish the project operating system, canonical contracts, plugin skeleton, activation/deactivation hooks, validation baseline, and documentation baseline.

Scope:

- Review existing plugins, themes, and `platform/`.
- Create or confirm the planned `bookings-flights-core` skeleton.
- Register no product features beyond bootstrap, constants, autoloading, and safe hooks.
- Confirm naming contracts and validation commands.

Prerequisites:

- `.plan/project-overview.md`
- `.plan/architecture-baseline.md`
- `AGENTS.md`

Deliverables:

- Core plugin skeleton if not already present.
- Activation/deactivation hooks.
- File structure for services, repositories, REST controllers, settings, CPTs, and migrations.
- Documentation updates for discovered contracts.

Files likely to change:

- `plugins/bookings-flights-core/bookings-flights-core.php`
- `plugins/bookings-flights-core/includes/`
- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/decisions.md`

Implementation prompt:

```markdown
Implement Phase 0 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 0, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 0 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Naming contracts are documented.
- Plugin skeleton activates without fatal errors.
- No provider secrets are exposed.
- No product workflow is implemented prematurely.
- Validation commands are documented.

Validation checklist:

- PHP syntax check for changed PHP files.
- Plugin activation check.
- Confirm no REST routes were added without permission callbacks.
- Confirm docs reflect current contracts.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/decisions.md`

## Phase 1: Core Data Model

Status: `Completed`

Objective: Register foundational CPTs, taxonomies, post meta contracts, capabilities, and data access helpers.

Scope:

- CPTs: `destination`, `route`, `travel_deal`, `trip_plan`, `travel_partner`, `travel_alert`.
- Taxonomies: `travel_region`, `travel_style`, `travel_vertical`, `travel_season`.
- Capability mapping.
- Repository/service stubs for reading/writing travel entities.

Prerequisites:

- Phase 0 completed or enough skeleton exists to register WordPress components safely.

Deliverables:

- CPT registration module.
- Taxonomy registration module.
- Capability registration module.
- Meta key documentation.
- Repository/data access helper boundaries.

Files likely to change:

- `plugins/bookings-flights-core/includes/post-types/`
- `plugins/bookings-flights-core/includes/taxonomies/`
- `plugins/bookings-flights-core/includes/capabilities/`
- `.plan/architecture-baseline.md`

Implementation prompt:

```markdown
Implement Phase 1 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 1, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 1 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- CPTs and taxonomies register with documented keys.
- Capabilities are documented and mapped safely.
- Rewrite behavior is activation-safe.
- No unbounded queries or direct SQL are introduced.

Validation checklist:

- PHP syntax check.
- Plugin activation check.
- Confirm CPT/taxonomy registration in admin or WP-CLI.
- Confirm capabilities are not broader than documented.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/regression-watchlist.md`

## Phase 2: Settings and Admin Foundation

Status: `Completed`

Objective: Create polished, secure admin settings and integration status foundations.

Scope:

- Admin menu structure.
- Settings API registrations.
- Provider credential UI with masked secrets.
- Integration status and missing-configuration states.
- Asset enqueueing only on relevant screens.

Prerequisites:

- Phase 0 baseline.
- Existing affiliate bridge settings reconciled.

Deliverables:

- Admin dashboard/settings pages.
- Settings schemas and sanitizers.
- Capability gates and nonces.
- Missing configuration and success/error states.

Files likely to change:

- `plugins/bookings-flights-core/includes/admin/`
- `plugins/bookings-flights-core/includes/settings/`
- `plugins/bookings-and-flights-affiliate-bridge/`
- `.plan/architecture-baseline.md`

Implementation prompt:

```markdown
Implement Phase 2 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 2, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 2 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Sensitive settings are administrator-gated.
- Secrets are masked and never exposed to frontend or REST.
- Admin UI has clear empty/error/success states.
- Settings are sanitized on save.

Validation checklist:

- PHP syntax check.
- Plugin activation check.
- Manual admin settings save test.
- Permission failure check for insufficient users.
- Confirm frontend source contains no secrets.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/phase-review-log.md`

## Phase 3: REST API Foundation

Status: `Completed`

Objective: Establish thin REST controller structure, permission callbacks, request schemas, response conventions, and route smoke tests.

Scope:

- Controller base patterns.
- Permission helpers.
- Request validation/sanitization helpers.
- Response/error conventions.
- Initial read-only list routes for destinations/routes where data exists.

Prerequisites:

- Phase 0 baseline.
- Phase 1 data model if CPT routes are included.

Deliverables:

- REST controller modules.
- Permission callbacks for every route.
- Route documentation.
- Smoke and permission failure validation notes.

Files likely to change:

- `plugins/bookings-flights-core/includes/rest/`
- `plugins/bookings-flights-core/includes/services/`
- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`

Implementation prompt:

```markdown
Implement Phase 3 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 3, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 3 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Every route has a real permission callback.
- List endpoints paginate.
- Request parameters are validated and sanitized.
- Private data is not exposed.

Validation checklist:

- PHP syntax check.
- Plugin activation check.
- REST route smoke test.
- REST permission failure test.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/phase-review-log.md`

## Phase 4: Primary Product Workflow

Status: `Completed`

Objective: Implement the first end-to-end affiliate travel workflow using services and repositories.

Scope:

- Search or destination-to-affiliate-card workflow.
- SubID generation.
- Affiliate disclosure.
- Click handoff tracking.
- Saved trip or alert capture if prerequisites are ready.

Prerequisites:

- REST foundation.
- Settings/admin foundation.
- Provider configuration boundary.

Deliverables:

- User-facing workflow.
- Service-layer affiliate card/link generation.
- Safe tracking records.
- Editable WordPress content integration.

Files likely to change:

- `plugins/bookings-flights-core/includes/services/`
- `plugins/bookings-flights-core/includes/rest/`
- `plugins/bookings-flights-core/includes/frontend/`
- `themes/bookings-and-flights-static/` if using theme templates
- `platform/` only if the workflow crosses into the existing monorepo

Implementation prompt:

```markdown
Implement Phase 4 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 4, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 4 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Workflow works without direct checkout or payment handling.
- Affiliate links include documented SubID behavior.
- Affiliate disclosure is visible where monetized cards/links appear.
- Missing provider configuration fails gracefully.

Validation checklist:

- PHP syntax check.
- REST smoke and permission tests if routes change.
- Manual happy-path click-through test.
- Missing-configuration test.
- Confirm no secrets in frontend output.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/regression-watchlist.md`

## Phase 5: Background Jobs and Automation

Status: `Completed`

Objective: Add safe background processing for cached offers, alerts, provider stats, retries, and cleanup.

Scope:

- WP-Cron hooks.
- Queue or status tracking where needed.
- Retry and failure handling.
- Non-render execution for long-running work.

Prerequisites:

- Core data model.
- Settings foundation.
- Any required custom tables documented and reviewed.

Deliverables:

- Cron registration and unscheduling.
- Background job handlers.
- Admin/status visibility for jobs.
- Validation for scheduled events.

Files likely to change:

- `plugins/bookings-flights-core/includes/cron/`
- `plugins/bookings-flights-core/includes/jobs/`
- `plugins/bookings-flights-core/includes/tables/`
- `.plan/architecture-baseline.md`

Implementation prompt:

```markdown
Implement Phase 5 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 5, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 5 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Long-running work is not triggered during render.
- Cron hooks are registered and unscheduled safely.
- Failures are logged without secrets.
- Retry behavior is bounded.

Validation checklist:

- PHP syntax check.
- Plugin activation/deactivation checks.
- Confirm scheduled hooks.
- Manual or WP-CLI cron smoke test if available.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/phase-review-log.md`

## Phase 6: AI and External Provider Integration

Status: `Completed`

Objective: Add AI provider abstraction, demo mode, structured itinerary generation, Travelpayouts/provider tools, run logging, and consent gates.

Scope:

- AI provider interface.
- Demo provider.
- Live provider adapter only when credentials/configuration are available.
- Structured output validation.
- Tool-calling boundaries.
- AI run/session records.
- Consent before sending private data externally.

Prerequisites:

- Settings foundation.
- REST foundation.
- Primary workflow or travel card service boundary.

Deliverables:

- AI abstraction.
- Demo mode.
- Structured itinerary schema.
- Safe error handling.
- AI session/run logging.

Files likely to change:

- `plugins/bookings-flights-core/includes/ai/`
- `plugins/bookings-flights-core/includes/providers/`
- `plugins/bookings-flights-core/includes/rest/`
- `platform/` if AI middleware is implemented there
- `.plan/architecture-baseline.md`

Implementation prompt:

```markdown
Implement Phase 6 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 6, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 6 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Demo mode works without live credentials.
- Live provider calls require explicit configuration and consent.
- Structured output is schema-validated before save.
- AI cannot auto-publish.
- Provider errors do not leak secrets or raw private prompts.

Validation checklist:

- PHP syntax and/or TypeScript typecheck.
- REST smoke and permission tests.
- Demo generation test.
- Missing-key and missing-consent tests.
- Secret exposure review.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/validation-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/known-issues.md` if provider limits are discovered

## Phase 7: UI Polish, Accessibility, and Responsive Behavior

Status: `Completed`

Objective: Improve admin/frontend UI quality, accessibility, responsive behavior, and state handling.

Scope:

- Admin UI polish.
- Frontend blocks/shortcodes/search UI polish.
- Loading, empty, error, and success states.
- Keyboard and screen reader behavior.
- Mobile-first responsive adjustments.

Prerequisites:

- UI surfaces from prior phases.

Deliverables:

- Polished UI components.
- Scoped styles.
- Accessibility improvements.
- Manual UI review notes.

Files likely to change:

- `plugins/bookings-flights-core/assets/`
- `plugins/bookings-flights-core/includes/admin/`
- `themes/bookings-and-flights-static/`
- `platform/apps/web/` if platform UI is in scope

Implementation prompt:

```markdown
Implement Phase 7 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 7, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 7 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- UI handles loading, empty, error, and success states.
- Styles are scoped.
- Keyboard navigation and accessible labels are reviewed.
- Responsive behavior is checked on common breakpoints.

Validation checklist:

- PHP syntax or TypeScript typecheck for changed code.
- Frontend build/typecheck when applicable.
- Manual browser/admin UI review.
- Responsive review.

Documentation updates required:

- `.plan/phase-review-log.md`
- `.plan/regression-watchlist.md`

## Phase 8: Analytics, Reporting, and Optimization

Status: `Completed`

Objective: Add reporting for searches, clicks, AI sessions, alerts, providers, conversion signals, and content performance.

Scope:

- Reporting data model.
- Admin dashboards.
- Provider stats sync.
- Revenue and conversion summaries where data is available.
- A/B testing foundations only if explicitly approved.

Prerequisites:

- Click/search/alert/session tracking foundations.
- Provider or postback data availability.

Deliverables:

- Reporting repositories/services.
- Admin reports.
- Export or summary views.
- Privacy-aware analytics handling.

Files likely to change:

- `plugins/bookings-flights-core/includes/reports/`
- `plugins/bookings-flights-core/includes/admin/`
- `plugins/bookings-flights-core/includes/tables/`
- `.plan/architecture-baseline.md`

Implementation prompt:

```markdown
Implement Phase 8 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 8, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 8 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Reports are capability-gated.
- Queries are bounded and paginated.
- Private user data is minimized.
- Provider stats failures are handled safely.

Validation checklist:

- PHP syntax check.
- Migration/table verification if schema changes.
- Admin report permission test.
- Query performance review.

Documentation updates required:

- `.plan/architecture-baseline.md`
- `.plan/phase-review-log.md`
- `.plan/regression-watchlist.md`

## Phase 9: Hardening, Regression Review, and Release Readiness

Status: `Completed`

Objective: Prepare for release through security review, regression review, validation, documentation, and release checklist completion.

Scope:

- Security review.
- REST permission review.
- Migration review.
- Secret exposure review.
- Activation/deactivation review.
- Admin/frontend UI review.
- Documentation completeness.
- Release notes.

Prerequisites:

- Product phases ready for release review.

Deliverables:

- Completed review log.
- Known issues triaged.
- Regression watchlist updated.
- Release readiness checklist.

Files likely to change:

- `.plan/phase-review-log.md`
- `.plan/known-issues.md`
- `.plan/regression-watchlist.md`
- `.plan/validation-baseline.md`
- release documentation if added later

Implementation prompt:

```markdown
Implement Phase 9 according to the current codebase state and the reconciled phase plan.

Requirements:
1. Consult the relevant official WordPress/provider documentation before coding.
2. Confirm applicable `AGENTS.md` instructions.
3. Keep changes minimal, modular, and consistent with existing project architecture.
4. Preserve documented naming contracts.
5. Add or update documentation for new contracts, routes, tables, options, capabilities, hooks, and validation commands.
6. Run targeted validation before finishing.
7. Do not mark the phase complete until the phase review gate passes.

Return:
- Summary of changes
- Files changed
- Research consulted
- Validation performed
- Documentation updated
- Known issues or deferred items
```

Consultant/alignment prompt:

```markdown
Before implementing Phase 9, reconcile the phase plan with the current codebase.

Review:
1. The original phase objective.
2. Completed work from prior phases.
3. Current architecture and naming contracts.
4. Existing files, classes, hooks, routes, capabilities, tables, settings, and UI surfaces.
5. Any deviations from the original plan.
6. Any regressions or partial implementations.
7. Whether prerequisites are actually complete.
8. Whether the planned implementation should be adjusted.

Do not code yet.

Return:
- Current state summary
- Plan alignment assessment
- Risks or discrepancies
- Recommended implementation approach
- Files likely to change
- Validation required
- Documentation updates required
```

Review prompt:

```markdown
Review Phase 9 against the project plan and current codebase.

Check:
1. Objective completion
2. Deliverables
3. Acceptance criteria
4. Security
5. REST permissions
6. Database/migration safety
7. Admin UI behavior
8. Error, empty, loading, and success states
9. Regression risk
10. Documentation updates
11. Validation results

Return:
- Pass/fail status
- Bugs found and fixed
- Bugs deferred
- Security notes
- Validation performed
- Documentation updated
- Whether the phase can move to `Completed`
```

Acceptance criteria:

- Security review passes or blockers are documented.
- Protected routes and actions have permission-failure tests.
- Activation/deactivation checks pass.
- Known issues are triaged.
- Release blockers are explicit.

Validation checklist:

- PHP syntax checks.
- Plugin activation/deactivation checks.
- REST smoke and permission tests.
- Migration/table verification.
- Frontend build/typecheck where applicable.
- Manual admin/frontend UI review.

Documentation updates required:

- `.plan/phase-review-log.md`
- `.plan/known-issues.md`
- `.plan/regression-watchlist.md`
- `.plan/validation-baseline.md`

## Phase 10: Travelpayouts-Controlled Booking Site Blueprint

Status: `Completed`

Objective: Reconcile the new product direction into a WordPress-native architecture blueprint and detailed execution plan where the frontend competes with top booking sites while Travelpayouts controls monetized search, widgets, White Label results, partner links, and booking handoff.

Scope:

- Document the Travelpayouts-controlled backend boundary.
- Define the competitive frontend site map and page templates.
- Define the homepage, flights, hotels, destination, route, deals, AI planner, saved trips, and alert experiences.
- Define how the official Travelpayouts WordPress plugin, widgets, White Label, SubIDs, and fallback embeds should be used.
- Add future implementation phases for compatibility, design system, widget registry, homepage rebuild, flights, hotels, SEO pages, AI handoff, retention, analytics, and release readiness.

Deliverables:

- `.plan/travelpayouts-wordpress-booking-site-blueprint.md`
- Architecture baseline updates for the Travelpayouts-controlled backend boundary.
- Decision log entry for Travelpayouts as the booking/search backend.

Validation performed:

- Documentation-only change.
- Current `.plan/` baseline reviewed.
- Current plugins, theme, and platform boundaries inspected.
- Official WordPress and Travelpayouts documentation consulted.

Research consulted:

- WordPress Plugin Developer Handbook.
- WordPress Plugin Security Handbook.
- WordPress REST API Handbook.
- WordPress Theme Developer Handbook.
- WordPress Block Editor Handbook.
- Travelpayouts WordPress plugin documentation.
- Travelpayouts widget, White Label, and SubID documentation.
- WordPress.org Travelpayouts plugin listing.
- Current competitive references from Booking.com, Expedia, Google Flights, Skyscanner, and KAYAK.

Decision: Phase 10 is a planning and documentation phase only. Product implementation should proceed through Phase 11+ below.

## Phase 11: Travelpayouts Compatibility and Backend Alignment

Status: `Completed`

Objective: Confirm the official Travelpayouts WordPress plugin or fallback Travelpayouts embed path works safely in this WordPress environment.

Scope:

- Install or stage the official Travelpayouts plugin.
- Verify plugin activation/deactivation on the local WordPress version.
- Configure Token, Partner ID, traffic source, and optional White Label URL through a documented safe setup process.
- Render test flight and hotel widgets/forms.
- Verify whether White Label result pages can preserve the Bookings and Flights header through Widget type embedding or require Page-type header customization.
- Document required White Label header assets and settings: logo URL, favicon URL, brand name, header background, menu links, footer links, and search-heading copy.
- Confirm whether production should use plugin-first placement or dashboard-generated embed fallback.
- Document the search surface mode options discovered by compatibility testing: official plugin widget, dashboard embed, White Label widget, Page-type White Label, safe redirect metadata, or explicitly approved `platform/` Adapter.

Acceptance criteria:

- Travelpayouts plugin or fallback path is confirmed.
- Widgets render on desktop and mobile.
- Generated links preserve Partner ID/SubID behavior where supported.
- White Label search/results pages match the home-site header treatment as closely as Travelpayouts Widget/Page customization allows.
- Search surface mode is documented without introducing a custom live inventory endpoint.
- No secrets appear in frontend HTML, JavaScript, REST responses, logs, or admin notices.
- No custom search inventory backend is introduced.

Validation checklist:

- Plugin activation/deactivation check. P11.1 passed locally on 2026-05-09 with official `travelpayouts` plugin version `1.2.2` on WordPress `6.9.4`; P11.6 re-ran deactivate/reactivate on 2026-05-12 and returned the plugin to `Status: Active`.
- Admin setup smoke check. P11.2 passed locally on 2026-05-12 with temporary-token missing/configured option checks, saved-token masking, blank-submission preservation, option sanitization, and non-secret Gutenberg token-route state.
- Frontend widget render smoke check. P11.3 and P11.6 passed for the official flight widget on desktop and mobile; official hotel widget/table shortcodes rendered empty because the plugin's legacy HotelLook availability gate is disabled. Travelpayouts documentation now treats Hotellook tools as shut down, so hotel surfaces use the dashboard-generated Trip.com or other Hotels & Accommodation brand widget/link path. The published Flights page now renders the Widget-type White Label search/results containers, and the published Hotels page renders a Trip.com partner iframe plus a visible sponsored handoff link. The final consent-enabled validation also removed visible footer placeholder defaults and confirmed no page-level mobile overflow on the hotel widget surface.
- Widget handoff/White Label smoke check. P11.3 and P11.6 confirmed the flight widget source hands off to Travelpayouts/Aviasales-controlled results by default. The final P11.6 browser pass confirmed the Flights page loads without PHP deprecation output and the Hotels page opens the Trip.com partner search form from the visible handoff button.
- White Label header continuity check against the WordPress homepage header. P11.4 documents Widget type as the preferred continuity path because it keeps the WordPress home shell; Page type is allowed only with Travelpayouts dashboard header customization that mirrors the WordPress logo, favicon, brand name, nav/footer links, colors, and route back to the main site.
- Backend mode decision. P11.5 documented Travelpayouts-controlled backend mode: official-plugin-first only for validated surfaces, dashboard-generated Travelpayouts fallback embeds for inactive or unvalidated hotel and White Label surfaces, and `/search/flights` plus `/search/hotels` limited to safe shell/configuration/placement/handoff metadata instead of live inventory APIs.
- Secret exposure review. P11.3 and P11.6 browser/source scans passed for temporary and final pages: no API token, postback secret, authorization string, checkout, payment, refund, or direct WordPress booking flow appeared in WordPress output.
- Completion gate. Phase 11 moved to `Completed` after PR #6, PR #7, and the final footer/mobile rendering follow-up were reviewed, merged, and synced back to Linear. The consent-enabled browser/source validation gate passed on 2026-05-12 for the published Flights and Hotels pages, with the known note that Trip.com iframe content can be blank under browser content blockers or visually compressed inside the provider iframe on narrow screens, so the hotel page keeps a visible handoff button.

## Phase 12: Information Architecture and Competitive Design System

Status: `Completed`

P11.6 note: minimal White Label Widget ID and Trip.com/Hotels widget script settings plus `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes were added early to unblock setup. Phase 13 still owns the full governed placement registry, SubID/disclosure behavior, block wrapper, and broader safe embed layer.

P12.1 note: sitemap, navigation, page ownership, SEO page families, and current-template conflicts are documented in `.plan/phase-12-sitemap-navigation-page-ownership.md`. The current live primary menu still reflects the older boilerplate page set and must be updated in a later implementation ticket before the public visual rebuild is treated as complete.

P12.2 note: design-token direction, media strategy, component inventory, disclosure treatment, accessibility requirements, and initial widget-frame guardrails are documented in `.plan/phase-12-design-system-component-inventory.md`. The current implementation still needs Phase 12.3 CSS split preparation before large public UI work, and Phase 12.4 still owns detailed widget-frame loading and responsive rules.

P12.3 note: CSS split and theme architecture preparation is documented in `.plan/phase-12-css-split-theme-architecture.md`. Shared `.skip-link` and `.btn` primitives moved from `header.css` into the new `components.css` layer, reducing `header.css` from 544 to 464 lines and preserving the enqueue order `fonts -> tokens -> base -> components -> header -> mobile-nav -> footer -> page-specific`.

P12.4 note: Travelpayouts widget frame dimensions, responsive constraints, state model, disclosure placement, performance rules, and Phase 13 registry prerequisites are documented in `.plan/phase-12-widget-frame-layout-rules.md`. Codex PR review found no major issues on PR #12. Future template and registry work should use those reservations instead of inventing per-widget layout behavior.

P12.5 note: Page-level structured wireframes for home, flights, hotels, explore, destination detail, route detail, deals, AI planner, saved trips, about/legal, and admin widget placement surfaces are documented in `.plan/phase-12-page-level-wireframes.md`. Codex PR review found no major issues on PR #13. Future visual/template work should use the owner/template/phase mapping and browser screenshot plan in that document.

P12.6 note: Final Phase 12 review, deferred runtime implementation risks, CSS file-size review, documentation completeness, Phase 13 start checklist, runtime screenshots, and keyboard navigation review are documented in `.plan/phase-12-completion-gate.md`. Codex PR review on PR #14 found a P2 consistency issue because browser screenshots and keyboard review had not yet run; the follow-up runtime pass executed those checks, fixed the widget keyboard-focus bug found during review, and completed Phase 12.

Objective: Rework the public WordPress frontend architecture so it feels like a modern travel search product rather than a static affiliate brochure.

Scope:

- Finalize navigation and site map.
- Define the design system, media strategy, component inventory, and responsive behavior.
- Split oversized theme CSS before major additions.
- Define the brand continuity source for logo, favicon, brand name, header colors, nav links, footer links, disclosure copy, and White Label heading copy.
- Plan the content manager field-pipeline split needed for large template work: field rendering, field persistence, media portability, schema export/import, and admin notices.
- Design homepage, flights, hotels, destination, route, deals, AI planner, saved trips, and legal templates.

Acceptance criteria:

- First viewport clearly supports flights and hotels.
- Real travel media replaces gradient-only hero treatment.
- Search controls are dense, accessible, mobile-first, and compatible with Travelpayouts widgets.
- Affiliate handoff and disclosures are visible.
- Header and White Label continuity inputs are defined once and reused by later phases.
- Content manager refactor needs are documented before template scope expands.

Validation checklist:

- Browser screenshots at mobile, tablet, and desktop sizes.
- Keyboard navigation review.
- CSS file-size review.
- No text overlap or clipping.

## Phase 13: Travelpayouts Widget Registry and Safe Embed Layer

Status: `Completed`

Objective: Provide governed WordPress-native placement of Travelpayouts widgets, tables, links, and White Label surfaces.

Scope:

- Add capability-gated widget placement registry.
- Add SubID builder and placement metadata.
- Add shortcode/block wrapper for approved Travelpayouts placements.
- Add configured, missing, disabled, loading, no-script, and error states.
- Deepen the registry so callers do not need to know raw embed code, consent state, disclosure rules, SubID construction, official-plugin versus fallback mode, or safe output details.
- Add a search surface mode seam for Travelpayouts widget, White Label page, safe redirect metadata, and explicitly approved integration Adapters.

Acceptance criteria:

- Only authorized users can manage raw widget/embed code.
- Public output includes only approved widget placements and disclosures.
- SubIDs follow the documented convention.
- Official plugin blocks remain supported where compatible.
- Search/result callers receive safe placement or handoff metadata rather than live supplier inventory.
- Registry behavior is testable through a small public Interface with permission, missing-config, and disclosure checks.

Validation checklist:

- PHP syntax checks.
- Plugin activation check.
- Admin nonce and capability checks.
- Frontend render checks.
- Secret exposure review.

P13.1 note: `BAF\Core\Services\Travelpayouts_Widget_Registry_Service` now owns the option-backed placement registry seam for `baf_travelpayouts_widget_registry`. The service seeds current Flights White Label and Hotels partner-search placements from existing Travelpayouts settings when the registry is first installed, stores sanitized metadata plus private embed references server-side, blocks admin private reads/writes without `manage_baf_affiliates` or `manage_baf_settings`, exposes an active-placement rendering read path for trusted server-side wrappers, preserves malformed stored placements during bootstrap normalization, and exposes a public projection that strips embed references, URLs, and admin notes. Admin UI, frontend wrapper rendering, disclosure state output, block support, and full security review remain in the later Phase 13 child tickets.

P13.2 note: `BAF\Core\Admin\Widget_Placements_Page` and `BAF\Core\Admin\Widget_Placement_Form` now provide the capability-gated `baf-widget-placements` admin screen for placement listing, summary status, create/edit, disable, and delete actions. The screen is available to users with `manage_baf_affiliates` or `manage_baf_settings`, uses `admin-post.php` actions with nonces, routes writes through the registry service, sanitizes posted placement fields, escapes rendered output, and keeps raw embed references/URLs out of listing tables, notices, logs, and public projections. Runtime Playwright screenshots and keyboard review covered desktop and mobile admin layouts, create/update smoke, disabled state, and form tab order. Frontend approved-placement rendering, shortcode/block wrapper integration, richer public state output, and full Phase 13 completion review remain in later Phase 13 child tickets.

P13.3 note: `BAF\Core\Frontend\Travelpayouts_Widget_Renderer` now exposes the approved-placement frontend wrapper through `[baf_travelpayouts_widget placement="..."]` and the dynamic `baf/travelpayouts-widget` editor block. The wrapper reads active placements through the registry service's trusted rendering path, keeps editor content limited to placement/context attributes, renders disclosure and safe handoff language outside provider iframes/scripts, supports configured, missing, disabled, no-script, and unavailable states, and preserves the Trip.com keyboard-reachable iframe plus visible handoff link behavior. White Label wrapper instances and the legacy White Label shortcode now use unique placeholder IDs and a shared fixed-ID claim guard so only one active `tpwl-search`/`tpwl-tickets` pair exists on a page, while additional instances render a visible unavailable fallback and skip empty placeholders in keyboard order. Existing direct setup shortcodes remain available for compatibility, but future templates should prefer the registry wrapper so callers do not need raw embed code.

P13.4 note: `BAF\Core\Services\Travelpayouts_Widget_Subid_Service` now centralizes runtime SubID generation for frontend widget placements using the `{channel}_{surface}_{vertical}_{slug}_{placement}` convention, normalizes values to lowercase Latin letters, numbers, and underscores, and applies SubIDs to provider iframe/script/handoff URLs while preserving existing Travelpayouts `marker=partner.subid` tracking where present. The frontend renderer now exposes `data-baf-state` and `data-baf-render-mode`, renders consent-disabled messaging for public users without provider requests, keeps disclosure visible for monetized states, and provides styled loading, disabled, missing-configuration, no-script, unavailable, and configured states. Dashboard-script and White Label wrappers keep loading visible until provider content mounts or a safe fallback state is reached.

P13.5 note: The Phase 13 security, capability, nonce, and exposure review passed locally for the current widget registry implementation. The review covered the registry service, placement admin screen, admin-post write actions, Settings API secret handling, shortcode/block renderer, frontend output, core `baf/v1` REST routes, and active affiliate bridge public route boundaries. Anonymous private registry reads, saves, and deletes return forbidden errors; public placement projections strip `embed.reference`, `embed.url`, and admin notes; missing admin nonces fail without creating placements; saved API keys render as masked empty password fields; frontend widget output does not expose private notes or saved secrets; and core REST routes use endpoint-specific permission callbacks. The affiliate bridge `config` and `postback` routes remain intentionally public: `config` exposes only non-secret supplier availability metadata, and `postback` rejects requests without the shared secret.

P13.6 note: The final Phase 13 review gate passed on 2026-05-12. The review reconciled P13.1 through P13.5 implementation, registry/admin/frontend/security documentation, Phase 14 consumption needs, and runtime validation. Phase 14 now has stable placement keys (`flights_white_label_search` and `hotels_partner_search`), the `[baf_travelpayouts_widget]` shortcode, the `baf/travelpayouts-widget` block, trusted server-side registry reads, public-safe placement projections, consent-disabled states, SubID metadata, visible disclosures, and keyboard-reachable handoff behavior to consume. Runtime screenshots and keyboard review confirmed desktop/mobile rendering, no horizontal overflow, configured iframe state, consent-disabled no-provider-output state, and focus through the Trip.com iframe followed by the visible hotel handoff link. No production code bug was found in P13.6; known provider-owned console/performance warnings and WP-CLI/PHP 8.5 deprecation noise remain watchlist items.

## Phase 14: Homepage Competitive Rebuild

Status: `Completed`

Objective: Replace the current placeholder homepage with a premium Travelpayouts-powered search, discovery, and planning experience.

Scope:

- Image-led hero.
- Unified Travelpayouts-powered flight/hotel search.
- Explore-anywhere and flexible-date modules.
- Trending destination/route modules.
- Hotel discovery module.
- AI planner entry.
- Price alert CTA.
- Trust and affiliate disclosure.

Acceptance criteria:

- No placeholder prices or unverified partner claims remain.
- Search actions route through the Phase 13 placement/search-surface seam to Travelpayouts-controlled widgets, White Label, or approved partner links.
- Search/result transitions preserve the Bookings and Flights header/navigation feel instead of showing an unrelated redirected-page header.
- Homepage passes mobile and desktop browser review.
- Disclosures remain visible.

Validation checklist:

- PHP syntax checks for changed templates.
- Browser screenshots across breakpoints.
- Widget render and handoff checks.
- Header continuity check for WordPress-to-White Label search/result flow.
- No secret exposure in source.

P14.1 note: `ONE-86` started on 2026-05-12. The homepage first viewport now uses local real travel media, a dense flight/hotel search shell, visible affiliate disclosure, and Phase 13 placement-key metadata (`flights_white_label_search`, `hotels_partner_search`) without copying raw provider snippets into the theme. Header and mobile navigation now fall back to the planned product sections when the stored WordPress menu is stale, and the CTA points to the homepage trip-planner entry instead of the old contact-path copy. Explore, Deals, Trip Planner, and Saved Trips remain homepage anchor entry points until their planned standalone pages are implemented in later Phase 14+ issues. Runtime Playwright screenshots and keyboard review were captured at desktop, tablet, and mobile widths with no horizontal overflow; the Codex in-app Browser surface was attempted first but had no active pane in this thread.

P14.2 note: `ONE-87` started on 2026-05-12. Flights and Hotels now have dedicated WordPress page templates that keep the Bookings and Flights header/navigation shell while rendering approved Travelpayouts placement-registry outputs instead of page-content shortcodes or custom inventory APIs. The Flights page consumes `flights_white_label_search`, preserves sanitized homepage intent details, scrubs provider query parameters before the White Label script can rewrite the page URL, and keeps a visible White Label handoff. The Hotels page consumes `hotels_partner_search`, preserves sanitized hotel intent details, and keeps the Trip.com iframe plus sponsored handoff. Runtime Playwright review captured desktop and mobile first-viewport and scrolled-widget screenshots, keyboard order, source scans, and provider handoff checks; the Codex in-app Browser surface was attempted first but had no active pane in this thread.

P14.3 note: `ONE-88` started on 2026-05-12. The homepage now includes below-hero discovery modules for trending route starters, explore-anywhere prompts, flexible-month planning, and hotel city discovery. Because there are no published `destination`, `route`, or `travel_deal` posts yet, the modules are explicitly editorial/static inspiration and route users into the approved Flights or Hotels handoff pages without static prices, fake deal claims, unsupported live availability claims, or raw provider snippets. Runtime Playwright review captured desktop, tablet, and mobile first-viewport and discovery screenshots, verified all four module groups render three cards each, confirmed keyboard reachability through route/flexible/hotel cards, and found no horizontal overflow, page errors, failed requests, or broad fake-claim/source-secret matches.

P14.4 note: `ONE-89` started on 2026-05-12. The homepage now includes a price-alert preview CTA and AI-planner placeholder entry below the discovery modules. The price-alert CTA routes to the existing Flights handoff surface and clearly states that alert capture is not active yet. The AI planner entry routes to the documented local `#trip-planner` placeholder, does not submit prompts, and does not call AI providers. No POST form, nonce-requiring write, auto-booking, auto-publishing, provider execution, or local intent capture was added. Runtime Playwright checks confirmed desktop/mobile rendering, link smoke behavior, keyboard reachability, no horizontal overflow, no browser errors, and no broad unsupported-claim/source-secret matches.

P14.5 note: `ONE-90` started on 2026-05-12. The homepage now adds a trust/disclosure row covering affiliate commission language, partner checkout/support ownership, local support, and destination-index entry. Flights and Hotels placement pages now show a local support note immediately below the approved Travelpayouts wrapper so disclosure and handoff language remain visible outside provider frames. The footer now includes an affiliate disclosure plus Terms, Privacy, Support, and Destination index links across footer layouts, with the Terms link corrected to the published `/terms-and-conditions/` page. The shared legal template now includes Travelpayouts/partner handoff, affiliate tracking, affiliate disclosure, and support-boundary language. Runtime Playwright checks confirmed homepage, footer, Flights, Hotels, Privacy, and Terms rendering, link smoke checks, keyboard reachability, no source-secret matches, no unsupported direct-OTA claims, and acceptable footer contrast. Provider-owned Travelpayouts/Chromium warnings remain on the existing watchlist; no page errors or failed requests were recorded.

P14.6 note: `ONE-91` started on 2026-05-12. The responsive/accessibility pass fixed reduced-motion mobile-menu link delays, raised the public header/menu/theme/footer legal and footer navigation targets to stable touch sizes, and scoped the official Travelpayouts WordPress plugin asset handles away from public pages that do not contain official Travelpayouts shortcodes. The Phase 13/14 registry-driven Flights and Hotels surfaces still load their approved wrapper-specific assets and provider output. Codex review follow-up broadened the asset guard so non-singular contexts keep official assets by default, active widgets are scanned for official Travelpayouts shortcodes/blocks, and template-level official output can opt in through a filter before assets are pruned. Runtime Playwright checks captured desktop, tablet, mobile, mobile-menu, reduced-motion, Flights, and Hotels screenshots; verified keyboard order through homepage navigation/search controls, mobile navigation, the Flight handoff, the Trip.com hotel iframe, and the Hotel handoff; confirmed no horizontal overflow, page errors, failed requests, framework overlays, source-secret matches, or unexpected Travelpayouts official plugin assets; and confirmed reduced-motion menu delays are `0s`. Provider-owned Flights React JSX-source warnings remain classified as existing Travelpayouts White Label runtime noise.

P14.7 note: `ONE-92` started on 2026-05-12. The final Phase 14 review gate reconciled P14.1 through P14.6 against the Phase 14 objective and acceptance criteria. Final validation confirmed the homepage now has the image-led search shell, approved flight/hotel handoff routes, discovery modules, flexible planning prompts, price-alert and AI-planner placeholders, visible trust/disclosure language, responsive/header continuity, keyboard-reachable handoffs, and script scope required before Phase 15 starts. Remaining deferred work is intentionally outside Phase 14: standalone Explore, Deals, Trip Planner, Saved Trips, seeded destination/route/deal content, real alert capture, and live AI itinerary generation. Phase 15 may now start from the completed Phase 14 homepage/search-surface baseline after this review gate merges.

## Phase 15: Flights Experience

Status: `Completed`

Objective: Build a flight-first WordPress experience using Travelpayouts for search/results/handoff.

Scope:

- Flights landing page.
- Origin and route landing pages.
- Low-price calendar and popular route widgets.
- White Label result flow.
- Price alert signup.

Acceptance criteria:

- Flight search and route pages use Travelpayouts-controlled results.
- Flexible-date and anywhere modes are honest about supported behavior.
- Alerts store local intent without claiming live fare ownership.
- WordPress SEO pages remain indexable.

Validation checklist:

- Widget and White Label handoff checks.
- Alert permission/nonce checks if implemented.
- SEO source review.
- Mobile layout review.

P15.1 note: `ONE-93` started on 2026-05-12. The dedicated Flights page now includes a local flight-intent module for origin, destination, depart date, return date, travelers, and cabin selection, while direct-only, nearby-airport, flexible-date, airline, baggage, and time filters are clearly marked as provider-controlled options to set inside the Travelpayouts White Label module. Submitted intent details are sanitized, rendered on the page, and cleaned from the visible URL before the provider widget can reinterpret them. No alert capture, route landing page, low-price calendar, popular-route widget, custom inventory API, or WordPress-owned booking path was added in P15.1. Runtime Playwright review covered desktop and mobile screenshots, the update-intent interaction, keyboard reachability through the local form and `Open flight search` handoff, source-secret/unsupported-claim scans, and the existing provider-owned Travelpayouts console-warning watch item. The Codex in-app Browser path was attempted first but had no active pane in this thread, so Playwright Chromium was used for required browser screenshots and keyboard navigation review.

P15.2 note: `ONE-94` started on 2026-05-12. The static theme now includes `archive-route.php`, `single-route.php`, a reusable route-card template part, and a scoped route-surface stylesheet for route/origin SEO pages. Route archive pages support a sanitized `route_origin` filter, route detail pages render editable WordPress route meta, related route links, an alert handoff placeholder, and the approved `flights_white_label_search` Travelpayouts placement through the Phase 13 registry seam. The registry schema migrated to `1.0.1` so the starter flight placement adds the `route` public surface once, preserving future admin edits after migration. No custom inventory API, provider result storage, alert storage, direct checkout, payment, booking, or auto-publishing path was added. Runtime Playwright review used temporary local route posts for desktop/mobile archive, origin-filter, route detail, provider-widget, and keyboard-navigation checks; the temporary posts were deleted after validation.

P15.3 note: `ONE-95` started on 2026-05-12. The Travelpayouts widget registry schema migrated to `1.0.2` and now seeds approved official-plugin placements for `flights_low_price_calendar`, `flights_popular_routes`, and `flights_route_map` on the Flights and route surfaces. The frontend renderer delegates approved `tp_` shortcode references through a trusted official-shortcode renderer, passes sanitized IATA route context plus generated SubIDs, preserves provider scripts/iframes, reserves responsive frames, and exposes loading, fallback, and no-script states. The Flights and route templates now render a discovery section below the primary White Label handoff, with visible missing-code states when origin/destination context is incomplete. No local fare inventory, booking, payment, alert storage, direct checkout, or provider API secret handling was added. Runtime Playwright review captured desktop/mobile Flights and route screenshots plus the missing-code state and keyboard-navigation evidence; provider-owned Aviasales analytics/image warnings remain a watchlist item because the widgets still rendered and no page errors, horizontal overflow, duplicate IDs, app-level failed requests, or fallback states were present.

P15.4 note: `ONE-96` started on 2026-05-12. Flights and route detail pages now render a reusable White Label continuity band immediately before the approved `flights_white_label_search` placement. The continuity band keeps visible links back to Home, Flights, route guides, and the current route guide, states that WordPress owns the branded shell and SEO/editorial route page, and keeps Travelpayouts responsible for live search, result filters, booking, payment, changes, and support. The provider sections now have scroll-margin protection so anchored/jump navigation lands below the fixed header. No custom inventory API, direct checkout, payment flow, provider secret handling, alert storage, or Page-type White Label dashboard rewrite was added. Runtime Playwright review captured desktop/mobile Home, Flights, route, and provider-section screenshots; header logo/navigation matched Home on Flights and route detail; keyboard review reached continuity links and `Open flight search`; and source scans found no secret exposure. Provider-owned Sentry/API aborts and React/GraphQL warnings remain watchlist-only because the White Label modules loaded and no app-owned page errors, horizontal overflow, duplicate IDs, secret terms, or keyboard misses were found.

P15.5 note: `ONE-97` started on 2026-05-13. The core plugin now registers `[baf_flight_alert_signup]` and an authenticated/anonymous `admin-post.php` handler for local flight alert intent capture. Flights and route detail pages render the alert form with nonce, explicit consent, email, route, and frequency fields; the handler stores private `travel_alert` records with minimized route/watch metadata, contact email, consent timestamp, source surface, and requested status. Missing nonce, invalid email, missing consent, missing route, missing alert CPT prerequisites, or immediate duplicate submissions fail closed or redirect to safe form states. The workflow does not claim live fare monitoring, does not store provider inventory or booking records, and keeps Travelpayouts/provider ownership of live fares, filters, booking, payment, changes, and support. Runtime Playwright review captured desktop/mobile Flights and route screenshots, submitted a real alert intent, verified saved/error/rate-limited states and keyboard reachability, and found no app-owned layout, source-secret, duplicate-ID, overflow, or keyboard issues. Codex PR review follow-up hardened anonymous writes with a per-client/email/route transient throttle, rebuilt current form URLs from request paths so subdirectory installs do not duplicate the site path, and normalized lowercase route-code submissions before allowlist validation. Provider-owned Travelpayouts console warnings remain watchlist-only.

P15.6 note: `ONE-98` started on 2026-05-13. The static theme now owns flight/route SEO metadata through `inc/seo-metadata.php`: `/flights/` has a safe search-handoff title and description, flight-search query URLs receive `noindex, follow` and canonicalize to the base Flights page, `/routes/` has archive metadata, origin-filtered route archives canonicalize to the sanitized uppercase `route_origin`, and route detail pages use excerpt-backed descriptions plus core singular canonical output. Route indexing behavior is documented as WordPress-owned route archives, origin-filter archives, and route singles remaining indexable editorial pages; Travelpayouts White Label and provider widgets remain result/handoff surfaces and are not the SEO source of truth. Lowercase route codes now normalize uppercase before filtering in route archive/card/single templates and the core route-meta sanitizer. Validation covered PHP syntax, file-size checks, `git diff --check`, HTTP/source scans, bounded REST/content smoke, temporary route-post source checks, desktop/mobile Playwright screenshots, and keyboard navigation from origin archive to route detail. The Codex in-app Browser plugin was attempted first, but no active browser pane was available in this session, so Playwright Chromium was used for the required runtime screenshots and keyboard review. Provider-owned Travelpayouts console warnings and aborted analytics/image requests remain watchlist-only because the pages rendered, source scans were clean, and no app-owned page errors or failed requests were found.

P15.7 note: `ONE-99` started on 2026-05-13. The final Phase 15 review gate reconciled P15.1 through P15.6 against the Flights Experience objective and acceptance criteria. HTTP/source checks covered `/flights/`, transient flight query URLs, `/routes/`, origin-filtered route archives, a temporary route detail page, and the public route REST collection; alert nonce validation returned `403` without creating a `travel_alert`; and the temporary route post was deleted after runtime validation. Playwright Chromium screenshots confirmed the Flights handoff, White Label widget section, route detail, mobile origin archive, and keyboard-focused route link render without app-owned console errors, app-owned failed requests, framework overlays, duplicate IDs, or horizontal overflow. Keyboard navigation tabbed from the origin archive to the temporary route card link and Enter opened the route detail page. Provider-owned Travelpayouts warnings and external request noise remain watchlist-only. Phase 16 may start after this review-gate PR is reviewed, merged, and Linear is synced.

## Phase 16: Hotels and Stays Experience

Status: `Completed`

Objective: Build a strong hotel discovery experience using Travelpayouts-supported hotel widgets, maps, links, and program tools.

Scope:

- Hotels landing page.
- City hotel guides.
- Hotel map/search widgets.
- Editorial stay-type and neighborhood modules.
- Hotel SubID placement strategy.

Acceptance criteria:

- Hotel discovery is powered by Travelpayouts widgets/links or approved Travelpayouts program tools.
- WordPress filters are editorial unless the Travelpayouts surface supports live filtering.
- Booking handoff language is clear.

Validation checklist:

- Widget render checks.
- Mobile map/widget layout review.
- Secret exposure review.
- Editorial SEO checks.

P16.1 note: `ONE-100` started on 2026-05-13. The Hotels page now has a local hotel-intent module for destination, check-in, check-out, guests, rooms, and stay focus before the approved `hotels_partner_search` placement. The page keeps these fields as WordPress-owned planning intent and clearly states that live rates, room inventory, taxes, policies, map/neighborhood/amenity filters, booking, payment, changes, and support stay with Trip.com, Travelpayouts, or the partner provider. The configured hotel partner placement continues to render through the Phase 13 registry as an iframe with a visible sponsored `Open hotel search` handoff and no-script/missing-configuration states from the shared placement shell. Runtime validation found the shared URL cleanup script was only enqueued on Flights, so hotel intent submissions kept query parameters in the visible URL; `search-surface.js` now handles hotel intent keys and the script is enqueued for both Flights and Hotels. Playwright Chromium desktop/mobile checks confirmed the Hotels page, widget section, updated-intent interaction, clean URL, and keyboard path through local controls to `Open hotel search`, with no app-owned console errors, app-owned failed requests, relevant failed requests, horizontal overflow, duplicate IDs, framework overlays, source-secret leaks, or unsupported inventory claims.

P16.2 note: `ONE-101` started on 2026-05-13. Published `destination` posts now render city hotel guide archive and detail templates for editable WordPress stay guidance before the approved hotel partner handoff. The core plugin registers destination-only hotel guide meta keys for summary, neighborhoods, best-fit guidance, family, luxury, budget, and landmark notes; the single guide template combines those editable fields with normal post content, related route links, and the configured `hotels_partner_search` placement. The Hotels page now surfaces published city guide cards when destination posts exist. Runtime validation found and fixed one placement-surface bug: the destination template initially requested the new `destination` surface, but the existing governed Trip.com placement is only approved for `home` and `hotels`, so the city guide now renders the placement with `surface="hotels"` while preserving `destination_single` in the channel/SubID context. Playwright Chromium desktop/mobile checks confirmed the city guide archive, city guide detail, hotel modules, `/hotels/` guide teaser, provider section, keyboard navigation from the archive into a city guide, and keyboard reachability of `Open hotel search`, with no app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, unavailable-placement state, source-secret leakage, direct-checkout claims, or unsupported WordPress-owned inventory claims.

P16.3 note: `ONE-102` started on 2026-05-13. The widget registry schema now seeds `hotels_map_handoff` and `hotels_listing_handoff` companion placements as governed `handoff_link` placements when the existing approved hotel partner URL is a usable handoff URL. The renderer now adds widget-family and placement-key CSS classes so the compact Trip.com search iframe crop is scoped to `hotels_partner_search` only, leaving future hotel map/listing iframes with normal reserved frames. `/hotels/` and destination guide pages render a reusable hotel partner tools section with map/listing handoffs, visible disclosures, unique SubIDs, provider-owned live map/listing/booking language, and no local live hotel inventory claims. Runtime validation created temporary destination post `315`, confirmed the new placements and SubIDs in source, captured desktop/mobile Playwright screenshots for Hotels and the destination guide, reached `Open hotel map` and `Open hotel listings` by keyboard tab order, fixed an oversized link-only handoff card layout, patched the admin widget-family choices after Codex PR review flagged the missing `hotel_listing` option, then removed the temporary post and confirmed `temporary_posts_remaining=0`.

P16.4 note: `ONE-103` started on 2026-05-13. Hotel/stays handoff copy now names sponsored partner handoffs more explicitly, removes vague local-search CTA language, avoids Booking.com White Label promises, and keeps map, neighborhood, amenity, room, rate, tax, policy, booking-term, payment, change, and support ownership with Travelpayouts, Trip.com, or the partner provider. The shared travel placement shell now labels visible support notes as `Affiliate disclosure:` and links each placement section to that disclosure with `aria-describedby`. Runtime validation used temporary destination posts `318` and `319`, confirmed `/hotels/`, a hotel-intent URL, `/destinations/`, and the destination guide render without unsupported hotel claims, missing labelled disclosures, horizontal overflow, duplicate IDs, app-owned console errors, or app-owned failed requests, fixed a visual disclosure overlap in companion placement cards, and captured keyboard focus to `Open partner search`, `Open hotel map`, and `Open hotel listings`.

P16.5 note: `ONE-104` started on 2026-05-13. Mobile widget/map layout and source review used the current P16.1-P16.4 hotel surfaces without adding new provider contracts, registry schema, REST routes, options, tables, shortcodes, or blocks. Playwright Chromium reviewed `/hotels/`, hotel-intent URLs, `/destinations/`, and a temporary destination guide across desktop, tablet, mobile, and 320px narrow viewports, plus keyboard paths to `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search`. The first strict responsive pass found small hit areas in the header menu toggle, desktop nav links, and hotel guide card title links; `header.css` and `hotel-guide.css` now preserve 44px minimum interactive targets without changing the Travelpayouts/partner ownership boundary. The final runtime report found no app-owned console errors, app-owned failed requests, horizontal overflow, duplicate IDs, unsupported hotel copy, sensitive source terms, missing disclosures, clipping problems, or small touch targets.

P16.6 note: `ONE-105` started on 2026-05-13. The final Phase 16 review gate reconciled P16.1 through P16.5 against the Hotels and Stays objective, current codebase state, Travelpayouts-owned backend boundary, widget registry contracts, source output, responsive screenshots, keyboard navigation, and documentation. Runtime Playwright Chromium validation used temporary destination post `321`, covered `/hotels/`, a hotel-intent URL, `/destinations/`, and the temporary destination guide across desktop, tablet, mobile, and 320px narrow widths, and reached `Update hotel intent`, `Open hotel map`, `Open hotel listings`, and `Open partner search` by keyboard. The first gate found a real SEO bug: transient hotel-intent query URLs canonicalized to `/hotels/` but did not render `noindex, follow`; `seo-metadata.php` now applies the same transient-query robots boundary to hotel intent URLs that Flights already used. Codex PR review then found the initial hotel path helper missed WordPress subdirectory installs, so the helper now strips the site's `home_url()` path prefix before comparing `/hotels/`. The final report at `/tmp/one105-phase16-report.json` has `findingCount=0`, all temporary content was deleted, and Phase 17 may start after this review-gate PR is reviewed, merged, and Linear is synced.

## Phase 17: Destination, Route, and SEO Content Engine

Status: `Completed`

Objective: Scale organic landing pages around WordPress CPTs and Travelpayouts monetized placement zones.

Scope:

- Destination single/archive templates.
- Route single/archive templates.
- Deal templates.
- Taxonomy archives.
- Internal linking and monetized module rules.

Acceptance criteria:

- Pages are editable in WordPress.
- Monetized modules use approved Travelpayouts placements.
- Generated content never auto-publishes without approval.
- Affiliate disclosure appears on every monetized page.

Validation checklist:

- CPT template checks.
- Accessibility and responsive review.
- SEO source review.
- Editor workflow smoke check.

P17.1 note: `ONE-106` started on 2026-05-13. Destination archive and single templates now present `destination` CPT content as broader editable destination guides instead of hotel-only pages. The single destination template renders post content, taxonomy labels, destination facts, best-time/activity/seasonal modules, related destination links, related route links, and the existing governed hotel handoff section while keeping provider search, live availability, booking, payment, changes, and support outside WordPress. The core plugin registers destination-only editable meta keys for best-time, facts, activities, and seasonal notes with private REST exposure. Runtime validation found the temporary browser seed content was initially assigned numeric taxonomy terms by WP-CLI, so the validation data was corrected to slug-based `ONE-106 Coast`, `ONE-106 Family`, and `ONE-106 Fall` terms before final screenshots. Playwright Chromium captured archive and single guide pages across desktop, mobile, and 320px narrow widths and keyboard focus to `Open provider flight search`, `Open provider hotel search`, `Open hotel map`, `Open hotel listings`, and a related destination guide; the final report at `/tmp/one106-destination-report.json` returned `findingCount=0`.

P17.2 note: `ONE-107` started on 2026-05-13 after `ONE-106` and Phase 16 were merged and synced. Route archive and single templates now deepen the existing Phase 15 route surface with archive SEO modules, editable route travel-time/airport/flexible-date/destination notes, a route planning module grid, low-price calendar and White Label anchors, local alert intent links, destination hotel/activity follow-up, matching destination guide links, and guarded related-route output. Related routes now require shared origin/destination airport context or shared route taxonomy terms before rendering; airport matches are collected first and taxonomy matches fill remaining slots, while context-free route posts show empty-state copy instead of arbitrary route cards. Codex PR review caught the first patch skipping taxonomy peers whenever airport meta existed, and the follow-up validation used a taxonomy-only related route to confirm the fix. Playwright Chromium captured `/routes/`, `/routes/?route_origin=nyc`, a temporary route guide, and a no-context route across desktop/mobile/320px where applicable, plus keyboard focus to flight handoff, watch route, low-price calendar module, hotel handoff, activity prompts, related route, and destination guide links. The final report at `/tmp/one107-route-report.json` returned `findingCount=0`; temporary route and destination posts plus the temporary taxonomy term were deleted and confirmed at zero remaining.

P17.3 note: `ONE-108` started on 2026-05-13 after `ONE-107` and Phase 16 were completed in Linear. Travel deal archive and single templates now render editable `travel_deal` briefs for seasonal, weekend, budget, family, luxury, beach, business, activity, source-note, and partner-handoff context without claiming fake urgency, current prices, live availability, or WordPress-owned booking. The core plugin registers deal-only editable meta keys for seasonal context, weekend ideas, theme notes, activity notes, partner notes, and source notes with private REST exposure. The existing `flights_white_label_search` registry placement now allows the `deal` public surface so deal singles can render approved SubID-bearing White Label handoff output while keeping live search, filters, booking, payment, changes, and support with Travelpayouts or the partner provider. Runtime validation found the deal hero needed extra top padding under the fixed header on mobile and 320px narrow widths; `deal-surface.css` now clears the header before the first hero text. Codex PR review found route links required both origin and destination airport metadata; follow-up patch now matches routes by either shared airport and Playwright confirmed a destination-only route appears on a deal single.

P17.4 note: `ONE-109` started on 2026-05-13 after `ONE-108` merged and Linear was synced. The static theme now includes a shared taxonomy archive template for `travel_region`, `travel_style`, `travel_vertical`, and `travel_season`, with term-specific hero copy, bounded public main-query content cards, taxonomy-aware SEO metadata, internal-linking rule cards, and governed handoff links to Flights, Hotels, Destinations, Routes, Deals, and Trip Planner shell surfaces. Taxonomy archives only query published `destination`, `route`, and `travel_deal` content, with `travel_vertical` limited to `route` and `travel_deal`; non-public `trip_plan`, `travel_alert`, `travel_partner`, private posts, live provider inventory, booking, payment, and auto-publishing remain outside these archives. Runtime validation created temporary taxonomy content, confirmed page-one and page-two pagination, confirmed private/non-public content stayed hidden, and captured `/tmp/one109-taxonomy-report.json` with `findingCount=0`. The first Playwright touch-target pass found mobile nav links could render below the 44px target height, so `mobile-nav.css` now gives mobile nav links an inline-flex 44px minimum block target; follow-up desktop, mobile, 320px, and keyboard screenshots passed. Codex PR review then found paginated taxonomy archives canonicalized to the first term page, raw paginated request URLs could preserve tracking query args, and a separate template query could drift from WordPress pagination. The theme now bounds the main taxonomy query with `pre_get_posts`, the template renders the main loop, and taxonomy canonicals are built from the clean term URL plus the pagination path.

P17.5 note: `ONE-110` started on 2026-05-13 after `ONE-109` merged and Linear was synced. The core plugin now adds structured Destination, Route, and Travel Deal editor meta boxes for the Phase 17 guide-module fields that public templates already render, so editors can maintain destination facts, route planning notes, deal context, hotel-guide modules, airport codes, budget context, and source notes without typing raw `baf_*` custom-field keys. The save path uses a nonce, `edit_post` capability checks, autosave/revision guards, text/textarea/code/number sanitization, and empty-value cleanup. Travelpayouts scripts remain outside editor content; templates continue to consume approved placement registry wrappers and local handoff links. The legacy local `bookings-and-flights-content-manager` plugin was reviewed as a field-pipeline candidate, but it is still untracked, page-template oriented, missing P17 CPT export/import declarations, and has oversized `class-meta-boxes.php` and `class-export-import.php` seams that should be split before large tracked content-manager expansion. Real admin runtime screenshots and keyboard review passed after fixing the destination field grouping/tab order. PR #46 merged on 2026-05-13 with merge commit `ebad31731f8f3c4f7667833b9cb3d601fa34dffb`; detailed workflow and deferred issues are recorded in `.plan/editor-workflow-content-manager-review.md`.

P17.6 note: `ONE-111` started on 2026-05-13 after `ONE-110` merged and Linear was synced. The accessibility, responsive, SEO, and disclosure pass reviewed destination, route, deal, taxonomy, and archive surfaces with real Playwright Chromium screenshots, keyboard navigation, source checks, console/request health, and transient Flights/Hotels query SEO checks. The first strict runtime pass found four app-owned issues: route alert consent target sizing, an unnamed provider-owned route-map iframe, sub-44px deal/taxonomy card title links, and default destination copy that still referenced guaranteed availability. The fixes are limited to public CSS target sizing, official widget iframe title labelling, and safer seasonal copy. The final report at `/tmp/one111-accessibility-seo-report.json` returned `status=pass` and `findingCount=0`; screenshots and review details are recorded in `.plan/phase-17-accessibility-seo-disclosure-review.md`. Provider-owned WebGL, Babel, and GraphQL widget warnings remain watchlist-only when app-owned rendering, disclosures, keyboard navigation, and source checks pass. `frontend.css` is now 590 lines, so future substantial core frontend CSS work should split it before expansion.

P17.7 note: `ONE-112` started on 2026-05-13 after `ONE-111` merged and Linear was synced. The final Phase 17 review reconciled destination, route, deal, taxonomy, editor workflow, SEO, disclosure, accessibility, responsive, and source/security work against the current codebase. Runtime validation used temporary destination, route, deal, taxonomy, private destination, and non-public trip-plan records, then confirmed public archives/singles render correctly, transient Flights/Hotels query URLs stay noindex with base canonicals, taxonomy archives do not leak private or non-public records, and keyboard paths reach destination, route, deal, and taxonomy interactions. The first report surfaced a label-owned alert checkbox target and an Aviasales/Travelpayouts analytics-pixel `400`; both were classified correctly, and the final report at `/tmp/one112-phase17-final-report.json` returned `status=pass` and `findingCount=0`. Phase 18 may start after this review-gate PR is reviewed, merged, and Linear is synced.

## Phase 18: AI Planner With Travelpayouts Handoff

Status: `In Progress`

Objective: Turn AI itinerary planning into a conversion assistant while keeping live availability and booking controlled by Travelpayouts.

Scope:

- AI planner page.
- Prompt-to-trip brief.
- Day-by-day itinerary cards.
- Travelpayouts widget/card recommendations.
- Approval-oriented AI opportunity handoff that turns validated recommendations into approved placement drafts without executing bookings, provider searches, or publishing actions.
- Save trip and alert CTAs.

Acceptance criteria:

- Demo mode works without live credentials.
- Live AI requires consent and configuration.
- AI does not invent prices or availability.
- AI cannot book, pay, or publish.
- AI-generated affiliate opportunities remain `not_executed` until an authorized WordPress action approves a Travelpayouts placement or handoff.

Validation checklist:

- AI REST smoke and permission tests.
- Missing-key and missing-consent tests.
- Schema validation.
- Prompt/output secret review.

P18.1 note: `ONE-113` started on 2026-05-13 after Phase 17 merged and Linear was synced. The first AI planner slice adds a WordPress-owned `/trip-planner/` frontend route, dedicated planner template, scoped CSS/JS assets, homepage/header/content CTA routing, and a prompt-to-trip-brief form that calls the protected `POST /wp-json/baf/v1/ai/itinerary` endpoint. Demo mode prepares structured editable briefs locally; live mode now requires both saved external AI consent and a per-request `external_ai_consent` confirmation before a live provider is selected. The response exposes a sanitized `trip_brief` plus validated itinerary cards and recommendation-only affiliate opportunities with `not_executed` status. This slice does not save trip drafts from the public page, publish content, execute provider searches, book, pay, store live inventory, or expose raw prompts in rendered results. Runtime validation used Playwright Chromium after the Codex in-app Browser path reported no active pane; screenshots and keyboard review confirmed the planner loads at `/trip-planner/`, focuses the prompt field, submits a demo brief, does not echo the raw prompt in the rendered result, has no horizontal overflow on desktop/mobile, and logs no app-owned console or request failures.

P18.2 note: `ONE-114` started on 2026-05-13 after `ONE-113` merged and Linear was synced. The planner now offers an editor-only save checkbox that sends `save=true` only for users with `edit_baf_content`. The service rejects save requests from run-AI-only users before provider selection, then saves approved results as private admin `trip_plan` drafts with editable day-by-day content, sanitized excerpt, source session UUID, date/origin/destination/style meta, and validated itinerary JSON. The REST response includes a safe draft object with ID, draft status, and edit URL; it still does not publish, book, pay, execute provider searches, create Travelpayouts placements, store live inventory, or echo raw prompts into visible output or saved post content. Runtime validation used Playwright Chromium after the Codex in-app Browser path reported no active pane; screenshots and keyboard review confirmed the save option, submit path, saved draft message, edit-link handoff to the WordPress editor, and cleanup of temporary draft/user.

P18.3 note: `ONE-115` started on 2026-05-13 after `ONE-114` merged and Linear was synced. The AI opportunity contract is now versioned as `travelpayouts_opportunity_v1`. Travelpayouts opportunities carry recommendation type, placement context, destination/route, sanitized suggested SubID, disclosure requirement, confidence, limitations, approval state, and blocked actions while remaining `not_executed`. Schema validation rejects booked/paid/published/executed statuses, approval or disclosure bypasses, and provider-owned booking, payment, price, availability, confirmation, link, or live-inventory claims before a response or draft save is allowed.

P18.4 note: `ONE-116` started on 2026-05-13 after `ONE-115` merged and Linear was synced. The planner now exposes editor-only handoff controls for saved Trip Plan drafts and validated Travelpayouts opportunities. `POST /wp-json/baf/v1/ai/handoff` requires `edit_baf_content`, a valid REST nonce, source `trip_plan` edit permission, global provider-request consent, per-request provider handoff consent, explicit approval, and a still-valid `not_executed` opportunity before storing an `ai_handoff_intent_v1` record in `baf_ai_handoff_intents`. The stored intent is approval-oriented local metadata only: `provider_action=not_executed`, `provider_action_executed=false`, and `external_request_sent=false`. It does not call Travelpayouts, create live provider links, publish content, book, pay, send alerts, or create public saved-trip records. Playwright Chromium runtime validation was used after the Codex in-app Browser path reported no active pane; screenshots and keyboard review confirmed draft save, handoff approval, `201` responses from itinerary and handoff routes, no raw prompt or provider link in stored handoff meta, and cleanup of temporary browser-test content.

P18.5 note: `ONE-117` started on 2026-05-13 after `ONE-116` merged and Linear was synced. The planner now uses `Provider_Factory::live_readiness()` as the shared contract for live-mode configuration and consent readiness. Missing provider, unsupported provider, missing API key, and missing saved External AI consent states render actionable planner messages and are blocked client-side before any itinerary REST request is sent, while the backend still enforces the same configuration and consent gates. Live-ready requests still require the per-request external AI consent checkbox. REST/service/provider normalization now guards malformed non-scalar values before sanitization or prompt-payload construction so invalid input fails safely without PHP conversion warnings or secret/raw-prompt leakage. Playwright Chromium runtime validation was used after the Codex in-app Browser path timed out; screenshots and keyboard review confirmed demo success, live missing-key, missing saved consent, missing per-request consent, zero REST requests for blocked live states, no rendered fake API key, no app-owned console/request failures, and no mobile horizontal overflow.

P18.6 note: `ONE-118` started on 2026-05-13 after `ONE-117` merged and Linear was synced. The AI privacy/security review hardened schema, provider, service, settings, and admin-dashboard paths that still accepted malformed non-scalar values before sanitization or status rendering. Structured output validation now normalizes itinerary duration/day/activity fields, opportunity destination fallbacks, and OpenAI response content through scalar-safe helpers; malformed AI mode/provider option values fall back safely; and dashboard AI status checks do not cast array/object option values or expose API keys. Focused backend smokes confirmed explicit REST permissions, malformed schema rejection, malformed settings sanitization, dashboard secret absence, provider error safety, and AI session log privacy without `Array to string conversion`, warning, fatal, fake-key, or raw-prompt leaks. Playwright Chromium runtime validation was used after the Codex in-app Browser path reported no active pane; screenshots and keyboard review confirmed page identity, nonblank planner content, keyboard reachability through prompt/destination/submit, demo `201` response, no raw prompt echo, no live-provider data in demo output, live missing per-request consent with zero additional itinerary REST requests, no fake API key in source, no app-owned console/request failures, and no mobile horizontal overflow. Non-app WordPress/admin and Travelpayouts asset aborts were documented as navigation noise, not app-owned BAF failures. PR #54 was reviewed by Codex with no major issues and merged into `main` at `833f013cefafff5c6845774300de1a2386d1edf9`.

## Phase 19: Saved Trips, Alerts, Analytics, and Release Readiness

Status: `Not Started`

Objective: Complete retention, measurement, hardening, and launch validation for the Travelpayouts-powered WordPress product.

Scope:

- Saved trip board.
- Price alert intent.
- Email follow-up hooks where approved.
- Travelpayouts SubID reporting map.
- Local privacy-aware placement analytics.
- Security, accessibility, performance, and release review.
- Final review that the placement registry, search surface mode seam, brand continuity source, AI opportunity handoff, and content manager field pipeline are either implemented, explicitly deferred, or recorded in `.plan/known-issues.md`.

Acceptance criteria:

- Saved trips and alerts store local intent, not partner booking records.
- Users can delete saved data.
- Travelpayouts reports can identify major placements by SubID.
- Search, widget, handoff, admin, REST, and frontend flows pass release review.

Validation checklist:

- PHP syntax checks.
- Plugin activation/deactivation checks.
- REST route inventory and permission checks.
- Browser screenshots across breakpoints.
- Widget handoff and White Label checks.
- Source review for secrets.
- Performance baseline.
- Architecture deepening review for Travelpayouts placements, search-surface routing, brand continuity, AI handoff, and content manager maintainability.

## Cross-Phase Rules

- Do not rename documented contracts without updating `.plan/architecture-baseline.md` and `.plan/decisions.md`.
- Do not introduce direct booking, payments, ticketing, refunds, or supplier contract workflows without explicit approval.
- Do not expose provider credentials or AI secrets.
- Do not store sensitive raw prompts, tokens, passports, payment data, or private travel documents.
- Do not trigger long-running provider or AI calls during page render.
- Every implementation summary must include `Research consulted`.
