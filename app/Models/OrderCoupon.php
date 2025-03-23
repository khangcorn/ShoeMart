<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCoupon extends Model
{
    use HasFactory;

    protected $table = 'order_coupons';
    protected $primaryKey = 'order_coupon_id';
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'coupon_id',
        'applied_amount',
        'created_at',
    ];

    /**
     * Get the order that owns the coupon.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the coupon that is applied to the order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }
}