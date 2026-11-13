<?php
/**
 * A product's page (Business::productInfo()), from v1/mart/artifact
 *
 * @var array       $site            v1/mart/site
 * @var array       $artifact        v1/mart/artifact's artifact: name, category, rating (with breakdown), description, ...
 * @var string      $summary         the start of the description, as plain text
 * @var bool        $summary_cut     whether $summary is only the start
 * @var list<array> $images          {public_id, url, variant_public_id}, the cover first
 * @var list<array> $options         {public_id, name, values: [{public_id, value, color_code}]}
 * @var list<array> $variants        as Business::productVariants() makes them; the first in stock is chosen to begin with
 * @var list<array> $reviews         v1/mart/artifact's reviews, each with its date written out
 * @var string      $add_to_cart_url POST /{business}/add-to-cart
 * @var string      $checkout_url    /{business}/checkout
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$inStock  = array_values(array_filter($variants, static fn (array $variant): bool => $variant['stock'] !== 0));
$chosen   = $inStock[0] ?? $variants[0] ?? null;
$rating   = (float) $artifact['rating']['average'];
$ratings  = (int) $artifact['rating']['count'];
$homeUrl  = base_url(rawurlencode($business['slug']));
$cartUrl  = base_url(rawurlencode($business['slug']) . '/cart');
$noImage  = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 3"><rect width="4" height="3" fill="#e9ecef"/></svg>');
$valueOf  = static function (array $variant, string $optionId): ?string {
    return $variant['values'][$optionId] ?? null;
};
// an option is shown as colour dots when every value of it has a colour
$isColor = static function (array $option): bool {
    foreach ($option['values'] as $value) {
        if (! preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($value['color_code'] ?? ''))) {
            return false;
        }
    }

    return $option['values'] !== [];
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
        .product-details .variant-picker .color-dots { margin-bottom: 14px; }
        .product-details .product-detail-card .detail-header .stock-indicator.low { color: #d97706; }
        .product-details .product-detail-card .detail-header .stock-indicator.out { color: #dc2626; }
        .product-details .units-left[hidden], .product-details .divider-dot[hidden] { display: none; }
        .product-details .avatar-initial { width: 44px; height: 44px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-weight: 600; flex: none; }
        .product-details .business-reply { border-left: 3px solid var(--accent-color); padding: 8px 12px; margin-top: 8px; background: color-mix(in srgb, var(--default-color), transparent 96%); }
        .product-details .main-image-container .discount-badge[hidden] { display: none; }
        .product-details .cart-message { font-size: 14px; margin-top: 12px; }
        .product-details .cart-message[hidden] { display: none; }
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
                <!-- Product Gallery -->
                <div class="col-lg-7">
                    <div class="image-showcase">
                        <div class="main-image-container">
                            <span class="discount-badge" id="product-discount"<?= ($chosen['discount'] ?? null) === null ? ' hidden' : '' ?>>-<?= (int) ($chosen['discount'] ?? 0) ?>%</span>
                            <?php $cover = ($chosen['image'] ?? null) ?? $images[0]['url'] ?? $noImage; ?>
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
                                    <div class="thumb-cell thumbnail-item<?= $image['url'] === $cover ? ' active' : '' ?>" data-image="<?= esc($image['url'], 'attr') ?>">
                                        <img src="<?= esc($image['url'], 'attr') ?>" alt="<?= esc(lang('Business.service.picture', [$i + 1]), 'attr') ?>" class="img-fluid">
                                    </div>
                                <?php endforeach ?>
                            </div><!-- End Thumb Strip -->
                        <?php endif ?>
                    </div>
                </div><!-- End Product Gallery -->

                <!-- Product Details -->
                <div class="col-lg-5">
                    <div class="product-detail-card">
                        <div class="detail-header">
                            <span class="type-badge"><?= esc($artifact['category']['name'] ?? lang('Business.service.options')) ?></span>
                            <?php if ($chosen === null): ?>
                                <span class="stock-indicator out"><i class="bi bi-circle-fill"></i> <?= lang('Business.product.unavailable') ?></span>
                            <?php else: ?>
                                <span class="stock-indicator <?= esc($chosen['status_class'], 'attr') ?>" id="product-status"><i class="bi bi-circle-fill"></i> <span><?= esc($chosen['status']) ?></span></span>
                            <?php endif ?>
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
                            <?php $remaining = ($chosen['stock'] ?? null) !== null && $chosen['stock'] > 0; ?>
                            <span class="divider-dot" id="product-remaining-dot"<?= $remaining ? '' : ' hidden' ?>>·</span>
                            <span class="units-left" id="product-remaining"<?= $remaining ? '' : ' hidden' ?>><?= $remaining ? lang('Business.product.remaining', [$chosen['stock']]) : '' ?></span>
                        </div>

                        <?php if ($chosen !== null): ?>
                            <div class="pricing-area">
                                <div class="price-row">
                                    <span class="price-now" id="product-price"><?= esc($chosen['price']) ?></span>
                                    <span class="price-was" id="product-compare"<?= $chosen['compare'] === null ? ' hidden' : '' ?>><?= esc($chosen['compare'] ?? '') ?></span>
                                </div>
                                <span class="save-tag" id="product-save"<?= $chosen['save'] === null ? ' hidden' : '' ?>><?= esc($chosen['save'] ?? '') ?></span>
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
                                    <?php if ($isColor($option)): ?>
                                        <div class="color-dots">
                                            <?php foreach ($option['values'] as $value): ?>
                                                <button type="button" class="dot<?= $value['public_id'] === $chosenValue ? ' active' : '' ?>" style="background:<?= esc($value['color_code'], 'attr') ?>;"
                                                        title="<?= esc($value['value'], 'attr') ?>" aria-label="<?= esc($value['value'], 'attr') ?>" data-label="<?= esc($value['value'], 'attr') ?>"
                                                        data-option="<?= esc($option['public_id'], 'attr') ?>" data-value="<?= esc($value['public_id'], 'attr') ?>"><i class="bi bi-check2"></i></button>
                                            <?php endforeach ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="option-group">
                                            <?php foreach ($option['values'] as $value): ?>
                                                <button type="button" class="option-btn<?= $value['public_id'] === $chosenValue ? ' active' : '' ?>" data-label="<?= esc($value['value'], 'attr') ?>"
                                                        data-option="<?= esc($option['public_id'], 'attr') ?>" data-value="<?= esc($value['public_id'], 'attr') ?>"><?= esc($value['value']) ?></button>
                                            <?php endforeach ?>
                                        </div>
                                    <?php endif ?>
                                <?php endforeach ?>
                                <?php if ($options === [] && count($variants) > 1): ?>
                                    <span class="picker-label"><?= lang('Business.service.options') ?>:</span>
                                    <div class="option-group">
                                        <?php foreach ($variants as $variant): ?>
                                            <button type="button" class="option-btn<?= $variant['public_id'] === $chosen['public_id'] ? ' active' : '' ?>" data-variant="<?= esc($variant['public_id'], 'attr') ?>"><?= esc($variant['label']) ?></button>
                                        <?php endforeach ?>
                                    </div>
                                <?php endif ?>
                            </div>
                            <form id="product-cart-form" action="<?= esc($add_to_cart_url, 'attr') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="artifact" value="<?= esc($artifact['public_id'], 'attr') ?>">
                                <input type="hidden" name="variant" id="product-variant" value="<?= esc($chosen['public_id'], 'attr') ?>">
                                <div class="action-row">
                                    <div class="quantity-selector">
                                        <button class="quantity-btn decrease" type="button" aria-label="<?= esc(lang('Business.product.decrease'), 'attr') ?>"><i class="bi bi-dash"></i></button>
                                        <input type="number" class="quantity-input" name="quantity" id="product-quantity" value="1" min="1" max="<?= max(1, (int) $chosen['max']) ?>" aria-label="<?= esc(lang('Business.product.quantity'), 'attr') ?>">
                                        <button class="quantity-btn increase" type="button" aria-label="<?= esc(lang('Business.product.increase'), 'attr') ?>"><i class="bi bi-plus"></i></button>
                                    </div>
                                    <button class="btn primary-action-btn" type="submit" name="intent" value="cart"<?= $chosen['stock'] === 0 ? ' disabled' : '' ?>>
                                        <i class="bi bi-bag-plus"></i> <?= lang('Business.product.add-to-cart') ?>
                                    </button>
                                    <button class="btn wishlist-toggle" title="<?= esc(lang('Business.service.wishlist'), 'attr') ?>" type="button">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>
                                <button class="btn checkout-now-btn" type="submit" name="intent" value="buy"<?= $chosen['stock'] === 0 ? ' disabled' : '' ?>>
                                    <i class="bi bi-lightning-charge"></i> <?= lang('Business.product.buy-now') ?>
                                </button>
                                <div class="cart-message alert alert-success" id="product-cart-added" role="status" hidden>
                                    <?= lang('Business.product.added') ?> <a href="<?= esc($cartUrl, 'attr') ?>"><?= lang('Business.product.view-cart') ?></a>
                                </div>
                                <div class="cart-message alert alert-danger" id="product-cart-failed" role="alert" hidden><?= lang('Business.product.add-failed') ?></div>
                            </form>
                        <?php endif ?>
                    </div>
                </div><!-- End Product Details -->
            </div>

            <!-- Tabbed Content -->
            <div class="row mt-5" id="product-details-tabs">
                <div class="col-12">
                    <div class="info-tabs">
                        <ul class="tab-nav nav" role="tablist">
                            <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-details-tab-desc" type="button" aria-selected="true" role="tab"><?= lang('Business.service.description') ?></button></li>
                            <?php if ($chosen !== null): ?>
                                <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-specs" type="button" aria-selected="false" tabindex="-1" role="tab"><?= lang('Business.product.specifications') ?></button></li>
                            <?php endif ?>
                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-feedback" type="button" aria-selected="false" tabindex="-1" role="tab"><?= lang('Business.service.feedback', [$ratings]) ?></button></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Description Tab -->
                            <div class="tab-pane fade show active" id="product-details-tab-desc" role="tabpanel">
                                <div class="desc-content">
                                    <div class="row g-4">
                                        <div class="col">
                                            <h3><?= lang('Business.product.about') ?></h3>
                                            <?php if (trim((string) ($artifact['description'] ?? '')) !== ''): ?>
                                                <p><?= nl2br(esc($artifact['description'])) ?></p>
                                            <?php else: ?>
                                                <p class="text-muted"><?= lang('Business.service.no-description') ?></p>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Description Tab -->

                            <?php if ($chosen !== null): ?>
                                <!-- Specifications Tab -->
                                <div class="tab-pane fade" id="product-details-tab-specs" role="tabpanel">
                                    <div class="specs-content">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="spec-block">
                                                    <h4><?= esc($artifact['name']) ?></h4>
                                                    <table class="data-table">
                                                        <tbody>
                                                        <?php foreach ($options as $option): ?>
                                                            <tr>
                                                                <td><?= esc($option['name']) ?></td>
                                                                <td data-chosen-for="<?= esc($option['public_id'], 'attr') ?>"><?php foreach ($option['values'] as $value) { echo $value['public_id'] === $valueOf($chosen, $option['public_id']) ? esc($value['value']) : ''; } ?></td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                        <tr id="product-spec-sku"<?= $chosen['sku'] === null ? ' hidden' : '' ?>>
                                                            <td><?= lang('Business.product.sku') ?></td>
                                                            <td><?= esc($chosen['sku'] ?? '') ?></td>
                                                        </tr>
                                                        <tr id="product-spec-weight"<?= $chosen['weight'] === null ? ' hidden' : '' ?>>
                                                            <td><?= lang('Business.product.weight') ?></td>
                                                            <td><?= esc($chosen['weight'] ?? '') ?></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <p class="text-muted" id="product-no-specs"<?= $options === [] && $chosen['sku'] === null && $chosen['weight'] === null ? '' : ' hidden' ?>><?= lang('Business.product.no-specs') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- End Specifications Tab -->
                            <?php endif ?>

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
        // the variant picker: the chosen values find the variant, whose price, stock and specifications the page shows
        (function () {
            const variants = <?= json_encode($variants, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
            const remainingText = <?= json_encode(lang('Business.product.remaining', ['{n}']), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
            const checkoutUrl = <?= json_encode($checkout_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?>;
            if (variants.length === 0) return;
            const byId = (id) => document.getElementById(id);
            const quantity = byId('product-quantity');
            let current = variants.find((variant) => variant.public_id === byId('product-variant').value) || variants[0];
            let chosen = Object.assign({}, current.values);

            const show = (variant) => {
                current = variant;
                chosen = Object.assign({}, variant.values);
                byId('product-variant').value = variant.public_id;
                byId('product-price').textContent = variant.price;
                byId('product-compare').textContent = variant.compare || '';
                byId('product-compare').hidden = variant.compare === null;
                byId('product-save').textContent = variant.save || '';
                byId('product-save').hidden = variant.save === null;
                byId('product-discount').textContent = variant.discount === null ? '' : '-' + variant.discount + '%';
                byId('product-discount').hidden = variant.discount === null;

                const status = byId('product-status');
                status.className = 'stock-indicator ' + variant.status_class;
                status.querySelector('span').textContent = variant.status;
                const remaining = variant.stock !== null && variant.stock > 0;
                byId('product-remaining').textContent = remaining ? remainingText.replace('{n}', variant.stock) : '';
                byId('product-remaining').hidden = !remaining;
                byId('product-remaining-dot').hidden = !remaining;

                quantity.max = Math.max(1, variant.max);
                quantity.value = Math.min(Math.max(1, parseInt(quantity.value, 10) || 1), Math.max(1, variant.max));
                document.querySelectorAll('#product-cart-form [type="submit"]').forEach((button) => { button.disabled = variant.stock === 0; });
                byId('product-cart-added').hidden = true;
                byId('product-cart-failed').hidden = true;

                const sku = byId('product-spec-sku');
                sku.hidden = variant.sku === null;
                sku.cells[1].textContent = variant.sku || '';
                const weight = byId('product-spec-weight');
                weight.hidden = variant.weight === null;
                weight.cells[1].textContent = variant.weight || '';
                byId('product-no-specs').hidden = !(Object.keys(variant.values).length === 0 && variant.sku === null && variant.weight === null);

                document.querySelectorAll('[data-option][data-value]').forEach((button) => {
                    const active = chosen[button.dataset.option] === button.dataset.value;
                    button.classList.toggle('active', active);
                    if (active) {
                        document.querySelectorAll('[data-chosen-for="' + button.dataset.option + '"]').forEach((label) => { label.textContent = button.dataset.label; });
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

            document.querySelectorAll('[data-option][data-value], .option-btn[data-variant]').forEach((button) => button.addEventListener('click', () => {
                if (button.dataset.variant) {
                    show(variants.find((variant) => variant.public_id === button.dataset.variant));
                    return;
                }
                const wanted = Object.assign({}, chosen, { [button.dataset.option]: button.dataset.value });
                const matches = (variant, values) => Object.keys(values).every((key) => variant.values[key] === values[key]);
                // the exact combination, else the first variant with the value just picked, one in stock before one that is not
                const withValue = variants.filter((variant) => variant.values[button.dataset.option] === button.dataset.value);
                show(variants.find((variant) => matches(variant, wanted))
                    || withValue.find((variant) => variant.stock !== 0)
                    || withValue[0]
                    || current);
            }));

            // add to cart, or buy now: put it in the cart and go to the checkout
            const form = byId('product-cart-form');
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const buyNow = event.submitter && event.submitter.value === 'buy';
                const buttons = form.querySelectorAll('[type="submit"]');
                buttons.forEach((button) => { button.disabled = true; });
                byId('product-cart-added').hidden = true;
                byId('product-cart-failed').hidden = true;
                fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then((response) => response.json().then((answer) => {
                        if (answer.summary) document.dispatchEvent(new CustomEvent('cart:changed', { detail: answer }));
                        if (!response.ok) throw new Error(String(response.status));
                        if (buyNow) {
                            window.location.href = checkoutUrl;
                            return;
                        }
                        byId('product-cart-added').hidden = false;
                    }))
                    .catch(() => { byId('product-cart-failed').hidden = false; })
                    .finally(() => { buttons.forEach((button) => { button.disabled = current.stock === 0; }); });
            });

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
