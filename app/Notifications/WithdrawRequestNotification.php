<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WithdrawRequestNotification extends Notification
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // Gửi thông báo vào database
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'message' => $this->message,
        ]);
    }
}
