// Cart Drawer Controls
function toggleCartDrawer() {
    const overlay = document.getElementById('cart-drawer-overlay');
    if (overlay) {
        overlay.classList.toggle('active');
        if (overlay.classList.contains('active')) {
            fetchCart();
        }
    }
}

function closeCartDrawer() {
    const overlay = document.getElementById('cart-drawer-overlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
}

// Fetch Cart Data and Render in Drawer
function fetchCart() {
    fetch('/store/cart/data')
        .then(res => res.json())
        .then(data => {
            renderCartDrawer(data);
            updateCartBadge(data.total_quantity);
        })
        .catch(err => {
            console.error('Failed to fetch cart data', err);
        });
}

// Update Header Cart Badge
function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.innerText = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

// Render Cart Drawer HTML and Cart Page Content
function renderCartDrawer(data) {
    const body = document.getElementById('cart-drawer-body');
    const totalEl = document.getElementById('cart-total-amount');
    
    if (body) {
        if (!data.items || Object.keys(data.items).length === 0) {
            body.innerHTML = `
                <div class="cart-empty-state">
                    <i class="fa-solid fa-shopping-basket"></i>
                    <p>Your shopping cart is empty.</p>
                </div>`;
            if (totalEl) totalEl.innerText = '0 Ks';
        } else {
            let html = '';
            for (const [id, item] of Object.entries(data.items)) {
                html += `
                    <div class="cart-item">
                        <div class="cart-item-book-container">
                            <div class="cart-item-book">
                                <div class="cart-item-cover ${item.cover_class}">
                                    <span class="cart-item-cover-text">${escapeHtml(item.name)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-title" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                            <div class="cart-item-author">By ${escapeHtml(item.author || 'Unknown')}</div>
                            <div class="cart-item-price">${parseFloat(item.price).toLocaleString()} ကျပ်</div>
                        </div>
                        <div class="cart-item-actions">
                            <button class="btn-remove-item" onclick="removeCartItem(${id})">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="updateCartQuantity(${id}, ${item.quantity - 1})">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <div class="quantity-value">${item.quantity}</div>
                                <button class="quantity-btn" onclick="updateCartQuantity(${id}, ${item.quantity + 1})">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
            body.innerHTML = html;
            if (totalEl) totalEl.innerText = parseFloat(data.total_amount).toLocaleString() + ' Ks';
        }
    }

    const mainContent = document.getElementById('cart-page-content');
    if (mainContent) {
        const homeUrl = mainContent.getAttribute('data-home-url');
        const checkoutUrl = mainContent.getAttribute('data-checkout-url');
        
        if (!data.items || Object.keys(data.items).length === 0) {
            mainContent.innerHTML = `
                <div class="empty-page-card">
                    <div class="empty-page-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h2 class="empty-page-title">Your Cart is Empty</h2>
                    <p class="empty-page-desc">You haven't added any books to your shopping cart yet. Visit our store to find your next read!</p>
                    <a href="${homeUrl}" class="btn-primary">
                        <i class="fa-solid fa-store"></i> Browse Bookstore Catalog
                    </a>
                </div>
            `;
        } else {
            let itemsHtml = '';
            for (const [id, item] of Object.entries(data.items)) {
                itemsHtml += `
                    <div class="cart-item">
                        <div class="cart-item-book-container">
                            <div class="cart-item-book">
                                <div class="cart-item-cover ${item.cover_class}">
                                    <span class="cart-item-cover-text">${escapeHtml(item.name)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-title" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                            <div class="cart-item-author">By ${escapeHtml(item.author || 'Unknown')}</div>
                            <div class="cart-item-price">${parseFloat(item.price).toLocaleString()} Ks</div>
                        </div>
                        <div class="cart-item-actions">
                            <button class="btn-remove-item" onclick="removeCartItem(${id})">
                                <i class="fa-regular fa-trash-can"></i> Remove
                            </button>
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="updateCartQuantity(${id}, ${item.quantity - 1})">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <div class="quantity-value">${item.quantity}</div>
                                <button class="quantity-btn" onclick="updateCartQuantity(${id}, ${item.quantity + 1})">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            mainContent.innerHTML = `
                <div class="checkout-grid">
                    <div class="checkout-card flex-column-gap">
                        <div class="checkout-section-title">
                            <i class="fa-solid fa-basket-shopping"></i> Selected Books
                        </div>
                        ${itemsHtml}
                    </div>
                    
                    <div class="checkout-card">
                        <div class="checkout-section-title">
                            <i class="fa-solid fa-receipt"></i> Cart Summary
                        </div>
                        <div class="checkout-totals">
                            <div class="checkout-totals-row">
                                <span>Total Quantity:</span>
                                <span>${data.total_quantity} items</span>
                            </div>
                            <div class="checkout-totals-row">
                                <span>Shipping:</span>
                                <span class="color-success">Free Shipping</span>
                            </div>
                            <div class="checkout-totals-row checkout-totals-total">
                                <span>Grand Total:</span>
                                <span>${parseFloat(data.total_amount).toLocaleString()} Ks</span>
                            </div>
                        </div>
                        <a href="${checkoutUrl}" class="btn-checkout">
                            <i class="fa-solid fa-credit-card"></i> Proceed to Checkout
                        </a>
                    </div>
                </div>
            `;
        }
    }
}

// Add Item to Cart
function addToCart(itemId, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/store/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: quantity
        })
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;
        if (data.success) {
            updateCartBadge(data.total_quantity);
            fetchCart(); // Update drawer content silently in the background
        } else {
            showToast(data.message || 'Failed to add item to cart', true);
        }
    })
    .catch(err => {
        console.error('Add to cart failed', err);
        showToast('Error connecting to the server', true);
    });
}

