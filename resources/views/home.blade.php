@extends('layouts.app')

@section('title', 'Booknest - Best Books Collection')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.5.6">
@endsection

@section('content')
<!-- Hero Banner Section -->
<section class="hero-banner">
    <!-- Sliding background images container -->
    <div class="hero-slides">
        @if($banners->count() > 0)
            @foreach($banners as $banner)
                <div class="hero-slide @if($loop->first) active @endif" style="background-image: url('{{ asset('storage/' . $banner->image) }}');">
                </div>
            @endforeach
        @else
            <!-- Fallback Default Banners -->
            <div class="hero-slide active" style="background-image: url('/images/hero/hero_bg_1.png');"></div>
            <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_2.png');"></div>
            <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_3.png');"></div>
            <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_4.png');"></div>
        @endif
    </div>

    <!-- Slider Navigation Arrows -->
    <button class="hero-slider-arrow prev" onclick="changeHeroSlide(-1)" aria-label="Previous Slide">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="hero-slider-arrow next" onclick="changeHeroSlide(1)" aria-label="Next Slide">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Slider Dot Indicators -->
    <div class="hero-slider-dots">
        @if($banners->count() > 0)
            @foreach($banners as $banner)
                <span class="hero-dot @if($loop->first) active @endif" onclick="setHeroSlide({{ $loop->index }})"></span>
            @endforeach
        @else
            <span class="hero-dot active" onclick="setHeroSlide(0)"></span>
            <span class="hero-dot" onclick="setHeroSlide(1)"></span>
            <span class="hero-dot" onclick="setHeroSlide(2)"></span>
            <span class="hero-dot" onclick="setHeroSlide(3)"></span>
        @endif
    </div>

    <!-- Wavy Bottom Shape Divider -->
    <div class="hero-shape-divider">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,42.4V120H0Z" class="shape-fill"></path>
        </svg>
    </div>
</section>

<!-- Hero Value Propositions / Features Showcase Section -->
<section class="hero-features-showcase">
    <div class="container">
        <div class="features-grid-wrapper">
            
            <!-- Column 1: Curriculum -->
            <div class="feature-column-card">
                <div class="feature-header-wrap">
                    <h3 class="feature-title">Curriculum? <span class="highlight-text">Sorted!</span></h3>
                    <p class="feature-desc">Grade-specific books sets & curriculum reading lists curated for all academic levels.</p>
                </div>
                
                <!-- SVG Donut Gauge -->
                <div class="feature-gauge-box">
                    <svg viewBox="0 0 100 100" class="gauge-svg">
                        <!-- Background track -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f4f1ea" stroke-width="10" />
                        <!-- Active progress arc (gold/green) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="var(--brand-gold)" stroke-width="10" 
                                stroke-dasharray="251" stroke-dashoffset="62" stroke-linecap="round" transform="rotate(-90 50 50)" />
                        <!-- Text inside -->
                        <text x="50" y="47" class="gauge-value" text-anchor="middle">G 1-12</text>
                        <text x="50" y="65" class="gauge-label" text-anchor="middle">Kits Ready</text>
                    </svg>
                </div>
            </div>
            
            <!-- Column 2: VIP Subscriptions -->
            <div class="feature-column-card">
                <div class="feature-header-wrap">
                    <h3 class="feature-title">VIP Access? <span class="highlight-text">Active!</span></h3>
                    <p class="feature-desc">Get unlimited reading access, digital streaming files, and shipping on physical books orders.</p>
                </div>
                
                <!-- SVG Segmented Donut Gauge -->
                <div class="feature-gauge-box">
                    <svg viewBox="0 0 100 100" class="gauge-svg">
                        <!-- Track -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f4f1ea" stroke-width="10" />
                        <!-- Segment 1: Green -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#2d6a4f" stroke-width="10" 
                                stroke-dasharray="251" stroke-dashoffset="120" stroke-linecap="round" transform="rotate(-90 50 50)" />
                        <!-- Segment 2: Gold -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#cca353" stroke-width="10" 
                                stroke-dasharray="251" stroke-dashoffset="190" stroke-linecap="round" transform="rotate(30 50 50)" />
                        <text x="50" y="47" class="gauge-value" text-anchor="middle">VIP</text>
                        <text x="50" y="65" class="gauge-label" text-anchor="middle">Unlimited</text>
                    </svg>
                </div>
            </div>
            
            <!-- Column 3: Flexible Payments -->
            <div class="feature-column-card">
                <div class="feature-header-wrap">
                    <h3 class="feature-title">Payment? <span class="highlight-text">Flexible!</span></h3>
                    <p class="feature-desc">Swift home delivery across Myanmar with COD, KBZPay, WaveMoney, or international cards.</p>
                </div>
                
                <!-- SVG Gauge Arc -->
                <div class="feature-gauge-box">
                    <svg viewBox="0 0 100 100" class="gauge-svg">
                        <!-- Track -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f4f1ea" stroke-width="10" />
                        <!-- Active progress arc (purple/gold) -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#581c87" stroke-width="10" 
                                stroke-dasharray="251" stroke-dashoffset="80" stroke-linecap="round" transform="rotate(-90 50 50)" />
                        <text x="50" y="47" class="gauge-value" text-anchor="middle">COD</text>
                        <text x="50" y="65" class="gauge-label" text-anchor="middle">& Wallets</text>
                    </svg>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Main Store Catalog Section -->
