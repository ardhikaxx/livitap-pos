<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Penyesuaian stok
     */
    public function adjustStock($productId, $qtyChange, $type, $userId, $notes = null, $variantId = null)
    {
        return DB::transaction(function () use ($productId, $qtyChange, $type, $userId, $notes, $variantId) {
            $stock = ProductStock::firstOrCreate([
                'product_id' => $productId,
                'variant_id' => $variantId,
            ], ['qty' => 0]);

            $qtyBefore = $stock->qty;
            $stock->qty += $qtyChange;
            $stock->save();

            // Catat pergerakan stok
            StockMovement::create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'type' => $type,
                'reference_type' => null,
                'reference_id' => null,
                'qty_before' => $qtyBefore,
                'qty_change' => $qtyChange,
                'qty_after' => $stock->qty,
                'notes' => $notes,
                'user_id' => $userId,
            ]);

            return $stock;
        });
    }

    /**
     * Selesaikan opname stok
     */
    public function completeOpname($opnameId, $userId)
    {
        return DB::transaction(function () use ($opnameId, $userId) {
            $opname = \App\Models\StockOpname::with('items')->findOrFail($opnameId);
            
            if ($opname->status === 'closed') {
                throw new \Exception("Opname sudah ditutup");
            }

            foreach ($opname->items as $item) {
                $difference = $item->actual_qty - $item->system_qty;
                $item->difference = $difference;
                $item->save();

                // Update stok
                $stock = ProductStock::firstOrCreate([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                ], ['qty' => 0]);

                $stock->qty = $item->actual_qty;
                $stock->save();

                // Catat pergerakan
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'opname',
                    'reference_type' => 'App\Models\StockOpname',
                    'reference_id' => $opnameId,
                    'qty_before' => $item->system_qty,
                    'qty_change' => $difference,
                    'qty_after' => $item->actual_qty,
                    'notes' => 'Stock opname',
                    'user_id' => $userId,
                ]);
            }

            $opname->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => $userId,
            ]);

            return $opname;
        });
    }

    /**
     * Cek stok minimum
     */
    public function checkLowStock()
    {
        $lowStocks = ProductStock::with('product')
            ->whereColumn('qty', '<=', 'min_qty')
            ->where('min_qty', '>', 0)
            ->get();

        return $lowStocks;
    }
}
