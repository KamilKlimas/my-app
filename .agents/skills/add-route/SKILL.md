---
name: add-route
description: Add or change a page, form endpoint or controller action in DuelLab end to end - route in public/index.php, controller action, template with the shared header and footer partials, CSS, and curl verification of the status codes. Use for any task that creates or edits a URL.
---

# Add or change a route

Follow the conventions in `AGENTS.md`. Work through the steps in order; if a step does not apply, say so in your summary.
If the task needs something from the Roadmap in `AGENTS.md` (database, login, JavaScript, translations, ...), stop and agree the approach with the owner first.

## 1. Define the contract first

Write this down before touching code:

- Method and path, e.g. `GET /demos/{id}` or `POST /demos/{id}/training-set`.
- Who may call it: guest, USER or ADMIN, and whether ownership of the resource matters.
  Until login exists, write the rule down anyway and pass the mock `user` that fits it.
- Inputs (path, query, body) and their validation rules.
- Every response: the success case and each error status it can return.

## 2. Route

Register it in `public/index.php`, above the `/` route (the first match wins):

```php
$routing->get('/demos/{id}', DemoController::class, 'show');
$routing->post('/demos/{id}/delete', DemoController::class, 'delete');
```

- `Routing` has only `get()` and `post()`.
- `{name}` always matches digits (`\d+`). The value reaches the action as an `int` **named argument**, so the parameter
  name must match the placeholder: `{id}` → `show(int $id)`.
- A non-numeric segment (`/demos/abc`) matches no route and ends as 404.
- Need another placeholder type, another HTTP method or guards? That is a change to `src/Routing.php`: propose the diff to the owner first.

## 3. Controller action

In `src/controllers/<Area>Controller.php`: `namespace App\Controller;`, `final class <Area>Controller extends AppController`.
The router creates controllers with `new $class()`, so they can't take constructor arguments.

Read input → validate → render.

```php
public function show(int $id): void
{
    foreach (self::DEMOS as $demo) {
        if ($demo['id'] === $id) {
            $this->render('demo', ['pageTitle' => $demo['map'], 'activeNav' => 'demos', 'user' => self::USER, 'demo' => $demo]);

            return;
        }
    }

    http_response_code(404);
    (new DefaultController())->notFound();
}
```

- Query parameters: read `$_GET[...]`, cast, and accept only whitelisted values with a default (see `HotspotController::index()`).
- Missing record → 404 as above. There is no mechanism for 400 (malformed input) or 403 (not the owner, wrong role) yet;
  list in your summary which cases should get them.
- POST actions: after success redirect (`header('Location: /demos', true, 303); return;`) instead of rendering.
- No SQL, no HTML strings, no business rules in the controller. Mock data in a `private const` is fine until the database exists.

## 4. View (pages only)

- Template `public/views/<name>.html.php`. DuelLab pages start with `<?php require __DIR__ . '/partials/header.html.php'; ?>`,
  put the content in `<main>`, and end with the footer partial. Pass `pageTitle` (the header needs it), `activeNav` and `user`.
- New navigation entry: `$navItems` in `public/views/partials/header.html.php`.
- Escape every dynamic value with `$e(...)`; integers may use `(int)`. Never echo a raw variable.
- Forms: `<label for>`, the right input `type`, `required` / `pattern` mirroring the server rules, error messages next to the fields
  with `aria-invalid` and `aria-describedby` (see the scale field in `admin.html.php`).
- No inline styles; extend `public/css/` as described in `AGENTS.md` → Conventions → HTML and CSS.

## 5. Verify

```bash
docker compose exec -T php php -l <each changed .php file>
curl -i http://localhost:8080/<path>          # expected success status
curl -i http://localhost:8080/demos/abc       # no route matches → 404
curl -i http://localhost:8080/demos/999999    # missing record → 404
curl -i -X POST http://localhost:8080/<path>  # for POST routes
```

Open the page in the browser at 360, 768 and 1440 px and go through it with the keyboard (Tab order, visible focus).

## 6. Finish

- If the route accepts input or files, or changes data, check it against `AGENTS.md` → Security (escaping, whitelists, CSRF, authorization).
- Report: the route contract from step 1, changed files, and the verification results.
