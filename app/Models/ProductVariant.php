<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
<<<<<<< HEAD
    protected $primaryKey = 'variant_id'; // Đảm bảo đúng khóa chính là variant_id
    public $incrementing = true;  // Đảm bảo tự động tăng
    protected $keyType = 'int';  // Kiểu dữ liệu của variant_id là int
    protected $fillable = ['product_id', 'price', 'price_sale', 'stock'];

   // In the ProductVariant model
public function products()
{
    return $this->belongsTo(Product::class, 'product_id', 'id');
}
=======
    protected $primaryKey = 'variant_id';

    public $incrementing = true;

    protected $keyType = 'int';
>>>>>>> 1bbab0a (Full code DATN)

    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class, 'variant_id', 'variant_id');
    }
    public function images()
<<<<<<< HEAD
{
    return $this->hasMany(ProductImage::class, 'variant_id');
=======
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
>>>>>>> 1bbab0a (Full code DATN)
}
}

