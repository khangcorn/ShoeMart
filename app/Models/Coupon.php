<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    protected $primaryKey = 'coupon_id';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'apply_to', 
        'discount_type',
        'discount_value',
        'max_discount_value',
        'expiration_date',
        'usage_limit',
        'min_order_value',
        'usage_count',
        'status'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'expiration_date' => 'date',
        'usage_count' => 'integer',
        'usage_limit' => 'integer',
        'status' => 'string',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(OrderCoupon::class, 'coupon_id');
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && Carbon::now()->gt($this->expiration_date);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }
}
