# AGENTS.md: src/

Backend rules. They add to the root `AGENTS.md`.

## Responsibilities

- `Routing.php` maps an HTTP method and a URL pattern to a controller method. All routes are defined there.
- Controllers (`controllers/`, namespace `App\Controller`) read the request, validate input, call the logic and choose the view or response. They print no HTML and contain no SQL.

<!-- TODO: when you add them, say where services and repositories live (e.g. `src/services/`, `src/repositories/`) and that SQL lives only in repositories. -->

## Classes

- New logic goes into a class with one clear responsibility. No free functions, no logic in the global scope.
- A class receives its collaborators through the constructor.
- Controllers and data access talk through interfaces once repositories exist.

<!-- TODO: say where objects are created and wired together (e.g. in `public/index.php`). -->

## Authentication and session

- Passwords only through `password_hash()` and `password_verify()`.
- A failed login never reveals whether the e-mail exists.
- Regenerate the session id after login. The session cookie is `HttpOnly` and `SameSite=Lax`, and `Secure` in production.
- The user id and role come only from the server-side session, never from request data.
- Logout destroys the session and expires the cookie.
- Every POST, PUT, PATCH and DELETE request checks a CSRF token.

## Data, files and permissions

- SQL only through PDO prepared statements with bound parameters.
- Check permissions before every protected action. Hiding a button in HTML is not protection.
- Uploads: check the size, the real MIME type with `finfo` and the extension against a whitelist; save under a random name; store only the relative path in the database.

## Routes and errors

- Validate path parameters. Malformed id → 400, missing record → 404, logged-in user without rights → 403, not logged in → 401 for JSON or a redirect to the login page.
- No `die()`, `exit`, `var_dump` or `print_r` in committed code. Errors end with a proper HTTP status and a user-friendly page.