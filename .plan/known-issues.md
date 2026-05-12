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

## Travelpayouts WordPress Plugin Production Follow-Ups

Severity: Medium until production credentials, hotel fallback embeds, and real White Label domains are validated.

Affected area: Travelpayouts-controlled booking/search backend, widget placement, and production frontend pages.

Current issue: Phase 11 locally confirmed the Travelpayouts-controlled backend boundary, but production placement still needs real account/domain inputs. The official Travelpayouts WordPress plugin remains usable for the locally validated flight widget path only. P11.1 installed the official `travelpayouts` plugin version `1.2.2` from WordPress.org and validated local activate, deactivate, and reactivate behavior on WordPress `6.9.4`. P11.2 identified the account setup fields and cleared the missing/configured setup smoke check with temporary credentials: saved tokens render blank in admin HTML, blank submissions preserve existing tokens, account options are sanitized before storage, and the Gutenberg token route returns only non-secret configured state. P11.3 and P11.6 validated a temporary flight widget test page on desktop and mobile: `[tp_popular_routes_widget]` rendered the Travelpayouts `weedle/widget.js` source with marker/SubID behavior and no source-level token, secret, checkout, payment, or WordPress-owned booking flow. Official hotel widget/table shortcodes still render empty because the plugin's HotelLook availability gate returns false, so hotel surfaces are not plugin-first-cleared in this staged package. Real White Label result validation still requires configured White Label domains or dashboard-generated White Label widget code.

Security watch item: GitHub push protection identified an embedded Airtable personal access token in the official plugin package during PR publication. The staged local package now redacts the hard-coded token and disables the Airtable distribution script unless a token is supplied outside Git through `TRAVELPAYOUTS_AIRTABLE_TOKEN`. Do not commit provider, analytics, or distribution tokens into the repository.

Compatibility watch item: Direct PHP syntax scanning of the official plugin passed with no syntax errors, but PHP `8.5.4` emitted deprecation warnings from bundled Redux/PHP-DI/Parsedown/Opis/Travelpayouts classes. Local web PHP is configured with `E_ALL & ~E_DEPRECATED`, so this did not block activation, but future admin/browser validation should watch for displayed warnings if error reporting changes.

Workaround: Treat official-plugin usage as production-cleared only for the exact flight widget path validated in Phase 11. Use Travelpayouts dashboard-generated hotel widget/table/embed code and White Label Widget/Page code inside a secured, capability-gated WordPress wrapper until an official plugin version or upstream configuration activates the HotelLook tools locally. Keep planned `/search/flights` and `/search/hotels` routes limited to shell/configuration, placement, consent/disclosure, SubID, missing-configuration, or handoff metadata. Do not build a custom replacement flight/hotel inventory backend or promote `platform/` search adapters as the canonical WordPress backend without a new architecture decision.

Planned fix phase: Phase 13 Travelpayouts widget registry and the first real Travelpayouts account/domain setup pass.

## Oversized Static Theme Stylesheets

Severity: Low for release readiness; medium for future maintainability.

Affected area: `themes/bookings-and-flights-static/assets/css/header-footer.css` and `themes/bookings-and-flights-static/assets/css/home.css`.

Current issue: Phase 9 release review confirmed both static theme stylesheets exceed the project source-file size guideline. `header-footer.css` is 895 lines and `home.css` is 705 lines. This is existing generated theme debt, not a runtime release blocker.

Workaround: Avoid adding new styles to these files. Continue using focused stylesheets, such as `assets/css/accessibility.css`, for scoped fixes until a planned theme CSS split is approved.

Planned fix phase: Future theme refactor or frontend polish task.
