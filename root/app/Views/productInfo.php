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
                            <img id="main-product-image" src="assets/img/product/product-details-3.webp" data-zoom="assets/img/product/product-details-3.webp" alt="Product" class="img-fluid">
                            <div class="image-zoom-container"></div>
                            <button class="image-nav-btn prev-image" type="button"><i class="bi bi-chevron-left"></i></button>
                            <button class="image-nav-btn next-image" type="button"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div class="thumb-strip">
                            <div class="thumb-cell thumbnail-item active" data-image="assets/img/product/product-details-3.webp">
                                <img src="assets/img/product/product-details-3.webp" alt="View 1" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="assets/img/product/product-details-4.webp">
                                <img src="assets/img/product/product-details-4.webp" alt="View 2" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="assets/img/product/product-details-5.webp">
                                <img src="assets/img/product/product-details-5.webp" alt="View 3" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="assets/img/product/product-details-6.webp">
                                <img src="assets/img/product/product-details-6.webp" alt="View 4" class="img-fluid">
                            </div>
                            <div class="thumb-cell thumbnail-item" data-image="assets/img/product/product-details-7.webp">
                                <img src="assets/img/product/product-details-7.webp" alt="View 5" class="img-fluid">
                            </div>
                        </div><!-- End Thumb Strip -->
                    </div>
                </div><!-- End Product Gallery -->

                <!-- Product Details -->
                <div class="col-lg-5">
                    <div class="product-detail-card">
                        <div class="detail-header">
                            <span class="type-badge">Sound Equipment</span>
                            <span class="stock-indicator"><i class="bi bi-circle-fill"></i> In Stock</span>
                        </div>

                        <h1 class="product-heading">Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor</h1>

                        <div class="review-summary">
                            <div class="stars-inline">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </div>
                            <span class="score-text">4.6</span>
                            <span class="divider-dot">·</span>
                            <a href="#" class="reviews-anchor">143 ratings</a>
                            <span class="divider-dot">·</span>
                            <span class="units-left">18 remaining</span>
                        </div>

                        <div class="pricing-area">
                            <div class="price-row">
                                <span class="price-now">$189.99</span>
                                <span class="price-was">$239.99</span>
                            </div>
                            <span class="save-tag">Save $50.00</span>
                        </div>

                        <p class="summary-text">Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.</p>

                        <div class="separator"></div>

                        <div class="variant-picker">
                            <span class="picker-label">Color — <span class="chosen-variant">Obsidian</span></span>
                            <div class="color-dots">
                                <button class="dot active" data-color="Obsidian" style="background:#1a1a2e;" type="button"><i class="bi bi-check2"></i></button>
                                <button class="dot" data-color="Ivory" style="background:#f0ece2;" type="button"><i class="bi bi-check2"></i></button>
                                <button class="dot" data-color="Sapphire" style="background:#0055aa;" type="button"><i class="bi bi-check2"></i></button>
                                <button class="dot" data-color="Moss" style="background:#2d6a4f;" type="button"><i class="bi bi-check2"></i></button>
                            </div>
                        </div>

                        <div class="action-row">
                            <div class="quantity-selector">
                                <button class="quantity-btn decrease" type="button"><i class="bi bi-dash"></i></button>
                                <input type="number" class="quantity-input" value="1" min="1" max="18">
                                <button class="quantity-btn increase" type="button"><i class="bi bi-plus"></i></button>
                            </div>
                            <button class="btn primary-action-btn">
                                <i class="bi bi-bag-plus"></i> Add to Cart
                            </button>
                            <button class="btn wishlist-toggle" title="Save to Wishlist" type="button">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>

                        <button class="btn checkout-now-btn" type="button">
                            <i class="bi bi-lightning-charge"></i> Purchase Instantly
                        </button>

                        <div class="guarantee-bar">
                            <div class="guarantee-item">
                                <i class="bi bi-truck"></i>
                                <span>Free shipping $75+</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <span>45-day returns</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="bi bi-shield-check"></i>
                                <span>3-year warranty</span>
                            </div>
                            <div class="guarantee-item">
                                <i class="bi bi-headset"></i>
                                <span>24/7 support</span>
                            </div>
                        </div><!-- End Guarantee Bar -->
                    </div>
                </div><!-- End Product Details -->
            </div>

            <!-- Tabbed Content -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="info-tabs">
                        <ul class="tab-nav nav" role="tablist">
                            <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-details-tab-desc" type="button" aria-selected="true" role="tab">Description</button></li>
                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-specs" type="button" aria-selected="false" tabindex="-1" role="tab">Specifications</button></li>
                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#product-details-tab-feedback" type="button" aria-selected="false" tabindex="-1" role="tab">Feedback (143)</button></li>
                        </ul>

                        <div class="tab-content">
                            <!-- Description Tab -->
                            <div class="tab-pane fade show active" id="product-details-tab-desc" role="tabpanel">
                                <div class="desc-content">
                                    <div class="row g-4">
                                        <div class="col-lg-8">
                                            <h3>About This Product</h3>
                                            <p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores.</p>

                                            <h4>Feature Highlights</h4>
                                            <div class="row g-3">
                                                <div class="col-sm-6">
                                                    <div class="highlight-card">
                                                        <i class="bi bi-soundwave"></i>
                                                        <div>
                                                            <h5>Premium Sound</h5>
                                                            <p>Temporibus autem quibusdam officiis debitis rerum</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="highlight-card">
                                                        <i class="bi bi-battery-full"></i>
                                                        <div>
                                                            <h5>Extended Battery</h5>
                                                            <p>Saepe eveniet ut et voluptates repudiandae sint</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="highlight-card">
                                                        <i class="bi bi-bluetooth"></i>
                                                        <div>
                                                            <h5>Seamless Pairing</h5>
                                                            <p>Itaque earum rerum hic tenetur sapiente delectus</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="highlight-card">
                                                        <i class="bi bi-gem"></i>
                                                        <div>
                                                            <h5>Ergonomic Design</h5>
                                                            <p>Aut reiciendis voluptatibus maiores alias consequatur</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="included-box">
                                                <h4><i class="bi bi-box-seam"></i> What's Included</h4>
                                                <ul>
                                                    <li><i class="bi bi-check2-circle"></i> Premium Audio Device</li>
                                                    <li><i class="bi bi-check2-circle"></i> Protective Travel Case</li>
                                                    <li><i class="bi bi-check2-circle"></i> USB-C Charging Cable</li>
                                                    <li><i class="bi bi-check2-circle"></i> 3.5mm AUX Connector</li>
                                                    <li><i class="bi bi-check2-circle"></i> Setup Manual</li>
                                                    <li><i class="bi bi-check2-circle"></i> Warranty Certificate</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Description Tab -->

                            <!-- Specifications Tab -->
                            <div class="tab-pane fade" id="product-details-tab-specs" role="tabpanel">
                                <div class="specs-content">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="spec-block">
                                                <h4>Audio Performance</h4>
                                                <table class="data-table">
                                                    <tbody>
                                                    <tr>
                                                        <td>Frequency Range</td>
                                                        <td>15Hz - 25kHz</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Driver Size</td>
                                                        <td>50mm Dynamic</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sensitivity</td>
                                                        <td>98dB SPL</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Impedance</td>
                                                        <td>24 Ohm</td>
                                                    </tr>
                                                    <tr>
                                                        <td>THD</td>
                                                        <td>&lt; 0.5%</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="spec-block">
                                                <h4>Wireless &amp; Power</h4>
                                                <table class="data-table">
                                                    <tbody>
                                                    <tr>
                                                        <td>Protocol</td>
                                                        <td>Bluetooth 5.3</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Range</td>
                                                        <td>Up to 30ft (10m)</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Battery</td>
                                                        <td>800mAh Li-ion</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Playtime</td>
                                                        <td>35+ hours</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Charge Time</td>
                                                        <td>2.5 hours</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="spec-block">
                                                <h4>Build &amp; Dimensions</h4>
                                                <table class="data-table">
                                                    <tbody>
                                                    <tr>
                                                        <td>Weight</td>
                                                        <td>285g</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Dimensions</td>
                                                        <td>190 x 165 x 82mm</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Cushion Material</td>
                                                        <td>Memory Foam</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Frame</td>
                                                        <td>Adjustable Steel</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="spec-block">
                                                <h4>Smart Features</h4>
                                                <table class="data-table">
                                                    <tbody>
                                                    <tr>
                                                        <td>Noise Cancelling</td>
                                                        <td>Hybrid ANC</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Voice Assistant</td>
                                                        <td>Siri &amp; Google</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Microphone</td>
                                                        <td>Dual Array</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Water Resistance</td>
                                                        <td>IPX5</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Specifications Tab -->

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