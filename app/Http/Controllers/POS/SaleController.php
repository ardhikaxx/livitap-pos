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
            return redirect()->route('pos.index')->with('success', 'Transaksi berhasil disimpan. No: ' . $sale->invoice_number);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}