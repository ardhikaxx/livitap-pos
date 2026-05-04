<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenOrder extends Model
{
    protected $fillable = [
        'sale_id', 'table_id', 'status', 'notes', 'printed_at'
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'status' => 'string',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}