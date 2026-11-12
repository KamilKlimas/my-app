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

## Current state (after lab 5: light and dark theme)

- Pages render hard-coded mock data defined in the controllers. There is no database access, login, session, CSRF
  or translation system yet.
- PHPUnit 12 is installed as a dev dependency and configured in `phpunit.xml` (suite `tests/Unit`, fails on warnings,
  notices and deprecations), but there are no tests yet.
- The only JavaScript is the theme: an inline anti-FOUC script in the header partial picks `data-theme` on `<html>`
  (saved `localStorage` `theme`, else `prefers-color-scheme`), and the module `public/js/theme.js` wires the Theme button.
  A switch cross-fades once via the View Transitions API (instant without support or with `prefers-reduced-motion`).
- Forms in the templates (`/login`, `/logout`, demo upload, `/admin/maps`, ...) post to routes that don't exist yet, so they get 404.
- `/` renders the start page (`DemoController::upload`: upload form, how it works, detected mistakes); `/demos` is "My demos"
  (stats, quick upload, table filtered by `?map=` and `?outcome=`). Unknown URLs render `not-found` with the shared partials.
- UI text is hard-coded: the login page is Polish, the other pages English.

## Stack and services

PHP 8.3 (FPM on Alpine) behind nginx, PostgreSQL 17, plain CSS, Docker Compose for everything; no frontend or CSS libraries.
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
- Database data lives in an **anonymous volume** (`pg-data` is declared but not mounted), so `docker compose down`
  followed by `up` starts with an empty database.
- The `php` service gets no environment variables yet.

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
                              upload-form.html.php (the one demo upload form; $dropzoneClass picks the look)
