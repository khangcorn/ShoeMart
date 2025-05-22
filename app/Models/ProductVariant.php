<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'variant_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['variant_id', 'product_id', 'price', 'price_sale', 'stock', 'size', 'color'];

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
    public function variantAttributes()
{
    return $this->belongsToMany(
        VariantAttribute::class,
        'variant_attribute_values', // bảng trung gian
        'variant_id',               // khóa ngoại trong bảng trung gian
        'attribute_id',             // khóa chính của bảng attributes
        'variant_id',               // khóa chính của bảng hiện tại (product_variants)
        'attribute_id'              // khóa chính của bảng attributes
    );
}
}
