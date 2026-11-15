<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\CurrentUser;
use App\Http\NotFound;
use App\Http\Query;
use App\View\View;

final class DemoController extends AppController
{
    private const DEMOS = [
        ['id' => 1, 'map' => 'de_mirage', 'playedAt' => '2026-10-06 21:40', 'score' => [13, 10], 'lostDuels' => 9, 'status' => 'ready'],
        ['id' => 2, 'map' => 'de_inferno', 'playedAt' => '2026-10-06 20:55', 'score' => [8, 13], 'lostDuels' => 12, 'status' => 'ready'],
        ['id' => 3, 'map' => 'de_ancient', 'playedAt' => '2026-10-05 22:10', 'score' => null, 'lostDuels' => null, 'status' => 'processing', 'progress' => 40],
        ['id' => 4, 'map' => 'de_nuke', 'playedAt' => '2026-10-04 19:30', 'score' => null, 'lostDuels' => null, 'status' => 'failed', 'error' => 'invalid_header'],
        ['id' => 5, 'map' => 'de_anubis', 'playedAt' => '2026-10-03 18:05', 'score' => [13, 7], 'lostDuels' => 6, 'status' => 'ready'],
        ['id' => 6, 'map' => 'de_dust2', 'playedAt' => '2026-10-02 19:15', 'score' => [11, 13], 'lostDuels' => 14, 'status' => 'ready'],
    ];

    private const STATS = ['demosAnalysed' => 18, 'lostDuels' => 214, 'trainableDuels' => 137];

    // Ordered by share; the first one is the "Top issue" on /demos. Tone picks the colour in CSS.
    private const ISSUES = [
        ['code' => 'moving', 'share' => 31, 'tone' => 'danger'],
        ['code' => 'crosshair', 'share' => 24, 'tone' => 'accent'],
        ['code' => 'blind_angle', 'share' => 18, 'tone' => 'info'],
        ['code' => 'weapon_matchup', 'share' => 15, 'tone' => 'success'],
    ];

    private const OUTCOMES = ['all', 'win', 'loss'];

    public function __construct(
        private readonly View $view,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function index(): void
    {
        $filters = $this->selectedFilters();

        $this->view->render('demos', [
            'pageTitle' => 'demos.title',
            'activeNav' => 'demos',
            'user' => $this->currentUser->user(),
            'stats' => self::STATS + [
                'duelsPerMatch' => round(self::STATS['lostDuels'] / self::STATS['demosAnalysed'], 1),
                'convertedPercent' => (int) round(self::STATS['trainableDuels'] / self::STATS['lostDuels'] * 100),
                'topIssue' => self::ISSUES[0],
            ],
            'upload' => ['file' => 'match_2026-10-06_de_mirage.dem', 'progress' => 64],
            'filters' => $filters,
            'mapNames' => self::mapNames(),
            'outcomeOptions' => self::OUTCOMES,
            'demos' => $this->filterDemos($filters['map'], $filters['outcome']),
            'totalDemos' => count(self::DEMOS),
        ]);
    }

    public function show(int $id): void
    {
        foreach (self::DEMOS as $demo) {
            if ($demo['id'] === $id && $demo['status'] === 'ready') {
                $this->renderDemo($demo);

                return;
            }
        }

        throw new NotFound();
    }

    /** @return array{map: string, outcome: string} */
    private function selectedFilters(): array
    {
        $query = new Query($_GET);
        $map = $query->string('map', 'all');
        $outcome = $query->string('outcome', 'all');

        return [
            'map' => in_array($map, self::mapNames(), true) ? $map : 'all',
            'outcome' => in_array($outcome, self::OUTCOMES, true) ? $outcome : 'all',
        ];
    }

    /** @return list<string> */
    private static function mapNames(): array
    {
        return array_values(array_unique(array_column(self::DEMOS, 'map')));
    }

    // Stands in for a WHERE clause until demos come from the database.
    private function filterDemos(string $map, string $outcome): array
    {
        $demos = array_filter(self::DEMOS, function (array $demo) use ($map, $outcome): bool {
            if ($map !== 'all' && $demo['map'] !== $map) {
                return false;
            }

            if ($outcome === 'all') {
                return true;
            }

            // Demos without a score (still parsing or failed) are neither a win nor a loss.
            return $demo['score'] !== null && ($demo['score'][0] > $demo['score'][1]) === ($outcome === 'win');
        });

        return array_values($demos);
    }

    private function renderDemo(array $demo): void
    {
        $duels = [
            ['round' => 3, 'time' => '1:12', 'weapons' => 'AK-47 vs AWP', 'you' => 'Top Mid', 'enemy' => 'Window',
                'tags' => [['crosshair_off', ['degrees' => 14]], ['peeked_awp', []]], 'trainable' => true, 'note' => null],
            ['round' => 5, 'time' => '0:48', 'weapons' => 'AK-47 vs M4A1-S', 'you' => 'A Ramp', 'enemy' => 'A Site',
                'tags' => [['moving', []], ['crosshair_off', ['degrees' => 6]]], 'trainable' => true,
                'note' => ['repeats', ['count' => 3, 'days' => 30]]],
            ['round' => 8, 'time' => '1:31', 'weapons' => 'Galil AR vs M4A4', 'you' => 'B Approach', 'enemy' => 'B Site',
                'tags' => [['crosshair_off', ['degrees' => 21]]], 'trainable' => true, 'note' => null],
            ['round' => 11, 'time' => '0:12', 'weapons' => 'AK-47 vs M4A4', 'you' => 'Connector', 'enemy' => 'CT Spawn',
                'tags' => [['flashed', ['seconds' => 1.4]]], 'trainable' => false, 'note' => ['excluded_flashed', []]],
            ['round' => 14, 'time' => '1:05', 'weapons' => 'AK-47 vs USP-S', 'you' => 'A Site', 'enemy' => null,
                'tags' => [['killed_from_behind', []]], 'trainable' => false, 'note' => ['excluded_not_trainable', []]],
        ];

        $requestedRound = (int) (new Query($_GET))->string('round', '5');
        $selectedDuel = $duels[1];

        foreach ($duels as $duel) {
            if ($duel['round'] === $requestedRound) {
                $selectedDuel = $duel;
            }
        }

        $this->view->render('demo', [
            'pageTitle' => 'demo.title',
            'pageTitleParameters' => ['map' => $demo['map']],
            'activeNav' => 'demos',
            'user' => $this->currentUser->user(),
            'demo' => $demo,
            'match' => ['mode' => 'Premier', 'rounds' => 23, 'parsedIn' => 48],
            'trainableCount' => 6,
            'selectedCount' => 4,
            'duels' => $duels,
            'selectedDuel' => $selectedDuel,
            'metrics' => [
                ['code' => 'crosshair_offset', 'value' => 6, 'unit' => '°'],
                ['code' => 'speed_at_shot', 'value' => 138, 'unit' => 'u/s'],
                ['code' => 'time_to_damage', 'value' => 410, 'unit' => 'ms'],
                ['code' => 'enemies_with_sight', 'value' => 1, 'unit' => ''],
            ],
            'verdict' => 'counter_strafe',
        ]);
    }
}
