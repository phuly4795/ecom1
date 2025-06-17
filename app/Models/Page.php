<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'content_json', 'is_active'];

    protected $casts = [
        'content_json' => 'array',
        'is_active' => 'boolean',
    ];
}
