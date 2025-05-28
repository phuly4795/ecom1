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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $categories = Category::where('status', 1)->orderBy('sort', 'asc')->get();

            if (Auth::check()) {
                $userId = Auth::id();

                $cart = Cart::with('cartDetails.product')  // load luôn sản phẩm để lấy info
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

                // Tính tổng số lượng và tổng tiền
                foreach ($cartItems as $item) {
                    $countQtyCart += $item->qty;
                    $totalPrice += $item->qty * $item->product->price;  // giả sử 'price' là cột giá trong bảng products
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
