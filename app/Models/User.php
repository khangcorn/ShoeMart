<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
use Illuminate\Notifications\Notifiable; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory,Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = ['username', 'password', 'email', 'phone', 'address', 'avatar'];

    protected $hidden = [
        'password',
         'remember_token',
    ];
    // Quan hệ một nhiều với Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // Quan hệ một nhiều với Comment
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    // Quan hệ một nhiều với UserAddress
    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

 
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

}
