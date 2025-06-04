<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visit;

class TrackVisits
{

    public function handle(Request $request, Closure $next)
    {
        $visit = Visit::firstOrCreate(['page' => 'homepage']);
        $visit->increment('visit_count');

        return $next($request);
    }
}