// Update Cart Quantity
function updateCartQuantity(itemId, quantity) {
    if (quantity <= 0) {
        removeCartItem(itemId);
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/store/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            quantity: quantity
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            fetchCart();
        } else {
            showToast(data.message || 'Failed to update quantity', true);
        }
    })
    .catch(err => {
        console.error('Update quantity failed', err);
    });
}

// Remove Item from Cart
function removeCartItem(itemId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/store/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, false);
            fetchCart();
        } else {
            showToast('Failed to remove item', true);
        }
    })
    .catch(err => {
        console.error('Remove item failed', err);
    });
}

// Modal Detail Window Controls
function openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, pdfFile, image, isWishlisted) {
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-author').innerText = 'By ' + author;
    document.getElementById('modal-desc').innerText = desc || 'No description available for this book.';
    document.getElementById('modal-pages').innerText = totalPages;
    document.getElementById('modal-price').innerText = parseFloat(price).toLocaleString() + ' Ks';
    
    // Set Wishlist button state in modal
    const wishlistBtn = document.getElementById('btn-wishlist-modal');
    if (wishlistBtn) {
        wishlistBtn.setAttribute('data-id', id);
        if (isWishlisted) {
            wishlistBtn.classList.add('active');
            wishlistBtn.innerHTML = '<i class="fa-solid fa-heart"></i> Wishlisted';
        } else {
            wishlistBtn.classList.remove('active');
            wishlistBtn.innerHTML = '<i class="fa-regular fa-heart"></i> Wishlist';
        }
    }
    
    const stockEl = document.getElementById('modal-stock');
    if (stockEl) {
        if (parseInt(stock) > 0) {
            stockEl.innerText = stock + ' In Stock';
            stockEl.className = 'meta-item-val color-success';
            document.getElementById('btn-add-to-cart-modal').disabled = false;
        } else {
            stockEl.innerText = 'Out of Stock';
            stockEl.className = 'meta-item-val color-danger';
            document.getElementById('btn-add-to-cart-modal').disabled = true;
        }
    }

    // PDF Download Button toggle
    const pdfBtn = document.getElementById('btn-download-pdf-modal');
    if (pdfBtn) {
        if (pdfFile) {
            pdfBtn.href = '/store/books/' + id + '/download';
            pdfBtn.classList.remove('display-none');
        } else {
            pdfBtn.classList.add('display-none');
        }
    }

    // Reapply coloring classes and image background
    const modalBook3D = document.getElementById('modal-book-3d');
    if (modalBook3D) {
        modalBook3D.className = 'book-3d ' + colorClass;
        const coverFront = modalBook3D.querySelector('.book-cover-front');
        const titleBox = modalBook3D.querySelector('.book-cover-title-box');
        const badge = modalBook3D.querySelector('.book-cover-badge');
        
        if (image) {
            coverFront.style.backgroundImage = "url('" + image + "')";
            coverFront.style.backgroundSize = "cover";
            coverFront.style.backgroundPosition = "center";
            if (titleBox) titleBox.style.display = 'none';
            if (badge) badge.style.display = 'none';
        } else {
            coverFront.style.backgroundImage = "none";
            if (titleBox) titleBox.style.display = 'flex';
            if (badge) badge.style.display = 'flex';
        }
    }
    
    const coverTitle = document.getElementById('modal-book-cover-title');
    if (coverTitle) coverTitle.innerText = title;
    
    const coverAuthor = document.getElementById('modal-book-cover-author');
    if (coverAuthor) coverAuthor.innerText = author;

    // Trigger click button
    const cartBtn = document.getElementById('btn-add-to-cart-modal');
    if (cartBtn) {
        cartBtn.onclick = function() {
            closeBookDetail();
            addToCart(id, 1);
        };
    }

    const modal = document.getElementById('detail-modal');
    if (modal) modal.classList.add('active');
}

