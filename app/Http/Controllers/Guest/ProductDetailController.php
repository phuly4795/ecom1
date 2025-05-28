<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function index($slug)
    {
        $productDetail = Product::with('productImages')->where('slug', $slug)->first();
        $productLastest = Product::with('productImages')->latest()->get()->take(4);
        $mainCategory = $productDetail->subCategory->categories->first();

        return view('layouts.pages.guest.product_detail', [
            'product' => $productDetail,
            'productLastest' => $productLastest,
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => $mainCategory->name, 'url' => route('category.show', $mainCategory->slug)],
                ['name' => $productDetail->title, 'url' => null],
            ]
        ]);
    }
}
