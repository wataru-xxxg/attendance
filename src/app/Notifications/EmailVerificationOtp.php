<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtp extends Notification
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('メール認証コード')
            ->greeting('こんにちは！')
            ->line('メール認証のための確認コードは以下の通りです：')
            ->line('**' . $this->otp . '**')
            ->line('このコードは10分間有効です。')
            ->line('このメールに心当たりがない場合は、無視してください。');
    }
}
