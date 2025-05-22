<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;

    protected $table = 'shipping_fees';

    protected $primaryKey = 'shipping_id';

    public $timestamps = true;

    protected $fillable = ['province', 'district', 'ward', 'fee'];

    protected $casts = [
        'fee' => 'decimal:2',
    ];

    public function scopeFilterByLocation($query, $province, $district = null, $ward = null)
    {
        $query->where('province', $province);

        if ($district) {
            $query->where('district', $district);
        }

        if ($ward) {
            $query->where('ward', $ward);
        }

        return $query;
    }
}
