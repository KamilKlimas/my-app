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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap">
    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/demos.css">
</head>
<body>
    <aside class="topbar" aria-label="System status">
        <ul class="topbar__list">
            <li>CS2 protocol: tick-64/sub-tick</li>
            <li>Match pipeline: <strong class="topbar__ok">online</strong></li>
            <li>Net: 0% loss</li>
        </ul>
    </aside>

    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="/demos">
                <strong class="brand__name">Duel<span class="brand__accent">Lab</span></strong>
                <small class="brand__tag">Analytics Hub</small>
            </a>

            <?php if ($user !== null): ?>
                <button type="button" class="menu-toggle" aria-label="Open menu" aria-expanded="false">Menu</button>
                <nav class="main-nav" aria-label="Main">
                    <ul class="main-nav__list">
                        <?php foreach ($navItems as $key => [$href, $label]): ?>
                            <li>
                                <a class="main-nav__link" href="<?= $e($href) ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>>
                                    <?= $e($label) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="site-header__tools">
                <div class="lang-switch" role="group" aria-label="Language">
                    <button type="button" class="lang-switch__button" aria-pressed="<?= $lang === 'pl' ? 'true' : 'false' ?>">PL</button>
                    <button type="button" class="lang-switch__button" aria-pressed="<?= $lang === 'en' ? 'true' : 'false' ?>">EN</button>
                </div>
                <button type="button" class="icon-button" aria-label="Toggle dark mode">Theme</button>

                <?php if ($user !== null): ?>
                    <p class="user-chip">
                        <span class="user-chip__name"><?= $e($user['name']) ?></span>
                        <span class="user-chip__role"><?= $e($user['role']) ?></span>
                    </p>
                    <form method="post" action="/logout">
                        <button type="submit" class="link-button">Log out</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>
