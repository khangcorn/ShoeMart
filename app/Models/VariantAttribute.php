<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    // Chỉ định khóa chính là attribute_id thay vì id
    protected $primaryKey = 'attribute_id';

    // Nếu khóa chính không tự động tăng, bạn có thể thiết lập
    public $incrementing = false;

    // Kiểu dữ liệu của khóa chính nếu cần
    protected $keyType = 'int';

    public $timestamps = false;

    // Các thuộc tính có thể gán hàng loạt (nếu cần)
    protected $fillable = ['attribute_name', 'attribute_value'];

    // Quan hệ ngược lại với bảng VariantAttributeValue
    public function variantAttributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_id', 'attribute_id');
    }

    public function attributes()
    {
        return $this->hasManyThrough(
            VariantAttribute::class,
            VariantAttributeValue::class,
            'variant_id', // Foreign key ở variant_attribute_values
            'attribute_id', // Foreign key ở variant_attributes
            'variant_id', // Local key ở product_variants
            'attribute_id' // Local key ở variant_attribute_values
        );
    }
}
