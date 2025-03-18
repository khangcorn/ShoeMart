<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // Tên bảng (nếu tên bảng không theo chuẩn Laravel)
    protected $table = 'coupons';

    // Chỉ định khóa chính là 'coupon_id'
    protected $primaryKey = 'coupon_id';  // Thêm dòng này để chỉ định khóa chính là 'coupon_id'

    // Các thuộc tính có thể gán giá trị
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'expiration_date',
        'usage_limit',
        'usage_count',
        'status',
    ];

    // Các thuộc tính kiểu ngày tháng
    protected $dates = [
        'expiration_date',
        'created_at',
        'updated_at',
    ];

    // Nếu khóa chính không tự động tăng (auto-increment), bạn cần khai báo
    public $incrementing = true; // Đảm bảo auto-increment vẫn được sử dụng
}
