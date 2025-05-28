<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'sort'
    ];

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'category_sub_category', 'category_id', 'sub_category_id')
            ->withTimestamps();
    }
}
