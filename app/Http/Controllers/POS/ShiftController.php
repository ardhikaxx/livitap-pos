<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function __construct(private ShiftService $shiftService) {}

    public function open(Request $request)
    {
        $outletId = session('outlet_id', 1);
        $user = auth()->user();
        
        // Check if already open
        $existing = Shift::where('user_id', $user->id)
            ->where('outlet_id', $outletId)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Shift sudah dibuka',
                'shift' => $existing,
            ], 400);
        }

        $shift = $this->shiftService->openShift($user, $request->opening_cash ?? 0, $outletId);

        return response()->json([
            'success' => true,
            'message' => 'Shift dibuka',
            'shift' => $shift,
        ]);
    }

    public function close(Request $request, Shift $shift)
    {
        $this->authorize('close', $shift);

        $request->validate([
            'closing_cash' => 'required|numeric',
            'notes' => 'nullable|string|max:1000',
        ]);

        $shiftService = app(ShiftService::class);
        $shift = $shiftService->closeShift($shift, $request->closing_cash, $request->notes);

        return response()->json([
            'success' => true,
            'message' => 'Shift ditutup',
            'data' => $shift,
        ]);
    }

    public function active()
    {
        $shift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => $shift,
        ]);
    }

    public function show(Shift $shift)
    {
        $this->authorize('view', $shift);
        
        $shift->load(['user', 'sales', 'cashFlows']);
        
        return response()->json([
            'success' => true,
            'data' => $shift,
        ]);
    }

    public function report(Shift $shift)
    {
        $this->authorize('view', $shift);
        
        return view('shifts.report', compact('shift'));
    }
}