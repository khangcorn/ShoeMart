<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefundRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'refund_id';

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'reason',
        'attachments',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'approved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    // Trong model RefundRequest


}
