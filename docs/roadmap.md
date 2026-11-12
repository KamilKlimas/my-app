# DuelLab: roadmap

Status as of 2026-11-13 (week 2). Plan assumes about 8–10 hours a week. Week 1 starts on 2026-11-02.
<!-- TODO: add the submission deadline and demo date once known. -->

## Done

- Topic chosen: practice scenarios from lost CS2 duels.
- Repository from the course template (`public/index.php`, `src/Routing.php`, `src/controllers/`, Docker).
- AI instruction files and project docs.
- UI prototype (5 web + 5 mobile views), submitted.
- Pages with mock data: start page with the upload form (`/`), "My demos" with `?map=` and `?outcome=` filters,
  404 page with the shared layout.
- Light and dark theme: colour tokens per theme, saved preference, per-theme backdrop photos (lab 5).
- M1 foundations (weeks 1–2, closed 2026-11-13, on schedule):
  - Router: static route, `{id}` matching digits only, whitelisted query parameters; 404 for unknown paths and ids,
    400 for a query parameter that is not a string.
  - PostgreSQL with data in the `pg-data` volume; migrations `001_create_users`, `002_create_maps` and the seed
    `900_seed_demo_data` load on an empty volume.
  - PHPUnit 12 in the `php` container with router and query tests.
  - `bot_place` checked in the game: works without a height correction (`architecture.md` → Open technical questions).
- Open from M1: compare the radar calibration of de_ancient, de_anubis and de_dust2 with the game files.
  Before the deploy (M3): a migration runner, and keeping the demo accounts out of production.

## Current milestone: M2, accounts (week 3)

Exit criteria: register, login, logout, session, roles, CSRF, PL/EN (the theme is already done).

## Next milestones

| Milestone | Weeks | Exit criteria |
|-----------|-------|---------------|
| M3 first deploy and upload | 4 | the skeleton is deployed; demo upload, queue, worker with the parser, status polling |
| M4 analysis | 5–6 | lost duels, diagnoses showing the measured value next to the threshold, Hotspots page with the database view, radar view; pattern check on 10 own demos |
| M5 training | 7 | training sets, "Train this spot" from a hotspot, `.cfg` export with the export date stored, tests for the generator |
| M6 admin and background | 8 | maps with radar upload, queue panel, CRON jobs, e-mail; before/after rate for each trained pattern |
| M7 polish and docs | 9–10 | WCAG report, responsiveness, Security Bingo table, README with ERD, `.sql` export |
| Buffer | 11–12 | fixes, demo rehearsal |

## If time runs short, cut in this order

1. Writing straight into the game folder (keep the plain download).
2. The weekly summary e-mail (password reset still covers the e-mail criterion).
3. The radar view (keep the duel list and the Hotspots table).

Never cut the core of the app's own framework or the deployment: they carry the most points.
Never cut "Train this spot" or the before/after rate either: they are what other tools don't do.

## What sets DuelLab apart

Other tools replay one death at a time (cs2demo.gg on a local server, Memorin in a browser simulation) or give
generic drills from aggregate stats (Leetify, Refrag). None of them checks whether training changed the next matches.

- **Pattern:** map + hotspot cell + diagnosis. "Train this spot" builds a set (max 5) from the trainable duels in one pattern.
- **Before/after rate:** deaths in the pattern per 100 rounds on that map, before and after the set was exported.
  Below 50 rounds after the export the page shows "not enough data yet"; otherwise it shows both rates and the raw counts,
  without claiming an improvement.
- **Pattern check (M4):** parse 10 own demos. If fewer than a third of the lost duels fall into patterns that repeat
  3 or more times, change the pattern definition (bigger cells, or no diagnosis) before M5.

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
