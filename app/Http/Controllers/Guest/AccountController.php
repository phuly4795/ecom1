<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Province;
use App\Models\Role;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $provinces = Province::all();
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        $addresses = ShippingAddress::where('user_id', Auth::id())->orderBy('is_default', 'desc')->get();
        return view('layouts.pages.guest.my_account', [
            'user' => Auth::user(),
            'provinces' => $provinces,
            'roles' => $roles,
            'orders' => $orders,
            'addresses' => $addresses,
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => 'Tài khoản của tôi', 'url' => null],
            ]
        ]);
    }

    public function track($id)
    {
        // Lấy thông tin đơn hàng dựa trên ID
        $order = Order::with('orderDetails')->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Giả định bạn có thông tin vận đơn (tracking info) trong model Order
        $trackingInfo = $order->tracking_number ?? 'Chưa có thông tin vận đơn';

        // Trả về view theo dõi (tạo file resources/views/order/track.blade.php nếu cần)
        return view('layouts.pages.guest.track', compact('order', 'trackingInfo'));
    }

    public function show($id)
    {
        $order = Order::with([
            'orderDetails.product.productImages', 
            'orderDetails.product.productVariants', 
            'shippingAddress.ward', 
            'shippingAddress.district', 
            'shippingAddress.province',
            'billingWard',
            'billingDistrict',
            'billingProvince'
        ])
        ->where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

        $shippingAddress = null;
        if ($order->shippingAddress) {
            $shippingAddress = [
                'full_name' => $order->shippingAddress->full_name,
                'email' => $order->shippingAddress->email,
                'telephone' => $order->shippingAddress->telephone,
                'address' => $order->shippingAddress->address,
                'ward' => $order->shippingAddress->ward ? ['name' => $order->shippingAddress->ward->full_name] : null,
                'district' => $order->shippingAddress->district ? ['name' => $order->shippingAddress->district->full_name] : null,
                'province' => $order->shippingAddress->province ? ['name' => $order->shippingAddress->province->full_name] : null,
            ];
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'billing_full_name' => $order->billing_full_name ?? null,
                'billing_email' => $order->billing_email ?? null,
                'billing_telephone' => $order->billing_telephone ?? null,
                'billing_address' => $order->billing_address ?? null,
                'note' => $order->note ?? null,
                'billingWard' => $order->billingWard ? ['name' => $order->billingWard->full_name] : null,
                'billingDistrict' => $order->billingDistrict ? ['name' => $order->billingDistrict->full_name] : null,
                'billingProvince' => $order->billingProvince ? ['name' => $order->billingProvince->full_name] : null,
                'status' => $order->status,
                'shippingAddress' => $shippingAddress,
            ],
            'items' => $order->orderDetails->map(function ($item) {
                $image = '';
                if ($item->product && $item->product->productImages) {
                    $imgObj = $item->product->productImages->where('type', 1)->first();
                    if ($imgObj) {
                        $image = $imgObj->image;
                    }
                }
                $imagePath = $image
                    ? asset('storage/' . $image)
                    : asset('asset/img/no-image.png');

                $variant = null;
                if ($item->product_variant_id && $item->product && $item->product->productVariants) {
                    $vObj = $item->product->productVariants->firstWhere('id', $item->product_variant_id);
                    if ($vObj) {
                        $variant = $vObj->variant_name;
                    }
                }
                return [
                    'image' => $imagePath,
                    'name' => $item->product ? $item->product->title : $item->product_name,
                    'variant_name' => $variant ?? "-",
                    'quantity' => $item->quantity,
                    'price' => number_format($item->price, 0, ',', '.') . ' VNĐ',
                    'total_price' => number_format($item->total_price, 0, ',', '.') . ' VNĐ',
                ];
            }),
        ]);
    }
    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if (in_array($order->status, ['waiting_pay', 'pending', 'processing'])) {
            $order->status = 'cancelled';
            $order->save();
            return response()->json(['success' => true, 'message' => 'Đơn hàng đã được hủy']);
        }

        return response()->json(['success' => false, 'message' => 'Không thể hủy đơn hàng ở trạng thái này'], 422);
    }

    public function addressStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
        ]);

        $isDefault = $request->has('is_default') ? 1 : 0;

        if ($isDefault) {
            ShippingAddress::where('user_id', $user->id)
                ->update(['is_default' => 0]);
        }

        ShippingAddress::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'ward_id' => $request->ward_id,
            'is_default' => $isDefault,
        ]);
        return redirect()->back()->with('success', 'Địa chỉ đã được thêm thành công.');
    }

    public function addressUpdate(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
        ]);

        $address = ShippingAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $isDefault = $request->has('is_default') ? 1 : $address->is_default;

        if ($isDefault) {
            ShippingAddress::where('user_id', $user->id)
                ->where('id', '!=', $id) 
                ->update(['is_default' => 0]);
        }

        $address->update([
            'full_name' => $request->full_name,
            'telephone' => $request->telephone,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'ward_id' => $request->ward_id,
            'is_default' => $isDefault,
        ]);

        return redirect()->back()->with('success', 'Địa chỉ đã được cập nhật thành công.');
    }

    public function setDefault($id)
    {
        $user = Auth::user();

        // Tìm địa chỉ thuộc user
        $address = ShippingAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Reset tất cả địa chỉ khác về 0
        ShippingAddress::where('user_id', $user->id)
            ->update(['is_default' => 0]);

        // Đặt địa chỉ này thành mặc định
        $address->update(['is_default' => 1]);

        return redirect()->back()->with('success', 'Đã thiết lập địa chỉ mặc định thành công.');
    }

    public function addressDelete(Request $request, $id)
    {
        $user = Auth::user();

        // Tìm địa chỉ thuộc user
        $address = ShippingAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $address->delete();

        return redirect()->back()->with('success', 'Địa chỉ đã được xóa thành công.');
    }
}
