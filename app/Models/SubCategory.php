<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'sub_categories';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'status'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_sub_category', 'sub_category_id', 'category_id')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'id');
    }
}
