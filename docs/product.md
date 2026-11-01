# DuelLab: product overview

What the app does and for whom. Specs for individual features live in `specs/`; grading criteria in [criteria.md](criteria.md).
"DuelLab" is a working name.

## Problem

CS2 players lose duels in the same places for the same reasons. Existing tools (Leetify, Refrag and others) measure
aim or train typical angles; we found no tool that takes a player's own lost duels from their demos and turns them
into practice scenarios they can replay in the game.

## Roles

- **Guest:** can register, log in and reset a forgotten password by e-mail.
- **USER:** uploads demos and sees only their own data.
- **ADMIN:** everything a USER can do, plus the admin panel: maps, the processing queue and scheduled jobs.

## Main flow

1. The user logs in.
2. The user uploads a CS2 demo (`.dem`, max 300 MB) by drag-and-drop, or picks the replays folder once for auto-import (Chrome and Edge only). Files that are not CS2 demos are rejected with a clear message.
3. The demo is processed in the background. The page shows the status (pending → processing with % → ready, or failed with a reason) and refreshes it without reloading.
4. The demo page lists the duels the user lost. Each duel shows a diagnosis and, after selecting it, a 2D radar with the player, the killer, the line of sight and the aim direction.
5. Duels that cannot be trained stay visible but disabled, with the reason.
6. The user picks trainable duels (max 5 by default), orders them, names the set and saves it.
7. The user downloads the set as a `.cfg` file, writes it straight into the CS2 `cfg` folder (Chrome and Edge), or, on a phone, sends it to their e-mail.
8. In the game: an offline match on the same map, `exec duellab/<set>`, then the `N` key for each scenario.

## Other features

- **Hotspots:** where the user loses duels across all demos, filtered by map, period, issue and side. Shows a heat map, the top spots and a breakdown of reasons.
- **Admin, maps:** add and edit a map: name, radar image (PNG, JPG or WebP, max 5 MB) and calibration (`pos_x`, `pos_y`, `scale`).
- **Admin, queue:** processing status of all demos; retry failed ones.
- **Admin, scheduled jobs:** last run and result of each job.
- **Account:** language PL/EN, dark/light theme, opt-in weekly summary e-mail.

## Diagnoses

| Code            | Meaning                                         | Trainable |
|-----------------|-------------------------------------------------|-----------|
| `crosshair_off` | crosshair far from the killer at first contact  | yes |
| `moving_shot`   | the player fired while moving too fast          | yes |
| `slow_reaction` | long time from first contact to first damage    | yes |
| `flashed`       | the player was blind at the moment of death     | no (utility problem) |
| `not_visible`   | the killer was never visible before the death   | no (positioning, shown on the map only) |
| `crossfire`     | two or more enemies damaged the player          | optional, two-bot scenario |

Thresholds are not decided yet. They will be tuned against duels checked by hand in the in-game replay.