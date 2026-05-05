<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'gender', 'birthdate',
        'photo', 'tier', 'points', 'credit_limit', 'notes', 'is_active'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'points' => 'integer',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}