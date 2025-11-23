<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    protected $fillable = [
        'user_id',
        'name',
        'created_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(WarehouseDetail::class, 'warehouse_id', 'id');
    }
}
