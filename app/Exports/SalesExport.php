<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $from;
    protected $to;
    protected $userId;

    public function __construct($from, $to, $userId = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->userId = $userId;
    }

    public function query()
    {
        $query = Sale::query()->with(['user', 'customer']);

        if ($this->from) {
            $query->where('sale_date', '>=', $this->from);
        }
        if ($this->to) {
            $query->where('sale_date', '<=', $this->to);
        }
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Date',
            'Cashier',
            'Customer',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Status'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_number,
            $sale->sale_date->format('d/m/Y H:i'),
            $sale->user->name ?? '-',
            $sale->customer->name ?? '-',
            $sale->subtotal,
            $sale->discount_amount,
            $sale->tax_amount,
            $sale->total,
            ucfirst($sale->status)
        ];
    }
}
