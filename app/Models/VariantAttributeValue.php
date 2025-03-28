<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    use HasFactory;

    // Bảng tương ứng trong cơ sở dữ liệu
    protected $table = 'variant_attribute_values';
 

    // Các trường có thể gán đại trà
    protected $fillable = [
        'variant_id',
        'attribute_id',
    ];
    protected $primaryKey = null; 
    public $incrementing = false;
    
    public $timestamps = true;

    // Mối quan hệ với bảng ProductVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'variant_id');
    }

    // Mối quan hệ với bảng VariantAttribute
    public function variantAttribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'attribute_id', 'attribute_id');
    }
}