function closeBookDetail() {
    const modal = document.getElementById('detail-modal');
    if (modal) modal.classList.remove('active');
}

function openReviewsModal(id, title, author) {
    currentDetailBookId = id;
    document.getElementById('reviews-modal-title').innerText = title;
    document.getElementById('reviews-modal-author').innerText = 'By ' + author;
    
    resetReviewForm();
    loadBookReviews(id);
    
    const modal = document.getElementById('reviews-modal');
    if (modal) modal.classList.add('active');
}

function closeReviewsModal() {
    const modal = document.getElementById('reviews-modal');
    if (modal) modal.classList.remove('active');
}

function openBookDetailFromElement(el) {
    const id = el.getAttribute('data-id');
    const title = el.getAttribute('data-title-raw');
    const author = el.getAttribute('data-author-raw');
    const desc = el.getAttribute('data-desc');
    const price = el.getAttribute('data-price');
    const stock = el.getAttribute('data-stock');
    const totalPages = el.getAttribute('data-pages');
    const colorClass = el.getAttribute('data-color-class');
    const pdfFile = el.getAttribute('data-pdf-file');
    const image = el.getAttribute('data-image');
    const isWishlisted = el.getAttribute('data-wishlisted') === 'true';
    
    openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, pdfFile, image, isWishlisted);
}

// Search & Filters for Catalog
function updateRowVisibility() {
    const containers = Array.from(document.querySelectorAll('.books-slider-container'));
    
    // 1. Update container display based on visible books
    containers.forEach(container => {
        const books = container.querySelectorAll('.book-container-3d');
        let visibleCount = 0;
        books.forEach(book => {
            if (book.style.display !== 'none') {
                visibleCount++;
            }
        });
        container.style.display = visibleCount === 0 ? 'none' : 'block';
        
        // Hide/show the parent section wrapper so headers are hidden when empty
        const sectionParent = container.closest('.store-section-container');
        if (sectionParent) {
            sectionParent.style.display = visibleCount === 0 ? 'none' : 'block';
        }
        
        const row = container.querySelector('.books-row');
        if (row) {
            row.style.display = visibleCount === 0 ? 'none' : 'flex';
            // Sync slider arrows visibility based on scroll boundaries
            syncSliderArrows(row);
        }
    });

    // 2. Hide all separators first
    const separators = document.querySelectorAll('.row-separator-line-art');
    separators.forEach(sep => {
        sep.style.display = 'none';
    });

    // 3. Find all currently visible containers
    const visibleContainers = containers.filter(container => container.style.display !== 'none');
    
    // 4. Place exactly one separator between each adjacent visible container
    for (let i = 0; i < visibleContainers.length - 1; i++) {
        const currentContainer = visibleContainers[i];
        
        // Find the next separator element following the current container
        let nextEl = currentContainer.nextElementSibling;
        while (nextEl && !nextEl.classList.contains('books-slider-container')) {
            if (nextEl.classList.contains('row-separator-line-art')) {
                nextEl.style.display = 'flex';
                break;
            }
            nextEl = nextEl.nextElementSibling;
        }
    }
}

