<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model {
    use HasFactory;

    protected $primaryKey = 'address_id';
    protected $fillable = ['user_id', 'province', 'district', 'ward', 'street_address', 'is_default'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
