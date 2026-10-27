<?php

declare(strict_types=1);

namespace App\Controller;

final class ProjectController extends AppController
{
    private const PROJECTS = [
        ['id' => 1, 'name' => 'Projekt A'],
        ['id' => 2, 'name' => 'Projekt B'],
        ['id' => 12, 'name' => 'Projekt C'],
    ];

    public function index(): void
    {
        $this->render('projects', [
            'pageTitle' => 'Projekty',
            'projects' => self::PROJECTS,
        ]);
    }

    public function show(int $id): void
    {
        foreach (self::PROJECTS as $project) {
            if ($project['id'] === $id) {
                $this->render('project', [
                    'pageTitle' => 'Szczegóły projektu',
                    'projectId' => $project['id'],
                    'projectName' => $project['name'],
                ]);

                return;
            }
        }

        http_response_code(404);
        (new DefaultController())->notFound();
    }
}
