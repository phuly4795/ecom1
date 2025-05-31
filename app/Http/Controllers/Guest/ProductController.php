<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function storeReview(Request $request, $productId)
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|email',
            'comment' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        $product = Product::findOrFail($productId);
        $product->reviews()->create($request->all());

        return redirect()->back()->with('success', 'Đánh giá của bạn đã được gửi thành công!');
    }
}
