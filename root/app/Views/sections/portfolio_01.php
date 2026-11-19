<?php
/**
 * Portfolio 01 (Config\WebpageDesigns::$designs['portfolio_01'] in otternova_api): up to six work cards in a
 * masonry grid, each a picture that opens full size, with filter buttons made from the cards' categories, and a
 * call-to-action box under them. Drawn by customizedPage.php for a section whose design_code is portfolio_01; the
 * styles are the theme's (.portfolio), the grid and filters main.js's (.isotope-layout).
 *
 * Text slots (texts.{slot}), all optional but item_1_title: title, subtitle, subtitle_highlight (40 each, the title
 * block); item_1_title .. item_6_title (60), item_N_text (200),
 * item_N_category (32, its filter), item_N_tag (24, a corner badge); cta_title (120), cta_text (400, keeps its line
 * breaks), cta_button_label (32, to the contact page). Card N shows when it has a title or the Nth picture; the
 * filters show when the cards have two categories or more.
 * Image slots (images.{slot}): items, 0 to 6 of 800x600, the Nth for card N.
 *
 * @var array  $section       A section of v1/mart/webpage (see hero_01.php)
 * @var string $business_slug The business, for the button's link
 */
$texts      = $section['texts'] ?? [];
$pictures   = array_values((array) ($section['images']['items'] ?? []));
$text       = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
$items      = [];
$categories = []; // category => its filter's class
foreach (range(1, 6) as $n) {
    $picture = $pictures[$n - 1] ?? null;
    if ($text("item_{$n}_title") === '' && $picture === null) {
        continue;
    }
    $category = $text("item_{$n}_category");
    if ($category !== '' && ! isset($categories[$category])) {
        $categories[$category] = 'filter-' . (count($categories) + 1);
    }
    $items[] = ['picture' => $picture, 'title' => $text("item_{$n}_title"), 'text' => $text("item_{$n}_text"), 'category' => $category, 'tag' => $text("item_{$n}_tag")];
}
$gallery = 'portfolio-' . $section['public_id'];
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="portfolio section portfolio-01">
    <?= view('section_parts/title', ['title' => $text('title'), 'subtitle' => $text('subtitle'), 'subtitle_highlight' => $text('subtitle_highlight')]) ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <?php if ($items !== []): ?>
            <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
                <?php if (count($categories) > 1): ?>
                    <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="200">
                        <li data-filter="*" class="filter-active"><?= esc(lang('Business.customized.filter-all')) ?></li>
                        <?php foreach ($categories as $category => $class): ?>
                            <li data-filter=".<?= $class ?>"><?= esc($category) ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
                <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="300">
                    <?php foreach ($items as $item): ?>
                        <div class="col-lg-4 col-md-6 portfolio-item isotope-item<?= $item['category'] !== '' ? ' ' . $categories[$item['category']] : '' ?>">
                            <div class="portfolio-card">
                                <?php if ($item['picture'] !== null): ?>
                                    <div class="card-image">
                                        <img src="<?= esc($item['picture'], 'attr') ?>" alt="<?= esc($item['title'], 'attr') ?>" class="img-fluid" loading="lazy">
                                        <div class="overlay">
                                            <a href="<?= esc($item['picture'], 'attr') ?>" class="glightbox icon-btn" data-gallery="<?= esc($gallery, 'attr') ?>" aria-label="<?= esc($item['title'], 'attr') ?>">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </a>
                                        </div>
                                        <?php if ($item['tag'] !== ''): ?>
                                            <div class="tag"><?= esc($item['tag']) ?></div>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?>
                                <div class="card-content">
                                    <?php if ($item['category'] !== '' || ($item['picture'] === null && $item['tag'] !== '')): ?>
                                        <div class="meta">
                                            <?php if ($item['category'] !== ''): ?>
                                                <span class="category"><?= esc($item['category']) ?></span>
                                            <?php endif ?>
                                            <?php if ($item['picture'] === null && $item['tag'] !== ''): ?>
                                                <span class="year"><?= esc($item['tag']) ?></span>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($item['title'] !== ''): ?>
                                        <h3><?= esc($item['title']) ?></h3>
                                    <?php endif ?>
                                    <?php if ($item['text'] !== ''): ?>
                                        <p><?= esc($item['text']) ?></p>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>
        <?php if ($text('cta_title') !== '' || $text('cta_text') !== '' || $text('cta_button_label') !== ''): ?>
            <div class="cta-box" data-aos="fade-up" data-aos-delay="400">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <?php if ($text('cta_title') !== ''): ?>
                            <h3><?= esc($text('cta_title')) ?></h3>
                        <?php endif ?>
                        <?php if ($text('cta_text') !== ''): ?>
                            <p><?= nl2br(esc($text('cta_text'))) ?></p>
                        <?php endif ?>
                    </div>
                    <?php if ($text('cta_button_label') !== ''): ?>
                        <div class="col-lg-4 text-lg-end">
                            <a href="<?= esc(base_url($business_slug . '/contact-us'), 'attr') ?>" class="btn-primary"><?= esc($text('cta_button_label')) ?></a>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</section>
