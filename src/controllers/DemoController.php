<?php

declare(strict_types=1);

namespace App\Controller;

final class DemoController extends AppController
{
    private const USER = ['name' => 'nightowl', 'role' => 'USER'];

    private const DEMOS = [
        ['id' => 1, 'map' => 'de_mirage', 'playedAt' => '2026-10-06 21:40', 'score' => [13, 10], 'lostDuels' => 9, 'status' => 'ready'],
        ['id' => 2, 'map' => 'de_inferno', 'playedAt' => '2026-10-06 20:55', 'score' => [8, 13], 'lostDuels' => 12, 'status' => 'ready'],
        ['id' => 3, 'map' => 'de_ancient', 'playedAt' => '2026-10-05 22:10', 'score' => null, 'lostDuels' => null, 'status' => 'processing', 'progress' => 40],
        ['id' => 4, 'map' => 'de_nuke', 'playedAt' => '2026-10-04 19:30', 'score' => null, 'lostDuels' => null, 'status' => 'failed', 'error' => 'invalid header', 'errorDetail' => 'Not a CS2 demo (invalid file header)'],
        ['id' => 5, 'map' => 'de_anubis', 'playedAt' => '2026-10-03 18:05', 'score' => [13, 7], 'lostDuels' => 6, 'status' => 'ready'],
        ['id' => 6, 'map' => 'de_dust2', 'playedAt' => '2026-10-02 19:15', 'score' => [11, 13], 'lostDuels' => 14, 'status' => 'ready'],
    ];

    private const STATS = ['demosAnalysed' => 18, 'lostDuels' => 214, 'trainableDuels' => 137];

    // Ordered by share; the first one is the "Top issue" on /demos. Tone picks the colour in CSS.
    private const ISSUES = [
        ['label' => 'Shooting while moving', 'share' => 31, 'tone' => 'danger',
            'description' => 'Fired before deceleration completed. Velocity exceeded weapon accuracy threshold.'],
        ['label' => 'Crosshair placement offset', 'share' => 24, 'tone' => 'accent',
            'description' => 'Crosshair rested more than 8° away from the head-level pre-aim when the enemy appeared.'],
        ['label' => 'Blind angle engagement', 'share' => 18, 'tone' => 'info',
            'description' => 'Dueling while flashed or through smoke, without a teammate ready to trade.'],
        ['label' => 'Unfavourable weapon matchup', 'share' => 15, 'tone' => 'success',
            'description' => 'Dry-peeking an AWP on a long sightline without utility or spacing.'],
    ];

    private const OUTCOMES = ['all' => 'All outcomes', 'win' => 'Wins', 'loss' => 'Losses'];

    public function upload(): void
    {
        $this->render('upload', [
            'pageTitle' => 'Upload a demo',
            'activeNav' => 'demos',
            'user' => self::USER,
            'issues' => self::ISSUES,
        ]);
    }

    public function index(): void
    {
        $filters = $this->selectedFilters();

        $this->render('demos', [
            'pageTitle' => 'My demos',
            'activeNav' => 'demos',
            'user' => self::USER,
            'stats' => self::STATS + [
                'duelsPerMatch' => round(self::STATS['lostDuels'] / self::STATS['demosAnalysed'], 1),
                'convertedPercent' => (int) round(self::STATS['trainableDuels'] / self::STATS['lostDuels'] * 100),
                'topIssue' => self::ISSUES[0],
            ],
            'upload' => ['file' => 'match_2026-10-06_de_mirage.dem', 'progress' => 64],
            'filters' => $filters,
            'mapOptions' => ['all' => 'All maps'] + array_combine(self::mapNames(), self::mapNames()),
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

        http_response_code(404);
        (new DefaultController())->notFound();
    }

    /** @return array{map: string, outcome: string} */
    private function selectedFilters(): array
    {
        $map = (string) ($_GET['map'] ?? 'all');
        $outcome = (string) ($_GET['outcome'] ?? 'all');

        return [
            'map' => in_array($map, self::mapNames(), true) ? $map : 'all',
            'outcome' => array_key_exists($outcome, self::OUTCOMES) ? $outcome : 'all',
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
                'tags' => ['Crosshair 14° off', 'Peeked into AWP'], 'trainable' => true, 'note' => null],
            ['round' => 5, 'time' => '0:48', 'weapons' => 'AK-47 vs M4A1-S', 'you' => 'A Ramp', 'enemy' => 'A Site',
                'tags' => ['Shooting while moving', 'Crosshair 6° off'], 'trainable' => true, 'note' => 'Repeats 3× in 30 days'],
            ['round' => 8, 'time' => '1:31', 'weapons' => 'Galil AR vs M4A4', 'you' => 'B Approach', 'enemy' => 'B Site',
                'tags' => ['Crosshair 21° off'], 'trainable' => true, 'note' => null],
            ['round' => 11, 'time' => '0:12', 'weapons' => 'AK-47 vs M4A4', 'you' => 'Connector', 'enemy' => 'CT Spawn',
                'tags' => ['Flashed 1.4 s'], 'trainable' => false, 'note' => 'Excluded: flashed'],
            ['round' => 14, 'time' => '1:05', 'weapons' => 'AK-47 vs USP-S', 'you' => 'A Site', 'enemy' => 'behind you',
                'tags' => ['Killed from behind'], 'trainable' => false, 'note' => 'Excluded: not trainable'],
        ];

        $requestedRound = (int) ($_GET['round'] ?? 5);
        $selectedDuel = $duels[1];

        foreach ($duels as $duel) {
            if ($duel['round'] === $requestedRound) {
                $selectedDuel = $duel;
            }
        }

        $this->render('demo', [
            'pageTitle' => $demo['map'],
            'activeNav' => 'demos',
            'user' => self::USER,
            'demo' => $demo,
            'match' => ['mode' => 'Premier', 'rounds' => 23, 'parsedIn' => 48],
            'trainableCount' => 6,
            'selectedCount' => 4,
            'duels' => $duels,
            'selectedDuel' => $selectedDuel,
            'metrics' => [
                ['label' => 'Crosshair offset', 'value' => '6', 'unit' => '°'],
                ['label' => 'Your speed at shot', 'value' => '138', 'unit' => 'u/s'],
                ['label' => 'Time to damage', 'value' => '410', 'unit' => 'ms'],
                ['label' => 'Enemies with sight', 'value' => '1', 'unit' => ''],
            ],
            'verdict' => 'Trainable. Aim was close, but you fired while still running — practise counter-strafing on this angle.',
        ]);
    }
}
