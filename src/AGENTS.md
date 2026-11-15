# AGENTS.md: src/

Backend rules. They add to the root `AGENTS.md`; if the two ever disagree, the root file wins.
Login, sessions, CSRF, SQL and translations exist since M2; the upload rules apply once uploads exist (see the Roadmap in the root file).

## Responsibilities

- `Routing.php` (`App\Routing`) matches an HTTP method and a URL pattern to a controller method and, before it builds
  the controller, asks `App\Http\AccessGuard` whether the route's `Access` level allows the request. The router knows the guard,
  not the session. The routes themselves are registered in `public/index.php`.
- Controllers (`controllers/`, namespace `App\Controller`) read the request, validate input, call the logic and choose the view or response. They print no HTML and contain no SQL.
- Models (`Model/`, `App\Model`) are immutable data objects (`readonly`) with no SQL and no PDO. Fixed value sets are backed enums (`Role`).
- Repositories (`Repository/`, `App\Repository`): an interface (`UserRepository`) and its PDO implementation (`PdoUserRepository`).
  They are the only place with SQL and map each row to a model by hand (`mapRow()`), not with `FETCH_CLASS`.
- Services (`Service/`, `App\Service`) hold business logic and depend on repository interfaces, never on `PDO`:
  `AuthService` (login check, registration rules), `AccountService` (account settings such as the language).
- `Http/` holds the request-side classes: `Session`, `Csrf`, `Query`, `CurrentUser` (the logged-in user), `Access` and `AccessGuard`
  (who may open a route), and the `HttpException` family with `Redirect`.
- `View/View.php` renders a template and gives it `$e`, `$t`, `$lang` and `$csrfToken`; `I18n/` holds `Locale` (which language) and
  `Translator` (key → text). Controllers pass translation keys, never text.

## Classes

- New logic goes into a class with one clear responsibility. No free functions, no logic in the global scope.
- A controller without collaborators is registered by class name (the router calls `new $class()`). One that needs
  collaborators takes them through its constructor and is registered with a factory closure in `public/index.php`.
  Other classes (services, repositories) also receive their collaborators through the constructor.
- Controllers and data access talk through interfaces.
- Objects are created and wired by hand in `public/index.php` (`PDO` → repository → service → controller factory).
  No DI container, service locator or static access.

## Authentication and session

- Passwords only through `password_hash()` and `password_verify()`.
- A failed login never reveals whether the e-mail exists.
- The session starts only in `App\Http\Session::start()`, called once from `public/index.php`; code reads and writes it
  through `Session`, never `$_SESSION` directly. It holds only the user id, the CSRF token and the chosen `locale`; name and role come from the database.
- Regenerate the session id after login. The session cookie is `HttpOnly` and `SameSite=Lax`, and `Secure` when `SESSION_SECURE=1` (production).
- The user id and role come only from the server-side session, never from request data. Code reads the logged-in user only
  through `App\Http\CurrentUser` (session key `CurrentUser::SESSION_KEY`, account loaded once per request).
- Logout destroys the session and expires the cookie.
- Every POST, PUT, PATCH and DELETE request checks a CSRF token. Today `App\Http\Csrf::verifyRequest()` does it for every POST
  in `public/index.php`, before dispatch; actions don't check it themselves. Compare tokens only with `hash_equals()`.

## Data, files and permissions

- SQL only through PDO prepared statements with bound parameters.
- Check permissions before every protected action. Hiding a button in HTML is not protection.
- Uploads: check the size, the real MIME type with `finfo` and the extension against a whitelist; save under a random name; store only the relative path in the database.

## Routes and errors

- A `{param}` placeholder matches digits only, so a non-numeric id (`/demos/abc`) matches no route and ends as 404.
  Missing record → 404. Malformed query or body input → 400, logged-in user without rights → 403,
  not logged in → 401 for `/api/*` or a redirect to `/login` for pages (400, 403 and 404 exist so far; `AccessGuard` throws the redirect and the 403). A failed database connection → 503.
- Error statuses are thrown as `App\Http\HttpException` subclasses and rendered only in `public/index.php`;
  redirects are thrown as `App\Http\Redirect` (303, local paths only).
- No `die()`, `exit`, `var_dump` or `print_r` in committed code. Errors end with a proper HTTP status and a user-friendly page.