<div class="store-catalog-wrapper container" id="store-catalog">
    <!-- Section 1: Top Seller & Popular Downloads -->
    @if($books->count() > 0)
        <div class="store-section-container">
            <div class="store-section-header">
                <h2><i class="fa-solid fa-fire text-accent"></i> Top Seller & Popular Downloads</h2>
                <p>Trending and most read books of the week</p>
            </div>
            
            <div class="promo-showcase-grid">
                @foreach($books->take(3) as $book)
                    @php
                        $bookColorClass = 'book-color-' . ($loop->index % 4 + 1);
                        $categoryIds = $book->classifications->pluck('id')->implode(',');
                    @endphp
                    <div class="book-container-3d" 
                         data-categories="{{ $categoryIds }}" 
                         data-title="{{ strtolower($book->name) }}" 
                         data-author="{{ strtolower($book->author?->name ?? 'unknown') }}">
                         
                        <div class="promo-book-ad-card" 
                             data-id="{{ $book->id }}"
                             data-title-raw="{{ $book->name }}"
                             data-author-raw="{{ $book->author?->name ?? 'Unknown Author' }}"
                             data-desc="{{ $book->description }}"
                             data-price="{{ $book->price }}"
                             data-stock="{{ $book->stock_quantity }}"
                             data-pages="{{ $book->pages }}"
                             data-color-class="{{ $bookColorClass }}"
                             data-pdf-file="{{ $book->pdf_file }}"
                             data-image="{{ $book->image ? asset('storage/' . $book->image) : '' }}"
                             data-wishlisted="{{ Auth::guard('customer')->check() && Auth::guard('customer')->user()->wishlistBooks->contains($book->id) ? 'true' : 'false' }}"
                             onclick="openBookDetailFromElement(this)">
                             
                            <!-- Left: Cover -->
                            <div class="promo-cover-box">
                                <div class="book-card-3d-wrapper">
                                    <div class="book-3d {{ $bookColorClass }}">
                                        <!-- Cover Front -->
                                        @if($book->image)
                                            <div class="book-cover-front" style="background-image: url('{{ asset('storage/' . $book->image) }}');">
                                                <div class="book-cover-emboss"></div>
                                            </div>
                                        @else
                                            <div class="book-cover-front">
                                                <div class="book-cover-emboss">
                                                    <div class="book-cover-title-box">
                                                        <div class="book-cover-title">{{ $book->name }}</div>
                                                        <div class="book-cover-author">{{ $book->author?->name ?? 'Unknown Author' }}</div>
                                                    </div>
                                                    <div class="book-cover-badge">
                                                        <i class="fa-solid fa-book-open-reader"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <!-- Book Spine -->
                                        <div class="book-spine"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Detailed Promotional info -->
                            <div class="promo-info-box">
                                <div class="promo-header">
                                    <span class="promo-tag"><i class="fa-solid fa-crown"></i> Best Seller</span>
                                    <h3 class="promo-title" title="{{ $book->name }}">{{ $book->name }}</h3>
                                    <span class="promo-author">By {{ $book->author?->name ?? 'Unknown Author' }}</span>
                                </div>
                                
                                <p class="promo-desc">{{ Str::limit($book->description, 70, '...') }}</p>
                                
                                <div class="promo-footer">
                                    <span class="promo-price">{{ number_format($book->price) }} Ks</span>
                                    <div class="promo-cta-btn">Details <i class="fa-solid fa-arrow-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Custom SVG Line Art Divider -->
        <div class="row-separator-line-art" style="margin: 1.5rem 0 3.5rem 0;">
            <svg viewBox="0 0 800 40" class="separator-svg">
                <line x1="50" y1="20" x2="360" y2="20" stroke="var(--border-cream)" stroke-width="1.5" stroke-linecap="round" />
                <line x1="440" y1="20" x2="750" y2="20" stroke="var(--border-cream)" stroke-width="1.5" stroke-linecap="round" />
                <path d="M 370 20 Q 385 10 400 20 Q 415 30 430 20" fill="none" stroke="var(--brand-gold)" stroke-width="2" stroke-linecap="round" />
                <path d="M 370 20 Q 385 30 400 20 Q 415 10 430 20" fill="none" stroke="var(--brand-gold)" stroke-width="2" stroke-linecap="round" />
                <circle cx="400" cy="20" r="4" fill="var(--brand-chocolate)" stroke="var(--brand-gold)" stroke-width="1.5" />
                <circle cx="350" cy="20" r="2" fill="var(--border-cream)" />
                <circle cx="450" cy="20" r="2" fill="var(--border-cream)" />
            </svg>
        </div>
    @endif

    <!-- Static Search Box & Sort Dropdown -->
    <div class="store-search-box-container" style="margin-top: 1rem; margin-bottom: 3rem;">
        <div class="hero-search-box">
            <input type="text" id="search-input" onkeyup="filterSearch()" placeholder="Search by book name or author...">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
        
        <!-- Custom Sort Dropdown -->
        <div class="custom-sort-dropdown" id="custom-sort-select">
            <label class="sort-label">Sort By</label>
            <div class="sort-selected" onclick="toggleSortDropdown(event)">
                <span id="current-sort-label">default</span>
                <i class="fa-solid fa-chevron-down sort-arrow"></i>
            </div>
            <div class="sort-options-list display-none" id="sort-options-list">
                <div class="sort-option active" data-value="default" onclick="selectSortOption('default', 'default', event)">default</div>
                <div class="sort-option" data-value="price-low-high" onclick="selectSortOption('price-low-high', 'price low to high', event)">price low to high</div>
                <div class="sort-option" data-value="price-high-low" onclick="selectSortOption('price-high-low', 'price high to low', event)">price high to low</div>
                <div class="sort-option" data-value="name-a-z" onclick="selectSortOption('name-a-z', 'product name a-z', event)">product name a-z</div>
                <div class="sort-option" data-value="name-z-a" onclick="selectSortOption('name-z-a', 'product name z-a', event)">product name z-a</div>
            </div>
        </div>
    </div>

    <!-- Section 2: Classified Hits -->
    <div class="store-section-container">
        <div class="store-section-header" style="margin-bottom: 1.5rem;">
            <h2><i class="fa-solid fa-layer-group text-accent"></i> Classified Hits</h2>
            <p>Explore our library by genres and custom categories</p>
        </div>

        <!-- Category Tabs Filter -->
        <div class="category-tabs-container">
            @php
                $categoryIcons = [
                    'non-fiction' => 'fa-book-open',
                    'fiction' => 'fa-feather-pointed',
                    'self-help' => 'fa-seedling',
                    'emotions' => 'fa-heart',
                    'psychology' => 'fa-brain',
                    'social life drama' => 'fa-masks-theater',
                    'creativity' => 'fa-palette',
                ];
            @endphp
            <button class="filter-tab active" onclick="filterCategory('all', this)">
                <i class="fa-solid fa-layer-group"></i> All
            </button>
            @foreach($classifications as $class)
                @php
                    $normalizedName = strtolower(trim($class->name));
                    $iconClass = $categoryIcons[$normalizedName] ?? 'fa-bookmark';
                @endphp
                <button class="filter-tab" onclick="filterCategory('{{ $class->id }}', this)">
                    <i class="fa-solid {{ $iconClass }}"></i> {{ $class->name }}
                </button>
            @endforeach
        </div>

        <!-- Books Grid Row-Based Layout -->
        <div class="books-rows-container">
            @forelse($books->chunk(12) as $chunk)
                <div class="books-slider-container">
                    <button class="slider-arrow prev" onclick="scrollSlider(this, -1)" aria-label="Previous Page">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="books-row">
                        @foreach($chunk as $book)
                            @php
                                $colorIdx = ($loop->parent->index * 12 + $loop->index) % 4 + 1;
                                $bookColorClass = 'book-color-' . $colorIdx;
                                $categoryIds = $book->classifications->pluck('id')->implode(',');
                            @endphp
                            <div class="book-container-3d" 
                                 data-categories="{{ $categoryIds }}" 
                                 data-title="{{ strtolower($book->name) }}" 
                                 data-author="{{ strtolower($book->author?->name ?? 'unknown') }}">
                                <div class="book-card-premium" 
                                     data-id="{{ $book->id }}"
                                     data-title-raw="{{ $book->name }}"
                                     data-author-raw="{{ $book->author?->name ?? 'Unknown Author' }}"
                                     data-desc="{{ $book->description }}"
                                     data-price="{{ $book->price }}"
                                     data-stock="{{ $book->stock_quantity }}"
                                     data-pages="{{ $book->pages }}"
                                     data-color-class="{{ $bookColorClass }}"
                                     data-pdf-file="{{ $book->pdf_file }}"
                                     data-image="{{ $book->image ? asset('storage/' . $book->image) : '' }}"
                                     data-wishlisted="{{ Auth::guard('customer')->check() && Auth::guard('customer')->user()->wishlistBooks->contains($book->id) ? 'true' : 'false' }}"
                                     onclick="openBookDetailFromElement(this)">
                                    
                                    <!-- Wishlist Heart Button -->
                                    <button class="btn-card-wishlist @if(Auth::guard('customer')->check() && Auth::guard('customer')->user()->wishlistBooks->contains($book->id)) active @endif" 
                                            onclick="event.stopPropagation(); toggleWishlist({{ $book->id }}, this);" 
                                            title="Wishlist" 
                                            data-id="{{ $book->id }}">
                                        <i class="{{ Auth::guard('customer')->check() && Auth::guard('customer')->user()->wishlistBooks->contains($book->id) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                    </button>
                                    
                                    <!-- 3D Book Layout -->
                                    <div class="book-card-3d-wrapper">
                                        <div class="book-3d {{ $bookColorClass }}">
                                            <!-- Cover Front -->
                                            @if($book->image)
                                                <div class="book-cover-front" style="background-image: url('{{ asset('storage/' . $book->image) }}');">
                                                    <div class="book-cover-emboss"></div>
                                                </div>
                                            @else
                                                <div class="book-cover-front">
                                                    <div class="book-cover-emboss">
                                                        <div class="book-cover-title-box">
                                                            <div class="book-cover-title">{{ $book->name }}</div>
                                                            <div class="book-cover-author">{{ $book->author?->name ?? 'Unknown Author' }}</div>
                                                        </div>
                                                        <div class="book-cover-badge">
                                                            <i class="fa-solid fa-book-open-reader"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- Book Spine -->
                                            <div class="book-spine"></div>
                                        </div>
                                    </div>

                                    <!-- Book Metadata -->
                                    <div class="book-card-info">
                                        <h3 class="book-title-text" title="{{ $book->name }}">{{ $book->name }}</h3>
                                        <p class="book-author-text">By {{ $book->author?->name ?? 'Unknown Author' }}</p>
                                        
                                        <div class="book-tags">
                                            @foreach($book->classifications as $bc)
                                                <span class="book-tag">{{ $bc->name }}</span>
                                            @endforeach
                                        </div>

                                        <div class="book-price-row">
                                            <div class="price-stock-box">
                                                <span class="book-price">{{ number_format($book->price) }} Ks</span>
                                                @if($book->stock_quantity > 0)
                                                    <span class="book-stock-status in-stock"><i class="fa-solid fa-check"></i> Stock: {{ $book->stock_quantity }}</span>
                                                @else
                                                    <span class="book-stock-status out-of-stock"><i class="fa-solid fa-xmark"></i> Out of Stock</span>
                                                @endif
                                            </div>
                                            
                                            <div class="card-action-buttons">
                                                <button class="btn-card-reviews" 
                                                        onclick="event.stopPropagation(); openReviewsModal({{ $book->id }}, '{{ addslashes($book->name) }}', '{{ addslashes($book->author?->name ?? 'Unknown Author') }}');" 
                                                        title="View Reviews" 
                                                        aria-label="View Reviews">
                                                    <i class="fa-regular fa-comment-dots"></i>
                                                </button>
                                                @if($book->stock_quantity > 0)
                                                    <button class="btn-card-add-to-cart" 
                                                            onclick="event.stopPropagation(); addToCart({{ $book->id }}, 1);" 
                                                            title="Add to Cart" 
                                                            aria-label="Add to Cart">
                                                        <i class="fa-solid fa-cart-plus"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="slider-arrow next" onclick="scrollSlider(this, 1)" aria-label="Next Page">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                @if(!$loop->last)
                    <!-- Custom SVG Line Art Divider -->
                    <div class="row-separator-line-art">
                        <svg viewBox="0 0 800 40" class="separator-svg">
                            <line x1="50" y1="20" x2="360" y2="20" stroke="var(--border-cream)" stroke-width="1.5" stroke-linecap="round" />
                            <line x1="440" y1="20" x2="750" y2="20" stroke="var(--border-cream)" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M 370 20 Q 385 10 400 20 Q 415 30 430 20" fill="none" stroke="var(--brand-gold)" stroke-width="2" stroke-linecap="round" />
                            <path d="M 370 20 Q 385 30 400 20 Q 415 10 430 20" fill="none" stroke="var(--brand-gold)" stroke-width="2" stroke-linecap="round" />
                            <circle cx="400" cy="20" r="4" fill="var(--brand-chocolate)" stroke="var(--brand-gold)" stroke-width="1.5" />
                            <circle cx="350" cy="20" r="2" fill="var(--border-cream)" />
                            <circle cx="450" cy="20" r="2" fill="var(--border-cream)" />
                        </svg>
                    </div>
                @endif
            @empty
                <div class="empty-books-state">
                    <i class="fa-solid fa-book-medical"></i>
                    <p>There are no books to display at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>

