<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Contact;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Đơn hàng theo trạng thái
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Người dùng mới trong 7 ngày
        $usersLast7Days = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sản phẩm bán chạy (Top 5)
        $bestSellers = Product::select('products.title', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('order_details', 'products.id', '=', 'order_details.product_id')
            ->groupBy('products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Số lượt liên hệ
        $contactCount = Contact::count();

        // Số người đăng ký (tổng users)
        $userCount = User::count();

        return view('layouts.pages.admin.dashboard', compact(
            'ordersByStatus',
            'usersLast7Days',
            'bestSellers',
            'contactCount',
            'userCount'
        ));
    }

    public function data()
    {
        // Biểu đồ doanh thu theo tháng (giả sử mỗi sản phẩm có trường 'price' và 'created_at')
        $monthlySales = Product::selectRaw('MONTH(created_at) as month, SUM(price) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = 'Tháng ' . $i;
            $data[] = $monthlySales[$i] ?? 0;
        }

        // Biểu đồ tròn: Thống kê số lượng thực thể
        $productCount = Product::count();
        $userCount = User::count();
        $contactCount = Contact::count();

        return response()->json([
            'areaChart' => [
                'labels' => $labels,
                'data' => $data
            ],
            'pieChart' => [
                'labels' => ['Sản phẩm', 'Người dùng', 'Liên hệ'],
                'data' => [$productCount, $userCount, $contactCount]
            ]
        ]);
    }
}
