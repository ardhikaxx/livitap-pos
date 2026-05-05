<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\Shift;
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

        $shift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            throw new \Exception("Shift belum dibuka");
        }

        $cashFlow = CashFlow::create([
            'shift_id' => $shift->id,
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

        $shift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            throw new \Exception("Shift belum dibuka");
        }

        $cashFlow = CashFlow::create([
            'outlet_id' => session('outlet_id'),
            'shift_id' => $shift->id,
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
