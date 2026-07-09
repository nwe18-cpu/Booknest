<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Booknest Admin')</title>

    <!-- Google Fonts (Inter & Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons (Only library allowed for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Admin Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}?v=1.2.0">

    @yield('styles')
</head>
<body>
    <!-- Custom Center Alert Dialog -->
    <div class="custom-alert-overlay display-none" id="custom-alert-modal">
        <div class="custom-alert-card">
            <div class="custom-alert-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="custom-alert-title">Access Denied</h4>
            <p class="custom-alert-message" id="custom-alert-message">You don't have permission to access this section.</p>
            <button class="btn-custom-alert-ok" id="custom-alert-ok-btn">OK</button>
        </div>
    </div>

    <!-- Reusable Custom Confirmation Modal -->
    <div class="custom-confirm-overlay" id="custom-confirm-modal">
        <div class="custom-confirm-card">
            <div class="custom-confirm-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h4 class="custom-confirm-title" id="custom-confirm-title">Confirm Action</h4>
            <p class="custom-confirm-message" id="custom-confirm-message">Are you sure you want to proceed?</p>
            <div class="custom-confirm-actions">
                <button class="custom-confirm-btn btn-cancel" id="custom-confirm-cancel-btn">Cancel</button>
                <button class="custom-confirm-btn btn-confirm" id="custom-confirm-ok-btn">Confirm</button>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <button class="sidebar-close-btn" aria-label="Close sidebar">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="sidebar-brand">
                <i class="fa-solid fa-book-open"></i>
                <span>Booknest Admin</span>
            </div>
            <ul class="sidebar-menu">
                @php
                    $isAdmin = auth()->guard('staff')->check() && auth()->guard('staff')->user()->role?->name === 'admin';
                @endphp
                
                <!-- Core Dashboard Section -->
                <li class="sidebar-section-heading">Core</li>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Content Management Section -->
                <li class="sidebar-section-heading">Content Manager</li>
                <li class="{{ request()->routeIs('admin.authors.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.authors.index') }}">
                        <i class="fa-solid fa-user-pen"></i>
                        <span>Authors</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.catalog.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.catalog.index') }}">
                        <i class="fa-solid fa-book"></i>
                        <span>Catalog</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.classifications.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.classifications.index') }}">
                        <i class="fa-solid fa-tags"></i>
                        <span>Classifications</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.index') }}">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span>Reviews</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}">
                        <i class="fa-solid fa-image"></i>
                        <span>Banners</span>
                    </a>
                </li>

                <!-- Business Relations Section -->
                <li class="sidebar-section-heading">Business</li>
                <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    @if($isAdmin)
                        <a href="{{ route('admin.orders.index') }}">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span>Orders</span>
                        </a>
                    @else
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); showAlert('You don\'t have permission to access this section.');">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span>Orders</span>
                            <i class="fa-solid fa-lock admin-only-lock" title="Admin Only"></i>
                        </a>
                    @endif
                </li>
                <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    @if($isAdmin)
                        <a href="{{ route('admin.customers.index') }}">
                            <i class="fa-solid fa-users"></i>
                            <span>Customers</span>
                        </a>
                    @else
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); showAlert('You don\'t have permission to access this section.');">
                            <i class="fa-solid fa-user s"></i>
                            <span>Customers</span>
                            <i class="fa-solid fa-lock admin-only-lock" title="Admin Only"></i>
                        </a>
                    @endif
                </li>
                <li class="{{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                    @if($isAdmin)
                        <a href="{{ route('admin.subscriptions.index') }}">
                            <i class="fa-solid fa-crown"></i>
                            <span>Subscriptions</span>
                        </a>
                    @else
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); showAlert('You don\'t have permission to access this section.');">
                            <i class="fa-solid fa-crown"></i>
                            <span>Subscriptions</span>
                            <i class="fa-solid fa-lock admin-only-lock" title="Admin Only"></i>
                        </a>
                    @endif
                </li>

                <!-- System & Account Section -->
                <li class="sidebar-section-heading">Account</li>
                @if($isAdmin)
                    <li class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.staff.index') }}">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Staff Management</span>
                        </a>
                    </li>
                @else
                    <li class="disabled-link">
                        <a href="#" onclick="event.preventDefault(); showAlert('You don\'t have permission to access this section.');">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>Staff Management</span>
                            <i class="fa-solid fa-lock admin-only-lock" title="Admin Only"></i>
                        </a>
                    </li>
                @endif
                <li class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.activity-logs.index') }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Activity History</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile') }}">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Profile Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        <div class="sidebar-overlay"></div>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Navbar -->
            <header class="navbar">
                <div class="nav-left-group">
                    <div class="nav-toggle-btn">
                        <i class="fa-solid fa-bars"></i>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="nav-brand-logo">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Booknest</span>
                    </a>
                </div>
                <div class="nav-right-side">
                    <a href="{{ url('/') }}" class="nav-view-store-btn" target="_blank">
                        <i class="fa-solid fa-store"></i>
                        <span>View Store</span>
                    </a>
                    <div class="user-profile">
                        <div class="user-toggle static-profile-toggle">
                            <img class="user-avatar" src="{{ auth()->guard('staff')->check() && auth()->guard('staff')->user()->image ? asset('storage/' . auth()->guard('staff')->user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->guard('staff')->check() ? auth()->guard('staff')->user()->name : 'Admin') . '&background=f1e4d8&color=5c3a21&bold=true' }}" alt="Avatar">
                            <span>{{ auth()->guard('staff')->check() ? auth()->guard('staff')->user()->name : 'Admin' }}</span>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST" id="admin-logout-form" class="display-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')
    
    <!-- AJAX Filter and Pagination script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. AJAX Filter & Pagination Logic
            const filterForm = document.querySelector('.filters-row-card, .filters-row-card-inline');
            if (filterForm) {
                const tableResponsive = document.querySelector('.table-responsive');
                const paginationWrapper = document.querySelector('.pagination-wrapper');

                function updateFilters(url) {
                    if (tableResponsive) {
                        tableResponsive.style.opacity = '0.5';
                        tableResponsive.style.transition = 'opacity 0.15s ease';
                    }
                    if (paginationWrapper) {
                        paginationWrapper.style.opacity = '0.5';
                        paginationWrapper.style.transition = 'opacity 0.15s ease';
                    }

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Extract and replace table
                        const newTable = doc.querySelector('.table-responsive');
                        if (newTable && tableResponsive) {
                            tableResponsive.innerHTML = newTable.innerHTML;
                        }

                        // Extract and replace pagination
                        const newPagination = doc.querySelector('.pagination-wrapper');
                        if (paginationWrapper) {
                            if (newPagination) {
                                paginationWrapper.innerHTML = newPagination.innerHTML;
                                paginationWrapper.style.display = '';
                            } else {
                                paginationWrapper.innerHTML = '';
                                paginationWrapper.style.display = 'none';
                            }
                        }

                        // Update browser URL
                        history.pushState(null, '', url);

                        // Sync clear buttons
                        initSearchClearButtons();
                    })
                    .catch(err => {
                        console.error('AJAX filtering error:', err);
                        // Fallback to normal page load in case of errors
                        window.location.href = url;
                    })
                    .finally(() => {
                        if (tableResponsive) tableResponsive.style.opacity = '1';
                        if (paginationWrapper) paginationWrapper.style.opacity = '1';
                    });
                }

                // Intercept form submissions (e.g. search Box Enter or requestSubmit)
                filterForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData);
                    const action = filterForm.getAttribute('action') || window.location.pathname;
                    const url = `${action}?${params.toString()}`;
                    updateFilters(url);
                });

                // Intercept pagination clicks
                document.addEventListener('click', function (e) {
                    const link = e.target.closest('.pagination-wrapper a');
                    if (link) {
                        e.preventDefault();
                        const url = link.getAttribute('href');
                        if (url) {
                            updateFilters(url);
                        }
                    }
                });

                // Handle browser back/forward buttons
                window.addEventListener('popstate', function () {
                    updateFilters(window.location.href);
                });
            }

            // 2. Search Input Clear Button Logic
            function initSearchClearButtons() {
                const searchInputs = document.querySelectorAll('input[name="search"], input#author-search, input#search-input');
                searchInputs.forEach(input => {
                    if (input.dataset.clearInit) {
                        // Just update visibility in case value changed programatically
                        const btn = input.parentElement.querySelector('.search-clear-btn');
                        if (btn) btn.style.display = input.value ? 'block' : 'none';
                        return;
                    }
                    input.dataset.clearInit = "true";

                    // Wrap input in a relative container to ensure absolute positioning is relative to the input's width
                    let wrapper = input.parentElement;
                    if (!wrapper.classList.contains('search-input-wrapper-ajax')) {
                        wrapper = document.createElement('div');
                        wrapper.className = 'search-input-wrapper-ajax';
                        wrapper.style.position = 'relative';
                        wrapper.style.width = '100%';
                        wrapper.style.display = 'inline-block';
                        input.parentNode.insertBefore(wrapper, input);
                        wrapper.appendChild(input);
                    }

                    // Create clear icon button
                    const clearBtn = document.createElement('i');
                    clearBtn.className = 'fa-solid fa-circle-xmark search-clear-btn';
                    clearBtn.style.position = 'absolute';
                    clearBtn.style.setProperty('left', 'auto', 'important');
                    clearBtn.style.setProperty('right', '14px', 'important');
                    clearBtn.style.top = '50%';
                    clearBtn.style.transform = 'translateY(-50%)';
                    clearBtn.style.cursor = 'pointer';
                    clearBtn.style.color = '#724E32'; // var(--text-muted)
                    clearBtn.style.opacity = '0.6';
                    clearBtn.style.transition = 'all 0.2s ease';
                    clearBtn.style.display = input.value ? 'block' : 'none';
                    clearBtn.style.fontSize = '0.95rem';
                    clearBtn.style.zIndex = '5';
                    clearBtn.title = 'Clear search';

                    clearBtn.addEventListener('mouseenter', () => {
                        clearBtn.style.opacity = '1';
                        clearBtn.style.color = '#C84B31'; // var(--accent-red)
                    });
                    clearBtn.addEventListener('mouseleave', () => {
                        clearBtn.style.opacity = '0.6';
                        clearBtn.style.color = '#724E32';
                    });

                    wrapper.appendChild(clearBtn);

                    // Add padding to prevent text overlap
                    input.style.paddingRight = '36px';

                    input.addEventListener('input', () => {
                        clearBtn.style.display = input.value ? 'block' : 'none';
                    });

                    clearBtn.addEventListener('click', () => {
                        input.value = '';
                        clearBtn.style.display = 'none';
                        input.focus();

                        // Dispatch input and keyup events for client-side list filtering
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.dispatchEvent(new Event('keyup', { bubbles: true }));

                        // Submit form for AJAX filters
                        if (input.form) {
                            input.form.requestSubmit();
                        }
                    });
                });
            }

            initSearchClearButtons();

            // Sidebar Toggle Logic for Mobile
            const navToggleBtn = document.querySelector('.nav-toggle-btn');
            const sidebar = document.querySelector('.sidebar');
            const sidebarOverlay = document.querySelector('.sidebar-overlay');
            if (navToggleBtn && sidebar && sidebarOverlay) {
                navToggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });

                sidebarOverlay.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });

                const sidebarCloseBtn = sidebar.querySelector('.sidebar-close-btn');
                if (sidebarCloseBtn) {
                    sidebarCloseBtn.addEventListener('click', function () {
                        sidebar.classList.remove('active');
                        sidebarOverlay.classList.remove('active');
                    });
                }

                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        sidebar.classList.remove('active');
                        sidebarOverlay.classList.remove('active');
                    });
                });
            }

            // Global Custom Center Alert Dialog Helper
            window.showAlert = function(message) {
                const modal = document.getElementById('custom-alert-modal');
                const msgEl = document.getElementById('custom-alert-message');
                const okBtn = document.getElementById('custom-alert-ok-btn');
                if (!modal || !msgEl || !okBtn) return;

                msgEl.textContent = message;
                
                // Show modal
                modal.classList.remove('display-none');
                document.body.style.overflow = 'hidden'; // Lock background scroll
                
                // Active transition
                setTimeout(() => {
                    modal.classList.add('active');
                }, 50);

                // Dismiss handler
                const dismiss = () => {
                    modal.classList.remove('active');
                    setTimeout(() => {
                        modal.classList.add('display-none');
                        document.body.style.overflow = '';
                    }, 250);
                    okBtn.removeEventListener('click', dismiss);
                };

                okBtn.addEventListener('click', dismiss);
            };

            // Override native window.alert globally for admin panel
            window.alert = function(message) {
                window.showAlert(message);
            };

            // Global Custom Confirm Modal Dialog Helper
            let activeConfirmCallback = null;

            window.showCustomConfirm = function(message, onConfirm) {
                const modal = document.getElementById('custom-confirm-modal');
                const messageEl = document.getElementById('custom-confirm-message');
                if (!modal || !messageEl) return;

                messageEl.textContent = message;
                modal.classList.add('show');
                activeConfirmCallback = onConfirm;
            };

            const confirmModal = document.getElementById('custom-confirm-modal');
            const cancelBtn = document.getElementById('custom-confirm-cancel-btn');
            const confirmBtn = document.getElementById('custom-confirm-ok-btn');

            if (confirmModal && cancelBtn && confirmBtn) {
                const closeConfirmModal = function() {
                    confirmModal.classList.remove('show');
                    activeConfirmCallback = null;
                };

                cancelBtn.addEventListener('click', closeConfirmModal);
                confirmModal.addEventListener('click', function(e) {
                    if (e.target === confirmModal) closeConfirmModal();
                });

                confirmBtn.addEventListener('click', function() {
                    if (activeConfirmCallback) {
                        activeConfirmCallback();
                    }
                    closeConfirmModal();
                });
            }

            // Disable default browser validation bubbles globally for admin
            document.addEventListener('invalid', function(e) {
                e.preventDefault();
            }, true);

            // Global client-side validation check on form submission for Admin forms
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // If form has novalidate, let it submit or handle manually
                if (form.noValidate) {
                    return;
                }

                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.classList.add('validation-shake');
                        setTimeout(() => {
                            firstInvalid.classList.remove('validation-shake');
                        }, 500);

                        let fieldName = firstInvalid.getAttribute('placeholder') || firstInvalid.getAttribute('name') || 'field';
                        if (firstInvalid.previousElementSibling && firstInvalid.previousElementSibling.tagName === 'LABEL') {
                            fieldName = firstInvalid.previousElementSibling.textContent.replace('*', '').replace(':', '').trim();
                        }
                        
                        let errorMsg = `Please fill out the "${fieldName}" field correctly.`;
                        if (firstInvalid.validity.valueMissing) {
                            errorMsg = `"${fieldName}" is required.`;
                        } else if (firstInvalid.validity.patternMismatch) {
                            errorMsg = firstInvalid.getAttribute('title') || `Please enter a valid format for "${fieldName}".`;
                        } else if (firstInvalid.validity.tooShort) {
                            errorMsg = `"${fieldName}" must be at least ${firstInvalid.minLength} characters.`;
                        } else if (firstInvalid.validity.tooLong) {
                            errorMsg = `"${fieldName}" cannot exceed ${firstInvalid.maxLength} characters.`;
                        } else if (firstInvalid.type === 'email') {
                            errorMsg = `Please enter a valid email address.`;
                        }
                        
                        if (window.showAlert) {
                            window.showAlert(errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                    return;
                }

                // Global interception of native confirm on form submission for Admin forms
                if (form.dataset.confirmVerified === "true") {
                    return; // Allow form submission
                }

                const onsubmitAttr = form.getAttribute('onsubmit');
                if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Extract message from confirm('...')
                    let msg = "Are you sure you want to proceed?";
                    const match = onsubmitAttr.match(/confirm\(['"](.*)['"]\)/);
                    if (match && match[1]) {
                        msg = match[1];
                    }

                    window.showCustomConfirm(msg, function() {
                        form.dataset.confirmVerified = "true";
                        form.submit();
                    });
                }
            }, true);
        });
    </script>
</body>
</html>