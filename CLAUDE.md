@AGENTS.md

<!-- AGENTS.md is the single source of truth for project rules. Put only Claude Code specifics below. -->

## Claude Code

- Project skill: `/add-route`. Each `.claude/skills/<name>/SKILL.md` is a thin wrapper that points to the canonical playbook in
  `.agents/skills/<name>/SKILL.md`, which other agents read too. Edit the canonical file, never the wrapper body.
  If you change a skill's `description`, change it in both files. A new skill gets both files and a row in the AGENTS.md Skills table.
- Use plan mode before changes that touch `src/Routing.php`, `src/controllers/AppController.php`, `public/index.php`,
  `public/views/partials/`, `docker/` (including the `docker/db/` init scripts), `docker-compose.yaml` or `composer.json`,
  or more than three files.
- `.claude/settings.json` denies reading `.env`. Don't work around it: no `cat .env`, no printing environment variables.
- Personal, machine-specific notes go in `CLAUDE.local.md` (gitignored), not in this file.
