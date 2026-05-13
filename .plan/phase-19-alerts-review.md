# Phase 19.2 Alert Intent Review

Date: 2026-05-13

Linear issue: `ONE-121`

Status: In Review

## Summary

P19.2 finalizes the local price-alert lifecycle without turning WordPress into a live fare monitor. Alert capture still stores private `travel_alert` intent records only. The new alert service deduplicates email/route submissions, limits each email to ten active or pending alerts, records email follow-up state, sends confirmation/manage emails from the hourly alert cron job, moves failed email sends to a non-counted `email_failed` state, and gives users a signed delete-confirmation link that requires nonce-protected POST confirmation before permanently removing the private alert record.

No Travelpayouts provider call, custom inventory API, live fare storage, booking, payment, provider payload logging, raw prompt storage, or public alert REST exposure was added.

## Review Gate

- Scope review: Passed. The implementation matches P19.2: alert intent, limits, email/cron hooks, opt-out/delete, and provider-boundary copy.
- Functional happy path: Passed. Browser form submission creates a local alert and redirects to the saved state.
- Error/limits: Passed. Duplicate email/route submissions update the existing alert, short throttling remains, the eleventh active/pending route for one email is rejected, and failed email sends do not permanently consume the active/pending quota.
- Cron/email: Passed. Pending alerts are processed through `baf_process_travel_alerts`; successful emails mark alerts active and sent, while failed sends move to `email_failed` for safe retry through a later same-route save.
- Delete/opt-out: Passed. Email delete links use signed tokens, open a frontend confirmation form, permanently delete the private alert post only after a valid nonce-protected POST, and report `wp_delete_post()` `false`/`null` returns as failures.
- Security/data review: Passed. Nonces, consent, validation, sanitization, safe redirects, token verification, private meta, and source-secret checks passed.
- UI review: Passed with Playwright Chromium screenshots and keyboard review after the Codex in-app Browser pane was unavailable.

## Validation

- PHP syntax:
  - `plugins/bookings-flights-core/includes/services/class-flight-alert-service.php`
  - `plugins/bookings-flights-core/includes/frontend/class-flight-alert-intent-handler.php`
  - `plugins/bookings-flights-core/includes/frontend/class-flight-alert-signup-shortcode.php`
  - `plugins/bookings-flights-core/includes/jobs/class-job-runner.php`
  - `plugins/bookings-flights-core/includes/cron/class-cron-manager.php`
  - `plugins/bookings-flights-core/includes/post-types/class-post-type-registrar.php`
  - `themes/bookings-and-flights-static/page-home.php`
- Plugin activation: `bookings-flights-core` deactivate/reactivate/is-active passed.
- WP-CLI smoke: `/tmp/one121-alert-smoke.php` passed 65 assertions after adding failed-email quota, same-route retry, query-cache freshness, and `wp_delete_post()` failure regressions.
- Permission/meta checks: missing alert nonce returned `403` and created zero records; new alert follow-up meta keys are registered for `travel_alert` and stay private.
- Runtime browser review: `/tmp/one121-runtime-review-report.json` and `/tmp/one121-delete-runtime-review-report.json` returned `status=pass`, `findingCount=0`.
- Screenshots:
  - `/tmp/one121-alert-form-desktop.png`
  - `/tmp/one121-alert-saved-desktop.png`
  - `/tmp/one121-alert-form-mobile.png`
  - `/tmp/one121-alert-delete-confirm-desktop.png`
- Cleanup: temporary `one121-*` alert records were deleted before the final smoke rerun.

## Bugs

Found:

- Success copy was too absolute about email delivery even though `wp_mail()` can defer or fail.
- Codex PR review found that the cached-offer cron path was missing the `Post_Type_Registrar` import after the alert-job edit.
- Codex PR review found that the first alert delete link allowed a destructive GET request from email.
- Codex PR review found that `delete_by_token()` treated a `false` return from `wp_delete_post()` as success.
- Codex PR review found that failed `wp_mail()` sends kept alerts in `requested`, causing failed sends to consume the per-email limit without sending a manage/delete link.

Fixed:

- Updated frontend messaging to say the queue will try to send confirmation/delete email while preserving the provider-owned live fare and booking boundary.
- Restored the cached-offer cron import.
- Changed email delete links into signed frontend confirmation URLs, added a confirmation form, and required a nonce-protected POST before deleting the alert.
- Styled the confirmation warning and delete/keep controls for the runtime page.
- Required a deleted `WP_Post` object before treating token deletion as successful.
- Moved failed `wp_mail()` sends to `email_failed`, excluded that status from the active/pending quota, and verified a same-route save can queue another attempt.

Deferred:

- WordPress personal-data exporter/eraser integration remains P19.3 scope.
- Analytics/reporting and final release-readiness remain later Phase 19 scope.

## Research Consulted

- WordPress Common APIs Handbook: Nonces.
- WordPress Plugin Handbook: Cron.
- WordPress Code Reference: `wp_mail()`.
- WordPress Code Reference: `pre_wp_mail`.
- WordPress Code Reference: `wp_delete_post()`.
- WordPress Code Reference: `admin_post_{$action}`.
- WordPress Common APIs Handbook: Sanitizing Data.
