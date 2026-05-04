<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\StoreSaleRequest;
use App\Services\SaleService;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService) {}

    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = $this->saleService->createSale($request->validated(), auth()->user());
            return redirect()->route('pos.receipt', $sale->id)->with('success', 'Transaksi berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt($id)
    {
        $sale = \App\Models\Sale::with(['items.product', 'user', 'outlet.business'])->findOrFail($id);
        return view('pos.receipt', compact('sale'));
    }
}