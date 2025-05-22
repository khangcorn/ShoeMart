<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

<<<<<<< HEAD
    // Chỉ định tên bảng (không bắt buộc nếu Laravel có thể tự động xác định)
    protected $table = 'categories';

    // Cho phép các cột có thể gán dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = [
        'name',
        'parent_id'
    ];
=======
    protected $primaryKey = 'category_id';

    // Thêm image_url vào mảng $fillable
    protected $fillable = ['name', 'description', 'description', 'parent_id', 'image_url'];
>>>>>>> 1bbab0a (Full code DATN)

    /**
     * Một danh mục có thể có nhiều danh mục con (quan hệ đệ quy).
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Một danh mục con thuộc về một danh mục cha.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Một danh mục có thể có nhiều sản phẩm.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
