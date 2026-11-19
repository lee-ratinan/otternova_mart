<?php
/**
 * About 01 (Config\WebpageDesigns::$designs['about_01'] in otternova_api): who the business is, with up to four
 * figures, two strengths and two buttons on the left, and a large picture with a smaller one over its corner and an
 * award badge on the right. Drawn by customizedPage.php for a section whose design_code is about_01; the styles are
 * the theme's (.about).
 *
 * Text slots (texts.{slot}), all optional but the headline: badge (32), headline (120), paragraph (600, keeps its
 * line breaks), stat_1_number .. stat_4_number (12) with stat_1_label .. stat_4_label (40), feature_1_title,
 * feature_2_title (60) with feature_1_text, feature_2_text (200), button_label (32, to the system home),
 * button_2_label (32, to the contact page), award_title (40), award_subtitle (60).
 * Image slots (images.{slot}): picture, 0 or 1 of 1200x900; inset, 0 or 1 of 800x800.
 *
 * @var array  $section       A section of v1/mart/webpage (see hero_01.php)
 * @var string $business_slug The business, for the buttons' links
 */
$texts   = $section['texts'] ?? [];
$picture = $section['images']['picture'][0] ?? null;
$inset   = $section['images']['inset'][0] ?? null;
$text    = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
$stats   = array_values(array_filter(array_map(static fn (int $n): array => ['number' => $text("stat_{$n}_number"), 'label' => $text("stat_{$n}_label")], range(1, 4)),
    static fn (array $stat): bool => $stat['number'] !== '' || $stat['label'] !== ''));
$icons    = [1 => 'bi-lightning-charge-fill', 2 => 'bi-shield-check'];
$features = [];
foreach ($icons as $n => $icon) {
    if ($text("feature_{$n}_title") !== '' || $text("feature_{$n}_text") !== '') {
        $features[] = ['icon' => $icon, 'title' => $text("feature_{$n}_title"), 'text' => $text("feature_{$n}_text")];
    }
}
$hasVisual = $picture !== null || $inset !== null;
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="about section light-background about-01">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
            <div class="<?= $hasVisual ? 'col-lg-6' : 'col-lg-10 mx-auto' ?>" data-aos="fade-up" data-aos-delay="200">
                <div class="content-wrapper">
                    <?php if ($text('badge') !== ''): ?>
                        <div class="section-badge"><?= esc($text('badge')) ?></div>
                    <?php endif ?>
                    <?php if ($text('headline') !== ''): ?>
                        <h2><?= esc($text('headline')) ?></h2>
                    <?php endif ?>
                    <?php if ($text('paragraph') !== ''): ?>
                        <p class="lead"><?= nl2br(esc($text('paragraph'))) ?></p>
                    <?php endif ?>
                    <?php if ($stats !== []): ?>
                        <div class="stats-grid">
                            <?php foreach ($stats as $stat): ?>
                                <div class="stat-item">
                                    <div class="stat-number"><?= esc($stat['number']) ?></div>
                                    <div class="stat-label"><?= esc($stat['label']) ?></div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <?php if ($features !== []): ?>
                        <div class="features-row">
                            <?php foreach ($features as $feature): ?>
                                <div class="feature-item">
                                    <div class="feature-icon"><i class="bi <?= $feature['icon'] ?>"></i></div>
                                    <div class="feature-content">
                                        <?php if ($feature['title'] !== ''): ?>
                                            <h4><?= esc($feature['title']) ?></h4>
                                        <?php endif ?>
                                        <?php if ($feature['text'] !== ''): ?>
                                            <p><?= esc($feature['text']) ?></p>
                                        <?php endif ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                    <?php if ($text('button_label') !== '' || $text('button_2_label') !== ''): ?>
                        <div class="cta-group">
                            <?php if ($text('button_label') !== ''): ?>
                                <a href="<?= esc(base_url($business_slug . '/home'), 'attr') ?>" class="btn btn-primary"><?= esc($text('button_label')) ?> <i class="bi bi-arrow-right"></i></a>
                            <?php endif ?>
                            <?php if ($text('button_2_label') !== ''): ?>
                                <a href="<?= esc(base_url($business_slug . '/contact-us'), 'attr') ?>" class="btn btn-secondary"><?= esc($text('button_2_label')) ?></a>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <?php if ($hasVisual): ?>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="image-stack">
                        <?php if ($picture !== null): ?>
                            <div class="image-card image-primary">
                                <img src="<?= esc($picture, 'attr') ?>" alt="<?= esc($text('headline'), 'attr') ?>" class="img-fluid">
                            </div>
                        <?php endif ?>
                        <?php if ($inset !== null): ?>
                            <div class="image-card image-secondary">
                                <img src="<?= esc($inset, 'attr') ?>" alt="" class="img-fluid">
                            </div>
                        <?php endif ?>
                        <?php if ($text('award_title') !== '' || $text('award_subtitle') !== ''): ?>
                            <div class="floating-badge">
                                <div class="badge-icon"><i class="bi bi-award-fill"></i></div>
                                <div class="badge-text">
                                    <?php if ($text('award_title') !== ''): ?>
                                        <div class="badge-title"><?= esc($text('award_title')) ?></div>
                                    <?php endif ?>
                                    <?php if ($text('award_subtitle') !== ''): ?>
                                        <div class="badge-subtitle"><?= esc($text('award_subtitle')) ?></div>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
