<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= esc($page_title ?? lang('System.page.' . $slug)) ?> | <?= esc((isset($business_name) ? $business_name . ' | ' : '') . lang('System.site-title')) ?></title>
    <meta name="description" content="<?= esc($meta_description ?? '???', 'attr') ?>">
    <meta name="keywords" content="<?= esc($meta_keywords ?? '???', 'attr') ?>">
    <!-- Favicons -->
    <?php
    // the business's logo, when it has one; a wide one is too narrow for a favicon, so that keeps OtterNova's
    $logoUrl    = isset($logo['url']) ? $logo['url'] : assets_url('img/logo.webp');
    $faviconUrl = isset($logo['url']) && $logo['shape'] === 'S' ? $logo['url'] : assets_url('img/logo.webp');
    ?>
    <link href="<?= esc($faviconUrl, 'attr') ?>" rel="icon">
    <link href="<?= esc($faviconUrl, 'attr') ?>" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <?php if (isset($google_fonts_url)): ?>
        <link href="<?= esc($google_fonts_url, 'attr') ?>" rel="stylesheet">
    <?php elseif ('en-US' == $locale): ?>
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <?php elseif ('th' == $locale): ?>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Thai:wght@100..900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php endif; ?>
    <!-- Vendor CSS Files -->
    <link href="<?= assets_url('vendor/bootstrap5.3.8/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/bootstrap-icons1.13.1/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/swiper11.2.10/swiper-bundle.min.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/drift-zoom/drift-basic.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/glightbox/css/glightbox.min.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/aos/aos.css') ?>" rel="stylesheet">
    <!-- Main CSS File -->
    <style>
        :root {<?php foreach ($css['fonts'] as $key => $values) : ?><?= $key . ': ' . $values ?>;<?php endforeach; ?>}
        :root {<?php foreach ($css['colors'] as $key => $values) : ?><?= $key . ': ' . $values ?>;<?php endforeach; ?>}
    </style>
    <link href="<?= assets_url('themes/mart/css/main.css') ?>" rel="stylesheet">
    <!-- =======================================================
    * Template Name: ShopWise
    * Template URL: https://bootstrapmade.com/shopwise-bootstrap-ecommerce-template/
    * Updated: Apr 08 2026 with Bootstrap v5.3.8
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
    <script>
        // calls made before tracker.js has loaded are kept for it; see tracker.js
        window.OtternovaTracker = window.OtternovaTracker || { q: [] };
        OtternovaTracker.track = OtternovaTracker.track || function () { OtternovaTracker.q.push(arguments); };
        <?php foreach (array_merge(session()->getFlashdata('tracker_events') ?? [], $tracker_events ?? []) as [$eventName, $eventProps]): ?>
        OtternovaTracker.track(<?= json_encode($eventName, JSON_HEX_TAG | JSON_HEX_AMP) ?>, <?= json_encode($eventProps, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
        <?php endforeach ?>
    </script>
    <?php
    $homeUrl = base_url();
    if (isset($site['business'])) {
        $homeUrl = base_url(rawurlencode($site['business']['slug']));
    }
    // the search for businesses (Home::searchResults()): on the mart's own home page and its results, not a shop's pages
    $showSearch  = ! isset($business_slug) && in_array($slug ?? '', ['home', 'search'], true);
    $searchQuery = (string) ($search_query ?? '');
    ?>
</head>
<body class="index-page lang-<?= $locale ?>">
<header id="header" class="header position-relative">
    <!-- Main Header -->
    <div class="main-bar">
        <div class="container-fluid container-xl">
            <div class="row align-items-center gy-2">
                <!-- Logo -->
                <div class="col-auto">
                    <a href="<?= esc($homeUrl, 'attr') ?>" class="logo d-flex align-items-center">
                        <img src="<?= esc($logoUrl, 'attr') ?>" alt="<?= esc((isset($business_name) ? $business_name : lang('System.site-title'))) ?>">
                        <h1 class="sitename"><?= esc((isset($business_name) ? $business_name : lang('System.site-title'))) ?></h1>
                    </a>
                </div>
                <!-- Search -->
                <div class="col d-none d-lg-block">
                    <?php if ($showSearch): ?>
                    <form class="search-bar" action="<?= esc(base_url('search'), 'attr') ?>" method="get" role="search">
                        <i class="bi bi-search search-icon" aria-hidden="true"></i>
                        <input type="search" class="search-field" name="q" value="<?= esc($searchQuery, 'attr') ?>" maxlength="100" required
                               placeholder="<?= esc(lang('System.search.placeholder'), 'attr') ?>" aria-label="<?= esc(lang('System.search.label'), 'attr') ?>">
                        <button class="search-submit" type="submit"><?= lang('System.search.submit') ?></button>
                    </form>
                    <?php endif ?>
                </div>
                <!-- Actions -->
                <div class="col-auto ms-auto ms-lg-0">
                    <div class="action-group d-flex align-items-center">
                        <?php if ($showSearch): ?>
                        <!-- Mobile Search Toggle -->
                        <button class="action-btn mobile-search-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false" aria-controls="mobileSearch"
                                aria-label="<?= esc(lang('System.search.label'), 'attr') ?>">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php endif ?>
                        <?php if (isset($cart)): ?>
                        <!-- Cart -->
                        <div class="dropdown" id="header-cart">
                            <button class="action-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="<?= esc(lang('Business.cart.open', [$cart['summary']['items_label']]), 'attr') ?>">
                                <i class="bi bi-bag"></i>
                                <span class="badge-count"<?= $cart['summary']['items'] > 0 ? '' : ' hidden' ?>><?= $cart['summary']['items'] > 99 ? '99+' : $cart['summary']['items'] ?></span>
                            </button>
                            <div class="dropdown-menu cart-flyout">
                                <div class="flyout-top">
                                    <h6><?= lang('Business.cart.flyout-title') ?></h6>
                                    <span class="items-label"><?= esc($cart['summary']['items_label']) ?></span>
                                </div>
                                <p class="flyout-empty"<?= $cart['lines'] === [] ? '' : ' hidden' ?>><?= lang('Business.cart.empty') ?></p>
                                <template id="header-cart-remove">
                                    <form method="post" action="<?= esc($cart['urls']['remove'], 'attr') ?>" class="flyout-remove">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="line" value="">
                                        <button type="submit" class="item-dismiss"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </template>
                                <div class="flyout-items">
                                    <?php foreach ($cart['lines'] as $line): ?>
                                    <div class="flyout-item" data-line="<?= esc($line['id'], 'attr') ?>">
                                        <a href="<?= esc($line['url'], 'attr') ?>" class="flyout-item-thumb">
                                            <?php if ($line['image']): ?>
                                                <img src="<?= esc($line['image'], 'attr') ?>" alt="" class="img-fluid" loading="lazy">
                                            <?php else: ?>
                                                <i class="bi bi-image"></i>
                                            <?php endif ?>
                                        </a>
                                        <div class="flyout-item-details">
                                            <h6><a href="<?= esc($line['url'], 'attr') ?>"><?= esc($line['name']) ?></a></h6>
                                            <?php if ($line['tags'] !== []): ?>
                                                <span class="item-option"><?= esc(implode(' / ', $line['tags'])) ?></span>
                                            <?php endif ?>
                                            <?php if ($line['when']): ?>
                                                <span class="item-option"><i class="bi bi-calendar-event"></i> <?= esc($line['when']) ?></span>
                                            <?php endif ?>
                                            <div class="item-bottom">
                                                <span class="item-price"><?= esc($line['price']) ?></span>
                                                <span class="item-qty">×<?= $line['quantity'] ?></span>
                                            </div>
                                        </div>
                                        <form method="post" action="<?= esc($cart['urls']['remove'], 'attr') ?>" class="flyout-remove">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="line" value="<?= esc($line['id'], 'attr') ?>">
                                            <button type="submit" class="item-dismiss" aria-label="<?= esc(lang('Business.cart.remove') . ' ' . $line['name'], 'attr') ?>">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php endforeach ?>
                                </div>
                                <div class="flyout-bottom">
                                    <div class="subtotal-row"<?= $cart['lines'] === [] ? ' hidden' : '' ?>>
                                        <span><?= lang('Business.cart.subtotal') ?></span>
                                        <span class="subtotal-value"><?= esc($cart['summary']['subtotal']) ?></span>
                                    </div>
                                    <p class="flyout-note"<?= $cart['summary']['missing'] > 0 ? '' : ' hidden' ?>><?= esc($cart['summary']['missing_label']) ?></p>
                                    <!-- the checkout (the cart checked again first) until a participant's name is missing, then the cart, where it is added -->
                                    <form method="post" action="<?= esc($cart['urls']['checkout'], 'attr') ?>" class="flyout-checkout"<?= $cart['lines'] === [] || ! $cart['summary']['ready'] ? ' hidden' : '' ?>>
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-proceed w-100"><?= lang('Business.cart.proceed') ?></button>
                                    </form>
                                    <a href="<?= esc($cart['urls']['cart'], 'attr') ?>" class="btn btn-proceed flyout-names"<?= $cart['lines'] !== [] && ! $cart['summary']['ready'] ? '' : ' hidden' ?>><?= lang('Business.cart.proceed') ?></a>
                                    <a href="<?= esc($cart['urls']['cart'], 'attr') ?>" class="link-viewbag"><?= lang('Business.cart.view-cart') ?></a>
                                </div>
                            </div>
                        </div>
                        <?php endif ?>
                        <!-- Mobile Navigation Toggle -->
                        <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showSearch): ?>
    <!-- Mobile Search Form -->
    <div class="collapse" id="mobileSearch">
        <div class="container-fluid container-xl">
            <form class="mobile-search" action="<?= esc(base_url('search'), 'attr') ?>" method="get" role="search">
                <div class="mobile-search-inner">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="search" class="form-control" name="q" value="<?= esc($searchQuery, 'attr') ?>" maxlength="100" required
                           placeholder="<?= esc(lang('System.search.placeholder'), 'attr') ?>" aria-label="<?= esc(lang('System.search.label'), 'attr') ?>">
                </div>
            </form>
        </div>
    </div>
    <?php endif ?>
</header>
<?= $this->renderSection('content') ?>
<?php if (isset($business_slug, $site['business'])): ?>
<?php
// the business's footer: who it is, its order pages, how to reach it, and its languages
helper('html'); // formatPhoneNumber()
$footerBusiness = $site['business'];
$footerName     = (string) ($footerBusiness['name'] ?? $business_name ?? lang('System.site-title'));
$footerSlugUrl  = static fn (string $path = ''): string => base_url(rawurlencode($business_slug) . ($path === '' ? '' : '/' . $path));
$contact        = is_array($footerBusiness['contact'] ?? null) ? $footerBusiness['contact'] : [];
$contactValue   = static fn (string $key): string => trim((string) ($contact[$key] ?? ''));
$subdivision    = $contactValue('subdivision_code');
if ($subdivision !== '') {
    $subdivision = listSupportedSubdivisions((string) ($footerBusiness['country_code'] ?? ''))[$subdivision] ?? $subdivision;
}
$addressLines = array_values(array_filter([
    $contactValue('address_line_1'),
    $contactValue('address_line_2'),
    implode(' ', array_filter([$contactValue('city'), $subdivision, $contactValue('postal_code')], 'strlen')),
], 'strlen'));
$email   = $contactValue('email_address');
$phone   = $contactValue('phone_number');
$website = $contactValue('website');
if ($website !== '' && ! preg_match('#^https?://#i', $website)) {
    $website = 'https://' . $website;
}
$websiteLabel = preg_replace('#^https?://(www\.)?#i', '', rtrim($website, '/'));
$hasContact   = $email !== '' || $phone !== '' || $website !== '' || $addressLines !== [];
$about        = trim((string) ($footerBusiness['description'] ?? $site['meta']['description'] ?? ''));
$languages    = is_array($site['languages'] ?? null) ? $site['languages'] : [];
$socialLinks  = socialLinksOf($site);
?>
<footer id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-12">
                    <div class="footer-widget footer-about">
                        <a href="<?= esc($homeUrl, 'attr') ?>" class="logo d-flex align-items-center gap-2">
                            <?php if (isset($logo['url']) && $logo['shape'] === 'S'): ?>
                                <img src="<?= esc($logo['url'], 'attr') ?>" alt="" class="footer-logo">
                            <?php endif ?>
                            <span class="sitename"><?= esc($footerName) ?></span>
                        </a>
                        <?php if ($about !== ''): ?>
                            <p class="footer-about-text"><?= esc(mb_strimwidth($about, 0, 220, '…')) ?></p>
                        <?php endif ?>
                        <?php if ($socialLinks !== []): ?>
                            <nav class="social-icons mb-3" aria-label="<?= esc(lang('Business.footer.follow-us'), 'attr') ?>">
                                <?php foreach ($socialLinks as $socialLink): ?>
                                    <a href="<?= esc($socialLink['url'], 'attr') ?>" target="_blank" rel="noopener" title="<?= esc($socialLink['name'], 'attr') ?>"
                                       aria-label="<?= esc(lang('Business.footer.social-link', [$socialLink['name']]), 'attr') ?>"><i class="bi bi-<?= esc($socialLink['platform'], 'attr') ?>" aria-hidden="true"></i></a>
                                <?php endforeach ?>
                            </nav>
                        <?php endif ?>
                        <?php if (count($languages) > 1): ?>
                            <div class="footer-languages" aria-label="<?= esc(lang('Business.footer.language'), 'attr') ?>">
                                <i class="bi bi-translate"></i>
                                <?php foreach ($languages as $langRow): ?>
                                    <?php $isCurrent = ($langRow['code'] ?? '') === $locale; ?>
                                    <a href="?hl=<?= esc(rawurlencode((string) ($langRow['code'] ?? '')), 'attr') ?>" hreflang="<?= esc($langRow['code'] ?? '', 'attr') ?>"<?= $isCurrent ? ' class="active" aria-current="true"' : '' ?>><?= esc($langRow['name'] ?? $langRow['code'] ?? '') ?></a>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="footer-widget">
                        <h4><?= lang('Business.footer.shop') ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?= esc($homeUrl, 'attr') ?>"><?= lang('Business.footer.home') ?></a></li>
                            <li><a href="<?= esc($footerSlugUrl('cart'), 'attr') ?>"><?= lang('Business.footer.cart') ?></a></li>
                            <?php if (! empty($footerBusiness['shows_staff'])): ?>
                                <li><a href="<?= esc($footerSlugUrl('staff'), 'attr') ?>"><?= lang('Business.staff-page.title') ?></a></li>
                            <?php endif ?>
                            <li><a href="<?= esc($footerSlugUrl('contact-us'), 'attr') ?>"><?= lang('Business.contact-page.title') ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="footer-widget">
                        <h4><?= lang('Business.footer.your-orders') ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?= esc($footerSlugUrl('check-order-status'), 'attr') ?>"><?= lang('Business.order-status.title') ?></a></li>
                            <li><a href="<?= esc($footerSlugUrl('my-order-history'), 'attr') ?>"><?= lang('Business.order-history.title') ?></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="footer-widget footer-contact">
                        <h4><a href="<?= esc($footerSlugUrl('contact-us'), 'attr') ?>"><?= lang('Business.footer.contact-us') ?></a></h4>
                        <?php if (! $hasContact): ?>
                            <p class="text-muted small mb-0"><?= lang('Business.footer.no-contact') ?></p>
                        <?php endif ?>
                        <?php if ($addressLines !== []): ?>
                            <div class="contact-item">
                                <i class="bi bi-geo-alt"></i>
                                <address class="mb-0"><?= implode('<br>', array_map('esc', $addressLines)) ?></address>
                            </div>
                        <?php endif ?>
                        <?php if ($phone !== ''): ?>
                            <div class="contact-item">
                                <i class="bi bi-telephone"></i>
                                <a href="tel:<?= esc(preg_replace('/[^\d+]/', '', $phone), 'attr') ?>"><?= esc(formatPhoneNumber($phone)) ?></a>
                            </div>
                        <?php endif ?>
                        <?php if ($email !== ''): ?>
                            <div class="contact-item">
                                <i class="bi bi-envelope"></i>
                                <a href="mailto:<?= esc($email, 'attr') ?>"><?= esc($email) ?></a>
                            </div>
                        <?php endif ?>
                        <?php if ($website !== ''): ?>
                            <div class="contact-item">
                                <i class="bi bi-globe2"></i>
                                <a href="<?= esc($website, 'attr') ?>" target="_blank" rel="noopener"><?= esc($websiteLabel) ?></a>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <p class="copyright mb-0">© <?= date('Y') ?> <strong><?= esc($footerName) ?></strong>. <?= lang('Business.footer.rights') ?></p>
                <div class="legal-links">
                    <a href="<?= esc(base_url('terms-of-service'), 'attr') ?>"><?= lang('Business.footer.terms') ?></a>
                    <a href="<?= esc(base_url('privacy-policy'), 'attr') ?>"><?= lang('Business.footer.privacy') ?></a>
                    <a href="<?= esc($footerSlugUrl('report'), 'attr') ?>" rel="nofollow"><?= lang('Business.report-page.report-business') ?></a>
                    <a href="<?= esc(base_url(), 'attr') ?>" class="powered-by"><?= lang('Business.footer.powered-by', [lang('System.site-title')]) ?></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php else: ?>
<footer id="footer" class="footer">
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
                <p class="copyright mb-0">© <?= date('Y') ?> <strong><?= esc(lang('System.site-title')) ?></strong>. <?= lang('Business.footer.rights') ?></p>
                <div class="legal-links">
                    <a href="<?= esc(base_url('terms-of-service'), 'attr') ?>"><?= lang('Business.footer.terms') ?></a>
                    <a href="<?= esc(base_url('privacy-policy'), 'attr') ?>"><?= lang('Business.footer.privacy') ?></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php endif ?>
<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<!-- Ribbon -->
<?php $env = getenv('CI_ENVIRONMENT'); ?>
<?php if (in_array($env, ['beta', 'development'])): ?>
    <?php if ('development' == $env) { $env = 'dev'; } ?>
    <div class="corner-ribbon">
        <span><?= esc($env) ?></span>
    </div>
<?php endif; ?>
<!-- Vendor JS Files -->
<script src="<?= assets_url('vendor/bootstrap5.3.8/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= assets_url('vendor/swiper11.2.10/swiper-bundle.min.js') ?>"></script>
<script src="<?= assets_url('vendor/drift-zoom/Drift.min.js') ?>"></script>
<script src="<?= assets_url('vendor/glightbox/js/glightbox.min.js') ?>"></script>
<script src="<?= assets_url('vendor/purecounter1.5.0/purecounter_vanilla.js') ?>"></script>
<script src="<?= assets_url('vendor/aos/aos.js') ?>"></script>
<script src="<?= getenv('LINK_API') ?>js/tracker.js" data-site="mart" data-business="<?= esc($business_slug ?? '', 'attr') ?>" defer></script>
<script src="<?= assets_url('themes/mart/js/main.js') ?>"></script>
<?php if (isset($cart)): ?>
<script>
    // the header's cart: redrawn from every cart answer (a page sends them as "cart:changed"), and its remove buttons
    (function () {
        const root = document.getElementById('header-cart');
        const urls = <?= json_encode($cart['urls'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
        const text = <?= json_encode(['open' => lang('Business.cart.open', ['{0}']), 'remove' => lang('Business.cart.remove')], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
        const pick = (selector) => root.querySelector(selector);
        const make = (tag, className, content) => {
            const element = document.createElement(tag);
            if (className) element.className = className;
            if (content !== undefined) element.textContent = content;
            return element;
        };
        const template = document.getElementById('header-cart-remove').content.firstElementChild;

        const item = (line) => {
            const row = make('div', 'flyout-item');
            row.dataset.line = line.id;
            const thumb = make('a', 'flyout-item-thumb');
            thumb.href = line.url;
            if (line.image) {
                const image = make('img', 'img-fluid');
                Object.assign(image, { src: line.image, alt: '', loading: 'lazy' });
                thumb.append(image);
            } else {
                thumb.append(make('i', 'bi bi-image'));
            }
            const details = make('div', 'flyout-item-details');
            const title = make('h6');
            const link = make('a', '', line.name);
            link.href = line.url;
            title.append(link);
            details.append(title);
            if (line.tags.length) details.append(make('span', 'item-option', line.tags.join(' / ')));
            if (line.when) {
                const when = make('span', 'item-option', ' ' + line.when);
                when.prepend(make('i', 'bi bi-calendar-event'));
                details.append(when);
            }
            const bottom = make('div', 'item-bottom');
            bottom.append(make('span', 'item-price', line.price), make('span', 'item-qty', '×' + line.quantity));
            details.append(bottom);
            row.append(thumb, details);
            const form = template.cloneNode(true);
            form.querySelector('[name="line"]').value = line.id;
            form.querySelector('button').setAttribute('aria-label', text.remove + ' ' + line.name);
            row.append(form);
            return row;
        };

        const draw = (summary, lines) => {
            if (!summary) return;
            const empty = summary.lines === 0;
            const badge = pick('.badge-count');
            badge.textContent = summary.items > 99 ? '99+' : summary.items;
            badge.hidden = summary.items === 0;
            pick('.action-btn').setAttribute('aria-label', text.open.replace('{0}', summary.items_label));
            pick('.items-label').textContent = summary.items_label;
            pick('.flyout-empty').hidden = !empty;
            pick('.subtotal-row').hidden = empty;
            pick('.subtotal-value').textContent = summary.subtotal;
            pick('.flyout-note').textContent = summary.missing_label;
            pick('.flyout-note').hidden = summary.missing === 0;
            pick('.flyout-checkout').hidden = empty || !summary.ready;
            pick('.flyout-names').hidden = empty || summary.ready;
            if (Array.isArray(lines)) pick('.flyout-items').replaceChildren(...lines.map(item));
        };

        document.addEventListener('cart:changed', (event) => {
            draw(event.detail.summary, event.detail.lines);
            (event.detail.track || []).forEach(([name, props]) => window.OtternovaTracker.track(name, props));
        });

        root.addEventListener('submit', (event) => {
            const form = event.target.closest('.flyout-remove');
            if (!form) return;
            event.preventDefault();
            form.querySelector('button').disabled = true;
            fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.json())
                .then((answer) => document.dispatchEvent(new CustomEvent('cart:changed', { detail: Object.assign({ source: 'flyout' }, answer) })))
                .catch(() => { form.querySelector('button').disabled = false; });
        });
    })();
</script>
<?php endif ?>
<script>AOS.init();</script>
</body>
</html>