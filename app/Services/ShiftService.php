<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\Carbon;

class ShiftService
{
    public function openShift($user, $openingCash)
    {
        return Shift::create([
            'outlet_id' => 1,
            'user_id' => $user->id,
            'status' => 'open',
            'opened_at' => Carbon::now(),
            'opening_cash' => $openingCash,
        ]);
    }
}
