<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    // public function getReport(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     // Retrieve filters from the request
    //     $dateFrom = $request->input('date_from', null);
    //     $dateTo = $request->input('date_to', null);

    //     // Build the query to retrieve posts within the date range
    //     $query = UserPost::with('users');

    //     if ($dateFrom) {
    //         $query->whereDate('created_at', '>=', $dateFrom);
    //     }
    //     if ($dateTo) {
    //         $query->whereDate('created_at', '<=', $dateTo);
    //     }

    //     // Fetch posts with applied filters
    //     $posts = $query->get();

    //     // Return the posts along with the timestamps of likes
    //     return response()->json([
    //         'posts' => $posts->map(function ($post) {
    //             return [
    //                 'post' => $post,
    //                 'likes' => $post->users->pluck('pivot.created_at')  // Retrieve like timestamps
    //             ];
    //         })
    //     ], 200);
    // }

    // dashboard pie chart 1 - most active user
    public function getMostActiveUsers()
    {
        $mostActiveUsers = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        if ($mostActiveUsers->isEmpty()) {
            return response()->json(['users' => []], 200);
        }

        return response()->json([
            'users' => $mostActiveUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fname . ' ' . $user->lname,
                    'total_posts' => $user->posts_count
                ];
            })
        ], 200);
    }

    // dashboard pie chart 2 - most liked posts
    public function getMostLikedPosts()
    {
        $mostLikedPosts = UserPost::where('status', 'posted')
            ->orderByDesc('total_likes')
            ->limit(5)
            ->get(['id', 'title', 'total_likes']);

        if ($mostLikedPosts->isEmpty()) {
            return response()->json(['message' => 'No liked posts found'], 404);
        }

        return response()->json([
            'posts' => $mostLikedPosts
        ]);
    }

    // dashboard bar chart - most talked-about issues
    public function chartMaterials() 
    {
        $currentYear = Carbon::now()->year;

        $materials = [
            'Compost', 'Plastic', 'Rubber', 'Wood', 'Paper', 'Glass', 'Boxes', 
            'Mixed Waste', 'Cloth', 'Miscellaneous Products', 'Tips & Tricks', 'Issues'
        ];

        $counts = [];
        $totalPosts = UserPost::whereIn('status', ['posted', 'reported'])
            ->whereYear('created_at', $currentYear)
            ->count(); // Get total posts for the year

        // Count occurrences of each material
        foreach ($materials as $material) {
            $counts[] = [
                'materials' => $material,
                'total_posts' => UserPost::whereIn('status', ['posted', 'reported'])
                    ->whereYear('created_at', $currentYear)
                    ->whereJsonContains('materials', $material)
                    ->count()
            ];
        }

        return response()->json([
            'total_posts' => $totalPosts,
            'materials_data' => $counts
        ]);
    }

    // dashboard bar chart - most talked-about categories
    public function tableCategories() 
    {
        // Get the current year
        $currentYear = Carbon::now()->year;

        $categoryAnalytics = UserPost::whereIn('status', ['posted', 'reported'])
            ->whereYear('created_at', $currentYear)
            ->selectRaw('category, COUNT(*) as total_posts')
            ->groupBy('category')
            ->get();

        return response()->json($categoryAnalytics);
    }

    // report tab - materials posted count
    public function getMaterialsCount()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $materials = [
            'Compost', 'Plastic', 'Rubber', 'Wood', 'Paper', 'Glass', 'Boxes', 
            'Mixed Waste', 'Cloth', 'Miscellaneous Products', 'Tips & Tricks', 'Issues'
        ];

        $counts = [];

        // Count occurrences of each material
        foreach ($materials as $material) {
            $counts[$material] = UserPost::whereJsonContains('materials', $material)->count();
        }

        // Sort by count in descending order
        arsort($counts);

        return response()->json($counts);
    }

    // report tab - categories posted count
    public function mostCategories(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $categories = [
            "Reduce", "Reuse", "Recycle", "Gardening"
        ];

        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()->toDateString()));

        $data = UserPost::whereIn('status', ['posted', 'reported'])
            ->whereIn('category', $categories)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        return response()->json($data);
    }

    // report tab - most active user
    public function getUserStats()
    {
        $users = User::getUsersWithPostAndLikeStats();
        return response()->json($users);
    }

    // report tab - most liked posts
    public function get20MostLikedPosts()
    {
        $posts = UserPost::get20MostLikedPosts();
        return response()->json($posts);
    }
}
