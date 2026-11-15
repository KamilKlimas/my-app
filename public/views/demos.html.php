<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <header class="page-header">
            <div>
                <h1 class="page-title"><?= $e($t('demos.heading')) ?> <span class="live-badge"><?= $e($t('demos.live_badge')) ?></span></h1>
                <p class="page-lead"><?= $e($t('demos.lead')) ?></p>
            </div>
            <a class="button button--outline" href="/training"><?= $e($t('demos.open_training')) ?></a>
        </header>

        <section class="overview" aria-labelledby="stats-heading">
            <h2 id="stats-heading" class="visually-hidden"><?= $e($t('demos.stats.heading')) ?></h2>
            <dl class="stats__list">
                <div class="stat">
                    <dt class="stat__label"><?= $e($t('demos.stats.analysed')) ?></dt>
                    <dd class="stat__value"><?= (int) $stats['demosAnalysed'] ?></dd>
                    <dd class="stat__note"><?= $e($t('demos.stats.analysed_note')) ?></dd>
                </div>
                <div class="stat stat--danger">
                    <dt class="stat__label"><?= $e($t('demos.stats.lost')) ?></dt>
                    <dd class="stat__value"><?= (int) $stats['lostDuels'] ?></dd>
                    <dd class="stat__note"><?= $e($t('demos.stats.lost_note', ['average' => $stats['duelsPerMatch']])) ?></dd>
                </div>
                <div class="stat stat--accent">
                    <dt class="stat__label"><?= $e($t('demos.stats.trainable')) ?></dt>
                    <dd class="stat__value"><?= (int) $stats['trainableDuels'] ?></dd>
                    <dd class="stat__note"><?= $e($t('demos.stats.trainable_note', ['percent' => (int) $stats['convertedPercent']])) ?></dd>
                </div>
                <div class="stat stat--issue">
                    <dt class="stat__label"><?= $e($t('demos.stats.top_issue')) ?></dt>
                    <dd class="stat__value stat__value--text"><?= $e($t('issue.' . $stats['topIssue']['code'])) ?></dd>
                    <dd class="stat__note"><strong class="stat__share"><?= (int) $stats['topIssue']['share'] ?>%</strong> <?= $e($t('demos.stats.top_issue_note')) ?></dd>
                </div>
            </dl>

            <section class="quick-upload" aria-labelledby="quick-upload-heading">
                <div class="quick-upload__header">
                    <h3 id="quick-upload-heading" class="quick-upload__title"><?= $e($t('demos.quick_upload')) ?></h3>
                    <button class="button button--ghost button--small" type="button"><?= $e($t('demos.sync_folder')) ?></button>
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
                    <?= $e($t('demos.recent')) ?> <span class="count-badge"><?= $e($t('demos.match_count', ['count' => count($demos)])) ?></span>
                </h2>
                <p class="recent__note"><?= $e($t('demos.recent_note')) ?></p>

                <form class="filters" method="get" action="/demos">
                    <label class="visually-hidden" for="filter-map"><?= $e($t('common.map')) ?></label>
                    <select class="filters__select" id="filter-map" name="map">
                        <option value="all"<?= $filters['map'] === 'all' ? ' selected' : '' ?>><?= $e($t('demos.filter.all_maps')) ?></option>
                        <?php foreach ($mapNames as $mapName): ?>
                            <option value="<?= $e($mapName) ?>"<?= $mapName === $filters['map'] ? ' selected' : '' ?>><?= $e($mapName) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="visually-hidden" for="filter-outcome"><?= $e($t('demos.filter.outcome')) ?></label>
                    <select class="filters__select" id="filter-outcome" name="outcome">
                        <?php foreach ($outcomeOptions as $outcome): ?>
                            <option value="<?= $e($outcome) ?>"<?= $outcome === $filters['outcome'] ? ' selected' : '' ?>><?= $e($t('demos.filter.outcome.' . $outcome)) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="button button--ghost button--small" type="submit"><?= $e($t('demos.filter.submit')) ?></button>
                </form>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?= $e($t('common.map')) ?></th>
                        <th scope="col"><?= $e($t('demos.column.played')) ?></th>
                        <th scope="col"><?= $e($t('demos.column.score')) ?></th>
                        <th scope="col"><?= $e($t('demos.column.lost')) ?></th>
                        <th scope="col"><?= $e($t('common.status')) ?></th>
                        <th scope="col" class="table__cell--action"><?= $e($t('common.action')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($demos === []): ?>
                        <tr>
                            <td class="table__empty" colspan="6"><?= $e($t('demos.empty')) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($demos as $demo): ?>
                        <?php $playedAt = new DateTimeImmutable($demo['playedAt']); ?>
                        <tr class="table__row table__row--<?= $e($demo['status']) ?>">
                            <th scope="row"><code class="map-name map-name--<?= $e($demo['status']) ?>"><?= $e($demo['map']) ?></code></th>
                            <td data-label="<?= $e($t('demos.column.played')) ?>">
                                <time datetime="<?= $e($playedAt->format('Y-m-d\TH:i')) ?>">
                                    <?= $e($t('common.date_time', ['at' => $playedAt])) ?>
                                </time>
                            </td>
                            <td data-label="<?= $e($t('demos.column.score')) ?>">
                                <?php if ($demo['score'] !== null): ?>
                                    <span class="score">
                                        <?= (int) $demo['score'][0] ?>&ndash;<?= (int) $demo['score'][1] ?>
                                        <?php if ($demo['score'][0] > $demo['score'][1]): ?>
                                            <abbr class="badge badge--win" title="<?= $e($t('common.win')) ?>"><?= $e($t('common.win_short')) ?></abbr>
                                        <?php else: ?>
                                            <abbr class="badge badge--loss" title="<?= $e($t('common.loss')) ?>"><?= $e($t('common.loss_short')) ?></abbr>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    &mdash;
                                <?php endif; ?>
                            </td>
                            <td class="table__lost" data-label="<?= $e($t('demos.column.lost')) ?>"><?= $demo['lostDuels'] !== null ? (int) $demo['lostDuels'] : '&mdash;' ?></td>
                            <td data-label="<?= $e($t('common.status')) ?>">
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <strong class="status status--ready"><?= $e($t('demos.status.ready')) ?></strong>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <strong class="status status--processing"><?= $e($t('demos.status.processing', ['progress' => (int) $demo['progress']])) ?></strong>
                                    <progress class="progress progress--inline" value="<?= (int) $demo['progress'] ?>" max="100">
                                        <?= (int) $demo['progress'] ?>%
                                    </progress>
                                <?php else: ?>
                                    <strong class="status status--failed"><?= $e($t('demos.status.failed.' . $demo['error'])) ?></strong>
                                    <small class="table__detail"><?= $e($t('demos.status.failed.' . $demo['error'] . '.detail')) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="table__cell--action">
                                <?php if ($demo['status'] === 'ready'): ?>
                                    <a class="table__action" href="/demos/<?= (int) $demo['id'] ?>"><?= $e($t('demos.action.view')) ?></a>
                                <?php elseif ($demo['status'] === 'processing'): ?>
                                    <em class="table__action table__action--muted"><?= $e($t('demos.action.parsing')) ?></em>
                                <?php else: ?>
                                    <form method="post" action="/demos/<?= (int) $demo['id'] ?>/delete">
                                        <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                                        <button type="submit" class="table__action table__action--danger"><?= $e($t('demos.action.remove')) ?></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="recent__footer"><?= $e($t('demos.showing', ['shown' => count($demos), 'total' => (int) $totalDemos])) ?></p>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
