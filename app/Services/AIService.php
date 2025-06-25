<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class AIService
{
    public static function chat($userMessage, $systemPrompt = '')
    {
        $client = new Client([
            'timeout' => 20,
            'connect_timeout' => 5,
        ]);

        try {
            return self::sendRequest($client, $userMessage, $systemPrompt);
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            if ($status === 429) {
                sleep(2); // nghỉ 2 giây trước khi thử lại
                try {
                    return self::sendRequest($client, $userMessage, $systemPrompt);
                } catch (\Exception $e2) {
                    Log::error('AIService retry failed (429): ' . $e2->getMessage());
                    return 'Hệ thống đang quá tải. Vui lòng thử lại sau.';
                }
            }

            Log::error('AIService failed: ' . $e->getMessage());
            return 'Đã xảy ra lỗi khi kết nối AI. Vui lòng thử lại sau.';
        } catch (\Exception $e) {
            Log::error('AIService unknown error: ' . $e->getMessage());
            return 'Hệ thống đang gặp sự cố. Xin vui lòng quay lại sau.';
        }
    }

    private static function sendRequest(Client $client, $userMessage, $systemPrompt)
    {
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.7,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['choices'][0]['message']['content'] ?? 'Xin lỗi, em chưa hiểu rõ câu hỏi của anh/chị.';
    }
}
