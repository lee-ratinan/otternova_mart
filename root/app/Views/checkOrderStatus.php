<?php
/**
 * Check my order status (Business::checkOrderStatus()): the order number with the email address or the telephone
 * number the order was placed with, posted to Business::retrieveOrderStatus(). Both must match an order of this
 * business; the order itself is then shown by the payment view.
 *
 * @var array       $site
 * @var string      $number  filled in again after a failed try, or from the address
 * @var string      $contact filled in again after a failed try
 * @var string|null $error
 * @var string      $action
 * @var string      $home_url
 */
$this->extend('_layout_otternova');

$business = $site['business'];
?>
<?= $this->section('content') ?>
    <style>
        .order-status .lookup-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 12px; padding: 28px; }
        .order-status .note { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .order-status #lookup-number { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing: .04em; text-transform: uppercase; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.order-status.title') ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($home_url, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li class="current"><?= lang('Business.order-status.title') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="order-status" class="order-status section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="lookup-card">
                        <h4 class="mb-2"><i class="bi bi-receipt"></i> <?= esc($business['name']) ?></h4>
                        <p class="mb-4"><?= lang('Business.order-status.intro') ?></p>
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i> <?= esc($error) ?></div>
                        <?php endif ?>
                        <form method="post" action="<?= esc($action, 'attr') ?>" novalidate>
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label" for="lookup-number"><?= lang('Business.order-status.number') ?></label>
                                <input type="text" class="form-control" id="lookup-number" name="number" value="<?= esc($number, 'attr') ?>"
                                       maxlength="24" required autocomplete="off" autocapitalize="characters" spellcheck="false" aria-describedby="lookup-number-hint">
                                <div class="note mt-1" id="lookup-number-hint"><?= lang('Business.order-status.number-hint') ?></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="lookup-contact"><?= lang('Business.order-status.contact') ?></label>
                                <input type="text" class="form-control" id="lookup-contact" name="contact" value="<?= esc($contact, 'attr') ?>"
                                       maxlength="64" required autocomplete="email" spellcheck="false">
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> <?= lang('Business.order-status.submit') ?></button>
                        </form>
                        <p class="note mt-4 mb-0"><i class="bi bi-shop"></i> <?= lang('Business.order-status.other-shop') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
