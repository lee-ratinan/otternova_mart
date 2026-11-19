<?php
/**
 * Testimonials 01 (Config\WebpageDesigns::$designs['testimonials_01'] in otternova_api): what customers say, up to
 * four, one at a time in a slider with arrows, each with the person's picture large beside it. Drawn by
 * customizedPage.php for a section whose design_code is testimonials_01; the styles are the theme's
 * (.testimonials), the slider main.js's (.init-swiper).
 *
 * Text slots (texts.{slot}), all optional but item_1_quote and item_1_name: title, subtitle, subtitle_highlight (40
 * each, the title block); item_1_title .. item_4_title (80), item_N_quote (800, a paragraph per line), item_N_name
 * (60), item_N_role (60). Testimonial N shows when it has a quote.
 * Image slots (images.{slot}): people, 0 to 4 of 600x600, the Nth for testimonial N.
 *
 * @var array $section A section of v1/mart/webpage (see hero_01.php)
 */
$texts    = $section['texts'] ?? [];
$pictures = array_values((array) ($section['images']['people'] ?? []));
$text     = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
$lines    = static fn (string $slot): array => array_values(array_filter(array_map('trim', preg_split('/\R/u', $text($slot))), static fn (string $line): bool => $line !== ''));
$items    = [];
foreach (range(1, 4) as $n) {
    if ($text("item_{$n}_quote") !== '') {
        $items[] = ['title' => $text("item_{$n}_title"), 'quote' => $lines("item_{$n}_quote"), 'name' => $text("item_{$n}_name"), 'role' => $text("item_{$n}_role"),
            'picture' => $pictures[$n - 1] ?? null];
    }
}
$many   = count($items) > 1;
// rewind rather than loop: Swiper's loop needs more slides than a business may have, and gets stuck with two
$config = ['slidesPerView' => 1, 'rewind' => $many, 'speed' => 600, 'autoplay' => $many ? ['delay' => 5000] : false]
    + ($many ? ['navigation' => ['nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev']] : []);
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="testimonials section light-background testimonials-01">
    <?= view('section_parts/title', ['title' => $text('title'), 'subtitle' => $text('subtitle'), 'subtitle_highlight' => $text('subtitle_highlight')]) ?>
    <?php if ($items !== []): ?>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="testimonials-slider swiper init-swiper">
                <script type="application/json" class="swiper-config"><?= json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
                <div class="swiper-wrapper">
                    <?php foreach ($items as $item): ?>
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="row">
                                    <div class="<?= $item['picture'] !== null ? 'col-lg-8' : 'col-12' ?>">
                                        <?php if ($item['title'] !== ''): ?>
                                            <h2><?= esc($item['title']) ?></h2>
                                        <?php endif ?>
                                        <?php foreach ($item['quote'] as $paragraph): ?>
                                            <p><?= esc($paragraph) ?></p>
                                        <?php endforeach ?>
                                        <?php if ($item['name'] !== '' || $item['role'] !== ''): ?>
                                            <div class="profile d-flex align-items-center">
                                                <?php if ($item['picture'] !== null): ?>
                                                    <img src="<?= esc($item['picture'], 'attr') ?>" class="profile-img" alt="">
                                                <?php endif ?>
                                                <div class="profile-info">
                                                    <?php if ($item['name'] !== ''): ?>
                                                        <h3><?= esc($item['name']) ?></h3>
                                                    <?php endif ?>
                                                    <?php if ($item['role'] !== ''): ?>
                                                        <span><?= esc($item['role']) ?></span>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                    <?php if ($item['picture'] !== null): ?>
                                        <div class="col-lg-4 d-none d-lg-block">
                                            <div class="featured-img-wrapper">
                                                <img src="<?= esc($item['picture'], 'attr') ?>" class="featured-img" alt="<?= esc($item['name'], 'attr') ?>">
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if ($many): ?>
                    <div class="swiper-navigation w-100 d-flex align-items-center justify-content-center">
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    <?php endif ?>
</section>
