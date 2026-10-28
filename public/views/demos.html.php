<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <header class="page-header">
            <div>
                <p class="eyebrow">Combat telemetry // Replay vault</p>
                <h1 class="page-title">My demos</h1>
                <p class="page-lead">Upload a CS2 demo &mdash; DuelLab finds the duels you lost, tells you why, and turns them into practice scenarios.</p>
            </div>
            <a class="page-header__link" href="/training">Open training set</a>
        </header>

        <section class="stats" aria-labelledby="stats-heading">
            <h2 id="stats-heading" class="visually-hidden">Summary</h2>
            <dl class="stats__list">
                <div class="stat">
                    <dt class="stat__label">Demos analysed</dt>
                    <dd class="stat__value"><?= $e($stats['demosAnalysed']) ?></dd>
                    <dd class="stat__note">recorded</dd>
                </div>
                <div class="stat stat--danger">
                    <dt class="stat__label">Lost duels found</dt>
                    <dd class="stat__value"><?= $e($stats['lostDuels']) ?></dd>
                    <dd class="stat__note">total engaged</dd>
                </div>
                <div class="stat stat--accent">
                    <dt class="stat__label">Trainable duels</dt>
                    <dd class="stat__value"><?= $e($stats['trainableDuels']) ?></dd>
                    <dd class="stat__note"><?= $e($stats['convertedPercent']) ?>% converted</dd>
                </div>
                <div class="stat stat--issue">
                    <dt class="stat__label">Top issue</dt>
                    <dd class="stat__value stat__value--text"><?= $e($stats['topIssue']['label']) ?></dd>
                    <dd class="stat__note"><?= $e($stats['topIssue']['share']) ?>% of lost duels</dd>
                </div>
            </dl>
        </section>

        <div class="split">
            <section class="panel" aria-labelledby="upload-heading">
                <h2 id="upload-heading" class="panel__title">Upload a demo</h2>
                <form class="dropzone" method="post" action="/demos" enctype="multipart/form-data">
                    <label class="dropzone__label" for="demo-file">Drop a <code>.dem</code> file here or browse</label>
                    <input class="dropzone__input" type="file" id="demo-file" name="demo" accept=".dem" required>
                    <p class="dropzone__hint">CS2 demos only &middot; max 300 MB &middot; checked on the server before processing</p>
                    <button class="button" type="submit">Upload</button>
                </form>

                <?php if ($upload !== null): ?>
                    <p class="upload-progress">
                        <label class="upload-progress__label" for="upload-progress"><?= $e($upload['file']) ?> &mdash; uploading...</label>
                        <progress class="progress" id="upload-progress" value="<?= (int) $upload['progress'] ?>" max="100">
                            <?= (int) $upload['progress'] ?>%
                        </progress>
                    </p>
                <?php endif; ?>
            </section>

            <aside class="panel" aria-labelledby="auto-import-heading">
                <h2 id="auto-import-heading" class="panel__title panel__title--info">Auto-import from your replays folder</h2>
                <p class="panel__text">Point DuelLab at the CS2 replays folder once. New demos are picked up whenever this tab is open (Chrome / Edge).</p>
                <button class="button button--ghost" type="button">Choose folder</button>
                <p class="panel__note"><small>Other browsers: drag the file in manually.</small></p>
            </aside>
        </div>

        <section class="recent" aria-labelledby="recent-heading">
            <div class="recent__header">
                <h2 id="recent-heading">Recent demos</h2>
                <p class="recent__note">Status refreshes automatically</p>
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
                    <?php foreach ($demos as $demo): ?>
                        <?php $playedAt = new DateTimeImmutable($demo['playedAt']); ?>
                        <tr class="table__row<?= $demo['status'] === 'processing' ? ' is-processing' : '' ?>">
                            <th scope="row"><code class="chip"><?= $e($demo['map']) ?></code></th>
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
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
