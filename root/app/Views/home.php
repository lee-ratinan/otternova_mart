<?php
/**
 * OtterNovaMart's own home page (Home::index()): who lands here is mostly a shop's customer who followed the
 * "Powered by" link, or lost the link to their order; now and then a business owner looking at the platform.
 * It says what the mart is, what customers can do at a shop, how to get back to an order, and where owners go.
 * No listing of shops yet: that waits for an API endpoint of the businesses that chose to be listed.
 *
 * @var string $locale
 * @var string $www_url the Otternova site, for business owners
 */
$this->extend('_layout_otternova');
?>
<?= $this->section('content') ?>
    <style>
        .mart-home .hero { padding: 80px 0 72px; }
        .mart-home .eyebrow { display: inline-block; font-size: 13px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--accent-color); margin-bottom: 16px; }
        .mart-home .hero h1 { font-size: clamp(34px, 5vw, 52px); line-height: 1.1; margin-bottom: 20px; }
        .mart-home .hero .lead { font-size: 18px; max-width: 34em; margin-bottom: 32px; }
        .mart-home .btn-main { background: var(--accent-color); color: var(--contrast-color); border-radius: 8px; padding: 12px 24px; font-weight: 600; }
        .mart-home .btn-main:hover { background: color-mix(in srgb, var(--accent-color), #000 12%); color: var(--contrast-color); }
        .mart-home .btn-plain { color: var(--heading-color); padding: 12px 8px; font-weight: 600; }
        .mart-home .btn-plain:hover { color: var(--accent-color); }

        /* the hero's picture: what a shop page holds, drawn as three stacked cards */
        .mart-home .stack { position: relative; max-width: 420px; margin: 0 auto; }
        .mart-home .stack-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 88%); border-radius: 14px; padding: 18px 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 12px 32px color-mix(in srgb, var(--heading-color), transparent 92%); }
        .mart-home .stack-card + .stack-card { margin-top: 14px; }
        .mart-home .stack-card:nth-child(2) { margin-left: 32px; margin-right: -32px; }
        .mart-home .stack-card .icon { flex: 0 0 48px; height: 48px; border-radius: 12px; display: grid; place-items: center; font-size: 22px; background: color-mix(in srgb, var(--accent-color), transparent 88%); color: var(--accent-color); }
        .mart-home .stack-card strong { display: block; color: var(--heading-color); font-family: var(--heading-font); font-weight: 500; }
        .mart-home .stack-card span { font-size: 14px; }
        .mart-home .stack-card .tick { margin-left: auto; color: #1f9d55; font-size: 20px; }
        @media (max-width: 991px) { .mart-home .stack-card:nth-child(2) { margin-left: 0; margin-right: 0; } }

        .mart-home .section-head { max-width: 640px; margin-bottom: 40px; }
        .mart-home .section-head h2 { font-size: clamp(26px, 3.5vw, 34px); margin-bottom: 12px; }
        .mart-home .feature { height: 100%; padding: 28px; border-radius: 14px; border: 1px solid color-mix(in srgb, var(--default-color), transparent 88%); background: var(--surface-color); }
        .mart-home .feature i { font-size: 28px; color: var(--accent-color); }
        .mart-home .feature h3 { font-size: 20px; margin: 16px 0 8px; }
        .mart-home .feature p { margin: 0; }

        .mart-home .step { display: flex; gap: 18px; }
        .mart-home .step + .step { margin-top: 28px; }
        .mart-home .step .num { flex: 0 0 40px; height: 40px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; background: var(--heading-color); color: var(--contrast-color); }
        .mart-home .step h3 { font-size: 18px; margin: 6px 0 6px; }
        .mart-home .step p { margin: 0; }
        .mart-home .order-note { border-radius: 14px; padding: 28px; background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 88%); }
        .mart-home .order-note i { font-size: 28px; color: var(--accent-color); }
        .mart-home .order-note h3 { font-size: 20px; margin: 14px 0 8px; }

        .mart-home .owners { border-radius: 18px; padding: 48px; background: var(--heading-color); color: color-mix(in srgb, var(--contrast-color), transparent 20%); }
        .mart-home .owners h2 { color: var(--contrast-color); font-size: clamp(24px, 3vw, 32px); margin-bottom: 10px; }
        .mart-home .owners p { margin: 0; }
        @media (max-width: 575px) { .mart-home .owners { padding: 32px 24px; } }
    </style>
    <main class="main mart-home">
        <!-- What the mart is -->
        <section id="hero" class="hero light-background">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="eyebrow"><?= lang('System.home.hero.eyebrow') ?></span>
                        <h1><?= lang('System.home.hero.title') ?></h1>
                        <p class="lead"><?= lang('System.home.hero.lead') ?></p>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <a href="#orders" class="btn btn-main"><?= lang('System.home.hero.find-order') ?></a>
                            <a href="#owners" class="btn btn-plain"><?= lang('System.home.hero.for-owners') ?> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="stack" aria-hidden="true">
                            <div class="stack-card">
                                <div class="icon"><i class="bi bi-bag"></i></div>
                                <div><strong><?= lang('System.home.stack.product') ?></strong><span><?= lang('System.home.stack.product-note') ?></span></div>
                                <i class="bi bi-check-circle-fill tick"></i>
                            </div>
                            <div class="stack-card">
                                <div class="icon"><i class="bi bi-calendar2-check"></i></div>
                                <div><strong><?= lang('System.home.stack.booking') ?></strong><span><?= lang('System.home.stack.booking-note') ?></span></div>
                                <i class="bi bi-check-circle-fill tick"></i>
                            </div>
                            <div class="stack-card">
                                <div class="icon"><i class="bi bi-receipt"></i></div>
                                <div><strong><?= lang('System.home.stack.order') ?></strong><span><?= lang('System.home.stack.order-note') ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- What a customer can do at a shop -->
        <section id="what" class="section">
            <div class="container">
                <div class="section-head">
                    <h2><?= lang('System.home.what.title') ?></h2>
                    <p><?= lang('System.home.what.lead') ?></p>
                </div>
                <div class="row g-4">
                    <?php foreach ([['bi-bag', 'products'], ['bi-calendar2-check', 'appointments'], ['bi-people', 'sessions']] as [$icon, $key]): ?>
                        <div class="col-md-4">
                            <div class="feature">
                                <i class="bi <?= $icon ?>"></i>
                                <h3><?= lang('System.home.what.' . $key . '.title') ?></h3>
                                <p><?= lang('System.home.what.' . $key . '.text') ?></p>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </section>

        <!-- How to get back to an order: every order belongs to one shop, so the way back is through that shop -->
        <section id="orders" class="section light-background">
            <div class="container">
                <div class="row g-5 align-items-start">
                    <div class="col-lg-7">
                        <div class="section-head mb-4">
                            <h2><?= lang('System.home.orders.title') ?></h2>
                            <p><?= lang('System.home.orders.lead') ?></p>
                        </div>
                        <?php foreach (['email', 'status', 'history'] as $n => $key): ?>
                            <div class="step">
                                <div class="num"><?= $n + 1 ?></div>
                                <div>
                                    <h3><?= lang('System.home.orders.' . $key . '.title') ?></h3>
                                    <p><?= lang('System.home.orders.' . $key . '.text') ?></p>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="col-lg-5">
                        <div class="order-note">
                            <i class="bi bi-life-preserver"></i>
                            <h3><?= lang('System.home.orders.help.title') ?></h3>
                            <p class="mb-0"><?= lang('System.home.orders.help.text') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For business owners: the Otternova site is where they find out more -->
        <section id="owners" class="section">
            <div class="container">
                <div class="owners d-lg-flex align-items-center justify-content-between gap-4">
                    <div class="mb-4 mb-lg-0">
                        <h2><?= lang('System.home.owners.title') ?></h2>
                        <p><?= lang('System.home.owners.text') ?></p>
                    </div>
                    <a href="<?= esc($www_url, 'attr') ?>" class="btn btn-main flex-shrink-0"><?= lang('System.home.owners.cta') ?> <i class="bi bi-arrow-up-right ms-1"></i></a>
                </div>
            </div>
        </section>
    </main>
<?= $this->endSection() ?>
