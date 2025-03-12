<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model {
    use HasFactory;

    protected $table = 'user_addresses';
    protected $primaryKey = 'address_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id', 
        'recipient_name', 
        'recipient_phone', 
        'recipient_email', 
        'province', 
        'district', 
        'ward', 
        'street_address', 
        'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
