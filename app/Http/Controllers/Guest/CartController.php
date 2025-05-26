<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Hiển thị trang giỏ hàng
    public function index()
    {
        $cart = null;

        if (Auth::check()) {
            // Người dùng đã đăng nhập: lấy giỏ hàng theo user_id
            $userId = Auth::id();
            $cart = Cart::with('cartDetails.product')->where('user_id', $userId)->first();
        } else {
            // Chưa đăng nhập: lấy giỏ hàng theo session_id
            $sessionId = Session::getId();
            $cart = Cart::with('cartDetails.product')->where('session_id', $sessionId)->first();
        }

        $cartItems = collect();

        if ($cart && $cart->cartDetails) {
            $cartItems = $cart->cartDetails;
        }

        // Tính tổng tạm tính
        $subtotal = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->price * $item->qty);
        }, 0);

        $shippingFee = 20000;
        $discount = Session::get('discount', 0);

        $total = max($subtotal + $shippingFee - $discount, 0);

        return view('layouts.pages.guest.cart', compact('cartItems', 'subtotal', 'shippingFee', 'discount', 'total'));
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function update(Request $request)
    {
        $quantities = $request->input('quantities'); // expects ['product_id' => quantity]

        if (!$quantities || !is_array($quantities)) {
            return redirect()->back()->with('error', 'Dữ liệu không hợp lệ.');
        }

        $cart = Session::get('cart', []);

        foreach ($quantities as $productId => $qty) {
            if (isset($cart[$productId]) && $qty > 0) {
                $cart[$productId]['quantity'] = (int)$qty;
            }
        }

        Session::put('cart', $cart);

        return redirect()->back()->with('success', 'Cập nhật giỏ hàng thành công.');
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove($productId)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }

        return redirect()->back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
    }

    // Áp dụng mã giảm giá
    public function applyCoupon(Request $request)
    {
        $code = $request->input('coupon_code');

        // Ví dụ kiểm tra mã coupon đơn giản
        if ($code === 'DISCOUNT10') {
            // Giảm 10$ cố định (ví dụ)
            Session::put('discount', 10);
            return redirect()->back()->with('success', 'Áp dụng mã giảm giá thành công.');
        }

        return redirect()->back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
    }


    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $qty = max(1, (int) $request->input('qty', 1)); // Đảm bảo >= 1
        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = session()->getId();

        // 1. Tìm hoặc tạo giỏ hàng của người dùng / khách
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'session_id' => $userId ? null : $sessionId]
        );

        // 2. Kiểm tra sản phẩm đã có trong giỏ chưa
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartDetail) {
            // Nếu đã có → tăng số lượng
            $cartDetail->qty = $cartDetail->qty + $qty;
            $cartDetail->save();
        } else {
            // Nếu chưa có → thêm mới
            CartDetail::create([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
                'qty'        => $qty,
                'price'      => $product->price,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }
}
