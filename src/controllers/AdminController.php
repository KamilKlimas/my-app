<?php

declare(strict_types=1);

namespace App\Controller;

final class AdminController extends AppController
{
    public function index(): void
    {
        $hasError = isset($_GET['error']);

        $this->render('admin', [
            'pageTitle' => 'Admin',
            'activeNav' => 'admin',
            'user' => ['name' => 'admin', 'role' => 'ADMIN'],
            'maps' => [
                ['name' => 'de_mirage', 'posX' => '-3230', 'posY' => '1713', 'scale' => '5.00', 'demos' => 142],
                ['name' => 'de_inferno', 'posX' => '-2087', 'posY' => '3870', 'scale' => '4.90', 'demos' => 98],
                ['name' => 'de_nuke', 'posX' => '-3453', 'posY' => '2887', 'scale' => '7.00', 'demos' => 31],
            ],
            'form' => $hasError
                ? ['name' => 'de_ancient', 'posX' => '-2953', 'posY' => '2164', 'scale' => '0']
                : ['name' => '', 'posX' => '', 'posY' => '', 'scale' => ''],
            'errors' => $hasError ? ['scale' => 'Scale must be between 0.1 and 20.'] : [],
            'jobs' => [
                ['id' => 312, 'user' => 'nightowl', 'map' => 'de_ancient', 'status' => 'processing', 'statusLabel' => 'Processing 40%', 'progress' => 40, 'detail' => 'Started 21:52 · worker-1'],
                ['id' => 309, 'user' => 'kuba_p', 'map' => 'de_nuke', 'status' => 'failed', 'statusLabel' => 'Failed', 'progress' => 0, 'detail' => 'Invalid file header — not a CS2 demo'],
                ['id' => 305, 'user' => 'mati', 'map' => 'de_mirage', 'status' => 'done', 'statusLabel' => 'Done', 'progress' => 100, 'detail' => 'Parsed in 48 s · 11 lost duels'],
                ['id' => 301, 'user' => 'nightowl', 'map' => 'de_inferno', 'status' => 'done', 'statusLabel' => 'Done', 'progress' => 100, 'detail' => 'Parsed in 52 s · 12 lost duels'],
            ],
            'scheduledJobs' => [
                ['name' => 'cleanup:raw-demos', 'status' => 'OK', 'schedule' => 'Daily 03:00', 'lastRun' => 'last run Oct 7 · removed 12 raw .dem files'],
                ['name' => 'queue:requeue-stuck', 'status' => 'OK', 'schedule' => 'Every 10 min', 'lastRun' => 'last run 15:50 · 0 jobs requeued'],
                ['name' => 'mail:weekly-summary', 'status' => 'OK', 'schedule' => 'Mondays 08:00', 'lastRun' => 'last run Oct 5 · 23 e-mails sent'],
            ],
        ]);
    }
}
