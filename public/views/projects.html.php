<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <main>
        <h1>Strona do prezentacji listy wszystkich projektów</h1>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <a href="/projects/<?= htmlspecialchars($project['id']) ?>">
                        <?= htmlspecialchars($project['name']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- TODO: Lista projektów wyświetlona w pętli -->
    </main>
</body>
</html>