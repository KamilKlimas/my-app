# AGENTS.md: public/

Entry point and frontend rules. They add to the root `AGENTS.md`.

## Entry point

- `index.php` only bootstraps the app and hands the request to the router. No business logic here.

## Views

- Views in `views/` only present data prepared by a controller: no SQL, no business decisions.
- Escape every dynamic value with `$e()`, which `AppController::render()` passes to each view. Never call `htmlspecialchars()` in a view.
- Repeated parts (header, navigation, footer) live in shared view files and are included, never copied.
- Semantic HTML. Every form field has a `<label>`, images have `alt`, everything works with a keyboard.
- Every form includes the CSRF token field.

## Styles and layout

- Styles only in `css/`, no inline `style=""`.
- Theme: `data-theme` on `<html>`, the choice saved in localStorage, the first visit follows `prefers-color-scheme`, and a short script in `<head>` sets it before rendering.
- Every view works on phone, tablet and desktop without horizontal scrolling.

<!-- TODO: once they exist, add:
- where translations live (all user-facing text in Polish and English, including validation and error messages; new keys go into both)
- where JavaScript modules live, and that network calls use fetch with JSON, send the CSRF token in the `X-CSRF-Token` header, and show loading and error states
-->