<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user && $user->business && !$user->business->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['business_inactive' => 'Akun bisnis Anda tidak aktif.']);
        }

        return $next($request);
    }
}
