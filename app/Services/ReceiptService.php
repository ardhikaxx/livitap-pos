<?php

namespace App\Services;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptService
{
    /**
     * Generate PDF struk
     */
    public function generatePdf(Sale $sale)
    {
        $sale->load([
            'items.product', 
            'payments', 
            'customer', 
            'user'
        ]);

        $data = [
            'sale' => $sale,
            'outlet' => null,
            'business' => null,
        ];

        $pdf = PDF::loadView('pos.receipt', $data);
        
        return $pdf->output();
    }

    /**
     * Generate data untuk print thermal (58mm / 80mm)
     */
    public function generateThermalData(Sale $sale)
    {
        $sale->load(['items.product']);
        
        return [
            'header' => [
                'business_name' => session('business_name', 'LIVITAP POS'),
                'business_address' => session('business_address', ''),
                'business_phone' => session('business_phone', ''),
                'outlet_name' => '',
                'invoice_number' => $sale->invoice_number,
                'date' => $sale->sale_date->format('d/m/Y H:i'),
                'cashier' => $sale->user->name,
            ],
            'items' => $sale->items->map(function ($item) {
                return [
                    'name' => $item->name_snapshot,
                    'qty' => $item->qty,
                    'price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'summary' => [
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount_amount,
                'tax' => $sale->tax_amount,
                'total' => $sale->total,
                'paid' => $sale->paid_amount,
                'change' => $sale->change_amount,
                'payment_methods' => $sale->payments->pluck('method', 'amount')->toArray(),
            ],
            'footer' => [
                'thank_you' => 'Terima kasih atas kunjungan Anda!',
            ],
        ];
    }
}
