<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\CurrentUser;
use App\View\View;

final class AdminController extends AppController
{
    public function __construct(
        private readonly View $view,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function index(): void
    {
        $hasError = isset($_GET['error']);

        $this->view->render('admin', [
            'pageTitle' => 'admin.title',
            'activeNav' => 'admin',
            'user' => $this->currentUser->user(),
            'maps' => [
                ['name' => 'de_mirage', 'posX' => '-3230', 'posY' => '1713', 'scale' => '5.00', 'demos' => 142],
                ['name' => 'de_inferno', 'posX' => '-2087', 'posY' => '3870', 'scale' => '4.90', 'demos' => 98],
                ['name' => 'de_nuke', 'posX' => '-3453', 'posY' => '2887', 'scale' => '7.00', 'demos' => 31],
            ],
            'form' => $hasError
                ? ['name' => 'de_ancient', 'posX' => '-2953', 'posY' => '2164', 'scale' => '0']
                : ['name' => '', 'posX' => '', 'posY' => '', 'scale' => ''],
            'errors' => $hasError ? ['scale' => 'scale_out_of_range'] : [],
            'jobs' => [
                ['id' => 312, 'user' => 'nightowl', 'map' => 'de_ancient', 'status' => 'processing', 'progress' => 40,
                    'detail' => ['started', ['at' => new \DateTimeImmutable('2026-10-07 21:52'), 'worker' => 'worker-1']]],
                ['id' => 309, 'user' => 'kuba_p', 'map' => 'de_nuke', 'status' => 'failed', 'progress' => 0,
                    'detail' => ['invalid_header', []]],
                ['id' => 305, 'user' => 'mati', 'map' => 'de_mirage', 'status' => 'done', 'progress' => 100,
                    'detail' => ['parsed', ['seconds' => 48, 'duels' => 11]]],
                ['id' => 301, 'user' => 'nightowl', 'map' => 'de_inferno', 'status' => 'done', 'progress' => 100,
                    'detail' => ['parsed', ['seconds' => 52, 'duels' => 12]]],
            ],
            'scheduledJobs' => [
                ['name' => 'cleanup:raw-demos', 'status' => 'ok', 'schedule' => ['daily', ['time' => '03:00']],
                    'lastRun' => ['cleanup', ['at' => new \DateTimeImmutable('2026-10-07 03:00'), 'count' => 12]]],
                ['name' => 'queue:requeue-stuck', 'status' => 'ok', 'schedule' => ['every_minutes', ['minutes' => 10]],
                    'lastRun' => ['requeue', ['at' => new \DateTimeImmutable('2026-10-07 15:50'), 'count' => 0]]],
                ['name' => 'mail:weekly-summary', 'status' => 'ok', 'schedule' => ['mondays', ['time' => '08:00']],
                    'lastRun' => ['weekly_summary', ['at' => new \DateTimeImmutable('2026-10-05 08:00'), 'count' => 23]]],
            ],
        ]);
    }
}
