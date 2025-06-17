<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\FavoriteProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteProductController extends Controller
{
    // Danh sách sản phẩm yêu thích
    public function index()
    {
        $favorites = FavoriteProduct::with(['products', 'productVariants', 'products.productImages'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('layouts.pages.guest.favorites', compact('favorites'));
    }

    // Thêm / Bỏ yêu thích
    public function toggle(Product $product)
    {
        $user = Auth::user();

        // Nếu chưa đăng nhập
        if (!$user) {
            if (request()->ajax()) {
                return response()->json(['status' => 'unauthenticated'], 401);
            }
            return redirect()->route('login')->withErrors('Bạn cần đăng nhập');
        }

        $variantId = request()->input('variant_id', null);

        $productFavorite = FavoriteProduct::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id);

        // if ($variantId) {
        //     $productFavorite->where('product_variant_id', $variantId);
        // }

        $productFavorite = $productFavorite->first();

        if ($productFavorite) {
            $productFavorite->delete();

            if (request()->expectsJson()) {
                return response()->json(['status' => 'removed']);
            }

        } else {
            FavoriteProduct::create([
                'user_id'            => $user->id,
                'product_id'         => $product->id,
                // 'product_variant_id' => $variantId,
            ]);

            if (request()->expectsJson()) {
                return response()->json(['status' => 'added']);
            }

        }
    }
}