// Scroll slider by clicking next/prev buttons
function scrollSlider(btn, direction) {
    const container = btn.parentElement;
    const row = container.querySelector('.books-row');
    if (row) {
        // Scroll by almost the full width of the visible row
        const scrollAmount = direction * (row.clientWidth - 50);
        row.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
}

// Sync arrow buttons visibility based on scroll boundaries
function syncSliderArrows(row) {
    const container = row.parentElement;
    if (!container || !container.classList.contains('books-slider-container')) return;
    
    const prevBtn = container.querySelector('.slider-arrow.prev');
    const nextBtn = container.querySelector('.slider-arrow.next');
    
    if (prevBtn && nextBtn) {
        const isScrollable = row.scrollWidth > row.clientWidth;
        
        if (!isScrollable) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            // Hide prev arrow if scrolled all the way to the left
            prevBtn.style.display = row.scrollLeft <= 5 ? 'none' : 'flex';
            // Hide next arrow if scrolled all the way to the right
            nextBtn.style.display = (row.scrollLeft + row.clientWidth >= row.scrollWidth - 10) ? 'none' : 'flex';
        }
    }
}

function filterCategory(catId, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const books = document.querySelectorAll('.book-container-3d');
    books.forEach(book => {
        const bookCatsStr = book.getAttribute('data-categories') || '';
        const bookCats = bookCatsStr ? bookCatsStr.split(',') : [];
        if (catId === 'all' || bookCats.includes(catId.toString())) {
            book.style.display = 'block';
            setTimeout(() => book.style.opacity = '1', 50);
        } else {
            book.style.opacity = '0';
            setTimeout(() => book.style.display = 'none', 300);
        }
    });

    // Update rows and dividers visibility after animations
    setTimeout(updateRowVisibility, 350);
}

function filterSearch() {
    const query = document.getElementById('search-input').value.toLowerCase();
    const books = document.querySelectorAll('.book-container-3d');
    
    books.forEach(book => {
        const title = book.getAttribute('data-title');
        const author = book.getAttribute('data-author');
        
        if (title.includes(query) || author.includes(query)) {
            book.style.display = 'block';
            book.style.opacity = '1';
        } else {
            book.style.opacity = '0';
            setTimeout(() => book.style.display = 'none', 300);
        }
    });

    // Update rows and dividers visibility after animations
    setTimeout(updateRowVisibility, 350);
}

// Select Payment Radio Cards
function selectPaymentMethod(method) {
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    const selectedCard = document.getElementById('pay-method-' + method);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        const radio = selectedCard.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }
}

