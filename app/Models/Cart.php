<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Tên bảng tương ứng
    protected $table = 'carts';

    // Các cột có thể gán giá trị (Mass Assignable)
    protected $fillable = [
        'user_id',
        'session_id',
        'created_at',
        'updated_at',
    ];

    // Hoặc có thể dùng protected $guarded nếu không muốn cho phép gán giá trị cho các cột
    // protected $guarded = ['cart_id'];

    // Nếu bạn muốn làm việc với các trường timestamp tự động
    public $timestamps = true;

    // Mối quan hệ với User (nếu có)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}

