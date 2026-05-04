<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Services\ShiftService;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct(protected ShiftService $shiftService) {}

    public function store(Request $request)
    {
        $this->shiftService->openShift(auth()->user(), $request->opening_cash);
        return back()->with('success', 'Shift berhasil dibuka.');
    }
}
