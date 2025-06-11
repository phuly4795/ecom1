<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request, $slug)
    {
        $category = Category::with('subCategories')->where('slug', $slug)->firstOrFail();
        $subCategoryIds = $category->subCategories->pluck('id');

        $query = Product::where(function ($q1) use ($category, $subCategoryIds) {
            $q1->whereIn('subcategory_id', $subCategoryIds)
                ->orWhere(function ($q2) use ($category) {
                    $q2->where('category_id', $category->id)
                        ->whereNull('subcategory_id');
                });
        })->latest();

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('brand_id')) {
            $query->whereIn('brand_id', $request->brand_id);
        }
        $brandIds = (clone $query)->pluck('brand_id')->unique()->filter()->values();
        $brands = Brand::whereIn('id', $brandIds)->get();

        $productCountsByBrand = Product::where(function ($q1) use ($category, $subCategoryIds) {
            $q1->whereIn('subcategory_id', $subCategoryIds)
                ->orWhere(function ($q2) use ($category) {
                    $q2->where('category_id', $category->id)
                        ->whereNull('subcategory_id');
                });
        })
            ->selectRaw('brand_id, COUNT(*) as total')
            ->groupBy('brand_id')
            ->pluck('total', 'brand_id');

        $products = $query->paginate(12);

        $bestSellingProducts = Product::with('category')
            ->select('products.*', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->join('order_details', 'order_details.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(3)
            ->get();
        $priceMin = (clone $query)->min('original_price');
        $priceMax = (clone $query)->max('original_price');

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.products', compact('products'))->render()
            ]);
        }
        return view('layouts.pages.guest.category', compact('subCategoryIds', 'products', 'brands', 'productCountsByBrand', 'bestSellingProducts', 'priceMin', 'priceMax'));
    }
}
