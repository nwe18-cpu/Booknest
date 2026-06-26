@extends('layouts.app')

@section('title', 'My Library - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}?v=1.1.6">
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.1.9">
@endsection

@section('content')
<!-- Ambient Forest Rain Header Dashboard Banner -->
<section class="dashboard-banner">
    <div class="banner-overlay"></div>
    <div class="banner-content container">
        <div class="dashboard-welcome-header">
            <h2 class="welcome-left-text">My Bookshelf</h2>
            <div class="welcome-right-user">
                <span class="welcome-title">Hello! <span class="username">{{ auth()->guard('customer')->user()->name }}</span></span>
                <div class="welcome-status-line">
                    @if(auth()->guard('customer')->user()->hasActiveSubscription())
                        <span class="welcome-vip-status"><i class="fa-solid fa-crown"></i> VIP ({{ auth()->guard('customer')->user()->getSubscriptionDaysLeft() }} left)</span>
                    @else
                        <span class="welcome-regular-status"><i class="fa-solid fa-user"></i> Regular</span>
                        <a href="{{ route('customer.subscription.index') }}" class="btn-upgrade-badge">Upgrade VIP <i class="fa-solid fa-arrow-right"></i></a>
                    @endif
                </div>
            </div>
        </div>
        <!-- Library Statistics Section (Unified Status Dock) -->
        <div class="stats-dock">
            <!-- Stat Item: Total Books -->
            <div class="stat-dock-item">
                <div class="stat-dock-icon icon-books">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Total Books</span>
                    <span class="stat-dock-value">{{ $stats['total_books'] }} books</span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Currently Reading -->
            <div class="stat-dock-item">
                <div class="stat-dock-icon icon-reading">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Currently Reading</span>
                    <span class="stat-dock-value">{{ $stats['reading'] }} books</span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Completed Books -->
            <div class="stat-dock-item">
                <div class="stat-dock-icon icon-completed">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Completed Books</span>
                    <span class="stat-dock-value">{{ $stats['completed'] }} books</span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Avg. Progress -->
            <div class="stat-dock-item">
                <div class="stat-dock-icon icon-progress">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Avg. Progress</span>
                    <span class="stat-dock-value">{{ $stats['avg_progress'] }}%</span>
                </div>
            </div>
        </div>
        
        <!-- Cozy Reading Music Widget -->
        <div class="cozy-music-widget">
            <div class="music-widget-header">
                <i class="fa-solid fa-music music-icon"></i>
                <span id="music-track-title">Selecting music...</span>
            </div>
            <div class="music-widget-controls">
                <div class="playback-buttons">
                    <button id="music-prev" onclick="playPrevTrack()" class="btn-music-nav" title="Previous Track">
                        <i class="fa-solid fa-backward-step"></i>
                    </button>
                    <button id="music-toggle" onclick="toggleMusicSound()" class="btn-music-toggle" title="Play/Pause">
                        <i class="fa-solid fa-play"></i> Play
                    </button>
                    <button id="music-next" onclick="playNextTrack()" class="btn-music-nav" title="Next Track">
                        <i class="fa-solid fa-forward-step"></i>
                    </button>
                    <button id="music-loop" onclick="toggleMusicLoop()" class="btn-music-loop active" title="Repeat Single Track">
                        <i class="fa-solid fa-repeat"></i>
                    </button>
                </div>
                <div class="volume-slider-wrapper">
                    <i class="fa-solid fa-volume-low"></i>
                    <input type="range" id="music-volume" min="0" max="1" step="0.05" value="0.3" oninput="setMusicVolume(this.value)">
                    <i class="fa-solid fa-volume-high"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Wavy Bottom Shape Divider -->
    <div class="hero-shape-divider">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,42.4V120H0Z" class="shape-fill"></path>
        </svg>
    </div>
</section>



<!-- Virtual Bookshelf Container -->
<div class="bookshelf-wrapper container">
    <div class="bookshelf-header-row">
        <h2 class="section-title"><i class="fa-solid fa-cubes"></i> My 3D Bookshelf</h2>
        
        <div class="bookshelf-header-actions">
            <!-- Wishlist Trigger Button -->
            <button class="btn-wishlist-trigger" onclick="openWishlistModal()">
                <i class="fa-solid fa-heart"></i> Wishlist
                <span class="wishlist-badge">{{ auth()->guard('customer')->user()->wishlistBooks->count() }}</span>
            </button>

            <!-- Local Search Box -->
            <div class="bookshelf-search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" onkeyup="filterSearch()" placeholder="Search books...">
            </div>
        </div>
    </div>

    <!-- Category Tabs Filter -->
    <div class="category-tabs-container">
        <button class="filter-tab active" onclick="filterCategory('all', this)">
            <i class="fa-solid fa-layer-group"></i> All
        </button>
        @foreach($classifications as $class)
            <button class="filter-tab" onclick="filterCategory('{{ $class->id }}', this)">
                <i class="fa-solid fa-bookmark"></i> {{ $class->name }}
            </button>
        @endforeach
    </div>

    <!-- 3D Wooden Bookshelf Structure -->
    <div class="bookshelf-3d-wood">
        @php
            $chunks = $books->chunk(4); // Render 4 books per shelf row
        @endphp

        @forelse($chunks as $shelfBooks)
            <div class="shelf-row">
                <!-- Standing Books Group -->
                <div class="books-lineup">
                    @foreach($shelfBooks as $book)
                        @php
                            $colorIdx = ($loop->parent->index * 4 + $loop->index) % 4 + 1;
                            $bookColorClass = 'book-color-' . $colorIdx;
                            $categoryIds = $book->classifications->pluck('id')->implode(',');
                            
                            $itemProgress = $progress->get($book->id);
                            $currentPage = $itemProgress ? $itemProgress->current_page : 1;
                            $progressPercent = 0;
                            if ($book->pages > 0) {
                                $progressPercent = min(100, round(($currentPage / $book->pages) * 100));
                            }
                        @endphp
                        <div class="book-container-3d" 
                             data-categories="{{ $categoryIds }}" 
                             data-title="{{ strtolower($book->name) }}" 
                             data-author="{{ strtolower($book->author?->name ?? 'unknown') }}">
                            
                            <div class="shelf-book-premium" 
                                 data-id="{{ $book->id }}"
                                 data-title-raw="{{ $book->name }}"
                                 data-author-raw="{{ $book->author?->name ?? 'Unknown Author' }}"
                                 data-desc="{{ $book->description }}"
                                 data-price="{{ $book->price }}"
                                 data-stock="{{ $book->stock_quantity }}"
                                 data-pages="{{ $book->pages }}"
                                 data-color-class="{{ $bookColorClass }}"
                                 data-progress-percent="{{ $progressPercent }}"
                                 data-current-page="{{ $currentPage }}"
                                 data-bookmarked-page="{{ $itemProgress ? $itemProgress->bookmarked_page : '' }}"
                                 data-pages-content="{{ $book->pages_content ?? '[]' }}"
                                 data-image="{{ $book->image ? asset('storage/' . $book->image) : '' }}"
                                 data-pdf-file="{{ $book->pdf_file ? route('customer.books.stream', $book->id) : '' }}"
                                 data-downloaded="true"
                                 data-wishlisted="{{ auth()->guard('customer')->user()->wishlistBooks->contains($book->id) ? 'true' : 'false' }}"
                                 onclick="openBookDetailFromElement(this)">
                                <div class="book-3d {{ $bookColorClass }}">
                                    @if($book->image)
                                        <div class="book-cover-front" style="background-image: url('{{ asset('storage/' . $book->image) }}');">
                                            <div class="book-cover-emboss">
                                                <!-- Tiny progress badge on book cover -->
                                                <div class="book-cover-progress-tag">
                                                    <span>{{ $progressPercent }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="book-cover-front">
                                            <div class="book-cover-emboss">
                                                <div class="book-cover-title-box">
                                                    <div class="book-cover-title">{{ $book->name }}</div>
                                                    <div class="book-cover-author">{{ $book->author?->name ?? 'Unknown Author' }}</div>
                                                </div>
                                                <!-- Tiny progress badge on book cover -->
                                                <div class="book-cover-progress-tag">
                                                    <span>{{ $progressPercent }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="book-spine"></div>
                                </div>
                                <div class="book-shelf-shadow"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Wooden Shelf Plank -->
                <div class="wood-plank">
                    <div class="plank-top"></div>
                    <div class="plank-front"></div>
                </div>
            </div>
        @empty
            <div class="empty-shelf-state">
                <!-- Cozy Empty Nook Illustration -->
                <div class="empty-nook-illustration">
                    <div class="mini-shelf">
                        <div class="shelf-book-shadow-outline"></div>
                        <div class="shelf-plank"></div>
                        <div class="leaning-leaf"></div>
                    </div>
                    <div class="cozy-chair">
                        <div class="chair-legs">
                            <span></span>
                            <span></span>
                        </div>
                        <div class="warm-lamp">
                            <div class="lamp-glow"></div>
                            <div class="lamp-shade"></div>
                            <div class="lamp-pole"></div>
                            <div class="lamp-base"></div>
                        </div>
                    </div>
                </div>
                <h3>Your bookshelf is waiting...</h3>
                <p>Browse our online collection and download your favorite PDF books to start building your personal library.</p>
                <a href="{{ route('customer.store.home') }}" class="btn-goto-store">
                    <i class="fa-solid fa-compass"></i> Explore Bookstore
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Wishlist Modal Overlay -->
<div class="wishlist-modal-overlay" id="wishlist-modal" onclick="if(event.target === this) closeWishlistModal()">
    <div class="wishlist-modal-content">
        <button class="wishlist-modal-close-btn" onclick="closeWishlistModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h3 class="wishlist-modal-title"><i class="fa-solid fa-heart"></i> My Wishlist</h3>
        
        <div class="wishlist-items-container" id="wishlist-items-list">
            @php
                $wishlistBooks = auth()->guard('customer')->user()->wishlistBooks()->with(['author'])->get();
            @endphp
            @forelse($wishlistBooks as $book)
                <div class="wishlist-modal-item" data-wishlist-item-id="{{ $book->id }}">
                    <div class="wishlist-item-cover-wrapper">
                        <div class="wishlist-item-book book-color-{{ ($loop->index % 4) + 1 }}" onclick="openBookFromWishlist({{ $book->id }}, '{{ addslashes($book->name) }}', '{{ addslashes($book->author?->name ?? 'Unknown Author') }}', '{{ addslashes($book->description) }}', {{ $book->price }}, {{ $book->stock_quantity }}, {{ $book->pages }}, 'book-color-{{ ($loop->index % 4) + 1 }}', '{{ $book->image ? asset('storage/' . $book->image) : '' }}')">
                            @if($book->image)
                                <div class="wishlist-item-cover" style="background-image: url('{{ asset('storage/' . $book->image) }}');"></div>
                            @else
                                <div class="wishlist-item-cover">
                                    <span class="wishlist-item-title-fallback">{{ $book->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="wishlist-item-details">
                        <h4 class="wishlist-item-title" onclick="openBookFromWishlist({{ $book->id }}, '{{ addslashes($book->name) }}', '{{ addslashes($book->author?->name ?? 'Unknown Author') }}', '{{ addslashes($book->description) }}', {{ $book->price }}, {{ $book->stock_quantity }}, {{ $book->pages }}, 'book-color-{{ ($loop->index % 4) + 1 }}', '{{ $book->image ? asset('storage/' . $book->image) : '' }}')">{{ $book->name }}</h4>
                        <p class="wishlist-item-author">By {{ $book->author?->name ?? 'Unknown Author' }}</p>
                        <span class="wishlist-item-price">{{ number_format($book->price) }} Ks</span>
                    </div>
                    <div class="wishlist-item-actions">
                        @if($book->stock_quantity > 0)
                            <button class="btn-wishlist-buy" onclick="addToCart({{ $book->id }}, 1); closeWishlistModal();">
                                <i class="fa-solid fa-cart-plus"></i> Buy
                            </button>
                        @else
                            <span class="wishlist-out-of-stock">Out of Stock</span>
                        @endif
                        <button class="btn-wishlist-remove" onclick="toggleWishlist({{ $book->id }}, null)" title="Remove from Wishlist">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="wishlist-empty-state">
                    <i class="fa-regular fa-heart"></i>
                    <p>Your wishlist is empty.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Library Book Detail Modal (Read Overlay Trigger) -->
<div class="detail-modal-overlay" id="detail-modal" onclick="if(event.target === this) closeBookDetail()">
    <div class="detail-modal-content">
        <!-- Close Button -->
        <button class="modal-close-btn" onclick="closeBookDetail()">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="modal-body-grid">
            <!-- Left Side: 3D Book view -->
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

            <!-- Right Side: details & triggers -->
            <div class="modal-book-details">
                <h2 id="modal-title" class="details-title">Book Name</h2>
                <div id="modal-author" class="details-author">By Author</div>

                <div class="details-meta-grid">
                    <div class="meta-item">
                        <span class="meta-item-lbl">Pages</span>
                        <span id="modal-pages" class="meta-item-val">0</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-item-lbl">Price</span>
                        <span id="modal-price" class="meta-item-val">0 Ks</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-item-lbl">Progress</span>
                        <span id="modal-progress" class="meta-item-val color-success">0%</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-item-lbl">Bookmark</span>
                        <span id="modal-bookmark" class="meta-item-val text-mute">None</span>
                    </div>
                </div>

                <div class="details-description-box">
                    <h4>Book Summary</h4>
                    <p id="modal-desc">Book description placeholder...</p>
                </div>

                <div class="details-action-buttons">
                    <button id="btn-read-trigger" class="btn-read-book">
                        <i class="fa-solid fa-book-open"></i> Read Book
                    </button>
                    <button id="btn-resume-bookmark" class="btn-resume-bookmark display-none">
                        <i class="fa-solid fa-bookmark"></i> Resume from Bookmark
                    </button>
                    <button id="btn-add-to-cart-modal" class="btn-buy-book display-none" onclick="addToCartFromDashboardModal(this)">
                        <i class="fa-solid fa-cart-plus"></i> Buy Book
                    </button>
                    <button id="btn-wishlist-modal" class="btn-wishlist-modal-toggle" onclick="toggleWishlistFromModal(this)">
                        <i class="fa-regular fa-heart"></i> Wishlist
                    </button>
                </div>
            </div>
        </div>

        <!-- Premium Comment-Style Reviews Section -->
        <div class="reviews-section-container">
            <h3 class="reviews-section-title"><i class="fa-solid fa-comments"></i> Customer Reviews</h3>
            
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

<!-- Immersive 3D Page-Turning E-book Reader Viewport Overlay -->
<div class="reader-overlay" id="reader-overlay" 
     data-progress-url="{{ route('customer.save_progress') }}"
     data-bookmark-url="{{ route('customer.toggle_bookmark') }}">
    <div class="reader-header-bar">
        <div class="reader-title-info">
            <span class="reader-back-btn" onclick="closeReader()">
                <i class="fa-solid fa-arrow-left-long"></i>
            </span>
            <div>
                <h3 id="reader-book-name">Book Title</h3>
                <span id="reader-book-author" class="text-mute">Book Author</span>
            </div>
        </div>
        
        <!-- Zoom Controls for PDF Reader -->
        <div class="reader-zoom-controls" id="reader-zoom-controls" style="display: none;">
            <button onclick="zoomOut()" class="btn-zoom" title="Zoom Out">
                <i class="fa-solid fa-magnifying-glass-minus"></i>
            </button>
            <span id="zoom-percent">100%</span>
            <button onclick="zoomIn()" class="btn-zoom" title="Zoom In">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
            </button>
            <button onclick="zoomReset()" class="btn-zoom" title="Reset Zoom">
                <i class="fa-solid fa-rotate-left"></i>
            </button>
        </div>

        <!-- Bookmark Current Page Toggle Button -->
        <button id="btn-reader-bookmark" class="btn-reader-bookmark" onclick="toggleBookmark()" title="Bookmark Current Page">
            <i class="fa-regular fa-bookmark"></i>
        </button>

        <div class="reader-close-action" onclick="closeReader()">
            <i class="fa-solid fa-xmark"></i>
        </div>
    </div>

    <!-- The 3D Book Viewport -->
    <div class="reader-book-viewport">
        <div class="book-double-page-wrapper">
            <!-- Left Page -->
            <div class="reader-page page-left">
                <div class="page-header" id="page-l-header">CHAPTER I</div>
                <div class="page-inner-content" id="page-l-content">
                    <canvas id="canvas-page-l" class="pdf-page-canvas"></canvas>
                    <div class="reader-text-container">
                        <p>Loading book contents...</p>
                    </div>
                </div>
                <div class="page-footer-num" id="page-l-num">Page 1</div>
            </div>

            <!-- Flipping Center Sheet Sheet -->
            <div class="reader-page-flipping" id="flipping-page">
                <!-- Front Page side -->
                <div class="page-face face-front">
                    <div class="page-header" id="page-flip-f-header">CHAPTER I</div>
                    <div class="page-inner-content" id="page-flip-f-content">
                        <canvas id="canvas-page-flip-f" class="pdf-page-canvas"></canvas>
                        <div class="reader-text-container">
                            <p></p>
                        </div>
                    </div>
                    <div class="page-footer-num" id="page-flip-f-num"></div>
                </div>
                <!-- Back Page side -->
                <div class="page-face face-back">
                    <div class="page-header" id="page-flip-b-header">CHAPTER II</div>
                    <div class="page-inner-content" id="page-flip-b-content">
                        <canvas id="canvas-page-flip-b" class="pdf-page-canvas"></canvas>
                        <div class="reader-text-container">
                            <p></p>
                        </div>
                    </div>
                    <div class="page-footer-num" id="page-flip-b-num"></div>
                </div>
            </div>

            <!-- Right Page -->
            <div class="reader-page page-right">
                <div class="page-header" id="page-r-header">CHAPTER II</div>
                <div class="page-inner-content" id="page-r-content">
                    <canvas id="canvas-page-r" class="pdf-page-canvas"></canvas>
                    <div class="reader-text-container">
                        <p>Loading...</p>
                    </div>
                </div>
                <div class="page-footer-num" id="page-r-num">Page 2</div>
            </div>
        </div>
    </div>

    <!-- Reader Control Dock -->
    <div class="reader-control-dock">
        <button class="nav-page-btn" id="btn-prev-page" onclick="prevPage()">
            <i class="fa-solid fa-chevron-left"></i> Previous
        </button>

        <div class="reader-progress-info">
            <div class="reader-progress-top-row">
                <span class="progress-details" id="page-indicator-text">Page 1 of 100 (0% completed)</span>
                <!-- Jump to Bookmark Link -->
                <span class="bookmark-quick-jump" id="bookmark-quick-jump" style="display: none;" onclick="jumpToBookmark()">
                    <i class="fa-solid fa-bookmark"></i> Go to Bookmark (Page <span id="bookmark-page-num">0</span>)
                </span>
            </div>
            <div class="reader-progress-track">
                <div class="progress-fill-line" id="reader-progress-fill-bar"></div>
            </div>
        </div>

        <button class="nav-page-btn" id="btn-next-page" onclick="nextPage()">
            Next <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
    }
</script>
<script src="{{ asset('js/customer/dashboard_custom.js') }}?v=1.2.7"></script>
@endsection
