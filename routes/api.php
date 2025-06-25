<?php

use App\Http\Controllers\Guest\ChatAIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web'])->group(function () {
    Route::post('/chat-ai', [ChatAIController::class, 'handle']);
    Route::get('/chat/history', [ChatAIController::class, 'history']);
    Route::post('/chat/clear-chat', [ChatAIController::class, 'destroy']);
});
