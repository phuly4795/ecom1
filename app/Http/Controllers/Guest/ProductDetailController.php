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
        return view('layouts.pages.guest.product_detail', [
            'product' => $productDetail,
            'productLastest' => $productLastest,
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('home')],
                ['name' => $productDetail->category->name, 'url' => route('category.show', $productDetail->category->slug)],
                ['name' => $productDetail->title, 'url' => null],
            ]
        ]);
    }
}
