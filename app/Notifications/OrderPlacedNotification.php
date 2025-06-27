<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Xác nhận đặt hàng #' . $this->order->order_code)
            ->greeting('Xin chào ' . $this->order->billing_full_name . '!')
            ->line('Cảm ơn bạn đã đặt hàng tại Electro.')
            ->line('Mã đơn hàng: ' . $this->order->order_code)
            ->line('Tổng tiền: ' . number_format($this->order->total_amount) . ' VNĐ')
            ->line('Phương thức thanh toán: ' . ($this->order->payment_method == 'cash' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản'))
            ->action('Xem đơn hàng', url('/my-account?tab=orders'))
            ->line('Nếu bạn có thắc mắc, hãy liên hệ với chúng tôi.')
            ->salutation('Trân trọng, Electro');
    }
}
