<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashController extends Controller
{
    public function cashIn(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $cashFlow = CashFlow::create([
            'user_id' => auth()->id(),
            'type' => 'in',
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'description' => $validated['description'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $cashFlow,
        ]);
    }

    public function cashOut(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $cashFlow = CashFlow::create([
            'user_id' => auth()->id(),
            'type' => 'out',
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'description' => $validated['description'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $cashFlow,
        ]);
    }
}
