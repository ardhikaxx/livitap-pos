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
            $primaryOutlet = $user->outlets()
                ->where('is_primary', true)
                ->first();

            if ($primaryOutlet) {
                session(['outlet_id' => $primaryOutlet->id]);
            } else {
                // Fallback ke outlet pertama
                $firstOutlet = $user->outlets()->first();
                if ($firstOutlet) {
                    session(['outlet_id' => $firstOutlet->id]);
                }
            }
        }

        return $next($request);
    }
}
