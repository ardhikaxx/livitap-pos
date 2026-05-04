<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        $outletId = session('outlet_id', 1);
        
        $stocks = ProductStock::with(['product.category', 'outlet'])
            ->where('outlet_id', $outletId)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('product', function ($sub) use ($request) {
                    $sub->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('sku', 'like', '%'.$request->search.'%');
                });
            })
            ->paginate(25);

        return view('stocks.index', compact('stocks'));
    }

    public function adjust(Product $product)
    {
        $outletId = session('outlet_id', 1);
        $stock = $product->stocks()->where('outlet_id', $outletId)->first();
        return view('stocks.adjust', compact('product', 'stock'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'qty' => 'required|numeric',
            'action' => 'required|in:add,subtract,set',
            'notes' => 'nullable|string|max:500',
        ]);

        $outletId = session('outlet_id', 1);
        
        $qtyChange = match ($request->action) {
            'add' => $request->qty,
            'subtract' => -$request->qty,
            'set' => $request->qty - ($product->stocks()->where('outlet_id', $outletId)->first()?->qty ?? 0),
        };

        $this->stockService->adjustStock(
            $product->id,
            $outletId,
            $qtyChange,
            'adjustment',
            auth()->id(),
            $request->notes
        );

        return back()->with('success', 'Stok berhasil disesuaikan');
    }

    public function adjustStock(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric',
            'type' => 'required|in:purchase,adjustment,transfer,return',
            'notes' => 'nullable|string',
        ]);

        $this->stockService->adjustStock(
            $request->product_id,
            $request->outlet_id,
            $request->qty,
            $request->type,
            auth()->id(),
            $request->notes
        );

        return response()->json(['success' => true, 'message' => 'Stok berhasil diperbarui']);
    }

    public function transferStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_outlet_id' => 'required|exists:outlets,id',
            'to_outlet_id' => 'required|exists:outlets,id|different:from_outlet_id',
            'qty' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        $this->stockService->transferStock(
            $request->product_id,
            $request->from_outlet_id,
            $request->to_outlet_id,
            $request->qty,
            auth()->id(),
            $request->notes
        );

        return response()->json(['success' => true, 'message' => 'Transfer stok berhasil']);
    }

    public function opname(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.actual_qty' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $opname = DB::transaction(function () use ($request) {
            $opname = \App\Models\StockOpname::create([
                'outlet_id' => $request->outlet_id,
                'status' => 'open',
                'opened_by' => auth()->id(),
                'opened_at' => now(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $stock = ProductStock::where('product_id', $item['product_id'])
                    ->where('outlet_id', $request->outlet_id)
                    ->first();

                $opname->items()->create([
                    'product_id' => $item['product_id'],
                    'system_qty' => $stock?->qty ?? 0,
                    'actual_qty' => $item['actual_qty'],
                ]);
            }

            return $opname;
        });

        return response()->json(['success' => true, 'data' => $opname]);
    }

    public function closeOpname(Request $request, $opnameId)
    {
        $opname = \App\Models\StockOpname::findOrFail($opnameId);
        
        if ($opname->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Opname sudah ditutup'], 400);
        }

        $this->stockService->completeOpname($opnameId, auth()->id());

        return response()->json(['success' => true, 'message' => 'Opname selesai']);
    }

    public function lowStockAlert()
    {
        $outletId = session('outlet_id', 1);
        $lowStocks = $this->stockService->checkLowStock($outletId);
        
        return response()->json([
            'success' => true,
            'data' => $lowStocks,
            'count' => $lowStocks->count(),
        ]);
    }
}
