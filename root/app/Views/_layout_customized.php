<?php
/**
 * The layout of the pages a business designs itself (customizedPage.php): the business's colours and typefaces
 * (v1/mart/site's theme, Business::layout()), its menu of published pages, and the styles every section shares.
 *
 * @var string      $locale
 * @var string      $page_title
 * @var string      $business_name
 * @var string|null $meta_description
 * @var string|null $meta_keywords
 * @var array       $css              {fonts: {--*-font: value}, colors: {--*-color: value}}
 * @var string|null $google_fonts_url
 * @var array|null  $logo             {url, shape}
 * @var list<array> $menu             {name, url, current}: the published pages, the home page first
 * @var string      $system_home_url  /{business}/home, the system's home with the products and services
 * @var string      $business_slug
 * @var array       $site             v1/mart/site: business {name, description, contact, country_code, shows_staff}, languages
 * @var string|null $preview          'page' (a preview link) or 'design' (a design with sample texts), else null
 */
$faviconUrl = isset($logo['url']) && $logo['shape'] === 'S' ? $logo['url'] : assets_url('img/logo.webp');
?>
<!DOCTYPE html>
<html lang="<?= esc($locale, 'attr') ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= esc($page_title) ?><?= $page_title !== $business_name ? ' | ' . esc($business_name) : '' ?></title>
    <meta name="description" content="<?= esc($meta_description ?? '', 'attr') ?>">
    <meta name="keywords" content="<?= esc($meta_keywords ?? '', 'attr') ?>">
    <?php if ($preview !== null): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif ?>
    <!-- Favicons -->
    <link href="<?= esc($faviconUrl, 'attr') ?>" rel="icon">
    <link href="<?= esc($faviconUrl, 'attr') ?>" rel="apple-touch-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <?php if (isset($google_fonts_url)): ?>
        <link href="<?= esc($google_fonts_url, 'attr') ?>" rel="stylesheet">
    <?php else: ?>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <?php endif ?>
    <!-- Vendor CSS Files -->
    <link href="<?= assets_url('vendor/bootstrap5.3.8/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/bootstrap-icons1.13.1/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/aos/aos.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/glightbox/css/glightbox.min.css') ?>" rel="stylesheet">
    <link href="<?= assets_url('vendor/swiper11.2.10/swiper-bundle.min.css') ?>" rel="stylesheet">
    <!-- Main CSS File -->
    <link href="<?= assets_url('themes/mart-sites/css/main.css') ?>" rel="stylesheet">
    <!-- the business's colours and typefaces, after main.css so they win over its defaults -->
    <style>
        :root {<?php foreach ($css['fonts'] as $key => $value): ?><?= $key . ': ' . $value ?>;<?php endforeach ?>}
        :root {<?php foreach ($css['colors'] as $key => $value): ?><?= $key . ': ' . $value ?>;<?php endforeach ?>}
        /* main.css's .light-background is a fixed dark navy; a tint of the business's own colours instead, so the
           sections that alternate with it (about_01, testimonials_01, why_us_01) stay readable */
        .light-background {--surface-color: inherit; --background-color: color-mix(in srgb, var(--accent-color) 5%, var(--surface-color));}
        /* likewise main.css's header is fixed dark navy and its menu fixed light grey: the business's colours instead,
           so its name (--heading-color) and the menu stay readable whichever colours it picked */
        .header {--background-color: inherit; --surface-color: inherit;}
        :root {--nav-color: var(--default-color); --nav-hover-color: var(--accent-color); --nav-mobile-background-color: var(--surface-color);
            --nav-dropdown-background-color: var(--surface-color); --nav-dropdown-color: var(--default-color); --nav-dropdown-hover-color: var(--accent-color);}
        /* the footer of _layout_otternova.php (themes/mart/css/main.css's .footer), in place of main.css's own */
        .footer {padding-bottom: 0; border-top: 1px solid color-mix(in srgb, var(--default-color), transparent 90%);}
        .footer .footer-main {padding: 70px 0 40px;}
        .footer .footer-main .footer-widget {margin-bottom: 30px;}
        .footer .footer-main .footer-widget .logo {display: inline-block; margin-bottom: 20px; line-height: normal;}
        .footer .footer-main .footer-widget .logo span {color: var(--heading-color); font-size: 26px; font-weight: 700; letter-spacing: .5px; font-family: var(--heading-font);}
        .footer .footer-main .footer-widget .footer-logo {width: 40px; height: 40px; max-height: none; margin: 0; border-radius: 10px; object-fit: cover;}
        .footer .footer-main .footer-widget p {margin-bottom: 20px; line-height: 1.7;}
        .footer .footer-main .footer-about-text {max-width: 360px; color: color-mix(in srgb, var(--default-color), transparent 25%); font-size: 15px; font-family: inherit;}
        .footer .footer-main .footer-widget h4 {font-size: 18px; font-weight: 600; margin-bottom: 25px; padding-bottom: 0;}
        .footer .footer-main .footer-widget h4 a {color: inherit;}
        .footer .footer-main .footer-widget h4 a:hover {color: var(--accent-color);}
        .footer .footer-main .footer-links {list-style: none; padding: 0; margin: 0;}
        .footer .footer-main .footer-links li {margin-bottom: 12px;}
        .footer .footer-main .footer-links a {color: color-mix(in srgb, var(--default-color), transparent 30%); transition: all .3s ease; position: relative; display: inline-block;}
        .footer .footer-main .footer-links a:hover {color: var(--accent-color); padding-left: 5px;}
        .footer .footer-main .footer-links a:before {content: ""; position: absolute; width: 0; height: 1px; bottom: -2px; left: 0; background-color: var(--accent-color); transition: all .3s ease;}
        .footer .footer-main .footer-links a:hover:before {width: 100%;}
        .footer .footer-main .social-icons {display: flex; gap: 12px; flex-wrap: wrap;}
        .footer .footer-main .social-icons a {display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; font-size: 16px; transition: all .3s ease;
            background-color: color-mix(in srgb, var(--default-color), transparent 90%); color: var(--default-color);}
        .footer .footer-main .social-icons a:hover {background-color: var(--accent-color); transform: rotate(360deg); color: var(--contrast-color);}
        .footer .footer-main .footer-languages {display: flex; flex-wrap: wrap; align-items: center; gap: 6px;}
        .footer .footer-main .footer-languages i {color: var(--accent-color); margin-right: 4px;}
        .footer .footer-main .footer-languages a {padding: 3px 12px; border: 1px solid color-mix(in srgb, var(--default-color), transparent 80%); border-radius: 999px; font-size: 13px;
            color: color-mix(in srgb, var(--default-color), transparent 20%); transition: all .2s ease;}
        .footer .footer-main .footer-languages a:hover, .footer .footer-main .footer-languages a.active {border-color: var(--accent-color); color: var(--accent-color);}
        .footer .footer-main .footer-languages a.active {background-color: color-mix(in srgb, var(--accent-color), transparent 90%); font-weight: 600;}
        .footer .footer-main .footer-contact .contact-item {display: flex; align-items: flex-start; margin-bottom: 15px;}
        .footer .footer-main .footer-contact .contact-item i {color: var(--accent-color); font-size: 18px; margin-right: 12px; margin-top: 1px;}
        .footer .footer-main .footer-contact .contact-item a, .footer .footer-main .footer-contact .contact-item address {color: color-mix(in srgb, var(--default-color), transparent 25%); line-height: 1.5; word-break: break-word;}
        .footer .footer-main .footer-contact .contact-item a:hover {color: var(--accent-color);}
        .footer .footer-bottom {background-color: color-mix(in srgb, var(--default-color), transparent 95%); padding: 25px 0;}
        .footer .footer-bottom .copyright {padding: 0; background: none; font-size: 14px;}
        .footer .footer-bottom .copyright strong {color: color-mix(in srgb, var(--default-color), transparent 10%);}
        .footer .footer-bottom .legal-links {display: flex; flex-wrap: wrap; gap: 8px 20px; justify-content: center;}
        .footer .footer-bottom .legal-links a {color: color-mix(in srgb, var(--default-color), transparent 40%); font-size: 13px; transition: color .3s ease;}
        .footer .footer-bottom .legal-links a:hover {color: var(--accent-color);}
        .footer .footer-bottom .legal-links .powered-by {font-weight: 600;}
    </style>

    <!-- =======================================================
    * Template Name: Forma
    * Template URL: https://bootstrapmade.com/forma-bootstrap-agency-template/
    * Updated: Nov 08 2025 with Bootstrap v5.3.8
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>
<body>
<?php if ($preview !== null): ?>
    <div class="alert alert-warning text-center rounded-0 mb-0 py-2 small" role="status"><?= esc(lang('Business.customized.preview-' . $preview)) ?></div>
