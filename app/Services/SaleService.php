<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    public function createSale(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $outletId = $data['outlet_id'];
            $shift = Shift::where('user_id', $user->id)
                ->where('outlet_id', $outletId)
                ->where('status', 'open')
                ->first();

            if (!$shift) {
                throw new \Exception('Shift belum dibuka');
            }

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] * $item['qty']);
            }

            $discountAmount = $data['discount_amount'] ?? 0;
            $taxAmount = $data['tax_amount'] ?? 0;
            $total = $subtotal - $discountAmount + $taxAmount;

            $sale = Sale::create([
                'id' => Str::uuid(),
                'outlet_id' => $outletId,
                'user_id' => $user->id,
                'shift_id' => $shift->id,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'paid_amount' => $data['paid_amount'],
                'change_amount' => $data['paid_amount'] - $total,
                'notes' => $data['notes'] ?? null,
                'order_type' => $data['order_type'] ?? 'dine_in',
            ]);

            foreach ($data['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                
                if ($product && $product->track_stock) {
                    $stock = ProductStock::where('product_id', $item['product_id'])
                        ->where('outlet_id', $outletId)
                        ->first();

                    if ($stock && $stock->qty < $item['qty']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi");
                    }

                    $qtyBefore = $stock->qty;
                    $stock->decrement('qty', $item['qty']);

                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'outlet_id' => $outletId,
                        'type' => 'sale',
                        'reference_type' => 'App\Models\Sale',
                        'reference_id' => $sale->id,
                        'qty_before' => $qtyBefore,
                        'qty_change' => -$item['qty'],
                        'qty_after' => $qtyBefore - $item['qty'],
                        'user_id' => $user->id,
                    ]);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'name_snapshot' => $item['name'] ?? $product->name,
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'buy_price' => $product->prices->first()?->buy_price ?? 0,
                ]);
            }

            return $sale;
        });
    }

    public function calculateChange(float $paidAmount, float $total): float
    {
        return max(0, $paidAmount - $total);
    }

    public function formatRupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}