<?php $this->extend('_layout_otternova'); ?>
<?= $this->section('content') ?>
    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section light-background">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <div class="intro-content">
                            <span class="badge-label">Curated Selection</span>
                            <h1 class="headline">Discover What Defines Modern Living</h1>
                            <p class="subtext">Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
                                cubilia curae. Donec velit neque, auctor sit amet aliquam vel.</p>
                            <div class="action-group">
                                <a href="category.html" class="btn-primary-action">Browse Items</a>
                                <a href="category.html" class="btn-ghost-action"><i class="bi bi-arrow-right me-2"></i>See
                                    All Categories</a>
                            </div>
                            <div class="trust-indicators">
                                <div class="indicator">
                                    <i class="bi bi-truck"></i>
                                    <span>Free Shipping</span>
                                </div>
                                <div class="indicator">
                                    <i class="bi bi-shield-check"></i>
                                    <span>Verified Quality</span>
                                </div>
                                <div class="indicator">
                                    <i class="bi bi-arrow-return-left"></i>
                                    <span>Easy Returns</span>
                                </div>
                                <div class="indicator">
                                    <i class="bi bi-chat-dots"></i>
                                    <span>24/7 Support</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="product-tile">
                                    <div class="tile-image">
                                        <img src="<?= base_url('assets/img/product/product-6.webp') ?>"
                                             class="img-fluid" alt="Product">
                                        <span class="tile-badge">Best Seller</span>
                                    </div>
                                    <div class="tile-info">
                                        <h4>Precision Audio Hub</h4>
                                        <div class="tile-price">
                                            <span class="current">$219</span>
                                            <span class="original">$299</span>
                                        </div>
                                    </div>
                                </div><!-- End Product Tile -->
                            </div>
                            <div class="col-md-6">
                                <div class="product-tile featured">
                                    <div class="tile-image">
                                        <img src="<?= base_url('assets/img/product/product-3.webp') ?>"
                                             class="img-fluid" alt="Product">
                                        <span class="tile-badge accent">Trending Now</span>
                                    </div>
                                    <div class="tile-info">
                                        <h4>Smart Wearable Pro</h4>
                                        <div class="tile-price">
                                            <span class="current">$159</span>
                                            <span class="original">$229</span>
                                        </div>
                                    </div>
                                </div><!-- End Product Tile -->
                            </div>
                            <div class="col-12">
                                <div class="product-tile horizontal">
                                    <div class="row g-0 align-items-center">
                                        <div class="col-sm-4">
                                            <div class="tile-image">
                                                <img src="<?= base_url('assets/img/product/product-10.webp') ?>"
                                                     class="img-fluid" alt="Product">
                                                <span class="tile-badge">Just Launched</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="tile-info">
                                                <h4>Essential Daily Companion</h4>
                                                <p class="tile-desc">Proin eget tortor risus. Vivamus magna justo,
                                                    lacinia eget consectetur sed, convallis at tellus curabitur.</p>
                                                <div class="tile-price">
                                                    <span class="current">$99</span>
                                                    <span class="original">$149</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- End Product Tile -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="slider-section">

                    <div
                        class="product-carousel swiper init-swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                        <script type="application/json" class="swiper-config">
                            {
                                "loop": true,
                                "speed": 500,
                                "autoplay": {
                                    "delay": 4000,
                                    "disableOnInteraction": true
                                },
                                "slidesPerView": 1,
                                "spaceBetween": 16,
                                "breakpoints": {
                                    "576": {
                                        "slidesPerView": 2,
                                        "spaceBetween": 16
                                    },
                                    "768": {
                                        "slidesPerView": 3,
                                        "spaceBetween": 16
                                    },
                                    "1200": {
                                        "slidesPerView": 4,
                                        "spaceBetween": 24
                                    }
                                },
                                "pagination": {
                                    "el": ".swiper-pagination",
                                    "type": "bullets",
                                    "clickable": true
                                },
                                "navigation": {
                                    "nextEl": ".swiper-button-next",
                                    "prevEl": ".swiper-button-prev"
                                }
                            }
                        </script>
                        <div class="swiper-wrapper" id="swiper-wrapper-0f8681ceaf4722b7" aria-live="off"
                             style="transition-duration: 0ms; transform: translate3d(-660px, 0px, 0px); transition-delay: 0ms;">

                            <div class="swiper-slide" style="width: 306px; margin-right: 24px;" role="group"
                                 aria-label="2 / 6" data-swiper-slide-index="1">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-5.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                        <span class="slide-badge accent">Popular</span>
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Ergonomic Desk Lamp</h4>
                                        <p>Curabitur aliquet quam id dui posuere blandit.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$64</span>
                                            <span class="price-was">$85</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                            <div class="swiper-slide swiper-slide-prev" style="width: 306px; margin-right: 24px;"
                                 role="group" aria-label="3 / 6" data-swiper-slide-index="2">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-8.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Ceramic Aroma Diffuser</h4>
                                        <p>Pellentesque in ipsum id orci porta dapibus.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$42</span>
                                            <span class="price-was">$58</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                            <div class="swiper-slide swiper-slide-active" style="width: 306px; margin-right: 24px;"
                                 role="group" aria-label="4 / 6" data-swiper-slide-index="3">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-2.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                        <span class="slide-badge">Sale</span>
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Minimalist Wall Clock</h4>
                                        <p>Mauris blandit aliquet elit eget tincidunt nibh.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$37</span>
                                            <span class="price-was">$55</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                            <div class="swiper-slide swiper-slide-next" style="width: 306px; margin-right: 24px;"
                                 role="group" aria-label="5 / 6" data-swiper-slide-index="4">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-9.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                        <span class="slide-badge accent">Hot</span>
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Wireless Charging Pad</h4>
                                        <p>Vivamus suscipit tortor eget felis porttitor.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$29</span>
                                            <span class="price-was">$45</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                            <div class="swiper-slide" style="width: 306px; margin-right: 24px;" role="group"
                                 aria-label="6 / 6" data-swiper-slide-index="5">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-4.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Portable Power Station</h4>
                                        <p>Donec sollicitudin molestie malesuada cras.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$175</span>
                                            <span class="price-was">$240</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                            <div class="swiper-slide" style="width: 306px; margin-right: 24px;" role="group"
                                 aria-label="1 / 6" data-swiper-slide-index="0">
                                <div class="slide-card">
                                    <div class="slide-card-image">
                                        <img src="<?= base_url('assets/img/product/product-1.webp') ?>"
                                             class="img-fluid" loading="lazy" alt="Product">
                                        <span class="slide-badge">New</span>
                                    </div>
                                    <div class="slide-card-body">
                                        <h4>Urban Tech Backpack</h4>
                                        <p>Nulla porttitor accumsan tincidunt sed lectus.</p>
                                        <div class="slide-card-price">
                                            <span class="price-now">$89</span>
                                            <span class="price-was">$120</span>
                                        </div>
                                    </div>
                                </div><!-- End Slide Card -->
                            </div>
                        </div>
                        <div
                            class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                            <span class="swiper-pagination-bullet" tabindex="0" role="button"
                                  aria-label="Go to slide 1"></span><span class="swiper-pagination-bullet" tabindex="0"
                                                                          role="button"
                                                                          aria-label="Go to slide 2"></span><span
                                class="swiper-pagination-bullet" tabindex="0" role="button"
                                aria-label="Go to slide 3"></span><span
                                class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0"
                                role="button" aria-label="Go to slide 4" aria-current="true"></span><span
                                class="swiper-pagination-bullet" tabindex="0" role="button"
                                aria-label="Go to slide 5"></span><span class="swiper-pagination-bullet" tabindex="0"
                                                                        role="button" aria-label="Go to slide 6"></span>
                        </div>
                        <div class="swiper-button-prev" tabindex="0" role="button" aria-label="Previous slide"
                             aria-controls="swiper-wrapper-0f8681ceaf4722b7"></div>
                        <div class="swiper-button-next" tabindex="0" role="button" aria-label="Next slide"
                             aria-controls="swiper-wrapper-0f8681ceaf4722b7"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span><span
                            class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>

            </div>

        </section><!-- /Hero Section -->

        <!-- Promo Cards Section -->
        <section id="promo-cards" class="promo-cards section">

            <div class="container">

                <div class="row g-4 align-items-stretch mb-5">
                    <div class="col-lg-7">
                        <div class="highlight-card">
                            <img src="<?= base_url('assets/img/product/product-showcase-2.webp') ?>"
                                 alt="Winter Lookbook" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex">
                        <div class="highlight-info">
                            <span class="tag-label">New Season</span>
                            <h2>Winter Lookbook</h2>
                            <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis
                                egestas. Vestibulum tortor quam, feugiat vitae ultricies eget.</p>
                            <ul class="feature-list">
                                <li><i class="bi bi-check-circle"></i> Curated seasonal selections</li>
                                <li><i class="bi bi-check-circle"></i> Exclusive online availability</li>
                                <li><i class="bi bi-check-circle"></i> Complimentary shipping included</li>
                            </ul>
                            <a href="#" class="btn-primary-action">Explore Collection <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div><!-- End Highlight Banner -->

                <div class="row g-3">

                    <div class="col-lg-3 col-md-6">
                        <div class="category-card">
                            <div class="card-img-wrapper">
                                <img src="<?= base_url('assets/img/product/product-m-8.webp') ?>" alt="Modern Menswear"
                                     loading="lazy" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h4>Modern Menswear</h4>
                                <span class="count-label">245 products</span>
                            </div>
                            <div class="card-action">
                                <a href="#">View All <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div><!-- End Category Card -->

                    <div class="col-lg-3 col-md-6">
                        <div class="category-card">
                            <div class="card-img-wrapper">
                                <img src="<?= base_url('assets/img/product/product-f-12.webp') ?>"
                                     alt="Everyday Essentials" loading="lazy" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h4>Everyday Essentials</h4>
                                <span class="count-label">189 products</span>
                            </div>
                            <div class="card-action">
                                <a href="#">View All <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div><!-- End Category Card -->

                    <div class="col-lg-3 col-md-6">
                        <div class="category-card">
                            <div class="card-img-wrapper">
                                <img src="<?= base_url('assets/img/product/product-f-3.webp') ?>" alt="Beauty Rituals"
                                     loading="lazy" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h4>Beauty Rituals</h4>
                                <span class="count-label">112 products</span>
                            </div>
                            <div class="card-action">
                                <a href="#">View All <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div><!-- End Category Card -->

                    <div class="col-lg-3 col-md-6">
                        <div class="category-card">
                            <div class="card-img-wrapper">
                                <img src="<?= base_url('assets/img/product/product-m-11.webp') ?>" alt="Travel Gear"
                                     loading="lazy" class="img-fluid">
                            </div>
                            <div class="card-body">
                                <h4>Travel Gear</h4>
                                <span class="count-label">327 products</span>
                            </div>
                            <div class="card-action">
                                <a href="#">View All <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div><!-- End Category Card -->

                </div>

            </div>

        </section><!-- /Promo Cards Section -->

        <!-- Best Sellers Section -->
        <section id="best-sellers" class="best-sellers section">

            <!-- Section Title -->
            <div class="container section-title">
                <h2>Best Sellers</h2>
                <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row g-4">

                    <!-- Product 1 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-media">
                                <img src="<?= base_url('assets/img/product/product-5.webp') ?>" alt="Product"
                                     class="img-fluid" loading="lazy">
                                <div class="badge-label">Limited Edition</div>
                                <div class="action-overlay">
                                    <button class="action-btn" aria-label="Add to wishlist"><i class="bi bi-heart"></i>
                                    </button>
                                    <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-body">
                                <div class="product-meta">
                                    <span class="category-tag">Premium Collection</span>
                                    <div class="rating-group">
                                        <i class="bi bi-star-fill"></i>
                                        <span>4.2</span>
                                        <span class="count">(24)</span>
                                    </div>
                                </div>
                                <h4 class="product-title"><a href="product-details.html">Donec sollicitudin molestie
                                        malesuada viverra</a></h4>
                                <div class="color-options">
                                    <span class="color-swatch active" style="background-color: #3b82f6;"></span>
                                    <span class="color-swatch" style="background-color: #14b8a6;"></span>
                                    <span class="color-swatch" style="background-color: #f43f5e;"></span>
                                </div>
                                <div class="product-footer">
                                    <div class="product-price">$149.00</div>
                                    <a href="#" class="cart-btn" aria-label="Add to cart"><i class="bi bi-bag-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Product 1 -->

                    <!-- Product 2 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-media">
                                <img src="<?= base_url('assets/img/product/product-8.webp') ?>" alt="Product"
                                     class="img-fluid" loading="lazy">
                                <div class="badge-label discount">25% Off</div>
                                <div class="action-overlay">
                                    <button class="action-btn" aria-label="Add to wishlist"><i class="bi bi-heart"></i>
                                    </button>
                                    <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-body">
                                <div class="product-meta">
                                    <span class="category-tag">Best Seller</span>
                                    <div class="rating-group">
                                        <i class="bi bi-star-fill"></i>
                                        <span>4.7</span>
                                        <span class="count">(58)</span>
                                    </div>
                                </div>
                                <h4 class="product-title"><a href="product-details.html">Pellentesque in ipsum lacinia
                                        orci rutrum</a></h4>
                                <div class="color-options">
                                    <span class="color-swatch active" style="background-color: #1e293b;"></span>
                                    <span class="color-swatch" style="background-color: #eab308;"></span>
                                    <span class="color-swatch" style="background-color: #a855f7;"></span>
                                </div>
                                <div class="product-footer">
                                    <div class="product-price">
                                        <span class="original">$220.00</span>
                                        <span class="current">$165.00</span>
                                    </div>
                                    <a href="#" class="cart-btn" aria-label="Add to cart"><i class="bi bi-bag-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Product 2 -->

                    <!-- Product 3 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-media">
                                <img src="<?= base_url('assets/img/product/product-11.webp') ?>" alt="Product"
                                     class="img-fluid" loading="lazy">
                                <div class="action-overlay">
                                    <button class="action-btn active" aria-label="Remove from wishlist"><i
                                            class="bi bi-heart-fill"></i></button>
                                    <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-body">
                                <div class="product-meta">
                                    <span class="category-tag">New Arrival</span>
                                    <div class="rating-group">
                                        <i class="bi bi-star-fill"></i>
                                        <span>3.8</span>
                                        <span class="count">(12)</span>
                                    </div>
                                </div>
                                <h4 class="product-title"><a href="product-details.html">Quisque velit nisi pretium ut
                                        lacinia</a></h4>
                                <div class="color-options">
                                    <span class="color-swatch active" style="background-color: #ef4444;"></span>
                                    <span class="color-swatch" style="background-color: #0ea5e9;"></span>
                                    <span class="color-swatch" style="background-color: #22c55e;"></span>
                                </div>
                                <div class="product-footer">
                                    <div class="product-price">$89.00</div>
                                    <a href="#" class="cart-btn" aria-label="Add to cart"><i class="bi bi-bag-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Product 3 -->

                    <!-- Product 4 -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-media">
                                <img src="<?= base_url('assets/img/product/product-2.webp') ?>" alt="Product"
                                     class="img-fluid" loading="lazy">
                                <div class="badge-label trending">Trending</div>
                                <div class="action-overlay">
                                    <button class="action-btn" aria-label="Add to wishlist"><i class="bi bi-heart"></i>
                                    </button>
                                    <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-body">
                                <div class="product-meta">
                                    <span class="category-tag">Editor's Pick</span>
                                    <div class="rating-group">
                                        <i class="bi bi-star-fill"></i>
                                        <span>4.9</span>
                                        <span class="count">(71)</span>
                                    </div>
                                </div>
                                <h4 class="product-title"><a href="product-details.html">Sed porttitor lectus nibh
                                        vivamus magna</a></h4>
                                <div class="color-options">
                                    <span class="color-swatch" style="background-color: #64748b;"></span>
                                    <span class="color-swatch active" style="background-color: #7c3aed;"></span>
                                    <span class="color-swatch" style="background-color: #f97316;"></span>
                                </div>
                                <div class="product-footer">
                                    <div class="product-price">$199.00</div>
                                    <a href="#" class="cart-btn" aria-label="Add to cart"><i class="bi bi-bag-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Product 4 -->

                </div>

            </div>

        </section><!-- /Best Sellers Section -->

        <!-- Cards Section -->
        <section id="cards" class="cards section">

            <div class="container">

                <div class="tab-nav-wrapper">
                    <ul class="nav nav-tabs filter-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cards-tab-1"
                                    type="button" role="tab">
                                <i class="bi bi-fire"></i> Trending Now
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cards-tab-2" type="button"
                                    role="tab">
                                <i class="bi bi-award"></i> Highest Rated
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cards-tab-3" type="button"
                                    role="tab">
                                <i class="bi bi-bookmark-star"></i> Hand-Picked
                            </button>
                        </li>
                    </ul>
                </div><!-- End Filter Tabs -->

                <div class="tab-content">

                    <!-- Trending Now Tab -->
                    <div class="tab-pane fade show active" id="cards-tab-1" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-1.webp') ?>"
                                             alt="Handcrafted Tote" class="img-fluid">
                                        <span class="status-badge new-badge">New</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.5</span>
                                                <span class="rating-count">(31)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Handcrafted Tote</h4>
                                        <div class="product-price">
                                            <span class="current-price">$92.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-3.webp') ?>"
                                             alt="Sapphire Stud Set" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">5.0</span>
                                                <span class="rating-count">(53)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Sapphire Stud Set</h4>
                                        <div class="product-price">
                                            <span class="current-price">$44.50</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-5.webp') ?>"
                                             alt="Cotton Weave Top" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.0</span>
                                                <span class="rating-count">(22)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Cotton Weave Top</h4>
                                        <div class="product-price">
                                            <span class="current-price">$49.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-10.webp') ?>"
                                             alt="Woven Crossbody Pouch" class="img-fluid">
                                        <span class="status-badge sale-badge">-15%</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.7</span>
                                                <span class="rating-count">(45)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Woven Crossbody Pouch</h4>
                                        <div class="product-price">
                                            <span class="current-price">$68.00</span>
                                            <span class="original-price">$80.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                        </div>
                    </div><!-- End Trending Now Tab -->

                    <!-- Highest Rated Tab -->
                    <div class="tab-pane fade" id="cards-tab-2" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-2.webp') ?>"
                                             alt="Structured Denim Fit" class="img-fluid">
                                        <span class="status-badge sale-badge">-20%</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">5.0</span>
                                                <span class="rating-count">(93)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Structured Denim Fit</h4>
                                        <div class="product-price">
                                            <span class="current-price">$64.00</span>
                                            <span class="original-price">$80.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-6.webp') ?>"
                                             alt="Padded Chain Clutch" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.5</span>
                                                <span class="rating-count">(68)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Padded Chain Clutch</h4>
                                        <div class="product-price">
                                            <span class="current-price">$134.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-8.webp') ?>"
                                             alt="Urban Day Pack" class="img-fluid">
                                        <span class="status-badge hot-badge">Hot</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">5.0</span>
                                                <span class="rating-count">(119)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Urban Day Pack</h4>
                                        <div class="product-price">
                                            <span class="current-price">$99.50</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-11.webp') ?>"
                                             alt="Heritage Belt Bag" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.8</span>
                                                <span class="rating-count">(87)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Heritage Belt Bag</h4>
                                        <div class="product-price">
                                            <span class="current-price">$76.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                        </div>
                    </div><!-- End Highest Rated Tab -->

                    <!-- Hand-Picked Tab -->
                    <div class="tab-pane fade" id="cards-tab-3" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-7.webp') ?>"
                                             alt="Pleated Wrap Dress" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.0</span>
                                                <span class="rating-count">(38)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Pleated Wrap Dress</h4>
                                        <div class="product-price">
                                            <span class="current-price">$79.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-4.webp') ?>"
                                             alt="Geometric Hoop Pair" class="img-fluid">
                                        <span class="status-badge exclusive-badge">Limited</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.5</span>
                                                <span class="rating-count">(51)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Geometric Hoop Pair</h4>
                                        <div class="product-price">
                                            <span class="current-price">$47.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-9.webp') ?>"
                                             alt="Vintage Buckle Carryall" class="img-fluid">
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">5.0</span>
                                                <span class="rating-count">(72)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Vintage Buckle Carryall</h4>
                                        <div class="product-price">
                                            <span class="current-price">$94.99</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-thumb">
                                        <img src="<?= base_url('assets/img/product/product-12.webp') ?>"
                                             alt="Minimalist Canvas Wallet" class="img-fluid">
                                        <span class="status-badge new-badge">New</span>
                                        <div class="overlay-actions">
                                            <button class="action-btn" aria-label="Add to wishlist"><i
                                                    class="bi bi-heart"></i></button>
                                            <button class="action-btn" aria-label="Quick view"><i class="bi bi-eye"></i>
                                            </button>
                                            <button class="action-btn cart-btn" aria-label="Add to cart"><i
                                                    class="bi bi-bag-plus"></i></button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-meta">
                                            <div class="rating">
                                                <i class="bi bi-star-fill"></i>
                                                <span class="rating-value">4.3</span>
                                                <span class="rating-count">(29)</span>
                                            </div>
                                        </div>
                                        <h4 class="product-title">Minimalist Canvas Wallet</h4>
                                        <div class="product-price">
                                            <span class="current-price">$32.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Product Card -->
                        </div>
                    </div><!-- End Hand-Picked Tab -->
                </div><!-- End Tab Content -->
            </div>
        </section><!-- /Cards Section -->
        <!-- Call To Action Section -->
        <section id="call-to-action" class="call-to-action section dark-background">
            <div class="container">
                <div class="promo-banner">
                    <div class="row align-items-center gy-3">
                        <div class="col-lg-5">
                            <div class="banner-content">
                                <span class="sale-badge"><i class="bi bi-lightning-charge-fill"></i> Flash Sale — Up to 60% Off</span>
                                <h2>Exclusive Offers Just for You</h2>
                                <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis
                                    egestas. Don't miss these limited-time savings.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="timer-block">
                                <span class="timer-heading"><i class="bi bi-hourglass-split"></i> Ends In:</span>
                                <div class="countdown d-flex" data-count="2026/12/31">
                                    <div>
                                        <h3 class="count-days"></h3>
                                        <h4>Days</h4>
                                    </div>
                                    <div>
                                        <h3 class="count-hours"></h3>
                                        <h4>Hours</h4>
                                    </div>
                                    <div>
                                        <h3 class="count-minutes"></h3>
                                        <h4>Minutes</h4>
                                    </div>
                                    <div>
                                        <h3 class="count-seconds"></h3>
                                        <h4>Seconds</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="banner-actions">
                                <a href="#" class="btn-primary-deal">Claim Offer <i class="bi bi-arrow-right"></i></a>
                                <a href="#" class="btn-secondary-deal">View All Items</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gy-4 mt-2">
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= base_url('assets/img/product/product-6.webp') ?>" alt="Product"
                                     class="img-fluid">
                                <span class="discount-label">-45%</span>
                            </div>
                            <div class="product-body">
                                <div class="star-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                    <span class="review-count">(312)</span>
                                </div>
                                <h6 class="product-title">Premium Wireless Headphones</h6>
                                <div class="price-row">
                                    <span class="original-price">$179</span>
                                    <span class="sale-price">$98</span>
                                </div>
                            </div>
                            <div class="product-footer">
                                <a href="#" class="cart-btn"><i class="bi bi-bag-plus"></i> Add to Cart</a>
                            </div>
                        </div>
                    </div><!-- End Product Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= base_url('assets/img/product/product-11.webp') ?>" alt="Product"
                                     class="img-fluid">
                                <span class="discount-label">-50%</span>
                            </div>
                            <div class="product-body">
                                <div class="star-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <span class="review-count">(478)</span>
                                </div>
                                <h6 class="product-title">Smart Fitness Tracker Pro</h6>
                                <div class="price-row">
                                    <span class="original-price">$120</span>
                                    <span class="sale-price">$60</span>
                                </div>
                            </div>
                            <div class="product-footer">
                                <a href="#" class="cart-btn"><i class="bi bi-bag-plus"></i> Add to Cart</a>
                            </div>
                        </div>
                    </div><!-- End Product Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= base_url('assets/img/product/product-2.webp') ?>" alt="Product"
                                     class="img-fluid">
                                <span class="discount-label">-35%</span>
                            </div>
                            <div class="product-body">
                                <div class="star-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                    <span class="review-count">(189)</span>
                                </div>
                                <h6 class="product-title">Lightweight Travel Backpack</h6>
                                <div class="price-row">
                                    <span class="original-price">$210</span>
                                    <span class="sale-price">$136</span>
                                </div>
                            </div>
                            <div class="product-footer">
                                <a href="#" class="cart-btn"><i class="bi bi-bag-plus"></i> Add to Cart</a>
                            </div>
                        </div>
                    </div><!-- End Product Card -->
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?= base_url('assets/img/product/product-5.webp') ?>" alt="Product"
                                     class="img-fluid">
                                <span class="discount-label">-55%</span>
                            </div>
                            <div class="product-body">
                                <div class="star-rating">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <span class="review-count">(245)</span>
                                </div>
                                <h6 class="product-title">Artisan Porcelain Collection</h6>
                                <div class="price-row">
                                    <span class="original-price">$95</span>
                                    <span class="sale-price">$43</span>
                                </div>
                            </div>
                            <div class="product-footer">
                                <a href="#" class="cart-btn"><i class="bi bi-bag-plus"></i> Add to Cart</a>
                            </div>
                        </div>
                    </div><!-- End Product Card -->
                </div>
            </div>
        </section><!-- /Call To Action Section -->
    </main>
<?= $this->endSection() ?>