<?php
/**
 * A page the business designed itself (Business::showBusinessPages(), ownHome()), or one design shown with sample
 * texts (Business::designPreview()): its sections top to bottom, each drawn by its own view, sections/{design_code}
 *
 * @var list<array> $sections      v1/mart/webpage's sections, each with view (Business::drawable())
 * @var string      $business_slug
 */
$this->extend('_layout_customized');
?>
<?= $this->section('content') ?>
    <main class="main">
        <?php foreach ($sections as $section): ?>
            <?= view($section['view'], ['section' => $section, 'business_slug' => $business_slug]) ?>
        <?php endforeach ?>
    </main>
<?= $this->endSection() ?>
