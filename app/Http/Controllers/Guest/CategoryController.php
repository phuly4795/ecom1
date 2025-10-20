<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request, $slug)
    {

        $isKhuyenMaiPage = $slug === 'khuyen-mai';

        if ($isKhuyenMaiPage) {
            $now = now();

            // 1. Lấy ID các sản phẩm khuyến mãi
            $variantProductIds = ProductVariant::where('discount_percentage', '>', 0)
                ->whereNotNull('discount_start_date')
                ->whereNotNull('discount_end_date')
                ->where('discount_start_date', '<=', $now)
                ->where('discount_end_date', '>=', $now)
                ->pluck('product_id');

            $singleProductIds = Product::where('discount_percentage', '>', 0)
                ->whereNotIn('id', function ($query) {
                    $query->select('product_id')->from('product_variants');
                })
                ->whereNotNull('discount_start_date')
                ->whereNotNull('discount_end_date')
                ->where('discount_start_date', '<=', $now)
                ->where('discount_end_date', '>=', $now)
                ->pluck('id');

            $allProductIds = $variantProductIds->merge($singleProductIds)->unique();

            // 2. Query chính
            $query = Product::with(['productVariants', 'productImages', 'favoritedByUsers', 'category', 'reviews'])
                ->whereIn('id', $allProductIds);

            if ($request->filled('brand_id')) {
                $brandIds = is_array($request->brand_id) ? $request->brand_id : [$request->brand_id];
                $query->whereIn('brand_id', $brandIds);
            }

            $products = $query->paginate(12);

            // Các dữ liệu phụ
            $brandIds = (clone $query)->pluck('brand_id')->unique()->filter()->values();
            $brands = Brand::whereIn('id', $brandIds)->get();

            $productCountsByBrand = Product::whereIn('id', $allProductIds)
                ->selectRaw('brand_id, COUNT(*) as total')
                ->groupBy('brand_id')
                ->pluck('total', 'brand_id');

            $variantPrices = DB::table('product_variants')
                ->whereIn('product_id', $allProductIds)
                ->pluck('original_price');

            $nonVariantPrices = Product::whereIn('id', $allProductIds)
                ->whereNotIn('id', DB::table('product_variants')->select('product_id'))
                ->pluck('original_price');

            $allPrices = $variantPrices->merge($nonVariantPrices);
            $priceMin = $allPrices->min() ?? 0;
            $priceMax = $allPrices->max() ?? 0;

            $bestSellingProducts = Product::with('category')
                ->select('products.*', DB::raw('SUM(order_details.quantity) as total_sold'))
                ->join('order_details', 'order_details.product_id', '=', 'products.id')
                ->groupBy('order_details.product_id')
                ->orderByDesc('total_sold')
                ->take(3)
                ->get();

            // 3. Trả về theo AJAX hoặc view
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('partials.products', [
                        'products' => $products,
                        'isKhuyenMaiPage' => true
                    ])->render()
                ]);
            }

            return view('layouts.pages.guest.category', [
                'subCategoryIds' => [],
                'products' => $products,
                'brands' => $brands,
                'productCountsByBrand' => $productCountsByBrand,
                'bestSellingProducts' => $bestSellingProducts,
                'priceMin' => $priceMin,
                'priceMax' => $priceMax,
            ]);
        }

        $category = Category::with('subCategories')->where('slug', $slug)->firstOrFail();
        $subCategoryIds = $category->subCategories->pluck('id');

        // Lấy danh sách ID sản phẩm thuộc category này
        $allProductIds = Product::where(function ($q1) use ($category, $subCategoryIds) {
            $q1->whereIn('subcategory_id', $subCategoryIds)
                ->orWhere(function ($q2) use ($category) {
                    $q2->where('category_id', $category->id)
                        ->whereNull('subcategory_id');
                });
        })->pluck('id')->toArray();

        // Lọc sản phẩm có biến thể thỏa điều kiện giá
        $variantProductIds = DB::table('product_variants')
            ->select('product_id')
            ->whereIn('product_id', $allProductIds)
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('original_price', '>=', (int) str_replace('.', '', $request->min_price));
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('original_price', '<=', (int) str_replace('.', '', $request->max_price));
            })
            ->pluck('product_id')
            ->toArray();

        // Lọc sản phẩm không có biến thể, dùng giá từ bảng products
        $nonVariantProductIds = Product::whereIn('id', $allProductIds)
            ->whereNotIn('id', DB::table('product_variants')->select('product_id'))
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('original_price', '>=', (int) str_replace('.', '', $request->min_price));
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('original_price', '<=', (int) str_replace('.', '', $request->max_price));
            })
            ->pluck('id')
            ->toArray();

        // Tổng hợp danh sách sản phẩm sau khi lọc theo giá
        $filteredProductIds = array_unique(array_merge($variantProductIds, $nonVariantProductIds));
        $query = Product::whereIn('id', $filteredProductIds)
            ->when($request->filled('brand_id'), function ($q) use ($request) {
                $brandIds = is_array($request->brand_id)
                    ? $request->brand_id
                    : [$request->brand_id]; // ép thành mảng nếu là chuỗi
                $q->whereIn('brand_id', $brandIds);
            })
            ->latest();

        $products = $query->paginate(12);

        $brandIds = (clone $query)->pluck('brand_id')->unique()->filter()->values();
        $brands = Brand::whereIn('id', $brandIds)->get();

        $productCountsByBrand = Product::whereIn('id', $filteredProductIds)
            ->selectRaw('brand_id, COUNT(*) as total')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        $bestSellingProducts = Product::with('category')
            ->select('products.*', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('order_details', 'order_details.product_id', '=', 'products.id')
            ->groupBy('order_details.product_id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();

        // Tính min/max giá từ bảng product_variants của tất cả sản phẩm trong category này
        $variantPrices = DB::table('product_variants')
            ->whereIn('product_id', $allProductIds)
            ->pluck('original_price');

        $variantPricesFiltered = DB::table('product_variants')
            ->whereIn('product_id', $filteredProductIds)
            ->pluck('original_price');

        $nonVariantPricesFiltered = Product::whereIn('id', $filteredProductIds)
            ->whereNotIn('id', DB::table('product_variants')->select('product_id'))
            ->pluck('original_price');

        // Kết hợp cả giá sản phẩm có biến thể và không có biến thể
        $allPrices = $variantPricesFiltered->merge($nonVariantPricesFiltered);

        // Gán min và max
        $priceMin = $allPrices->min() ?? 0;
        $priceMax = $allPrices->max() ?? 0;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.products', compact('products'))->render()
            ]);
        }

        return view('layouts.pages.guest.category', compact(
            'subCategoryIds',
            'products',
            'brands',
            'productCountsByBrand',
            'bestSellingProducts',
            'priceMin',
            'priceMax'
        ));
    }
}
