@extends('layouts.app')

@section('title', 'My Library - Booknest')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/customer/store.css') }}?v=1.5.7">
<link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}?v=1.4.4">
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
            <div class="stat-dock-item" title="Total Books">
                <div class="stat-dock-icon icon-books">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Total Books</span>
                    <span class="stat-dock-value">{{ $stats['total_books'] }}<span class="stat-unit"> books</span></span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Currently Reading -->
            <div class="stat-dock-item" title="Currently Reading">
                <div class="stat-dock-icon icon-reading">
                    <i class="fa-solid fa-book-open-reader"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Currently Reading</span>
                    <span class="stat-dock-value">{{ $stats['reading'] }}<span class="stat-unit"> books</span></span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Completed Books -->
            <div class="stat-dock-item" title="Completed Books">
                <div class="stat-dock-icon icon-completed">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-dock-meta">
                    <span class="stat-dock-label">Completed Books</span>
                    <span class="stat-dock-value">{{ $stats['completed'] }}<span class="stat-unit"> books</span></span>
                </div>
            </div>

            <div class="stat-dock-divider"></div>

            <!-- Stat Item: Avg. Progress -->
            <div class="stat-dock-item" title="Avg. Progress">
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

            <!-- App Settings Trigger Button -->
            <button class="btn-wishlist-trigger btn-settings-trigger" onclick="openAppSettingsModal()" title="App Settings">
                <i class="fa-solid fa-sliders"></i> Settings
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
                @php
                    $bookColorClass = 'book-color-' . (($loop->index % 4) + 1);
                @endphp
                <div class="wishlist-modal-item" data-wishlist-item-id="{{ $book->id }}">
                    <div class="wishlist-item-cover-wrapper">
                        <div class="wishlist-item-book {{ $bookColorClass }}" 
                             data-id="{{ $book->id }}"
                             data-title-raw="{{ $book->name }}"
                             data-author-raw="{{ $book->author?->name ?? 'Unknown Author' }}"
                             data-desc="{{ $book->description }}"
                             data-price="{{ $book->price }}"
                             data-stock="{{ $book->stock_quantity }}"
                             data-pages="{{ $book->pages }}"
                             data-color-class="{{ $bookColorClass }}"
                             data-image="{{ $book->image ? asset('storage/' . $book->image) : '' }}"
                             data-pdf-file=""
                             data-downloaded="false"
                             data-wishlisted="true"
                             onclick="openBookFromWishlist(this)">
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
                        <h4 class="wishlist-item-title" 
                            data-id="{{ $book->id }}"
                            data-title-raw="{{ $book->name }}"
                            data-author-raw="{{ $book->author?->name ?? 'Unknown Author' }}"
                            data-desc="{{ $book->description }}"
                            data-price="{{ $book->price }}"
                            data-stock="{{ $book->stock_quantity }}"
                            data-pages="{{ $book->pages }}"
                            data-color-class="{{ $bookColorClass }}"
                            data-image="{{ $book->image ? asset('storage/' . $book->image) : '' }}"
                            data-pdf-file=""
                            data-downloaded="false"
                            data-wishlisted="true"
                            onclick="openBookFromWishlist(this)">{{ $book->name }}</h4>
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

