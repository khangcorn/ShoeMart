<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tên bảng nếu không phải bảng mặc định (products)
    protected $table = 'products';

    // Chỉ định khóa chính
    protected $primaryKey = 'product_id';

    public $incrementing = true; // Xác định rằng khóa chính là tự động tăng

    protected $keyType = 'int'; // Kiểu dữ liệu khóa chính là integer

    // Các cột có thể điền vào (fillable)
    protected $fillable = [
        'name',
        'description',
        'price',
        'price_sale',
        'stock',
        'category_id',
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
        return $this->hasMany(ProductVariant::class, 'product_id', 'product_id');
    }

    /**
     * Một sản phẩm có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

    /**
     * Một sản phẩm có nhiều thuộc tính (màu sắc, kích thước...).
     */
    public function attributes()
    {
        return $this->hasManyThrough(VariantAttribute::class, ProductVariant::class, 'product_id', 'variant_id', 'product_id', 'variant_id');
    }

    /**
     * Lấy ảnh chính của sản phẩm (loại 'main')
     */
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')->where('type', 'main');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'product_id');
    }

    public function orderReviews()
    {
        return $this->hasManyThrough(OrderReview::class, OrderDetail::class, 'product_id', 'order_detail_id', 'product_id', 'order_detail_id');
    }
    public function reviews()
    {
        return $this->hasMany(OrderReview::class, 'product_id', 'product_id');
    }

}
