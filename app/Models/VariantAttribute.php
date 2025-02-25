<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    protected $primaryKey = 'attribute_id';

    protected $fillable = ['variant_id', 'attribute_name', 'attribute_value'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }
}

