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
use App\Http\Controllers\AdminDashboardController; // Dashboard Controller ကို ခေါ်ယူခြင်း

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

Route::get('/store/cart/data', [CustomerController::class, 'getCartData'])->name('customer.store.cart.data');
Route::get('/store/books/{id}/reviews', [CustomerController::class, 'getBookReviews'])->name('customer.books.reviews');

// Protected Customer Routes
Route::middleware('auth:customer')->group(function () {
    Route::get('/customer/dashboard', [CustomerController::class, 'index'])->name('customer.dashboard');
    Route::post('/customer/reading-progress', [CustomerController::class, 'saveProgress'])->name('customer.save_progress');
    Route::post('/customer/reading-progress/bookmark', [CustomerController::class, 'toggleBookmark'])->name('customer.toggle_bookmark');
    Route::post('/store/books/{id}/wishlist', [CustomerController::class, 'toggleWishlist'])->name('customer.books.wishlist.toggle');
    Route::post('/store/books/{id}/reviews', [CustomerController::class, 'submitBookReview'])->name('customer.books.reviews.submit');
    Route::get('/customer/profile', [CustomerController::class, 'showProfile'])->name('customer.profile.show');
    Route::post('/customer/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
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
        Route::post('/authors/quick-store', [AuthorController::class, 'quickStore'])->name('authors.quick-store');
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
        });

        // Profile Settings Routes
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

});

/* ==========================================================================
   3. TESTING ROUTES
   ========================================================================== */
Route::get('/test-layout', function () {
    return view('layouts.app'); 
});