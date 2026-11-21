<?php
/**
 * A shop that is not open on the mart yet (Business::site(), sent with 404): its name and logo, that it is not open,
 * and the way to the mart's search; customers with an order there are pointed to the link in their order email,
 * as the order pages still work
 *
 * @var string $business_name
 */
$this->extend('_layout_otternova');
?>
<?= $this->section('content') ?>
    <section class="section">
        <div class="container text-center py-5" style="max-width: 640px">
            <i class="bi bi-shop" style="font-size: 56px; color: var(--accent-color)" aria-hidden="true"></i>
            <h1 class="h2 mt-3"><?= esc(lang('Business.closed.heading', [$business_name])) ?></h1>
            <p class="mt-3"><?= lang('Business.closed.text') ?></p>
            <p class="small text-muted"><?= lang('Business.closed.orders') ?></p>
            <a class="btn btn-primary mt-3" href="<?= esc(base_url(), 'attr') ?>"><i class="bi bi-search"></i> <?= lang('Business.closed.browse') ?></a>
        </div>
    </section>
<?= $this->endSection() ?>
