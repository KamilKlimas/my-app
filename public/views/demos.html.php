<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <header class="page-header">
            <div>
                <h1 class="page-title">My demos <span class="live-badge">Live parser</span></h1>
                <p class="page-lead">Upload a CS2 demo &mdash; DuelLab finds the duels you lost, tells you why, and turns them into practice scenarios.</p>
            </div>
            <a class="button button--outline" href="/training">Open training set</a>
        </header>

        <section class="overview" aria-labelledby="stats-heading">
            <h2 id="stats-heading" class="visually-hidden">Summary</h2>
            <dl class="stats__list">
                <div class="stat">
                    <dt class="stat__label">Demos analysed</dt>
                    <dd class="stat__value"><?= (int) $stats['demosAnalysed'] ?></dd>
                    <dd class="stat__note">Last 30 days active</dd>
                </div>
                <div class="stat stat--danger">
                    <dt class="stat__label">Lost duels found</dt>
                    <dd class="stat__value"><?= (int) $stats['lostDuels'] ?></dd>
                    <dd class="stat__note"><?= $e($stats['duelsPerMatch']) ?> duels/match avg</dd>
                </div>
                <div class="stat stat--accent">
                    <dt class="stat__label">Trainable duels</dt>
                    <dd class="stat__value"><?= (int) $stats['trainableDuels'] ?></dd>
                    <dd class="stat__note"><?= (int) $stats['convertedPercent'] ?>% conversion rate</dd>
                </div>
                <div class="stat stat--issue">
                    <dt class="stat__label">Top issue</dt>
                    <dd class="stat__value stat__value--text"><?= $e($stats['topIssue']['label']) ?></dd>
                    <dd class="stat__note"><strong class="stat__share"><?= (int) $stats['topIssue']['share'] ?>%</strong> of all lost duels</dd>
                </div>
            </dl>

            <section class="quick-upload" aria-labelledby="quick-upload-heading">
                <div class="quick-upload__header">
                    <h3 id="quick-upload-heading" class="quick-upload__title">Quick upload .dem</h3>
                    <button class="button button--ghost button--small" type="button">Sync folder</button>
                </div>
                <?php $dropzoneClass = 'dropzone--compact'; ?>
                <?php require __DIR__ . '/partials/upload-form.html.php'; ?>

                <?php if ($upload !== null): ?>
                    <p class="upload-progress">
                        <label class="upload-progress__label" for="upload-progress">
                            <span><?= $e($upload['file']) ?></span>
                            <span class="upload-progress__value"><?= (int) $upload['progress'] ?>%</span>
                        </label>
                        <progress class="progress" id="upload-progress" value="<?= (int) $upload['progress'] ?>" max="100">
                            <?= (int) $upload['progress'] ?>%
                        </progress>
                    </p>
                <?php endif; ?>
            </section>
        </section>

        <section class="recent" aria-labelledby="recent-heading">
            <div class="recent__header">
                <h2 id="recent-heading" class="recent__title">
                    Recent demos <span class="count-badge"><?= count($demos) ?> <?= count($demos) === 1 ? 'match' : 'matches' ?></span>
                </h2>
                <p class="recent__note">Status refreshes automatically</p>

                <form class="filters" method="get" action="/demos">
                    <label class="visually-hidden" for="filter-map">Map</label>
                    <select class="filters__select" id="filter-map" name="map">
                        <?php foreach ($mapOptions as $value => $label): ?>
                            <option value="<?= $e($value) ?>"<?= $value === $filters['map'] ? ' selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="visually-hidden" for="filter-outcome">Outcome</label>
                    <select class="filters__select" id="filter-outcome" name="outcome">
                        <?php foreach ($outcomeOptions as $value => $label): ?>
                            <option value="<?= $e($value) ?>"<?= $value === $filters['outcome'] ? ' selected' : '' ?>><?= $e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="button button--ghost button--small" type="submit">Filter</button>
                </form>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Map</th>
                        <th scope="col">Played</th>
                        <th scope="col">Score</th>
                        <th scope="col">Lost duels</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="table__cell--action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($demos === []): ?>
                        <tr>
                            <td class="table__empty" colspan="6">No demos match these filters.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($demos as $demo): ?>
                        <?php $playedAt = new DateTimeImmutable($demo['playedAt']); ?>
                        <tr class="table__row table__row--<?= $e($demo['status']) ?>">
                            <th scope="row"><code class="map-name map-name--<?= $e($demo['status']) ?>"><?= $e($demo['map']) ?></code></th>
                            <td data-label="Played">
                                <time datetime="<?= $e($playedAt->format('Y-m-d\TH:i')) ?>">
                                    <?= $e($playedAt->format('M j, H:i')) ?>
                                </time>
                            </td>
                            <td data-label="Score">
                                <?php if ($demo['score'] !== null): ?>
                                    <span class="score">
                                        <?= (int) $demo['score'][0] ?>&ndash;<?= (int) $demo['score'][1] ?>
                                        <?php if ($demo['score'][0] > $demo['score'][1]): ?>
                                            <abbr class="badge badge--win" title="Win">W</abbr>
                                        <?php else: ?>
                                            <abbr class="badge badge--loss" title="Loss">L</abbr>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                            <td class="table__lost" data-label="Lost duels"><?= $demo['lostDuels'] !== null ? (int) $demo['lostDuels'] : '&mdash;' ?></td>
                            <td data-label="Status">
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <strong class="status status--ready">Ready</strong>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <strong class="status status--processing">Parsing ticks [<?= (int) $demo['progress'] ?>%]</strong>
                                    <progress class="progress progress--inline" value="<?= (int) $demo['progress'] ?>" max="100">
                                        <?= (int) $demo['progress'] ?>%
                                    </progress>
                                <?php else: ?>
                                    <strong class="status status--failed">Failed: <?= $e($demo['error']) ?></strong>
                                    <small class="table__detail"><?= $e($demo['errorDetail']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="table__cell--action">
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <a class="table__action" href="/demos/<?= (int) $demo['id'] ?>">View duels</a>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <em class="table__action table__action--muted">Parsing ticks...</em>
                                <?php else: ?>
                                    <form method="post" action="/demos/<?= (int) $demo['id'] ?>/delete">
                                        <button type="submit" class="table__action table__action--danger">Remove</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="recent__footer">Showing <strong><?= count($demos) ?></strong> of <strong><?= (int) $totalDemos ?></strong> uploaded demos</p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
