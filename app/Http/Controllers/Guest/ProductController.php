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
    public function search(Request $request)
    {
        $query = $request->query('query');

        $selectCategory = $request->input('category');

        $products = Product::where('title', 'like', "%$query%")
            ->when($request->filled('category'), function ($q) use ($selectCategory) {
                $q->where('category_id', $selectCategory);
            })
            ->limit(10)
            ->get();

        return response()->json([
            'items' => $products->map(function ($item) {
                $image = $item->productImages->where('type', 1)->first()->image ?? '';
                $imagePath = $image
                    ? asset('storage/' . $image)
                    : asset('asset/img/no-image.png');

                $variant = $item->productVariants->first();
                $displayItem = $variant ?? $item;

                $newFinalPrice = $displayItem->is_on_sale ? $displayItem->display_price : $displayItem->original_price;
                return [
                    'image' => $imagePath,
                    'name' => $item->title,
                    'route' => route('product.show', ['slug' => $item->slug]),
                    'variant_name' => $variant ?? "-",
                    'original_price' => number_format($displayItem->original_price) . ' vnđ',
                    'is_on_sale' => $displayItem->is_on_sale,
                    'price' => number_format($newFinalPrice) . ' vnđ',
                    'discount_percentage' => $displayItem->discount_percentage
                ];
            })
        ]);
    }
}
