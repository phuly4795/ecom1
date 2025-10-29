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
        $order = Order::with(['orderDetails.product', 'shippingAddress'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $shippingAddress = [
            'full_name' => $order->shippingAddress->full_name ?? null,
            'email' => $order->shippingAddress->email ?? null,
            'telephone' => $order->shippingAddress->telephone ?? null,
            'address' => $order->shippingAddress->address ?? null,
            'ward' => $order->shippingAddress->ward ? ['name' => $order->shippingAddress->ward->full_name] : null,
            'district' => $order->shippingAddress->district ? ['name' => $order->shippingAddress->district->full_name] : null,
            'province' => $order->shippingAddress->province ? ['name' => $order->shippingAddress->province->full_name] : null,
        ];

        return response()->json([
            'order' => [
                'id' => $order->id,
                'billing_full_name' => $order->billing_full_name ?? null,
                'billing_email' => $order->billing_email ?? null,
                'billing_telephone' => $order->billing_telephone ?? null,
                'billing_address' => $order->billing_address ?? null,
                'billingWard' => $order->billingWard ? ['name' => $order->billingWard->full_name] : null,
                'billingDistrict' => $order->billingDistrict ? ['name' => $order->billingDistrict->full_name] : null,
                'billingProvince' => $order->billingProvince ? ['name' => $order->billingProvince->full_name] : null,
                'status' => $order->status,
                'shippingAddress' => $shippingAddress,
            ],
            'items' => $order->orderDetails->map(function ($item) {
                $image = $item->product->productImages->where('type', 1)->first()->image ?? '';
                $imagePath = $image
                    ? asset('storage/' . $image)
                    : asset('asset/img/no-image.png');
                $variant = isset($item->product->productVariants) && $item->product->productVariants != '[]'
                    ? $item->product->productVariants->where('product_id', $item->product_id)->first()->variant_name
                    : null;
                return [
                    'image' => $imagePath,
                    'name' => $item->product->title,
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

        // Nếu người dùng chọn địa chỉ này là mặc định
        $isDefault = $request->has('is_default') ? 1 : 0;

        if ($isDefault) {
            // Reset tất cả địa chỉ cũ về 0 (không mặc định)
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

        // Tìm địa chỉ thuộc user
        $address = ShippingAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Nếu người dùng chọn địa chỉ này là mặc định
        $isDefault = $request->has('is_default') ? 1 : $address->is_default;

        if ($isDefault) {
            // Reset tất cả địa chỉ cũ về 0 (không mặc định)
            ShippingAddress::where('user_id', $user->id)
                ->where('id', '!=', $id) 
                ->update(['is_default' => 0]);
        }

        // Cập nhật thông tin địa chỉ
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
