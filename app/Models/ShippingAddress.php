<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ShippingAddress extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'user_id',
        'session_id',
        'full_name',
        'email',
        'address',
        'province_id',
        'district_id',
        'ward_id',
        'telephone',
        'is_default',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationship với model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship với model Province
     */
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

    /**
     * Relationship với model Ward
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'code');
    }
}
