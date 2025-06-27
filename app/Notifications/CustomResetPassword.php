<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Khôi phục mật khẩu tài khoản Electro')
            ->greeting('Xin chào!')
            ->line('Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.')
            ->action('Đặt lại mật khẩu', $url)
            ->line('Liên kết đặt lại mật khẩu sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không yêu cầu, hãy bỏ qua email này.')
            ->salutation('Trân trọng, Electro Team');
    }
}
