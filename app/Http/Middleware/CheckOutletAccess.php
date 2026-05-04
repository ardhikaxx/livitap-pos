<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOutletAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $outletId = $request->route('outlet') ? $request->route('outlet')->id 
                   : $request->outlet_id 
                   ?: session('outlet_id');

        if ($outletId && $user) {
            $hasAccess = $user->outlets()->where('outlets.id', $outletId)->exists();
            
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak ke outlet ini.'
                ], 403);
            }
        }

        return $next($request);
    }
}
