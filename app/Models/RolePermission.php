<?php

// app/Models/RolePermission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $table = 'role_permissions';

    protected $primaryKey = 'role_permission_id';

    public $timestamps = false;

    protected $fillable = ['role_id', 'permission_id'];
}
