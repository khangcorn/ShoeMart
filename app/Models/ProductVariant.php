<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants'; // Tên bảng trong database
    protected $primaryKey = 'variant_id'; // Khóa chính của bảng

    protected $fillable = [
        'product_id',
        'price',
        'price_sale',
        'stock',
    ];

    public $timestamps = true; // Tự động quản lý `created_at` và `updated_at`

    /**
     * Quan hệ với bảng `products`
     * Một biến thể thuộc về một sản phẩm
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
