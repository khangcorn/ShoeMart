<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WithdrawRejectedNotification extends Notification
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
            'message' => 'Yêu cầu rút tiền của bạn đã bị từ chối.',
            'withdraw_id' => $this->withdraw->id,
            'amount' => $this->withdraw->amount,
        ];
    }
}
