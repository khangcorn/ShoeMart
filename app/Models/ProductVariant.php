<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $primaryKey = 'variant_id';

    protected $fillable = ['product_id', 'price', 'price_sale', 'stock'];

   // In the ProductVariant model
public function products()
{
    return $this->belongsTo(Product::class, 'product_id', 'id');
}

    public function attributes()
    {
        return $this->hasMany(VariantAttribute::class, 'variant_id', 'variant_id');
    }
}

