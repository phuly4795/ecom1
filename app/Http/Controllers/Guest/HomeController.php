<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $product = Product::with('productImages')->get();
        return view('layouts.pages.dashboard', compact('categories', 'product'));
    }
}
