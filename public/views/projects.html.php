<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <nav aria-label="Nawigacja główna">
            <ul>
                <li><a href="/">Strona główna</a></li>
                <li><a href="/projects">Projekty</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <section aria-labelledby="projects-heading">
            <h2 id="projects-heading">Lista projektów</h2>
            <p>Liczba projektów: <?= count($projects) ?></p>
            <ul>
                <?php foreach ($projects as $project): ?>
                    <li>
                        <a href="/projects/<?= (int) $project['id'] ?>">
                            <?= htmlspecialchars($project['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
    <footer>
        <p>Wstęp do aplikacji internetowych</p>
    </footer>
</body>
</html>
