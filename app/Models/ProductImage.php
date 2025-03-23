<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';
    protected $table = 'product_images';

    protected $fillable = [
        'product_id', 'variant_id', 'image_url', 'type'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
    public function variants()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }
}