<!-- Book Details Modal (Overlay) -->
<div class="detail-modal-overlay" id="detail-modal" onclick="if(event.target === this) closeBookDetail()">
    <div class="detail-modal-wrapper">
        <div class="detail-modal-content">
            <!-- Close Button -->
            <button class="modal-close-btn" onclick="closeBookDetail()">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <!-- Desktop Layout (Hidden on mobile) -->
            <div class="modal-body-grid">
                <!-- Left Panel: 3D Book Cover View -->
                <div class="modal-book-view">
                    <div class="book-3d-container">
                        <div class="book-3d" id="modal-book-3d">
                            <div class="book-cover-front">
                                <div class="book-cover-emboss">
                                    <div class="book-cover-title-box">
                                        <div class="book-cover-title" id="modal-book-cover-title">Book Title</div>
                                        <div class="book-cover-author" id="modal-book-cover-author">Book Author</div>
                                    </div>
                                    <div class="book-cover-badge">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="book-spine"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Details and action triggers -->
                <div class="modal-book-details">
                    <div class="details-top-group">
                        <h2 id="modal-title" class="details-title">Book Name</h2>
                        <div id="modal-author" class="details-author">By Author</div>
                    </div>

                    <div class="details-meta-grid">
                        <div class="meta-item">
                            <span class="meta-item-lbl">Pages</span>
                            <span id="modal-pages" class="meta-item-val">0</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-item-lbl">Price</span>
                            <span id="modal-price" class="meta-item-val">0 Ks</span>
                        </div>
                    </div>

                    <div class="details-description-box">
                        <h4>Book Summary</h4>
                        <p id="modal-desc">Book description placeholder...</p>
                    </div>

                    <div class="details-action-buttons">
                        <button id="btn-buy-modal" class="btn-buy-book" onclick="addToCartFromModal(this)">
                            <i class="fa-solid fa-cart-plus"></i> Buy Book
                        </button>
                        <a id="btn-download-modal" href="#" class="btn-download-pdf display-none">
                            <i class="fa-solid fa-file-pdf"></i> Download PDF
                        </a>
                        <button id="btn-wishlist-modal" class="btn-wishlist-modal-toggle" onclick="toggleWishlistFromModal(this)">
                            <i class="fa-regular fa-heart"></i> Wishlist
                        </button>
                        <button id="btn-reviews-modal-trigger" class="btn-wishlist-modal-toggle" onclick="openReviewsModalFromDetails()">
                            <i class="fa-regular fa-comment-dots"></i> Reviews
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Layout (Hidden on desktop) -->
            <div class="mobile-detail-layout">
                <div class="mobile-detail-header">
                    <!-- 3D Book Cover Cover -->
                    <div class="mobile-book-cover-wrapper">
                        <div class="book-3d" id="modal-book-3d-mobile">
                            <div class="book-cover-front">
                                <div class="book-cover-emboss">
                                    <div class="book-cover-title-box">
                                        <div class="book-cover-title" id="modal-book-cover-title-mobile">Book Title</div>
                                    </div>
                                </div>
                            </div>
                            <div class="book-spine"></div>
                        </div>
                    </div>
                    <!-- Metadata Info (Right side of cover) -->
                    <div class="mobile-detail-meta">
                        <h2 id="modal-title-mobile" class="details-title">Book Name</h2>
                        <div id="modal-author-mobile" class="details-author">By Author</div>
                        <div class="mobile-meta-pills">
                            <span class="meta-pill"><i class="fa-solid fa-file-lines"></i> <span id="modal-pages-mobile">0</span> pages</span>
                            <span class="meta-pill price-pill"><i class="fa-solid fa-tags"></i> <span id="modal-price-mobile">0 Ks</span></span>
                        </div>
                    </div>
                </div>

                <!-- Description summary -->
                <div class="details-description-box">
                    <h4>Book Summary</h4>
                    <p id="modal-desc-mobile">Book description placeholder...</p>
                </div>

                <!-- Fixed sticky actions bar -->
                <div class="mobile-detail-actions">
                    <button id="btn-buy-mobile" class="btn-m-buy">
                        <i class="fa-solid fa-cart-shopping"></i> Buy Book
                    </button>
                    <a id="btn-download-mobile" href="#" class="btn-m-download display-none">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </a>
                    <button id="btn-wishlist-mobile" class="btn-m-wishlist" onclick="toggleWishlistFromModal(this)">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- You May Also Like Section (Separate Card, Screen-Full) -->
    <div class="detail-recommendations-card" id="detail-recommendations-section">
        <div class="recommendations-container-inner">
            <h3 class="recommendations-title"><i class="fa-solid fa-wand-magic-sparkles text-accent"></i> You may also like</h3>
            <div class="recommendations-row" id="modal-recommendations-list">
                <!-- Recommended books are loaded dynamically here -->
            </div>
        </div>
    </div>
