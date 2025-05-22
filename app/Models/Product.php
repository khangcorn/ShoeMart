<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Chỉ định tên bảng (không bắt buộc nếu Laravel có thể tự động xác định)
    protected $table = 'products';

<<<<<<< HEAD
    // Chỉ định khóa chính nếu không phải 'id'
    protected $primaryKey = 'id';
=======
    // Chỉ định khóa chính
    protected $primaryKey = 'product_id';

    public $incrementing = true; // Xác định rằng khóa chính là tự động tăng

    protected $keyType = 'int'; // Kiểu dữ liệu khóa chính là integer
>>>>>>> 1bbab0a (Full code DATN)

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
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Một sản phẩm có nhiều biến thể.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id'); // product_id in product_variants table, id in products table
    }

    /**
     * Một sản phẩm có nhiều hình ảnh.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }

<<<<<<< HEAD
=======
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
>>>>>>> 1bbab0a (Full code DATN)

}
