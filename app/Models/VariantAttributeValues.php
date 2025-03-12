<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttributeValues extends Model
{
    use HasFactory;

    protected $table = 'variant_attribute_values';
    protected $primaryKey = 'value_id';
    public $timestamps = true;
    protected $fillable = ['variant_id', 'attribute_id', 'attribute_value', 'stock'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'attribute_id');
    }
}