<?php endif ?>
<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="<?= esc($menu[0]['url'] ?? $system_home_url, 'attr') ?>" class="logo d-flex align-items-center">
            <?php if (isset($logo['url'])): ?>
                <img src="<?= esc($logo['url'], 'attr') ?>" alt="<?= esc($business_name, 'attr') ?>">
            <?php endif ?>
            <h1 class="sitename"><?= esc($business_name) ?></h1>
        </a>
        <nav id="navmenu" class="navmenu">
            <ul>
                <?php foreach ($menu as $item): ?>
                    <li><a href="<?= esc($item['url'], 'attr') ?>"<?= $item['current'] ? ' class="active" aria-current="page"' : '' ?>><?= esc($item['name']) ?></a></li>
                <?php endforeach ?>
                <li><a href="<?= esc($system_home_url, 'attr') ?>"><?= esc(lang('Business.customized.shop')) ?></a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>
<main class="main">
    <?= $this->renderSection('content') ?>
</main>
<?php
// the business's footer, as _layout_otternova.php's: who it is, its pages, its shop and order pages, how to reach it,
// and its languages
helper(['html', 'list']); // formatPhoneNumber(), socialLinksOf(), listSupportedSubdivisions()
$footerBusiness = is_array($site['business'] ?? null) ? $site['business'] : [];
$footerHomeUrl  = $menu[0]['url'] ?? $system_home_url;
$footerSlugUrl  = static fn (string $path): string => base_url(rawurlencode($business_slug) . '/' . $path);
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
$about        = trim((string) ($footerBusiness['description'] ?? $meta_description ?? ''));
$languages    = is_array($site['languages'] ?? null) ? $site['languages'] : [];
$socialLinks  = socialLinksOf($site);
?>
<footer id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-12">
                    <div class="footer-widget footer-about">
                        <a href="<?= esc($footerHomeUrl, 'attr') ?>" class="logo d-flex align-items-center gap-2">
                            <?php if (isset($logo['url']) && $logo['shape'] === 'S'): ?>
                                <img src="<?= esc($logo['url'], 'attr') ?>" alt="" class="footer-logo">
                            <?php endif ?>
                            <span class="sitename"><?= esc($business_name) ?></span>
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
                <?php if ($menu !== []): ?>
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="footer-widget">
                            <h4><?= esc($business_name) ?></h4>
                            <ul class="footer-links">
                                <?php foreach ($menu as $item): ?>
                                    <li><a href="<?= esc($item['url'], 'attr') ?>"<?= $item['current'] ? ' aria-current="page"' : '' ?>><?= esc($item['name']) ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
                <div class="col-lg-2 col-md-4 col-6<?= $menu === [] ? ' offset-lg-2' : '' ?>">
                    <div class="footer-widget">
                        <h4><?= lang('Business.footer.shop') ?></h4>
                        <ul class="footer-links">
                            <li><a href="<?= esc($system_home_url, 'attr') ?>"><?= lang('Business.customized.shop') ?></a></li>
                            <li><a href="<?= esc($footerSlugUrl('cart'), 'attr') ?>"><?= lang('Business.footer.cart') ?></a></li>
                            <?php if (! empty($footerBusiness['shows_staff'])): ?>
                                <li><a href="<?= esc($footerSlugUrl('staff'), 'attr') ?>"><?= lang('Business.staff-page.title') ?></a></li>
                            <?php endif ?>
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
                <p class="copyright mb-0">© <?= date('Y') ?> <strong><?= esc($business_name) ?></strong>. <?= lang('Business.footer.rights') ?></p>
                <div class="legal-links">
                    <a href="<?= esc(base_url(), 'attr') ?>" class="powered-by"><?= lang('Business.footer.powered-by', [lang('System.site-title')]) ?></a>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<!-- Preloader -->
<div id="preloader"></div>
<!-- Vendor JS Files -->
<script src="<?= assets_url('vendor/bootstrap5.3.8/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= assets_url('vendor/php-email-form/validate.js') ?>"></script>
<script src="<?= assets_url('vendor/aos/aos.js') ?>"></script>
<script src="<?= assets_url('vendor/glightbox/js/glightbox.min.js') ?>"></script>
<script src="<?= assets_url('vendor/purecounter1.5.0/purecounter_vanilla.js') ?>"></script>
<script src="<?= assets_url('vendor/imagesloaded/imagesloaded.pkgd.min.js') ?>"></script>
<script src="<?= assets_url('vendor/isotope-layout3.0.6/isotope.pkgd.min.js') ?>"></script>
<script src="<?= assets_url('vendor/swiper11.2.10/swiper-bundle.min.js') ?>"></script>
<!-- Main JS File -->
<script src="<?= assets_url('themes/mart-sites/js/main.js') ?>"></script>
</body>
</html>