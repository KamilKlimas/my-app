<?php
$lang = $lang ?? 'en';
$user = $user ?? null;
$activeNav = $activeNav ?? '';

$navItems = [
    'demos' => ['/demos', 'Demos'],
    'hotspots' => ['/hotspots', 'Hotspots'],
    'training' => ['/training', 'Training'],
];

if ($user !== null && $user['role'] === 'ADMIN') {
    $navItems['admin'] = ['/admin', 'Admin'];
}
?>
<!doctype html>
<html lang="<?= $e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($pageTitle) ?> | DuelLab</title>
</head>
<body>
    <aside aria-label="System status">
        <ul>
            <li>CS2 protocol: tick-64/sub-tick</li>
            <li>Match pipeline: <strong>online</strong></li>
            <li>Net: 0% loss</li>
        </ul>
    </aside>

    <header>
        <a href="/demos">
            <strong>DuelLab</strong>
            <small>Analytics Hub</small>
        </a>

        <?php if ($user !== null): ?>
            <button type="button" aria-label="Open menu" aria-expanded="false">Menu</button>
            <nav aria-label="Main">
                <ul>
                    <?php foreach ($navItems as $key => [$href, $label]): ?>
                        <li>
                            <a href="<?= $e($href) ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>>
                                <?= $e($label) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <div role="group" aria-label="Language">
            <button type="button" aria-pressed="<?= $lang === 'pl' ? 'true' : 'false' ?>">PL</button>
            <button type="button" aria-pressed="<?= $lang === 'en' ? 'true' : 'false' ?>">EN</button>
        </div>
        <button type="button" aria-label="Toggle dark mode">Theme</button>

        <?php if ($user !== null): ?>
            <p>
                <span><?= $e($user['name']) ?></span>
                <span><?= $e($user['role']) ?></span>
            </p>
            <form method="post" action="/logout">
                <button type="submit">Log out</button>
            </form>
        <?php endif; ?>
    </header>
