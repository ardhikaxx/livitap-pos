<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use Symfony\Component\HttpFoundation\Response;

class CheckShiftOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $outletId = session('outlet_id', 1);

        // Skip untuk routes tertentu (buka shift, login, dll)
        if ($request->is('login') || 
            $request->is('logout') || 
            $request->is('shifts/open') ||
            $request->is('shifts/*/close') ||
            $request->is('pos') ||
            $request->is('pos/*') ||
            $request->is('api/auth*')) {
            return $next($request);
        }

        // Hanya cek shift untuk user dengan role kasir/waiter
        if ($user && ($user->hasRole('kasir') || $user->hasRole('waiter'))) {
            $activeShift = Shift::where('user_id', $user->id)
                ->where('outlet_id', $outletId)
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shift belum dibuka. Silakan buka shift terlebih dahulu.'
                ], 403);
            }
        }

        return $next($request);
    }
}
