<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $casts = [
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];
    protected $fillable = [
        'product_id',
        'variant_name',
        'discounted_price',
        'discount_start_date',
        'discount_end_date',
        'price',
        'sku',
        'qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
