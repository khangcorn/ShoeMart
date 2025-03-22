<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'variant_id'; // Đảm bảo đúng khóa chính là variant_id
    public $incrementing = true;  // Đảm bảo tự động tăng
    protected $keyType = 'int';  // Kiểu dữ liệu của variant_id là int
    protected $fillable = ['product_id', 'price', 'price_sale', 'stock'];

    // Quan hệ với bảng Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Quan hệ với bảng VariantAttribute (Mỗi biến thể có nhiều thuộc tính như màu sắc, kích thước)
    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }

    // Quan hệ với bảng ProductImage (Mỗi biến thể có nhiều ảnh)
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'variant_id', 'variant_id');
    }
}

