<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\StoreSaleRequest;
use App\Services\SaleService;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService,
        protected DiscountService $discountService
    ) {}

    public function store(Request $request)
    {
        try {
            // Karena POS menggunakan fetch JSON, kita gunakan request raw
            // Pastikan data valid
            $data = $request->all();
            
            $sale = $this->saleService->createSale($data, auth()->user());
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'invoice_number' => $sale->invoice_number,
                    'id' => $sale->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function receipt($id)
    {
        $sale = Sale::with(['items.product', 'user', 'outlet.business', 'payments'])->findOrFail($id);
        return view('pos.receipt', compact('sale'));
    }

    public function calculateCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $discountCalc = $this->discountService->calculateDiscount($request->items);
        
        $subtotal = collect($request->items)->sum(fn($item) => $item['price'] * $item['qty']);
        $total = $subtotal - $discountCalc['discount_amount'];

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $subtotal,
                'discount_amount' => $discountCalc['discount_amount'],
                'discount_type' => $discountCalc['discount_type'],
                'total' => $total,
            ],
        ]);
    }

    public function hold(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
        ]);

        $held = $this->saleService->holdCart($request->cart, session('outlet_id'), auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Pesanan ditahan',
            'data' => $held,
        ]);
    }

    public function holds()
    {
        $heldCarts = $this->saleService->getHeldCarts(auth()->id());
        return response()->json([
            'success' => true,
            'data' => $heldCarts,
        ]);
    }

    public function void(Sale $sale, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $voidedSale = $this->saleService->voidSale($sale, $request->reason, auth()->id());
            return back()->with('success', 'Transaksi berhasil di-void');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
