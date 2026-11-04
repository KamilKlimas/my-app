<?php require __DIR__ . '/partials/header.html.php'; ?>

    <main class="page">
        <section class="hero" aria-labelledby="hero-heading">
            <p class="hero__badge">Combat duel isolation engine</p>
            <h1 id="hero-heading" class="hero__title">Upload your <span class="hero__accent">demo</span></h1>
            <p class="hero__lead">
                Drop your Counter-Strike 2 replay file (<code>.dem</code>) to isolate every lost duel, analyse crosshair angles
                at sub-tick resolution, and generate custom practice routines.
            </p>

            <div class="upload-card">
                <?php $dropzoneClass = 'dropzone--hero'; ?>
                <?php require __DIR__ . '/partials/upload-form.html.php'; ?>

                <div class="upload-card__actions">
                    <button class="button button--ghost upload-card__action" type="button">
                        Auto-import from replays folder (Chrome/Edge)
                    </button>
                    <a class="upload-card__action upload-card__action--sample" href="/demos/1">
                        Try with sample demo (de_mirage)
                    </a>
                </div>
                <p class="upload-card__note">Parser supports Valve matchmaking, Premier, FACEIT 128-tick and ESL demos</p>
            </div>
        </section>

        <section class="steps" aria-labelledby="steps-heading">
            <header class="section-header">
                <div>
                    <p class="section-header__eyebrow">Pipeline architecture</p>
                    <h2 id="steps-heading" class="section-header__title">How DuelLab works</h2>
                </div>
                <p class="section-header__note">Extract &rarr; diagnose &rarr; practise</p>
            </header>

            <ol class="steps__list">
                <li class="step-card">
                    <p class="step-card__number">01. Extraction <span class="step-card__tag">Sub-tick</span></p>
                    <h3 class="step-card__title">Sub-tick combat analysis</h3>
                    <p class="step-card__text">
                        Isolates every duel you lost down to the server tick: your velocity, weapon spread and crosshair error at the moment of the shot.
                    </p>
                    <dl class="step-card__preview readout">
                        <div class="readout__row">
                            <dt>Event: duel lost</dt>
                            <dd class="readout__value readout__value--danger">R5 &middot; 0:48</dd>
                        </div>
                        <div class="readout__row">
                            <dt>Velocity at shot</dt>
                            <dd class="readout__value readout__value--danger">138 u/s (running)</dd>
                        </div>
                        <div class="readout__row">
                            <dt>Crosshair offset</dt>
                            <dd class="readout__value readout__value--accent">6&deg; off-head</dd>
                        </div>
                        <div class="readout__row">
                            <dt>Time to damage</dt>
                            <dd class="readout__value">410 ms</dd>
                        </div>
                    </dl>
                </li>

                <li class="step-card">
                    <p class="step-card__number">02. Visualization <span class="step-card__tag">Radar</span></p>
                    <h3 class="step-card__title">Radar heatmaps &amp; sightlines</h3>
                    <p class="step-card__text">
                        Every duel is placed on a calibrated map radar, so you can see the line of sight, the peek and who else had an angle on you.
                    </p>
                    <figure class="step-card__preview radar-preview">
                        <svg class="radar-preview__svg" viewBox="0 0 240 90" role="img"
                             aria-label="Sightline from your position on A ramp to the enemy on A site">
                            <line class="radar-preview__line" x1="40" y1="65" x2="180" y2="25" />
                            <circle class="radar-preview__you" cx="40" cy="65" r="7" />
                            <rect class="radar-preview__enemy" x="172" y="17" width="16" height="16" transform="rotate(45 180 25)" />
                            <text class="radar-preview__label radar-preview__label--you" x="54" y="80">You (A ramp)</text>
                            <text class="radar-preview__label" x="120" y="14">Enemy (A site)</text>
                        </svg>
                        <figcaption class="radar-preview__caption">Sightline calibration</figcaption>
                    </figure>
                </li>

                <li class="step-card">
                    <p class="step-card__number">03. Execution <span class="step-card__tag">CS2 cfg</span></p>
                    <h3 class="step-card__title">Instant .cfg generator</h3>
                    <p class="step-card__text">
                        Export a practice config in one click. Bots are placed with the exact angles and gear, right where you died.
                    </p>
                    <pre class="step-card__preview console"><span class="console__prompt">&gt; exec duellab_practice.cfg</span>
Loaded 4 drill scenarios for de_mirage
<span class="console__info">[DuelLab] Bot placed at pos_x: -1240, pos_y: -431</span>
<span class="console__accent">[Practice active] Counter-strafe drill</span></pre>
                </li>
            </ol>
        </section>

        <section class="mistakes" aria-labelledby="mistakes-heading">
            <header class="section-header">
                <div>
                    <p class="section-header__eyebrow">Error classification</p>
                    <h2 id="mistakes-heading" class="section-header__title">Mistakes DuelLab detects automatically</h2>
                </div>
                <p class="section-header__note">Each one becomes a practice drill</p>
            </header>

            <ol class="mistakes__list">
                <?php foreach ($issues as $index => $issue): ?>
                    <li class="mistake-card mistake-card--<?= $e($issue['tone']) ?>">
                        <p class="mistake-card__meta">
                            <span>Repeating error #<?= (int) $index + 1 ?></span>
                            <strong class="mistake-card__share"><?= (int) $issue['share'] ?>% of losses</strong>
                        </p>
                        <h3 class="mistake-card__title"><?= $e($issue['label']) ?></h3>
                        <p class="mistake-card__text"><?= $e($issue['description']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
    </main>

<?php require __DIR__ . '/partials/footer.html.php'; ?>
