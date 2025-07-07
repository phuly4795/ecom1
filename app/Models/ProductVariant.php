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
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
        'original_price',
        'sku',
        'qty'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_products', 'product_variant_id', 'user_id');
    }

    public function isFavoritedBy($userId)
    {
        return $this->favoritedByUsers->contains('id', $userId);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->discount_percentage > 0
            && $this->discount_start_date
            && $this->discount_end_date
            && now()->between($this->discount_start_date, $this->discount_end_date);
    }

    public function getDisplayPriceAttribute()
    {
        return $this->is_on_sale
            ? round($this->original_price * (1 - $this->discount_percentage / 100))
            : $this->original_price;
    }
    
    public function scopeOnTrack($query)
    {
        return $query->where('qty', '>', 0);
    }
}
