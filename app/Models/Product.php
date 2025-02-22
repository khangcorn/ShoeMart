<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = ['name', 'description', 'price', 'price_sale', 'stock', 'category_id'];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class, 'product_id');
    }
}
