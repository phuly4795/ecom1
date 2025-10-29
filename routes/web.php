<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Guest\AccountController;
use App\Http\Controllers\Guest\SubCategoryController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\CategoryController;
use App\Http\Controllers\Guest\CheckoutController;
use App\Http\Controllers\Guest\FavoriteProductController;
use App\Http\Controllers\Guest\HomeController;
use App\Http\Controllers\Guest\PaymentController;
use App\Http\Controllers\Guest\ProductController;
use App\Http\Controllers\Guest\ProductDetailController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

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
    Route::post('/{product}/reviews', [ProductController::class, 'storeReview'])->name('review.store');

    Route::get('/product_detail/{slug}/{variant?}', [ProductDetailController::class, 'show'])->name('show');
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('show');
    Route::post('/add/{productId}', [CartController::class, 'addToCart'])->name('add');
    Route::delete('/remove/{productId}/{productVariantId?}', [CartController::class, 'remove'])->name('remove');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('applyCoupon');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');

    Route::post('/update-quantity', [CartController::class, 'updateQuantity'])->name('updateQuantity');
    Route::post('/remove-coupon', [CartController::class, 'removeCoupon'])->name('removeCoupon');
});

Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('placeOrder');
    Route::get('/thank-you', [CheckoutController::class, 'thankYou'])->name('thankyou');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/my-account', [AccountController::class, 'index'])->name('my.account');
    Route::get('/order/track/{id}', [AccountController::class, 'track'])->name('order.track');
    Route::get('/account/orders/{id}', [AccountController::class, 'show'])->name('account.order.detail');
    Route::post('/account/orders/{id}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::post('/account/address/store', [AccountController::class, 'addressStore'])->name('account.address.store');
    Route::post('/account/address/update/{id}', [AccountController::class, 'addressUpdate'])
        ->name('account.address.update');
    Route::delete('/account/address/delete/{id}', [AccountController::class, 'addressDelete'])
        ->name('account.address.delete');

    Route::post('/account/address/{id}/set-default', [AccountController::class, 'setDefault'])
    ->name('account.address.setDefault');

    Route::post('/favorites/{product}', [FavoriteProductController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteProductController::class, 'index'])->name('favorites.index');
});

Route::get('/provinces', [LocationController::class, 'provinces'])->name('getProvinces');
Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts'])->name('getDistricts');
Route::get('/wards/{districtId}', [LocationController::class, 'getWards'])->name('getWards');
Route::get('/search-products', [ProductController::class, 'search']);
Route::post('/lien-he', [HomeController::class, 'storeContact'])->middleware('throttle:3,1');
Route::put('/update-info', [ProfileController::class, 'updateInfo'])->name('admin.profile.updateInfo');

Route::post('/subscribe-newsletter', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe')->middleware('throttle:3,1'); // Tối đa 3 lần/phút;;

// đăng nhập bằng google
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Thanh toán bằng paypal
Route::get('paypal/create', [PaymentController::class, 'createPayment'])->name('paypal.create');
Route::post('paypal/success', [PaymentController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel', function () {
    return 'Payment cancelled';
})->name('paypal.cancel');

// Public route
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
