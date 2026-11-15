<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <section aria-labelledby="register-heading">
            <h1 id="register-heading"><?= $e($t('register.heading')) ?></h1>
            <p><?= $e($t('register.lead')) ?></p>

            <form method="post" action="/register">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <p>
                    <label for="email"><?= $e($t('register.email')) ?></label>
                    <input type="email" id="email" name="email" value="<?= $e($email) ?>"
                           autocomplete="email" maxlength="254"
                           <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?> required>
                    <?php if (isset($errors['email'])): ?>
                        <small id="email-error" role="alert"><?= $e($t('register.error.' . $errors['email'])) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="name"><?= $e($t('register.name')) ?></label>
                    <input type="text" id="name" name="name" value="<?= $e($name) ?>"
                           autocomplete="username" minlength="3" maxlength="32"
                           <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="name-error"' : '' ?> required>
                    <?php if (isset($errors['name'])): ?>
                        <small id="name-error" role="alert"><?= $e($t('register.error.' . $errors['name'])) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="password"><?= $e($t('register.password')) ?></label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" minlength="8"
                           <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?> required>
                    <?php if (isset($errors['password'])): ?>
                        <small id="password-error" role="alert"><?= $e($t('register.error.' . $errors['password'])) ?></small>
                    <?php endif; ?>
                </p>
                <p>
                    <label for="password-confirmation"><?= $e($t('register.password_confirmation')) ?></label>
                    <input type="password" id="password-confirmation" name="password_confirmation"
                           autocomplete="new-password"
                           <?= isset($errors['password_confirmation']) ? 'aria-invalid="true" aria-describedby="password-confirmation-error"' : '' ?> required>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <small id="password-confirmation-error" role="alert"><?= $e($t('register.error.' . $errors['password_confirmation'])) ?></small>
                    <?php endif; ?>
                </p>
                <button type="submit"><?= $e($t('register.submit')) ?></button>
            </form>

            <p><?= $e($t('register.has_account')) ?> <a href="/login"><?= $e($t('register.login_link')) ?></a></p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
