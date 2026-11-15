<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\CurrentUser;
use App\Http\Query;
use App\View\View;

final class HotspotController extends AppController
{
    private const OPTIONS = [
        'map' => [
            'label' => 'common.map',
            'options' => ['de_mirage' => null, 'de_inferno' => null, 'de_ancient' => null, 'de_nuke' => null, 'de_anubis' => null],
        ],
        'days' => [
            'label' => 'hotspots.filter.period',
            'options' => [
                '7' => 'hotspots.period.7',
                '30' => 'hotspots.period.30',
                '90' => 'hotspots.period.90',
                'all' => 'hotspots.period.all',
            ],
        ],
        'issue' => [
            'label' => 'hotspots.filter.issue',
            'options' => [
                'all' => 'hotspots.filter.issue.all',
                'crosshair' => 'issue.crosshair',
                'moving' => 'issue.moving',
                'position' => 'issue.position',
                'flashed' => 'issue.flashed',
            ],
        ],
        'side' => [
            'label' => 'hotspots.filter.side',
            'options' => ['both' => 'hotspots.filter.side.both', 'ct' => 'hotspots.filter.side.ct', 't' => 'hotspots.filter.side.t'],
        ],
    ];

    private const DEFAULTS = ['map' => 'de_mirage', 'days' => '30', 'issue' => 'all', 'side' => 'both'];

    public function __construct(
        private readonly View $view,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function index(): void
    {
        $query = new Query($_GET);
        $filters = [];
        $selected = [];

        foreach (self::OPTIONS as $name => $filter) {
            $value = $query->string($name, self::DEFAULTS[$name]);
            $value = array_key_exists($value, $filter['options']) ? $value : self::DEFAULTS[$name];

            $selected[$name] = $value;
            $filters[$name] = $filter + ['selected' => $value];
        }

        $this->view->render('hotspots', [
            'pageTitle' => 'hotspots.title',
            'activeNav' => 'hotspots',
            'user' => $this->currentUser->user(),
            'filters' => $filters,
            'query' => http_build_query($selected),
            'mapName' => $selected['map'],
            'periodKey' => self::OPTIONS['days']['options'][$selected['days']],
            'totalDeaths' => 41,
            'spots' => [
                ['from' => 'A Ramp', 'to' => 'A Site', 'deaths' => 7, 'issue' => 'moving', 'issueShare' => 57, 'trainable' => true, 'x' => 78, 'y' => 74],
                ['from' => 'Top Mid', 'to' => 'Window', 'deaths' => 4, 'issue' => 'crosshair', 'issueShare' => 75, 'trainable' => true, 'x' => 52, 'y' => 48],
                ['from' => 'B Apartments', 'to' => 'B Site', 'deaths' => 3, 'issue' => 'crosshair', 'issueShare' => 67, 'trainable' => true, 'x' => 18, 'y' => 28],
                ['from' => 'Connector', 'to' => 'CT Spawn', 'deaths' => 2, 'issue' => 'flashed', 'issueShare' => 100, 'trainable' => false, 'x' => 40, 'y' => 62],
            ],
            'spotsTotal' => 12,
            'causes' => [
                ['id' => 'crosshair', 'share' => 38],
                ['id' => 'moving', 'share' => 31],
                ['id' => 'position', 'share' => 19],
                ['id' => 'flashed', 'share' => 12],
            ],
        ]);
    }
}
