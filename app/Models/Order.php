<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'address_id',
        'status_id',
        'shipping_id',
        'coupon_id',
        'order_code',
        'total',
        'shipping_fee',
        'shipping_discount',
        'discount_amount',
        'total_price',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id',);
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingFee::class, 'shipping_id', 'shipping_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
    public function userAddresses()
    {
        return $this->belongsTo(UserAddresses::class, 'address_id', 'address_id');
    }
    // Order.php
public function orderCoupons()
{
    return $this->hasMany(\App\Models\OrderCoupon::class, 'order_id', 'order_id');
}


public function refund()
{
    return $this->hasOne(Refund::class, 'order_id', 'order_id');

// Trong model Order
public function returnRequest()
{
    return $this->hasOne(RefundRequest::class, 'order_id', 'order_id');

}


}
}
