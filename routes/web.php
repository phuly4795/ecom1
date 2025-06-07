<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Guest\AccountController;
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

    Route::get('/product/product_detail/{slug}/{variant?}', [ProductDetailController::class, 'show'])->name('show');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('show');
    Route::post('/cart/add/{productId}', [CartController::class, 'addToCart'])->name('add');
    Route::post('/cart/updateQty', [CartController::class, 'updateQty'])->name('updateQty');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('applyCoupon');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('placeOrder');
    Route::get('/thank-you', [CheckoutController::class, 'thankYou'])->name('thankyou');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-account', [AccountController::class, 'index'])->name('my.account');
    Route::get('/order/track/{id}', [AccountController::class, 'track'])->name('order.track');
    Route::get('/account/orders/{id}', [AccountController::class, 'show'])->name('account.order.detail');

});

Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts'])->name('getDistricts');
Route::get('/wards/{districtId}', [LocationController::class, 'getWards'])->name('getWards');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
