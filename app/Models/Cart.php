<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function cartDetails()
    {
        return $this->hasMany(CartDetail::class);
    }
}
