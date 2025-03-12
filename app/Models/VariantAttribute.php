<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    // Đảm bảo đúng khóa chính
    protected $primaryKey = 'attribute_id';
    public $timestamps = true;

    // Các trường có thể điền vào (fillable)
    protected $fillable = ['variant_id', 'attribute_name'];  // Bỏ attribute_value vì giá trị này sẽ được lưu trong VariantAttributeValue

    /**
     * Quan hệ với bảng ProductVariant.
     * Một thuộc tính thuộc về một biến thể sản phẩm
     */
    public function variant()
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_id');
    }

    /**
     * Quan hệ với bảng VariantAttributeValue.
     * Một thuộc tính có thể có nhiều giá trị (màu sắc, kích thước, ...)
     */
    public function attributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_id', 'attribute_id');
    }
}
