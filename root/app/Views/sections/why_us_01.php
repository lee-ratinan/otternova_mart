<?php
/**
 * Why Us 01 (Config\WebpageDesigns::$designs['why_us_01'] in otternova_api): why customers choose the business, in
 * three rows: an introduction with up to three figures beside a picture; up to three value cards, one with a badge
 * standing out; and what the business does best with two buttons beside up to four numbered steps. Drawn by
 * customizedPage.php for a section whose design_code is why_us_01; the styles are the theme's (.why-us).
 *
 * Text slots (texts.{slot}), all optional but the headline: title, subtitle, subtitle_highlight (40 each, the title
 * block); badge (32), headline (120), paragraph (600, keeps its line breaks); stat_1_number .. stat_3_number (12)
 * with stat_N_label (40); value_1_title .. value_3_title (60), value_N_text (300), value_N_badge (24, makes the card
 * stand out); capabilities_title (120), capabilities_text (400, keeps its line breaks), capability_1_title ..
 * capability_3_title (60) with capability_N_text (300); button_label (32, to the system home), button_2_label (32,
 * to the contact page); step_1_title .. step_4_title (40) with step_N_text (80), numbered in order.
 * Image slots (images.{slot}): picture, 0 or 1 of 1200x900.
 *
 * @var array  $section       A section of v1/mart/webpage (see hero_01.php)
 * @var string $business_slug The business, for the buttons' links
 */
$texts   = $section['texts'] ?? [];
$picture = $section['images']['picture'][0] ?? null;
$text    = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
/** The numbered items $prefix_1_* .. $prefix_{$count}_* that have any of $fields, each field => text */
$items   = static function (string $prefix, int $count, array $fields) use ($text): array {
    $found = [];
    foreach (range(1, $count) as $n) {
        $item = [];
        foreach ($fields as $field) {
            $item[$field] = $text("{$prefix}_{$n}_{$field}");
        }
        if (implode('', $item) !== '') {
            $found[] = $item;
        }
    }

    return $found;
};
$stats        = $items('stat', 3, ['number', 'label']);
$values       = $items('value', 3, ['title', 'text', 'badge']);
$capabilities = $items('capability', 3, ['title', 'text']);
$steps        = $items('step', 4, ['title', 'text']);
$valueIcons   = ['bi-lightning-charge-fill', 'bi-heart-fill', 'bi-shield-check'];
$hasIntro     = $text('badge') !== '' || $text('headline') !== '' || $text('paragraph') !== '' || $stats !== [];
$hasButtons   = $text('button_label') !== '' || $text('button_2_label') !== '';
$hasCapable   = $text('capabilities_title') !== '' || $text('capabilities_text') !== '' || $capabilities !== [] || $hasButtons;
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="why-us section light-background why-us-01">
    <?= view('section_parts/title', ['title' => $text('title'), 'subtitle' => $text('subtitle'), 'subtitle_highlight' => $text('subtitle_highlight')]) ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <?php if ($hasIntro || $picture !== null): ?>
            <div class="row align-items-center mb-5">
                <?php if ($hasIntro): ?>
                    <div class="<?= $picture !== null ? 'col-lg-6' : 'col-lg-10 mx-auto' ?>" data-aos="fade-right" data-aos-delay="200">
                        <div class="intro-content">
                            <?php if ($text('badge') !== ''): ?>
                                <span class="badge"><?= esc($text('badge')) ?></span>
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
                        </div>
                    </div>
                <?php endif ?>
                <?php if ($picture !== null): ?>
                    <div class="<?= $hasIntro ? 'col-lg-6' : 'col-lg-8 mx-auto' ?>" data-aos="fade-left" data-aos-delay="300">
                        <div class="showcase-image">
                            <img src="<?= esc($picture, 'attr') ?>" alt="<?= esc($text('headline'), 'attr') ?>" class="img-fluid">
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endif ?>
        <?php if ($values !== []): ?>
            <div class="row g-4 mb-5 justify-content-center">
                <?php foreach ($values as $i => $value): ?>
                    <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?= 100 + $i * 100 ?>">
                        <div class="value-card<?= $value['badge'] !== '' ? ' featured' : '' ?>">
                            <?php if ($value['badge'] !== ''): ?>
                                <div class="featured-badge"><?= esc($value['badge']) ?></div>
                            <?php endif ?>
                            <div class="icon-box"><i class="bi <?= $valueIcons[$i] ?>"></i></div>
                            <?php if ($value['title'] !== ''): ?>
                                <h4><?= esc($value['title']) ?></h4>
                            <?php endif ?>
                            <?php if ($value['text'] !== ''): ?>
                                <p><?= esc($value['text']) ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
        <?php if ($hasCapable || $steps !== []): ?>
            <div class="row align-items-center">
                <?php if ($hasCapable): ?>
                    <div class="<?= $steps !== [] ? 'col-lg-6 order-lg-2' : 'col-lg-10 mx-auto' ?>" data-aos="fade-left" data-aos-delay="200">
                        <div class="capabilities-content">
                            <?php if ($text('capabilities_title') !== ''): ?>
                                <h3><?= esc($text('capabilities_title')) ?></h3>
                            <?php endif ?>
                            <?php if ($text('capabilities_text') !== ''): ?>
                                <p><?= nl2br(esc($text('capabilities_text'))) ?></p>
                            <?php endif ?>
                            <?php if ($capabilities !== []): ?>
                                <div class="capability-list">
                                    <?php foreach ($capabilities as $capability): ?>
                                        <div class="capability-item">
                                            <div class="capability-header">
                                                <i class="bi bi-check-circle-fill"></i>
                                                <h5><?= esc($capability['title']) ?></h5>
                                            </div>
                                            <?php if ($capability['text'] !== ''): ?>
                                                <p><?= esc($capability['text']) ?></p>
                                            <?php endif ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                            <?php if ($hasButtons): ?>
                                <div class="cta-buttons">
                                    <?php if ($text('button_label') !== ''): ?>
                                        <a href="<?= esc(base_url($business_slug . '/home'), 'attr') ?>" class="btn btn-primary"><?= esc($text('button_label')) ?></a>
                                    <?php endif ?>
                                    <?php if ($text('button_2_label') !== ''): ?>
                                        <a href="<?= esc(base_url($business_slug . '/contact-us'), 'attr') ?>" class="btn btn-secondary"><?= esc($text('button_2_label')) ?></a>
                                    <?php endif ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endif ?>
                <?php if ($steps !== []): ?>
                    <div class="<?= $hasCapable ? 'col-lg-6 order-lg-1' : 'col-lg-8 mx-auto' ?>" data-aos="fade-right" data-aos-delay="300">
                        <div class="process-visual">
                            <?php foreach ($steps as $i => $step): ?>
                                <div class="process-step" data-aos="fade-up" data-aos-delay="<?= 400 + $i * 50 ?>">
                                    <div class="step-number"><?= sprintf('%02d', $i + 1) ?></div>
                                    <div class="step-content">
                                        <?php if ($step['title'] !== ''): ?>
                                            <h6><?= esc($step['title']) ?></h6>
                                        <?php endif ?>
                                        <?php if ($step['text'] !== ''): ?>
                                            <p><?= esc($step['text']) ?></p>
                                        <?php endif ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</section>
