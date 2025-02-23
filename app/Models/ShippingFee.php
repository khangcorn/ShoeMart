<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;

    // Tên bảng (nếu tên bảng không theo chuẩn Laravel)
    protected $table = 'shipping_fees';

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
}

