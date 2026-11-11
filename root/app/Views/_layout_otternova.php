<!DOCTYPE html>
<html lang="<?= $locale ?>">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= esc($page_title ?? lang('System.page.' . $slug)) ?> | <?= esc((isset($business_name) ? $business_name . ' | ' : '') . lang('System.site-title')) ?></title>
    <meta name="description" content="<?= esc($meta_description ?? '???', 'attr') ?>">
    <meta name="keywords" content="<?= esc($meta_keywords ?? '???', 'attr') ?>">
    <!-- Favicons -->
    <link href="<?= base_url('assets/img/logo.webp') ?>" rel="icon">
    <link href="<?= base_url('assets/img/logo.webp') ?>" rel="apple-touch-icon">
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
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/swiper/swiper-bundle.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/drift-zoom/drift-basic.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/glightbox/css/glightbox.min.css') ?>" rel="stylesheet">
    <!-- Main CSS File -->
    <style>
        :root {<?php foreach ($css['fonts'] as $key => $values) : ?><?= $key . ': ' . $values ?>;<?php endforeach; ?>}
        :root {<?php foreach ($css['colors'] as $key => $values) : ?><?= $key . ': ' . $values ?>;<?php endforeach; ?>}
    </style>
    <link href="<?= base_url('assets/css/main.css') ?>" rel="stylesheet">
    <!-- =======================================================
    * Template Name: ShopWise
    * Template URL: https://bootstrapmade.com/shopwise-bootstrap-ecommerce-template/
    * Updated: Apr 08 2026 with Bootstrap v5.3.8
    * Author: BootstrapMade.com
    * License: https://bootstrapmade.com/license/
    ======================================================== -->
</head>
<body class="index-page lang-<?= $locale ?>">
<header id="header" class="header position-relative">
    <!-- Main Header -->
    <div class="main-bar">
        <div class="container-fluid container-xl">
            <div class="row align-items-center gy-2">
                <!-- Logo -->
                <div class="col-auto">
                    <a href="index.html" class="logo d-flex align-items-center">
                        <!-- <img src="<?= base_url('assets/img/logo.webp') ?>" alt=""> -->
                        <i class="bi bi-cart2"></i>
                        <h1 class="sitename"><?= lang('System.site-title') ?></h1>
                    </a>
                </div>
                <!-- Search -->
                <div class="col d-none d-lg-block">
                    <form class="search-bar">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-field" placeholder="Search for products, brands, and more...">
                        <button class="search-submit" type="submit">Search</button>
                    </form>
                </div>

                <!-- Actions -->
                <div class="col-auto ms-auto ms-lg-0">
                    <div class="action-group d-flex align-items-center">

                        <!-- Mobile Search Toggle -->
                        <button class="action-btn mobile-search-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false" aria-controls="mobileSearch">
                            <i class="bi bi-search"></i>
                        </button>

                        <!-- Account -->
                        <div class="dropdown">
                            <button class="action-btn" data-bs-toggle="dropdown" aria-label="Account">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <div class="dropdown-menu account-flyout">
                                <div class="flyout-header">
                                    <h6>Welcome Back</h6>
                                    <p>Log in for a personalized experience</p>
                                </div>
                                <div class="flyout-actions">
                                    <a href="login.html" class="btn btn-primary-action">Log In</a>
                                    <a href="register.html" class="btn btn-outline-action">Register</a>
                                </div>
                                <div class="flyout-links">
                                    <a href="orders.html">
                                        <i class="bi bi-receipt"></i>
                                        <span>Order History</span>
                                    </a>
                                    <a href="wishlist.html">
                                        <i class="bi bi-bookmark-heart"></i>
                                        <span>Favorites</span>
                                    </a>
                                    <a href="returns.html">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Returns &amp; Exchanges</span>
                                    </a>
                                    <a href="help.html">
                                        <i class="bi bi-life-preserver"></i>
                                        <span>Support Center</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Cart -->
                        <div class="dropdown">
                            <button class="action-btn" data-bs-toggle="dropdown" aria-label="Cart">
                                <i class="bi bi-bag"></i>
                                <span class="badge-count">3</span>
                            </button>
                            <div class="dropdown-menu cart-flyout">
                                <div class="flyout-top">
                                    <h6>Your Bag</h6>
                                    <span class="items-label">3 items</span>
                                </div>
                                <div class="flyout-items">
                                    <!-- Cart Item 1 -->
                                    <div class="flyout-item">
                                        <div class="flyout-item-thumb">
                                            <img src="<?= base_url('assets/img/product/product-5.webp') ?>" alt="Product" class="img-fluid">
                                        </div>
                                        <div class="flyout-item-details">
                                            <h6>Woven Tote Handbag</h6>
                                            <span class="item-option">Beige / Medium</span>
                                            <div class="item-bottom">
                                                <span class="item-price">$89.00</span>
                                                <span class="item-qty">x1</span>
                                            </div>
                                        </div>
                                        <button class="item-dismiss" aria-label="Remove item">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div><!-- End Cart Item -->

                                    <!-- Cart Item 2 -->
                                    <div class="flyout-item">
                                        <div class="flyout-item-thumb">
                                            <img src="<?= base_url('assets/img/product/product-8.webp') ?>" alt="Product" class="img-fluid">
                                        </div>
                                        <div class="flyout-item-details">
                                            <h6>Slim Fit Denim Jacket</h6>
                                            <span class="item-option">Indigo / L</span>
                                            <div class="item-bottom">
                                                <span class="item-price">$145.00</span>
                                                <span class="item-qty">x1</span>
                                            </div>
                                        </div>
                                        <button class="item-dismiss" aria-label="Remove item">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div><!-- End Cart Item -->

                                    <!-- Cart Item 3 -->
                                    <div class="flyout-item">
                                        <div class="flyout-item-thumb">
                                            <img src="<?= base_url('assets/img/product/product-11.webp') ?>" alt="Product" class="img-fluid">
                                        </div>
                                        <div class="flyout-item-details">
                                            <h6>Canvas Low-Top Sneakers</h6>
                                            <span class="item-option">Off-White / 40</span>
                                            <div class="item-bottom">
                                                <span class="item-price">$68.00</span>
                                                <span class="item-qty">x1</span>
                                            </div>
                                        </div>
                                        <button class="item-dismiss" aria-label="Remove item">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div><!-- End Cart Item -->
                                </div>
                                <div class="flyout-bottom">
                                    <div class="subtotal-row">
                                        <span>Subtotal</span>
                                        <span class="subtotal-value">$302.00</span>
                                    </div>
                                    <a href="checkout.html" class="btn btn-proceed">Proceed to Checkout</a>
                                    <a href="cart.html" class="link-viewbag">View full bag →</a>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Navigation Toggle -->
                        <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Search Form -->
    <div class="collapse" id="mobileSearch">
        <div class="container-fluid container-xl">
            <form class="mobile-search">
                <div class="mobile-search-inner">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="What are you looking for?">
                </div>
            </form>
        </div>
    </div>
