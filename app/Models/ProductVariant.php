<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $primaryKey = 'variant_id';

    protected $fillable = [
        'product_id', 'price', 'price_sale', 'stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class, 'variant_id');
    }

    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'variant_id');
    }
}
