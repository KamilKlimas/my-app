<?php
$user = $user ?? null;
$pageTitleParameters = $pageTitleParameters ?? [];
$activeNav = $activeNav ?? '';

$navItems = [
    'demos' => ['/demos', 'nav.demos'],
    'hotspots' => ['/hotspots', 'nav.hotspots'],
    'training' => ['/training', 'nav.training'],
];

if ($user !== null && $user->role === \App\Model\Role::Admin) {
    $navItems['admin'] = ['/admin', 'nav.admin'];
}
?>
<!doctype html>
<html lang="<?= $e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($t($pageTitle, $pageTitleParameters)) ?> | DuelLab</title>
    <!-- Runs before any stylesheet so the first paint already uses the right theme (no FOUC).
         The toggle itself lives in /js/theme.js; both use the localStorage key "theme". -->
    <script>
        (function () {
            let savedTheme = null;

            try {
                savedTheme = localStorage.getItem('theme');
            } catch (error) {
                savedTheme = null;
            }

            const hasSavedTheme = savedTheme === 'light' || savedTheme === 'dark';
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            document.documentElement.dataset.theme = hasSavedTheme
                ? savedTheme
                : (systemPrefersDark ? 'dark' : 'light');
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&family=JetBrains+Mono:wght@400;600&display=swap">
    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/demos.css">
    <link rel="stylesheet" href="/css/states.css">
    <link rel="stylesheet" href="/css/responsive.css">
</head>
<body>
    <aside class="topbar" aria-label="<?= $e($t('layout.status.label')) ?>">
        <ul class="topbar__list">
            <li><?= $e($t('layout.status.protocol')) ?></li>
            <li><?= $e($t('layout.status.pipeline')) ?> <strong class="topbar__ok"><?= $e($t('layout.status.online')) ?></strong></li>
            <li><?= $e($t('layout.status.net')) ?></li>
        </ul>
    </aside>

    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="/">
                <strong class="brand__name">Duel<span class="brand__accent">Lab</span></strong>
                <small class="brand__tag"><?= $e($t('layout.brand.tag')) ?></small>
            </a>

            <?php if ($user !== null): ?>
                <button type="button" class="menu-toggle" aria-label="<?= $e($t('layout.menu.open')) ?>" aria-expanded="false"><?= $e($t('layout.menu')) ?></button>
                <nav class="main-nav" aria-label="<?= $e($t('layout.nav.label')) ?>">
                    <ul class="main-nav__list">
                        <?php foreach ($navItems as $key => [$href, $label]): ?>
                            <li>
                                <a class="main-nav__link" href="<?= $e($href) ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>>
                                    <?= $e($t($label)) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="site-header__tools">
                <div class="lang-switch" role="group" aria-label="<?= $e($t('layout.language')) ?>">
                    <button type="button" class="lang-switch__button" aria-pressed="<?= $lang === 'pl' ? 'true' : 'false' ?>">PL</button>
                    <button type="button" class="lang-switch__button" aria-pressed="<?= $lang === 'en' ? 'true' : 'false' ?>">EN</button>
                </div>
                <button type="button" class="icon-button theme-toggle" aria-pressed="false"
                        aria-label="<?= $e($t('layout.theme.to_dark')) ?>" title="<?= $e($t('layout.theme.title')) ?>"
                        data-label-dark="<?= $e($t('layout.theme.dark')) ?>" data-label-light="<?= $e($t('layout.theme.light')) ?>"
                        data-aria-dark="<?= $e($t('layout.theme.to_dark')) ?>" data-aria-light="<?= $e($t('layout.theme.to_light')) ?>">
                    <span class="theme-toggle__label"><?= $e($t('layout.theme.dark')) ?></span>
                </button>

                <?php if ($user !== null): ?>
                    <p class="user-chip">
                        <span class="user-chip__name"><?= $e($user->name) ?></span>
                        <span class="user-chip__role"><?= $e($t('role.' . strtolower($user->role->value))) ?></span>
                    </p>
                    <form method="post" action="/logout">
                        <?php require __DIR__ . '/csrf-field.html.php'; ?>
                        <button type="submit" class="link-button"><?= $e($t('layout.logout')) ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>
