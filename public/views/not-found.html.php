<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <header class="page-header">
            <div>
                <p class="eyebrow">Error 404</p>
                <h1 class="page-title"><?= $e($pageTitle) ?></h1>
                <p class="page-lead">This page doesn't exist or the record was removed.</p>
            </div>
            <a class="page-header__link" href="/demos">Back to my demos</a>
        </header>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
