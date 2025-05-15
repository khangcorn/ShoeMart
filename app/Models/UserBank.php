<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    use HasFactory;

    protected $table = 'user_banks';
    protected $fillable = ['user_id', 'bank_name', 'account_number'];

    // Quan hệ với bảng users
public function users()
{
    return $this->belongsToMany(User::class, 'user_banks', 'bank_id', 'user_id');
}

}
