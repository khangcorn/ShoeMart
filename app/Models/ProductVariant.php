<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    // Quan hệ với bảng Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Quan hệ với bảng VariantAttribute (Mỗi biến thể có nhiều thuộc tính như màu sắc, kích thước)
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'variant_id', 'variant_id');
    }

    // Quan hệ với bảng ProductImage (Mỗi biến thể có nhiều ảnh)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id', 'variant_id');
    }
}

