<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] * $item['qty']);
            }

            $sale = Sale::create([
                'outlet_id' => $data['outlet_id'],
                'user_id' => $user->id,
                'invoice_number' => 'INV-' . time(),
                'subtotal' => $subtotal,
                'total' => $subtotal, // simplified for now
                'paid_amount' => $data['paid_amount'],
                'change_amount' => $data['paid_amount'] - $subtotal,
            ]);

            foreach ($data['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'name_snapshot' => $item['name'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                // Update stock
                $stock = ProductStock::where('product_id', $item['product_id'])
                    ->where('outlet_id', $data['outlet_id'])
                    ->first();
                
                if ($stock) {
                    $stock->decrement('qty', $item['qty']);
                }
            }

            return $sale;
        });
    }
}
