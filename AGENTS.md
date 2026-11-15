# AGENTS.md — DuelLab

<!-- Canonical instructions for every AI coding agent. Claude Code loads this file through CLAUDE.md.
     Describe the code as it is; planned work gets one line in "Roadmap" until it is built.
     If the code and this file disagree, fix this file in the same change. -->

## Project

DuelLab is a web app for Counter-Strike 2 players. A user uploads a match demo (`.dem`), a worker parses it,
the app finds the duels the user lost, diagnoses why, and generates practice scenarios exported as a CS2 config file (`.cfg`).

Graded university course project (Politechnika Krakowska), built lab by lab:
- **No frameworks.** Routing, controllers and views are our own code.
- **The owner must explain every line at the demo.** Prefer small, readable changes; clever ones are acceptable with an explanation.
- **Build only what the current task needs.** Don't scaffold Roadmap items ahead of time.

## Current state (after M2: accounts; lab 5: light and dark theme)

- The demos, hotspots, training and admin pages render hard-coded mock data defined in the controllers; their tables come
  with later milestones. The header shows the logged-in user from the database: every page controller gets `CurrentUser` through a factory and passes `'user' => $this->currentUser->user()`.
- Login works against the database: `GET /login` (`SecurityController::login`) shows the form, `POST /login` (`authenticate`) checks it
  through `AuthService`. Success: `session_regenerate_id(true)`, `user_id` in the session, a new CSRF token, 303 to `/demos`.
  Failure: the form again with status 422, one generic message and the e-mail kept. An e-mail over 254 characters or a password
  over 72 bytes (bcrypt's limit) fails without a query. The session key is `CurrentUser::SESSION_KEY` (`user_id`).
- Registration: `GET /register` (`SecurityController::register`) and `POST /register` (`createAccount`) through
  `AuthService::register()`, which holds every rule (e-mail, name 3–32, password 8 characters to 72 bytes, matching confirmation)
  and returns error codes per field; the template translates them (`register.error.<code>`). Errors: 422 with the form, e-mail and name kept.
  Success and a taken e-mail both end with 303 to `/login?registered=1` (no account enumeration); the role is always USER.
  `PdoUserRepository::create()` turns SQLSTATE `23505` into `App\Repository\DuplicateEmail`; there is no `SELECT` before the `INSERT`.
- `POST /logout` (`SecurityController::logout`, the form in the header) runs `Session::destroy()`: empties the session data,
  expires the cookie with the same parameters, `session_destroy()`, then 303 to `/login`. The old session id is dead afterwards.
- Every route declares who may open it: the required last argument of `get()`/`post()` is an `App\Http\Access`
  (`Anyone`, `Guest`, `User`, `Admin`). After a route matches and before its controller is built, `Routing` calls
  `AccessGuard::check()`: a logged-in user on a `Guest` route (`/login`, `/register`) → 303 `/demos`; a guest on a `User`
  or `Admin` route → 303 `/login` (also for ids that don't exist, so a guest learns nothing about them); a USER on an
  `Admin` route (`/admin`) → 403. `POST /logout` and `/` are `Anyone`; `/` (`SecurityController::home`) redirects to `/demos` or `/login`.
  The guard reads the user through `App\Http\CurrentUser`: the id from the session, then `UserRepository::findById()`, once per request.
  An id whose account no longer exists is removed from the session and the request continues as a guest.
- Every request starts a session (`App\Http\Session::start()` in `public/index.php`, before the database): cookie `duellab_session`,
  `HttpOnly`, `SameSite=Lax`, strict mode, `Secure` only with `SESSION_SECURE=1`. It holds the CSRF token, after login the user id (`int`), and the chosen `locale`.
- Every POST is checked for a CSRF token in `public/index.php`, right after the session starts and before the database
  (`App\Http\Csrf::verifyRequest()`): a missing or wrong `_csrf` field renders the 403 `error` page, also for unknown URLs.
  The token (one per session, `bin2hex(random_bytes(32))`) reaches every template as `$csrfToken` from `View::render()`,
  and every POST form includes `partials/csrf-field.html.php`.
- `public/index.php` opens one `PDO` connection per request (`App\Database\ConnectionFactory` from `DatabaseConfig::fromEnvironment(getenv())`),
  and `SecurityController` uses it through `PdoUserRepository` → `AuthService`. If the database is down, every page renders the `error` page with 503 and the cause goes to `docker compose logs php`.
- User layers: `App\Model\User` and `Role`, `App\Repository\UserRepository` with `PdoUserRepository::findByEmail()` (case-insensitive), `findById()`, `create()` and `updateLocale()`,
  `App\Service\AuthService::authenticate()` and `register()`, and `AccountService::changeLocale()`. Routes are registered inside the `try` in `public/index.php`, after the services exist,
  because a controller factory (`fn () => new SecurityController(...)`) captures them when it is created.
- The database schema has `users` and `maps` (`docker/db/001_create_users.sql`, `002_create_maps.sql`); only `PdoUserRepository` reads and writes `users`, called by `POST /login`, `POST /register` and `POST /locale`.
  `docker/db/900_seed_demo_data.sql` runs after every migration and inserts six maps and two demo accounts (`ON CONFLICT DO NOTHING`,
  so it can be re-run by hand). It holds only bcrypt hashes; the demo password is in the README.
- PHPUnit 12 is installed as a dev dependency and configured in `phpunit.xml` (suite `tests/Unit`, fails on warnings,
  notices and deprecations). `tests/Unit/RoutingTest.php` covers the router (static, `{id}`, method, query string,
  route order, invalid controller, controller factories, the access check before the controller) through the fixtures `SpyController` and `InjectedController` in `tests/Unit/Fixture/`; `tests/Unit/Http/QueryTest.php`
  covers reading query parameters; `SessionTest.php` covers session access on a plain array and parsing `SESSION_SECURE`; `AccessGuardTest.php` covers every access level for a guest, a USER and an ADMIN; `CurrentUserTest.php` covers reading the user from the session id; `CsrfTest.php` covers the token and the POST check; `HttpExceptionTest.php` and `RedirectTest.php` cover status codes and local-only redirect targets;
  `tests/Unit/Database/DatabaseConfigTest.php` covers reading the `DB_*` variables and the DSN;
  `tests/Unit/Service/AuthServiceTest.php` covers login checks and registration rules, `AccountServiceTest.php` the stored language, against the in-memory double `Fixture/InMemoryUserRepository.php`.
- The only JavaScript is the theme: an inline anti-FOUC script in the header partial picks `data-theme` on `<html>`
  (saved `localStorage` `theme`, else `prefers-color-scheme`), and the module `public/js/theme.js` wires the Theme button.
  A switch cross-fades once via the View Transitions API (instant without support or with `prefers-reduced-motion`).
- Forms in the templates (demo upload, `/admin/maps`, ...) post to routes that don't exist yet, so with a valid token they get 404.
- `/` only redirects (see route access); `/demos` is "My demos"
  (stats, quick upload, table filtered by `?map=` and `?outcome=`). Unknown URLs render the `error` page (404) with the shared partials;
  a malformed query parameter (`?map[]=x`) renders it with 400. Every error status comes from a thrown `HttpException`
  rendered in one place; `Forbidden` (403) is thrown by the CSRF check and by `AccessGuard`; `Redirect` is used by `AccessGuard`, `/`, `POST /login`, `POST /register`, `POST /logout` and `POST /locale`. `ServiceUnavailable` (503) covers a failed database connection.
- Translations: `lang/pl.php` and `lang/en.php` are flat arrays of dotted keys (`login.title`) whose values are ICU MessageFormat
  patterns; `App\I18n\Translator` formats them with `MessageFormatter` (extension `intl`) and `View::render()` passes it to every
  template as the closure `$t('key', ['name' => value])`, next to `$e` and `$lang`. Syntax used: `{name}`,
  `{count, plural, one {…} few {…} many {…} other {…}}` (Polish has three integer forms, English `one`/`other`),
  `{at, date, ::MMMdHHmm}` (a `DateTimeInterface`), `{value, number}` (decimal comma in Polish). Controllers pass keys, codes and
  parameters, never text (`'pageTitle' => 'demos.title'`, tags as `['crosshair_off', ['degrees' => 14]]`). A missing key throws
  `MissingTranslation`, an invalid pattern a `LogicException`; `TranslatorTest` checks equal key sets and that every pattern compiles.
  The language (`App\I18n\Locale::resolve()`): a valid `locale` in the session wins, else `Accept-Language` (Polish preferred → `pl`,
  anything else or no header → `en`). The PL/EN buttons in the header are a form posting to `POST /locale` (`LocaleController::change`, `Anyone`):
  `locale` must be `pl` or `en` (else 400), it goes to the session and, for a logged-in user, to `users.locale` through `AccountService::changeLocale()`;
  then 303 back to the hidden `return_to` (the page's `$currentPath` from `View`), or `/` when it is not a local path.
  Login copies `users.locale` into the session; registration stores the language the form was filled in. Logout clears it with the session.
  Every page is translated; map callouts, weapons, map names, the game mode and the `.cfg` preview stay as data.

## Stack and services

PHP 8.3 (FPM on Alpine, with `intl` and full ICU data) behind nginx, PostgreSQL 17, plain CSS, Docker Compose for everything; no frontend or CSS libraries.
Composer handles PSR-4 autoloading (`App\Tests\` → `tests/` only in `autoload-dev`) and the dev dependency PHPUnit. Allowed packages when needed: `phpunit/phpunit` (dev), `phpmailer/phpmailer`.
Any other dependency needs the owner's approval.

| Service         | Purpose                                                           | Local address (default port, `.env` variable) |
|-----------------|-------------------------------------------------------------------|-----------------------------------------------|
| `server`        | nginx; serves `public/`, passes `*.php` to `php:9000`             | http://localhost:8080 (`APP_PORT`)            |
| `php`           | PHP-FPM; runs `composer install` on start if `vendor/` is missing | —                                             |
| `db`            | PostgreSQL 17                                                     | localhost:5433 (`POSTGRES_PORT`)              |
| `mailpit`       | catches outgoing e-mail                                           | http://localhost:8025 (`MAILPIT_PORT`)        |
| `pgadmin-wdpai` | pgAdmin, fixed container name `pg-admin`                          | http://localhost:5050 (`PGADMIN_PORT`)        |

- `./docker/db` is the Postgres init-script directory: its `.sql` files run in name order, only on an empty data directory.
  The schema lives there as `NNN_verb_object.sql` (three digits); a schema change is a new file, never an edit of an
  applied one. Every FK gets an explicit `ON DELETE` and a comment saying why. Strategy: `docs/architecture.md` → Migrations.
- Database data lives in the named volume `pg-data`, so it survives `docker compose down` and `up`. Because the volume
  is no longer empty, a new init script in `docker/db/` does not run on its own; only `docker compose down -v` (wipes the data) resets it.
- The `php` service gets only `DB_HOST`, `DB_PORT` (fixed: `db`, `5432`) and `DB_NAME`, `DB_USER`, `DB_PASSWORD` (from the `POSTGRES_*`
  values in `.env`) and `SESSION_SECURE` (default `0`), as an explicit `environment:` list, not `env_file`. It starts only after the `db` healthcheck (`pg_isready`) passes.

## Commands

```bash
docker compose up --build -d                                   # start
docker compose ps                                              # what is running
docker compose exec -T php php -l path/to/File.php             # syntax check, every changed PHP file
docker compose exec -T php composer dump-autoload              # after changing autoload in composer.json
docker compose exec -T php vendor/bin/phpunit                  # unit tests
docker compose logs -f php server                              # logs
docker compose exec -T db sh -c 'psql -U "$POSTGRES_USER" -d "$POSTGRES_DB"'   # SQL shell
curl -i http://localhost:8080/demos/1                          # check a route and its status code
```

Destructive, ask the owner first: `docker compose down -v`, and plain `docker compose down` once the database holds data you need.

## Layout

```
public/index.php              front controller: registers every route, then dispatches
public/views/*.html.php       page templates, one per page
public/views/partials/        header.html.php (doctype, <head>, top bar, nav), footer.html.php,
                              upload-form.html.php (the one demo upload form; $dropzoneClass picks the look),
                              csrf-field.html.php (the hidden CSRF field, included by every POST form)
public/css/                   stylesheets, linked by the header partial
public/js/                    browser scripts, loaded as modules by the footer partial (theme.js)
src/Routing.php               App\Routing: get()/post() routes, {param} placeholders, dispatch()
src/controllers/              App\Controller\*: AppController (empty base type the router checks) and one controller per area
src/View/                     App\View\View: renders a template with $e, $t, $lang and $csrfToken; built once in index.php
src/I18n/                     App\I18n\Locale (pl|en, picks the request language), Translator, MissingTranslation
lang/                         pl.php, en.php: translation catalogues, identical key sets
src/Http/                     App\Http\Query (typed $_GET access), Session (starts the session, typed $_SESSION access), Csrf (token, POST check), HttpException + BadRequest/Forbidden/NotFound/ServiceUnavailable
                              (400/403/404/503), Redirect (303 to a local path), Access (route levels), AccessGuard (checks them),
                              CurrentUser (the logged-in user from the session id)
src/Database/                 App\Database\DatabaseConfig (DB_* variables, DSN), ConnectionFactory (PDO), ConnectionFailed
src/Model/                    App\Model\*: User (readonly), Role (enum USER|ADMIN); no SQL
src/Repository/               App\Repository\*: UserRepository (interface), PdoUserRepository; the only place with SQL
src/Service/                  App\Service\*: AuthService (checks e-mail and password), AccountService (account settings); depend on repository interfaces
docker/                       Dockerfiles, nginx.conf, php entrypoint, db/ (Postgres init scripts)
docker-compose.yaml           services
docs/                         product overview, architecture, roadmap, grading criteria
public/AGENTS.md, src/AGENTS.md   extra rules for those folders (this file wins if they disagree)
composer.json                 PSR-4: App\Controller\ → src/controllers/, App\ → src/; dev: App\Tests\ → tests/
phpunit.xml                   PHPUnit config: suite tests/Unit, strict failure flags, source src/
```

## How a request works

nginx → `public/index.php` → `Routing::dispatch()` (method + regex) → `AccessGuard::check()` → `new XController()` → action → `View::render()`
→ template in `public/views/` with the partials. No route matched → `public/index.php` throws `NotFound`.
One `try` in `public/index.php` first opens the database connection, then dispatches. It catches `Redirect` (`Location` + 303),
any `HttpException` and `ConnectionFailed` (as `ServiceUnavailable`) → `DefaultController::error()`,
which sets the status and renders the `error` page. Throw before `render()`, otherwise part of the page is already sent.

- **Routes:** `$routing->get('/demos/{id}', DemoController::class, 'show', Access::User);` in `public/index.php`. The `Access` argument
  is required, so no route exists without a decision about who may open it. `{name}` matches digits only,
  is cast to `int` and passed as a **named argument**, so the action parameter must have the same name (`show(int $id)`).
  The first matching route wins; keep `/` last.
- **Controllers** extend `AppController` and are registered by class name (the router does `new $class()`) or, when they
  need collaborators (every page controller needs `View`), by a factory closure (`fn () => new XController($service)`), called only for the matched route. They end with
  `$this->render('<template>', [...])`. Usual variables: `pageTitle` (required by the header), `activeNav`
  (`demos|hotspots|training|admin`), `user` (`?App\Model\User` from `CurrentUser::user()`, `null` hides the nav).
- **Missing record:** `throw new NotFound();`. A new status gets its own `HttpException` subclass and a title and lead
  in both catalogues (`error.<status>.title`, `error.<status>.lead`). `Redirect` accepts only a local path (`/…`, not `//…` or `/\…`), against open redirects.
- **Query parameters** are read through `new Query($_GET)` in the controller and accepted only from a whitelist,
  with a default (`HotspotController`). A non-string value (`?map[]=x`) throws `BadRequest` → 400; an unknown value
  falls back to the default. Read the query before `render()`, otherwise part of the page is sent before the error page.

## Conventions

**PHP**
- `declare(strict_types=1);` in every class file and `public/index.php` (templates excluded).
- Type every parameter, return value and property. Classes are `final` by default (`AppController` is the abstract base).
- PSR-12 formatting. Short methods (aim for 20 lines or fewer); names describe responsibility.
- No `die()`, `exit`, `var_dump` or `print_r` in committed code.
- Controllers: no SQL, no HTML strings, no business rules. Mock arrays are the accepted exception for a page until the tables behind it exist.
- No duplicated logic: a query, validation rule or HTML fragment has exactly one home (mock-stage exception:
  the `theme` key check in the header's anti-FOUC script and in `theme.js`, because the first must run before any file loads).
- A new namespace needs a directory that matches it (`App\Repository\` → `src/Repository/`) or an explicit PSR-4 entry like
  `App\Controller\`, then `composer dump-autoload`. Linux (deployment) is case-sensitive.
- Comments explain *why*, not *what*; Polish or English, consistent within a file.

**HTML and CSS**
- Semantic HTML. Every input has a `<label>`, images have `alt`, everything works by keyboard, focus is visible.
- DuelLab pages start with `<?php require __DIR__ . '/partials/header.html.php'; ?>`, put content in `<main>`, and end with
  the footer partial. New nav entries go into `$navItems` in the header partial.
- CSS only in `public/css/`, no inline styles; use the tokens from `variables.css`. Files, in the order the header links them:
  `variables` (tokens) → `base` (reset, elements) → `layout` (top bar, header, nav, footer) → `components` → `demos` (demos page)
  → `states` (hover, `:focus-visible`, state classes) → `responsive` (breakpoint `max-width: 48rem`).
  Page-specific styles get their own `<page>.css`, linked before `states.css`.
- Colours only through tokens: `:root` holds the dark (default) values, `html[data-theme="light"]` overrides colour and backdrop tokens only (`--backdrop-image`, `--backdrop-veil`: the page photo in `public/images/` and how strongly it fades into the background).
  A new colour token gets a value in both blocks.
- Every view works at 360 px, 768 px and 1440 px without horizontal scrolling.

**HTTP**
- Status codes: malformed input → 400; not logged in → 401 for `/api/*`, redirect to `/login` for pages; logged in but not allowed → 403;
  missing record → 404; database down → 503. Today 400, 403, 404 and 503 exist as exceptions (401 comes with the first `/api/*` route): `/demos/abc` matches no route,
  `/demos/0` and unknown ids render the 404 error page, a non-string query parameter renders the 400 one.
- After a successful POST, redirect (Post/Redirect/Get) instead of rendering.

**Tests**
- PHPUnit unit tests in `tests/Unit/`: a valid and an invalid case per behaviour, no database in unit tests (playbook `add-unit-test`).

## Security (non-negotiable)

- Output: every dynamic value in a template goes through `$e(...)`, the closure `View::render()` passes to each template
  (`htmlspecialchars`, PHP 8 defaults `ENT_QUOTES | ENT_SUBSTITUTE`, UTF-8). Integers may be printed with a `(int)` cast.
  In JS insert text with `textContent`, not `innerHTML`.
- Input: cast and whitelist on the server. The client is never a source of user id or role.
- Every route that changes data needs a CSRF token and a server-side authorization check. The token check runs centrally
  for every POST (`Csrf::verifyRequest()` in `public/index.php`, `hash_equals`, 403 on failure); authorization is the route's `Access` level, checked by `AccessGuard` before the controller (ownership of a record comes with the `demos` table).
  Every POST form includes `partials/csrf-field.html.php`. Future `/api/*` calls send the token in the `X-CSRF-Token` header.
- Passwords only via `password_hash()` / `password_verify()`. SQL only as prepared statements with bound parameters;
  identifiers (sort column, direction) only from a whitelist.
- Sessions: `session_regenerate_id(true)` after login; cookie `HttpOnly`, `SameSite=Lax`, `Secure` in production; logout destroys the session.
- Uploads: size limit, real MIME type via `finfo`, extension whitelist, random file name, relative path in the DB;
  demos must start with the `PBDEMS2` header and are stored outside `public/`.
- Secrets live only in `.env` (gitignored). New variables go into `.env.example` with a safe placeholder.
- Known issue: templates sit under the web root, so `GET /views/<name>.html.php` runs a template without data and prints
  a PHP warning and stack trace with server paths (`display_errors` is on). Fixing it changes nginx or the layout: ask the owner.

## Roadmap (not built yet)

Don't scaffold these ahead of time. The change that builds an item moves its rules into the sections above.

- Database: the remaining tables from `docs/architecture.md`; a migration runner before the deploy; Mermaid ERD in the README and a full export in `database/duellab.sql`.
- The same layers (Controller → Service → Repository interface) for demos, hotspots and training sets, with "owner" checked on the record (`demos.user_id`), and `Unauthorized` (401) for `/api/*`.
- JavaScript modules in `public/js/`, all network calls through one `api.js`; JSON API under `/api/`.
- Uploads (demos, map radar images), the demo-parsing `worker`, `cron` jobs, e-mail through PHPMailer and Mailpit.

## Domain glossary

- **Demo:** a recorded CS2 match (`.dem`). **Tick:** one game update, 64 per second.
- **Lost duel:** the tracked player was killed by an enemy. **Trainable:** the killer was visible before the death and the player was not flashed.
- **Diagnosis:** why a duel was lost (`crosshair_off`, `moving_shot`, `slow_reaction`, `flashed`, `not_visible`, `crossfire`).
- **Scenario:** a reconstructed duel (player spawn + bot position). **Training set:** an ordered list of scenarios exported as `.cfg`.
- **Hotspot:** a map area where the player loses duels repeatedly. **Radar calibration:** `pos_x`, `pos_y`, `scale` of a map.

## Skills (task playbooks)

Canonical files: `.agents/skills/<name>/SKILL.md`. Any agent: when a task matches a row below, read that file first and follow it.
A new playbook is added in the change that builds the area it covers.

| Skill       | Use when                                                     |
|-------------|--------------------------------------------------------------|
| `add-route` | adding or changing a page, form endpoint or controller action |
| `add-unit-test` | writing, extending or fixing PHPUnit unit tests              |

## Working rules for agents

- Read the relevant code before editing. Change only what the task needs; don't reformat or rename unrelated code.
- **Ask before:** adding a dependency; starting a Roadmap item; rewriting `src/Routing.php`, `AppController`, `docker-compose.yaml`
  or the Dockerfiles (propose a diff and explain it instead); moving files to a new layout; any destructive command
  (`docker compose down -v`, deleting data, `git reset --hard`, force push).
- **Never:** read or print `.env`; commit secrets; edit `vendor/`; weaken escaping, CSRF or authorization, not even "temporarily"; commit or push unless asked.
- Git history is graded: one logical change per commit, imperative message with a scope, e.g. `feat(css): add hover and focus-visible states`,
  `chore(docker): ...`. Never one big catch-all commit. Work happens directly on `main` (no feature branches);
  every commit still starts with a type (`feat`, `fix`, `chore`, `docs`, `refactor`, `style`, `test`) and a scope.

## Definition of done

1. `php -l` passes for every changed PHP file.
2. Route changes: the URL returns the expected status (`curl -i`); non-numeric and unknown ids return 404.
3. Changed UI checked at 360/768/1440 px and by keyboard.
4. README updated if behaviour, routes or setup changed.
5. Final message: changed files (`git diff --stat`), what was verified, and what the owner should check by hand.
   Before each file's diff, say in one sentence why the change is made and, very briefly, what would break without it.
