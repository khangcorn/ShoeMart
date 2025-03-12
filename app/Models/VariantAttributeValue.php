<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    // Tên bảng tương ứng trong cơ sở dữ liệu (Nếu bảng tên khác mặc định là plural của tên model)
    protected $table = 'variant_attribute_values';

    // Khai báo khóa chính cho bảng này
    protected $primaryKey = 'value_id';

    // Đảm bảo tự động tăng cho khóa chính
    public $incrementing = true;

    // Kiểu dữ liệu của khóa chính
    protected $keyType = 'int';

    // Các thuộc tính có thể điền vào (fillable)
    protected $fillable = ['variant_id', 'attribute_id', 'attribute_value', 'stock'];

    /**
     * Quan hệ với bảng ProductVariant
     * Một giá trị thuộc tính thuộc về một biến thể sản phẩm
     */
    public function variants()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    /**
     * Quan hệ với bảng VariantAttribute
     * Một giá trị thuộc tính thuộc về một thuộc tính (màu sắc, kích thước, ...)
     */
    public function variantAttribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'attribute_id', 'attribute_id');
    }
}
