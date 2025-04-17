<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $primaryKey = 'refund_id';

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'amount',
        'reason',
        'note',
        'attachments',
        'approved_by',
    ];

    // ✅ Casts để định dạng đúng kiểu dữ liệu
    protected $casts = [
        'approved_at' => 'datetime',
        'attachments' => 'array', // Để xử lý JSON thành mảng
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
