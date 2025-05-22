<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'username', 'password', 'email', 'phone', 'address', 'avatar',
        'created_by', 'is_blocked', 'must_change_password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            Wallet::create([
                'user_id' => $user->user_id,
                'balance' => 0,
            ]);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddresses::class, 'user_id', 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('name', $roles)->exists();
        }
        return $this->roles()->where('name', $roles)->exists();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'user_id');
    }

    public function banks()
    {
        return $this->hasMany(UserBank::class, 'user_id');
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }


 public function permissions()
{
    return $this->belongsToMany(Permission::class, 'user_permission', 'user_id', 'permission_id');
}

   public function getAllPermissions()
{
    $rolePermissions = $this->roles()->with('permissions')->get()
        ->pluck('permissions')->flatten();

    $userPermissions = $this->permissions;

    return $rolePermissions->merge($userPermissions)->unique('permission_id');
}


    public function hasPermission($permissionName)
    {
        return $this->getAllPermissions()->contains('name', $permissionName);
    }

    public function isSuperAdmin()
    {
        return $this->roles()->where('name', 'admin')->exists();
    }
}
