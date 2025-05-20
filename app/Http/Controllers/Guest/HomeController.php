<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $collectionCategory = Category::orderBy('sort', 'asc')->take(3)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $productLatest = Product::with('productImages')
            ->latest()
            ->take(5)
            ->get();
        return view('layouts.pages.guest.home', compact('collectionCategory', 'productLatest', 'categories'));
    }
}
