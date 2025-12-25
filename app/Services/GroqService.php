<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GroqService
{
    public static function chat($userMessage, $systemPrompt = '')
    {
        $client = new Client([
            'timeout' => 30,
        ]);

        try {
            $response = $client->post(
                'https://api.groq.com/openai/v1/chat/completions',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => 'llama-3.1-8b-instant',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemPrompt ?: 'Bạn là trợ lý bán hàng thân thiện.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $userMessage
                            ]
                        ],
                        'temperature' => 0.7,
                    ],
                ]
            );

            $data = json_decode($response->getBody(), true);

            return $data['choices'][0]['message']['content']
                ?? 'Không có phản hồi từ AI';

        } catch (\Throwable $e) {
            Log::error('AI error: ' . $e->getMessage());
            return 'Hệ thống AI đang bận, vui lòng thử lại sau.';
        }
    }
}
