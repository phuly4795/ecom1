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
            $userId = Auth::id();
            $cart = Cart::with(['cartDetails.product', 'cartDetails.productVariant'])->where('user_id', $userId)->first();
        } else {
            $sessionId = Session::getId();
            $cart = Cart::with(['cartDetails.product', 'cartDetails.productVariant'])->where('session_id', $sessionId)->first();
        }

        if ($cart && $cart->cartDetails) {
            foreach ($cart->cartDetails as $item) {
                $product = $item->productVariant ?? $item->product;

                // Kiểm tra nếu đang khuyến mãi thì cập nhật giá
                $newFinalPrice = $product->is_on_sale ? $product->display_price : $product->original_price;

                if ($item->final_price != $newFinalPrice) {
                    $item->final_price = $newFinalPrice;
                    $item->save();
                }
            }

            // Tính tổng tạm tính từ final_price
            $subtotal = $cart->cartDetails->reduce(function ($carry, $item) {
                return $carry + ($item->final_price * $item->qty);
            }, 0);
        } else {
            $subtotal = 0;
        }

        $shippingFee = 20000;
        $discount = auth()->user()->cart->discount_amount; // nếu có mã thì tính sau
        $total = max($subtotal + $shippingFee - $discount, 0);

        $cartItems = $cart ? $cart->cartDetails : collect();

        $coupons = Coupon::where('is_active', true)
            ->whereColumn('used', '<', 'usage_limit')
            ->whereDate('end_date', '>=', now())
            ->get();

        return view('layouts.pages.guest.cart', compact(
            'cartItems',
            'subtotal',
            'shippingFee',
            'discount',
            'total',
            'cart',
            'coupons'
        ));
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
            return back()->with('error', 'Mã giảm giá đã được sử dụng quá số lần cho phép.');
        }

        if (strtolower($cart->coupon_code) == $couponCode) {
            return back()->with('error', 'Mã giảm giá đã được áp dụng.');
        }

        // $value = 0;
        // if ($coupon->type == 'fixed') {
        //     $value = $coupon->value;
        // } elseif($coupon->type == 'percent') {

        // }

        $cart->coupon_code = $coupon->code;
        $cart->discount_amount = $coupon->value; // hoặc tính theo %
        $cart->save();

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }


    public function addToCart(Request $request, $productId)
    {
        $product = Product::with('productVariants')->findOrFail($productId);
        $qty = max(1, (int) $request->input('qty', 1));
        $userId = auth()->check() ? auth()->id() : null;
        $sessionId = session()->getId();
        $productVariantId = $request->input('product_variant_id', null);

        // Tìm hoặc tạo giỏ hàng
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'session_id' => $userId ? null : $sessionId]
        );

        // Kiểm tra sản phẩm có trong giỏ chưa
        $cartDetail = CartDetail::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        // Lấy variant nếu có
        $variant = $productVariantId
            ? $product->productVariants->where('id', $productVariantId)->first()
            : null;

        // Kiểm tra số lượng tồn kho
        $availableQty = $variant ? $variant->qty : $product->qty;
        if ($qty > $availableQty) {
            return redirect()->back()->with(['status' => 'error', 'message' => 'Số lượng sản phẩm vượt quá tồn kho!']);
        }

        // Tính giá
        $item = $variant ?? $product;
        $originalPrice = $item->original_price;
        $finalPrice = $item->getIsOnSaleAttribute() == true ? $item->getDisplayPriceAttribute() : $originalPrice;

        if ($cartDetail) {
            // Nếu đã có → tăng số lượng
            $cartDetail->qty += $qty;
            $cartDetail->final_price = $finalPrice; // cập nhật giá mới nếu có
            $cartDetail->save();
        } else {
            // Nếu chưa có → thêm mới
            CartDetail::create([
                'cart_id'            => $cart->id,
                'product_id'         => $productId,
                'qty'                => $qty,
                'original_price'     => $originalPrice,
                'final_price'        => $finalPrice,
                'product_variant_id' => $productVariantId,
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

        $user = Auth::user();
        $cart = $user
            ? Cart::with('cartDetails.product')->where('user_id', $user->id)->first()
            : Cart::with('cartDetails.product')->where('session_id', Session::getId())->first();

        if (!$cart || $cart->cartDetails->isEmpty()) {
            return redirect()->back()->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $cartItems = collect();

        if ($cart && $cart->cartDetails) {
            $cartItems = $cart->cartDetails;
        }

        // Tính tổng tạm tính
        $subtotal = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->final_price * $item->qty);
        }, 0);

        $shippingFee = 20000;
        $discount = auth()->user()->cart->discount_amount; // nếu có mã thì tính sau

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
        $subtotal = $cartItems->sum(fn($item) => ($item->final_price) * $item->qty);
        $shippingFee = 20000;
        $discount = auth()->user()->cart->discount_amount; // nếu có mã thì tính sau
        $total = $subtotal + $shippingFee - $discount;

        return response()->json([
            'success' => true,
            'item_total' => number_format(($cartItem->final_price) * $qty),
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
