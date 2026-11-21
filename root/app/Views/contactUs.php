<?php
/**
 * Contact us (Business::contactUs()): how to reach the business, then each of its stores, the ones people can visit
 * first, with the address, the telephone number and the way there on Google Maps
 *
 * @var array  $site
 * @var string $home_url
 * @var array  $business_contact {address: list<string>, phone: {label, tel}|null, email: string, website: {url, label}|null}
 * @var list<array{name: string, online: bool, address: list<string>, phone: array|null, maps_url: string|null}> $stores
 * @var array  $contact_form {token: string|null, action, sent: string|null, error: string|null, errors: array<string, string>, old: array<string, string>}
 *             The contact form (Business::contactForm()): to the business's messages, through Business::sendMessage()
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$reach    = $business_contact;
$social   = socialLinksOf($site);
$form     = $contact_form;
$old      = static fn (string $field): string => (string) ($form['old'][$field] ?? '');
$invalid  = static fn (string ...$fields): string => array_intersect($fields, array_keys($form['errors'])) !== [] ? ' is-invalid' : '';
$feedback = static fn (string $field): string => isset($form['errors'][$field]) ? '<div class="invalid-feedback d-block">' . esc($form['errors'][$field]) . '</div>' : '';
?>
<?= $this->section('content') ?>
    <style>
        .contact-us .reach-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 14px; padding: 28px; height: 100%; }
        .contact-us .reach-item { display: flex; gap: 14px; align-items: flex-start; padding: 12px 0; border-bottom: 1px dashed color-mix(in srgb, var(--default-color), transparent 88%); }
        .contact-us .reach-item:last-child { border-bottom: 0; padding-bottom: 0; }
        .contact-us .reach-item .icon { flex: 0 0 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;
            background: color-mix(in srgb, var(--accent-color), transparent 88%); color: var(--accent-color); font-size: 18px; }
        .contact-us .reach-item .label { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: color-mix(in srgb, var(--default-color), transparent 45%); }
        .contact-us .reach-item a, .contact-us .reach-item address { color: var(--default-color); word-break: break-word; margin: 0; }
        .contact-us .reach-item a:hover { color: var(--accent-color); }
        .contact-us .social-list { list-style: none; padding: 0; margin: 4px 0 0; display: flex; flex-wrap: wrap; gap: 8px; }
        .contact-us .social-list a { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: 14px;
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); }
        .contact-us .social-list a:hover { background: var(--accent-color); border-color: var(--accent-color); color: var(--contrast-color); }
        .contact-us .store-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 14px; padding: 22px;
            height: 100%; display: flex; flex-direction: column; transition: box-shadow .2s ease, transform .2s ease; }
        .contact-us .store-card:hover { box-shadow: 0 10px 28px color-mix(in srgb, var(--default-color), transparent 90%); transform: translateY(-2px); }
        .contact-us .store-card h5 { font-size: 18px; margin-bottom: 4px; }
        .contact-us .store-card .store-type { font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 45%); margin-bottom: 14px; }
        .contact-us .store-card .store-line { display: flex; gap: 10px; margin-bottom: 10px; color: color-mix(in srgb, var(--default-color), transparent 20%); }
        .contact-us .store-card .store-line i { color: var(--accent-color); margin-top: 2px; }
        .contact-us .store-card .store-line a { color: inherit; }
        .contact-us .store-card .store-line a:hover { color: var(--accent-color); }
        .contact-us .store-card .muted { color: color-mix(in srgb, var(--default-color), transparent 50%); font-style: italic; }
        .contact-us .store-card .store-actions { margin-top: auto; padding-top: 12px; display: flex; flex-wrap: wrap; gap: 8px; }
        .contact-us .store-card .btn-store { border: 1px solid var(--accent-color); color: var(--accent-color); border-radius: 999px; padding: 6px 16px; font-size: 14px; }
        .contact-us .store-card .btn-store:hover, .contact-us .store-card .btn-store.primary { background: var(--accent-color); color: var(--contrast-color); }
        .contact-us .store-card .btn-store.primary:hover { filter: brightness(.92); }
        .contact-us .message-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 14px; padding: 28px; margin-bottom: 32px; scroll-margin-top: 100px; }
        .contact-us .message-card .form-hint { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .contact-us .message-card .btn-send { background: var(--accent-color); color: var(--contrast-color); border: 0; border-radius: 999px; padding: 10px 28px; }
        .contact-us .message-card .btn-send:hover { filter: brightness(.92); }
        .contact-us .message-card .btn-send:disabled { opacity: .65; }
        /* the honeypot: out of sight and out of the tab order, so only a bot fills it in */
        .contact-us .message-card .contact-website { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.contact-page.title') ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($home_url, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li class="current"><?= lang('Business.contact-page.title') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="contact-us" class="contact-us section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="reach-card">
                        <h4 class="mb-1"><?= esc($business['name']) ?></h4>
                        <p class="mb-3"><?= lang('Business.contact-page.intro') ?></p>
                        <?php if ($reach['phone']): ?>
                            <div class="reach-item">
                                <span class="icon"><i class="bi bi-telephone"></i></span>
                                <div><span class="label"><?= lang('Business.contact-page.call') ?></span>
                                    <a href="tel:<?= esc($reach['phone']['tel'], 'attr') ?>"><?= esc($reach['phone']['label']) ?></a></div>
                            </div>
                        <?php endif ?>
                        <?php if ($reach['email'] !== ''): ?>
                            <div class="reach-item">
                                <span class="icon"><i class="bi bi-envelope"></i></span>
                                <div><span class="label"><?= lang('Business.contact-page.email') ?></span>
                                    <a href="mailto:<?= esc($reach['email'], 'attr') ?>"><?= esc($reach['email']) ?></a></div>
                            </div>
                        <?php endif ?>
                        <?php if ($reach['website']): ?>
                            <div class="reach-item">
                                <span class="icon"><i class="bi bi-globe2"></i></span>
                                <div><span class="label"><?= lang('Business.contact-page.website') ?></span>
                                    <a href="<?= esc($reach['website']['url'], 'attr') ?>" target="_blank" rel="noopener"><?= esc($reach['website']['label']) ?></a></div>
                            </div>
                        <?php endif ?>
                        <?php if ($reach['address'] !== []): ?>
                            <div class="reach-item">
                                <span class="icon"><i class="bi bi-building"></i></span>
                                <div><span class="label"><?= lang('Business.contact-page.address') ?></span>
                                    <address><?= implode('<br>', array_map('esc', $reach['address'])) ?></address></div>
                            </div>
                        <?php endif ?>
                        <?php if ($social !== []): ?>
                            <div class="reach-item">
                                <span class="icon"><i class="bi bi-share"></i></span>
                                <div class="flex-grow-1"><span class="label"><?= lang('Business.contact-page.follow') ?></span>
                                    <ul class="social-list">
                                        <?php foreach ($social as $link): ?>
                                            <li><a href="<?= esc($link['url'], 'attr') ?>" target="_blank" rel="noopener"
                                                   aria-label="<?= esc(lang('Business.footer.social-link', [$link['name']]), 'attr') ?>">
                                                    <i class="bi bi-<?= esc($link['platform'], 'attr') ?>" aria-hidden="true"></i><?= esc($link['name']) ?></a></li>
                                        <?php endforeach ?>
                                    </ul></div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="message-card" id="contact-form">
                        <h4 class="mb-1"><?= lang('Business.contact-page.form-title') ?></h4>
                        <p class="mb-3"><?= lang('Business.contact-page.form-intro') ?></p>
                        <?php if ($form['sent'] !== null): ?>
                            <div class="alert alert-success" role="status"><i class="bi bi-check-circle"></i> <?= esc($form['sent']) ?></div>
                        <?php endif ?>
                        <?php if ($form['error'] !== null): ?>
                            <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i> <?= esc($form['error']) ?></div>
                        <?php endif ?>
                        <?php if ($form['token'] === null): ?>
                            <p class="text-muted mb-0"><?= lang('Business.contact-page.form-unavailable') ?></p>
                        <?php else: ?>
                            <form method="post" action="<?= esc($form['action'], 'attr') ?>" class="contact-message-form" novalidate>
                                <?= csrf_field() ?>
                                <input type="hidden" name="form_token" value="<?= esc($form['token'], 'attr') ?>">
                                <div class="contact-website" aria-hidden="true">
                                    <label for="contact-website"><?= lang('Business.contact-page.form-leave-empty') ?></label>
                                    <input type="text" id="contact-website" name="website" value="" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="contact-name" class="form-label"><?= lang('Business.contact-page.form-name') ?></label>
                                    <input type="text" class="form-control<?= $invalid('name') ?>" id="contact-name" name="name" maxlength="100" required autocomplete="name"
                                           value="<?= esc($old('name'), 'attr') ?>">
                                    <?= $feedback('name') ?>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="contact-email" class="form-label"><?= lang('Business.contact-page.form-email') ?></label>
                                        <input type="email" class="form-control<?= $invalid('email', 'contact') ?>" id="contact-email" name="email" maxlength="128" autocomplete="email"
                                               value="<?= esc($old('email'), 'attr') ?>" aria-describedby="contact-reach-hint">
                                        <?= $feedback('email') ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact-phone" class="form-label"><?= lang('Business.contact-page.form-phone') ?></label>
                                        <input type="tel" class="form-control<?= $invalid('phone', 'contact') ?>" id="contact-phone" name="phone" maxlength="32" autocomplete="tel"
                                               value="<?= esc($old('phone'), 'attr') ?>" aria-describedby="contact-reach-hint">
                                        <?= $feedback('phone') ?>
                                    </div>
                                </div>
                                <div id="contact-reach-hint" class="form-hint mt-1 mb-3"><?= lang('Business.contact-page.form-reach-hint') ?></div>
                                <?= $feedback('contact') ?>
                                <div class="mb-3">
                                    <label for="contact-message" class="form-label"><?= lang('Business.contact-page.form-message') ?></label>
                                    <textarea class="form-control<?= $invalid('message') ?>" id="contact-message" name="message" rows="5" maxlength="5000" required><?= esc($old('message')) ?></textarea>
                                    <?= $feedback('message') ?>
                                </div>
                                <?= $feedback('locale') ?>
                                <button type="submit" class="btn btn-send"><i class="bi bi-send"></i> <?= lang('Business.contact-page.form-send') ?></button>
                            </form>
                        <?php endif ?>
                    </div>
                    <h4 class="mb-3"><?= lang('Business.contact-page.locations') ?></h4>
                    <?php if ($stores === []): ?>
                        <p class="text-muted"><?= lang('Business.contact-page.no-stores') ?></p>
                    <?php endif ?>
                    <div class="row g-3">
                        <?php foreach ($stores as $store): ?>
                            <div class="col-md-6">
                                <div class="store-card">
                                    <h5><?= esc($store['name']) ?></h5>
                                    <?php if ($store['online']): ?>
                                        <div class="store-type"><i class="bi bi-cloud"></i> <?= lang('Business.contact-page.online-store') ?></div>
                                    <?php else: ?>
                                        <div class="mb-3"></div>
                                    <?php endif ?>
                                    <?php if (! $store['online']): ?>
                                        <div class="store-line">
                                            <i class="bi bi-geo-alt"></i>
                                            <?php if ($store['address'] !== []): ?>
                                                <address class="mb-0"><?= implode('<br>', array_map('esc', $store['address'])) ?></address>
                                            <?php else: ?>
                                                <span class="muted"><?= lang('Business.contact-page.no-address') ?></span>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($store['phone']): ?>
                                        <div class="store-line">
                                            <i class="bi bi-telephone"></i>
                                            <a href="tel:<?= esc($store['phone']['tel'], 'attr') ?>"><?= esc($store['phone']['label']) ?></a>
                                        </div>
                                    <?php endif ?>
                                    <?php if ($store['maps_url'] || $store['phone']): ?>
                                        <div class="store-actions">
                                            <?php if ($store['maps_url']): ?>
                                                <a class="btn btn-store primary" href="<?= esc($store['maps_url'], 'attr') ?>" target="_blank" rel="noopener">
                                                    <i class="bi bi-map"></i> <?= lang('Business.contact-page.directions') ?>
                                                </a>
                                            <?php endif ?>
                                            <?php if ($store['phone']): ?>
                                                <a class="btn btn-store" href="tel:<?= esc($store['phone']['tel'], 'attr') ?>"><i class="bi bi-telephone"></i> <?= lang('Business.contact-page.call') ?></a>
                                            <?php endif ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php if ($form['token'] !== null): ?>
        <script>
            // an email address or a telephone number before sending, as the business needs one to answer; then one send only
            document.querySelectorAll('.contact-message-form').forEach((form) => {
                const reach = [form.elements.email, form.elements.phone];
                form.addEventListener('input', (event) => {
                    event.target.classList.remove('is-invalid');
                    if (reach.includes(event.target)) {
                        reach.forEach((field) => field.classList.remove('is-invalid'));
                    }
                });
                form.addEventListener('submit', (event) => {
                    const fields = [form.elements.name, form.elements.message];
                    fields.forEach((field) => field.classList.toggle('is-invalid', field.value.trim() === ''));
                    const noReach = reach.every((field) => field.value.trim() === '');
                    reach.forEach((field) => field.classList.toggle('is-invalid', noReach || !field.checkValidity()));
                    if (fields.some((field) => field.value.trim() === '') || noReach || reach.some((field) => !field.checkValidity())) {
                        event.preventDefault();
                        return;
                    }
                    form.querySelector('button[type="submit"]').disabled = true;
                });
            });
        </script>
    <?php endif ?>
<?= $this->endSection() ?>
