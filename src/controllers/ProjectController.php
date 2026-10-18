<?php

declare(strict_types=1);

namespace App\Controller;

final class ProjectController extends AppController
{
    public function index(): void
    {
        $this->render('projects', [
            'pageTitle' => 'Projekty',
            'projects' => [
                ['id' => 1, 'name' => 'Projekt A'],
                ['id' => 2, 'name' => 'Projekt B'],
                ['id' => 12, 'name' => 'Projekt C'],
            ],
        ]);
    }

    public function show(int $id): void
    {
        $this->render('project', [
            'pageTitle' => 'Szczegóły projektu',
            'projectId' => $id,
        ]);
    }
}