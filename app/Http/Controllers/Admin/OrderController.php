<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        return view('layouts.pages.admin.order.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['user', 'orderDetails'])->select('orders.*');

            if ($statusFilter = request('status')) {
                $query->where('orders.status', $statusFilter);
            }
            return DataTables::of($query)
                ->addColumn('checkbox', function ($order) {
                    return '<input type="checkbox" class="row-checkbox" value="' . $order->id . '">';
                })
                ->addColumn('DT_RowIndex', function ($order) {
                    static $index = 0;
                    return ++$index;
                })
                ->addColumn('order_code', function ($order) {
                    return $order->order_code;
                })
                ->addColumn('customer', function ($order) {
                    return $order->user ? $order->user->name : $order->billing_full_name;
                })
                ->addColumn('total_items', function ($order) {
                    return $order->orderDetails->sum('quantity');
                })
                ->addColumn('total_amount', function ($order) {
                    return number_format($order->total_amount) . " VNĐ";
                })
                ->addColumn('status', function ($order) {
                    $statuses = [
                        'pending' => 'Đang chờ',
                        'processing' => 'Đang xử lý',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ];
                    $statusClass = match ($order->status) {
                        'pending' => 'badge bg-warning',
                        'processing' => 'badge bg-info',
                        'completed' => 'badge bg-success',
                        'cancelled' => 'badge bg-danger',
                        default => 'badge bg-secondary'
                    };
                    return '<span class="' . $statusClass . '">' . ($statuses[$order->status] ?? ucfirst($order->status)) . '</span>';
                })
                ->addColumn('actions', function ($order) {
                    return '
                    <a href="' . route('admin.orders.show', $order->id) . '" class="btn btn-sm btn-info" data-toggle="tooltip" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                    <a href="' . route('admin.orders.edit', $order->id) . '" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-sm btn-danger delete-order" data-id="' . $order->id . '" data-toggle="tooltip" title="Xóa"><i class="fas fa-trash"></i></button>
                ';
                })
                ->editColumn('payment_method', function ($order) {
                    $statuses = [
                        'cash' => 'Tiền mặt',
                        'cheque' => 'Thanh toán bằng séc',
                    ];
                    return $statuses[$order->payment_method];
                })
                ->editColumn('created_at', function ($order) {
                    return $order->created_at->format('d/m/Y');
                })
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })->orWhere('billing_full_name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['checkbox', 'status', 'actions'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'orderDetails.product',
            'orderDetails.productVariant',
            'shippingAddress.province',
            'shippingAddress.district',
            'shippingAddress.ward',
            'billingProvince',
            'billingDistrict',
            'billingWard'
        ])->findOrFail($id);
        $statuses = ['pending' => 'Đang chờ', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
        return view('layouts.pages.admin.order.detail', compact('order', 'statuses'), ['isEdit' => false]);
    }

    public function edit($id)
    {
        $order = Order::with([
            'user',
            'orderDetails.product',
            'orderDetails.productVariant',
            'shippingAddress.province',
            'shippingAddress.district',
            'shippingAddress.ward',
            'billingProvince',
            'billingDistrict',
            'billingWard'
        ])->findOrFail($id);

        $statuses = ['pending' => 'Đang chờ', 'processing' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
        return view('layouts.pages.admin.order.detail', compact('order', 'statuses'), ['isEdit' => true]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'note' => 'nullable|string|max:500',
        ]);

        $order->update([
            'status' => $validated['status'],
            'note' => $validated['note'],
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công');
    }

    public function massDestroy(Request $request)
    {
        $ids = $request->input('ids');

        try {
            Order::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Đã xóa đơn hàng thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi xóa đơn hàng']);
        }
    }

    public function destroy($id)
    {
        try {
            Order::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Đã xóa đơn hàng thành công']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi khi xóa đơn hàng']);
        }
    }
}
