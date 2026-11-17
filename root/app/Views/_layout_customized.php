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
            <?php else: ?>
                <h1 class="sitename"><?= esc($business_name) ?></h1>
            <?php endif ?>
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
<?= $this->renderSection('content') ?>
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