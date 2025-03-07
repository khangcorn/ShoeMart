<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Chỉ định tên bảng (không bắt buộc nếu Laravel có thể tự động xác định)
    protected $table = 'products';

    // Chỉ định khóa chính nếu không phải 'id'
    protected $primaryKey = 'product_id';
    public $incrementing = true; // Nếu không phải là auto-increment
    protected $keyType = 'int';
    

    // Cho phép các cột có thể gán dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = [
        'name',
        'description',
        'price',
        'price_sale',
        'stock',
        'category_id'
    ];

    /**
     * Một sản phẩm thuộc về một danh mục.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Một sản phẩm có nhiều biến thể.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id'); // product_id in product_variants table, id in products table
    }

    /**
     * Một sản phẩm có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }


}
