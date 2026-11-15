<?php
require __DIR__ . '/partials/header.html.php';

$playedAt = new DateTimeImmutable($demo['playedAt']);
$won = $demo['score'][0] > $demo['score'][1];
?>

    <main>
        <nav aria-label="<?= $e($t('demo.breadcrumb')) ?>">
            <ol>
                <li><a href="/demos"><?= $e($t('demos.heading')) ?></a></li>
                <li><?= $e($demo['map']) ?></li>
                <li><time datetime="<?= $e($playedAt->format('Y-m-d')) ?>"><?= $e($t('common.date', ['at' => $playedAt])) ?></time></li>
            </ol>
        </nav>

        <header>
            <h1>
                <?= $e($demo['map']) ?> &middot; <?= (int) $demo['score'][0] ?>&ndash;<?= (int) $demo['score'][1] ?>
                <small><?= $e($t($won ? 'common.win' : 'common.loss')) ?></small>
            </h1>
            <p>
                <time datetime="<?= $e($playedAt->format('Y-m-d\TH:i')) ?>"><?= $e($t('common.date_time', ['at' => $playedAt])) ?></time>
                &middot; <?= $e($match['mode']) ?>
                &middot; <?= $e($t('demo.rounds', ['count' => (int) $match['rounds']])) ?>
                &middot; <?= $e($t('demo.parsed_in', ['seconds' => (int) $match['parsedIn']])) ?>
            </p>
            <dl>
                <div>
                    <dt><?= $e($t('demos.column.lost')) ?></dt>
                    <dd><?= (int) $demo['lostDuels'] ?></dd>
                </div>
                <div>
                    <dt><?= $e($t('demo.trainable')) ?></dt>
                    <dd><?= (int) $trainableCount ?></dd>
                </div>
            </dl>
            <form method="post" action="/demos/<?= (int) $demo['id'] ?>/training-set">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <button type="submit"><?= $e($t('demo.build_set', ['count' => (int) $selectedCount])) ?></button>
            </form>
        </header>

        <section aria-labelledby="radar-heading">
            <h2 id="radar-heading"><?= $e($t('demo.round_heading', ['round' => (int) $selectedDuel['round'], 'time' => $selectedDuel['time']])) ?></h2>
            <p><?= $e($selectedDuel['weapons']) ?></p>

            <figure>
                <div role="img" aria-label="<?= $e($t('demo.radar.label', ['map' => $demo['map']])) ?>">
                    <?= $e($t('demo.radar.placeholder', ['map' => $demo['map']])) ?>
                </div>
                <figcaption>
                    <ul>
                        <li><?= $e($t('demo.legend.you')) ?></li>
                        <li><?= $e($t('demo.legend.enemy')) ?></li>
                        <li><?= $e($t('demo.legend.sight')) ?></li>
                        <li><?= $e($t('demo.legend.other_deaths')) ?></li>
                    </ul>
                    <small><?= $e($t('demo.radar.note')) ?></small>
                </figcaption>
            </figure>

            <h3><?= $e($t('demo.metrics')) ?></h3>
            <dl>
                <?php foreach ($metrics as $metric): ?>
                    <div>
                        <dt><?= $e($t('demo.metric.' . $metric['code'])) ?></dt>
                        <dd><?= (int) $metric['value'] ?> <small><?= $e($metric['unit']) ?></small></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <p><strong><?= $e($t('demo.verdict')) ?></strong> <?= $e($t('demo.verdict.' . $verdict)) ?></p>
        </section>

        <section aria-labelledby="duels-heading">
            <h2 id="duels-heading"><?= $e($t('demos.column.lost')) ?></h2>
            <p><?= $e($t('demo.duels_shown', ['shown' => count($duels), 'total' => (int) $demo['lostDuels']])) ?></p>

            <form method="post" action="/demos/<?= (int) $demo['id'] ?>/training-set">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
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
                                <p><?= $e($duel['enemy'] === null
                                    ? $t('demo.positions_behind', ['you' => $duel['you']])
                                    : $t('demo.positions', ['you' => $duel['you'], 'enemy' => $duel['enemy']])) ?></p>
                                <ul>
                                    <?php foreach ($duel['tags'] as [$tag, $tagParameters]): ?>
                                        <li><?= $e($t('tag.' . $tag, $tagParameters)) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <p>
                                    <input type="checkbox" id="duel-<?= (int) $duel['round'] ?>"
                                           name="rounds[]" value="<?= (int) $duel['round'] ?>"
                                           <?= $duel['trainable'] ? 'checked' : 'disabled' ?>>
                                    <label for="duel-<?= (int) $duel['round'] ?>">
                                        <?= $e($t($duel['trainable'] ? 'demo.add_to_set' : 'demo.not_trainable')) ?>
                                    </label>
                                    <?php if ($duel['note'] !== null): ?>
                                        <small><?= $e($t('tag.' . $duel['note'][0], $duel['note'][1])) ?></small>
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
