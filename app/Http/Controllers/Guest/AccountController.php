<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Province;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $provinces = Province::all();
        $orders = Order::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10); // hoặc paginate(10)
        return view('layouts.pages.guest.my_account', [
            'user' => Auth::user(),
            'provinces' => $provinces,
            'roles' => $roles,
            'orders' => $orders,
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
        $order = Order::with(['orderDetails.product'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'order' => $order,
            'items' => $order->orderDetails->map(function ($item) {
                return [
                    'name' => $item->product->title,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
        ]);
    }
}