<!-- App Settings Modal Overlay -->
<div class="wishlist-modal-overlay settings-modal-overlay" id="settings-modal" onclick="if(event.target === this) closeAppSettingsModal()">
    <div class="wishlist-modal-content settings-modal-content">
        <button class="wishlist-modal-close-btn" onclick="closeAppSettingsModal()">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <h3 class="wishlist-modal-title"><i class="fa-solid fa-sliders"></i> App Settings</h3>
        
        <div class="settings-modal-body">
            <!-- Section: Reading Theme -->
            <div class="settings-section">
                <h4><i class="fa-solid fa-palette"></i> Reading Theme</h4>
                <div class="theme-options-grid">
                    <button class="theme-opt-btn theme-light active" onclick="setSettingTheme('light')">
                        <span class="theme-color-dot" style="background-color: #FAF9F5; border: 1px solid #d4cbb8;"></span>
                        <span class="theme-opt-label">Light (Default)</span>
                    </button>
                    <button class="theme-opt-btn theme-dark" onclick="setSettingTheme('dark')">
                        <span class="theme-color-dot" style="background-color: #122521; border: 1px solid #CCA353;"></span>
                        <span class="theme-opt-label">Dark Room</span>
                    </button>
                    <button class="theme-opt-btn theme-sepia" onclick="setSettingTheme('sepia')">
                        <span class="theme-color-dot" style="background-color: #f4edd8; border: 1px solid #c7ba9d;"></span>
                        <span class="theme-opt-label">Sepia Paper</span>
                    </button>
                </div>
            </div>

            <!-- Section: Font Preferences -->
            <div class="settings-section font-section-grid">
                <div class="form-group-settings">
                    <label for="setting-font-size"><i class="fa-solid fa-text-height"></i> Font Size</label>
                    <select id="setting-font-size" class="settings-select" onchange="updateSettingsPreview()">
                        <option value="sm">Small</option>
                        <option value="md" selected>Medium (Default)</option>
                        <option value="lg">Large</option>
                        <option value="xl">Extra Large</option>
                    </select>
                </div>
                
                <div class="form-group-settings">
                    <label for="setting-font-style"><i class="fa-solid fa-font"></i> Font Style</label>
                    <select id="setting-font-style" class="settings-select" onchange="updateSettingsPreview()">
                        <option value="sans" selected>Sans-Serif (Modern)</option>
                        <option value="serif">Serif (Classic)</option>
                        <option value="mono">Monospace (Code)</option>
                    </select>
                </div>
            </div>

            <!-- Section: Toggle Switches -->
            <div class="settings-section toggle-switches-grid">
                <div class="settings-toggle-row">
                    <div class="toggle-meta">
                        <strong>Auto-Play Ambient Music</strong>
                        <p class="toggle-desc">Automatically play cozy background music when opening the book reader.</p>
                    </div>
                    <label class="switch-toggle-label">
                        <input type="checkbox" id="setting-auto-music" checked onchange="updateSettingsPreview()">
                        <span class="switch-slider"></span>
                    </label>
                </div>
                
                <div class="settings-toggle-row">
                    <div class="toggle-meta">
                        <strong>Auto-Save Progress</strong>
                        <p class="toggle-desc">Automatically save book reading page counts to your cloud library.</p>
                    </div>
                    <label class="switch-toggle-label">
                        <input type="checkbox" id="setting-auto-save" checked onchange="updateSettingsPreview()">
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Section: Live Preview Box -->
            <div class="settings-section">
                <h4><i class="fa-solid fa-eye"></i> Reading Live Preview</h4>
                <div class="settings-preview-box" id="settings-preview-box">
                    <h5 class="preview-chapter">CHAPTER I: The Reading Cozy</h5>
                    <p class="preview-text">Booknest offers an immersive digital reading experience. Change the settings above and watch this text style transform dynamically to find your perfect reading comfort.</p>
                </div>
            </div>
        </div>
        
        <div class="settings-modal-footer">
            <button class="btn-settings-save" onclick="saveAppSettings()">
                <i class="fa-solid fa-circle-check"></i> Apply Settings
            </button>
        </div>
    </div>
</div>

