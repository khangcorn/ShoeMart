<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    /**
     * Mỗi yêu cầu admin thuộc về một người dùng.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');

    }

}
