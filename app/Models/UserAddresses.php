<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddresses extends Model
{
    use HasFactory;

    protected $table = 'user_addresses'; // Đảm bảo đúng tên bảng

    protected $primaryKey = 'address_id'; // Khóa chính là address_id, không phải id

    public $incrementing = true; // address_id là auto-increment

    protected $keyType = 'int'; // Kiểu dữ liệu của khóa chính

    protected $fillable = [
        'user_id', 'address_name', 'recipient_name', 'recipient_phone',
        'city', 'district', 'ward', 'street_address', 'is_default',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                    ->where('address_id', '!=', $address->address_id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Liên kết đến User thông qua user_id
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }
}