</header>
<?= $this->renderSection('content') ?>
<footer id="footer" class="footer">
    <div class="footer-main">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="footer-widget footer-about">
                        <a href="#" class="logo">
                            <span class="sitename">ShopWise</span>
                        </a>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam in nibh vehicula, facilisis magna ut, consectetur lorem.</p>
                        <div class="footer-contact mt-4">
                            <div class="contact-item">
                                <i class="bi bi-geo-alt"></i>
                                <span>123 Fashion Street, New York, NY 10001</span>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-telephone"></i>
                                <span>+1 (555) 123-4567</span>
                            </div>
                            <div class="contact-item">
                                <i class="bi bi-envelope"></i>
                                <span>hello@example.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h4>Shop</h4>
                        <ul class="footer-links">
                            <li><a href="category.html">New Arrivals</a></li>
                            <li><a href="category.html">Bestsellers</a></li>
                            <li><a href="category.html">Women's Clothing</a></li>
                            <li><a href="category.html">Men's Clothing</a></li>
                            <li><a href="category.html">Accessories</a></li>
                            <li><a href="category.html">Sale</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h4>Support</h4>
                        <ul class="footer-links">
                            <li><a href="support.html">Help Center</a></li>
                            <li><a href="account.html">Order Status</a></li>
                            <li><a href="shiping-info.html">Shipping Info</a></li>
                            <li><a href="return-policy.html">Returns &amp; Exchanges</a></li>
                            <li><a href="#">Size Guide</a></li>
                            <li><a href="contact.html">Contact Us</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h4>Company</h4>
                        <ul class="footer-links">
                            <li><a href="about.html">About Us</a></li>
                            <li><a href="about.html">Careers</a></li>
                            <li><a href="about.html">Press</a></li>
                            <li><a href="about.html">Affiliates</a></li>
                            <li><a href="about.html">Responsibility</a></li>
                            <li><a href="about.html">Investors</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-widget">
                        <h4>Download Our App</h4>
                        <p>Shop on the go with our mobile app</p>
                        <div class="app-buttons">
                            <a href="#" class="app-btn">
                                <i class="bi bi-apple"></i>
                                <span>App Store</span>
                            </a>
                            <a href="#" class="app-btn">
                                <i class="bi bi-google-play"></i>
                                <span>Google Play</span>
                            </a>
                        </div>
                        <div class="social-links mt-4">
                            <h5>Follow Us</h5>
                            <div class="social-icons">
                                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                                <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                                <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                                <a href="#" aria-label="Pinterest"><i class="bi bi-pinterest"></i></a>
                                <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">

            <div class="payment-methods d-flex align-items-center justify-content-center">
                <span>We Accept:</span>
                <div class="payment-icons">
                    <i class="bi bi-credit-card" aria-label="Credit Card"></i>
                    <i class="bi bi-paypal" aria-label="PayPal"></i>
                    <i class="bi bi-apple" aria-label="Apple Pay"></i>
                    <i class="bi bi-google" aria-label="Google Pay"></i>
                    <i class="bi bi-shop" aria-label="Shop Pay"></i>
                    <i class="bi bi-cash" aria-label="Cash on Delivery"></i>
                </div>
            </div>

            <div class="legal-links">
                <a href="tos.html">Terms of Service</a>
                <a href="privacy.html">Privacy Policy</a>
                <a href="tos.html">Cookies Settings</a>
            </div>

            <div class="copyright text-center">
                <p>© <span>Copyright</span> <strong class="sitename">ShopWise</strong>. All Rights Reserved.</p>
            </div>

        </div>

    </div>
</footer>
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
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/php-email-form/validate.js') ?>"></script>
<script src="<?= base_url('assets/vendor/swiper/swiper-bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/drift-zoom/Drift.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/glightbox/js/glightbox.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/purecounter/purecounter_vanilla.js') ?>"></script>
<script src="<?= getenv('LINK_API') ?>js/tracker.js" data-site="mart" data-business="<?= esc($business_slug ?? '', 'attr') ?>" defer></script>
<!-- Main JS File -->
<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>