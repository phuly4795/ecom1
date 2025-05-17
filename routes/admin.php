<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

Route::middleware('auth','verified', 'checkRole')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
        Route::get('/createUserRole', [HomeController::class, 'createCustomer'])->name('createCustomer');
        Route::get('/createRole', function () {
            $role         =  new Role();
            $role->name   =  'Customer';
            $role->slug   =  'customer';
            $role->save();
        });
        Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts'])->name('getDistricts');
        Route::get('/wards/{districtId}', [LocationController::class, 'getWards'])->name('getWards');


        Route::prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/data', [CategoryController::class, 'data'])->name('data');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');

            Route::post('category', [CategoryController::class, 'storeOrUpdate'])->name('store');
            Route::put('category/{id}', [CategoryController::class, 'storeOrUpdate'])->name('update');

            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');

            Route::post('/upload-image', [CategoryController::class, 'uploadImage'])->name('uploadImage');
        });

    });

    
});
Route::fallback(function () {
    return view('layouts.pages.404');
});