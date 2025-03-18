<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;

    // Tên bảng (nếu tên bảng không theo chuẩn Laravel)
    protected $table = 'shipping_fees';

    // Chỉ định khóa chính là 'shipping_id'
    protected $primaryKey = 'shipping_id';  // Thêm dòng này để chỉ định khóa chính

    // Các thuộc tính có thể gán giá trị
    protected $fillable = [
        'province',
        'district',
        'ward',
        'fee',
    ];

    // Các thuộc tính kiểu ngày tháng
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    // Nếu khóa chính không tự động tăng (auto-increment), bạn cần khai báo
    public $incrementing = true; // Đảm bảo auto-increment vẫn được sử dụng
}
