<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $primaryKey = 'coupon_id';

    protected $fillable = ['code', 'discount_type', 'discount_value', 'max_discount_value', 'expiration_date', 'usage_limit', 'usage_count', 'status'];

    public function orderCoupons(): HasMany
    {
        return $this->hasMany(OrderCoupon::class, 'coupon_id');
    }
}
