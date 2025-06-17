<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property \Illuminate\Database\Eloquent\Relations\BelongsToMany $favoriteProducts
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birthday',
        'user_roles',
        'is_active',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function hasRoles($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class, 'user_id', 'id');
    }
}
