<?php

// app/Models/Coupon.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    protected $primaryKey = 'coupon_id';
    public $timestamps = true;

    protected $fillable = ['code', 'discount_type', 'discount_value', 'expiration_date', 'usage_limit', 'usage_count'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }
}