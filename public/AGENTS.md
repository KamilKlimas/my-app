# AGENTS.md: public/

Entry point and frontend rules. They add to the root `AGENTS.md`; if the two ever disagree, the root file wins.

## Entry point

- `index.php` starts the session, checks CSRF, opens the database, wires the objects by hand, registers every route (each with its `Access` level)
  and hands the request to the router. No business logic here.

## Views

- Views in `views/` only present data prepared by a controller: no SQL, no business decisions.
- Escape every dynamic value with `$e()`, which `View::render()` passes to each view. Never call `htmlspecialchars()` in a view.
- Every user-facing text comes from `$t('key')` (catalogues in `lang/`, new keys go into both `pl.php` and `en.php`), printed as `$e($t('key'))`.
  Numbers, counts and dates inside a sentence are parameters formatted by ICU in the catalogue (`{count, plural, ...}`, `{at, date, ::MMMd}`),
  never glued from pieces in the template and never formatted with `date()`/`number_format()` for display.
  Controllers pass keys; texts that JavaScript swaps go into `data-*` attributes (see the theme button).
- Repeated parts (header, navigation, footer, the demo upload form) live in `views/partials/` and are included, never copied.
- Semantic HTML. Every form field has a `<label>`, images have `alt`, everything works with a keyboard.
- Every POST form includes the CSRF field right after `<form>`: `<?php require __DIR__ . '/partials/csrf-field.html.php'; ?>`
  (from a partial: `__DIR__ . '/csrf-field.html.php'`). Without it the POST ends with 403.

## Styles and layout

- Styles only in `css/`, no inline `style=""`.
- Theme: `data-theme` on `<html>`, the choice saved in localStorage, the first visit follows `prefers-color-scheme`, and a short script in `<head>` sets it before rendering.
- Every view works on phone, tablet and desktop without horizontal scrolling.

<!-- TODO: once they exist, add:
- where JavaScript modules live, and that network calls use fetch with JSON, send the CSRF token in the `X-CSRF-Token` header, and show loading and error states
-->