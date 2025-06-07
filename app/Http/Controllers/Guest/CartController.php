<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\Province;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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

        return view('layouts.pages.guest.cart', compact('cartItems', 'subtotal', 'shippingFee', 'discount', 'total', 'cart'));
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
    public function remove($productId, $productVariantId = null)
    {

        $cartDetail = CartDetail::whereHas('cart', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->where('product_id', $productId)
            ->when($productVariantId, fn($q) => $q->where('product_variant_id', $productVariantId))
            ->first();

        if (isset($cartDetail)) {
            $cartDetail->delete();
            return redirect()->back()->with(['status' => 'success', 'success' => 'Đã xóa sản phẩm khỏi giỏ hàng.']);
        }

        return redirect()->back()->with('error', 'Sản phẩm không tồn tại trong giỏ hàng.');
    }

    // Áp dụng mã giảm giá
    public function applyCoupon(Request $request)
    {

        $request->validate([
            'coupon_code' => 'required|string'
        ]);
        $now = Carbon::now();

        $cart = Cart::where('user_id', auth()->id())->firstOrFail();

        $couponCode = strtolower($request->coupon_code);

        // Giả sử bạn có model Coupon
        $coupon = Coupon::whereRaw('LOWER(code) = ?', [$couponCode])
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        if ($coupon->usage_limit !== null && $coupon->used >= $coupon->usage_limit) {
            return response()->json(['error' => 'Mã giảm giá đã được sử dụng quá số lần cho phép.'], 400);
        }

        if (strtolower($cart->coupon_code) == $couponCode) {
            return back()->with('error', 'Mã giảm giá đã được áp dụng.');
        }


        $cart->coupon_code = $coupon->code;
        $cart->discount_amount = $coupon->value; // hoặc tính theo %
        $cart->save();

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }


    public function addToCart(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $qty = max(1, (int) $request->input('qty', 1)); // Đảm bảo >= 1
        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = session()->getId();
        $productVariantId = $request->input('product_variant_id', null);
        // 1. Tìm hoặc tạo giỏ hàng của người dùng / khách
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'session_id' => $userId ? null : $sessionId]
        );

        // 2. Kiểm tra sản phẩm đã có trong giỏ chưa
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        if ($cartDetail) {
            // Nếu đã có → tăng số lượng
            $cartDetail->qty = $cartDetail->qty + $qty;
            $cartDetail->save();
        } else {
            $productPrice = (isset($product->productVariants) && $product->productVariants != '[]') ? $product->productVariants->where('id', $productVariantId)->first()->price : $product->price;

            // Nếu chưa có → thêm mới
            CartDetail::create([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
                'qty'        => $qty,
                'price'      => $productPrice,
                'product_variant_id' => $productVariantId
            ]);
        }

        return redirect()->back()->with(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng!']);
    }

    public function checkout()
    {
        $cart = null;
        $provinces = Province::all();
        if (Auth::check()) {
            // Người dùng đã đăng nhập: lấy giỏ hàng theo user_id
            $userId = Auth::id();
            $cart = Cart::with('cartDetails.product')->where('user_id', $userId)->first();
            $ShippingAddress = ShippingAddress::where('user_id', $userId)->get();
        } else {
            // Chưa đăng nhập: lấy giỏ hàng theo session_id
            $sessionId = Session::getId();
            $cart = Cart::with('cartDetails.product')->where('session_id', $sessionId)->first();
            $ShippingAddress = ShippingAddress::where('session_id', $sessionId)->get();
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
        $userInfo = Auth::user();

        return view('layouts.pages.guest.checkout', compact('cartItems', 'subtotal', 'shippingFee', 'discount', 'total', 'provinces', 'userInfo', 'cart', 'ShippingAddress'));
    }

    public function updateQty(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $qty = max(1, (int) $request->input('qty', 1)); // Đảm bảo >= 1
        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = session()->getId();
        $productVariantId = $request->input('product_variant_id', null);
        // 1. Tìm hoặc tạo giỏ hàng của người dùng / khách
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'session_id' => $userId ? null : $sessionId]
        );

        // 2. Kiểm tra sản phẩm đã có trong giỏ chưa
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $productVariantId)
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
                'product_variant_id' => $productVariantId
            ]);
        }
    }

    // app/Http/Controllers/CartController.php

    public function updateQuantity(Request $request)
    {
        $itemId = $request->input('id');
        $qty = (int) $request->input('qty');

        // Cập nhật giỏ hàng (nếu bạn dùng một Cart package, ví dụ Gloudemans)
        $cartItem = CartDetail::find($itemId);
        if ($cartItem) {
            $cartItem->qty = $qty;
            $cartItem->save();
        }

        // Tính lại tổng
        $cartItems = auth()->user()->cart->cartDetails; // Hoặc session-based tùy bạn
        // dd($cartItems);
        $subtotal = $cartItems->sum(fn($item) => ($item->productVariant->price ?? $item->product->price) * $item->qty);
        $shippingFee = 20000;
        $discount = 0; // nếu có mã thì tính sau
        $total = $subtotal + $shippingFee - $discount;

        return response()->json([
            'success' => true,
            'item_total' => number_format(($cartItem->productVariant->price ?? $cartItem->product->price) * $qty),
            'subtotal' => number_format($subtotal),
            'total' => number_format($total),
        ]);
    }

    public function removeCoupon(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->coupon_code = null;
        $cart->discount_amount = null; // hoặc tính theo %
        $cart->save();

        return redirect()->back()->with('success', 'Đã xóa mã giảm giá.');
    }
}
