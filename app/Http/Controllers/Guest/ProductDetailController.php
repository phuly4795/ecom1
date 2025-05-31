<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function index($slug)
    {
        $productDetail = Product::with(['productImages', 'brand', 'reviews'])->where('slug', $slug)->firstOrFail();
        $productLastest = Product::with('productImages')->latest()->get()->take(4);
        $mainCategory = $productDetail->subCategory->categories->first();

        // Tính toán thông tin đánh giá
        $averageRating = $productDetail->reviews->avg('rating') ?? 0;
        $ratings = array_fill(1, 5, 0);
        foreach ($productDetail->reviews as $review) {
            $ratings[(int)$review->rating] = ($ratings[(int)$review->rating] ?? 0) + 1;
        }
        foreach ($ratings as $key => &$value) {
            $value = $productDetail->reviews->count() > 0 ? round(($value / $productDetail->reviews->count()) * 100) : 0;
        }
        unset($value); // Unset reference để tránh lỗi

        return view('layouts.pages.guest.product_detail', [
            'product' => $productDetail,
            'productLastest' => $productLastest,
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => $mainCategory->name, 'url' => route('category.show', $mainCategory->slug)],
                ['name' => $productDetail->title, 'url' => null],
            ],
            'averageRating' => $averageRating,
            'ratings' => $ratings,
        ]);
    }
}
