<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $bestSellers = DB::table('order_details')
            ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select('products.id', 'products.title', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.id', 'products.title')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Số lượt liên hệ
        $contactCount = Contact::count();

        // Số người đăng ký
        $userCount = User::count();

        // Doanh thu theo tháng (năm hiện tại)
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        $revenueLabels = [];
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueLabels[] = 'Tháng ' . $i;
            $revenueData[] = $monthlyRevenue[$i] ?? 0;
        }

        // Top khách hàng theo tổng chi tiêu
        $topCustomers = User::select('users.name', DB::raw('SUM(orders.total_amount) as total_spent'))
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->groupBy('users.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Thống kê sản phẩm theo loại (category)
        $productByCategory = Category::select('categories.name', DB::raw('COUNT(products.id) as total'))
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.name')
            ->get();

        // Doanh thu 7 ngày gần nhất
        $revenueLast7Days = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Tổng số liệu
        $productCount = Product::count();
        $userCount = User::count();
        $contactCount = Contact::count();
        return view('layouts.pages.admin.dashboard', compact(
            'ordersByStatus',
            'usersLast7Days',
            'bestSellers',
            'contactCount',
            'userCount',
            'revenueLabels',
            'revenueData',
            'topCustomers',
            'productByCategory',
            'revenueLast7Days',
            'productCount',
            'userCount',
            'contactCount',
        ));
    }
}
