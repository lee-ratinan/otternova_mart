<?php
/**
 * My order history (Business::myOrderHistory() and openOrderHistory()), by $mode:
 * - form: the email address to send the link to (posted to Business::retrieveOrderHistory())
 * - open: the emailed link's page, whose button opens the history (Business::confirmOrderHistory())
 * - list: the orders placed with the address at this business: when, the number, the amount, the payment's status
 *         and the order's; an order's details are on Check Order Status
 *
 * @var array       $site
 * @var string      $mode     form, open or list
 * @var string      $home_url
 * @var string      $action   form, open: where the form posts
 * @var string|null $error    form
 * @var string|null $sent     form: the address a link was asked for
 * @var string      $email    form: the address typed; list: the address of the history
 * @var list<array{number: string, placed: string, total: string, order_status: string, financial_status: string}> $orders list
 * @var string      $sign_out list
 * @var string      $lookup   list: Check Order Status
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$orderTone = ['open' => 'primary', 'in_progress' => 'info', 'closed' => 'success', 'cancelled' => 'secondary'];
$payTone   = ['pending' => 'warning', 'partially_paid' => 'warning', 'paid' => 'success', 'partially_refunded' => 'secondary', 'full_refunded' => 'secondary'];
$label     = static fn (string $key, string $fallback): string => ($text = lang($key)) === $key ? $fallback : $text;
?>
<?= $this->section('content') ?>
    <style>
        .order-history .history-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 12px; padding: 28px; }
        .order-history .note { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .order-history .order-number { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing: .04em; white-space: nowrap; }
        .order-history table { margin-bottom: 0; }
        .order-history th { font-size: 13px; font-weight: 600; color: color-mix(in srgb, var(--default-color), transparent 30%); white-space: nowrap; }
        .order-history td { vertical-align: middle; }
        .order-history .amount { font-variant-numeric: tabular-nums; white-space: nowrap; }
        /* a phone: each order a card of its own */
        @media (max-width: 767.98px) {
            .order-history table thead { display: none; }
            .order-history table, .order-history tbody, .order-history tr, .order-history td { display: block; width: 100%; }
            .order-history tr { padding: 12px 0; border-bottom: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); }
            .order-history td { border: 0; padding: 2px 0; display: flex; justify-content: space-between; gap: 12px; }
            .order-history td::before { content: attr(data-label); font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.order-history.title') ?></h1>
            <?= view('section_parts/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
        </div>
    </div>
    <section id="order-history" class="order-history section">
        <div class="container">
            <?php if ($mode === 'list'): ?>
                <div class="history-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h4 class="mb-1"><i class="bi bi-clock-history"></i> <?= esc($business['name']) ?></h4>
                            <div class="note"><?= lang('Business.order-history.signed-in-as', [esc($email)]) ?></div>
                        </div>
                        <form method="post" action="<?= esc($sign_out, 'attr') ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> <?= lang('Business.order-history.sign-out') ?></button>
                        </form>
                    </div>
                    <?php if ($orders === []): ?>
                        <p class="mb-0"><?= lang('Business.order-history.none') ?></p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?= lang('Business.order-history.placed') ?></th>
                                        <th scope="col"><?= lang('Business.order-history.number') ?></th>
                                        <th scope="col" class="text-end"><?= lang('Business.order-history.amount') ?></th>
                                        <th scope="col"><?= lang('Business.order-history.payment-status') ?></th>
                                        <th scope="col"><?= lang('Business.order-history.order-status') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td data-label="<?= esc(lang('Business.order-history.placed'), 'attr') ?>"><?= esc($order['placed']) ?></td>
                                            <td data-label="<?= esc(lang('Business.order-history.number'), 'attr') ?>"><span class="order-number"><?= esc($order['number']) ?></span></td>
                                            <td data-label="<?= esc(lang('Business.order-history.amount'), 'attr') ?>" class="amount text-md-end"><?= esc($order['total']) ?></td>
                                            <td data-label="<?= esc(lang('Business.order-history.payment-status'), 'attr') ?>">
                                                <span class="badge text-bg-<?= $payTone[$order['financial_status']] ?? 'secondary' ?>"><?= esc($label('Business.order-history.pay-' . $order['financial_status'], $order['financial_status'])) ?></span>
                                            </td>
                                            <td data-label="<?= esc(lang('Business.order-history.order-status'), 'attr') ?>">
                                                <span class="badge text-bg-<?= $orderTone[$order['order_status']] ?? 'secondary' ?>"><?= esc($label('Business.order-history.order-' . $order['order_status'], $order['order_status'])) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif ?>
                    <p class="note mt-3 mb-0"><i class="bi bi-info-circle"></i> <?= lang('Business.order-history.details') ?>
                        <a href="<?= esc($lookup, 'attr') ?>"><?= lang('Business.order-status.title') ?></a></p>
                </div>
                <p class="note mt-3"><i class="bi bi-shop"></i> <?= lang('Business.order-history.other-shop') ?></p>
            <?php else: ?>
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="history-card">
                            <h4 class="mb-2"><i class="bi bi-clock-history"></i> <?= esc($business['name']) ?></h4>
                            <?php if ($mode === 'open'): ?>
                                <p class="mb-4"><?= lang('Business.order-history.open-intro') ?></p>
                                <form method="post" action="<?= esc($action, 'attr') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> <?= lang('Business.order-history.open') ?></button>
                                </form>
                            <?php else: ?>
                                <p class="mb-4"><?= lang('Business.order-history.intro') ?></p>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i> <?= esc($error) ?></div>
                                <?php endif ?>
                                <?php if ($sent): ?>
                                    <div class="alert alert-success" role="status"><i class="bi bi-envelope-check"></i> <?= lang('Business.order-history.sent', [esc($sent)]) ?></div>
                                <?php endif ?>
                                <form method="post" action="<?= esc($action, 'attr') ?>" novalidate>
                                    <?= csrf_field() ?>
                                    <div class="mb-4">
                                        <label class="form-label" for="history-email"><?= lang('Business.order-history.email') ?></label>
                                        <input type="email" class="form-control" id="history-email" name="email" value="<?= esc($email, 'attr') ?>"
                                               maxlength="64" required autocomplete="email" spellcheck="false">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-envelope"></i> <?= lang('Business.order-history.submit') ?></button>
                                </form>
                            <?php endif ?>
                            <p class="note mt-4 mb-0"><i class="bi bi-shop"></i> <?= lang('Business.order-history.other-shop') ?></p>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </section>
<?= $this->endSection() ?>
