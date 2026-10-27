<?php
require __DIR__ . '/partials/header.html.php';

$playedAt = new DateTimeImmutable($demo['playedAt']);
$won = $demo['score'][0] > $demo['score'][1];
?>

    <main>
        <nav aria-label="Breadcrumb">
            <ol>
                <li><a href="/demos">My demos</a></li>
                <li><?= $e($demo['map']) ?></li>
                <li><time datetime="<?= $e($playedAt->format('Y-m-d')) ?>"><?= $e($playedAt->format('M j')) ?></time></li>
            </ol>
        </nav>

        <header>
            <h1>
                <?= $e($demo['map']) ?> &middot; <?= (int) $demo['score'][0] ?>&ndash;<?= (int) $demo['score'][1] ?>
                <small><?= $won ? 'Win' : 'Loss' ?></small>
            </h1>
            <p>
                <time datetime="<?= $e($playedAt->format('Y-m-d\TH:i')) ?>"><?= $e($playedAt->format('M j, H:i')) ?></time>
                &middot; <?= $e($match['mode']) ?>
                &middot; <?= (int) $match['rounds'] ?> rounds
                &middot; parsed in <?= (int) $match['parsedIn'] ?> s
            </p>
            <dl>
                <div>
                    <dt>Lost duels</dt>
                    <dd><?= (int) $demo['lostDuels'] ?></dd>
                </div>
                <div>
                    <dt>Trainable</dt>
                    <dd><?= (int) $trainableCount ?></dd>
                </div>
            </dl>
            <form method="post" action="/demos/<?= (int) $demo['id'] ?>/training-set">
                <button type="submit">Build training set (<?= (int) $selectedCount ?>)</button>
            </form>
        </header>

        <section aria-labelledby="radar-heading">
            <h2 id="radar-heading">Round <?= (int) $selectedDuel['round'] ?> &middot; <?= $e($selectedDuel['time']) ?> left</h2>
            <p><?= $e($selectedDuel['weapons']) ?></p>

            <figure>
                <div role="img" aria-label="Radar of <?= $e($demo['map']) ?> with your position, aim line and the enemy position">
                    Radar placeholder: <?= $e($demo['map']) ?>
                </div>
                <figcaption>
                    <ul>
                        <li>You &mdash; line shows where you were aiming</li>
                        <li>Enemy</li>
                        <li>Line of sight</li>
                        <li>Other deaths</li>
                    </ul>
                    <small>Map placeholder &mdash; the real radar image is uploaded by an admin per map and calibrated with the map's pos_x / pos_y / scale.</small>
                </figcaption>
            </figure>

            <h3>Duel metrics</h3>
            <dl>
                <?php foreach ($metrics as $metric): ?>
                    <div>
                        <dt><?= $e($metric['label']) ?></dt>
                        <dd><?= $e($metric['value']) ?> <small><?= $e($metric['unit']) ?></small></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <p><strong>Verdict:</strong> <?= $e($verdict) ?></p>
        </section>

        <section aria-labelledby="duels-heading">
            <h2 id="duels-heading">Lost duels</h2>
            <p><?= count($duels) ?> of <?= (int) $demo['lostDuels'] ?> shown &middot; select one to see it on the radar</p>

            <form method="post" action="/demos/<?= (int) $demo['id'] ?>/training-set">
                <ol>
                    <?php foreach ($duels as $duel): ?>
                        <li<?= $duel['round'] === $selectedDuel['round'] ? ' aria-current="true"' : '' ?>>
                            <article>
                                <header>
                                    <h3>
                                        <a href="/demos/<?= (int) $demo['id'] ?>?round=<?= (int) $duel['round'] ?>">
                                            R<?= (int) $duel['round'] ?> &middot; <?= $e($duel['time']) ?>
                                        </a>
                                    </h3>
                                    <p><?= $e($duel['weapons']) ?></p>
                                </header>
                                <p>You: <?= $e($duel['you']) ?> &rarr; Enemy: <?= $e($duel['enemy']) ?></p>
                                <ul>
                                    <?php foreach ($duel['tags'] as $tag): ?>
                                        <li><?= $e($tag) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <p>
                                    <input type="checkbox" id="duel-<?= (int) $duel['round'] ?>"
                                           name="rounds[]" value="<?= (int) $duel['round'] ?>"
                                           <?= $duel['trainable'] ? 'checked' : 'disabled' ?>>
                                    <label for="duel-<?= (int) $duel['round'] ?>">
                                        <?= $duel['trainable'] ? 'Add to training set' : 'Not trainable' ?>
                                    </label>
                                    <?php if ($duel['note'] !== null): ?>
                                        <small><?= $e($duel['note']) ?></small>
                                    <?php endif; ?>
                                </p>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </form>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
