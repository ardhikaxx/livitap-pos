<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id', 'outlet_id', 'variant_id', 'qty', 'min_qty', 'max_qty'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'min_qty' => 'decimal:2',
        'max_qty' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}