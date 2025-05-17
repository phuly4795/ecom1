<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function province() {
        return $this->belongsTo(Province::class, 'city_code', 'code');
    }

    public function ward() {
        return $this->hasMany(Ward::class, 'district_code', 'code');
    }

}
