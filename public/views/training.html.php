<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1><?= $e($t('training.heading', ['map' => $mapName])) ?></h1>
            <p><?= $e($t('training.lead')) ?></p>
        </header>

        <section aria-labelledby="scenarios-heading">
            <h2 id="scenarios-heading"><?= $e($t('training.scenarios', ['count' => count($scenarios)])) ?></h2>
            <a href="/demos/<?= (int) $demoId ?>"><?= $e($t('training.add_more')) ?></a>

            <form method="post" action="/training">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <ol>
                    <?php foreach ($scenarios as $index => $scenario): ?>
                        <li>
                            <p><?= $e($t('training.positions', ['you' => $scenario['you'], 'enemy' => $scenario['enemy']])) ?></p>
                            <ul>
                                <?php foreach ($scenario['tags'] as [$tag, $tagParameters]): ?>
                                    <li><?= $e($t('tag.' . $tag, $tagParameters)) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="submit" name="move[<?= (int) $index ?>]" value="down"
                                    aria-label="<?= $e($t('training.move_down', ['number' => $index + 1])) ?>"
                                    <?= $index === count($scenarios) - 1 ? 'disabled' : '' ?>>&darr;</button>
                            <button type="submit" name="move[<?= (int) $index ?>]" value="up"
                                    aria-label="<?= $e($t('training.move_up', ['number' => $index + 1])) ?>"
                                    <?= $index === 0 ? 'disabled' : '' ?>>&uarr;</button>
                            <button type="submit" name="remove" value="<?= (int) $index ?>"
                                    aria-label="<?= $e($t('training.remove', ['number' => $index + 1])) ?>">&times;</button>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <p>
                    <label for="set-name"><?= $e($t('training.set_name')) ?></label>
                    <input type="text" id="set-name" name="name" value="<?= $e($setName) ?>" maxlength="60" required>
                    <button type="submit" name="save" value="1"><?= $e($t('training.save')) ?></button>
                    <output><?= $e($t('training.saved', ['count' => count($scenarios)])) ?></output>
                </p>
            </form>
        </section>

        <section aria-labelledby="config-heading">
            <h2 id="config-heading"><?= $e($t('training.config')) ?></h2>
            <p><code>duellab/last_match.cfg</code></p>
            <pre><code><?= $e($cfgPreview) ?></code></pre>
            <p>
                <a href="/training/last_match.cfg" download><?= $e($t('training.download')) ?></a>
                <button type="button"><?= $e($t('training.write_folder')) ?></button>
            </p>
            <p><small><?= $e($t('training.write_folder_note')) ?> <code>game/csgo/cfg/</code></small></p>
        </section>

        <section aria-labelledby="howto-heading">
            <h2 id="howto-heading"><?= $e($t('training.howto')) ?></h2>
            <ol>
                <li><?= $e($t('training.howto.start', ['map' => $mapName])) ?></li>
                <li><?= $e($t('training.howto.console')) ?> <code>exec duellab/last_match</code></li>
                <li><?= $e($t('training.howto.press')) ?></li>
                <li><?= $e($t('training.howto.play')) ?></li>
            </ol>
        </section>

        <aside aria-labelledby="progress-heading">
            <h2 id="progress-heading"><?= $e($t('training.progress')) ?></h2>
            <p><?= $e($t('training.progress_note', ['map' => $mapName])) ?></p>
        </aside>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
