<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProductDetailController extends Controller
{
    public function show($slug, $variant = null)
    {
        $productDetail = Product::with(['productImages', 'brand', 'reviews', 'productVariants'])->where('slug', $slug)->firstOrFail();
        $productLastest = Product::with('productImages')->latest()->get()->take(4);
        $categoryParent = $productDetail->category;
        $categoryChild = $productDetail->subCategory;

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

        $selectedVariant = null;
        $isDiscountActive = false;
        if ($variant) {
            $selectedVariant = $productDetail->productVariants->firstWhere('variant_name', str_replace('-', ' ', $variant));
            if ($selectedVariant) {
                $currentDate = Carbon::now();
                $startDate = $selectedVariant->discount_start_date ? Carbon::parse($selectedVariant->discount_start_date) : null;
                $endDate = $selectedVariant->discount_end_date ? Carbon::parse($selectedVariant->discount_end_date) : null;
                $isDiscountActive = $selectedVariant->discounted_price && $startDate && $endDate && $currentDate->between($startDate, $endDate);
            }
        }


        $reviews = Review::where('product_id', $productDetail->id)
            ->orderByDesc('created_at')
            ->paginate(5); // mỗi trang 5 review


        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => $categoryParent->name, 'url' => route('category.show', $categoryParent->slug)],
        ];

        if ($categoryChild) {
            $breadcrumbs[] = ['name' => $categoryChild->name, 'url' => route('subcategory.show', $categoryChild->slug)];
        }

        $breadcrumbs[] = ['name' => $productDetail->title, 'url' => null];

        return view('layouts.pages.guest.product_detail', [
            'product' => $productDetail,
            'productLastest' => $productLastest,
            'breadcrumbs' => $breadcrumbs,
            'averageRating' => $averageRating,
            'ratings' => $ratings,
            'selectedVariant' => $selectedVariant,
            'isDiscountActive' => $isDiscountActive,
            'reviews' => $reviews
        ]);
    }
}
