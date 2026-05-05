<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sale extends Model
{
    use SoftDeletes, HasUuids;

    const STATUS_PAID = 'paid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_VOID = 'void';
    const STATUS_PENDING = 'pending';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'user_id', 'customer_id', 'invoice_number', 'type', 'status',
        'sale_date', 'subtotal', 'discount_amount', 'tax_amount', 'total',
        'paid_amount', 'change_amount', 'notes', 'table_id', 'order_type'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalePayment::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function scopeInPeriod($query, $from, $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'void');
    }
}
