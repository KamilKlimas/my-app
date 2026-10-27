<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <header>
            <h1>Admin</h1>
            <p>Maps, the processing queue and scheduled jobs. Only ADMIN accounts reach this page &mdash; a USER calling any admin route gets 403.</p>
        </header>

        <section aria-labelledby="maps-heading">
            <h2 id="maps-heading">Maps</h2>
            <p>Calibration values come from each map's overview file</p>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Radar</th>
                        <th scope="col">Map</th>
                        <th scope="col">pos_x</th>
                        <th scope="col">pos_y</th>
                        <th scope="col">Scale</th>
                        <th scope="col">Demos</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($maps as $map): ?>
                        <tr>
                            <td><span role="img" aria-label="Radar image of <?= $e($map['name']) ?>">[img]</span></td>
                            <th scope="row"><?= $e($map['name']) ?></th>
                            <td><?= $e($map['posX']) ?></td>
                            <td><?= $e($map['posY']) ?></td>
                            <td><?= $e($map['scale']) ?></td>
                            <td><?= (int) $map['demos'] ?></td>
                            <td><a href="/admin/maps/<?= $e($map['name']) ?>/edit">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section aria-labelledby="add-map-heading">
            <h2 id="add-map-heading">Add map</h2>
            <form method="post" action="/admin/maps" enctype="multipart/form-data">
                <p>
                    <label for="map-name">Map name</label>
                    <input type="text" id="map-name" name="name" value="<?= $e($form['name']) ?>"
                           pattern="de_[a-z0-9_]+" aria-describedby="map-name-hint" required>
                    <small id="map-name-hint">Lowercase, starts with de_</small>
                </p>
                <p>
                    <label for="map-radar">Radar image</label>
                    <input type="file" id="map-radar" name="radar" accept="image/png,image/jpeg,image/webp"
                           aria-describedby="map-radar-hint" required>
                    <small id="map-radar-hint">PNG, JPG or WebP, max 5 MB. Real type is checked on the server and the file gets a random name.</small>
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
                        <small id="map-scale-error" role="alert"><?= $e($errors['scale']) ?></small>
                    <?php endif; ?>
                </p>
                <button type="submit">Save map</button>
                <a href="/admin">Cancel</a>
            </form>
        </section>

        <section aria-labelledby="queue-heading">
            <h2 id="queue-heading">Processing queue</h2>
            <ul>
                <?php foreach ($jobs as $job): ?>
                    <li>
                        <article>
                            <h3>#<?= (int) $job['id'] ?> &middot; <?= $e($job['user']) ?> &middot; <?= $e($job['map']) ?></h3>
                            <p><strong><?= $e($job['statusLabel']) ?></strong></p>
                            <?php if ($job['status'] === 'processing'): ?>
                                <progress value="<?= (int) $job['progress'] ?>" max="100"><?= (int) $job['progress'] ?>%</progress>
                            <?php endif; ?>
                            <p><small><?= $e($job['detail']) ?></small></p>
                            <?php if ($job['status'] === 'failed'): ?>
                                <form method="post" action="/admin/jobs/<?= (int) $job['id'] ?>/retry">
                                    <button type="submit">Retry</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section aria-labelledby="scheduled-heading">
            <h2 id="scheduled-heading">Scheduled jobs</h2>
            <ul>
                <?php foreach ($scheduledJobs as $scheduled): ?>
                    <li>
                        <article>
                            <h3><code><?= $e($scheduled['name']) ?></code></h3>
                            <p><strong><?= $e($scheduled['status']) ?></strong></p>
                            <p><small><?= $e($scheduled['schedule']) ?> &middot; <?= $e($scheduled['lastRun']) ?></small></p>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
