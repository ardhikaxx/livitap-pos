<?php

namespace App\Http\Controllers;

use App\Models\KitchenOrder;
use App\Models\KitchenOrderItem;
use App\Models\Sale;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function orders(Request $request)
    {
        $outletId = session('outlet_id');
        
        $orders = KitchenOrder::with(['sale.items.product', 'table', 'items'])
            ->whereHas('sale', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('kitchen.orders', compact('orders'));
    }

    public function updateStatus(Request $request, KitchenOrder $kitchenOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready,served,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $kitchenOrder->update($validated);

        // If all items ready, maybe update order status? (optional)

        return response()->json([
            'success' => true,
            'data' => $kitchenOrder,
        ]);
    }

    public function printOrder(KitchenOrder $kitchenOrder)
    {
        $kitchenOrder->load(['sale.items.product', 'table']);
        
        // Print to kitchen printer
        // This would integrate with escpos-php or browser print
        
        $kitchenOrder->update(['printed_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Order sent to kitchen',
        ]);
    }
}