// Premium Toast Message Synthesizer
function showToast(message, isError = false) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${isError ? 'toast-error' : ''}`;
    
    const icon = isError 
        ? '<i class="fa-solid fa-circle-exclamation toast-error-icon"></i>' 
        : '<i class="fa-solid fa-circle-check toast-success-icon"></i>';

    toast.innerHTML = `
        ${icon}
        <span>${escapeHtml(message)}</span>
    `;

    container.appendChild(toast);

    // Auto remove after 3s
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Utility Helpers
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Auto-run on document load
document.addEventListener('DOMContentLoaded', () => {
    // Sync cart status if cart elements are present
    if (document.getElementById('cart-page-content') || document.getElementById('cart-drawer-body')) {
        fetchCart();
    }

    // Initialize row visibility checks
    updateRowVisibility();

    // Hook scroll listeners to sync arrow buttons for sliders
    document.querySelectorAll('.books-row').forEach(row => {
        row.addEventListener('scroll', () => {
            syncSliderArrows(row);
        });
        // Run initial check
        syncSliderArrows(row);
    });

    // Initialize hero slideshow if multiple slides exist (Carousel Slide Motion)
    const track = document.querySelector('.hero-slides-track');
    const slides = document.querySelectorAll('.hero-slides .hero-slide');
    if (track && slides.length > 1) {
        let currentSlideIdx = 0;
        setInterval(() => {
            currentSlideIdx = (currentSlideIdx + 1) % slides.length;
            track.style.transform = `translateX(-${currentSlideIdx * 100}%)`;
        }, 6000); // Rotate slide every 6 seconds
    }
});

// Sync slider arrows on resize
window.addEventListener('resize', () => {
    document.querySelectorAll('.books-row').forEach(row => {
        syncSliderArrows(row);
    });
});

// Checkout Submit Form Handler
function submitCheckout(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('btn-submit-order');
    if (!submitBtn) return;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing Order...';
    
    const formData = new FormData(document.getElementById('checkout-form'));
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/store/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(resData => {
        if (resData.success) {
            showToast('Order generated successfully!', false);
            setTimeout(() => {
                window.location.href = resData.redirect_url;
            }, 1000);
        } else {
            showToast(resData.message || 'Validation failed. Please verify form details.', true);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Place Secure Order';
        }
    })
    .catch(err => {
        console.error('Checkout submit failed', err);
        showToast('Failed to process order. Please try again.', true);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Place Secure Order';
    });
}

// ==========================================================================
// BOOK RATINGS AND REVIEWS SYSTEM (COMMENT STYLE)
// ==========================================================================
let currentDetailBookId = null;
let currentFormRating = 0;

function setFormRating(rating) {
    currentFormRating = rating;
    document.getElementById('form-rating-value').value = rating;
    
    const stars = document.querySelectorAll('.star-rating-interactive .star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('fa-regular');
            star.classList.add('fa-solid');
        } else {
            star.classList.remove('fa-solid');
            star.classList.add('fa-regular');
        }
    });
}

function resetReviewForm() {
    currentFormRating = 0;
    const ratingInput = document.getElementById('form-rating-value');
    if (ratingInput) ratingInput.value = 0;
    
    const commentText = document.getElementById('form-comment-text');
    if (commentText) commentText.value = "";
    
    const stars = document.querySelectorAll('.star-rating-interactive .star-btn');
    stars.forEach(star => {
        star.classList.remove('fa-solid');
        star.classList.add('fa-regular');
    });
}

function loadBookReviews(bookId) {
    currentDetailBookId = bookId;
    resetReviewForm();
    
    const listContainer = document.getElementById('modal-reviews-list');
    if (listContainer) {
        listContainer.innerHTML = '<div class="reviews-loading-placeholder"><i class="fa-solid fa-circle-notch fa-spin"></i> Loading comments and reviews...</div>';
    }

    fetch(`/store/books/${bookId}/reviews`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;
            
            // 1. Render Summary Card
            const avgRatingEl = document.getElementById('reviews-avg-rating');
            if (avgRatingEl) avgRatingEl.innerText = parseFloat(data.average_rating).toFixed(1);
            
            const avgStarsEl = document.getElementById('reviews-avg-stars');
            if (avgStarsEl) {
                let starsHtml = "";
                const fullStars = Math.floor(data.average_rating);
                const hasHalf = (data.average_rating - fullStars) >= 0.3;
                for (let i = 1; i <= 5; i++) {
                    if (i <= fullStars) {
                        starsHtml += '<i class="fa-solid fa-star"></i>';
                    } else if (i === fullStars + 1 && hasHalf) {
                        starsHtml += '<i class="fa-solid fa-star-half-stroke"></i>';
                    } else {
                        starsHtml += '<i class="fa-regular fa-star"></i>';
                    }
                }
                avgStarsEl.innerHTML = starsHtml;
            }
            
            const totalCountEl = document.getElementById('reviews-total-count');
            if (totalCountEl) {
                totalCountEl.innerText = `Based on ${data.total_reviews} ${data.total_reviews === 1 ? 'review' : 'reviews'}`;
            }
            
            // 2. Render Progress Bars Distribution
            const barsContainer = document.getElementById('reviews-bars');
            if (barsContainer) {
                let barsHtml = "";
                for (let rating = 5; rating >= 1; rating--) {
                    const count = data.distribution[rating] || 0;
                    const percent = data.total_reviews > 0 ? Math.round((count / data.total_reviews) * 100) : 0;
                    barsHtml += `
                        <div class="star-bar-row">
                            <span class="bar-star-label">${rating} <i class="fa-solid fa-star"></i></span>
                            <div class="star-progress-track">
                                <div class="star-progress-fill" style="width: ${percent}%;"></div>
                            </div>
                            <span class="bar-percent-label">${percent}%</span>
                        </div>
                    `;
                }
                barsContainer.innerHTML = barsHtml;
            }
            
            // 3. Populate existing user review if already submitted
            if (data.user_review) {
                setFormRating(data.user_review.rating);
                const commentText = document.getElementById('form-comment-text');
                if (commentText) commentText.value = data.user_review.comment || "";
                
                const submitBtn = document.querySelector('.btn-submit-comment');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Update Review <i class="fa-solid fa-rotate"></i>';
                }
            } else {
                const submitBtn = document.querySelector('.btn-submit-comment');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Post Review <i class="fa-solid fa-paper-plane"></i>';
                }
            }
            
            // 4. Render Reviews Comments List
            if (listContainer) {
                if (data.reviews.length === 0) {
                    listContainer.innerHTML = `
                        <div class="empty-reviews-state">
                            <i class="fa-regular fa-comment-dots"></i>
                            <p>No reviews yet. Be the first to leave a comment review!</p>
                        </div>
                    `;
                } else {
                    let reviewsHtml = "";
                    data.reviews.forEach(rev => {
                        let stars = "";
                        for (let i = 1; i <= 5; i++) {
                            stars += i <= rev.rating 
                                ? '<i class="fa-solid fa-star"></i>' 
                                : '<i class="fa-regular fa-star"></i>';
                        }
                        
                        reviewsHtml += `
                            <div class="review-comment-card">
                                <img src="${rev.customer_image}" alt="avatar" class="comment-card-avatar">
                                <div class="comment-card-body">
                                    <div class="comment-card-header">
                                        <span class="comment-author-name">${rev.customer_name}</span>
                                        <div class="comment-stars">${stars}</div>
                                        <span class="comment-time">${rev.created_at_formatted}</span>
                                    </div>
                                    <div class="comment-card-text">${escapeHtml(rev.comment || '')}</div>
                                </div>
                            </div>
                        `;
                    });
                    listContainer.innerHTML = reviewsHtml;
                }
            }
        })
        .catch(err => {
            console.error("Failed to load reviews", err);
            if (listContainer) {
                listContainer.innerHTML = '<div class="reviews-error-placeholder"><i class="fa-solid fa-triangle-exclamation"></i> Failed to load reviews.</div>';
            }
        });
}

function submitReview() {
    if (!currentDetailBookId) return;
    
    const ratingVal = parseInt(document.getElementById('form-rating-value').value);
    if (ratingVal < 1 || ratingVal > 5) {
        alert("Please select a star rating!");
        return;
    }
    
    const commentText = document.getElementById('form-comment-text').value.trim();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const submitBtn = document.querySelector('.btn-submit-comment');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Posting...';
    
    fetch(`/store/books/${currentDetailBookId}/reviews`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            rating: ratingVal,
            comment: commentText
        })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if (data.success) {
            loadBookReviews(currentDetailBookId);
        } else {
            alert(data.message || "Failed to submit review.");
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.error("Error submitting review", err);
        alert("An error occurred while submitting your review.");
    });
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function toggleWishlist(itemId, element) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/store/books/${itemId}/wishlist`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => {
        if (res.status === 401) {
            window.location.href = '/login';
            return;
        }
        return res.json();
    })
    .then(data => {
        if (!data) return;
        if (data.success) {
            const added = data.status === 'added';
            showToast(data.message, false);
            
            // Sync all heart icons of this book on the page
            const heartButtons = document.querySelectorAll(`.btn-card-wishlist[data-id="${itemId}"]`);
            heartButtons.forEach(btn => {
                btn.setAttribute('data-wishlisted', added ? 'true' : 'false');
                const card = btn.closest('.book-card-premium');
                if (card) {
                    card.setAttribute('data-wishlisted', added ? 'true' : 'false');
                }
                const icon = btn.querySelector('i');
                if (added) {
                    btn.classList.add('active');
                    if (icon) {
                        icon.className = 'fa-solid fa-heart';
                    }
                } else {
                    btn.classList.remove('active');
                    if (icon) {
                        icon.className = 'fa-regular fa-heart';
                    }
                }
            });
            
            // Update modal wishlist button if currently open
            const modalBtn = document.getElementById('btn-wishlist-modal');
            if (modalBtn && modalBtn.getAttribute('data-id') == itemId) {
                if (added) {
                    modalBtn.classList.add('active');
                    modalBtn.innerHTML = '<i class="fa-solid fa-heart"></i> Wishlisted';
                } else {
                    modalBtn.classList.remove('active');
                    modalBtn.innerHTML = '<i class="fa-regular fa-heart"></i> Wishlist';
                }
            }
        } else {
            showToast(data.message || 'Failed to toggle wishlist', true);
        }
    })
    .catch(err => {
        console.error('Wishlist toggle failed', err);
        showToast('Error connecting to the server', true);
    });
}

function toggleWishlistFromModal(button) {
    const itemId = button.getAttribute('data-id');
    if (itemId) {
        toggleWishlist(itemId, null);
    }
}

