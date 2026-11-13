<?php
/**
 * The customer's cart at a business (Business::cart())
 *
 * @var array       $site
 * @var list<array> $lines   as Business::cartView() makes them
 * @var array       $summary as Business::cartSummary() makes it
 * @var string|null $message what was just done
 * @var string|null $error   why it was not
 * @var array       $urls    home, update, remove, clear, checkout
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$noImage  = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><rect width="1" height="1" fill="#e9ecef"/></svg>');
$json     = static fn ($value): string => json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<?= $this->section('content') ?>
    <style>
        .cart .cart-item .remove-btn { top: 20px; transform: none; }
        .cart .cart-item .product-title a { color: inherit; }
        .cart .cart-item .when { display: block; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 30%); margin-top: 4px; }
        .cart .cart-item .fixed-qty { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 30%); }
        .cart .participants { margin-top: 14px; padding-top: 12px; border-top: 1px dashed color-mix(in srgb, var(--default-color), transparent 88%); }
        .cart .participants .participants-label { font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block; }
        .cart .participants .form-control.missing { border-color: color-mix(in srgb, #d97706, transparent 40%); }
        .cart .line-error { font-size: 13px; margin-top: 8px; }
        .cart .line-error[hidden], .cart .names-needed[hidden] { display: none; }
        .cart .summary-card .proceed-btn.disabled { opacity: .55; cursor: not-allowed; }
        .cart .summary-card .names-needed { font-size: 13px; color: #b45309; margin-top: 10px; }
        .cart .charges-note { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); margin: 10px 0 0; }
        .cart .empty-cart { text-align: center; padding: 48px 16px; }
        .cart .empty-cart i { font-size: 48px; color: color-mix(in srgb, var(--default-color), transparent 70%); }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.cart.title') ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($urls['home'], 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li class="current"><?= lang('Business.cart.title') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="cart" class="cart section">
        <div class="container">
            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
            <?php elseif ($message !== null): ?>
                <div class="alert alert-success" role="status"><?= esc($message) ?></div>
            <?php endif ?>

            <?php if ($lines === []): ?>
                <div class="empty-cart">
                    <i class="bi bi-bag"></i>
                    <p class="mt-3"><?= lang('Business.cart.empty') ?></p>
                    <a href="<?= esc($urls['home'], 'attr') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> <?= lang('Business.cart.browse') ?></a>
                </div>
            <?php else: ?>
            <div class="row gy-5">
                <div class="col-lg-8">
                    <div class="cart-table">
                        <div class="table-head d-none d-md-flex row align-items-center">
                            <div class="col-md-5"><?= lang('Business.cart.item') ?></div>
                            <div class="col-md-2 text-center"><?= lang('Business.cart.price') ?></div>
                            <div class="col-md-3 text-center"><?= lang('Business.cart.quantity') ?></div>
                            <div class="col-md-2 text-end"><?= lang('Business.cart.total') ?></div>
                        </div>

                        <?php foreach ($lines as $line): ?>
                            <div class="cart-item" data-line="<?= esc($line['id'], 'attr') ?>">
                                <form class="line-form" method="post" action="<?= esc($urls['update'], 'attr') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="line" value="<?= esc($line['id'], 'attr') ?>">
                                    <div class="row align-items-center g-3">
                                        <div class="col-md-5">
                                            <div class="product-info d-flex align-items-center gap-3">
                                                <div class="product-img">
                                                    <img src="<?= esc($line['image'] ?? $noImage, 'attr') ?>" alt="<?= esc($line['name'], 'attr') ?>" class="img-fluid" loading="lazy">
                                                </div>
                                                <div class="product-meta">
                                                    <h6 class="product-title"><a href="<?= esc($line['url'], 'attr') ?>"><?= esc($line['name']) ?></a></h6>
                                                    <?php if ($line['tags'] !== []): ?>
                                                        <div class="product-tags">
                                                            <?php foreach ($line['tags'] as $tag): ?>
                                                                <span class="tag-item"><?= esc($tag) ?></span>
                                                            <?php endforeach ?>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if ($line['when'] !== null): ?>
                                                        <span class="when"><i class="bi bi-calendar-event me-1"></i><?= esc($line['when']) ?></span>
                                                    <?php endif ?>
                                                    <?php if ($line['discount'] !== null): ?>
                                                        <span class="discount-badge"><?= lang('Business.cart.save', [$line['discount']]) ?></span>
                                                    <?php endif ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-6">
                                            <div class="price-col text-md-center">
                                                <span class="current-price"><?= esc($line['price']) ?></span>
                                                <?php if ($line['compare'] !== null): ?>
                                                    <span class="original-price"><?= esc($line['compare']) ?></span>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="qty-col d-flex justify-content-md-center">
                                                <?php if ($line['kind'] === 'A'): ?>
                                                    <span class="fixed-qty"><?= lang('Business.cart.one-person') ?></span>
                                                <?php else: ?>
                                                    <div class="quantity-selector">
                                                        <button class="quantity-btn decrease" type="button" aria-label="<?= esc(lang('Business.product.decrease'), 'attr') ?>"><i class="bi bi-dash"></i></button>
                                                        <input type="number" class="quantity-input" name="quantity" value="<?= $line['quantity'] ?>" min="1" max="<?= $line['max'] ?>" aria-label="<?= esc(lang('Business.cart.quantity'), 'attr') ?>">
                                                        <button class="quantity-btn increase" type="button" aria-label="<?= esc(lang('Business.product.increase'), 'attr') ?>"><i class="bi bi-plus"></i></button>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-6">
                                            <div class="total-col text-md-end">
                                                <span class="row-total"><?= esc($line['total']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($line['participants'] !== null): ?>
                                        <div class="participants">
                                            <span class="participants-label"><?= lang('Business.cart.participants') ?></span>
                                            <div class="row g-2 participant-list">
                                                <?php foreach ($line['participants'] as $i => $name): ?>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control form-control-sm<?= $name === '' ? ' missing' : '' ?>" name="participants[]" maxlength="100" autocomplete="name"
                                                               value="<?= esc($name, 'attr') ?>" placeholder="<?= esc(lang('Business.cart.participant', [$i + 1]), 'attr') ?>" aria-label="<?= esc(lang('Business.cart.participant', [$i + 1]), 'attr') ?>">
                                                    </div>
                                                <?php endforeach ?>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <div class="line-error text-danger" role="alert"<?= isset($problems[$line['id']]) ? '' : ' hidden' ?>><?= esc($problems[$line['id']] ?? '') ?></div>
                                    <noscript><button type="submit" class="btn btn-sm btn-outline-secondary mt-2"><?= lang('Business.cart.update') ?></button></noscript>
                                </form>
                                <form class="remove-form" method="post" action="<?= esc($urls['remove'], 'attr') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="line" value="<?= esc($line['id'], 'attr') ?>">
                                    <button class="remove-btn" type="submit" aria-label="<?= esc(lang('Business.cart.remove'), 'attr') ?>" title="<?= esc(lang('Business.cart.remove'), 'attr') ?>">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div><!-- End Cart Item -->
                        <?php endforeach ?>
                    </div>

                    <div class="cart-actions d-flex justify-content-between align-items-center">
                        <a href="<?= esc($urls['home'], 'attr') ?>" class="action-link">
                            <i class="bi bi-arrow-left"></i> <?= lang('Business.cart.browse') ?>
                        </a>
                        <form method="post" action="<?= esc($urls['clear'], 'attr') ?>" id="cart-clear">
                            <?= csrf_field() ?>
                            <button class="action-clear" type="submit">
                                <i class="bi bi-trash3"></i> <?= lang('Business.cart.clear') ?>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card">
                        <div class="summary-header">
                            <h5><?= lang('Business.cart.summary') ?></h5>
                            <span class="items-count" id="cart-items"><?= esc($summary['items_label']) ?></span>
                        </div>
                        <div class="summary-body">
                            <div class="summary-lines">
                                <div class="summary-row">
                                    <span class="label-text"><?= lang('Business.cart.subtotal') ?></span>
                                    <span class="value-text" id="cart-subtotal"><?= esc($summary['subtotal']) ?></span>
                                </div>
                            </div>
                            <div class="summary-total">
                                <span><?= lang('Business.cart.grand-total') ?></span>
                                <span class="total-value" id="cart-total"><?= esc($summary['subtotal']) ?></span>
                            </div>
                            <p class="charges-note"><?= lang('Business.cart.charges-note') ?></p>
                            <!-- the whole cart is checked again before the checkout opens -->
                            <form method="post" action="<?= esc($urls['checkout'], 'attr') ?>">
                                <?= csrf_field() ?>
                                <button type="submit" id="cart-proceed" class="btn proceed-btn w-100<?= $summary['ready'] ? '' : ' disabled' ?>"<?= $summary['ready'] ? '' : ' aria-disabled="true"' ?>>
                                    <?= lang('Business.cart.proceed') ?> <i class="bi bi-arrow-right"></i>
                                </button>
                            </form>
                            <p class="names-needed" id="cart-names-needed"<?= $summary['missing'] > 0 ? '' : ' hidden' ?>><?= esc($summary['missing_label']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>
        </div>
    </section>
    <?php if ($lines !== []): ?>
    <script>
        // each change is saved as it is made: the quantity, checked with the shop when it goes up, and the names
        (function () {
            const urls = <?= $json($urls) ?>;
            const text = <?= $json(['participant' => lang('Business.cart.participant', ['{n}']), 'confirm' => lang('Business.cart.clear-confirm'), 'failed' => lang('Business.cart.try-again')]) ?>;
            const byId = (id) => document.getElementById(id);

            // every answer also redraws the header's cart
            const send = (form) => fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.json().catch(() => ({ ok: false, message: text.failed })))
                .catch(() => ({ ok: false, message: text.failed }))
                .then((answer) => {
                    if (answer.summary) document.dispatchEvent(new CustomEvent('cart:changed', { detail: answer }));
                    return answer;
                });

            const showSummary = (summary) => {
                if (!summary) return;
                if (summary.lines === 0) {
                    window.location.href = urls.home + '/cart';
                    return;
                }
                byId('cart-items').textContent = summary.items_label;
                byId('cart-subtotal').textContent = summary.subtotal;
                byId('cart-total').textContent = summary.subtotal;
                const proceed = byId('cart-proceed');
                proceed.classList.toggle('disabled', !summary.ready);
                if (summary.ready) proceed.removeAttribute('aria-disabled'); else proceed.setAttribute('aria-disabled', 'true');
                byId('cart-names-needed').textContent = summary.missing_label;
                byId('cart-names-needed').hidden = summary.missing === 0;
            };

            const showLine = (item, line) => {
                const quantity = item.querySelector('input[name="quantity"]');
                if (quantity) {
                    quantity.value = line.quantity;
                    quantity.max = line.max;
                }
                item.querySelector('.row-total').textContent = line.total;
                const list = item.querySelector('.participant-list');
                if (!list || !line.participants) return;
                const inputs = list.querySelectorAll('input');
                // rebuilt only when the seats change, so a name being typed is not lost
                if (inputs.length !== line.participants.length) {
                    list.replaceChildren(...line.participants.map((name, i) => {
                        const column = document.createElement('div');
                        const input = document.createElement('input');
                        const label = text.participant.replace('{n}', i + 1);
                        column.className = 'col-sm-6';
                        Object.assign(input, { type: 'text', name: 'participants[]', maxLength: 100, autocomplete: 'name', value: name, placeholder: label });
                        input.className = 'form-control form-control-sm';
                        input.setAttribute('aria-label', label);
                        column.append(input);
                        return column;
                    }));
                }
                list.querySelectorAll('input').forEach((input) => input.classList.toggle('missing', input.value.trim() === ''));
            };

            document.querySelectorAll('.cart-item').forEach((item) => {
                const form = item.querySelector('.line-form');
                const error = item.querySelector('.line-error');
                let timer = null;
                const save = () => {
                    clearTimeout(timer);
                    send(form).then((answer) => {
                        if (answer.line) showLine(item, answer.line);
                        showSummary(answer.summary);
                        error.textContent = answer.ok ? '' : answer.message;
                        error.hidden = answer.ok;
                    });
                };
                // the +/- buttons change the number after this runs, and several clicks make one save
                const later = () => { clearTimeout(timer); timer = setTimeout(save, 500); };
                form.addEventListener('submit', (event) => { event.preventDefault(); save(); });
                form.querySelectorAll('.quantity-btn').forEach((button) => button.addEventListener('click', later));
                form.addEventListener('change', (event) => {
                    if (event.target.name === 'quantity') later(); else save();
                });
                form.addEventListener('input', (event) => {
                    if (event.target.name === 'participants[]') event.target.classList.toggle('missing', event.target.value.trim() === '');
                });

                const remove = item.querySelector('.remove-form');
                remove.addEventListener('submit', (event) => {
                    event.preventDefault();
                    send(remove).then((answer) => {
                        if (answer.ok) {
                            item.remove();
                        } else {
                            error.textContent = answer.message;
                            error.hidden = false;
                        }
                        showSummary(answer.summary);
                    });
                });
            });

            // a line removed from the header's cart goes from this page too
            document.addEventListener('cart:changed', (event) => {
                if (event.detail.source !== 'flyout' || !Array.isArray(event.detail.lines)) return;
                const kept = new Set(event.detail.lines.map((line) => line.id));
                document.querySelectorAll('.cart-item').forEach((item) => { if (!kept.has(item.dataset.line)) item.remove(); });
                showSummary(event.detail.summary);
            });

            byId('cart-clear').addEventListener('submit', (event) => {
                if (!window.confirm(text.confirm)) event.preventDefault();
            });

            // until every participant is named, the checkout button points at the first name missing
            byId('cart-proceed').addEventListener('click', (event) => {
                if (!event.currentTarget.classList.contains('disabled')) return;
                event.preventDefault();
                const missing = document.querySelector('.participant-list input.missing');
                if (missing) missing.focus();
            });
        })();
    </script>
    <?php endif ?>
<?= $this->endSection() ?>
