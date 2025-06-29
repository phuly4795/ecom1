<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Pagination\Paginator;
use App\Models\Category;
use App\Models\Contact;
use App\Models\FavoriteProduct;
use App\Models\Notification;
use App\Models\Page;
use App\Models\ShippingFee;
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
        Paginator::useBootstrapFour(); // Sử dụng Bootstrap 4 cho phân trang
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
                    $totalPrice += $item->qty * $item->final_price;
                }
            }
            $discount = auth()->user()->cart->discount_amount ?? 0; // nếu có mã thì tính sau
            $shippingFee = $countQtyCart > 0 ? $this->getShippingFee() : 0;
            $totalPrice = max($totalPrice + $shippingFee - $discount, 0);


            $countFavoriteProduct =  FavoriteProduct::with(['products', 'productVariants'])
                ->where('user_id', Auth::id())
                ->count();

            $categories = Category::orderBy('sort')->where('status', 1)->get();

            $view->with([
                'globalCategories' => $categories,
                'countQtyCart' => $countQtyCart,
                'cartItems' => $cartItems,
                'totalPrice' => $totalPrice,
                'countFavoriteProduct' => $countFavoriteProduct,
                'footerCategories' => $categories
            ]);
            $view->with('globalPages', Page::where('is_active', true)->get());


            $notificationContacts = Contact::where('is_read', 0)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $notifications = Notification::where('is_read', 0)
                ->whereNotNull('type')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $view->with([
                'notifications' => $notifications,
                'notificationContacts' => $notificationContacts
            ]);
        });
    }

    private function getShippingFee()
    {
        $user = auth()->user();

        if (!$user) {
            return config('settings.default_shipping_fee', 50000); // mặc định nếu chưa đăng nhập
        }

        $shippingAddress = $user->shippingAddresses->count() === 1
            ? $user->shippingAddresses->first()
            : $user->shippingAddresses->firstWhere('is_default', true);

        if (!$shippingAddress) {
            return config('settings.default_shipping_fee', 50000); // không có địa chỉ => dùng mặc định
        }

        $fee = ShippingFee::where('province_id', $shippingAddress->province_id)
            ->where('district_id', $shippingAddress->district_id)
            ->value('fee');

        return $fee ?? config('settings.default_shipping_fee', 50000);
    }
}
