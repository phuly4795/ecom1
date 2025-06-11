<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    public function show($slug)
    {
        $subCategory = SubCategory::with('categories', 'products')
            ->where('slug', $slug)
            ->firstOrFail();

        $products = $subCategory->products()
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        $brandIds = $products->pluck('brand_id')->unique()->filter()->values();

        $brands = Brand::whereIn('id', $brandIds)->get();
        $bestSellingProducts = Product::with('category')
            ->select('products.*', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('order_details', 'order_details.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();

        $productCountsByBrand = Product::where('subcategory_id', $subCategory->id)
            ->selectRaw('brand_id, COUNT(*) as total')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        $variantPrices = DB::table('product_variants')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.subcategory_id', $subCategory->id)
            ->pluck('product_variants.original_price');

        $priceMin = $variantPrices->min();
        $priceMax = $variantPrices->max();

        return view('layouts.pages.guest.category', compact('subCategory', 'products', 'brands', 'bestSellingProducts', 'productCountsByBrand', 'priceMin', 'priceMax'));
    }
}
