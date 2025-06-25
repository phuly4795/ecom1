<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public static function chat($userMessage, $systemPrompt = '')
    {
        $client = new Client([
            'timeout' => 20,
            'connect_timeout' => 5,
        ]);

        try {
            $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
                'query' => ['key' => env('GEMINI_API_KEY')],
                'json' => [
                    'contents' => [
                        ['parts' => [['text' => $systemPrompt . "\n\n" . $userMessage]]],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể phản hồi lúc này.';
        } catch (\Exception $e) {
            Log::error('GeminiService error: ' . $e->getMessage());
            return 'Hệ thống gặp lỗi. Vui lòng thử lại sau.';
        }
    }
}