<!-- Library Book Detail Modal (Read Overlay Trigger) -->
<div class="detail-modal-overlay" id="detail-modal" onclick="if(event.target === this) closeBookDetail()">
    <div class="detail-modal-wrapper">
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
                        
                        <div class="details-secondary-actions">
                            <button id="btn-wishlist-modal" class="btn-wishlist-modal-toggle" onclick="toggleWishlistFromModal(this)">
                                <i class="fa-regular fa-heart"></i> Wishlist
                            </button>
                            <!-- Reviews Trigger Button -->
                            <button id="btn-reviews-modal-trigger" class="btn-wishlist-modal-toggle" onclick="openReviewsModalFromDashboard()">
                                <i class="fa-regular fa-comment-dots"></i> Reviews
                            </button>
                            <!-- Remove from Library Button -->
                            <button id="btn-remove-library" class="btn-wishlist-modal-toggle btn-danger-remove" onclick="removeBookFromLibraryTrigger()">
                                <i class="fa-regular fa-trash-can"></i> Remove
                            </button>
                        </div>
                    </div>
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
        <div class="reader-zoom-controls display-none" id="reader-zoom-controls">
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

        <!-- Action Buttons Group (Right side) -->
        <div class="reader-action-group" style="display: flex; align-items: center; gap: 18px;">
            <!-- Reader Settings Gear Button -->
            <button class="btn-reader-bookmark btn-reader-settings-gear" onclick="openAppSettingsModal()" title="Reader Settings" style="margin: 0 !important;">
                <i class="fa-solid fa-gear"></i>
            </button>

            <!-- Bookmark Current Page Toggle Button -->
            <button id="btn-reader-bookmark" class="btn-reader-bookmark" onclick="toggleBookmark()" title="Bookmark Current Page" style="margin: 0 !important;">
                <i class="fa-regular fa-bookmark"></i>
            </button>

            <div class="reader-close-action" onclick="closeReader()" style="margin: 0 !important; padding-left: 6px;">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Wattpad-Style Layout Viewport -->
    <div class="reader-book-viewport">
        <div class="reader-wattpad-layout">
            <!-- Left Sidebar: Author Profile -->
            <div class="reader-left-sidebar">
                <div class="reader-author-card" style="padding-bottom: 1.5rem !important;">
                    <img id="reader-author-avatar" src="" class="reader-author-img" onerror="this.src='https://ui-avatars.com/api/?name=Author&background=d4cbb8&color=122521&bold=true'">
                    <span class="by-author-lbl">by</span>
                    <strong id="reader-author-name" class="reader-author-name-text" style="margin-bottom: 0 !important;">Author Name</strong>
                </div>
                <div class="reader-share-icons">
                    <span class="share-title">Contact</span>
                    <a href="https://www.facebook.com" target="_blank" class="share-icon share-fb" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com" target="_blank" class="share-icon share-tt" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="https://t.me" target="_blank" class="share-icon share-tg" title="Telegram"><i class="fa-brands fa-telegram"></i></a>
                </div>
            </div>

            <!-- Center Column: Reading Content -->
            <div class="reader-center-content">
                <div class="reader-article-card">
                    <div class="reader-article-header">
                        <span class="reader-episode-num" id="reader-episode-num">Chapter 1</span>
                        <h1 id="reader-chapter-title" class="reader-chapter-title-text">Book Title</h1>
                        <div class="reader-article-stats">
                            <span><i class="fa-solid fa-star" style="color: var(--brand-gold);"></i> <span id="reader-avg-rating">0.0</span> Rating</span>
                            <span><i class="fa-regular fa-comment"></i> <span id="reader-comments-count">0</span> Comments</span>
                        </div>
                    </div>

                    <!-- Left Page content containers are recycled for unified styling -->
                    <div class="reader-page page-left" style="width:100% !important; margin:0 !important; box-shadow:none !important; border:none !important; background:transparent !important; padding: 0 !important;">
                        <div class="page-inner-content" id="page-l-content">
                            <!-- Canvas displays here in PDF mode -->
                            <canvas id="canvas-page-l" class="pdf-page-canvas"></canvas>
                            <!-- Text displays here in Text mode -->
                            <div class="reader-text-container">
                                <p>Loading book contents...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Recommended Books -->
            <div class="reader-right-sidebar">
                <h4 class="sidebar-rec-title">YOU'LL ALSO LIKE</h4>
                <div class="rec-books-list" id="reader-rec-books-list">
                    <!-- Loaded dynamically via JS -->
                </div>
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
                <span class="bookmark-quick-jump display-none" id="bookmark-quick-jump" onclick="jumpToBookmark()">
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

<!-- Custom Premium Confirmation Modal Overlay -->
<div class="confirm-modal-overlay" id="confirm-delete-modal" onclick="if(event.target === this) closeConfirmDeleteModal()">
    <div class="confirm-modal-card">
        <div class="confirm-modal-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="confirm-modal-title">Remove Book</h3>
        <p class="confirm-modal-message">Are you sure you want to remove <strong id="confirm-book-title"></strong> from your library? This will delete your reading progress.</p>
        <div class="confirm-modal-actions">
            <button type="button" onclick="closeConfirmDeleteModal()" class="btn-confirm-cancel">Cancel</button>
            <button type="button" id="btn-confirm-delete-action" class="btn-confirm-delete">Remove</button>
        </div>
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
<script src="{{ asset('js/customer/dashboard_custom.js') }}?v=1.3.7"></script>
@endsection
