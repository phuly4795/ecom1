<?php

use App\Http\Controllers\Guest\SubCategoryController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\CategoryController;
use App\Http\Controllers\Guest\CheckoutController;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\ProductController;
use App\Http\Controllers\Guest\ProductDetailController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/category/{slug}', [CategoryController::class, 'index'])->name('category.show');
Route::get('/subcategory/{slug}', [SubCategoryController::class, 'show'])->name('subcategory.show');

Route::prefix('product')->name('product.')->group(function () {
    Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('review.store');

    Route::get('product_detail/{slug}', [ProductDetailController::class, 'index'])->name('show');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('show');
    Route::post('/cart/add/{productId}', [CartController::class, 'addToCart'])->name('add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('applyCoupon');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('show');
    Route::post('/', [CheckoutController::class, 'process'])->name('process');
});

Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts'])->name('getDistricts');
Route::get('/wards/{districtId}', [LocationController::class, 'getWards'])->name('getWards');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
