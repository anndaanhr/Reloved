<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/home', function () {
    return redirect()->route('home');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Forgot Password Routes
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('forgot-password/sent', [ForgotPasswordController::class, 'showLinkSentResponse'])->name('password.sent');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Email Verification Routes (accessible by both guest and authenticated users)
Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verify.email');
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->name('resend.otp');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile Routes
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Product Routes (Auth Required)
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/{id}/images/{imageId}', [ProductController::class, 'deleteImage'])->name('products.images.destroy');
});

// Public Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Category Routes
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Public Shipping API Routes (for registration and product forms)
Route::get('/api/shipping/provinces', [ShippingController::class, 'getProvinces'])->name('api.shipping.provinces');
Route::get('/api/shipping/cities', [ShippingController::class, 'getCities'])->name('api.shipping.cities');
Route::post('/api/shipping/check-cost', [ShippingController::class, 'checkCost'])->name('api.shipping.check-cost');
Route::post('/api/shipping/city-id', [ShippingController::class, 'getCityId'])->name('api.shipping.city-id');
Route::get('/api/shipping/search-destinations', [ShippingController::class, 'searchDestinations'])->name('api.shipping.search-destinations');
Route::post('api/shipping/subdistrict-id', [ShippingController::class, 'getSubdistrictId'])->name('api.shipping.subdistrict-id');
Route::post('api/shipping/calculate-cost', [ShippingController::class, 'calculateCost'])->name('api.shipping.calculate-cost');
Route::post('api/shipping/search-cities', [ShippingController::class, 'searchCities'])->name('api.shipping.search-cities');

// Chat Routes
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{id}/send', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/chat/{id}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
    Route::get('/api/products/{productId}/buyers', [ChatController::class, 'getBuyersForProduct'])->name('api.products.buyers');
    
    // Offer Routes
    Route::post('/chat/{conversationId}/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::post('/chat/{conversationId}/offers/{messageId}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::post('/chat/{conversationId}/offers/{messageId}/reject', [OfferController::class, 'reject'])->name('offers.reject');
    Route::post('/chat/{conversationId}/offers/{messageId}/counter', [OfferController::class, 'counter'])->name('offers.counter');
    
    // Transaction Routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/{id}/shipping', [TransactionController::class, 'updateShipping'])->name('transactions.shipping');
    Route::post('/transactions/{id}/received', [TransactionController::class, 'markAsReceived'])->name('transactions.received');
    Route::post('/transactions/{id}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    
    // Review Routes
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create/{transactionId}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{productId}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/api/wishlist/{productId}/check', [WishlistController::class, 'check'])->name('api.wishlist.check');
    
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
});