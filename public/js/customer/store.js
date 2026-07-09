// Keep track of original parents of books to flatten them during category filtering
const originalBookParents = new Map();

// Initialize original parents immediately when the script executes
(function() {
    setTimeout(() => {
        document.querySelectorAll('.books-row .book-container-3d').forEach(book => {
            originalBookParents.set(book, book.parentElement);
        });
    }, 0);
})();

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
                                <div class="cart-item-cover ${item.image ? 'has-cover-image' : (item.cover_class || 'book-color-1')}" ${item.image ? `style="background-image: url('${item.image}'); background-size: cover; background-position: center;"` : ''}>
                                    ${item.image ? '' : `<span class="cart-item-cover-text">${escapeHtml(item.name)}</span>`}
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
                                <div class="cart-item-cover ${item.image ? 'has-cover-image' : (item.cover_class || 'book-color-1')}" ${item.image ? `style="background-image: url('${item.image}'); background-size: cover; background-position: center;"` : ''}>
                                    ${item.image ? '' : `<span class="cart-item-cover-text">${escapeHtml(item.name)}</span>`}
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
function openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, pdfFile, image, isWishlisted, categories) {
    // 1. Populate Desktop Elements
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-author').innerText = 'By ' + author;
    document.getElementById('modal-desc').innerText = desc || 'No description available for this book.';
    document.getElementById('modal-pages').innerText = totalPages;
    document.getElementById('modal-price').innerText = parseFloat(price).toLocaleString() + ' Ks';
    
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

    const pdfBtn = document.getElementById('btn-download-pdf-modal');
    if (pdfBtn) {
        if (pdfFile) {
            pdfBtn.href = '/store/books/' + id + '/download';
            pdfBtn.classList.remove('display-none');
        } else {
            pdfBtn.classList.add('display-none');
        }
    }

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
            coverFront.style.backgroundImage = "";
            if (titleBox) titleBox.style.display = 'flex';
            if (badge) badge.style.display = 'flex';
        }
    }
    
    const coverTitle = document.getElementById('modal-book-cover-title');
    if (coverTitle) coverTitle.innerText = title;
    
    const coverAuthor = document.getElementById('modal-book-cover-author');
    if (coverAuthor) coverAuthor.innerText = author;

    const cartBtn = document.getElementById('btn-add-to-cart-modal');
    if (cartBtn) {
        cartBtn.onclick = function() {
            closeBookDetail();
            addToCart(id, 1);
        };
    }

    // 2. Populate Mobile-Only Elements
    const mobileTitle = document.getElementById('modal-title-mobile') || document.getElementById('modal-mobile-title');
    if (mobileTitle) mobileTitle.innerText = title;
    
    const mobileAuthor = document.getElementById('modal-author-mobile') || document.getElementById('modal-mobile-author');
    if (mobileAuthor) mobileAuthor.innerText = 'By ' + author;

    const mobilePages = document.getElementById('modal-pages-mobile');
    if (mobilePages) mobilePages.innerText = totalPages;

    const mobilePrice = document.getElementById('modal-price-mobile');
    if (mobilePrice) mobilePrice.innerText = parseFloat(price).toLocaleString() + ' Ks';

    const mobileDesc = document.getElementById('modal-desc-mobile') || document.getElementById('modal-mobile-desc');
    if (mobileDesc) mobileDesc.innerText = desc || 'No description available for this book.';

    // Populate Mobile 3D Book Cover
    const mobileBook3D = document.getElementById('modal-book-3d-mobile');
    if (mobileBook3D) {
        mobileBook3D.className = 'book-3d ' + colorClass;
        const coverFront = mobileBook3D.querySelector('.book-cover-front');
        const titleBox = mobileBook3D.querySelector('.book-cover-title-box');
        
        if (image) {
            coverFront.style.backgroundImage = "url('" + image + "')";
            coverFront.style.backgroundSize = "cover";
            coverFront.style.backgroundPosition = "center";
            if (titleBox) titleBox.style.display = 'none';
        } else {
            coverFront.style.backgroundImage = "";
            if (titleBox) titleBox.style.display = 'flex';
        }
    }
    
    const mobileCoverTitle = document.getElementById('modal-book-cover-title-mobile');
    if (mobileCoverTitle) mobileCoverTitle.innerText = title;

    // Set mobile cover (legacy elements fallback)
    const mobileCover = document.getElementById('modal-mobile-cover');
    if (mobileCover) {
        if (image) {
            mobileCover.style.backgroundImage = "url('" + image + "')";
            mobileCover.style.backgroundSize = "cover";
            mobileCover.style.backgroundPosition = "center";
            mobileCover.innerHTML = "";
            mobileCover.className = 'mobile-detail-cover';
        } else {
            mobileCover.style.backgroundImage = "";
            mobileCover.className = 'mobile-detail-cover ' + colorClass;
            mobileCover.innerHTML = `<div class="fallback-cover-text">${escapeHtml(title)}</div>`;
        }
    }

    // Author avatar circular thumbnail (legacy fallback)
    const mobileAvatar = document.getElementById('modal-mobile-author-avatar');
    if (mobileAvatar) {
        const initials = author ? author.trim().charAt(0).toUpperCase() : 'A';
        mobileAvatar.innerText = initials;
        const hue = (author ? author.length * 35 : 120) % 360;
        mobileAvatar.style.backgroundColor = `hsl(${hue}, 40%, 35%)`;
    }

    // Copy tags dynamically from the list card to the mobile modal (legacy fallback)
    const mobileTags = document.getElementById('modal-mobile-tags');
    if (mobileTags) {
        mobileTags.innerHTML = "";
        const card = document.querySelector(`.book-card-premium[data-id="${id}"], .promo-book-ad-card[data-id="${id}"]`);
        if (card) {
            const tagsContainer = card.querySelector('.book-tags');
            if (tagsContainer) {
                mobileTags.innerHTML = tagsContainer.innerHTML;
            }
        }
        if (!mobileTags.innerHTML) {
            mobileTags.innerHTML = `<span class="book-tag">Best Seller</span>`;
        }
    }

    // Hook Wishlist button for mobile modal
    const mobileWishlistBtn = document.getElementById('btn-wishlist-mobile');
    if (mobileWishlistBtn) {
        mobileWishlistBtn.setAttribute('data-id', id);
        if (isWishlisted) {
            mobileWishlistBtn.classList.add('active');
            mobileWishlistBtn.innerHTML = '<i class="fa-solid fa-heart"></i>';
        } else {
            mobileWishlistBtn.classList.remove('active');
            mobileWishlistBtn.innerHTML = '<i class="fa-regular fa-heart"></i>';
        }
    }

    // Hook Buy button for mobile modal
    const mobileBuyBtn = document.getElementById('btn-buy-mobile');
    if (mobileBuyBtn) {
        if (parseInt(stock) > 0) {
            mobileBuyBtn.disabled = false;
            mobileBuyBtn.innerHTML = '<i class="fa-solid fa-cart-shopping"></i> Buy Book';
            mobileBuyBtn.onclick = function() {
                closeBookDetail();
                addToCart(id, 1);
            };
        } else {
            mobileBuyBtn.disabled = true;
            mobileBuyBtn.innerHTML = 'Out of Stock';
        }
    }

    // Hook PDF download button for mobile modal
    const mobilePdfBtn = document.getElementById('btn-download-mobile');
    if (mobilePdfBtn) {
        if (pdfFile) {
            mobilePdfBtn.href = '/store/books/' + id + '/download';
            mobilePdfBtn.classList.remove('display-none');
        } else {
            mobilePdfBtn.classList.add('display-none');
        }
    }

    const modal = document.getElementById('detail-modal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    loadBookRecommendations(id, categories || '');
}

