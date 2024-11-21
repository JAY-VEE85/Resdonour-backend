<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;

class ReportController extends Controller
{
    public function getReport(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retrieve filters from the request
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        // Build the query to retrieve posts within the date range
        $query = UserPost::with('users');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Fetch posts with applied filters
        $posts = $query->get();

        // Return the posts along with the timestamps of likes
        return response()->json([
            'posts' => $posts->map(function ($post) {
                return [
                    'post' => $post,
                    'likes' => $post->users->pluck('pivot.created_at')  // Retrieve like timestamps
                ];
            })
        ], 200);
    }
}
