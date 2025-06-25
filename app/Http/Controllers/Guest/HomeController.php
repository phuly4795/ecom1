<?php

namespace App\Http\Controllers\Guest;

use App\Events\NewContactMessage;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $collectionCategory = Category::orderBy('sort', 'asc')->take(3)->get();
        $categories = Category::orderBy('name', 'asc')->get();
        $productLatest = Product::with('productImages', 'favoritedByUsers')
            ->where('is_featured', 'yes')
            ->latest()
            ->take(5)
            ->get();

        $topSellingProducts = Product::with('category')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(9)
            ->get();

        $mostFavorited = Product::with('category')
            ->withCount('wishlists')
            ->orderByDesc('wishlists_count')
            ->take(9)
            ->get()
            ->chunk(3);

        $topRated = Product::with(['category', 'reviews'])
            ->get()
            ->sortByDesc(fn($p) => $p->reviews->avg('rating'))
            ->take(8)
            ->chunk(3);
        $featured = Product::where('is_featured', true)->take(9)->get()->chunk(3);
        return view('layouts.pages.guest.home', compact('collectionCategory', 'productLatest', 'categories', 'topSellingProducts', 'mostFavorited', 'topRated', 'featured'));
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'content' => 'required|string|max:255'
        ], [
            'content.max' => "Nội dung tối đa chỉ 255 ký tự"
        ]);

        $contact = Contact::create($request->all());

        event(new NewContactMessage($contact));
        return back()->with(['status' => 'success', 'message' => 'Đánh giá của bạn đã được gửi thành công!']);
    }
}
