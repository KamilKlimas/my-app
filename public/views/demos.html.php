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

        <section aria-labelledby="recent-heading">
            <h2 id="recent-heading">Recent demos</h2>
            <p>Status refreshes automatically</p>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Map</th>
                        <th scope="col">Played</th>
                        <th scope="col">Score</th>
                        <th scope="col">Lost duels</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($demos as $demo): ?>
                        <?php $playedAt = new DateTimeImmutable($demo['playedAt']); ?>
                        <tr>
                            <th scope="row"><code><?= $e($demo['map']) ?></code></th>
                            <td>
                                <time datetime="<?= $e($playedAt->format('Y-m-d\TH:i')) ?>">
                                    <?= $e($playedAt->format('M j, H:i')) ?>
                                </time>
                            </td>
                            <td>
                                <?php if ($demo['score'] !== null): ?>
                                    <?= (int) $demo['score'][0] ?>&ndash;<?= (int) $demo['score'][1] ?>
                                    <?php if ($demo['score'][0] > $demo['score'][1]): ?>
                                        <abbr title="Win">W</abbr>
                                    <?php else: ?>
                                        <abbr title="Loss">L</abbr>
                                    <?php endif; ?>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                            <td><?= $demo['lostDuels'] !== null ? (int) $demo['lostDuels'] : '&mdash;' ?></td>
                            <td>
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <strong>Ready</strong>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <strong>Parsing ticks [<?= (int) $demo['progress'] ?>%]</strong>
                                    <progress value="<?= (int) $demo['progress'] ?>" max="100">
                                        <?= (int) $demo['progress'] ?>%
                                    </progress>
                                <?php else: ?>
                                    <strong>Failed: <?= $e($demo['error']) ?></strong>
                                    <br>
                                    <small><?= $e($demo['errorDetail']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <a href="/demos/<?= (int) $demo['id'] ?>">View duels</a>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <em>Parsing ticks...</em>
                                <?php else: ?>
                                    <form method="post" action="/demos/<?= (int) $demo['id'] ?>/delete">
                                        <button type="submit">Remove</button>
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
