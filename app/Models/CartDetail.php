<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    use HasFactory;

    // Tên bảng tương ứng
    protected $table = 'cart_details';

    // Các cột có thể gán giá trị (Mass Assignable)
    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'created_at',
        'updated_at',
    ];

    // Hoặc có thể dùng protected $guarded nếu không muốn cho phép gán giá trị cho các cột
    // protected $guarded = ['cart_detail_id'];

    // Mối quan hệ với bảng Cart
    // public function cart()
    // {
    //     return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    // }

    // // Mối quan hệ với bảng Product
    // public function product()
    // {
    //     return $this->belongsTo(Product::class, 'product_id', 'product_id');
        
    // }

    // // Mối quan hệ với bảng ProductVariant (nếu có variant)
    // public function variant()
    // {
    //     return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    // }
}
