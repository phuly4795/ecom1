<?php

namespace App\Http\Controllers\Guest;

use App\Events\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Models\ShippingAddress;
use App\Models\ShippingFee;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'billing_full_name' => 'required|string',
            'billing_email' => 'required|email',
            'billing_telephone' => 'required|string',
            'billing_address' => 'required|string',
            'billing_province_id' => 'required|exists:provinces,code',
            'billing_district_id' => 'required|exists:districts,code',
            'billing_ward_id' => 'required|exists:wards,code',
            'payment_method' => 'required|in:cash,transfer,paypal',
            'terms' => 'accepted',
            'shipping_address_id' => 'nullable|exists:shipping_addresses,id',
            'shipping_full_name' => 'required_if:use_new_shipping_address,on|string|nullable',
            'shipping_telephone' => 'required_if:use_new_shipping_address,on|string|nullable',
            'shipping_address' => 'required_if:use_new_shipping_address,on|string|nullable',
            'shipping_province_id' => 'required_if:use_new_shipping_address,on|exists:provinces,code|nullable',
            'shipping_district_id' => 'required_if:use_new_shipping_address,on|exists:districts,code|nullable',
            'shipping_ward_id' => 'required_if:use_new_shipping_address,on|exists:wards,code|nullable',
        ], [
            'terms.accepted' => "Bạn cần đọc điều khoản và điều kiện"
        ]);

        $user = Auth::user();
        $cart = $user
            ? Cart::with('cartDetails.product')->where('user_id', $user->id)->first()
            : Cart::with('cartDetails.product')->where('session_id', Session::getId())->first();

        if (!$cart || $cart->cartDetails->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        if ($cart->coupon_code) {
            $appliedCoupon = Coupon::where('code', $cart->coupon_code)->first();
            if (!$appliedCoupon || $appliedCoupon->end_date < Carbon::now() || !$appliedCoupon->is_active) {
                $cart->coupon_code = null;
                $cart->discount_amount = null;
                $cart->save();
            }
        }

        foreach ($cart->cartDetails as $item) {
            $product = Product::with('productVariants')->find($item->product_id);

            if (!$product) {
                return redirect()->route('cart.show')->with('error', "Sản phẩm không tồn tại trong hệ thống.");
            }
            $variant = $item->product_variant_id
                ? $product->productVariants->where('id', $item->product_variant_id)->first()
                : null;

            $availableQty = $variant ? $variant->qty : $product->qty;

            if ($item->qty > $availableQty) {
                return redirect()->route('cart.show')->with('error', "Sản phẩm \"{$product->title}\" chỉ còn lại {$availableQty} sản phẩm. Vui lòng điều chỉnh số lượng.");
            }
        }

        DB::beginTransaction();
        try {
            // Tính tổng
            $subtotal = $cart->cartDetails->sum(fn($item) => $item->final_price * $item->qty);
            $shippingAddress = ShippingAddress::find($request->shipping_address_id);

            $province_id = $shippingAddress ? $shippingAddress->province_id : $request->shipping_province_id;
            $district_id = $shippingAddress ? $shippingAddress->district_id : $request->shipping_district_id;

            $shippingFee = $this->getShippingFee($province_id, $district_id);
            $discount = auth()->user()->cart->discount_amount ?? 0; // nếu có mã thì tính sau
            $total = max($subtotal + $shippingFee - $discount, 0);

            // Xử lý địa chỉ giao hàng
            $shippingAddressId = $request->shipping_address_id;
            $useNewShippingAddress = $request->has('use_new_shipping_address');

            if ($useNewShippingAddress && $user) {
                if ($request->is_default) {
                    ShippingAddress::where('user_id', $user->id)
                        ->update(['is_default' => 0]);
                }

                // Lưu địa chỉ giao hàng mới nếu người dùng đăng nhập
                $shippingAddress = ShippingAddress::create([
                    'user_id' => $user->id,
                    'full_name' => $request->shipping_full_name,
                    'email' => $request->shipping_email,
                    'telephone' => $request->shipping_telephone,
                    'address' => $request->shipping_address,
                    'province_id' => $request->shipping_province_id,
                    'district_id' => $request->shipping_district_id,
                    'ward_id' => $request->shipping_ward_id,
                    'is_default' => $request->is_default ? 1 : 0,
                ]);

                $shippingAddressId = $shippingAddress->id;
            }
            // Lấy số thứ tự tiếp theo
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
            $orderCode = 'ORD-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id ?? null,
                'order_code' => $orderCode,
                'shipping_address_id' => $shippingAddressId,
                'billing_full_name' => $request->billing_full_name,
                'billing_email' => $request->billing_email,
                'billing_telephone' => $request->billing_telephone,
                'billing_address' => $request->billing_address,
                'billing_province_id' => $request->billing_province_id,
                'billing_district_id' => $request->billing_district_id,
                'billing_ward_id' => $request->billing_ward_id,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
                'shipping_fee' => $shippingFee,
                'total_amount' => $total,
                'coupon_code' => $cart->coupon_code,
                'discount_amount' => $cart->discount_amount,
                'status' => $request->payment_method == 'transfer' ? 'waiting_pay' :  'pending',
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($cart->cartDetails as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->title,
                    'price' => $item->final_price,
                    'quantity' => $item->qty,
                    'total_price' => $item->final_price * $item->qty,
                ]);
            }
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if (isset($coupon)) {
                $coupon->used++;
                $coupon->save();
            }

            // Xóa giỏ hàng
            $cart->cartDetails()->delete();
            $cart->delete();

            foreach ($order->orderDetails as $item) {
                $variant = $item->product->productVariants->where('id', $item->product_variant_id)->first();
                $displayItem = $variant ?? $item->product;
                $product = $displayItem;
                $product->qty -= $item->quantity;
                $product->save();

                if ($product && $product->qty <= 5) {
                    $notification = Notification::create([
                        'type' => 'low-stock',
                        'title' => 'Sản phẩm sắp hết hàng',
                        'message' => 'Sản phẩm ' . $item->product_name . ' chỉ còn lại ' . $product->qty . ' sản phẩm trong kho.',
                        'reference_id' => $product->id
                    ]);
                    event(new NewNotification($notification));
                }
            }

            $notification = Notification::create([
                'type' => 'new-order',
                'title' => 'Đơn hàng mới',
                'message' => 'Đơn hàng #' . $order->id . ' vừa được tạo ',
                'reference_id' => $order->id
            ]);

            event(new NewNotification($notification));
            // Gửi email xác nhận đơn hàng
            if ($order->billing_email) {
                NotificationFacade::route('mail', $order->billing_email)
                    ->notify(new OrderPlacedNotification($order));
            }
            DB::commit();

            return redirect()->route('checkout.thankyou')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi đặt hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }

    public function thankYou()
    {
        return view('layouts.pages.guest.thank_you');
    }


    public function getShippingFee($province_id, $district_id)
    {

        $fee = ShippingFee::where('province_id', $province_id)
            ->where('district_id', $district_id)
            ->value('fee');
        return $fee ?? config('settings.default_shipping_fee', 50000);
    }
}
