<?php
/**
 * An order's payment (Business::payment()): how to pay it, by its payment method's handler, and what was ordered
 * - promptpay_qr: a PromptPay QR with the amount in it, drawn in the browser from the payload v1/mart/order sent
 * - instruction:  the business's instructions (cash at the counter, a bank transfer ...)
 * The business confirms the payment itself; the status says where the order is.
 *
 * @var array  $site
 * @var array  $order       as Business::orderView() makes it
 * @var bool   $just_placed the customer has just placed it
 * @var bool   $looked_up   found by its number and contact (Business::checkOrderStatus()), not by its key: the
 *                          order's status only, as telling the shop it was paid needs the key
 * @var string $home_url
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$payment  = $order['payment'];
$looked_up ??= false;
$title    = $looked_up ? lang('Business.order-status.title') : lang('Business.payment.title');
$status   = ['P' => ['warning', 'hourglass-split'], 'C' => ['success', 'check-circle'], 'X' => ['secondary', 'x-circle']][$order['status']] ?? ['secondary', 'info-circle'];
?>
<?= $this->section('content') ?>
    <style>
        .payment .pay-card, .payment .order-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 12px; padding: 24px; }
        .payment .order-number { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 18px; letter-spacing: .04em; }
        .payment .amount { font-size: 28px; font-weight: 700; color: var(--heading-color); }
        .payment .qr-box { display: inline-block; padding: 14px; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; }
        .payment .qr-box img, .payment .qr-box canvas { display: block; }
        .payment .instruction { white-space: pre-line; }
        .payment .line { display: flex; justify-content: space-between; gap: 16px; padding: 12px 0; border-bottom: 1px solid color-mix(in srgb, var(--default-color), transparent 92%); }
        .payment .line:last-child { border-bottom: 0; }
        .payment .line .sub { display: block; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .payment .totals .row-line { display: flex; justify-content: space-between; padding: 4px 0; }
        .payment .totals .grand { font-weight: 700; font-size: 18px; border-top: 1px solid color-mix(in srgb, var(--default-color), transparent 88%); margin-top: 6px; padding-top: 10px; }
        .payment .note { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= $title ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($home_url, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <?php if ($looked_up): ?>
                        <li><a href="<?= esc(base_url(rawurlencode($business_slug) . '/check-order-status'), 'attr') ?>"><?= $title ?></a></li>
                        <li class="current"><?= esc($order['number']) ?></li>
                    <?php else: ?>
                        <li class="current"><?= $title ?></li>
                    <?php endif ?>
                </ol>
            </nav>
        </div>
    </div>
    <section id="payment" class="payment section">
        <div class="container">
            <?php if ($just_placed): ?>
                <div class="alert alert-success" role="status"><i class="bi bi-check-circle"></i> <?= lang('Business.payment.placed') ?></div>
            <?php endif ?>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="pay-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="note"><?= lang('Business.payment.number') ?></div>
                                <div class="order-number"><?= esc($order['number']) ?></div>
                            </div>
                            <span class="badge text-bg-<?= $status[0] ?>"><i class="bi bi-<?= $status[1] ?>"></i> <?= lang('Business.payment.status-' . $order['status']) ?></span>
                        </div>
                        <h4 class="mt-4"><?= lang('Business.payment.how') ?>: <?= esc($payment['name']) ?></h4>
                        <div class="note"><?= lang('Business.payment.amount') ?></div>
                        <div class="amount mb-3"><?= esc($order['totals']['total']) ?></div>

                        <?php if ($order['status'] === 'P' && $payment['handler_code'] === 'promptpay_qr'): ?>
                            <?php if ($payment['promptpay_payload']): ?>
                                <p><?= lang('Business.payment.scan', [esc($order['totals']['total'])]) ?></p>
                                <div class="qr-box" id="promptpay-qr" data-payload="<?= esc($payment['promptpay_payload'], 'attr') ?>" role="img" aria-label="PromptPay QR"></div>
                                <p class="text-danger mt-2" id="promptpay-failed" hidden><?= lang('Business.payment.promptpay-failed') ?></p>
                            <?php else: ?>
                                <p class="text-danger"><?= lang('Business.payment.promptpay-failed') ?></p>
                            <?php endif ?>
                        <?php endif ?>
                        <?php if ($payment['instruction']): ?>
                            <p class="instruction mt-3"><?= esc($payment['instruction']) ?></p>
                        <?php elseif ($payment['handler_code'] !== 'promptpay_qr'): ?>
                            <p class="mt-3"><?= lang('Business.payment.no-instruction') ?></p>
                        <?php endif ?>
                        <?php if (session()->getFlashdata('payment_reported')): ?>
                            <div class="alert alert-success mt-3 mb-0"><?= esc(session()->getFlashdata('payment_reported')) ?></div>
                        <?php endif ?>
                        <?php if (session()->getFlashdata('payment_report_error')): ?>
                            <div class="alert alert-danger mt-3 mb-0"><?= esc(session()->getFlashdata('payment_report_error')) ?></div>
                        <?php endif ?>
                        <?php if ($order['status'] === 'P' && ! empty($payment['reported_at'])): ?>
                            <p class="mt-3 mb-0"><i class="bi bi-hourglass-split"></i> <?= lang('Business.payment.reported') ?></p>
                        <?php elseif ($order['status'] === 'P' && $payment['method_code'] !== 'cash' && ! $looked_up): ?>
                            <!-- the customer says they paid: the shop checks it and marks it received -->
                            <form class="mt-4" method="post" action="<?= esc(base_url(rawurlencode($business_slug) . '/payment/' . rawurlencode($order['number']) . '/report'), 'attr') ?>" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <h5><?= lang('Business.payment.paid-title') ?></h5>
                                <p class="note"><?= lang('Business.payment.paid-help') ?></p>
                                <div class="mb-2">
                                    <label class="form-label" for="pay-reference"><?= lang('Business.payment.reference') ?></label>
                                    <input type="text" class="form-control" id="pay-reference" name="reference" maxlength="100" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="pay-slip"><?= lang('Business.payment.slip') ?></label>
                                    <input type="file" class="form-control" id="pay-slip" name="slip" accept="image/jpeg,image/png,image/webp,application/pdf">
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send-check"></i> <?= lang('Business.payment.paid-button') ?></button>
                            </form>
                        <?php endif ?>
                        <?php if ($looked_up && $order['status'] === 'P' && empty($payment['reported_at']) && $payment['method_code'] !== 'cash'): ?>
                            <p class="note mt-4 mb-0"><i class="bi bi-envelope"></i> <?= lang('Business.order-status.keep') ?></p>
                        <?php elseif (! $looked_up): ?>
                            <p class="note mt-4 mb-0"><i class="bi bi-info-circle"></i> <?= lang('Business.payment.keep') ?></p>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="order-card">
                        <h4><?= lang('Business.payment.ordered') ?></h4>
                        <?php foreach ($order['lines'] as $line): ?>
                            <div class="line">
                                <div>
                                    <strong><?= esc($line['name']) ?></strong> × <?= (int) $line['quantity'] ?>
                                    <?php if ($line['label'] !== '' || $line['store']): ?>
                                        <span class="sub"><?= esc(implode(' · ', array_filter([$line['label'], $line['store']]))) ?></span>
                                    <?php endif ?>
                                    <?php if ($line['when']): ?>
                                        <span class="sub"><i class="bi bi-calendar-event"></i> <?= esc($line['when']) ?></span>
                                    <?php endif ?>
                                    <?php if ($line['participants'] !== []): ?>
                                        <span class="sub"><?= lang('Business.payment.for', [esc(implode(', ', $line['participants']))]) ?></span>
                                    <?php endif ?>
                                </div>
                                <div class="text-nowrap"><?= esc($line['total']) ?></div>
                            </div>
                        <?php endforeach ?>
                        <div class="totals mt-3">
                            <div class="row-line"><span><?= lang('Business.checkout.subtotal') ?></span><span><?= esc($order['totals']['subtotal']) ?></span></div>
                            <?php if ($order['totals']['discount'] !== null): ?>
                                <div class="row-line"><span><?= lang('Business.checkout.discount', [esc($order['discount_code'] ?? '')]) ?></span><span><?= esc($order['totals']['discount']) ?></span></div>
                            <?php endif ?>
                            <?php if ($order['totals']['shipping'] !== null): ?>
                                <div class="row-line"><span><?= lang('Business.checkout.shipping') ?></span><span><?= esc($order['totals']['shipping']) ?></span></div>
                            <?php endif ?>
                            <?php if ($order['totals']['tax_added'] !== null): ?>
                                <div class="row-line"><span><?= lang('Business.checkout.tax') ?></span><span><?= esc($order['totals']['tax_added']) ?></span></div>
                            <?php endif ?>
                            <div class="row-line grand"><span><?= lang('Business.checkout.total') ?></span><span><?= esc($order['totals']['total']) ?></span></div>
                            <?php if ($order['totals']['tax_included'] !== null): ?>
                                <div class="note text-end"><?= esc($order['totals']['tax_included']) ?></div>
                            <?php endif ?>
                        </div>
                        <?php if ($order['shipping'] !== null): ?>
                            <h5 class="mt-4"><?= lang('Business.payment.delivery') ?></h5>
                            <p class="mb-0"><?= esc($order['shipping']['name']) ?></p>
                            <?php if ($order['shipping']['where']): ?><p class="note instruction"><?= esc($order['shipping']['where']) ?></p><?php endif ?>
                        <?php endif ?>
                        <h5 class="mt-4"><?= lang('Business.payment.contact') ?></h5>
                        <p class="note mb-0"><?= esc($order['customer']['first_name'] . ' ' . $order['customer']['last_name']) ?> · <?= esc($order['customer']['email']) ?> · <?= esc($order['customer']['telephone']) ?></p>
                    </div>
                    <a href="<?= esc($home_url, 'attr') ?>" class="btn btn-outline-secondary mt-3"><i class="bi bi-arrow-left"></i> <?= lang('Business.payment.continue') ?></a>
                    <?php if ($looked_up): ?>
                        <a href="<?= esc(base_url(rawurlencode($business_slug) . '/check-order-status'), 'attr') ?>" class="btn btn-outline-secondary mt-3 ms-2"><i class="bi bi-search"></i> <?= lang('Business.order-status.another') ?></a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </section>
    <?php if ($order['status'] === 'P' && $payment['promptpay_payload']): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            (function () {
                const box = document.getElementById('promptpay-qr');
                try {
                    new QRCode(box, { text: box.dataset.payload, width: 240, height: 240, correctLevel: QRCode.CorrectLevel.M });
                } catch (error) {
                    box.hidden = true;
                    document.getElementById('promptpay-failed').hidden = false;
                }
            })();
        </script>
    <?php endif ?>
<?= $this->endSection() ?>
