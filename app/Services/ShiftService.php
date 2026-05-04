<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    /**
     * Buka shift kasir
     */
    public function openShift($user, $openingCash, $outletId)
    {
        return DB::transaction(function () use ($user, $openingCash, $outletId) {
            // Validasi tidak ada shift open lain untuk outlet ini
            $openShift = Shift::where('outlet_id', $outletId)
                ->where('status', 'open')
                ->first();
                
            if ($openShift) {
                throw new \Exception("Outlet masih memiliki shift terbuka. Tutup dahulu.");
            }

            return Shift::create([
                'outlet_id' => $outletId,
                'user_id' => $user->id,
                'status' => 'open',
                'opened_at' => Carbon::now(),
                'opening_cash' => $openingCash,
            ]);
        });
    }

    /**
     * Tutup shift kasir
     */
    public function closeShift(Shift $shift, $closingCash, $notes = null)
    {
        return DB::transaction(function () use ($shift, $closingCash, $notes) {
            $totalSales = $shift->sales()->sum('paid_amount');
            $expectedCash = $shift->opening_cash + $totalSales;
            $difference = $closingCash - $expectedCash;

            $shift->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closing_cash' => $closingCash,
                'expected_cash' => $expectedCash,
                'difference' => $difference,
                'notes' => $notes,
            ]);

            return $shift;
        });
    }

    /**
     * Force close shift oleh manager
     */
    public function forceCloseShift(Shift $shift, $manager)
    {
        if ($shift->status === 'closed') {
            throw new \Exception("Shift sudah ditutup");
        }

        // Estimate sales yang belum diinput (optional)
        $shift->update([
            'status' => 'forced_closed',
            'closed_at' => now(),
            'notes' => 'Force closed by ' . $manager->name,
        ]);

        return $shift;
    }
}

