<?php

use App\Http\Controllers\Guest\CategoryController;
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

Route::get('/', function () {
    return view('layouts.pages.guest.home');
})->name('home');


Route::get('/category/{slug}', [CategoryController::class, 'index'])->name('category.show');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
