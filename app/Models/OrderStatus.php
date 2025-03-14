<?php

// app/Models/OrderStatus.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_statuses';
    protected $primaryKey = 'status_id';
    public $timestamps = true;

    protected $fillable = ['name', 'description'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'status_id');
    }
}