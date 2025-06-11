<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteProduct extends Model
{
    protected $table = 'favorite_products';

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id'
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
