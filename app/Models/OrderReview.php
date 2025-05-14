<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
    use HasFactory;

    // Cập nhật `$fillable` để bao gồm `product_id` và `variant_id`
    protected $fillable = ['order_id', 'user_id', 'rating', 'comment', 'media_paths', 'order_detail_id', 'product_id', 'variant_id'];

    // Cập nhật phần cast cho media_paths
    protected $casts = [
        'media_paths' => 'array',
    ];

    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với OrderDetail
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id', 'order_detail_id');
    }

    // Quan hệ với Product (Sản phẩm)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Quan hệ với ProductVariant (Biến thể sản phẩm)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    // Quan hệ với User (Người dùng)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
