<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\Auth\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\ClassificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\ActivityLogController;

/*
|--------------------------------------------------------------------------
| 1. CUSTOMER ROUTES (Front-End & Portal)
|--------------------------------------------------------------------------
*/

Route::get('/', [CustomerController::class, 'storeHome'])->name('customer.store.home');
Route::get('/store', [CustomerController::class, 'storeHome']);

Route::get('/login', [CustomerAuthController::class, 'showAuthForm'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::get('/forgot-password', [CustomerAuthController::class, 'showForgotPasswordForm'])->name('customer.forgot_password');
Route::post('/forgot-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.forgot_password.reset');

Route::get('/store/cart/data', [CustomerController::class, 'getCartData'])->name('customer.store.cart.data');
Route::get('/store/books/{id}/reviews', [CustomerController::class, 'getBookReviews'])->name('customer.books.reviews');

// Protected Customer Routes
Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/dashboard', [CustomerController::class, 'index'])->name('customer.dashboard');
    Route::post('/customer/reading-progress', [CustomerController::class, 'saveProgress'])->name('customer.save_progress');
    Route::post('/customer/reading-progress/bookmark', [CustomerController::class, 'toggleBookmark'])->name('customer.toggle_bookmark');
    Route::post('/customer/library/remove/{id}', [CustomerController::class, 'removeDownloadedBook'])->name('customer.books.remove');
    Route::post('/store/books/{id}/wishlist', [CustomerController::class, 'toggleWishlist'])->name('customer.books.wishlist.toggle');
    Route::post('/store/books/{id}/reviews', [CustomerController::class, 'submitBookReview'])->name('customer.books.reviews.submit');
    Route::get('/customer/profile', [CustomerController::class, 'showProfile'])->name('customer.profile.show');
    Route::post('/customer/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
    
    // Saved Address Management
    Route::post('/customer/addresses', [CustomerController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::put('/customer/addresses/{id}', [CustomerController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/customer/addresses/{id}', [CustomerController::class, 'deleteAddress'])->name('customer.addresses.delete');
    Route::post('/customer/addresses/{id}/default', [CustomerController::class, 'setDefaultAddress'])->name('customer.addresses.default');
    Route::get('/customer/notifications', [CustomerController::class, 'getNotifications'])->name('customer.notifications.get');
    Route::post('/customer/notifications/mark-all-read', [CustomerController::class, 'markAllNotificationsRead'])->name('customer.notifications.mark_read');
    
    // Bookstore Portal Routes
    Route::get('/store/books/{id}/download', [CustomerController::class, 'downloadPdf'])->name('customer.books.download');
    Route::get('/store/books/{id}/stream', [CustomerController::class, 'streamPdf'])->name('customer.books.stream');
    Route::get('/store/cart', [CustomerController::class, 'cart'])->name('customer.store.cart');
    Route::post('/store/cart/add', [CustomerController::class, 'addToCart'])->name('customer.store.cart.add');
    Route::post('/store/cart/update', [CustomerController::class, 'updateCart'])->name('customer.store.cart.update');
    Route::post('/store/cart/remove', [CustomerController::class, 'removeFromCart'])->name('customer.store.cart.remove');
    Route::get('/store/checkout', [CustomerController::class, 'checkout'])->name('customer.store.checkout');
    Route::post('/store/checkout', [CustomerController::class, 'processCheckout'])->name('customer.store.checkout.process');
    Route::get('/store/checkout/success', [CustomerController::class, 'paymentSuccess'])->name('customer.store.checkout.success');
    Route::get('/store/checkout/cancel', [CustomerController::class, 'paymentCancel'])->name('customer.store.checkout.cancel');
    Route::get('/store/orders', [CustomerController::class, 'orders'])->name('customer.store.orders');
    
    // Subscription / Membership Routes
    Route::get('/store/subscription', [CustomerController::class, 'subscriptionIndex'])->name('customer.subscription.index');
    Route::post('/store/subscription/checkout', [CustomerController::class, 'subscriptionCheckout'])->name('customer.subscription.checkout');
    Route::get('/store/subscription/success', [CustomerController::class, 'subscriptionSuccess'])->name('customer.subscription.success');
    
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
});

use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StaffController;

/* ==========================================================================
   2. ADMIN PANEL ROUTES (Grouped & Prefixed)
   ========================================================================== */
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Guest Admin Routes
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated Admin Routes
    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Catalog Management URL: /admin/catalog/...
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::get('/', [CatalogController::class, 'index'])->name('index'); 
            Route::post('/store', [CatalogController::class, 'store'])->name('store');
            Route::post('/update/{id}', [CatalogController::class, 'update'])->name('update');
            Route::post('/destroy/{id}', [CatalogController::class, 'destroy'])->name('destroy');
        });

        // Author Management URL: /admin/authors/...
        Route::get('/authors/{author}/books', [CatalogController::class, 'getAuthorBooks'])->name('authors.books');
        Route::resource('authors', AuthorController::class)->except(['show']);
        Route::resource('classifications', ClassificationController::class)->except(['show']);
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->except(['show']);

        // Reviews Moderation Routes
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // Admin Only Routes (Orders, Customers, Subscriptions)
        Route::middleware('admin.only')->group(function () {
            // Order Management Routes
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

            // Customer Management Routes
            Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
            Route::post('/customers/{id}/toggle-status', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
            Route::post('/customers/{id}/subscription', [AdminCustomerController::class, 'updateSubscription'])->name('customers.updateSubscription');

            // Subscription Management Routes
            Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');

            // Staff Management Routes
            Route::resource('staff', StaffController::class)->except(['show']);
        });

        // Profile Settings Routes
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Activity History Route
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

});

/* ==========================================================================
   3. TESTING ROUTES
   ========================================================================== */
Route::get('/test-layout', function () {
    return view('layouts.app'); 
});