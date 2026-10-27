<?php

declare(strict_types=1);

namespace App\Controller;

final class HotspotController extends AppController
{
    private const USER = ['name' => 'nightowl', 'role' => 'USER'];

    private const OPTIONS = [
        'map' => [
            'label' => 'Map',
            'options' => [
                'de_mirage' => 'de_mirage',
                'de_inferno' => 'de_inferno',
                'de_ancient' => 'de_ancient',
                'de_nuke' => 'de_nuke',
                'de_anubis' => 'de_anubis',
            ],
        ],
        'days' => [
            'label' => 'Period',
            'options' => ['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days', 'all' => 'All time'],
        ],
        'issue' => [
            'label' => 'Issue',
            'options' => [
                'all' => 'All issues',
                'crosshair' => 'Crosshair placement',
                'moving' => 'Shooting while moving',
                'position' => 'Not visible (positioning)',
                'flashed' => 'Flashed',
            ],
        ],
        'side' => [
            'label' => 'Side',
            'options' => ['both' => 'Both', 'ct' => 'CT', 't' => 'T'],
        ],
    ];

    private const DEFAULTS = ['map' => 'de_mirage', 'days' => '30', 'issue' => 'all', 'side' => 'both'];

    public function index(): void
    {
        $filters = [];
        $selected = [];

        foreach (self::OPTIONS as $name => $filter) {
            $value = (string) ($_GET[$name] ?? self::DEFAULTS[$name]);
            $value = array_key_exists($value, $filter['options']) ? $value : self::DEFAULTS[$name];

            $selected[$name] = $value;
            $filters[$name] = $filter + ['selected' => $value];
        }

        $this->render('hotspots', [
            'pageTitle' => 'Hotspots',
            'activeNav' => 'hotspots',
            'user' => self::USER,
            'filters' => $filters,
            'query' => http_build_query($selected),
            'mapName' => $selected['map'],
            'periodLabel' => strtolower(self::OPTIONS['days']['options'][$selected['days']]),
            'totalDeaths' => 41,
            'spots' => [
                ['from' => 'A Ramp', 'to' => 'A Site', 'deaths' => 7, 'issue' => 'Shooting while moving', 'issueShare' => 57, 'trainable' => true, 'x' => 78, 'y' => 74],
                ['from' => 'Top Mid', 'to' => 'Window', 'deaths' => 4, 'issue' => 'Crosshair placement', 'issueShare' => 75, 'trainable' => true, 'x' => 52, 'y' => 48],
                ['from' => 'B Apartments', 'to' => 'B Site', 'deaths' => 3, 'issue' => 'Crosshair placement', 'issueShare' => 67, 'trainable' => true, 'x' => 18, 'y' => 28],
                ['from' => 'Connector', 'to' => 'CT Spawn', 'deaths' => 2, 'issue' => 'Flashed', 'issueShare' => 100, 'trainable' => false, 'x' => 40, 'y' => 62],
            ],
            'spotsTotal' => 12,
            'causes' => [
                ['id' => 'crosshair', 'label' => 'Crosshair placement', 'share' => 38],
                ['id' => 'moving', 'label' => 'Shooting while moving', 'share' => 31],
                ['id' => 'position', 'label' => 'Not visible (positioning)', 'share' => 19],
                ['id' => 'flashed', 'label' => 'Flashed', 'share' => 12],
            ],
        ]);
    }
}
