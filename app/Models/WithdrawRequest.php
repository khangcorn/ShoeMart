<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/WithdrawRequest.php
class WithdrawRequest extends Model
{
    protected $fillable = ['user_id', 'amount', 'status', 'note', 'user_bank_id']; // Thêm 'user_bank_id' vào $fillable nếu cần

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



public function userBank()
{
    return $this->belongsTo(UserBank::class, 'user_bank_id', 'id');
}

}

