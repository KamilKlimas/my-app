---
name: add-route
description: Add or change a page, form endpoint or controller action in DuelLab end to end - route in public/index.php, controller action, template with the shared header and footer partials, CSS, and curl verification of the status codes. Use for any task that creates or edits a URL.
---

# Add or change a route

Follow the conventions in `AGENTS.md`. Work through the steps in order; if a step does not apply, say so in your summary.
If the task needs something from the Roadmap in `AGENTS.md` (uploads, the worker, the JSON API, e-mail, ...), stop and agree the approach with the owner first.

## 1. Define the contract first

Write this down before touching code:

- Method and path, e.g. `GET /demos/{id}` or `POST /demos/{id}/training-set`.
- Who may call it: an `App\Http\Access` level (`Anyone`, `Guest`, `User`, `Admin`), and whether the record must belong to the
  current user (ownership is a check in the action, not an access level).
- Inputs (path, query, body) and their validation rules.
- Every response: the success case and each error status it can return.

## 2. Route

Register it in `public/index.php`, inside the `try`, after the services exist and above the `/` route (the first match wins):

```php
$demoController = fn (): DemoController => new DemoController($view, $currentUser);

$routing->get('/demos/{id}', $demoController, 'show', Access::User);
$routing->post('/demos/{id}/delete', $demoController, 'delete', Access::User);
```

- `Routing` has only `get()` and `post()`. The last argument (`Access`) is required: no route exists without a decision about who may open it.
- `AccessGuard` checks it after the match and before the controller is built: guest on `User`/`Admin` → 303 `/login`,
  logged-in user on `Guest` → 303 `/demos`, USER on `Admin` → 403.
- `{name}` always matches digits (`\d+`). The value reaches the action as an `int` **named argument**, so the parameter
  name must match the placeholder: `{id}` → `show(int $id)`.
- A non-numeric segment (`/demos/abc`) matches no route and ends as 404.
- Need another placeholder type or another HTTP method? That is a change to `src/Routing.php`: propose the diff to the owner first.

## 3. Controller action

In `src/controllers/<Area>Controller.php`: `namespace App\Controller;`, `final class <Area>Controller extends AppController`.
A controller that renders takes `App\View\View` (and usually `CurrentUser`) through its constructor and is registered
with a factory closure, as above. Only a controller without collaborators may be registered by class name.

Read input → validate → render, or throw.

```php
public function show(int $id): void
{
    foreach (self::DEMOS as $demo) {
        if ($demo['id'] === $id) {
            $this->view->render('demo', [
                'pageTitle' => 'demo.title',
                'pageTitleParameters' => ['map' => $demo['map']],
                'activeNav' => 'demos',
                'user' => $this->currentUser->user(),
                'demo' => $demo,
            ]);

            return;
        }
    }

    throw new NotFound();
}
```

- Errors are thrown, never rendered by the action: `NotFound` (404), `BadRequest` (400), `Forbidden` (403, e.g. not the owner),
  all from `App\Http`. `public/index.php` renders them in one place. Throw before `render()`, otherwise part of the page is already sent.
- Query and body parameters: `new Query($_GET)` / `new Query($_POST)`, `->string('name', 'default')`; a non-string value throws `BadRequest`.
  Accept only whitelisted values with a default (see `HotspotController::index()`).
- POST actions: the CSRF token is checked centrally before dispatch, so the action does not check it. After success
  `throw new Redirect('/demos');` (303, local paths only). Validation errors: `http_response_code(422)` and render the form
  again with the kept values and error codes (see `SecurityController::createAccount()`).
- Controllers pass translation keys and error codes, never user-facing text.
- No SQL, no HTML strings, no business rules in the controller (those go to a service). Mock data in a `private const` is fine
  until the table exists.

## 4. View (pages only)

- Template `public/views/<name>.html.php`. DuelLab pages start with `<?php require __DIR__ . '/partials/header.html.php'; ?>`,
  put the content in `<main>`, and end with the footer partial. Pass `pageTitle` (a translation key, required by the header),
  `activeNav` and `user`.
- New navigation entry: `$navItems` in `public/views/partials/header.html.php` (label is a key).
- Escape every dynamic value with `$e(...)`; integers may use `(int)`. Never echo a raw variable.
- Every visible text is `$e($t('area.key'))`, with the key in both `lang/pl.php` and `lang/en.php` (`TranslatorTest` fails otherwise).
  Values inside a sentence are parameters: `$t('error.eyebrow', ['status' => 404])` with `{status}` in the catalogue.
- Every POST form includes `partials/csrf-field.html.php` right after `<form>`.
- Forms: `<label for>`, the right input `type`, `required` / `minlength` mirroring the server rules, error messages next to the fields
  with `aria-invalid` and `aria-describedby` (see `register.html.php`).
- No inline styles; extend `public/css/` as described in `AGENTS.md` → Conventions → HTML and CSS.

## 5. Verify

```bash
docker compose exec -T php php -l <each changed .php file>
docker compose exec -T php vendor/bin/phpunit
curl -i http://localhost:8080/<path>                          # as a guest: 303 /login for User/Admin routes
curl -i -X POST http://localhost:8080/<path>                  # POST without a token → 403
curl -i -H 'Accept-Language: pl' http://localhost:8080/<path> # Polish texts, <html lang="pl">
```

As a logged-in user (keep the cookie with `curl -c jar -b jar`, take `_csrf` from the login form): the success status,
`/demos/abc` → 404, an unknown id → 404, a malformed query (`?map[]=x`) → 400, and a USER on an `Admin` route → 403.
Open the page in the browser at 360, 768 and 1440 px in both languages and go through it with the keyboard (Tab order, visible focus).

## 6. Finish

- If the route accepts input or files, or changes data, check it against `AGENTS.md` → Security (escaping, whitelists, CSRF, authorization).
- Update the routes table in `README.md` and, if the project state changed, "Current state" in `AGENTS.md`.
- Report: the route contract from step 1, changed files, and the verification results.
