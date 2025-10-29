<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    protected $table = 'warehouse';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'qty',
        'price',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productVariants()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }
}
