<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingFee extends Model
{
    use HasFactory;
    protected $fillable = [
        'province_id',
        'district_id',
        'fee',
    ];
    
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }

    /**
     * Relationship với model District
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }
}
