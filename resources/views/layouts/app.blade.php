<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Booknest')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Buy the best books conveniently and quickly from Booknest online bookstore.')">
    <meta name="keywords" content="bookstore, online book store, online shopping myanmar, booknest, books">

    <!-- Google Fonts (Inter & Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons (Only library allowed for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Theme Initializer (Must run before styling loads to prevent FOUC) -->
    <script src="{{ asset('js/theme_init.js') }}"></script>

    <!-- CSS Variables and Base Layout -->
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=1.0.1">

    <!-- Page Specific Styles (if any) -->
    @yield('styles')
</head>
<body>
    <div class="app-container">
        <!-- 1. Header & Navigation Bar -->
        <header class="main-header">
            <div class="container">
                <nav class="navbar">
                    <!-- Mobile Menu Toggle Button -->
                    <button class="menu-toggle" aria-label="Toggle Navigation Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- Brand Logo -->
                    <a href="{{ url('/') }}" class="nav-brand">
                        <i class="fa-solid fa-book-open"></i>
                        <span>Booknest</span>
                    </a>

                    <!-- Navigation Links & Menu -->
                    <ul class="nav-menu">
                        <li><a href="{{ route('customer.store.home') }}" class="nav-link {{ request()->routeIs('customer.store.home') ? 'active' : '' }}">Home</a></li>
                        <li><a href="#main-footer" class="nav-link">Contact</a></li>
                    </ul>

                    <!-- Cart and Account Actions -->
                    <div class="nav-actions">
                        <!-- Theme Toggle Button (Warm Mode) -->
                        <button class="theme-toggle-btn" id="theme-toggle-btn" aria-label="Toggle Warm Sepia Mode" title="Toggle Warm Mode">
                            <i class="fa-solid fa-leaf"></i>
                        </button>

                        <!-- Notification Button & Dropdown -->
                        <div class="notification-container">
                            <button class="notification-toggle" aria-label="Open Notifications">
                                <i class="fa-solid fa-bell"></i>
                                @if(auth()->guard('customer')->check())
                                    <span class="notification-badge" id="notification-badge">{{ auth()->guard('customer')->user()->notifications()->where('is_read', false)->count() }}</span>
                                @else
                                    <span class="notification-badge" id="notification-badge">0</span>
                                @endif
                            </button>
                            <div class="notification-dropdown" id="notification-dropdown">
                                <div class="notification-dropdown-header">
                                    <h4>Notifications</h4>
                                    <button type="button" id="mark-all-read-btn" class="mark-all-read-btn">Mark all as read</button>
                                </div>
                                <div class="notification-dropdown-body" id="notification-dropdown-body">
                                    <div class="notification-loading">
                                        <i class="fa-solid fa-spinner fa-spin"></i> Loading...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Toggle Button -->
                        <button class="cart-toggle" aria-label="Open Shopping Cart">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="cart-badge" id="global-cart-count">0</span>
                        </button>

                        <!-- User Profile Dropdown / Login -->
                        @if(auth()->guard('staff')->check())
                            <div class="user-profile">
                                <button class="user-toggle">
                                    <img class="user-avatar" src="{{ auth()->guard('staff')->user()->image ? asset('storage/' . auth()->guard('staff')->user()->image) : asset('images/avatar-placeholder.png') }}" alt="Avatar">
                                    <span>{{ auth()->guard('staff')->user()->name }}</span>
                                    <i class="fa-solid fa-chevron-down user-toggle-arrow"></i>
                                </button>
                                <div class="user-dropdown">
                                    <div class="dropdown-header">
                                        <p>Staff Profile</p>
                                        <h4>{{ auth()->guard('staff')->user()->name }}</h4>
                                    </div>
                                    <a href="{{ url('/admin/dashboard') }}" class="dropdown-link"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                                    <a href="#" class="dropdown-link"><i class="fa-solid fa-user-gear"></i> Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ url('/admin/logout') }}" method="POST" id="admin-logout-form" class="hidden-form">
                                        @csrf
                                    </form>
                                    <a href="#" class="dropdown-link" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                                    </a>
                                </div>
                            </div>
                        @elseif(auth()->guard('customer')->check())
                            <div class="user-profile">
                                <button class="user-toggle">
                                    <img class="user-avatar" src="{{ auth()->guard('customer')->user()->image ? asset('storage/' . auth()->guard('customer')->user()->image) : asset('images/avatar-placeholder.png') }}" alt="Avatar">
                                    <span>{{ auth()->guard('customer')->user()->name }}</span>
                                    <i class="fa-solid fa-chevron-down user-toggle-arrow"></i>
                                </button>
                                <div class="user-dropdown">
                                    <div class="dropdown-header">
                                        <p>Customer Profile</p>
                                        <h4>{{ auth()->guard('customer')->user()->name }}</h4>
                                    </div>
                                    <a href="{{ route('customer.dashboard') }}" class="dropdown-link"><i class="fa-solid fa-book-bookmark"></i> My Library</a>
                                    <a href="{{ route('customer.store.orders') }}" class="dropdown-link"><i class="fa-solid fa-box-open"></i> My Orders</a>
                                    <a href="{{ route('customer.subscription.index') }}" class="dropdown-link"><i class="fa-solid fa-crown text-gold-crown"></i> Membership Plan</a>
                                    <a href="{{ route('customer.profile.show') }}" class="dropdown-link"><i class="fa-solid fa-user-gear"></i> Profile Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('customer.logout') }}" method="POST" id="customer-logout-form" class="hidden-form">
                                        @csrf
                                    </form>
                                    <a href="#" class="dropdown-link" onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                                    </a>
                                </div>
                            </div>
                        @else
                            <a href="{{ url('/login') }}" class="dropdown-link login-action-btn">
                                <i class="fa-solid fa-user-lock"></i> Login
                            </a>
                        @endif
                    </div>
                </nav>
            </div>
        </header>

        <!-- 2. Main Page Content Container -->
        <main>
            @yield('content')
        </main>

        <!-- 3. Slide-out Cart Drawer -->
        <div class="drawer-overlay" id="cart-drawer-overlay"></div>
        <div class="cart-drawer">
            <div class="drawer-header">
                <h3><i class="fa-solid fa-cart-shopping"></i> Shopping Cart</h3>
                <button class="drawer-close" aria-label="Close Cart">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="drawer-body" id="cart-drawer-body">
                <!-- Fallback Empty State -->
                <div class="cart-empty">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <p>Your shopping cart is empty.</p>
                </div>
            </div>
            <div class="drawer-footer">
                <div class="cart-summary">
                    <span>Total Value:</span>
                    <h4 id="cart-total-amount">0 Ks</h4>
                </div>
                <a href="{{ route('customer.store.checkout') }}" class="checkout-btn">Checkout</a>
            </div>
        </div>

        <!-- 4. Footer Section -->
        <footer class="main-footer" id="main-footer">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <div class="footer-brand">
                            <i class="fa-solid fa-book-open"></i>
                            <span>Booknest</span>
                        </div>
                        <p>Booknest is an online bookstore offering high-quality books and great literary experiences for you.</p>
                        <div class="footer-socials">
                            <a href="#" class="social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-icon" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
                            <a href="#" class="social-icon" aria-label="Viber"><i class="fa-brands fa-viber"></i></a>
                        </div>
                    </div>

                    <div class="footer-col">
                        <h3>Contact Us</h3>
                        <p><i class="fa-solid fa-location-dot footer-contact-icon"></i> No. 123, Anawrahta Road, Yangon.</p>
                        <p><i class="fa-solid fa-phone footer-contact-icon"></i> 09 123 456 789</p>
                        <p><i class="fa-solid fa-envelope footer-contact-icon"></i> support@booknest.com</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; 2026 Booknest. All rights reserved.</p>
                    <div class="footer-payment-methods">
                        <i class="fa-brands fa-cc-stripe" title="Stripe"></i>
                        <i class="fa-solid fa-money-bill-transfer" title="KPay / WaveMoney / COD"></i>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Layout Base JS Interaction -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Search Input Clear Button Logic for entire store -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initSearchClearButtons() {
                const searchInputs = document.querySelectorAll('input[name="search"], input#author-search, input#search-input');
                searchInputs.forEach(input => {
                    if (input.dataset.clearInit) {
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
    
    <!-- Page Specific Scripts -->
    @yield('scripts')
</body>
</html>
