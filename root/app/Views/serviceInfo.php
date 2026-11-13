<?php
/**
 * A service's page (Business::serviceInfo()), from v1/mart/artifact
 *
 * @var array       $site        v1/mart/site
 * @var array       $artifact    v1/mart/artifact's artifact: name, category, rating (with breakdown), description, ...
 * @var string      $summary     the start of the description, as plain text
 * @var bool        $summary_cut whether $summary is only the start
 * @var list<array> $images      {public_id, url, variant_public_id}, the cover first
 * @var list<array> $options     {public_id, name, values: [{public_id, value}]}
 * @var list<array> $variants    as Business::serviceVariants() makes them; the first is chosen to begin with
 * @var list<array> $reviews     v1/mart/artifact's reviews, each with its date written out
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$chosen   = $variants[0] ?? null;
$rating   = (float) $artifact['rating']['average'];
$ratings  = (int) $artifact['rating']['count'];
$homeUrl  = base_url(rawurlencode($business['slug']));
$noImage  = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 3"><rect width="4" height="3" fill="#e9ecef"/></svg>');
// the variants a value belongs to tell the picker which value goes with which
$valueOf = static function (array $variant, string $optionId): ?string {
    return $variant['values'][$optionId] ?? null;
};
$stars = static function (float $average): string {
    $out = '';
    for ($star = 1; $star <= 5; $star++) {
        $icon = $average >= $star ? 'bi-star-fill' : ($average >= $star - 0.5 ? 'bi-star-half' : 'bi-star');
        $out .= '<i class="bi ' . $icon . '"></i>';
    }

    return $out;
};
?>
<?= $this->section('content') ?>
    <style>
        .product-details .option-group { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
        .product-details .option-btn { border: 1px solid color-mix(in srgb, var(--default-color), transparent 80%); background: var(--surface-color); color: var(--default-color); border-radius: 6px; padding: 6px 14px; font-size: 14px; }
        .product-details .option-btn:hover { border-color: var(--accent-color); }
        .product-details .option-btn.active { border-color: var(--accent-color); background: var(--accent-color); color: var(--contrast-color); }
        .product-details .variant-details { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 20%); margin-bottom: 20px; }
        .product-details .variant-details i { color: var(--accent-color); margin-right: 4px; }
        .product-details .avatar-initial { width: 44px; height: 44px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-weight: 600; flex: none; }
        .product-details .business-reply { border-left: 3px solid var(--accent-color); padding: 8px 12px; margin-top: 8px; background: color-mix(in srgb, var(--default-color), transparent 96%); }
        .product-details .main-image-container .discount-badge[hidden] { display: none; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= esc($artifact['name']) ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($homeUrl, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li class="current"><?= esc($artifact['name']) ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="product-details" class="product-details section">
        <div class="container">
            <div class="row g-4">
                <!-- Service Gallery -->
                <div class="col-lg-7">
                    <div class="image-showcase">
                        <div class="main-image-container">
                            <span class="discount-badge" id="service-discount"<?= ($chosen['discount'] ?? null) === null ? ' hidden' : '' ?>>-<?= (int) ($chosen['discount'] ?? 0) ?>%</span>
                            <?php $cover = $images[0]['url'] ?? $noImage; ?>
                            <img id="main-product-image" src="<?= esc($cover, 'attr') ?>" data-zoom="<?= esc($cover, 'attr') ?>" alt="<?= esc($artifact['name'], 'attr') ?>" class="img-fluid">
                            <div class="image-zoom-container"></div>
                            <?php if (count($images) > 1): ?>
                                <button class="image-nav-btn prev-image" type="button" aria-label="<?= esc(lang('Business.service.previous-picture'), 'attr') ?>"><i class="bi bi-chevron-left"></i></button>
                                <button class="image-nav-btn next-image" type="button" aria-label="<?= esc(lang('Business.service.next-picture'), 'attr') ?>"><i class="bi bi-chevron-right"></i></button>
                            <?php endif ?>
                        </div>
                        <?php if (count($images) > 1): ?>
                            <div class="thumb-strip">
                                <?php foreach ($images as $i => $image): ?>
                                    <div class="thumb-cell thumbnail-item<?= $i === 0 ? ' active' : '' ?>" data-image="<?= esc($image['url'], 'attr') ?>">
                                        <img src="<?= esc($image['url'], 'attr') ?>" alt="<?= esc(lang('Business.service.picture', [$i + 1]), 'attr') ?>" class="img-fluid">
                                    </div>
                                <?php endforeach ?>
                            </div><!-- End Thumb Strip -->
                        <?php endif ?>
                    </div>
                </div><!-- End Service Gallery -->

                <!-- Service Details -->
                <div class="col-lg-5">
                    <div class="product-detail-card">
                        <div class="detail-header">
                            <span class="type-badge"><?= esc($artifact['category']['name'] ?? lang('Business.service.options')) ?></span>
                            <span class="stock-indicator"><i class="bi bi-circle-fill"></i> <?= lang($chosen === null ? 'Business.service.unavailable' : 'Business.service.available') ?></span>
                        </div>
                        <h1 class="product-heading"><?= esc($artifact['name']) ?></h1>
                        <div class="review-summary">
                            <?php if ($ratings > 0): ?>
                                <div class="stars-inline"><?= $stars($rating) ?></div>
                                <span class="score-text"><?= number_format($rating, 1) ?></span>
                                <span class="divider-dot">·</span>
                                <a href="#product-details-tabs" class="reviews-anchor" data-open-tab="#product-details-tab-feedback"><?= lang('Business.service.ratings', [$ratings]) ?></a>
                            <?php else: ?>
                                <span class="score-text"><?= lang('Business.service.no-ratings') ?></span>
                            <?php endif ?>
                        </div>

                        <?php if ($chosen !== null): ?>
                            <div class="pricing-area">
                                <div class="price-row">
                                    <span class="price-now" id="service-price"><?= esc($chosen['price']) ?></span>
                                    <span class="price-was" id="service-compare"<?= $chosen['compare'] === null ? ' hidden' : '' ?>><?= esc($chosen['compare'] ?? '') ?></span>
                                </div>
                                <span class="save-tag" id="service-save"<?= $chosen['save'] === null ? ' hidden' : '' ?>><?= esc($chosen['save'] ?? '') ?></span>
                            </div>
                        <?php endif ?>
                        <?php if ($summary !== ''): ?>
                            <p class="summary-text">
                                <?= esc($summary) ?>
                                <?php if ($summary_cut): ?>
                                    <a href="#product-details-tabs" data-open-tab="#product-details-tab-desc"><?= lang('Business.service.read-more') ?></a>
                                <?php endif ?>
                            </p>
                        <?php endif ?>
                        <div class="separator"></div>

                        <?php if ($chosen !== null): ?>
                            <div class="variant-picker">
                                <?php foreach ($options as $option): ?>
                                    <?php $chosenValue = $valueOf($chosen, $option['public_id']); ?>
                                    <span class="picker-label"><?= esc($option['name']) ?>:
                                        <span class="chosen-variant" data-chosen-for="<?= esc($option['public_id'], 'attr') ?>"><?php foreach ($option['values'] as $value) { echo $value['public_id'] === $chosenValue ? esc($value['value']) : ''; } ?></span>
                                    </span>
                                    <div class="option-group">
                                        <?php foreach ($option['values'] as $value): ?>
                                            <button type="button" class="option-btn<?= $value['public_id'] === $chosenValue ? ' active' : '' ?>"
                                                    data-option="<?= esc($option['public_id'], 'attr') ?>" data-value="<?= esc($value['public_id'], 'attr') ?>"><?= esc($value['value']) ?></button>
                                        <?php endforeach ?>
                                    </div>
                                <?php endforeach ?>
                                <?php if ($options === [] && count($variants) > 1): ?>
                                    <span class="picker-label"><?= lang('Business.service.options') ?>:</span>
                                    <div class="option-group">
                                        <?php foreach ($variants as $i => $variant): ?>
                                            <button type="button" class="option-btn<?= $i === 0 ? ' active' : '' ?>" data-variant="<?= esc($variant['public_id'], 'attr') ?>"><?= esc($variant['label']) ?></button>
                                        <?php endforeach ?>
                                    </div>
                                <?php endif ?>
                                <div class="variant-details" id="service-details">
                                    <?php foreach ($chosen['details'] as $i => $detail): ?>
                                        <span class="me-3"><i class="bi <?= $i === 0 && count($chosen['details']) > 1 ? 'bi-clock' : 'bi-people' ?>"></i><?= esc($detail) ?></span>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class="action-row">
                                <a class="btn primary-action-btn" id="service-search" href="<?= esc($chosen['search_url'], 'attr') ?>">
                                    <i class="bi bi-search"></i> <span><?= esc($chosen['search_label']) ?></span>
                                </a>
                                <button class="btn wishlist-toggle" title="<?= esc(lang('Business.service.wishlist'), 'attr') ?>" type="button">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        <?php endif ?>
                    </div>
                </div><!-- End Service Details -->
            </div>

            <!-- Tabbed Content -->
            <div class="row mt-5" id="product-details-tabs">
                <div class="col-12">
                    <div class="info-tabs">
                        <ul class="tab-nav nav" role="tablist">
                            <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-details-tab-desc" type="button" aria-selected="true" role="tab"><?= lang('Business.service.description') ?></button></li>
                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-feedback" type="button" aria-selected="false" tabindex="-1" role="tab"><?= lang('Business.service.feedback', [$ratings]) ?></button></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Description Tab -->
                            <div class="tab-pane fade show active" id="product-details-tab-desc" role="tabpanel">
                                <div class="desc-content">
                                    <div class="row g-4">
                                        <div class="col">
                                            <h3><?= lang('Business.service.about') ?></h3>
                                            <?php if (trim((string) ($artifact['description'] ?? '')) !== ''): ?>
                                                <p><?= nl2br(esc($artifact['description'])) ?></p>
                                            <?php else: ?>
                                                <p class="text-muted"><?= lang('Business.service.no-description') ?></p>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Description Tab -->

                            <!-- Feedback Tab -->
                            <div class="tab-pane fade" id="product-details-tab-feedback" role="tabpanel">
                                <div class="feedback-content">
                                    <?php if ($ratings > 0): ?>
                                        <div class="row g-4 mb-4">
                                            <div class="col-lg-3 col-md-4">
                                                <div class="rating-overview">
                                                    <div class="big-number"><?= number_format($rating, 1) ?></div>
                                                    <div class="star-row"><?= $stars($rating) ?></div>
                                                    <span class="count-label"><?= lang('Business.service.based-on', [$ratings]) ?></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-9 col-md-8">
                                                <div class="distribution-chart">
                                                    <?php for ($star = 5; $star >= 1; $star--): ?>
                                                        <?php $count = (int) ($artifact['rating']['breakdown'][$star] ?? 0); ?>
                                                        <div class="dist-row">
                                                            <span class="dist-label"><?= $star ?> <i class="bi bi-star-fill"></i></span>
                                                            <div class="dist-track">
                                                                <div class="dist-fill" style="width:<?= round(100 * $count / $ratings) ?>%;"></div>
                                                            </div>
                                                            <span class="dist-count"><?= $count ?></span>
                                                        </div>
                                                    <?php endfor ?>
                                                </div>
                                            </div>
                                        </div><!-- End Rating Overview -->
                                    <?php endif ?>

                                    <div class="reviews-list">
                                        <?php foreach ($reviews as $review): ?>
                                            <article class="review-entry">
                                                <div class="entry-top">
                                                    <span class="avatar-initial" aria-hidden="true"><?= esc(mb_substr($review['reviewer_name'], 0, 1)) ?></span>
                                                    <div class="entry-meta">
                                                        <strong><?= esc($review['reviewer_name']) ?></strong>
                                                        <div class="meta-line">
                                                            <span class="inline-stars"><?= $stars((float) $review['rating']) ?></span>
                                                            <span class="entry-date"><?= esc($review['date']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5><?= esc($review['title']) ?></h5>
                                                <p><?= nl2br(esc($review['message'])) ?></p>
                                                <?php if ($review['reply'] !== null): ?>
                                                    <div class="business-reply">
                                                        <strong><?= lang('Business.service.reply', [esc($business['name'])]) ?></strong>
                                                        <p class="mb-0"><?= nl2br(esc($review['reply']['message'])) ?></p>
                                                    </div>
                                                <?php endif ?>
                                            </article><!-- End Review Entry -->
                                        <?php endforeach ?>
                                        <?php if ($reviews === []): ?>
                                            <p class="text-muted"><?= lang('Business.service.no-reviews') ?></p>
                                        <?php endif ?>
                                    </div><!-- End Reviews List -->
                                </div>
                            </div><!-- End Feedback Tab -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
    <script>
        // the variant picker: the chosen values find the variant, whose price, details and search link the page shows
        (function () {
            const variants = <?= json_encode($variants, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
            if (variants.length === 0) return;
            const byId = (id) => document.getElementById(id);
            let current = variants[0];
            let chosen = Object.assign({}, current.values);

            const show = (variant) => {
                current = variant;
                chosen = Object.assign({}, variant.values);
                byId('service-price').textContent = variant.price;
                byId('service-compare').textContent = variant.compare || '';
                byId('service-compare').hidden = variant.compare === null;
                byId('service-save').textContent = variant.save || '';
                byId('service-save').hidden = variant.save === null;
                byId('service-discount').textContent = variant.discount === null ? '' : '-' + variant.discount + '%';
                byId('service-discount').hidden = variant.discount === null;
                const details = byId('service-details');
                details.replaceChildren(...variant.details.map((text, i) => {
                    const span = document.createElement('span');
                    const icon = document.createElement('i');
                    span.className = 'me-3';
                    icon.className = 'bi ' + (i === 0 && variant.details.length > 1 ? 'bi-clock' : 'bi-people');
                    span.append(icon, text);
                    return span;
                }));
                const search = byId('service-search');
                search.href = variant.search_url;
                search.querySelector('span').textContent = variant.search_label;
                document.querySelectorAll('.option-btn[data-option]').forEach((button) => {
                    const active = chosen[button.dataset.option] === button.dataset.value;
                    button.classList.toggle('active', active);
                    if (active) {
                        document.querySelector('[data-chosen-for="' + button.dataset.option + '"]').textContent = button.textContent;
                    }
                });
                document.querySelectorAll('.option-btn[data-variant]').forEach((button) => {
                    button.classList.toggle('active', button.dataset.variant === variant.public_id);
                });
                // the variant's own picture, when it has one
                if (variant.image) {
                    const thumb = Array.from(document.querySelectorAll('.thumbnail-item')).find((item) => item.dataset.image === variant.image);
                    if (thumb) {
                        thumb.click();
                    } else {
                        byId('main-product-image').src = variant.image;
                    }
                }
            };

            document.querySelectorAll('.option-btn').forEach((button) => button.addEventListener('click', () => {
                if (button.dataset.variant) {
                    show(variants.find((variant) => variant.public_id === button.dataset.variant));
                    return;
                }
                const wanted = Object.assign({}, chosen, { [button.dataset.option]: button.dataset.value });
                const matches = (variant, values) => Object.keys(values).every((key) => variant.values[key] === values[key]);
                // the exact combination, else the first variant with the value just picked
                show(variants.find((variant) => matches(variant, wanted))
                    || variants.find((variant) => variant.values[button.dataset.option] === button.dataset.value)
                    || current);
            }));

            // "Read more" and the ratings link open their tab before the page scrolls to it
            document.querySelectorAll('[data-open-tab]').forEach((link) => link.addEventListener('click', () => {
                const tab = document.querySelector('[data-bs-target="' + link.dataset.openTab + '"]');
                if (tab && window.bootstrap) {
                    bootstrap.Tab.getOrCreateInstance(tab).show();
                }
            }));
        })();
    </script>
<?= $this->endSection() ?>
