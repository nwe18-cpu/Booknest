@extends('layouts.app')

@section('title', 'Booknest - Best Books Collection')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.1.6">
@endsection

@section('content')
<!-- Hero Banner Section -->
<section class="hero-banner">
    <!-- Sliding background images container -->
    <div class="hero-slides">
        <div class="hero-slides-track">
            @if($banners->count() > 0)
                @foreach($banners as $banner)
                    <div class="hero-slide" style="background-image: url('{{ asset('storage/' . $banner->image) }}');">
                    </div>
                @endforeach
            @else
                <!-- Fallback Default Banners -->
                <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_1.png');"></div>
                <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_2.png');"></div>
                <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_3.png');"></div>
                <div class="hero-slide" style="background-image: url('/images/hero/hero_bg_4.png');"></div>
            @endif
        </div>
    </div>
</section>

<!-- Main Store Catalog Section -->
<div class="store-catalog-wrapper container" id="store-catalog">
    <!-- Static Search Box, search-input id is unique and singular! -->
    <div class="store-search-box-container">
        <div class="hero-search-box">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" id="search-input" onkeyup="filterSearch()" placeholder="Search by book name or author...">
        </div>
    </div>
    <!-- Section 1: Top Seller & Popular Downloads -->
    @if($books->count() > 0)
        <div class="store-section-container">
            <div class="store-section-header">
                <h2><i class="fa-solid fa-fire text-accent"></i> Top Seller & Popular Downloads</h2>
                <p>Trending and most read books of the week</p>
            </div>
            <div class="books-slider-container">
                <button class="slider-arrow prev" onclick="scrollSlider(this, -1)" aria-label="Previous Page">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <div class="books-row">
                    @foreach($books->take(6) as $book)
                        @php
                            $bookColorClass = 'book-color-' . ($loop->index % 4 + 1);
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
    <div class="detail-modal-content">
        <!-- Close Button -->
        <button class="modal-close-btn" onclick="closeBookDetail()">
            <i class="fa-solid fa-xmark"></i>
        </button>

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
                                    <i class="fa-solid fa-book-open-reader"></i>
                                </div>
                            </div>
                        </div>
                        <div class="book-spine"></div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Metadata & Action -->
            <div class="modal-book-details">
                <h2 id="modal-title" class="details-title">Book Name</h2>
                <div id="modal-author" class="details-author">By Author</div>

                <!-- Meta Details Grid -->
                <div class="details-meta-grid">
                    <div class="meta-item">
                        <span class="meta-item-lbl">Pages</span>
                        <span id="modal-pages" class="meta-item-val">0</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-item-lbl">Stock Status</span>
                        <span id="modal-stock" class="meta-item-val color-success">0 In Stock</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-item-lbl">Price</span>
                        <span id="modal-price" class="meta-item-val color-primary">0 Ks</span>
                    </div>
                </div>

                <div class="details-description-box">
                    <h4>Book Summary</h4>
                    <p id="modal-desc">Book description placeholder...</p>
                </div>

                <!-- Action Button Controls -->
                <div class="details-action-buttons">
                    <button id="btn-add-to-cart-modal" class="btn-buy-book">
                        <i class="fa-solid fa-cart-plus"></i> Buy Book
                    </button>
                    <a id="btn-download-pdf-modal" href="#" class="btn-download-pdf display-none">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </a>
                    <button id="btn-wishlist-modal" class="btn-wishlist-modal-toggle" onclick="toggleWishlistFromModal(this)">
                        <i class="fa-regular fa-heart"></i> Wishlist
                    </button>
                </div>
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
<script src="{{ asset('js/customer/store.js') }}?v=1.0.3"></script>
@endsection
