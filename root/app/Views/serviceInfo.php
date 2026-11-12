<?php $this->extend('_layout_otternova'); ?>
<?= $this->section('content') ?>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0">[service name]</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="#">[business name]</a></li>
                    <li class="current">[service name]</li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="product-details" class="product-details section">
        <div class="container">
            <div class="row g-4">
                <!-- Product Gallery -->
                <div class="col-lg-7">
                    <div class="image-showcase">
                        <div class="main-image-container">
                            <span class="discount-badge">-21%</span>
                            <img id="main-product-image" src="../../assets/img/product/product-details-3.webp" data-zoom="assets/img/product/product-details-3.webp" alt="Product" class="img-fluid">
                            <div class="image-zoom-container"></div>
                            <button class="image-nav-btn prev-image" type="button"><i class="bi bi-chevron-left"></i></button>
                            <button class="image-nav-btn next-image" type="button"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="thumb-strip">
                            <div class="thumb-cell thumbnail-item active" data-image="../../assets/img/product/product-details-3.webp">
                                <img src="../../assets/img/product/product-details-3.webp" alt="View 1" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="../../assets/img/product/product-details-4.webp">
                                <img src="../../assets/img/product/product-details-4.webp" alt="View 2" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="../../assets/img/product/product-details-5.webp">
                                <img src="../../assets/img/product/product-details-5.webp" alt="View 3" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="../../assets/img/product/product-details-6.webp">
                                <img src="../../assets/img/product/product-details-6.webp" alt="View 4" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="../../assets/img/product/product-details-7.webp">
                                <img src="../../assets/img/product/product-details-7.webp" alt="View 5" class="img-fluid">
                            </div>
                        </div><!-- End Thumb Strip -->
                    </div>
                </div><!-- End Product Gallery -->

                <!-- Product Details -->
                <div class="col-lg-5">
                    <div class="product-detail-card">
                        <div class="detail-header">
                            <span class="type-badge">[artifact category]</span>
                            <span class="stock-indicator"><i class="bi bi-circle-fill"></i> [active service?]</span>
                        </div>
                        <h1 class="product-heading">[service title]</h1>
                        <div class="review-summary">
                            <div class="stars-inline">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                            <span class="score-text">[#.#]</span>
                            <span class="divider-dot">·</span>
                            <a href="#" class="reviews-anchor">### ratings</a>
                        </div>

                        <div class="pricing-area">
                            <div class="price-row">
                                <span class="price-now">$189.99</span>
                                <span class="price-was">$239.99</span>
                            </div>
                            <span class="save-tag">Save $50.00</span>
                        </div>
                        <p class="summary-text">[short description with read more button that scroll the page down to the description below]</p>
                        <div class="separator"></div>
                        <div class="action-row">
                            <button class="btn primary-action-btn">
                                <i class="bi bi-search"></i> [Search Session / Search Slot]
                            </button>
                            <button class="btn wishlist-toggle" title="Save to Wishlist" type="button">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </div>
                </div><!-- End Product Details -->
            </div>

            <!-- Tabbed Content -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="info-tabs">
                        <ul class="tab-nav nav" role="tablist">
                            <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-details-tab-desc" type="button" aria-selected="true" role="tab">Description</button></li>
                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-feedback" type="button" aria-selected="false" tabindex="-1" role="tab">Feedback (143)</button></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Description Tab -->
                            <div class="tab-pane fade show active" id="product-details-tab-desc" role="tabpanel">
                                <div class="desc-content">
                                    <div class="row g-4">
                                        <div class="col">
                                            <h3>About This Product</h3>
                                            <p>[Description]</p>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Description Tab -->

                            <!-- Feedback Tab -->
                            <div class="tab-pane fade" id="product-details-tab-feedback" role="tabpanel">
                                <div class="feedback-content">
                                    <div class="row g-4 mb-4">
                                        <div class="col-lg-3 col-md-4">
                                            <div class="rating-overview">
                                                <div class="big-number">4.6</div>
                                                <div class="star-row">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-half"></i>
                                                </div>
                                                <span class="count-label">Based on 143 ratings</span>
                                                <button class="btn review-cta">Write a Review</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 col-md-8">
                                            <div class="distribution-chart">
                                                <div class="dist-row">
                                                    <span class="dist-label">5 <i class="bi bi-star-fill"></i></span>
                                                    <div class="dist-track">
                                                        <div class="dist-fill" style="width:68%;"></div>
                                                    </div>
                                                    <span class="dist-count">97</span>
                                                </div>
                                                <div class="dist-row">
                                                    <span class="dist-label">4 <i class="bi bi-star-fill"></i></span>
                                                    <div class="dist-track">
                                                        <div class="dist-fill" style="width:22%;"></div>
                                                    </div>
                                                    <span class="dist-count">31</span>
                                                </div>
                                                <div class="dist-row">
                                                    <span class="dist-label">3 <i class="bi bi-star-fill"></i></span>
                                                    <div class="dist-track">
                                                        <div class="dist-fill" style="width:6%;"></div>
                                                    </div>
                                                    <span class="dist-count">9</span>
                                                </div>
                                                <div class="dist-row">
                                                    <span class="dist-label">2 <i class="bi bi-star-fill"></i></span>
                                                    <div class="dist-track">
                                                        <div class="dist-fill" style="width:3%;"></div>
                                                    </div>
                                                    <span class="dist-count">4</span>
                                                </div>
                                                <div class="dist-row">
                                                    <span class="dist-label">1 <i class="bi bi-star-fill"></i></span>
                                                    <div class="dist-track">
                                                        <div class="dist-fill" style="width:1%;"></div>
                                                    </div>
                                                    <span class="dist-count">2</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- End Rating Overview -->

                                    <div class="reviews-list">
                                        <article class="review-entry">
                                            <div class="entry-top">
                                                <img src="assets/img/person/person-m-8.webp" alt="Reviewer" class="avatar-img">
                                                <div class="entry-meta">
                                                    <strong>Marcus Bennett</strong>
                                                    <div class="meta-line">
                              <span class="inline-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                              </span>
                                                        <span class="entry-date">April 12, 2024</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5>Exceptional clarity and comfortable wear</h5>
                                            <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati.</p>
                                            <div class="entry-actions">
                                                <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (14)</button>
                                                <button class="action-btn"><i class="bi bi-reply"></i> Reply</button>
                                            </div>
                                        </article><!-- End Review Entry -->

                                        <article class="review-entry">
                                            <div class="entry-top">
                                                <img src="assets/img/person/person-f-11.webp" alt="Reviewer" class="avatar-img">
                                                <div class="entry-meta">
                                                    <strong>Olivia Torres</strong>
                                                    <div class="meta-line">
                              <span class="inline-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                              </span>
                                                        <span class="entry-date">March 5, 2024</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5>Solid performance with minor quirks</h5>
                                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa quae ab illo inventore veritatis. Generally pleased with my purchase.</p>
                                            <div class="entry-actions">
                                                <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (9)</button>
                                                <button class="action-btn"><i class="bi bi-reply"></i> Reply</button>
                                            </div>
                                        </article><!-- End Review Entry -->

                                        <article class="review-entry">
                                            <div class="entry-top">
                                                <img src="assets/img/person/person-m-12.webp" alt="Reviewer" class="avatar-img">
                                                <div class="entry-meta">
                                                    <strong>Jason Kimura</strong>
                                                    <div class="meta-line">
                              <span class="inline-stars">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                              </span>
                                                        <span class="entry-date">January 18, 2024</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5>Ideal companion for remote professionals</h5>
                                            <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt neque porro quisquam est.</p>
                                            <div class="entry-actions">
                                                <button class="action-btn"><i class="bi bi-hand-thumbs-up"></i> Helpful (18)</button>
                                                <button class="action-btn"><i class="bi bi-reply"></i> Reply</button>
                                            </div>
                                        </article><!-- End Review Entry -->

                                        <div class="load-wrap">
                                            <button class="btn load-btn">Load More Reviews</button>
                                        </div>
                                    </div><!-- End Reviews List -->
                                </div>
                            </div><!-- End Feedback Tab -->
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
<?= $this->endSection() ?>