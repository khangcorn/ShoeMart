<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'variant_id'; // Đảm bảo đúng khóa chính là variant_id
    public $incrementing = true;  // Đảm bảo tự động tăng
    protected $keyType = 'int';  // Kiểu dữ liệu của variant_id là int
    protected $fillable = ['product_id', 'price', 'price_sale', 'stock'];

   // In the ProductVariant model
public function products()
{
    return $this->belongsTo(Product::class, 'product_id', 'id');
}

    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class, 'variant_id', 'variant_id');
    }
    public function images()
{
    return $this->hasMany(ProductImage::class, 'variant_id');
}
}