function closeBookDetail() {
    const modal = document.getElementById('detail-modal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
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

function openReviewsModalFromDetails() {
    const wishlistBtn = document.getElementById('btn-wishlist-modal');
    if (!wishlistBtn) return;
    
    const id = wishlistBtn.getAttribute('data-id');
    const title = document.getElementById('modal-title').innerText;
    const author = document.getElementById('modal-author').innerText.replace(/^By\s+/i, '');
    
    openReviewsModal(id, title, author);
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
    
    // Get categories
    const container3d = el.closest('.book-container-3d');
    const categories = container3d ? container3d.getAttribute('data-categories') : '';
    
    openBookDetail(id, title, author, desc, price, stock, totalPages, colorClass, pdfFile, image, isWishlisted, categories);
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
        
        // Only hide individual slider containers, do not hide the main section container which houses filter tabs.
        
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

    const firstRow = document.querySelector('.books-rows-container .books-row');
    const books = document.querySelectorAll('.books-rows-container .book-container-3d');

    // 1. Move books to flatten or restore them based on the filter type
    if (catId === 'all') {
        // Restore each book to its original row parent
        originalBookParents.forEach((originalParent, book) => {
            if (originalParent && book.parentElement !== originalParent) {
                originalParent.appendChild(book);
            }
        });
    } else {
        // Flatten books: move all books matching the category to the first row
        if (firstRow) {
            books.forEach(book => {
                const bookCatsStr = book.getAttribute('data-categories') || '';
                const bookCats = bookCatsStr ? bookCatsStr.split(',') : [];
                if (bookCats.includes(catId.toString())) {
                    if (book.parentElement !== firstRow) {
                        firstRow.appendChild(book);
                    }
                }
            });
        }
    }

    // 2. Animate and update display of matching books
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
    const books = document.querySelectorAll('.books-rows-container .book-container-3d');
    
    // Reset active category tabs and restore books to original rows if user starts searching
    if (query !== '') {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        const allTab = document.querySelector('.filter-tab[onclick*="\'all\'"]');
        if (allTab) allTab.classList.add('active');
        
        originalBookParents.forEach((originalParent, book) => {
            if (originalParent && book.parentElement !== originalParent) {
                originalParent.appendChild(book);
            }
        });
    }

    books.forEach(book => {
        const title = book.getAttribute('data-title') || '';
        const author = book.getAttribute('data-author') || '';
        
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

    // Initialize hero slideshow if multiple slides exist
    const slides = document.querySelectorAll('.hero-slides .hero-slide');
    let currentHeroSlideIdx = 0;
    let heroSlideInterval = null;

    function showHeroSlide(index) {
        const slides = document.querySelectorAll('.hero-slides .hero-slide');
        const dots = document.querySelectorAll('.hero-slider-dots .hero-dot');
        if (slides.length === 0) return;
        
        if (index >= slides.length) {
            currentHeroSlideIdx = 0;
        } else if (index < 0) {
            currentHeroSlideIdx = slides.length - 1;
        } else {
            currentHeroSlideIdx = index;
        }
        
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        if (slides[currentHeroSlideIdx]) {
            slides[currentHeroSlideIdx].classList.add('active');
        }
        if (dots[currentHeroSlideIdx]) {
            dots[currentHeroSlideIdx].classList.add('active');
        }
    }

    window.changeHeroSlide = function(direction) {
        showHeroSlide(currentHeroSlideIdx + direction);
        resetHeroTimer();
    };

    window.setHeroSlide = function(index) {
        showHeroSlide(index);
        resetHeroTimer();
    };

    function resetHeroTimer() {
        if (heroSlideInterval) {
            clearInterval(heroSlideInterval);
        }
        const slides = document.querySelectorAll('.hero-slides .hero-slide');
        if (slides.length > 1) {
            heroSlideInterval = setInterval(() => {
                showHeroSlide(currentHeroSlideIdx + 1);
            }, 6000);
        }
    }

    if (slides.length > 1) {
        resetHeroTimer();
    }
});

// Sync slider arrows on resize
window.addEventListener('resize', () => {
    document.querySelectorAll('.books-row').forEach(row => {
        syncSliderArrows(row);
    });
});

// Full-Screen Payment Overlay Loader Helpers
function showFullScreenLoader(titleText = 'Securely Connecting to Stripe', descText = 'Please do not close this window or refresh the page.') {
    let overlay = document.getElementById('full-screen-loader-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'full-screen-loader-overlay';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100vw';
        overlay.style.height = '100vh';
        overlay.style.backgroundColor = 'rgba(11, 20, 18, 0.9)'; // Deep forest-black overlay
        overlay.style.zIndex = '99999';
        overlay.style.display = 'flex';
        overlay.style.flexDirection = 'column';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.color = '#ffffff';
        overlay.style.fontFamily = "'Outfit', 'Inter', sans-serif";
        overlay.style.transition = 'opacity 0.3s ease';
        
        overlay.innerHTML = `
            <div style="text-align: center; padding: 2rem; background: rgba(18, 37, 33, 0.95); border: 2px solid #cca353; border-radius: 16px; box-shadow: 0 12px 36px rgba(0,0,0,0.5); max-width: 450px; width: 90%;">
                <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 3.5rem; color: #cca353; margin-bottom: 1.5rem; display: inline-block;"></i>
                <h2 style="font-size: 1.6rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #ffffff;" id="loader-title">${titleText}</h2>
                <p style="font-size: 0.95rem; opacity: 0.85; margin: 0; line-height: 1.5;" id="loader-desc">${descText}</p>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loader-title').innerText = titleText;
        document.getElementById('loader-desc').innerText = descText;
        overlay.style.display = 'flex';
    }
}

function hideFullScreenLoader() {
    const overlay = document.getElementById('full-screen-loader-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Checkout Submit Form Handler
function submitCheckout(e) {
    const form = document.getElementById('checkout-form');
    if (form && window.validateForm && !window.validateForm(form)) {
        e.preventDefault();
        return;
    }
    
    e.preventDefault();
    
    const submitBtn = document.getElementById('btn-submit-order');
    if (!submitBtn) return;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing Order...';
    
    const formData = new FormData(document.getElementById('checkout-form'));
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    const paymentMethod = data['payment_method'] || 'cod';
    if (paymentMethod === 'kpay' || paymentMethod === 'wave') {
        showFullScreenLoader('Connecting to Payment Gateway', `Initializing secure transfer link to ${paymentMethod === 'kpay' ? 'KBZPay' : 'Wave Pay'}. Please wait...`);
    } else if (paymentMethod === 'stripe') {
        showFullScreenLoader('Connecting to Stripe', 'Preparing secure checkout window. Please wait...');
    }

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
            if (paymentMethod === 'kpay' || paymentMethod === 'wave') {
                document.getElementById('loader-title').innerText = 'Payment Request Approved';
                document.getElementById('loader-desc').innerText = 'Redirecting to your order confirmation and invoice receipt...';
            } else if (paymentMethod === 'stripe') {
                document.getElementById('loader-title').innerText = 'Redirecting to Stripe';
                document.getElementById('loader-desc').innerText = 'Please complete your payment on the secure Stripe page...';
            }
            showToast(resData.message || 'Order placed successfully!', false);
            setTimeout(() => {
                window.location.href = resData.redirect_url;
            }, 1500);
        } else {
            hideFullScreenLoader();
            showToast(resData.message || 'Validation failed. Please verify form details.', true);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Place Secure Order';
        }
    })
    .catch(err => {
        hideFullScreenLoader();
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

            const mobileModalBtn = document.getElementById('btn-wishlist-mobile');
            if (mobileModalBtn && mobileModalBtn.getAttribute('data-id') == itemId) {
                if (added) {
                    mobileModalBtn.classList.add('active');
                    mobileModalBtn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                } else {
                    mobileModalBtn.classList.remove('active');
                    mobileModalBtn.innerHTML = '<i class="fa-regular fa-heart"></i>';
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

// Dynamically load book recommendations ("You may also like") from page data
function loadBookRecommendations(currentBookId, categoriesStr) {
    const recContainer = document.getElementById('modal-recommendations-list');
    if (!recContainer) return;
    
    const currentCats = categoriesStr ? categoriesStr.split(',').map(c => c.trim()).filter(Boolean) : [];
    const allBookEls = document.querySelectorAll('.book-card-premium');
    const candidates = [];
    const seenIds = new Set();
    
    allBookEls.forEach(el => {
        const id = el.getAttribute('data-id');
        if (id === currentBookId || seenIds.has(id)) return;
        
        const container3d = el.closest('.book-container-3d');
        if (!container3d) return;
        
        const categories = container3d.getAttribute('data-categories') || '';
        const bookCats = categories.split(',').map(c => c.trim()).filter(Boolean);
        
        let score = 0;
        bookCats.forEach(c => {
            if (currentCats.includes(c)) {
                score++;
            }
        });
        
        candidates.push({
            el: el,
            id: id,
            score: score,
            title: el.getAttribute('data-title-raw'),
            author: el.getAttribute('data-author-raw'),
            desc: el.getAttribute('data-desc'),
            price: el.getAttribute('data-price'),
            stock: el.getAttribute('data-stock'),
            pages: el.getAttribute('data-pages'),
            colorClass: el.getAttribute('data-color-class'),
            pdfFile: el.getAttribute('data-pdf-file'),
            image: el.getAttribute('data-image'),
            isWishlisted: el.getAttribute('data-wishlisted') === 'true',
            categories: categories
        });
        seenIds.add(id);
    });
    
    // Sort by score descending (most categories matching), then randomize
    candidates.sort((a, b) => b.score - a.score || Math.random() - 0.5);
    const recs = candidates.slice(0, 6);
    
    const recSection = document.getElementById('detail-recommendations-section');
    if (recs.length === 0) {
        if (recSection) recSection.style.display = 'none';
        return;
    } else {
        if (recSection) recSection.style.display = 'block';
    }
    
    let html = '';
    recs.forEach(book => {
        const defaultCover = `<div class="rec-cover-wrapper ${book.colorClass}" style="display:flex; align-items:center; justify-content:center; color:#fff; font-size:0.5rem; font-weight:bold; padding:4px; text-align:center; box-sizing:border-box;">${escapeHtml(book.title)}</div>`;
        const imageCover = book.image ? `<div class="rec-cover-wrapper" style="background-image: url('${book.image}')"></div>` : defaultCover;
        const formattedPrice = parseFloat(book.price || 0).toLocaleString() + ' Ks';
        
        const safeTitle = escapeJsString(book.title);
        const safeAuthor = escapeJsString(book.author);
        const safeDesc = escapeJsString(book.desc || '');
        const safeColor = escapeJsString(book.colorClass);
        const safePdf = escapeJsString(book.pdfFile || '');
        const safeImg = escapeJsString(book.image || '');
        const safeCats = escapeJsString(book.categories || '');
        
        html += `
            <div class="recommendation-card" onclick="openBookDetail('${book.id}', '${safeTitle}', '${safeAuthor}', '${safeDesc}', '${book.price}', '${book.stock}', '${book.pages}', '${safeColor}', '${safePdf}', '${safeImg}', ${book.isWishlisted}, '${safeCats}')">
                ${imageCover}
                <div class="rec-card-info">
                    <span class="rec-card-title" title="${escapeHtml(book.title)}">${escapeHtml(book.title)}</span>
                    <span class="rec-card-author">By ${escapeHtml(book.author)}</span>
                    <span class="rec-card-price">${formattedPrice}</span>
                </div>
            </div>
        `;
    });
    
    recContainer.innerHTML = html;
}

function escapeJsString(str) {
    if (!str) return '';
    return str
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '\\"')
        .replace(/\n/g, '\\n')
        .replace(/\r/g, '\\r');
}

function toggleDeliveryInfo(btn, orderId) {
    const shippingCol = document.getElementById(`shipping-col-${orderId}`);
    if (shippingCol) {
        const isActive = shippingCol.classList.toggle('active');
        const textSpan = btn.querySelector('span');
        const icon = btn.querySelector('i');
        
        if (isActive) {
            if (textSpan) textSpan.innerText = 'Hide Delivery Information';
            if (icon) {
                icon.className = 'fa-solid fa-chevron-up';
            }
        } else {
            if (textSpan) textSpan.innerText = 'View Delivery Information';
            if (icon) {
                icon.className = 'fa-solid fa-chevron-down';
            }
        }
    }
}

// Global click event listener to show loading spinner for all "Add to Cart" actions
document.addEventListener('click', function(e) {
    const btn = e.target.closest('button, a');
    if (!btn) return;
    
    const onclickAttr = btn.getAttribute('onclick') || '';
    if (onclickAttr.includes('addToCart') || btn.classList.contains('btn-add-to-cart') || btn.classList.contains('btn-buy-book')) {
        if (btn.classList.contains('btn-loading') || btn.disabled) return;
        
        btn.classList.add('btn-loading');
        const originalHtml = btn.innerHTML;
        btn.style.pointerEvents = 'none';
        
        // Check if button is an icon-only card button or text button
        if (btn.classList.contains('btn-card-cart') || btn.classList.contains('btn-wishlist-buy') || btn.tagName === 'A') {
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
        } else {
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Adding...';
        }
        
        // Restore button state after a safe timeout (1.2s) when server response has returned
        setTimeout(() => {
            btn.classList.remove('btn-loading');
            btn.style.pointerEvents = '';
            btn.innerHTML = originalHtml;
        }, 1200);
    }
});

// Client-side Sort Dropdown Controls
function toggleSortDropdown(e) {
    if (e) e.stopPropagation();
    const selectBox = document.getElementById('custom-sort-select');
    const optionsList = document.getElementById('sort-options-list');
    if (selectBox && optionsList) {
        selectBox.classList.toggle('active');
        optionsList.classList.toggle('display-none');
    }
}

function selectSortOption(value, label, e) {
    if (e) e.stopPropagation();
    
    // Update active class in options list
    document.querySelectorAll('.custom-sort-dropdown .sort-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.getAttribute('data-value') === value) {
            opt.classList.add('active');
        }
    });

    // Update label text
    const currentLabel = document.getElementById('current-sort-label');
    if (currentLabel) currentLabel.innerText = label;

    // Hide dropdown
    const selectBox = document.getElementById('custom-sort-select');
    const optionsList = document.getElementById('sort-options-list');
    if (selectBox && optionsList) {
        selectBox.classList.remove('active');
        optionsList.classList.add('display-none');
    }

    // Trigger client-side sort
    sortBooks(value);
}

function sortBooks(criteria) {
    const books = Array.from(document.querySelectorAll('.books-rows-container .book-container-3d'));
    if (books.length === 0) return;

    books.sort((a, b) => {
        const cardA = a.querySelector('.book-card-premium, .promo-book-ad-card');
        const cardB = b.querySelector('.book-card-premium, .promo-book-ad-card');
        if (!cardA || !cardB) return 0;

        const priceA = parseFloat(cardA.getAttribute('data-price')) || 0;
        const priceB = parseFloat(cardB.getAttribute('data-price')) || 0;

        const titleA = (a.getAttribute('data-title') || '').toLowerCase();
        const titleB = (b.getAttribute('data-title') || '').toLowerCase();

        const indexA = parseInt(a.getAttribute('data-index')) || 0;
        const indexB = parseInt(b.getAttribute('data-index')) || 0;

        if (criteria === 'price-low-high') {
            return priceA - priceB;
        } else if (criteria === 'price-high-low') {
            return priceB - priceA;
        } else if (criteria === 'name-a-z') {
            return titleA.localeCompare(titleB);
        } else if (criteria === 'name-z-a') {
            return titleB.localeCompare(titleA);
        } else if (criteria === 'default') {
            return indexA - indexB;
        }
        return 0;
    });

    // Re-distribute sorted books back to the visible rows
    const firstRow = document.querySelector('.books-rows-container .books-row');
    const rows = Array.from(document.querySelectorAll('.books-rows-container .books-row'));
    
    // Check if category filter is active (flattened state)
    const activeTab = document.querySelector('.filter-tab.active');
    const isFiltered = activeTab && activeTab.getAttribute('onclick') && !activeTab.getAttribute('onclick').includes('all');

    if (isFiltered && firstRow) {
        // If filtered, just append all sorted books to the first row (flat structure)
        books.forEach(book => {
            firstRow.appendChild(book);
        });
    } else {
        // Otherwise, re-chunk and distribute them across original rows (up to 12 per row)
        let bookIndex = 0;
        rows.forEach(row => {
            row.innerHTML = '';
            for (let i = 0; i < 12 && bookIndex < books.length; i++) {
                const book = books[bookIndex];
                row.appendChild(book);
                originalBookParents.set(book, row); // Update original parent mapping for filters
                bookIndex++;
            }
        });
    }

    // Update visibility and slide arrow states
    updateRowVisibility();
}

// Close sort dropdown when clicking outside
document.addEventListener('click', function(e) {
    const selectBox = document.getElementById('custom-sort-select');
    const optionsList = document.getElementById('sort-options-list');
    if (selectBox && optionsList && !selectBox.contains(e.target)) {
        selectBox.classList.remove('active');
        optionsList.classList.add('display-none');
    }
});

// Initialize book indices for sorting preservation
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.books-rows-container .book-container-3d').forEach((book, idx) => {
        book.setAttribute('data-index', idx);
    });
});

