<?php
/**
 * The checkout of a business (Business::checkout()): the customer's details, then the delivery (only when the cart
 * has products), then the discount code and the payment method, beside the order's summary. Every change is priced
 * again by checkout-quote; the order is placed by checkout-confirm.
 *
 * @var array        $site
 * @var array        $state    choice, customer, delivery: what the customer has entered so far
 * @var array        $checkout as Business::checkoutView() makes it
 * @var array        $errors   field => message, from the last try to place the order
 * @var string|null  $error    why the order could not be placed
 * @var list<string> $notices  prices that changed since the items were put in the cart
 * @var array        $urls     cart, quote, confirm
 */
$this->extend('_layout_otternova');

$business  = $site['business'];
$provinces = listSupportedSubdivisions((string) $business['country_code']);
$noImage   = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"><rect width="1" height="1" fill="#e9ecef"/></svg>');
$json      = static fn ($value): string => json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$value     = static fn (string $group, string $field): string => esc($state[$group][$field] ?? '', 'attr');
$invalid   = static fn (string $field): string => isset($errors[$field]) ? ' is-invalid' : '';
$feedback  = static fn (string $field): string => isset($errors[$field]) ? '<div class="invalid-feedback">' . esc($errors[$field]) . '</div>' : '';
$steps     = array_values(array_filter([
    ['contact', lang('Business.checkout.step-contact')],
    $checkout['needs_shipping'] ? ['delivery', lang('Business.checkout.step-delivery')] : null,
    ['payment', lang('Business.checkout.step-payment')],
]));
// the step to open on: the first with an error from the last try
$firstError = null;
foreach (array_keys($errors) as $field) {
    $step       = str_starts_with($field, 'customer.') ? 'contact' : (in_array($field, ['shipping', 'pickup_point'], true) || str_starts_with($field, 'address.') ? 'delivery' : 'payment');
    $firstError = $firstError === null || array_search($step, array_column($steps, 0), true) < array_search($firstError, array_column($steps, 0), true) ? $step : $firstError;
}
?>
<?= $this->section('content') ?>
    <style>
        .checkout .form-section[hidden] { display: none; }
        .checkout .form-section + .form-section { margin-top: 0; }
        .checkout .step-indicator .step-pill { cursor: default; }
        .checkout .step-indicator .step-pill.done { cursor: pointer; }
        .checkout .step-actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; }
        .checkout .step-actions .btn-next { margin-left: auto; }
        .checkout .option-list { display: grid; gap: 10px; }
        .checkout .option { display: flex; gap: 12px; align-items: flex-start; padding: 14px 16px; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); border-radius: 10px; cursor: pointer; margin: 0; }
        .checkout .option:has(input:checked) { border-color: var(--accent-color); background: color-mix(in srgb, var(--accent-color), transparent 95%); }
        .checkout .option.unavailable { opacity: .55; cursor: not-allowed; }
        .checkout .option input { margin-top: 4px; accent-color: var(--accent-color); }
        .checkout .option .option-body { flex: 1; min-width: 0; }
        .checkout .option .option-name { font-weight: 600; }
        .checkout .option .option-note { display: block; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .checkout .option .option-fee { font-weight: 600; white-space: nowrap; }
        .checkout .delivery-fields { margin-top: 18px; }
        .checkout .delivery-fields[hidden] { display: none; }
        .checkout .field-error { font-size: 13px; color: var(--bs-danger, #dc3545); margin-top: 8px; }
        .checkout .field-error[hidden] { display: none; }
        .checkout .discount-applied { font-size: 13px; color: #15803d; margin-top: 8px; }
        .checkout .discount-applied[hidden] { display: none; }
        .checkout .order-summary-card .product-row .when { display: block; font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .checkout .order-summary-card .cost-line[hidden] { display: none !important; }
        .checkout .order-summary-card .tax-note { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); margin: 8px 0 0; text-align: right; }
        .checkout .order-summary-card .tax-note[hidden] { display: none; }
        .checkout .summary-busy { opacity: .6; transition: opacity .2s; }
        .checkout .btn-accent { background: var(--accent-color); color: var(--contrast-color); border: 0; padding: 10px 22px; font-weight: 500; }
        .checkout .btn-accent:hover, .checkout .btn-accent:focus { background: color-mix(in srgb, var(--accent-color), black 10%); color: var(--contrast-color); }
        .checkout .btn-accent:disabled { opacity: .6; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.checkout.title') ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc(base_url(rawurlencode($business['slug'] ?? $business_slug)), 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li><a href="<?= esc($urls['cart'], 'attr') ?>"><?= lang('Business.cart.title') ?></a></li>
                    <li class="current"><?= lang('Business.checkout.title') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="checkout" class="checkout section">
        <div class="container">
            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert"><?= esc($error) ?></div>
            <?php endif ?>
            <?php if (isset($errors['lines'])): ?>
                <div class="alert alert-danger" role="alert"><?= esc($errors['lines']) ?> <a href="<?= esc($urls['cart'], 'attr') ?>"><?= lang('Business.checkout.edit-cart') ?></a></div>
            <?php endif ?>
            <?php foreach ($notices as $notice): ?>
                <div class="alert alert-warning" role="status"><?= esc($notice) ?></div>
            <?php endforeach ?>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="step-indicator d-flex align-items-center mb-4" id="checkout-steps">
                        <?php foreach ($steps as $n => [$name, $label]): ?>
                            <?php if ($n > 0): ?><div class="step-connector"></div><?php endif ?>
                            <div class="step-pill" data-step="<?= $name ?>">
                                <span class="step-num"><?= $n + 1 ?></span>
                                <span class="step-text"><?= esc($label) ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>

                    <form method="post" action="<?= esc($urls['confirm'], 'attr') ?>" id="checkout-form" class="checkout-card" novalidate>
                        <?= csrf_field() ?>

                        <!-- 1. who the order is for -->
                        <div class="form-section" data-step="contact">
                            <div class="section-label">
                                <span class="label-icon"><i class="bi bi-person"></i></span>
                                <div>
                                    <h3><?= lang('Business.checkout.step-contact') ?></h3>
                                    <p><?= lang('Business.checkout.contact-note') ?></p>
                                </div>
                            </div>
                            <div class="section-fields">
                                <div class="row g-3">
                                    <div class="col-md-6 field-group">
                                        <label for="first_name"><?= lang('Business.checkout.first-name') ?></label>
                                        <input type="text" class="form-control<?= $invalid('customer.first_name') ?>" id="first_name" name="first_name" maxlength="100" autocomplete="given-name" required value="<?= $value('customer', 'first_name') ?>">
                                        <?= $feedback('customer.first_name') ?>
                                    </div>
                                    <div class="col-md-6 field-group">
                                        <label for="last_name"><?= lang('Business.checkout.last-name') ?></label>
                                        <input type="text" class="form-control<?= $invalid('customer.last_name') ?>" id="last_name" name="last_name" maxlength="100" autocomplete="family-name" required value="<?= $value('customer', 'last_name') ?>">
                                        <?= $feedback('customer.last_name') ?>
                                    </div>
                                    <div class="col-md-6 field-group">
                                        <label for="email"><?= lang('Business.checkout.email') ?></label>
                                        <input type="email" class="form-control<?= $invalid('customer.email') ?>" id="email" name="email" maxlength="64" autocomplete="email" required value="<?= $value('customer', 'email') ?>">
                                        <?= $feedback('customer.email') ?>
                                    </div>
                                    <div class="col-md-6 field-group">
                                        <label for="telephone"><?= lang('Business.checkout.telephone') ?></label>
                                        <input type="tel" class="form-control<?= $invalid('customer.telephone') ?>" id="telephone" name="telephone" maxlength="24" autocomplete="tel" pattern="\+?[0-9 ()\-]{6,24}" required value="<?= $value('customer', 'telephone') ?>">
                                        <?= $feedback('customer.telephone') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="step-actions">
                                <a href="<?= esc($urls['cart'], 'attr') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> <?= lang('Business.checkout.edit-cart') ?></a>
                                <button type="button" class="btn btn-accent btn-next" data-next><?= lang('Business.checkout.continue') ?> <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>

                        <?php if ($checkout['needs_shipping']): ?>
                        <!-- 2. how the products get to the customer -->
                        <div class="form-section" data-step="delivery">
                            <div class="section-divider"></div>
                            <div class="section-label">
                                <span class="label-icon"><i class="bi bi-truck"></i></span>
                                <div>
                                    <h3><?= lang('Business.checkout.step-delivery') ?></h3>
                                    <p><?= lang('Business.checkout.delivery-method') ?></p>
                                </div>
                            </div>
                            <div class="section-fields">
                                <div class="option-list" id="shipping-options">
                                    <?php foreach ($checkout['shipping_methods'] as $method): ?>
                                        <label class="option<?= $method['available'] ? '' : ' unavailable' ?>" data-method="<?= esc($method['public_id'], 'attr') ?>">
                                            <input type="radio" name="shipping" value="<?= esc($method['public_id'], 'attr') ?>" data-type="<?= esc($method['type'], 'attr') ?>"
                                                <?= $checkout['shipping'] === $method['public_id'] || ($checkout['shipping'] === '' && $state['choice']['shipping'] === $method['public_id']) ? 'checked' : '' ?><?= $method['available'] ? '' : ' disabled' ?>>
                                            <span class="option-body">
                                                <span class="option-name"><?= esc($method['name']) ?></span>
                                                <span class="option-note" data-note><?= esc(implode(' · ', array_filter([$method['unavailable'], $method['eta'], $method['store'], $method['description']]))) ?></span>
                                            </span>
                                            <span class="option-fee" data-fee><?= esc($method['fee']) ?></span>
                                        </label>
                                    <?php endforeach ?>
                                </div>
                                <div class="field-error" id="shipping-error"<?= isset($errors['shipping']) || $checkout['shipping_error'] !== null ? '' : ' hidden' ?>><?= esc($errors['shipping'] ?? $checkout['shipping_error'] ?? '') ?></div>

                                <!-- a home delivery: the address -->
                                <div class="delivery-fields" data-for="H">
                                    <div class="row g-3">
                                        <div class="col-12 field-group">
                                            <label for="line_1"><?= lang('Business.checkout.address-line-1') ?></label>
                                            <input type="text" class="form-control<?= $invalid('address.line_1') ?>" id="line_1" name="line_1" maxlength="255" autocomplete="address-line1" value="<?= $value('delivery', 'line_1') ?>" data-required>
                                            <?= $feedback('address.line_1') ?>
                                        </div>
                                        <div class="col-12 field-group">
                                            <label for="line_2"><?= lang('Business.checkout.address-line-2') ?></label>
                                            <input type="text" class="form-control" id="line_2" name="line_2" maxlength="255" autocomplete="address-line2" value="<?= $value('delivery', 'line_2') ?>">
                                        </div>
                                        <div class="col-md-6 field-group">
                                            <label for="city"><?= lang('Business.checkout.city') ?></label>
                                            <input type="text" class="form-control<?= $invalid('address.city') ?>" id="city" name="city" maxlength="128" autocomplete="address-level2" value="<?= $value('delivery', 'city') ?>" data-required>
                                            <?= $feedback('address.city') ?>
                                        </div>
                                        <div class="col-md-6 field-group">
                                            <label for="postal_code"><?= lang('Business.checkout.postal-code') ?></label>
                                            <input type="text" class="form-control<?= $invalid('address.postal_code') ?>" id="postal_code" name="postal_code" maxlength="16" autocomplete="postal-code" inputmode="numeric" value="<?= $value('delivery', 'postal_code') ?>" data-required>
                                            <?= $feedback('address.postal_code') ?>
                                        </div>
                                        <?php if ($provinces !== []): ?>
                                        <div class="col-12 field-group">
                                            <label for="subdivision_code"><?= lang('Business.checkout.subdivision') ?></label>
                                            <select class="form-select" id="subdivision_code" name="subdivision_code" autocomplete="address-level1">
                                                <option value=""><?= lang('Business.checkout.subdivision-choose') ?></option>
                                                <?php foreach ($provinces as $code => $name): ?>
                                                    <option value="<?= esc($code, 'attr') ?>"<?= $state['delivery']['subdivision_code'] === $code ? ' selected' : '' ?>><?= esc($name) ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <!-- a locker or pickup point: which, as the customer names it -->
                                <div class="delivery-fields" data-for="L">
                                    <div class="field-group">
                                        <label for="pickup_point"><?= lang('Business.checkout.pickup-point') ?></label>
                                        <textarea class="form-control<?= $invalid('pickup_point') ?>" id="pickup_point" name="pickup_point" rows="3" maxlength="500" data-required><?= esc($state['delivery']['pickup_point']) ?></textarea>
                                        <div class="form-text"><?= lang('Business.checkout.pickup-point-help') ?></div>
                                        <?= $feedback('pickup_point') ?>
                                    </div>
                                </div>

                                <!-- a store pickup offered at every store: which store -->
                                <div class="delivery-fields" data-for="S">
                                    <div class="field-group">
                                        <label for="pickup_store"><?= lang('Business.checkout.pickup-store') ?></label>
                                        <select class="form-select" id="pickup_store" name="pickup_store" data-required></select>
                                    </div>
                                </div>
                            </div>
                            <div class="step-actions">
                                <button type="button" class="btn btn-outline-secondary" data-back><i class="bi bi-arrow-left"></i> <?= lang('Business.checkout.back') ?></button>
                                <button type="button" class="btn btn-accent btn-next" data-next><?= lang('Business.checkout.continue') ?> <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <?php endif ?>

                        <!-- 3. the discount code and how to pay -->
                        <div class="form-section" data-step="payment">
                            <div class="section-divider"></div>
                            <div class="section-label">
                                <span class="label-icon"><i class="bi bi-tag"></i></span>
                                <div><h3><?= lang('Business.checkout.discount-code') ?></h3></div>
                            </div>
                            <div class="section-fields">
                                <div class="input-group">
                                    <input type="text" class="form-control text-uppercase" id="discount" name="discount" maxlength="30" autocomplete="off" value="<?= esc($checkout['discount']['code'] ?? '', 'attr') ?>" aria-label="<?= esc(lang('Business.checkout.discount-code'), 'attr') ?>">
                                    <button type="button" class="btn btn-outline-secondary" id="discount-apply"><?= lang('Business.checkout.apply') ?></button>
                                    <button type="button" class="btn btn-outline-secondary" id="discount-remove"<?= $checkout['discount'] === null ? ' hidden' : '' ?>><?= lang('Business.checkout.remove') ?></button>
                                </div>
                                <div class="discount-applied" id="discount-applied"<?= $checkout['discount'] === null ? ' hidden' : '' ?>><?= esc($checkout['discount']['description'] ?? '') ?></div>
                                <div class="field-error" id="discount-error"<?= isset($errors['discount']) || $checkout['discount_error'] !== null ? '' : ' hidden' ?>><?= esc($errors['discount'] ?? $checkout['discount_error'] ?? '') ?></div>
                            </div>

                            <div class="section-divider"></div>
                            <div class="section-label">
                                <span class="label-icon"><i class="bi bi-wallet2"></i></span>
                                <div>
                                    <h3><?= lang('Business.checkout.payment-method') ?></h3>
                                    <p><?= lang('Business.checkout.payment-note') ?></p>
                                </div>
                            </div>
                            <div class="section-fields">
                                <div class="option-list">
                                    <?php foreach ($checkout['payment_methods'] as $method): ?>
                                        <label class="option">
                                            <input type="radio" name="payment" value="<?= esc($method['public_id'], 'attr') ?>" required<?= ($state['choice']['payment'] === $method['public_id'] || count($checkout['payment_methods']) === 1) ? ' checked' : '' ?>>
                                            <span class="option-body">
                                                <span class="option-name">
                                                    <i class="bi <?= $method['handler'] === 'promptpay_qr' ? 'bi-qr-code' : 'bi-cash-coin' ?>"></i> <?= esc($method['name']) ?>
                                                </span>
                                                <?php if ($method['instruction']): ?>
                                                    <span class="option-note"><?= esc($method['instruction']) ?></span>
                                                <?php endif ?>
                                            </span>
                                        </label>
                                    <?php endforeach ?>
                                </div>
                                <div class="field-error" id="payment-error"<?= isset($errors['payment']) ? '' : ' hidden' ?>><?= esc($errors['payment'] ?? '') ?></div>
                            </div>
                            <div class="step-actions">
                                <button type="button" class="btn btn-outline-secondary" data-back><i class="bi bi-arrow-left"></i> <?= lang('Business.checkout.back') ?></button>
                                <button type="submit" class="btn btn-accent btn-next" id="place-order"><?= lang('Business.checkout.place-order') ?> <i class="bi bi-lock"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-5">
                    <div class="order-summary-card" id="checkout-summary">
                        <div class="summary-header">
                            <h3><?= lang('Business.checkout.summary') ?></h3>
                            <span class="item-count" data-total="items"><?= esc($checkout['totals']['items']) ?></span>
                        </div>
                        <div class="summary-products" id="summary-products">
                            <?php foreach ($checkout['lines'] as $line): ?>
                                <div class="product-row">
                                    <div class="product-img">
                                        <img src="<?= esc($line['image'] ?: $noImage, 'attr') ?>" alt="" loading="lazy">
                                        <span class="qty-badge"><?= (int) $line['quantity'] ?></span>
                                    </div>
                                    <div class="product-detail">
                                        <h5><?= esc($line['name']) ?></h5>
                                        <?php if ($line['label'] !== '' || $line['store']): ?>
                                            <span class="product-variant"><?= esc(implode(' · ', array_filter([$line['label'], $line['store']]))) ?></span>
                                        <?php endif ?>
                                        <?php if ($line['when']): ?>
                                            <span class="when"><i class="bi bi-calendar-event"></i> <?= esc($line['when']) ?></span>
                                        <?php endif ?>
                                    </div>
                                    <div class="product-price"><?= esc($line['total']) ?></div>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <div class="cost-breakdown">
                            <div class="cost-line d-flex justify-content-between"><span><?= lang('Business.checkout.subtotal') ?></span><span data-total="subtotal"><?= esc($checkout['totals']['subtotal']) ?></span></div>
                            <div class="cost-line d-flex justify-content-between" data-row="discount"<?= $checkout['totals']['discount'] === null ? ' hidden' : '' ?>>
                                <span data-label="discount"><?= lang('Business.checkout.discount', [esc($checkout['discount']['code'] ?? '')]) ?></span>
                                <span data-total="discount"><?= esc($checkout['totals']['discount'] ?? '') ?></span>
                            </div>
                            <div class="cost-line d-flex justify-content-between" data-row="shipping"<?= $checkout['totals']['shipping'] === null ? ' hidden' : '' ?>>
                                <span><?= lang('Business.checkout.shipping') ?></span><span data-total="shipping"><?= esc($checkout['totals']['shipping'] ?? '') ?></span>
                            </div>
                            <div class="cost-line d-flex justify-content-between" data-row="tax_added"<?= $checkout['totals']['tax_added'] === null ? ' hidden' : '' ?>>
                                <span><?= lang('Business.checkout.tax') ?></span><span data-total="tax_added"><?= esc($checkout['totals']['tax_added'] ?? '') ?></span>
                            </div>
                        </div>
                        <div class="cost-breakdown">
                            <div class="cost-total d-flex justify-content-between"><span><?= lang('Business.checkout.total') ?></span><span data-total="total"><?= esc($checkout['totals']['total']) ?></span></div>
                            <p class="tax-note" data-total="tax_included"<?= $checkout['totals']['tax_included'] === null ? ' hidden' : '' ?>><?= esc($checkout['totals']['tax_included'] ?? '') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        // one step at a time; every choice that changes the price is priced again by the shop
        (function () {
            const form = document.getElementById('checkout-form');
            const steps = <?= $json(array_column($steps, 0)) ?>;
            const urls = <?= $json($urls) ?>;
            let methods = <?= $json($checkout['shipping_methods']) ?>;
            let pickupStore = <?= $json($checkout['pickup_store'] ?: $state['choice']['pickup_store']) ?>;
            const text = <?= $json(['discount' => lang('Business.checkout.discount', ['{code}']), 'failed' => lang('Business.cart.try-again')]) ?>;
            const sections = [...form.querySelectorAll('.form-section')];
            const pills = [...document.querySelectorAll('#checkout-steps .step-pill')];
            const summary = document.getElementById('checkout-summary');
            const byId = (id) => document.getElementById(id);
            const show = (element, message) => { if (!element) return; element.textContent = message || ''; element.hidden = !message; };
            let current = 0;

            const go = (index) => {
                current = index;
                sections.forEach((section, i) => { section.hidden = i !== index; });
                pills.forEach((pill, i) => {
                    pill.classList.toggle('active', i === index);
                    pill.classList.toggle('done', i < index);
                });
                const first = sections[index].querySelector('input:not([type=hidden]):not([disabled]), select, textarea');
                if (first && index > 0) first.focus({ preventScroll: true });
                document.getElementById('checkout').scrollIntoView({ behavior: 'smooth', block: 'start' });
            };
            pills.forEach((pill, i) => pill.addEventListener('click', () => { if (i < current) go(i); }));

            // the delivery fields of the chosen method only, and required only then
            const chosenMethod = () => form.querySelector('input[name="shipping"]:checked');
            const showDelivery = () => {
                const radio = chosenMethod();
                const method = radio ? methods.find((m) => m.public_id === radio.value) : null;
                const type = method ? (method.pickup_stores ? 'S' : method.type) : null;
                form.querySelectorAll('.delivery-fields').forEach((block) => {
                    block.hidden = block.dataset.for !== type;
                    block.querySelectorAll('[data-required]').forEach((field) => { field.required = !block.hidden; });
                });
                const select = byId('pickup_store');
                if (select && method && method.pickup_stores) {
                    select.replaceChildren(...method.pickup_stores.map((store) => {
                        const option = new Option(store.name + (store.address ? ' – ' + store.address : ''), store.public_id);
                        option.selected = store.public_id === pickupStore;
                        return option;
                    }));
                    pickupStore = select.value;
                }
            };

            const valid = (section) => {
                const fields = [...section.querySelectorAll('input, select, textarea')].filter((f) => !f.closest('.delivery-fields[hidden]') && f.type !== 'hidden');
                const bad = fields.find((field) => !field.checkValidity());
                fields.forEach((field) => field.classList.toggle('is-invalid', !field.checkValidity()));
                if (bad) { bad.reportValidity(); return false; }
                if (section.dataset.step === 'delivery' && !chosenMethod()) {
                    show(byId('shipping-error'), <?= $json(lang('Business.checkout.shipping-required')) ?>);
                    return false;
                }
                return true;
            };
            form.querySelectorAll('[data-next]').forEach((button) => button.addEventListener('click', () => {
                if (valid(sections[current])) go(current + 1);
            }));
            form.querySelectorAll('[data-back]').forEach((button) => button.addEventListener('click', () => go(current - 1)));
            form.addEventListener('input', (event) => event.target.classList.remove('is-invalid'));

            // the price again, with the choices as they are now
            let pending = null;
            const requote = () => {
                if (pending) pending.abort();
                pending = new AbortController();
                summary.classList.add('summary-busy');
                const body = new FormData(form);
                return fetch(urls.quote, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: pending.signal })
                    .then((response) => response.json())
                    .then((answer) => {
                        if (answer.cart_url) { window.location.href = answer.cart_url; return; }
                        if (!answer.ok) throw new Error(answer.message);
                        draw(answer.checkout);
                    })
                    .catch((error) => { if (error.name !== 'AbortError') show(byId('shipping-error') || byId('discount-error'), text.failed); })
                    .finally(() => summary.classList.remove('summary-busy'));
            };

            const draw = (data) => {
                const set = (name, value) => {
                    const element = summary.querySelector('[data-total="' + name + '"]');
                    const row = summary.querySelector('[data-row="' + name + '"]') || element;
                    if (element) element.textContent = value || '';
                    if (row && name !== 'subtotal' && name !== 'total' && name !== 'items') row.hidden = !value;
                };
                Object.entries(data.totals).forEach(([name, value]) => set(name, value));
                summary.querySelector('[data-label="discount"]').textContent = text.discount.replace('{code}', data.discount ? data.discount.code : '');

                methods = data.shipping_methods;
                methods.forEach((method) => {
                    const option = form.querySelector('.option[data-method="' + method.public_id + '"]');
                    if (!option) return;
                    option.classList.toggle('unavailable', !method.available);
                    option.querySelector('input').disabled = !method.available;
                    option.querySelector('[data-fee]').textContent = method.fee;
                    option.querySelector('[data-note]').textContent = [method.unavailable, method.eta, method.store, method.description].filter(Boolean).join(' · ');
                });
                show(byId('shipping-error'), data.shipping_error);
                show(byId('discount-error'), data.discount_error);
                show(byId('discount-applied'), data.discount ? (data.discount.description || '') : '');
                byId('discount-applied').hidden = !data.discount;
                byId('discount-remove').hidden = !data.discount;
                if (!data.discount) byId('discount').value = '';
            };

            form.addEventListener('change', (event) => {
                if (event.target.name === 'shipping') { showDelivery(); requote(); }
                if (event.target.name === 'pickup_store') { pickupStore = event.target.value; requote(); }
                if (event.target.name === 'payment') { show(byId('payment-error'), ''); requote(); }
            });
            byId('discount-apply').addEventListener('click', () => { if (byId('discount').value.trim() !== '') requote(); });
            byId('discount').addEventListener('keydown', (event) => {
                if (event.key === 'Enter') { event.preventDefault(); byId('discount-apply').click(); }
            });
            byId('discount-remove').addEventListener('click', () => { byId('discount').value = ''; requote(); });

            form.addEventListener('submit', (event) => {
                if (!form.querySelector('input[name="payment"]:checked')) {
                    event.preventDefault();
                    show(byId('payment-error'), <?= $json(lang('Business.checkout.payment-required')) ?>);
                    return;
                }
                for (let i = 0; i < sections.length - 1; i++) {
                    if (!valid(sections[i])) { event.preventDefault(); go(i); return; }
                }
                byId('place-order').disabled = true;
            });

            showDelivery();
            go(Math.max(0, steps.indexOf(<?= $json($firstError ?? 'contact') ?>)));
            // a pickup store picked by default is kept, so the price is for it
            if (byId('pickup_store') && !byId('pickup_store').closest('[hidden]') && pickupStore && <?= $json($checkout['pickup_store'] === '') ?>) requote();
        })();
    </script>
<?= $this->endSection() ?>