</div>

<!-- Book Reviews Modal (Overlay) -->
<div class="detail-modal-overlay" id="reviews-modal" onclick="if(event.target === this) closeReviewsModal()">
    <div class="detail-modal-content reviews-modal-content">
        <!-- Close Button -->
        <button class="modal-close-btn" onclick="closeReviewsModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="reviews-modal-header">
            <div class="reviews-modal-book-info">
                <span class="reviews-modal-badge"><i class="fa-solid fa-comments"></i> Book Reviews</span>
                <h2 id="reviews-modal-title" class="details-title">Book Name</h2>
                <div id="reviews-modal-author" class="details-author">By Author</div>
            </div>
        </div>

        <!-- Premium Comment-Style Reviews Section -->
        <div class="reviews-section-container">
            <div class="reviews-dashboard-grid">
                <!-- Left: Average rating breakdown -->
                <div class="reviews-summary-card">
                    <div class="avg-rating-value" id="reviews-avg-rating">0.0</div>
                    <div class="avg-rating-stars" id="reviews-avg-stars">
                        <!-- Stars are rendered by JS -->
                    </div>
                    <div class="avg-rating-count" id="reviews-total-count">Based on 0 reviews</div>
                    
                    <div class="rating-bars-container" id="reviews-bars">
                        <!-- Progress bars are rendered by JS -->
                    </div>
                </div>
                
                <!-- Right: Reviews list & Write Review Form -->
                <div class="reviews-content-area">
                    <!-- Write Review (Comment Form Style) -->
                    <div class="write-review-card" id="write-review-container">
                        @auth('customer')
                            <form id="book-review-form" onsubmit="event.preventDefault(); submitReview();">
                                <div class="comment-form-header">
                                    <span class="user-identity">
                                        <img src="{{ auth()->guard('customer')->user()->image ? asset('storage/' . auth()->guard('customer')->user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->guard('customer')->user()->name) . '&background=f1e4d8&color=5c3a21&bold=true' }}" alt="avatar" class="comment-avatar">
                                        <strong>Write a review as {{ auth()->guard('customer')->user()->name }}</strong>
                                    </span>
                                    
                                    <!-- Star Selector -->
                                    <div class="comment-star-selector">
                                        <span class="rating-label">Rating:</span>
                                        <div class="star-rating-interactive">
                                            <i class="fa-regular fa-star star-btn" data-value="1" onclick="setFormRating(1)"></i>
                                            <i class="fa-regular fa-star star-btn" data-value="2" onclick="setFormRating(2)"></i>
                                            <i class="fa-regular fa-star star-btn" data-value="3" onclick="setFormRating(3)"></i>
                                            <i class="fa-regular fa-star star-btn" data-value="4" onclick="setFormRating(4)"></i>
                                            <i class="fa-regular fa-star star-btn" data-value="5" onclick="setFormRating(5)"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="form-rating-value" value="0">
                                    </div>
                                </div>
                                <div class="comment-input-wrapper">
                                    <textarea name="comment" id="form-comment-text" placeholder="Write your review comment here..." rows="3" required></textarea>
                                    <button type="submit" class="btn-submit-comment">Post Review <i class="fa-solid fa-paper-plane"></i></button>
                                </div>
                            </form>
                        @else
                            <div class="login-prompt-comment">
                                <i class="fa-solid fa-lock"></i> Please <a href="{{ route('login') }}">login</a> to write a review.
                            </div>
                        @endauth
                    </div>
                    
                    <!-- Reviews list -->
                    <div class="reviews-comments-list" id="modal-reviews-list">
                        <!-- Loaded dynamically via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/customer/store.js') }}?v=1.2.1"></script>
@endsection
