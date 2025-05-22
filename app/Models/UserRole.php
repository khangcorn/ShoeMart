<?php

// app/Models/UserRole.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $primaryKey = 'user_role_id';

    public $timestamps = false;

    protected $fillable = ['user_id', 'role_id'];
}
