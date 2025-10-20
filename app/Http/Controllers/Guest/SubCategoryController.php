<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function show(Request $request, $slug)
    {
        $subCategory = SubCategory::with('categories', 'products')
            ->where('slug', $slug)
            ->firstOrFail();

        // Danh sách sản phẩm gốc
        $baseProducts = $subCategory->products();
        $productIds = $baseProducts->pluck('products.id')->toArray();

        // Lọc sản phẩm có biến thể thỏa điều kiện giá
        $variantFilteredIds = DB::table('product_variants')
            ->select('product_id')
            ->whereIn('product_id', $productIds)
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('original_price', '>=', (int) str_replace('.', '', $request->min_price));
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('original_price', '<=', (int) str_replace('.', '', $request->max_price));
            })
            ->pluck('product_id')
            ->toArray();

        // Lọc sản phẩm không có biến thể thỏa điều kiện giá
        $nonVariantFilteredIds = DB::table('products')
            ->whereIn('id', $productIds)
            ->whereNotIn('id', DB::table('product_variants')->select('product_id'))
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('original_price', '>=', (int) str_replace('.', '', $request->min_price));
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('original_price', '<=', (int) str_replace('.', '', $request->max_price));
            })
            ->pluck('id')
            ->toArray();

        // Gộp các ID sản phẩm hợp lệ
        $filteredProductIds = array_unique(array_merge($variantFilteredIds, $nonVariantFilteredIds));

        // Nếu không có sản phẩm thỏa mãn điều kiện giá
        $page = $request->input('page', 1);
        $perPage = 12;

        if (empty($filteredProductIds)) {
            $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        } else {
            $query = $subCategory->products()
                ->whereIn('products.id', $filteredProductIds)
                ->when($request->filled('brand_id'), function ($q) use ($request) {
                    $q->whereIn('brand_id', $request->brand_id);
                })
                ->orderBy('created_at', 'desc');

            $products = $query->paginate($perPage);
        }

        // Lấy danh sách thương hiệu từ các sản phẩm đã lọc
        $brandIds = $products instanceof \Illuminate\Pagination\LengthAwarePaginator
            ? $products->pluck('brand_id')->unique()->filter()->values()
            : [];

        $brands = Brand::whereIn('id', $brandIds)->get();
        
        $bestSellingProductId = Product::select('products.id', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('order_details', 'order_details.product_id', '=', 'products.id')
            // ->join('sub_categories', 'sub_categories.id', '=', 'products.subcategory_id')
            ->whereHas('subCategory', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })
            ->groupBy('order_details.product_id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->pluck('id')->toArray();

        $bestSellingProducts = Product::whereIn('id', $bestSellingProductId)
            ->with(['category', 'subCategory'])
            ->get();

        $productCountsByBrand = Product::where('subcategory_id', $subCategory->id)
            ->selectRaw('brand_id, COUNT(*) as total')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        // Lấy min/max từ tất cả product_variants của subcategory
        $variantPrices = DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.subcategory_id', $subCategory->id)
            ->pluck('product_variants.original_price');

        $productPrices = DB::table('products')
            ->where('subcategory_id', $subCategory->id)
            ->whereNotIn('id', DB::table('product_variants')->select('product_id'))
            ->pluck('original_price');

        $variantPrices = DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.subcategory_id', $subCategory->id)
            ->pluck('product_variants.original_price');

        $allPrices = $productPrices->merge($variantPrices);

        $priceMin = $allPrices->min() ?? 0;
        $priceMax = $allPrices->max() ?? 0;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.products', compact('products'))->render()
            ]);
        }

        return view('layouts.pages.guest.category', compact(
            'subCategory',
            'products',
            'brands',
            'bestSellingProducts',
            'productCountsByBrand',
            'priceMin',
            'priceMax'
        ));
    }
}
