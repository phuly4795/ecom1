<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Order extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'user_id',
        'order_code',
        'shipping_address_id',
        'billing_full_name',
        'billing_email',
        'billing_address',
        'billing_province_id',
        'billing_district_id',
        'billing_ward_id',
        'billing_telephone',
        'payment_method',
        'note',
        'total_amount',
        'coupon_code',
        'discount_amount',
        'status',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
    
    // Thêm relationship cho địa chỉ thanh toán
    public function billingProvince()
    {
        return $this->belongsTo(Province::class, 'billing_province_id', 'code');
    }

    public function billingDistrict()
    {
        return $this->belongsTo(District::class, 'billing_district_id', 'code');
    }

    public function billingWard()
    {
        return $this->belongsTo(Ward::class, 'billing_ward_id', 'code');
    }
}
