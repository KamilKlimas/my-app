<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1>Hotspots</h1>
            <p>Where you lose duels most often, across all your demos.</p>
        </header>

        <section aria-labelledby="filters-heading">
            <h2 id="filters-heading">Filters</h2>
            <form method="get" action="/hotspots">
                <?php foreach ($filters as $name => $filter): ?>
                    <p>
                        <label for="filter-<?= $e($name) ?>"><?= $e($filter['label']) ?></label>
                        <select id="filter-<?= $e($name) ?>" name="<?= $e($name) ?>">
                            <?php foreach ($filter['options'] as $value => $label): ?>
                                <option value="<?= $e($value) ?>"<?= (string) $value === $filter['selected'] ? ' selected' : '' ?>>
                                    <?= $e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                <?php endforeach; ?>
                <button type="submit">Apply</button>
            </form>
            <p><code>/hotspots?<?= $e($query) ?></code></p>
        </section>

        <section aria-labelledby="radar-heading">
            <h2 id="radar-heading">
                <?= $e($mapName) ?> &mdash; <?= (int) $totalDeaths ?> lost duels in <?= $e($periodLabel) ?>
            </h2>
            <p>Circle size = deaths in that spot</p>

            <figure>
                <div role="img" aria-label="Radar of <?= $e($mapName) ?> with circles marking the spots where you die most often">
                    Radar placeholder: <?= $e($mapName) ?>
                </div>
                <ol>
                    <?php foreach ($spots as $spot): ?>
                        <li data-x="<?= $e($spot['x']) ?>" data-y="<?= $e($spot['y']) ?>">
                            <?= $e($spot['from']) ?> &rarr; <?= $e($spot['to']) ?>: <?= (int) $spot['deaths'] ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <figcaption>
                    Counts come from the database view <code>v_death_hotspots</code>, filtered by the query parameters above.
                </figcaption>
            </figure>
        </section>

        <section aria-labelledby="top-spots-heading">
            <h2 id="top-spots-heading">Top spots</h2>
            <p>Ranked by loss</p>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Spot</th>
                        <th scope="col">Deaths</th>
                        <th scope="col">Main issue</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($spots as $spot): ?>
                        <tr>
                            <th scope="row"><?= $e($spot['from']) ?> &rarr; <?= $e($spot['to']) ?></th>
                            <td><?= (int) $spot['deaths'] ?></td>
                            <td><?= $e($spot['issue']) ?> (<?= (int) $spot['issueShare'] ?>%)</td>
                            <td>
                                <?php if ($spot['trainable']): ?>
                                    <a href="/training">Train</a>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><small>Showing <?= count($spots) ?> of <?= (int) $spotsTotal ?> locations</small></p>
        </section>

        <section aria-labelledby="causes-heading">
            <h2 id="causes-heading">Why you lose them</h2>
            <ul>
                <?php foreach ($causes as $cause): ?>
                    <li>
                        <label for="cause-<?= $e($cause['id']) ?>"><?= $e($cause['label']) ?></label>
                        <meter id="cause-<?= $e($cause['id']) ?>" min="0" max="100" value="<?= (int) $cause['share'] ?>">
                            <?= (int) $cause['share'] ?>%
                        </meter>
                        <span><?= (int) $cause['share'] ?>%</span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><small>Counts come from the database view <code>v_death_hotspots</code>, filtered by the query parameters above.</small></p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
