<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Useraddress extends Model
{
    use HasFactory;
    protected $table = "user_addresses";
    protected $primaryKey = 'address_id';
    protected $fillable = ['user_id', 'city', 'district', 'ward', 'street_address', 'is_default'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
