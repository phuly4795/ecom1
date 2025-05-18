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

    protected $fillable = [
        'id',
        'title',
        'slug',
        'description',
        'price',
        'compare_price',
        'category_id',
        'subcategory_id',
        'brand_id',
        'is_featured',
        'sku',
        'barcode',
        'track_qty',
        'qty',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // 1 Product thuộc về 1 Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // 1 Product thuộc về 1 SubCategory
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
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
}
