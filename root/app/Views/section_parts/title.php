<?php
/**
 * The title block over a section (the theme's .section-title): a small heading, and a line under it whose second
 * part is in the accent colour. Drawn by the section views with their texts title, subtitle and subtitle_highlight;
 * nothing when all three are empty. Kept out of sections/ so that it can never be a design_code.
 *
 * @var string $title
 * @var string $subtitle
 * @var string $subtitle_highlight
 */
if ($title === '' && $subtitle === '' && $subtitle_highlight === '') {
    return;
}
?>
<div class="container section-title" data-aos="fade-up">
    <?php if ($title !== ''): ?>
        <h2><?= esc($title) ?></h2>
    <?php endif ?>
    <?php if ($subtitle !== '' || $subtitle_highlight !== ''): ?>
        <div><?php if ($subtitle !== ''): ?><span><?= esc($subtitle) ?></span><?php endif ?><?= $subtitle !== '' && $subtitle_highlight !== '' ? ' ' : '' ?><?php if ($subtitle_highlight !== ''): ?><span class="description-title"><?= esc($subtitle_highlight) ?></span><?php endif ?></div>
    <?php endif ?>
</div>
