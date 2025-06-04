<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class VisitController extends Controller
{
    public function getTotalVisits(): JsonResponse
    {
        $totalVisits = Visit::sum('visit_count');

        return response()->json([
            'success' => true,
            'total_visits' => $totalVisits,
        ]);
    }

    public function addLandingPageVisit(): JsonResponse
    {
        $visit = Visit::firstOrCreate(['page' => 'landing-page']);
        $visit->increment('visit_count');

        return response()->json([
            'success' => true,
            'visit_count' => $visit->visit_count,
        ]);
    }
}
