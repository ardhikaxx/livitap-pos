<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Shift;
use App\Models\PointTransaction;
use App\Models\Customer;
use App\Services\StockService;
use App\Services\DiscountService;
use App\Services\PaymentService;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleService
{
    public function __construct(
        private StockService $stockService,
        private DiscountService $discountService,
        private PaymentService $paymentService,
        private PointService $pointService
    ) {}

    public function createSale(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $outletId = $data['outlet_id'];
            
            // Validate shift
            $shift = Shift::where('user_id', $user->id)
                ->where('outlet_id', $outletId)
                ->where('status', 'open')
                ->first();

            if (!$shift) {
                throw new \Exception("Shift belum dibuka. Buka shift terlebih dahulu.");
            }

            // Calculate subtotal
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] * $item['qty']);
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
                $customer = \App\Models\Customer::firstOrCreate([
                    'name' => $data['customer_name'],
                    'business_id' => $user->business_id,
                ]);
                $customerId = $customer->id;
            }

            // Create Sale
            $sale = Sale::create([
                'id' => Str::uuid(),
                'outlet_id' => $outletId,
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'shift_id' => $shift->id,
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
                'status' => 'pending',
            ]);

            // Create Sale Items & Validate Stock
            foreach ($data['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan: ID {$item['product_id']}");
                }

                // Validate stock if tracking
                if ($product->track_stock) {
                    $stockQuery = ProductStock::where('product_id', $item['product_id'])
                        ->where('outlet_id', $outletId);
                    
                    if (isset($item['variant_id'])) {
                        $stockQuery->where('variant_id', $item['variant_id']);
                    }

                    $stock = $stockQuery->first();

                    if (!$stock || $stock->qty < $item['qty']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi. Tersisa: " . ($stock?->qty ?? 0));
                    }

                    // Decrement stock & create movement
                    $qtyBefore = $stock->qty;
                    $stock->decrement('qty', $item['qty']);

                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'outlet_id' => $outletId,
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
            if (isset($data['payments']) && is_array($data['payments'])) {
                $totalPaid = collect($data['payments'])->sum('amount');
                
                if ($totalPaid < $total) {
                    throw new \Exception("Pembayaran tidak mencukupi. Kekurangan: Rp " . ($total - $totalPaid));
                }

                foreach ($data['payments'] as $payment) {
                    \App\Models\SalePayment::create([
                        'sale_id' => $sale->id,
                        'method' => $payment['method'],
                        'amount' => $payment['amount'],
                        'reference_number' => $payment['reference_number'] ?? null,
                        'notes' => $payment['notes'] ?? null,
                    ]);
                }

                $sale->update([
                    'paid_amount' => $totalPaid,
                    'change_amount' => $totalPaid - $total,
                    'status' => 'paid',
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
        if ($sale->status === 'void') {
            throw new \Exception("Transaksi sudah di-void");
        }

        // Cek shift policy
        $shift = $sale->shift;
        if ($shift && $shift->status !== 'open') {
            if (!Auth::check()) {
                throw new \Exception("Hanya Manager/Owner yang bisa void transaksi di shift yang sudah tutup");
            }

            $user = Auth::user();
            
            // Bypass Spatie trait methods and use direct relationship
            $hasPermission = $user->roles()->whereIn('name', ['manager', 'owner', 'super-admin', 'Owner'])->exists();
            
            if (!$hasPermission) {
                throw new \Exception("Hanya Manager/Owner yang bisa void transaksi di shift yang sudah tutup");
            }
        }

        return DB::transaction(function () use ($sale, $reason, $userId) {
            // Restock
            foreach ($sale->items as $item) {
                if ($item->product->track_stock) {
                    $stock = ProductStock::where('product_id', $item->product_id)
                        ->where('outlet_id', $sale->outlet_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('qty', $item->qty);
                    }
                }
            }

            // Refund points
            $this->pointService->voidPoints($sale);

            // Mark as void
            $sale->update(['status' => 'void']);

            return $sale;
        });
    }

    /**
     * Hold transaksi (tahan pesanan)
     */
    public function holdCart(array $cart, $outletId, $userId)
    {
        // Simpan ke session atau database
        $holdData = [
            'cart' => $cart,
            'outlet_id' => $outletId,
            'user_id' => $userId,
            'held_at' => now(),
        ];

        $holds = session()->get('held_carts', []);
        $holds[] = $holdData;
        session()->put('held_carts', $holds);

        return $holdData;
    }

    /**
     * Retrieve held carts
     */
    public function getHeldCarts($userId)
    {
        return collect(session()->get('held_carts', []))
            ->where('user_id', $userId)
            ->values()
            ->all();
    }
}
