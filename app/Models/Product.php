<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $casts = [
        'specifications' => 'array',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'compare_price',
        'original_price',
        'discount_percentage',
        'discount_start_date',
        'discount_end_date',
        'category_id',
        'subcategory_id',
        'brand_id',
        'is_featured',
        'product_type',
        'sku',
        'barcode',
        'track_qty',
        'qty',
        'status',
        'specifications',
        'warranty_period',
        'warranty_policy',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // 1 Product thuộc về 1 Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 1 Product thuộc về 1 SubCategory
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    // 1 Product thuộc về 1 Brand
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_products', 'product_id', 'user_id');
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

    public function orderItems()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function wishlists()
    {
        return $this->hasMany(FavoriteProduct::class, 'product_id');
    }
}
