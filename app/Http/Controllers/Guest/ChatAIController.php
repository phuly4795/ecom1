<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatHistory;
use App\Models\Product;
use App\Models\SubCategory;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatAIController extends Controller
{
    public function handle(Request $request)
    {
        $userMessage = $request->input('message');
        $isSystem = $request->input('is_system', false);
        if (Auth::check()) {
            $userId = Auth::id();
        } else {
            $sessionId = $request->session()->getId(); // sử dụng session làm định danh
        }
        if (!empty($userMessage) && $isSystem == 'false') {
            // Lưu tin nhắn người dùng
            ChatHistory::create([
                'user_id' => $userId ?? null,
                'session_id' => $sessionId ?? null,
                'sender' => 'user',
                'message' => $userMessage
            ]);
        }

        // Gửi yêu cầu ban đầu cho AI để lấy câu trả lời (không có bảng lúc này)
        $systemPrompt = "Bạn là trợ lý AI bán hàng. Hãy trả lời khách hàng một cách tự nhiên, thân thiện.";
        $reply = GeminiService::chat($userMessage, $systemPrompt);

        // Gộp cả user + AI trả lời lại để dò keyword
        $combined = strtolower($userMessage . ' ' . $reply);

        // Danh sách keyword
        $keywords = Category::pluck('name')
            ->map(fn($name) => strtolower($name))
            ->sortByDesc(fn($name) => strlen($name)) // Ưu tiên dài
            ->values()
            ->toArray();
        $keywordSubs = SubCategory::pluck('name')
            ->map(fn($name) => strtolower($name))
            ->sortByDesc(fn($name) => strlen($name)) // Ưu tiên dài
            ->values()
            ->toArray();

        // Có thể thêm 1 số từ phổ biến
        $keywords = array_merge($keywords, ['iphone', 'samsung', 'ốp lưng', 'macbook', 'hp', 'điện thoại']);
        $keywords = array_merge($keywords, $keywordSubs);
        // 👉 Tìm từ khóa đầu tiên có xuất hiện
        $matchedKeyword = null;
        foreach ($keywords as $keyword) {
            if (str_contains($combined, $keyword)) {
                $matchedKeyword = $keyword;
                break;
            }
        }

        // Nếu tìm thấy từ khóa danh mục => lọc sản phẩm theo keyword
        if ($matchedKeyword) {
            $products = Product::where('title', 'LIKE', '%' . $matchedKeyword . '%')
                ->limit(10)
                ->get();

            // Nếu có sản phẩm => tạo bảng
            if ($products->count() > 0) {
                $tableHtml = '<table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead style="background:#f1f1f1;">
                      <tr>
                        <th style="text-align:left; padding:8px; border:1px solid #ccc;">Tên sản phẩm</th>
                        <th style="text-align:right; padding:8px; border:1px solid #ccc;">Giá (VND)</th>
                      </tr>
                    </thead>
                    <tbody>';
                $price = 0;
                foreach ($products as $product) {
                    $variant = $product->productVariants->first();
                    $displayItem = $variant ?? $product;
                    if ($displayItem->getIsOnSaleAttribute()) {
                        $price = $displayItem->getDisplayPriceAttribute();
                    } else {
                        $price = $displayItem->original_price;
                    }
                    $tableHtml .= '<tr>
                        <td style="padding:8px; border:1px solid #ccc;">
                            <a href="' . route('product.show', $product->slug) . '">' . $product->title . '</a>
                        </td>
                        <td style="padding:8px; border:1px solid #ccc; text-align:right;">' . number_format($price, 0, ',', '.') . '</td>
                    </tr>';
                }

                $tableHtml .= '</tbody></table>';

                // Gắn bảng sản phẩm vào cuối câu trả lời
                $reply .= "\n\nDưới đây là một số sản phẩm liên quan đến \"{$matchedKeyword}\":\n\n" . $tableHtml;
                ChatHistory::create([
                    'user_id' => $userId ?? null,
                    'session_id' => $sessionId ?? null,
                    'sender' => 'ai',
                    'message' => $reply
                ]);
            }
        }
        return response()->json(['reply' => $reply]);
    }

    public function history(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $history = ChatHistory::where('user_id', $userId)->latest()->limit(30)->get()->reverse()->values();
        } else {
            $sessionId = $request->session()->getId(); // sử dụng session làm định danh
            $history = ChatHistory::where('session_id', $sessionId)->latest()->limit(30)->get()->reverse()->values();
        }

        return response()->json($history);
    }

    public function destroy(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $chat = ChatHistory::where('user_id', $userId)->delete();
        } else {
            $sessionId = $request->session()->getId(); // sử dụng session làm định danh
            $chat = ChatHistory::where('session_id', $sessionId)->delete();
        }

        return response()->json($chat);
    }
}
