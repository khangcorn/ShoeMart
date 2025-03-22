<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    // Đảm bảo rằng tên bảng là 'variant_attributes'
    protected $table = 'variant_attributes';

    // Các trường có thể điền vào
    protected $fillable = ['variant_id', 'attribute_name', 'attribute_value'];

    /**
     * Quan hệ với bảng ProductVariant.
     * Một thuộc tính thuộc về một biến thể sản phẩm
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
