<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name', 'description', 'price', 'price_sale', 'stock', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class, 'product_id');
    }

    public function images()
{
    return $this->hasMany(ProductImage::class, 'product_id');
}
}
