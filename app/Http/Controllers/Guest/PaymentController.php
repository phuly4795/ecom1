<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    /**
     * Tạo đơn hàng trên PayPal (nếu bạn cần gọi phía server — có thể không dùng nếu frontend tự tạo).
     */
    public function createPayment()
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "10.00"
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('paypal.cancel'),
            ]
        ]);

        if (isset($response['id'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('home')->with('error', 'Không thể khởi tạo thanh toán PayPal.');
    }

    /**
     * ✅ PayPal callback sau khi thanh toán thành công.
     * Frontend gửi AJAX từ JS (onApprove) sang đây.
     */
    public function success(Request $request)
    {
        Log::info('PayPal success callback', $request->all());

        if ($this->verifyPayment($request)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    protected function verifyPayment(Request $request)
    {
        $orderId = $request->input('orderID');
        if (!$orderId) {
            return false;
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $paypalOrder = $provider->showOrderDetails($orderId);

            if (isset($paypalOrder['status']) && $paypalOrder['status'] === 'COMPLETED') {
                return true;
            }
        } catch (\Exception $e) {
            Log::error('PayPal verify error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * (Tuỳ chọn) Nếu người dùng hủy trên PayPal
     */
    public function cancel()
    {
        return redirect()->route('checkout.failed')->with('error', 'Thanh toán qua PayPal đã bị hủy.');
    }
}
