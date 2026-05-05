<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'type', 'reference_type', 'reference_id',
        'qty_before', 'qty_change', 'qty_after', 'notes', 'user_id'
    ];

    protected $casts = [
        'qty_before' => 'decimal:2',
        'qty_change' => 'decimal:2',
        'qty_after' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}