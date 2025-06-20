<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

Route::middleware('auth', 'verified', 'role:admin')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/createUserRole', [HomeController::class, 'createCustomer'])->name('createCustomer');
        Route::get('/createRole', function () {
            $role         =  new Role();
            $role->name   =  'Customer';
            $role->slug   =  'customer';
            $role->save();
        });

        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/data', [CategoryController::class, 'data'])->name('data');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');

            Route::post('category', [CategoryController::class, 'storeOrUpdate'])->name('store');
            Route::put('category/{id}', [CategoryController::class, 'storeOrUpdate'])->name('update');
            Route::post('upload-image', [CategoryController::class, 'uploadImage'])->name('uploadImage');
            Route::delete('/mass-destroy', [CategoryController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
            Route::post('updateOrder', [CategoryController::class, 'updateOrder'])->name('updateOrder');
        });

        Route::prefix('sub_category')->name('sub_category.')->group(function () {
            Route::get('/', [SubCategoryController::class, 'index'])->name('index');
            Route::get('/data', [SubCategoryController::class, 'data'])->name('data');
            Route::get('/create', [SubCategoryController::class, 'create'])->name('create');
            Route::get('/{sub_category}/edit', [SubCategoryController::class, 'edit'])->name('edit');

            Route::post('sub_category', [SubCategoryController::class, 'storeOrUpdate'])->name('store');
            Route::put('sub_category/{id}', [SubCategoryController::class, 'storeOrUpdate'])->name('update');

            Route::delete('/mass-destroy', [SubCategoryController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{sub_category}', [SubCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('brand')->name('brand.')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index');
            Route::get('/data', [BrandController::class, 'data'])->name('data');
            Route::get('/create', [BrandController::class, 'create'])->name('create');
            Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');

            Route::post('brand', [BrandController::class, 'storeOrUpdate'])->name('store');
            Route::put('brand/{id}', [BrandController::class, 'storeOrUpdate'])->name('update');

            Route::delete('/mass-destroy', [BrandController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/data', [ProductController::class, 'data'])->name('data');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');

            Route::post('/', [ProductController::class, 'storeOrUpdate'])->name('store');
            Route::put('/{product}', [ProductController::class, 'storeOrUpdate'])->name('update');

            Route::delete('/mass-destroy', [ProductController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

            Route::post('/upload-image', [ProductController::class, 'uploadImage'])->name('uploadImage');

            Route::get('/get-subcategories/{category_id}', [ProductController::class, 'getSubcategories'])->name('getSubcategories');
            Route::patch('{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggleStatus');
        });

        Route::prefix('order')->name('orders.')->group(function () {
            Route::get('', [OrderController::class, 'index'])->name('index');
            Route::get('/data', [OrderController::class, 'getData'])->name('data');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{id}', [OrderController::class, 'update'])->name('update');
            Route::delete('/mass-destroy', [OrderController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index');
            Route::get('/data', [CouponController::class, 'data'])->name('data');
            Route::get('/create', [CouponController::class, 'create'])->name('create');
            Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
            Route::post('coupon', [CouponController::class, 'storeOrUpdate'])->name('store');
            Route::put('Coupon/{id}', [CouponController::class, 'storeOrUpdate'])->name('update');
            Route::delete('/mass-destroy', [CouponController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{Coupon}', [CouponController::class, 'destroy'])->name('destroy');
            Route::patch('{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('toggleStatus');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/data', [UserController::class, 'data'])->name('data');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');

            Route::post('user', [UserController::class, 'storeOrUpdate'])->name('store');
            Route::put('user/{id}', [UserController::class, 'storeOrUpdate'])->name('update');

            Route::delete('/mass-destroy', [UserController::class, 'massDestroy'])->name('massDestroy');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::put('/{page}', [PageController::class, 'update'])->name('update');
            Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('settings', [SettingController::class, 'edit'])->name('edit');
            Route::post('settings', [SettingController::class, 'update'])->name('update');
        });

        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [ContactController::class, 'index'])->name('index');
            Route::get('/data', [ContactController::class, 'data'])->name('data');
            Route::get('/{id}', [ContactController::class, 'show'])->name('show');
            Route::post('/{id}/mark-as-read', [ContactController::class, 'markAsRead'])->name('markAsRead');
        });

        Route::get('/notifications/{id}', [NotificationController::class, 'read'])->name('notifications.read');

        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
            Route::get('/data', [DashboardController::class, 'data'])->name('data');
        });
    });
});
Route::fallback(function () {
    return view('layouts.pages.admin.404');
});
