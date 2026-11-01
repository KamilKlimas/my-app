# AGENTS.md

## Project overview

DuelLab is a web app for Counter-Strike 2 players: it turns the duels a player lost in their match demos into
practice scenarios for the game. It is a graded university project, and the author must be able to explain every
line during the demo, so prefer small, readable changes.

Read before larger work:
- `docs/product.md`: what the app does, for whom, and the main user flows
- `docs/architecture.md`: layers, request flows, data model and the reasons behind them
- `docs/roadmap.md`: the current milestone, what is out of scope, pending decisions
- `docs/criteria.md`: the 36 grading criteria and where each one is met
- `docs/specs/`: one spec per larger feature, the definition of done for that work

Larger features are built from a spec in `docs/specs/<nnn-feature>.md` with the sections Summary, User stories,
Acceptance criteria, Requirements, Edge cases, Out of scope. If a larger task has no spec, ask whether to write one first.

When working on files in `src/` or `public/`, also read and follow `src/AGENTS.md` or `public/AGENTS.md`.

## Setup and commands

- First run: copy `.env.example` to `.env`
- Start: `docker compose up --build -d`, then open http://localhost:8080/demos
- Syntax check one file: `docker compose exec -T php php -l path/to/file.php`

<!-- TODO: add each command as soon as it exists, e.g.
- Tests: `docker compose exec -T php vendor/bin/phpunit`
- One test class: `docker compose exec -T php vendor/bin/phpunit --filter RoutingTest`
- Database shell: `docker compose exec -T <db-service> psql -U <user> <database>`
-->

## Code style

- PHP 8.3. `declare(strict_types=1);` in every PHP file; type all parameters, return values and properties.
- Object-oriented backend only; procedural code only in `public/index.php`. A procedural backend fails the course.
- No frameworks. Ask before adding any Composer dependency.
- A query, a validation rule or an HTML fragment has exactly one home; reuse it instead of copying it.
- Comments explain why, not what.
- Never modify anything in `vendor/`.

<!-- TODO: confirm with the lecturer which language is expected for code, comments and commit messages. -->

## Testing

- Run the syntax check on every PHP file you changed.
- After changing a route, request the correct URL and a wrong one. The expected statuses are in `src/AGENTS.md`.

<!-- TODO: once PHPUnit is set up: run the full suite before finishing; new logic needs a unit test with one valid and one invalid case. -->

## Security

- Never read, print or commit `.env`. New configuration goes into `.env.example` with a placeholder value.
- Ask before destructive commands: deleting Docker volumes or data, `git reset --hard`, force push.

## Commits and finishing a task

- Keep changes small and don't touch files unrelated to the task.
- One logical change per commit, e.g. `feat(routing): validate id parameter`. Commit only when asked.
- Finish with `git diff` and a short summary: changed files and how you verified them.

## Task playbooks

Step-by-step procedures live in `.claude/skills/<name>/SKILL.md`. Claude Code loads them as skills.
Other agents: when a task matches one below, read that file first and follow it.

<!-- TODO: list every skill when you add it, e.g.
- `.claude/skills/add-route/SKILL.md`: adding or changing a route
-->