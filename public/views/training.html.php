<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1>Training set &mdash; <?= $e($mapName) ?>, last match</h1>
            <p>Each scenario puts you back where you stood, aiming where you aimed, with a bot where your killer was. Fix the angle, then play the next one.</p>
        </header>

        <section aria-labelledby="scenarios-heading">
            <h2 id="scenarios-heading">Scenarios (<?= count($scenarios) ?>)</h2>
            <a href="/demos/<?= (int) $demoId ?>">Add more from the demo</a>

            <form method="post" action="/training">
                <ol>
                    <?php foreach ($scenarios as $index => $scenario): ?>
                        <li>
                            <p>You: <?= $e($scenario['you']) ?> &mdash; bot at <?= $e($scenario['enemy']) ?></p>
                            <ul>
                                <?php foreach ($scenario['tags'] as $tag): ?>
                                    <li><?= $e($tag) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="submit" name="move[<?= (int) $index ?>]" value="down"
                                    aria-label="Move scenario <?= $index + 1 ?> down"
                                    <?= $index === count($scenarios) - 1 ? 'disabled' : '' ?>>&darr;</button>
                            <button type="submit" name="move[<?= (int) $index ?>]" value="up"
                                    aria-label="Move scenario <?= $index + 1 ?> up"
                                    <?= $index === 0 ? 'disabled' : '' ?>>&uarr;</button>
                            <button type="submit" name="remove" value="<?= (int) $index ?>"
                                    aria-label="Remove scenario <?= $index + 1 ?>">&times;</button>
                        </li>
                    <?php endforeach; ?>
                </ol>

                <p>
                    <label for="set-name">Set name</label>
                    <input type="text" id="set-name" name="name" value="<?= $e($setName) ?>" maxlength="60" required>
                    <button type="submit" name="save" value="1">Save set</button>
                    <output>Saved &middot; <?= count($scenarios) ?> scenarios</output>
                </p>
            </form>
        </section>

        <section aria-labelledby="config-heading">
            <h2 id="config-heading">Practice config</h2>
            <p><code>duellab/last_match.cfg</code></p>
            <pre><code><?= $e($cfgPreview) ?></code></pre>
            <p>
                <a href="/training/last_match.cfg" download>Download .cfg</a>
                <button type="button">Write to CS2 cfg folder</button>
            </p>
            <p><small>Writing straight into the game folder works in Chrome and Edge after you pick the folder once. Everywhere else, download the file and drop it into <code>game/csgo/cfg/</code>.</small></p>
        </section>

        <section aria-labelledby="howto-heading">
            <h2 id="howto-heading">How to play it</h2>
            <ol>
                <li>Start an offline game on <strong><?= $e($mapName) ?></strong> (practice with bots).</li>
                <li>Open the console and run <code>exec duellab/last_match</code>.</li>
                <li>Press <kbd>N</kbd> &mdash; you spawn in your spot, the bot stands where your killer was.</li>
                <li>Play your next real matches; DuelLab checks whether you still lose these duels.</li>
            </ol>
        </section>

        <aside aria-labelledby="progress-heading">
            <h2 id="progress-heading">Progress</h2>
            <p>Tracking starts after 3 new demos on <?= $e($mapName) ?>. We compare how often you lose these exact spots before and after practice.</p>
        </aside>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
