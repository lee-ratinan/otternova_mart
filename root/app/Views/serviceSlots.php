<?php $this->extend('_layout_otternova'); ?>
<?= $this->section('content') ?>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0">Search Slots</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="">Home</a></li>
                    <li class="current">Search Slots</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="row">

            <div class="col-lg-4 sidebar">

                <div class="widgets-container">
                    <!-- Pricing Range Widget -->
                    <div class="pricing-range-widget widget-item">
                        <h3 class="widget-title">Price Range</h3>
                        <div class="price-range-container">
                            <div class="current-range mb-3">
                                <span class="min-price">$0</span>
                                <span class="max-price float-end">$500</span>
                            </div>
                            <div class="range-slider">
                                <div class="slider-track"></div>
                                <div class="slider-progress" style="left: 0%; width: 50%;"></div>
                                <input type="range" class="min-range" min="0" max="1000" value="0" step="10">
                                <input type="range" class="max-range" min="0" max="1000" value="500" step="10">
                            </div>
                            <div class="price-inputs mt-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control min-price-input" placeholder="Min" min="0" max="1000" value="0" step="10">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control max-price-input" placeholder="Max" min="0" max="1000" value="500" step="10">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-actions mt-3">
                                <button type="button" class="btn btn-sm btn-primary w-100">Apply Filter</button>
                            </div>
                        </div>

                    </div><!--/Pricing Range Widget -->

                    <!-- Filter Widget -->
                    <div class="filter-widget-3 widget-item">

                        <h3 class="widget-title">Select Service</h3>

                        <div class="brand-filter-content">
                            <div class="brand-search">
                                <input type="text" class="form-control" placeholder="Search brands...">
                                <i class="bi bi-search"></i>
                            </div>

                            <div class="brand-list">
                                <div class="brand-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brand1">
                                        <label class="form-check-label" for="brand1">
                                            Service 1 / Variant 1
                                            <span class="brand-count">(24)</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="brand-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brand2">
                                        <label class="form-check-label" for="brand2">
                                            Service 1 / Variant 2
                                            <span class="brand-count">(18)</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="brand-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brand3">
                                        <label class="form-check-label" for="brand3">
                                            Service 1 / Variant 3
                                            <span class="brand-count">(12)</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="brand-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brand4">
                                        <label class="form-check-label" for="brand4">
                                            Service 2 / Variant 1
                                            <span class="brand-count">(9)</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="brand-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="brand5">
                                        <label class="form-check-label" for="brand5">
                                            Service 2 / Variant 2
                                            <span class="brand-count">(7)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="brand-actions">
                                <button class="btn btn-sm btn-outline-primary">Apply Filter</button>
                                <button class="btn btn-sm btn-link">Clear All</button>
                            </div>
                        </div>
                    </div><!--/Filter Widget -->
                </div>
            </div>

            <div class="col-lg-8">

                <!-- Category Header Section -->
                <section id="category-header" class="category-header section">
                    <div class="container">
                        <!-- Search & Controls Card -->
                        <div class="controls-card">
                            <div class="controls-filters">
                                <div class="row align-items-end g-3">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="filter-field">
                                            <label for="priceRange">Price Range</label>
                                            <select class="form-select" id="priceRange">
                                                <option selected="">All Prices</option>
                                                <option>Under $25</option>
                                                <option>$25 to $50</option>
                                                <option>$50 to $100</option>
                                                <option>$100 to $200</option>
                                                <option>$200 &amp; Above</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="filter-field">
                                            <label for="sortBy">Sort By</label>
                                            <select class="form-select" id="sortBy">
                                                <option selected="">Featured</option>
                                                <option>Price: Low to High</option>
                                                <option>Price: High to Low</option>
                                                <option>Customer Rating</option>
                                                <option>Newest Arrivals</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="filter-field">
                                            <label for="itemsPerPage">Items Per Page</label>
                                            <select class="form-select" id="itemsPerPage" aria-label="Items per page">
                                                <option value="12">12 items</option>
                                                <option value="24">24 items</option>
                                                <option value="48">48 items</option>
                                                <option value="96">96 items</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <button class="clear-filters-btn" type="button">
                                            <i class="bi bi-arrow-counterclockwise"></i> Clear Filters
                                        </button>
                                    </div>
                                </div>
                            </div><!-- End Controls Filters -->
                        </div><!-- End Controls Card -->

                        <!-- Active Filters -->
                        <div class="active-tags">
                            <span class="tags-label">Active filters:</span>
                            <div class="tags-list">
                  <span class="tag-item">
                    Electronics
                    <button class="tag-remove" aria-label="Remove filter"><i class="bi bi-x"></i></button>
                  </span>
                                <span class="tag-item">
                    $50 to $100
                    <button class="tag-remove" aria-label="Remove filter"><i class="bi bi-x"></i></button>
                  </span>
                            </div>
                            <button class="reset-all">Reset All</button>
                        </div><!-- End Active Filters -->

                    </div>

                </section><!-- /Category Header Section -->

                <!-- Category Product List Section -->
                <section id="category-product-list" class="category-product-list section">

                    <div class="container">

                        <div class="row g-3 g-lg-4">

                            <!-- Product 1 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-f-3.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Everyday Wear</span>
                                        <h4 class="tile-title"><a href="product-details.html">Aliquam Tincidunt</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-half"></i>
                                            </div>
                                            <span class="count">42 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$145.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 2 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-m-3.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <span class="status-tag tag-fresh">Just Arrived</span>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Men's Collection</span>
                                        <h4 class="tile-title"><a href="product-details.html">Vivamus Euismod</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="count">29 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$112.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 3 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-f-5.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <span class="status-tag tag-discount">-25%</span>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Fashion Picks</span>
                                        <h4 class="tile-title"><a href="product-details.html">Nulla Facilisi</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                            </div>
                                            <span class="count">57 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$74.00 <del class="original-price">$99.00</del></span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 4 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-m-5.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Sneakers</span>
                                        <h4 class="tile-title"><a href="product-details.html">Etiam Consequat</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-half"></i>
                                            </div>
                                            <span class="count">36 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$162.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 5 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-f-7.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Women's Edit</span>
                                        <h4 class="tile-title"><a href="product-details.html">Morbi Tristique</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="count">18 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$88.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 6 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-m-7.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <span class="status-tag tag-discount">-15%</span>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Men's Style</span>
                                        <h4 class="tile-title"><a href="product-details.html">Sed Ullamcorper</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                            </div>
                                            <span class="count">48 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$125.00 <del class="original-price">$147.00</del></span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 7 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-f-19.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <span class="status-tag tag-fresh">Just Arrived</span>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Accessories</span>
                                        <h4 class="tile-title"><a href="product-details.html">Interdum Malesuada</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-half"></i>
                                            </div>
                                            <span class="count">31 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$58.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                            <!-- Product 8 -->
                            <div class="col-md-6 col-lg-4">
                                <article class="product-tile">
                                    <div class="tile-visual">
                                        <a href="product-details.html">
                                            <img src="assets/img/product/product-m-17.webp" class="img-fluid" alt="Product" loading="lazy">
                                        </a>
                                        <div class="tile-actions">
                                            <button type="button" class="action-btn" aria-label="Add to wishlist">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Quick view">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="action-btn" aria-label="Add to cart">
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="tile-body">
                                        <span class="tile-badge">Outerwear</span>
                                        <h4 class="tile-title"><a href="product-details.html">Aenean Placerat</a></h4>
                                        <div class="tile-rating">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="count">39 reviews</span>
                                        </div>
                                    </div>
                                    <div class="tile-footer">
                                        <span class="tile-price">$79.00</span>
                                        <button type="button" class="add-cart-btn">Add to Cart</button>
                                    </div>
                                </article>
                            </div><!-- End Product Item -->

                        </div>

                    </div>

                </section><!-- /Category Product List Section -->

                <!-- Category Pagination Section -->
                <section id="category-pagination" class="category-pagination section">

                    <div class="container">

                        <div class="pagination-bar">
                            <div class="row align-items-center gy-3">
                                <div class="col-lg-4 col-md-4">
                                    <div class="results-label">
                                        Page <span class="current-page">1</span> of <span class="total-pages">10</span> · Showing results
                                    </div>
                                </div><!-- End Results Label -->

                                <div class="col-lg-4 col-md-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="page-list">
                                            <li>
                                                <a href="#" class="page-control prev-control" aria-label="Previous page">
                                                    <i class="bi bi-arrow-left"></i>
                                                </a>
                                            </li>
                                            <li><a href="#" class="page-link-item active" aria-current="page">1</a></li>
                                            <li><a href="#" class="page-link-item">2</a></li>
                                            <li><a href="#" class="page-link-item">3</a></li>
                                            <li><span class="page-ellipsis">…</span></li>
                                            <li><a href="#" class="page-link-item">9</a></li>
                                            <li><a href="#" class="page-link-item">10</a></li>
                                            <li>
                                                <a href="#" class="page-control next-control" aria-label="Next page">
                                                    <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div><!-- End Page Numbers -->

                                <div class="col-lg-4 col-md-4">
                                    <div class="goto-group">
                                        <label for="goto-input">Go to</label>
                                        <input type="number" id="goto-input" min="1" max="10" value="1" aria-label="Jump to page number">
                                        <a href="#" class="goto-btn">Go</a>
                                    </div>
                                </div><!-- End Go To Page -->
                            </div>
                        </div>

                    </div>

                </section><!-- /Category Pagination Section -->

            </div>

        </div>
    </div>
<?= $this->endSection() ?>