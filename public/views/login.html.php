<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <section aria-labelledby="login-heading">
            <h1 id="login-heading"><?= $e($t('login.heading')) ?></h1>
            <p><?= $e($t('login.lead')) ?></p>

            <?php if ($noticeKey !== null): ?>
                <p role="status"><?= $e($t($noticeKey)) ?></p>
            <?php endif; ?>

            <?php if ($errorKey !== null): ?>
                <p role="alert"><?= $e($t($errorKey)) ?></p>
            <?php endif; ?>

            <form method="post" action="/login">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <p>
                    <label for="email"><?= $e($t('login.email')) ?></label>
                    <input type="email" id="email" name="email" value="<?= $e($email) ?>"
                           autocomplete="email" maxlength="254" required>
                </p>
                <p>
                    <label for="password"><?= $e($t('login.password')) ?></label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" required>
                    <button type="button" aria-label="<?= $e($t('login.show_password.label')) ?>"><?= $e($t('login.show_password')) ?></button>
                </p>
                <button type="submit"><?= $e($t('login.submit')) ?></button>
            </form>

            <p><a href="/forgot-password"><?= $e($t('login.forgot')) ?></a></p>
            <p><?= $e($t('login.no_account')) ?> <a href="/register"><?= $e($t('login.register_link')) ?></a></p>
        </section>

        <aside aria-labelledby="demo-accounts-heading">
            <h2 id="demo-accounts-heading"><?= $e($t('login.demo_accounts')) ?></h2>
            <p><code>nightowl@example.com</code> &middot; <code>admin@example.com</code></p>
        </aside>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
