<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Outlet;
use Symfony\Component\HttpFoundation\Response;

class SetActiveOutlet
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user) {
            // Set outlet_id jika belum ada
            if (!session('outlet_id')) {
                $primaryOutlet = $user->outlets()
                    ->wherePivot('is_primary', true)
                    ->first();

                $outlet = $primaryOutlet ?? $user->outlets()->first();

                if ($outlet) {
                    session(['outlet_id' => $outlet->id]);
                }
            }

            // Selalu sync business_id dari outlet aktif
            if (session('outlet_id') && !session('business_id')) {
                $outlet = \App\Models\Outlet::find(session('outlet_id'));
                if ($outlet) {
                    session(['business_id' => $outlet->business_id]);
                }
            }
        }

        return $next($request);
    }
}
