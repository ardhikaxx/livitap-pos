<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'type', 'logo', 'address', 'phone', 'email', 'npwp', 'settings', 'is_active'
    ];

    protected $casts = [
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    public function outlets(): HasMany
    {
        return $this->hasMany(Outlet::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }
}