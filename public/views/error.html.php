<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <header class="page-header">
            <div>
                <p class="eyebrow"><?= $e($t('error.eyebrow', ['status' => (int) $statusCode])) ?></p>
                <h1 class="page-title"><?= $e($t($pageTitle)) ?></h1>
                <p class="page-lead"><?= $e($t($leadKey)) ?></p>
            </div>
            <a class="page-header__link" href="/demos"><?= $e($t('error.back')) ?></a>
        </header>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
