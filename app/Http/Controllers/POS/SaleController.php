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
            return response()->json([
                'success' => true, 
                'message' => 'Transaksi Berhasil',
                'data' => $sale
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
