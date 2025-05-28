<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function show($slug)
    {
        $subCategory = SubCategory::with('categories')
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        // Lấy danh sách sản phẩm thuộc danh mục phụ (giả sử có quan hệ với products)
        $products = $subCategory->products; // Cần định nghĩa quan hệ trong model nếu có

        return view('layouts.pages.guest.category', compact('subCategory', 'products'));
    }
}
