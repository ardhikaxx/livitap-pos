<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrimStrings
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->merge($this->clean($request->all()));
        return $next($request);
    }

    protected function clean(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return trim($value);
            }
            return $value;
        }, $data);
    }
}
