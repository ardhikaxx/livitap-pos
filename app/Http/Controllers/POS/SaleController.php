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
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        try {
            $data = $request->all();
            
            $sale = $this->saleService->createSale($data, null, $user);
            
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
        $sale = Sale::with(['items.product', 'user', 'payments'])->findOrFail($id);
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
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $request->validate([
            'cart' => 'required|array',
        ]);

        $held = $this->saleService->holdCart($request->cart, $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan ditahan',
            'data' => $held,
        ]);
    }

    public function holds()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $heldCarts = $this->saleService->getHeldCarts($user->id);
        return response()->json([
            'success' => true,
            'data' => $heldCarts,
        ]);
    }

    public function void(Sale $sale, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return back()->with('error', 'Unauthenticated');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $voidedSale = $this->saleService->voidSale($sale, $request->reason, $user->id);
            return back()->with('success', 'Transaksi berhasil di-void');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
