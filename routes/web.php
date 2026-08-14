<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommunityImageController as AdminCommunityImageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'beranda'])->name('home');

/*
|--------------------------------------------------------------------------
| Toko (publik)
|--------------------------------------------------------------------------
*/
Route::get('/toko', [ShopController::class, 'index'])->name('shop.index');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/produk/{product:slug}/qr', [ShopController::class, 'qr'])->name('shop.qr');
Route::get('/tentang', [ShopController::class, 'about'])->name('shop.about');
Route::get('/arsip', [ShopController::class, 'archives'])->name('shop.archives');
Route::get('/komunitas', [ShopController::class, 'community'])->name('shop.community');

/*
|--------------------------------------------------------------------------
| Keranjang, Checkout & Pesanan (customer)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/keranjang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::post('/checkout/qris/generate', [CheckoutController::class, 'qrisGenerate'])->name('checkout.qris.generate');
    Route::get('/checkout/qris/status/{token}', [CheckoutController::class, 'qrisStatus'])->name('checkout.qris.status');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/checkout/qris/confirm/{token}', [CheckoutController::class, 'confirmQris'])
    ->name('checkout.qris.confirm')
    ->middleware('signed:relative');

Route::post('/checkout/qris/process/{token}', [CheckoutController::class, 'processQris'])->name('checkout.qris.process');
Route::get('/checkout/qris/success/{token}', [CheckoutController::class, 'qrisSuccess'])->name('checkout.qris.success');

/*
|--------------------------------------------------------------------------
| Panel Admin (hanya role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('kategori', AdminCategoryController::class)
        ->parameters(['kategori' => 'category'])
        ->except('show');

    Route::resource('produk', AdminProductController::class)
        ->parameters(['produk' => 'product'])
        ->except('show');

    Route::resource('galeri', AdminCommunityImageController::class)
        ->parameters(['galeri' => 'communityImage'])
        ->except('show');

    Route::get('produk/{product}/qr', [AdminProductController::class, 'qr'])->name('produk.qr');
    Route::delete('produk/gambar/{productImage}', [AdminProductController::class, 'deleteImage'])->name('produk.gambar.delete');
    Route::patch('produk/gambar/{productImage}/primary', [AdminProductController::class, 'setPrimaryImage'])->name('produk.gambar.primary');

    Route::get('pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
