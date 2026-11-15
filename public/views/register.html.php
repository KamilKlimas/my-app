<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <section aria-labelledby="register-heading">
            <h1 id="register-heading">Załóż konto</h1>
            <p>Wgraj demo, zobacz przegrane pojedynki i ćwicz dokładnie te sytuacje.</p>

            <form method="post" action="/register">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <p>
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?= $e($email) ?>"
                           autocomplete="email" maxlength="254"
                           <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?> required>
                    <?php if (isset($errors['email'])): ?>
                        <small id="email-error" role="alert"><?= $e($errors['email']) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="name">Nazwa gracza</label>
                    <input type="text" id="name" name="name" value="<?= $e($name) ?>"
                           autocomplete="username" minlength="3" maxlength="32"
                           <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?> required>
                    <?php if (isset($errors['name'])): ?>
                        <small id="name-error" role="alert"><?= $e($errors['name']) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="password">Hasło (co najmniej 8 znaków)</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" minlength="8"
                           <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?> required>
                    <?php if (isset($errors['password'])): ?>
                        <small id="password-error" role="alert"><?= $e($errors['password']) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="password-confirmation">Powtórz hasło</label>
                    <input type="password" id="password-confirmation" name="password_confirmation"
                           autocomplete="new-password"
                           <?= isset($errors['password_confirmation']) ? 'aria-invalid="true" aria-describedby="password-confirmation-error"' : '' ?> required>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <small id="password-confirmation-error" role="alert"><?= $e($errors['password_confirmation']) ?></small>
                    <?php endif; ?>
                </p>
                <button type="submit">Załóż konto</button>
            </form>

            <p>Masz już konto? <a href="/login">Zaloguj się</a></p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
