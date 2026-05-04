<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
