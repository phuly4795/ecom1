<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Lấy danh mục cha và danh mục phụ
            $categories = Category::with('subCategories') // Eager load quan hệ subCategories
                ->where('status', 1)
                ->orderBy('sort', 'asc')
                ->get();

            if (Auth::check()) {
                $userId = Auth::id();
                $cart = Cart::with('cartDetails.product')
                    ->where('user_id', $userId)
                    ->first();
            } else {
                $sessionId = Session::getId();
                $cart = Cart::with('cartDetails.product')
                    ->where('session_id', $sessionId)
                    ->first();
            }

            // Tổng số lượng sản phẩm
            $countQtyCart = 0;
            $totalPrice = 0;
            $cartItems = [];

            if ($cart) {
                $cartItems = $cart->cartDetails;
                foreach ($cartItems as $item) {
                    $countQtyCart += $item->qty;
                    $totalPrice += $item->qty * $item->product->price;
                }
            }

            $view->with([
                'globalCategories' => $categories,
                'countQtyCart' => $countQtyCart,
                'cartItems' => $cartItems,
                'totalPrice' => $totalPrice,
            ]);
        });
    }
}
