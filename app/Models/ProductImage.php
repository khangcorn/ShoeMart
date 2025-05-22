<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'product_id', 'variant_id', 'image_url', 'type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

<<<<<<< HEAD
    public function variant()
=======
    public function variants()
>>>>>>> 1bbab0a (Full code DATN)
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }
}
