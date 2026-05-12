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

Status: `In Review`

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
- Frontend widget render smoke check. P11.3 and P11.6 passed for the official flight widget on desktop and mobile; official hotel widget/table shortcodes rendered empty because the plugin's legacy HotelLook availability gate is disabled. Travelpayouts documentation now treats Hotellook tools as shut down, so hotel surfaces use the dashboard-generated Trip.com or other Hotels & Accommodation brand widget/link path. The published Flights page now renders the Widget-type White Label search/results containers, and the published Hotels page renders a Trip.com partner iframe plus a visible sponsored handoff link.
- Widget handoff/White Label smoke check. P11.3 and P11.6 confirmed the flight widget source hands off to Travelpayouts/Aviasales-controlled results by default. The final P11.6 browser pass confirmed the Flights page loads without PHP deprecation output and the Hotels page opens the Trip.com partner search form from the visible handoff button.
- White Label header continuity check against the WordPress homepage header. P11.4 documents Widget type as the preferred continuity path because it keeps the WordPress home shell; Page type is allowed only with Travelpayouts dashboard header customization that mirrors the WordPress logo, favicon, brand name, nav/footer links, colors, and route back to the main site.
- Backend mode decision. P11.5 documented Travelpayouts-controlled backend mode: official-plugin-first only for validated surfaces, dashboard-generated Travelpayouts fallback embeds for inactive or unvalidated hotel and White Label surfaces, and `/search/flights` plus `/search/hotels` limited to safe shell/configuration/placement/handoff metadata instead of live inventory APIs.
- Secret exposure review. P11.3 and P11.6 browser/source scans passed for temporary and final pages: no API token, postback secret, authorization string, checkout, payment, refund, or direct WordPress booking flow appeared in WordPress output.
- Completion gate. Phase 11 can move to `Completed` only after the P11.6 follow-up branch is reviewed and merged. The local browser/source validation gate passed on 2026-05-12 for the published Flights and Hotels pages, with the known note that Trip.com iframe content can be blank under browser content blockers, so the hotel page keeps a visible handoff button.

## Phase 12: Information Architecture and Competitive Design System

Status: `Not Started`

P11.6 note: minimal White Label Widget ID and Trip.com/Hotels widget script settings plus `[baf_travelpayouts_white_label]` and `[baf_travelpayouts_hotel_widget]` shortcodes were added early to unblock setup. Phase 13 still owns the full governed placement registry, SubID/disclosure behavior, block wrapper, and broader safe embed layer.

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

Status: `Not Started`

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

## Phase 14: Homepage Competitive Rebuild

Status: `Not Started`

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

## Phase 15: Flights Experience

Status: `Not Started`

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

## Phase 16: Hotels and Stays Experience

Status: `Not Started`

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

## Phase 17: Destination, Route, and SEO Content Engine

Status: `Not Started`

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

## Phase 18: AI Planner With Travelpayouts Handoff

Status: `Not Started`

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
