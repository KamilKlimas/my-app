<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <section aria-labelledby="login-heading">
            <h1 id="login-heading">Zaloguj się</h1>
            <p>Sprawdź, gdzie przegrywasz pojedynki, i trenuj dokładnie te sytuacje.</p>

            <?php if ($error !== null): ?>
                <p role="alert"><?= $e($error) ?></p>
            <?php endif; ?>

            <form method="post" action="/login">
                <p>
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= $e($email) ?>"
                           autocomplete="email" required>
                </p>
                <p>
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" required>
                    <button type="button" aria-label="Pokaż hasło">Pokaż</button>
                </p>
                <button type="submit">Zaloguj się</button>
            </form>

            <p><a href="/forgot-password">Nie pamiętasz hasła?</a></p>
            <p>Nie masz konta? <a href="/register">Załóż konto</a></p>
        </section>

        <aside aria-labelledby="demo-accounts-heading">
            <h2 id="demo-accounts-heading">Konta demonstracyjne</h2>
            <p><code>user@duellab.test</code> &middot; <code>admin@duellab.test</code></p>
        </aside>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
