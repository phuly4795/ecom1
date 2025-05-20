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
        return view('layouts.pages.guest.product_detail', [
            'product' => $productDetail,
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => $productDetail->category->name, 'url' => route('category.show', $productDetail->category->slug)],
                ['name' => $productDetail->title, 'url' => null],
            ]
        ]);
    }
}
