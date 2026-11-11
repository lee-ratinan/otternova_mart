<?php
/**
 * A business's main page as the system builds it (Business::systemHome()), from v1/mart/site
 *
 * @var array       $site       v1/mart/site: business, hero, theme, categories, stores, ...
 * @var array       $featured   best_sellers, trending, highest_rated: cards as Business::card() makes them
 * @var list<array> $hero_tiles up to three: {card, badge, accent}
 * @var list<array> $carousel   every featured card once
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$hero     = $site['hero'];
// a grey box in place of a missing cover, so every card keeps its shape
$noImage = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 3"><rect width="4" height="3" fill="#e9ecef"/></svg>');
$image   = static fn (array $card): string => esc($card['image'] ?? $noImage, 'attr');
$price   = static fn (array $card): string => esc($card['from'] ? lang('Business.from', [$card['price']]) : $card['price']);
$firstSection = $featured['best_sellers'] !== [] ? '#best-sellers' : '#cards';
$hasCards     = $carousel !== [];

// what the hero can truly say about the business, in place of the template's made-up promises
$indicators = [];
if ((int) $business['rating']['count'] > 0) {
    $indicators[] = ['bi-star-fill', number_format((float) $business['rating']['average'], 1) . ' · ' . lang('Business.reviews', [(int) $business['rating']['count']])];
}
if (($business['contact']['city'] ?? null) !== null) {
    $indicators[] = ['bi-geo-alt', $business['contact']['city']];
}
if (count($site['stores']) > 1) {
    $indicators[] = ['bi-shop', lang('Business.stores', [count($site['stores'])])];
}
if (($business['contact']['phone_number'] ?? null) !== null) {
    $indicators[] = ['bi-telephone', $business['contact']['phone_number']];
}
if ($business['audience']['age_restricted']) {
    $indicators[] = ['bi-person-exclamation', lang('Business.adults-only')];
}
?>
<?= $this->section('content') ?>
    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section light-background">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="<?= $hero_tiles === [] ? 'col-lg-8' : 'col-lg-5' ?>">
                        <div class="intro-content">
                            <?php if (($hero['badge'] ?? '') !== ''): ?>
                                <span class="badge-label"><?= esc($hero['badge']) ?></span>
                            <?php endif ?>
                            <h1 class="headline"><?= esc(($hero['headline'] ?? '') !== '' ? $hero['headline'] : $business['name']) ?></h1>
                            <?php $paragraph = ($hero['paragraph'] ?? '') !== '' ? $hero['paragraph'] : $business['description']; ?>
                            <?php if (($paragraph ?? '') !== ''): ?>
                                <p class="subtext"><?= nl2br(esc($paragraph)) ?></p>
                            <?php endif ?>
                            <?php if ($hasCards): ?>
                                <div class="action-group">
                                    <a href="<?= $firstSection ?>" class="btn-primary-action"><?= lang('Business.browse') ?></a>
                                </div>
                            <?php endif ?>
                            <?php if ($indicators !== []): ?>
                                <div class="trust-indicators">
                                    <?php foreach ($indicators as [$icon, $text]): ?>
                                        <div class="indicator">
                                            <i class="bi <?= $icon ?>"></i>
                                            <span><?= esc($text) ?></span>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <?php if ($hero_tiles !== []): ?>
                        <div class="col-lg-7">
                            <div class="row g-3">
                                <?php foreach ($hero_tiles as $i => $tile): ?>
                                    <?php $card = $tile['card']; ?>
                                    <?php if ($i < 2): ?>
                                        <div class="<?= count($hero_tiles) === 1 ? 'col-12' : 'col-md-6' ?>">
                                            <a href="<?= esc($card['url'], 'attr') ?>" class="product-tile d-block<?= $i === 1 ? ' featured' : '' ?>">
                                                <div class="tile-image">
                                                    <img src="<?= $image($card) ?>" class="img-fluid" alt="<?= esc($card['name'], 'attr') ?>">
                                                    <span class="tile-badge<?= $tile['accent'] ? ' accent' : '' ?>"><?= esc($tile['badge']) ?></span>
                                                </div>
                                                <div class="tile-info">
                                                    <h4><?= esc($card['name']) ?></h4>
                                                    <div class="tile-price">
                                                        <span class="current"><?= $price($card) ?></span>
                                                        <?php if ($card['compare'] !== null): ?>
                                                            <span class="original"><?= esc($card['compare']) ?></span>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            </a><!-- End Product Tile -->
                                        </div>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <a href="<?= esc($card['url'], 'attr') ?>" class="product-tile horizontal d-block">
                                                <div class="row g-0 align-items-center">
                                                    <div class="col-sm-4">
                                                        <div class="tile-image">
                                                            <img src="<?= $image($card) ?>" class="img-fluid" alt="<?= esc($card['name'], 'attr') ?>">
                                                            <span class="tile-badge<?= $tile['accent'] ? ' accent' : '' ?>"><?= esc($tile['badge']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="tile-info">
                                                            <h4><?= esc($card['name']) ?></h4>
                                                            <?php if ($card['category'] !== null): ?>
                                                                <p class="tile-desc"><?= esc($card['category']) ?></p>
                                                            <?php endif ?>
                                                            <div class="tile-price">
                                                                <span class="current"><?= $price($card) ?></span>
                                                                <?php if ($card['compare'] !== null): ?>
                                                                    <span class="original"><?= esc($card['compare']) ?></span>
                                                                <?php endif ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a><!-- End Product Tile -->
                                        </div>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endif ?>
                </div>

                <?php // the carousel only when it shows more than the tiles above ?>
                <?php if (count($carousel) > count($hero_tiles)): ?>
                    <div class="slider-section">
                        <div class="product-carousel swiper init-swiper">
                            <script type="application/json" class="swiper-config">
                                <?= json_encode([
                                    'loop'          => count($carousel) > 4,
                                    'speed'         => 500,
                                    'autoplay'      => ['delay' => 4000, 'disableOnInteraction' => true],
                                    'slidesPerView' => 1,
                                    'spaceBetween'  => 16,
                                    'breakpoints'   => [
                                        '576'  => ['slidesPerView' => 2, 'spaceBetween' => 16],
                                        '768'  => ['slidesPerView' => 3, 'spaceBetween' => 16],
                                        '1200' => ['slidesPerView' => 4, 'spaceBetween' => 24],
                                    ],
                                    'pagination'    => ['el' => '.swiper-pagination', 'type' => 'bullets', 'clickable' => true],
                                    'navigation'    => ['nextEl' => '.swiper-button-next', 'prevEl' => '.swiper-button-prev'],
                                ], JSON_UNESCAPED_SLASHES) ?>
                            </script>
                            <div class="swiper-wrapper">
                                <?php foreach ($carousel as $card): ?>
                                    <div class="swiper-slide">
                                        <a href="<?= esc($card['url'], 'attr') ?>" class="slide-card d-block">
                                            <div class="slide-card-image">
                                                <img src="<?= $image($card) ?>" class="img-fluid" loading="lazy" alt="<?= esc($card['name'], 'attr') ?>">
                                                <?php if ($card['label'] !== null): ?>
                                                    <span class="slide-badge accent"><?= esc($card['label']) ?></span>
                                                <?php endif ?>
                                            </div>
                                            <div class="slide-card-body">
                                                <h4><?= esc($card['name']) ?></h4>
                                                <?php if ($card['category'] !== null): ?>
                                                    <p><?= esc($card['category']) ?></p>
                                                <?php endif ?>
                                                <div class="slide-card-price">
                                                    <span class="price-now"><?= $price($card) ?></span>
                                                    <?php if ($card['compare'] !== null): ?>
                                                        <span class="price-was"><?= esc($card['compare']) ?></span>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        </a><!-- End Slide Card -->
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>
                <?php endif ?>

            </div>
        </section><!-- /Hero Section -->

        <?php if ($featured['best_sellers'] !== []): ?>
            <!-- Best Sellers Section -->
            <section id="best-sellers" class="best-sellers section">
                <div class="container section-title">
                    <h2><?= lang('Business.best-sellers') ?></h2>
                </div><!-- End Section Title -->
                <div class="container">
                    <div class="row g-4">
                        <?php foreach ($featured['best_sellers'] as $card): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-media">
                                        <a href="<?= esc($card['url'], 'attr') ?>">
                                            <img src="<?= $image($card) ?>" alt="<?= esc($card['name'], 'attr') ?>" class="img-fluid" loading="lazy">
                                        </a>
                                        <?php if ($card['discount'] !== null): ?>
                                            <div class="badge-label discount"><?= lang('Business.discount', [$card['discount']]) ?></div>
                                        <?php elseif ($card['label'] !== null): ?>
                                            <div class="badge-label"><?= esc($card['label']) ?></div>
                                        <?php endif ?>
                                    </div>
                                    <div class="product-body">
                                        <div class="product-meta">
                                            <span class="category-tag"><?= esc($card['category'] ?? '') ?></span>
                                            <?php if ($card['rating'] !== null): ?>
                                                <div class="rating-group">
                                                    <i class="bi bi-star-fill"></i>
                                                    <span><?= $card['rating'] ?></span>
                                                    <span class="count">(<?= $card['rating_count'] ?>)</span>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                        <h4 class="product-title"><a href="<?= esc($card['url'], 'attr') ?>"><?= esc($card['name']) ?></a></h4>
                                        <div class="product-footer">
                                            <div class="product-price">
                                                <?php if ($card['compare'] !== null): ?>
                                                    <span class="original"><?= esc($card['compare']) ?></span>
                                                    <span class="current"><?= $price($card) ?></span>
                                                <?php else: ?>
                                                    <?= $price($card) ?>
                                                <?php endif ?>
                                            </div>
                                            <a href="<?= esc($card['url'], 'attr') ?>" class="cart-btn" aria-label="<?= esc(lang('Business.view') . ' ' . $card['name'], 'attr') ?>"><i class="bi bi-bag-plus"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </section><!-- /Best Sellers Section -->
        <?php endif ?>

        <?php $tabs = array_filter(['trending' => 'bi-fire', 'highest_rated' => 'bi-award'], static fn (string $list): bool => $featured[$list] !== [], ARRAY_FILTER_USE_KEY); ?>
        <?php if ($tabs !== []): ?>
            <!-- Cards Section -->
            <section id="cards" class="cards section">
                <div class="container">
                    <div class="tab-nav-wrapper">
                        <ul class="nav nav-tabs filter-tabs" role="tablist">
                            <?php foreach (array_keys($tabs) as $i => $list): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link<?= $i === 0 ? ' active' : '' ?>" data-bs-toggle="tab" data-bs-target="#cards-tab-<?= $list ?>" type="button" role="tab">
                                        <i class="bi <?= $tabs[$list] ?>"></i> <?= lang('Business.' . str_replace('_', '-', $list)) ?>
                                    </button>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div><!-- End Filter Tabs -->
                    <div class="tab-content">
                        <?php foreach (array_keys($tabs) as $i => $list): ?>
                            <div class="tab-pane fade<?= $i === 0 ? ' show active' : '' ?>" id="cards-tab-<?= $list ?>" role="tabpanel">
                                <div class="row g-3">
                                    <?php foreach ($featured[$list] as $card): ?>
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <div class="product-card">
                                                <div class="product-thumb">
                                                    <a href="<?= esc($card['url'], 'attr') ?>">
                                                        <img src="<?= $image($card) ?>" alt="<?= esc($card['name'], 'attr') ?>" class="img-fluid">
                                                    </a>
                                                    <?php if ($card['discount'] !== null): ?>
                                                        <span class="status-badge sale-badge">-<?= $card['discount'] ?>%</span>
                                                    <?php elseif ($card['label'] !== null): ?>
                                                        <span class="status-badge new-badge"><?= esc($card['label']) ?></span>
                                                    <?php endif ?>
                                                </div>
                                                <div class="product-info">
                                                    <?php if ($card['rating'] !== null): ?>
                                                        <div class="product-meta">
                                                            <div class="rating">
                                                                <i class="bi bi-star-fill"></i>
                                                                <span class="rating-value"><?= $card['rating'] ?></span>
                                                                <span class="rating-count">(<?= $card['rating_count'] ?>)</span>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>
                                                    <h4 class="product-title"><a href="<?= esc($card['url'], 'attr') ?>"><?= esc($card['name']) ?></a></h4>
                                                    <div class="product-price">
                                                        <span class="current-price"><?= $price($card) ?></span>
                                                        <?php if ($card['compare'] !== null): ?>
                                                            <span class="original-price"><?= esc($card['compare']) ?></span>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Product Card -->
                                    <?php endforeach ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div><!-- End Tab Content -->
                </div>
            </section><!-- /Cards Section -->
        <?php endif ?>
    </main>
<?= $this->endSection() ?>