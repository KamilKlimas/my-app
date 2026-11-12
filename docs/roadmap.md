# DuelLab: roadmap

Status as of 2026-11-01. Plan assumes about 8–10 hours a week. Week 1 starts on 2026-11-02.
<!-- TODO: add the submission deadline and demo date once known. -->

## Done

- Topic chosen: practice scenarios from lost CS2 duels.
- Repository from the course template (`public/index.php`, `src/Routing.php`, `src/controllers/`, Docker).
- AI instruction files and project docs.

<!-- TODO: move "UI prototype (5 web + 5 mobile views)" here when it is submitted. -->

## Current milestone: M1, foundations (weeks 1–2)

Exit criteria:
- Router handles a static route, a regex route, a validated `{id}` parameter and a query parameter, with 400/404.
- PostgreSQL runs in Docker Compose with a persistent volume; the first migration and seed load.
- PHPUnit runs in the container with first router tests.
- Two-hour time-boxed check of `bot_place` in the game; result recorded in `architecture.md` → Open technical questions.

## Next milestones

| Milestone | Weeks | Exit criteria |
|-----------|-------|---------------|
| M2 accounts | 3 | register, login, logout, session, roles, CSRF, PL/EN, dark/light theme |
| M3 first deploy and upload | 4 | the skeleton is deployed; demo upload, queue, worker with the parser, status polling |
| M4 analysis | 5–6 | lost duels, diagnoses, Hotspots page with the database view, radar view |
| M5 training | 7 | training sets, `.cfg` export, tests for the generator |
| M6 admin and background | 8 | maps with radar upload, queue panel, CRON jobs, e-mail |
| M7 polish and docs | 9–10 | WCAG report, responsiveness, Security Bingo table, README with ERD, `.sql` export |
| Buffer | 11–12 | fixes, demo rehearsal |

## If time runs short, cut in this order

1. Writing straight into the game folder (keep the plain download).
2. The weekly summary e-mail (password reset still covers the e-mail criterion).
3. The radar view (keep the duel list and the Hotspots table).

Never cut the core of the app's own framework or the deployment: they carry the most points.

## Optional, after M7 if time allows

Both only add a new way for demos to reach the existing queue; the worker and the analysis stay the same.

- **Link a Steam account and detect new matches.** Steam OpenID 2.0 adds a "Connect Steam" button to the password
  account (the password login stays, it carries the login criterion) and stores the SteamID64. With the user's Game
  Authentication Code and one match share code, a scheduled job calls `GetNextMatchSharingCode` and lists new Premier
  and Competitive matches. The user still downloads the demo in CS2 and uploads it.
- **Detect new FACEIT matches.** The FACEIT Data API (standard key) finds the player by SteamID64 and lists their matches
  with a link to the match room, where the user downloads the demo and uploads it.

Needs: `users.steam_id`, `demos.source` and a unique `demos.external_match_id`, `STEAM_API_KEY` and `FACEIT_API_KEY`
in `.env.example`.

## Out of scope this semester

- Valorant: Riot's API needs a production key and player sign-in through Riot.
- Automatic demo download from Steam (needs a bot talking to Valve's game coordinator).
- Automatic demo download from FACEIT: the Downloads API needs an approved application (about 30 days) and has no
  public price; FACEIT moved demo downloads to a paid model in 2024.
- A native mobile app: the web app is responsive instead.
- Comparing players with each other: needs data from many users.
- Rank prediction and generic aim statistics: Leetify already covers them.
- Exporting scenarios as a server plugin profile (only for players running their own server).
- Ranking scenarios by round impact (win-probability change at each death) using the author's win-probability engine. Stretch goal after M7; computed in the worker, stored as one value per duel.
