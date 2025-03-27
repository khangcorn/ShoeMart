<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'variant_id'; // Đảm bảo đúng khóa chính là variant_id
    public $incrementing = true;  // Đảm bảo tự động tăng
    protected $keyType = 'int';  // Kiểu dữ liệu của variant_id là int
    protected $fillable = ['variant_id', 'product_id', 'price', 'stock', 'size', 'color'];

    // Quan hệ với bảng Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'variant_id', 'variant_id');
    }

    // Quan hệ với bảng VariantAttribute thông qua bảng variant_attribute_values
    public function attributes()
    {
        return $this->variantAttributeValues()->with('variantAttribute');
    }

    // Quan hệ với bảng ProductImage (Mỗi biến thể có nhiều ảnh)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id', 'variant_id');
    }
}
