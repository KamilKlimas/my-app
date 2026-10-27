<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <main>
        <h1><?= htmlspecialchars($projectName) ?></h1>
        <p>Projekt nr <?= (int) $projectId ?></p>
        <a href="/projects">Wróć do listy projektów</a>
    </main>
</body>
</html>
