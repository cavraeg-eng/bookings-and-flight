# Known Issues

Track current bugs, severity, affected area, workaround, and planned fix phase.

## Platform API Port Documentation Mismatch

Severity: Low

Affected area: `platform/` local development and WordPress/platform integration.

Current issue: `platform/README.md` says `npm run dev` starts the search API on `:4000`, while `platform/INTEGRATIONS.md`, `platform/services/search-api/src/config/env.ts`, `platform/services/search-api/.env.example`, `platform/apps/web/.env.example`, and `platform/apps/web/next.config.mjs` use `http://localhost:4050`. Phase 2 updated the affiliate bridge activation/default/admin copy to `http://localhost:4050`; the platform README remains stale.

Workaround: Before running platform integration work, inspect the actual `platform/services/search-api` server configuration and update remaining platform docs consistently.

Planned fix phase: First platform or settings integration task.

## WordPress CLI Emits PHP 8.5 Deprecation Warning

Severity: Low unless command execution fails.

Affected area: Validation using WP-CLI.

Current issue: `wp --info` emits a PHP 8.5 deprecation warning from a bundled dependency.

Workaround: Treat as tooling noise if commands succeed. If commands fail, use the Local by Flywheel PHP version or a supported WP-CLI/PHP pairing.

Planned fix phase: Validation environment setup, if it blocks work.

## Travelpayouts WordPress Plugin Compatibility Gate

Severity: Medium until admin, widget, handoff, White Label, and secret-exposure gates pass.

Affected area: Travelpayouts-controlled booking/search backend, widget placement, and production frontend pages.

Current issue: The official Travelpayouts WordPress plugin is the preferred backend placement path for widgets, tables, links, and search forms, but WordPress.org currently warns that the plugin has not been tested with the latest three major WordPress releases. P11.1 installed the official `travelpayouts` plugin version `1.2.2` from WordPress.org and validated local activate, deactivate, and reactivate behavior on WordPress `6.9.4` with no new `debug.log` entries. The workspace has not yet validated admin setup, widget rendering, handoff behavior, White Label header continuity, or frontend/admin secret exposure.

Security watch item: GitHub push protection identified an embedded Airtable personal access token in the official plugin package during PR publication. The staged local package now redacts the hard-coded token and disables the Airtable distribution script unless a token is supplied outside Git through `TRAVELPAYOUTS_AIRTABLE_TOKEN`. Do not commit provider, analytics, or distribution tokens into the repository.

Compatibility watch item: Direct PHP syntax scanning of the official plugin passed with no syntax errors, but PHP `8.5.4` emitted deprecation warnings from bundled Redux/PHP-DI/Parsedown/Opis/Travelpayouts classes. Local web PHP is configured with `E_ALL & ~E_DEPRECATED`, so this did not block activation, but future admin/browser validation should watch for displayed warnings if error reporting changes.

Workaround: Treat official-plugin usage as staged for local Phase 11 testing, not production-cleared. If later admin, widget, handoff, White Label, or secret-exposure checks fail, use Travelpayouts dashboard-generated widget and White Label embed code inside a secured, capability-gated WordPress wrapper. Do not build a custom replacement flight/hotel inventory backend.

Planned fix phase: Remaining Phase 11 Travelpayouts Compatibility and Backend Alignment gates.

## Oversized Static Theme Stylesheets

Severity: Low for release readiness; medium for future maintainability.

Affected area: `themes/bookings-and-flights-static/assets/css/header-footer.css` and `themes/bookings-and-flights-static/assets/css/home.css`.

Current issue: Phase 9 release review confirmed both static theme stylesheets exceed the project source-file size guideline. `header-footer.css` is 895 lines and `home.css` is 705 lines. This is existing generated theme debt, not a runtime release blocker.

Workaround: Avoid adding new styles to these files. Continue using focused stylesheets, such as `assets/css/accessibility.css`, for scoped fixes until a planned theme CSS split is approved.

Planned fix phase: Future theme refactor or frontend polish task.
