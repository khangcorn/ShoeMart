<?php

// app/Models/ShippingFee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;

    protected $table = 'shipping_fees';
    protected $primaryKey = 'shipping_id';
    public $timestamps = false;

    protected $fillable = ['region', 'fee'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_id');
    }
}