public/css/                   stylesheets, linked by the header partial
public/js/                    browser scripts, loaded as modules by the footer partial (theme.js)
src/Routing.php               App\Routing: get()/post() routes, {param} placeholders, dispatch()
src/controllers/              App\Controller\*: AppController (render() + $e) and one controller per area
docker/                       Dockerfiles, nginx.conf, php entrypoint, db/ (Postgres init scripts)
docker-compose.yaml           services
docs/                         product overview, architecture, roadmap, grading criteria
public/AGENTS.md, src/AGENTS.md   extra rules for those folders (this file wins if they disagree)
composer.json                 PSR-4: App\Controller\ → src/controllers/, App\ → src/; dev: App\Tests\ → tests/
phpunit.xml                   PHPUnit config: suite tests/Unit, strict failure flags, source src/
```

## How a request works

nginx → `public/index.php` → `Routing::dispatch()` (method + regex) → `new XController()` → action → `AppController::render()`
→ template in `public/views/` with the partials. No route matched → `DefaultController::notFound()` with status 404.

- **Routes:** `$routing->get('/demos/{id}', DemoController::class, 'show');` in `public/index.php`. `{name}` matches digits only,
  is cast to `int` and passed as a **named argument**, so the action parameter must have the same name (`show(int $id)`).
  The first matching route wins; keep `/` last.
- **Controllers** extend `AppController`, have no constructor arguments (the router does `new $class()`) and end with
  `$this->render('<template>', [...])`. Usual variables: `pageTitle` (required by the header), `activeNav`
  (`demos|hotspots|training|admin`), `user` (`['name' => ..., 'role' => 'USER'|'ADMIN']`, `null` hides the nav).
- **Missing record:** `http_response_code(404); (new DefaultController())->notFound(); return;`.
- **Query parameters** are read from `$_GET` in the controller and accepted only from a whitelist, with a default (`HotspotController`).

## Conventions

**PHP**
- `declare(strict_types=1);` in every class file and `public/index.php` (templates excluded).
- Type every parameter, return value and property. Classes are `final` by default (`AppController` is the abstract base).
- PSR-12 formatting. Short methods (aim for 20 lines or fewer); names describe responsibility.
- No `die()`, `exit`, `var_dump` or `print_r` in committed code.
- Controllers: no SQL, no HTML strings, no business rules. Mock arrays are the accepted exception until the database exists.
- No duplicated logic: a query, validation rule or HTML fragment has exactly one home (mock-stage exceptions: the 404 snippet, `USER` constants;
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
  → `upload` (start page) → `states` (hover, `:focus-visible`, state classes) → `responsive` (breakpoint `max-width: 48rem`).
  Page-specific styles get their own `<page>.css`, linked before `states.css`.
- Colours only through tokens: `:root` holds the dark (default) values, `html[data-theme="light"]` overrides colour and backdrop tokens only (`--backdrop-image`, `--backdrop-veil`: the page photo in `public/images/` and how strongly it fades into the background).
  A new colour token gets a value in both blocks. The PL/EN switch in the header isn't wired yet.
- Every view works at 360 px, 768 px and 1440 px without horizontal scrolling.

**HTTP**
- Status codes: malformed input → 400; not logged in → 401 for `/api/*`, redirect to `/login` for pages; logged in but not allowed → 403;
  missing record → 404. Today only 404 exists: `/demos/abc` matches no route, `/demos/0` and unknown ids render the not-found page.
- After a successful POST, redirect (Post/Redirect/Get) instead of rendering.

## Security (non-negotiable)

- Output: every dynamic value in a template goes through `$e(...)`, the closure `AppController::render()` passes to each template
  (`htmlspecialchars`, PHP 8 defaults `ENT_QUOTES | ENT_SUBSTITUTE`, UTF-8). Integers may be printed with a `(int)` cast.
  In JS insert text with `textContent`, not `innerHTML`.
- Input: cast and whitelist on the server. The client is never a source of user id or role.
- Every route that changes data needs a CSRF token and a server-side authorization check. Neither exists yet:
  the first POST route brings the CSRF mechanism (agree the design with the owner).
- Passwords only via `password_hash()` / `password_verify()`. SQL only as prepared statements with bound parameters;
  identifiers (sort column, direction) only from a whitelist.
- Sessions: `session_regenerate_id(true)` after login; cookie `HttpOnly`, `SameSite=Lax`, `Secure` in production; logout destroys the session.
- Uploads: size limit, real MIME type via `finfo`, extension whitelist, random file name, relative path in the DB;
  demos must start with the `PBDEMS2` header and are stored outside `public/`.
- Secrets live only in `.env` (gitignored). New variables go into `.env.example` with a safe placeholder (create it with the next variable).
- Known issue: templates sit under the web root, so `GET /views/<name>.html.php` runs a template without data and prints
  a PHP warning and stack trace with server paths (`display_errors` is on). Fixing it changes nginx or the layout: ask the owner.

## Roadmap (not built yet)

Don't scaffold these ahead of time. The change that builds an item moves its rules into the sections above.

- Database: schema as numbered SQL files in `docker/db/`, every FK with an explicit `ON DELETE` and a comment saying why;
  PDO with prepared statements; Mermaid ERD in the README and a full export in `database/duellab.sql`.
- Login with USER and ADMIN roles, sessions, CSRF; HTTP exceptions for 400/401/403/404 rendered in one place.
- Layers Controller → Service → Repository interface, wired by hand (no DI container, service locator or static facades).
- Translations (`lang/pl.php`, `lang/en.php` via `t('key')`).
- JavaScript modules in `public/js/`, all network calls through one `api.js`; JSON API under `/api/`.
- Uploads (demos, map radar images), the demo-parsing `worker`, `cron` jobs, e-mail through PHPMailer and Mailpit.
- PHPUnit unit tests in `tests/Unit/`: a valid and an invalid case per behaviour, no database in unit tests.

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
4. README updated if behaviour, routes or setup changed (create `README.md` with the first such change).
5. Final message: changed files (`git diff --stat`), what was verified, and what the owner should check by hand.
