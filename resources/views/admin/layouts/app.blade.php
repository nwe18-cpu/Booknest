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
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">

    @yield('styles')
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <i class="fa-solid fa-book-open"></i>
                <span>Booknest Admin</span>
            </div>
            <ul class="sidebar-menu">
                @php
                    $isAdmin = auth()->guard('staff')->check() && auth()->guard('staff')->user()->role?->name === 'admin';
                @endphp
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
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
                <li class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    @if($isAdmin)
                        <a href="{{ route('admin.orders.index') }}">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span>Orders</span>
                        </a>
                    @else
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); alert('Access Denied: Admin privileges required.');">
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
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); alert('Access Denied: Admin privileges required.');">
                            <i class="fa-solid fa-users"></i>
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
                        <a href="#" class="disabled-link" onclick="event.preventDefault(); alert('Access Denied: Admin privileges required.');">
                            <i class="fa-solid fa-crown"></i>
                            <span>Subscriptions</span>
                            <i class="fa-solid fa-lock admin-only-lock" title="Admin Only"></i>
                        </a>
                    @endif
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
                <li>
                    <a href="{{ url('/') }}">
                        <i class="fa-solid fa-store"></i>
                        <span>View Store</span>
                    </a>
                </li>
                <li>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="display-none">
                        @csrf
                    </form>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Top Navbar -->
            <header class="navbar">
                <div class="nav-toggle-btn">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="user-profile">
                    <a href="{{ route('admin.profile') }}" style="color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>{{ auth()->guard('staff')->check() ? auth()->guard('staff')->user()->name : 'Admin' }}</span>
                    </a>
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
            const filterForm = document.querySelector('.filters-row-card');
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
        });
    </script>
</body>
</html>