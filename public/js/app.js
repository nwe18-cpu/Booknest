document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Navigation Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('active');

            // Change hamburger icon depending on state
            const icon = menuToggle.querySelector('i');
            if (icon) {
                if (navMenu.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // 2. Slide-out Cart Drawer Toggle
    const cartToggle = document.querySelector('.cart-toggle');
    const cartDrawer = document.querySelector('.cart-drawer');
    const drawerClose = document.querySelector('.drawer-close');
    const drawerOverlay = document.querySelector('.drawer-overlay');

    function openCart() {
        if (cartDrawer && drawerOverlay) {
            cartDrawer.classList.add('active');
            drawerOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent main page scrolling
        }
    }

    function closeCart() {
        if (cartDrawer && drawerOverlay) {
            cartDrawer.classList.remove('active');
            drawerOverlay.classList.remove('active');
            document.body.style.overflow = ''; // Restore main page scrolling
        }
    }

    if (cartToggle) {
        cartToggle.addEventListener('click', function (e) {
            e.preventDefault();
            openCart();
        });
    }

    if (drawerClose) {
        drawerClose.addEventListener('click', closeCart);
    }

    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', closeCart);
    }

    // Expose openCart globally so dynamic product adding can trigger the slide out drawer
    window.openBooknestCart = openCart;
    window.closeBooknestCart = closeCart;

    // 3. User Profile Dropdown Toggle
    const userToggle = document.querySelector('.user-toggle');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
    }

    // 4. Customer Notification Dropdown Logic
    const notificationToggle = document.querySelector('.notification-toggle');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const notificationBody = document.getElementById('notification-dropdown-body');
    const notificationBadge = document.getElementById('notification-badge');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');

    function fetchNotifications() {
        if (!notificationBody) return;
        
        fetch('/customer/notifications')
            .then(res => {
                if (res.status === 401) {
                    notificationBody.innerHTML = '<div class="notification-empty">Please log in to view notifications.</div>';
                    throw new Error('Unauthorized');
                }
                return res.json();
            })
            .then(data => {
                // Update badge count
                if (notificationBadge) {
                    notificationBadge.textContent = data.unread_count;
                    if (data.unread_count === 0) {
                        notificationBadge.style.display = 'none';
                    } else {
                        notificationBadge.style.display = 'flex';
                    }
                }

                if (data.notifications.length === 0) {
                    notificationBody.innerHTML = '<div class="notification-empty"><i class="fa-regular fa-bell-slash"></i><p>No notifications yet.</p></div>';
                    return;
                }

                let html = '';
                data.notifications.forEach(item => {
                    const unreadClass = item.is_read ? '' : 'unread';
                    const unreadDot = item.is_read ? '' : '<span class="unread-gold-dot"></span>';
                    html += `
                        <div class="notification-item ${unreadClass}">
                            <div class="notification-item-header">
                                <h5 class="notification-item-title">${item.title}</h5>
                                ${unreadDot}
                            </div>
                            <p class="notification-item-msg">${item.message}</p>
                            <span class="notification-item-time">${item.time_ago}</span>
                        </div>
                    `;
                });
                notificationBody.innerHTML = html;
            })
            .catch(err => {
                console.error('Error fetching notifications:', err);
            });
    }

    if (notificationToggle && notificationDropdown) {
        notificationToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close user dropdown if open
            if (userDropdown) userDropdown.classList.remove('active');
            
            notificationDropdown.classList.toggle('active');
            
            if (notificationDropdown.classList.contains('active')) {
                notificationBody.innerHTML = '<div class="notification-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';
                fetchNotifications();
            }
        });
    }

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/customer/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (notificationBadge) {
                        notificationBadge.textContent = '0';
                        notificationBadge.style.display = 'none';
                    }
                    const unreadItems = notificationBody.querySelectorAll('.notification-item.unread');
                    unreadItems.forEach(item => {
                        item.classList.remove('unread');
                        const dot = item.querySelector('.unread-gold-dot');
                        if (dot) dot.remove();
                    });
                }
            })
            .catch(err => console.error('Error marking read:', err));
        });
    }

    // 5. Click outside to close menus and dropdowns
    document.addEventListener('click', function (e) {
        // Close user dropdown if clicked outside
        if (userDropdown && userDropdown.classList.contains('active')) {
            if (!userDropdown.contains(e.target) && !userToggle.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        }

        // Close notification dropdown if clicked outside
        if (notificationDropdown && notificationDropdown.classList.contains('active')) {
            if (!notificationDropdown.contains(e.target) && !notificationToggle.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        }

        // Close mobile nav menu if clicked outside
        if (navMenu && navMenu.classList.contains('active')) {
            if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                const icon = menuToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        }
    });

    // 6. Warm Sepia Theme Toggle
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    if (themeToggleBtn) {
        // Sync active state icon on load
        if (document.documentElement.classList.contains('warm-theme')) {
            themeToggleBtn.classList.add('active');
        }

        themeToggleBtn.addEventListener('click', function () {
            document.documentElement.classList.toggle('warm-theme');
            
            const isWarm = document.documentElement.classList.contains('warm-theme');
            localStorage.setItem('theme', isWarm ? 'warm' : 'default');
            
            if (isWarm) {
                themeToggleBtn.classList.add('active');
            } else {
                themeToggleBtn.classList.remove('active');
            }
        });
    }
});
