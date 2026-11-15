<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1><?= $e($t('admin.title')) ?></h1>
            <p><?= $e($t('admin.lead')) ?></p>
        </header>

        <section aria-labelledby="maps-heading">
            <h2 id="maps-heading"><?= $e($t('admin.maps')) ?></h2>
            <p><?= $e($t('admin.maps_note')) ?></p>
            <table>
                <thead>
                    <tr>
                        <th scope="col"><?= $e($t('admin.column.radar')) ?></th>
                        <th scope="col"><?= $e($t('common.map')) ?></th>
                        <th scope="col">pos_x</th>
                        <th scope="col">pos_y</th>
                        <th scope="col"><?= $e($t('admin.column.scale')) ?></th>
                        <th scope="col"><?= $e($t('admin.column.demos')) ?></th>
                        <th scope="col"><?= $e($t('common.action')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maps as $map): ?>
                        <tr>
                            <td><span role="img" aria-label="<?= $e($t('admin.radar_image', ['map' => $map['name']])) ?>">[img]</span></td>
                            <th scope="row"><?= $e($map['name']) ?></th>
                            <td><?= $e($map['posX']) ?></td>
                            <td><?= $e($map['posY']) ?></td>
                            <td><?= $e($map['scale']) ?></td>
                            <td><?= (int) $map['demos'] ?></td>
                            <td><a href="/admin/maps/<?= $e($map['name']) ?>/edit"><?= $e($t('admin.edit')) ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section aria-labelledby="add-map-heading">
            <h2 id="add-map-heading"><?= $e($t('admin.add_map')) ?></h2>
            <form method="post" action="/admin/maps" enctype="multipart/form-data">
                <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                <p>
                    <label for="map-name"><?= $e($t('admin.form.name')) ?></label>
                    <input type="text" id="map-name" name="name" value="<?= $e($form['name']) ?>"
                           pattern="de_[a-z0-9_]+" aria-describedby="map-name-hint" required>
                    <small id="map-name-hint"><?= $e($t('admin.form.name_hint')) ?></small>
                </p>
                <p>
                    <label for="map-radar"><?= $e($t('admin.form.radar')) ?></label>
                    <input type="file" id="map-radar" name="radar" accept="image/png,image/jpeg,image/webp"
                           aria-describedby="map-radar-hint" required>
                    <small id="map-radar-hint"><?= $e($t('admin.form.radar_hint')) ?></small>
                </p>
                <p>
                    <label for="map-pos-x">pos_x</label>
                    <input type="number" id="map-pos-x" name="pos_x" value="<?= $e($form['posX']) ?>" step="any" required>
                </p>
                <p>
                    <label for="map-pos-y">pos_y</label>
                    <input type="number" id="map-pos-y" name="pos_y" value="<?= $e($form['posY']) ?>" step="any" required>
                </p>
                <p>
                    <label for="map-scale">scale</label>
                    <input type="number" id="map-scale" name="scale" value="<?= $e($form['scale']) ?>"
                           min="0.1" max="20" step="any"
                           <?= isset($errors['scale']) ? 'aria-invalid="true" aria-describedby="map-scale-error"' : '' ?> required>
                    <?php if (isset($errors['scale'])): ?>
                        <small id="map-scale-error" role="alert"><?= $e($t('admin.error.' . $errors['scale'])) ?></small>
                    <?php endif; ?>
                </p>
                <button type="submit"><?= $e($t('admin.form.save')) ?></button>
                <a href="/admin"><?= $e($t('admin.form.cancel')) ?></a>
            </form>
        </section>

        <section aria-labelledby="queue-heading">
            <h2 id="queue-heading"><?= $e($t('admin.queue')) ?></h2>
            <ul>
                <?php foreach ($jobs as $job): ?>
                    <li>
                        <article>
                            <h3>#<?= (int) $job['id'] ?> &middot; <?= $e($job['user']) ?> &middot; <?= $e($job['map']) ?></h3>
                            <p><strong><?= $e($t('admin.job.status.' . $job['status'], ['progress' => (int) $job['progress']])) ?></strong></p>
                            <?php if ($job['status'] === 'processing'): ?>
                                <progress value="<?= (int) $job['progress'] ?>" max="100"><?= (int) $job['progress'] ?>%</progress>
                            <?php endif; ?>
                            <p><small><?= $e($t('admin.job.detail.' . $job['detail'][0], $job['detail'][1])) ?></small></p>
                            <?php if ($job['status'] === 'failed'): ?>
                                <form method="post" action="/admin/jobs/<?= (int) $job['id'] ?>/retry">
                                    <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
                                    <button type="submit"><?= $e($t('admin.job.retry')) ?></button>
                                </form>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section aria-labelledby="scheduled-heading">
            <h2 id="scheduled-heading"><?= $e($t('admin.scheduled')) ?></h2>
            <ul>
                <?php foreach ($scheduledJobs as $scheduled): ?>
                    <li>
                        <article>
                            <h3><code><?= $e($scheduled['name']) ?></code></h3>
                            <p><strong><?= $e($t('admin.scheduled.status.' . $scheduled['status'])) ?></strong></p>
                            <p><small>
                                <?= $e($t('admin.schedule.' . $scheduled['schedule'][0], $scheduled['schedule'][1])) ?>
                                &middot; <?= $e($t('admin.last_run.' . $scheduled['lastRun'][0], $scheduled['lastRun'][1])) ?>
                            </small></p>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
