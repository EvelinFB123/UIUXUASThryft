<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::middleware('web')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop');
    Route::delete('/cart/ajax-remove/{id}', [ShopController::class, 'ajaxRemove'])->name('cart.ajaxRemove');
    Route::get('/{category}/{id}', [ProductController::class, 'show'])->name('detail');
    Route::get('/cart', [ShopController::class, 'cart'])->name('cart');
});

// Guest only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/signup', [RegisterController::class, 'showRegistrationForm'])->name('signup');
    Route::post('/signup', [RegisterController::class, 'register'])->name('signup.post');
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
});

Route::middleware(['auth', 'isSeller'])->group(function () {
    Route::get('/myshop', [ShopController::class, 'index'])->name('myshop.index');
});


// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/room', [RoomController::class, 'index'])->name('room');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/payment/show', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/va', [PaymentController::class, 'processVaPayment'])->name('payment.va');
    Route::post('/payment/qris', [PaymentController::class, 'processQrisPayment'])->name('payment.qris');
    Route::post('/payment/bank', [PaymentController::class, 'processBankPayment'])->name('payment.bank');
    // Route::get('/payment/confirmation', [PaymentController::class, 'showConfirmation'])->name('payment.confirmation');
    Route::post('/shop/add-to-cart/{id}', [ShopController::class, 'addToCart'])->name('add.to.cart');
    Route::post('/shop/buy-now/{id}', [ShopController::class, 'buyNow'])->name('buy.now');
    Route::post('/cart/add/{id}', [ShopController::class, 'addToCart'])->name('cart.add');
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::put('/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    });
    Route::get('/orders/{order}', function (Order $order) {
        // Pastikan order milik user yang sedang login
        if (Auth::id() !== $order->user_id) {
            abort(403, 'Unauthorized action.');
        }
        
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'tracking_number' => $order->tracking_number,
            'shipping_address' => $order->shipping_address,
            'items' => $order->items,
            'created_at' => $order->created_at
        ]);
    })->middleware('auth')->name('orders.show');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/ai', [ChatController::class, 'ai'])->name('ai');
    // Di routes/web.php
    Route::get('/payment/confirmation', [PaymentController::class, 'showConfirmation'])->name('payment.confirmation');
     Route::post('/cart/update', [ShopController::class, 'updateCart'])->name('cart.update');
     Route::get('/payment/confirmation', [PaymentController::class, 'showConfirmation'])
     ->name('payment.confirmation');
});

// Search
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Static pages
Route::view('/categories', 'categories')->name('categories');
Route::view('/woman', 'woman')->name('woman');
Route::view('/man', 'man')->name('man');
Route::view('/carpentry', 'carpentry')->name('carpentry');
Route::view('/kids', 'kids')->name('kids');
Route::view('/muslim', 'muslim')->name('muslim');
Route::view('/books', 'books')->name('books');
Route::view('/sports', 'sports')->name('sports');
Route::view('/electronics', 'electronics')->name('electronics');
Route::view('/automotive', 'automotive')->name('automotive');
Route::view('/music', 'music')->name('music');
Route::view('/toys', 'toys')->name('toys');
Route::view('/gadget', 'gadget')->name('gadget');
Route::view('/furniture', 'furniture')->name('furniture');
Route::view('/gaming', 'gaming')->name('gaming');
Route::view('/kitchen', 'kitchen')->name('kitchen');
Route::view('/baby', 'baby')->name('baby');
Route::view('/aboutus', 'aboutus')->name('aboutus');

// Menampilkan form jual produk
Route::get('/sell', [ProductController::class, 'create'])->name('product.create');

// Menyimpan produk yang dijual
Route::post('/sell', [ProductController::class, 'store'])->name('product.store');
Route::get('/clear-cart', function() {
    session()->forget('cart');
    return "Cart cleared.";
});



