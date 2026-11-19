<?php
/**
 * Services 01 (Config\WebpageDesigns::$designs['services_01'] in otternova_api): an introduction with a link to
 * the shop, up to six service cards in a grid, each with a short list of points, and a call-to-action banner under
 * them. No pictures. Drawn by customizedPage.php for a section whose design_code is services_01; the styles are the
 * theme's (.services).
 *
 * Text slots (texts.{slot}), all optional but intro_title and service_1_title: title, subtitle, subtitle_highlight
 * (40 each, the title block); intro_title (120), intro_text (400, keeps its line breaks), view_all_label (32, to the
 * system home); service_1_title .. service_6_title (60), service_N_text (300), service_N_tag (24, a corner badge),
 * service_N_points (300, one point per line); service_link_label (32, the link under every card, to the system
 * home); cta_badge (32), cta_title (120), cta_text (400, keeps its line breaks), cta_button_label (32, to the system
 * home), cta_button_2_label (32, to the contact page). A card shows when it has a title or a text.
 * Image slots: none.
 *
 * @var array  $section       A section of v1/mart/webpage (see hero_01.php)
 * @var string $business_slug The business, for the links
 */
$texts    = $section['texts'] ?? [];
$text     = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
$lines    = static fn (string $slot): array => array_values(array_filter(array_map('trim', preg_split('/\R/u', $text($slot))), static fn (string $line): bool => $line !== ''));
$shopUrl  = base_url($business_slug . '/home');
$icons    = [1 => 'bi-star', 2 => 'bi-heart', 3 => 'bi-gem', 4 => 'bi-bag-check', 5 => 'bi-lightning-charge', 6 => 'bi-shield-check'];
$services = [];
foreach ($icons as $n => $icon) {
    if ($text("service_{$n}_title") !== '' || $text("service_{$n}_text") !== '') {
        $services[] = ['icon' => $icon, 'title' => $text("service_{$n}_title"), 'text' => $text("service_{$n}_text"), 'tag' => $text("service_{$n}_tag"),
            'points' => $lines("service_{$n}_points")];
    }
}
$hasCta = $text('cta_title') !== '' || $text('cta_text') !== '' || $text('cta_button_label') !== '' || $text('cta_button_2_label') !== '';
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="services section services-01">
    <?= view('section_parts/title', ['title' => $text('title'), 'subtitle' => $text('subtitle'), 'subtitle_highlight' => $text('subtitle_highlight')]) ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <?php if ($text('intro_title') !== '' || $text('intro_text') !== '' || $text('view_all_label') !== ''): ?>
            <div class="section-intro">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <?php if ($text('intro_title') !== ''): ?>
                            <h2 class="intro-title"><?= esc($text('intro_title')) ?></h2>
                        <?php endif ?>
                        <?php if ($text('intro_text') !== ''): ?>
                            <p class="intro-text"><?= nl2br(esc($text('intro_text'))) ?></p>
                        <?php endif ?>
                    </div>
                    <?php if ($text('view_all_label') !== ''): ?>
                        <div class="col-lg-5 text-lg-end">
                            <a href="<?= esc($shopUrl, 'attr') ?>" class="btn-view-all"><?= esc($text('view_all_label')) ?> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
        <?php if ($services !== []): ?>
            <div class="row gy-4">
                <?php foreach ($services as $i => $service): ?>
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= 150 + ($i % 3) * 50 ?>">
                        <div class="service-item">
                            <div class="service-header">
                                <div class="icon-wrapper"><i class="bi <?= $service['icon'] ?>"></i></div>
                                <?php if ($service['tag'] !== ''): ?>
                                    <span class="badge-popular"><?= esc($service['tag']) ?></span>
                                <?php endif ?>
                            </div>
                            <?php if ($service['title'] !== ''): ?>
                                <h3><?= esc($service['title']) ?></h3>
                            <?php endif ?>
                            <?php if ($service['text'] !== ''): ?>
                                <p><?= esc($service['text']) ?></p>
                            <?php endif ?>
                            <?php if ($service['points'] !== []): ?>
                                <ul class="feature-list">
                                    <?php foreach ($service['points'] as $point): ?>
                                        <li><i class="bi bi-check-circle"></i> <?= esc($point) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                            <?php if ($text('service_link_label') !== ''): ?>
                                <a href="<?= esc($shopUrl, 'attr') ?>" class="service-cta"><span><?= esc($text('service_link_label')) ?></span> <i class="bi bi-arrow-right"></i></a>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
        <?php if ($hasCta): ?>
            <div class="cta-banner" data-aos="fade-up" data-aos-delay="300">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="cta-content">
                            <?php if ($text('cta_badge') !== ''): ?>
                                <div class="cta-badge"><?= esc($text('cta_badge')) ?></div>
                            <?php endif ?>
                            <?php if ($text('cta_title') !== ''): ?>
                                <h3><?= esc($text('cta_title')) ?></h3>
                            <?php endif ?>
                            <?php if ($text('cta_text') !== ''): ?>
                                <p><?= nl2br(esc($text('cta_text'))) ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <?php if ($text('cta_button_label') !== ''): ?>
                            <a href="<?= esc($shopUrl, 'attr') ?>" class="btn-primary"><?= esc($text('cta_button_label')) ?> <i class="bi bi-arrow-right"></i></a>
                        <?php endif ?>
                        <?php if ($text('cta_button_2_label') !== ''): ?>
                            <a href="<?= esc(base_url($business_slug . '/contact-us'), 'attr') ?>" class="btn-secondary"><?= esc($text('cta_button_2_label')) ?></a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>
