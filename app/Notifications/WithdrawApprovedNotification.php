<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WithdrawApprovedNotification extends Notification
{
    protected $withdraw;

    public function __construct($withdraw)
    {
        $this->withdraw = $withdraw;
    }

    public function via($notifiable)
    {
        return ['database']; // Sử dụng database để lưu thông báo
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Yêu cầu rút tiền của bạn đã được chấp nhận. Số tiền đã được trừ khỏi ví.',
            'withdraw_id' => $this->withdraw->id,
            'amount' => $this->withdraw->amount,
        ];
    }
}
