<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1><?= $e($t('hotspots.title')) ?></h1>
            <p><?= $e($t('hotspots.lead')) ?></p>
        </header>

        <section aria-labelledby="filters-heading">
            <h2 id="filters-heading"><?= $e($t('hotspots.filters')) ?></h2>
            <form method="get" action="/hotspots">
                <?php foreach ($filters as $name => $filter): ?>
                    <p>
                        <label for="filter-<?= $e($name) ?>"><?= $e($t($filter['label'])) ?></label>
                        <select id="filter-<?= $e($name) ?>" name="<?= $e($name) ?>">
                            <?php foreach ($filter['options'] as $value => $labelKey): ?>
                                <option value="<?= $e($value) ?>"<?= (string) $value === $filter['selected'] ? ' selected' : '' ?>>
                                    <?= $e($labelKey === null ? $value : $t($labelKey)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                <?php endforeach; ?>
                <button type="submit"><?= $e($t('hotspots.apply')) ?></button>
            </form>
            <p><code>/hotspots?<?= $e($query) ?></code></p>
        </section>

        <section aria-labelledby="radar-heading">
            <h2 id="radar-heading">
                <?= $e($t('hotspots.radar.heading', ['map' => $mapName, 'count' => (int) $totalDeaths, 'period' => $t($periodKey)])) ?>
            </h2>
            <p><?= $e($t('hotspots.radar.legend')) ?></p>

            <figure>
                <div role="img" aria-label="<?= $e($t('hotspots.radar.label', ['map' => $mapName])) ?>">
                    <?= $e($t('demo.radar.placeholder', ['map' => $mapName])) ?>
                </div>
                <ol>
                    <?php foreach ($spots as $spot): ?>
                        <li data-x="<?= $e($spot['x']) ?>" data-y="<?= $e($spot['y']) ?>">
                            <?= $e($spot['from']) ?> &rarr; <?= $e($spot['to']) ?>: <?= (int) $spot['deaths'] ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <figcaption>
                    <?= $e($t('hotspots.source')) ?> <code>v_death_hotspots</code>
                </figcaption>
            </figure>
        </section>

        <section aria-labelledby="top-spots-heading">
            <h2 id="top-spots-heading"><?= $e($t('hotspots.top')) ?></h2>
            <p><?= $e($t('hotspots.top_note')) ?></p>
            <table>
                <thead>
                    <tr>
                        <th scope="col"><?= $e($t('hotspots.column.spot')) ?></th>
                        <th scope="col"><?= $e($t('hotspots.column.deaths')) ?></th>
                        <th scope="col"><?= $e($t('hotspots.column.issue')) ?></th>
                        <th scope="col"><?= $e($t('common.action')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($spots as $spot): ?>
                        <tr>
                            <th scope="row"><?= $e($spot['from']) ?> &rarr; <?= $e($spot['to']) ?></th>
                            <td><?= (int) $spot['deaths'] ?></td>
                            <td><?= $e($t('issue.' . $spot['issue'])) ?> (<?= (int) $spot['issueShare'] ?>%)</td>
                            <td>
                                <?php if ($spot['trainable']): ?>
                                    <a href="/training"><?= $e($t('hotspots.train')) ?></a>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><small><?= $e($t('hotspots.showing', ['shown' => count($spots), 'total' => (int) $spotsTotal])) ?></small></p>
        </section>

        <section aria-labelledby="causes-heading">
            <h2 id="causes-heading"><?= $e($t('hotspots.causes')) ?></h2>
            <ul>
                <?php foreach ($causes as $cause): ?>
                    <li>
                        <label for="cause-<?= $e($cause['id']) ?>"><?= $e($t('issue.' . $cause['id'])) ?></label>
                        <meter id="cause-<?= $e($cause['id']) ?>" min="0" max="100" value="<?= (int) $cause['share'] ?>">
                            <?= (int) $cause['share'] ?>%
                        </meter>
                        <span><?= (int) $cause['share'] ?>%</span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><small><?= $e($t('hotspots.source')) ?> <code>v_death_hotspots</code></small></p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
