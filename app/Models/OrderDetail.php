<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $primaryKey = 'order_detail_id';

    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
        'discount_amount',
        'subtotal',
        'total_price',
        'status',
        'cancel_reason',
        // ➕ Snapshot fields
        'product_name',
        'variant_name',
        'attributes',
        'original_price',
        'final_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    public function reviews()
    {
        return $this->hasOne(OrderReview::class, 'order_detail_id', 'order_detail_id');
    }
}
