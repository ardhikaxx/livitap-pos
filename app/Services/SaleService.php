<?php

namespace App\Services;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleService
{
    protected $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * Create transaksi baru
     */
    public function createSale(array $data, $outletId, $user)
    {
        return DB::transaction(function () use ($data, $outletId, $user) {
            if (!$user) {
                throw new \Exception("User tidak terautentikasi");
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            $discountAmount = $data['discount_amount'] ?? 0;
            $taxAmount = $data['tax_amount'] ?? 0;
            $total = $subtotal - $discountAmount + $taxAmount;

            if ($total < 0) {
                throw new \Exception("Total transaksi tidak boleh negatif");
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Process Customer
            $customerId = $data['customer_id'] ?? null;
            if (!$customerId && isset($data['customer_name'])) {
                $customer = \App\Models\Customer::firstOrCreate(
                    ['name' => $data['customer_name']],
                    ['name' => $data['customer_name']]
                );
                $customerId = $customer->id;
            }

            // Create Sale
            $sale = Sale::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'invoice_number' => $invoiceNumber,
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'paid_amount' => 0,
                'change_amount' => 0,
                'table_id' => $data['table_id'] ?? null,
                'order_type' => $data['order_type'] ?? 'takeaway',
                'status' => Sale::STATUS_PENDING,
            ]);

            // Create Sale Items & Validate Stock
            foreach ($data['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan: ID {$item['product_id']}");
                }

                // Validate stock if tracking (no outlet restriction)
                if ($product->track_stock) {
                    $stock = ProductStock::where('product_id', $item['product_id'])
                        ->where('qty', '>=', $item['qty'])
                        ->first();
                    
                    if (!$stock) {
                        $availableQty = ProductStock::where('product_id', $item['product_id'])->sum('qty');
                        throw new \Exception("Stok {$product->name} tidak mencukupi. Tersisa: " . $availableQty);
                    }

                    // Decrement stock & create movement
                    $qtyBefore = $stock->qty;
                    $stock->decrement('qty', $item['qty']);

                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'type' => 'sale',
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'qty_before' => $qtyBefore,
                        'qty_change' => -$item['qty'],
                        'qty_after' => $qtyBefore - $item['qty'],
                        'notes' => 'Sale: ' . $invoiceNumber,
                        'user_id' => $user->id,
                    ]);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'name_snapshot' => $item['name'] ?? $product->name,
                    'sku_snapshot' => $item['sku'] ?? $product->sku,
                    'qty' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'subtotal' => $item['price'] * $item['qty'],
                    'buy_price' => $product->prices->first()?->buy_price ?? 0,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Process payments if provided
            if (isset($data['payment_method']) && isset($data['paid_amount'])) {
                $totalPaid = $data['paid_amount'];
                
                if ($totalPaid < $total) {
                    throw new \Exception("Pembayaran tidak mencukupi. Kekurangan: Rp " . ($total - $totalPaid));
                }

                \App\Models\SalePayment::create([
                    'sale_id' => $sale->id,
                    'method' => $data['payment_method'],
                    'amount' => $totalPaid,
                    'notes' => 'Payment for sale ' . $invoiceNumber,
                ]);

                $sale->update([
                    'paid_amount' => $totalPaid,
                    'change_amount' => $totalPaid - $total,
                    'status' => Sale::STATUS_PAID,
                ]);
            }

            // Award points if customer
            if ($sale->customer && $sale->total > 0) {
                $this->pointService->awardPoints($sale->customer, $sale);
            }

            return $sale;
        });
    }

    /**
     * Void transaksi
     */
    public function voidSale(Sale $sale, $reason, $userId)
    {
        if ($sale->status === Sale::STATUS_VOID) {
            throw new \Exception("Transaksi sudah di-void");
        }

        return DB::transaction(function () use ($sale, $reason, $userId) {
            // Restock
            foreach ($sale->items as $item) {
                if ($item->product->track_stock) {
                    $stock = ProductStock::where('product_id', $item->product_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('qty', $item->qty);
                    }
                }
            }

            // Refund points
            $this->pointService->voidPoints($sale);

            // Mark as void
            $sale->update(['status' => Sale::STATUS_VOID]);

            return $sale;
        });
    }

    /**
     * Hold transaksi (tahan pesanan)
     */
    public function getHeldCarts($userId)
    {
        return session()->get("held_carts_{$userId}", []);
    }

    public function holdCart(array $cart, $userId)
    {
        $heldCarts = session()->get("held_carts_{$userId}", []);
        $heldCarts[] = [
            'id' => Str::uuid(),
            'cart' => $cart,
            'created_at' => now()->toDateTimeString(),
        ];
        session()->put("held_carts_{$userId}", $heldCarts);

        return end($heldCarts);
    }
}
