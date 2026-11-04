<?php
// The one home of the demo upload form. Pages pick the look with $dropzoneClass (dropzone--hero or dropzone--compact).
$dropzoneClass = $dropzoneClass ?? '';
?>
<form class="dropzone <?= $e($dropzoneClass) ?>" method="post" action="/demos" enctype="multipart/form-data">
    <?php /* width/height/fill/stroke as attributes too: without the stylesheet (e.g. a stale cache) the icon stays small and outlined. */ ?>
    <svg class="dropzone__icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"
         stroke-width="1.75" aria-hidden="true" focusable="false">
        <path d="M7 18a4.5 4.5 0 0 1-.6-8.96A6 6 0 0 1 18 8.5a4 4 0 0 1-.5 8.5" />
        <path d="M12 21v-9m-3.5 3.5L12 12l3.5 3.5" />
    </svg>
    <label class="dropzone__label" for="demo-file">Drop your <code>.dem</code> file here or browse</label>
    <input class="dropzone__input" type="file" id="demo-file" name="demo" accept=".dem" required>
    <p class="dropzone__hint">CS2 demos only &middot; max 300 MB &middot; checked on the server before processing</p>
    <button class="button" type="submit">Upload</button>
</form>
