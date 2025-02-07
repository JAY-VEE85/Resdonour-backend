<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;
use Illuminate\Support\Carbon;

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

    public function totalPost(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        $query = User::withCount([
            'posts' => function ($q) {
                $q->where('status', 'approved');
            },
            'likedPosts'
        ]);

        $query->whereNotIn('role', ['admin', 'agri']);

        if ($dateFrom) {
            $query->whereHas('posts', function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            });
        }
        if ($dateTo) {
            $query->whereHas('posts', function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });
        }

        $users = $query->orderByDesc('posts_count')->take(5)->get();

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->fname . ' ' . $user->lname,
                    'total_posts' => $user->posts_count,
                    'total_likes' => $user->liked_posts_count
                ];
            })
        ], 200);
    }

    public function oldestPending(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $oldestPendingPosts = UserPost::where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->take(2)
            ->get();

        if ($oldestPendingPosts->isEmpty()) {
            return response()->json(['message' => 'No pending posts found'], 404);
        }

        $oldestPendingPosts->each(function ($post) {
            $post->user_name = ($post->user->fname ?? '') . ' ' . ($post->user->lname ?? '');
            $post->user = null;  // Clear user data
        });

        return response()->json([
            'posts' => $oldestPendingPosts,
        ], 200);
    }

    public function totalPosts(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        $query = User::withCount([
            'posts' => function ($q) {
                $q->where('status', 'approved');
            },
            'likedPosts'
        ])
        ->whereNotIn('role', ['admin', 'agri']); 

        if ($dateFrom) {
            $query->whereHas('posts', function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            });
        }
        if ($dateTo) {
            $query->whereHas('posts', function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });
        }

        $users = $query->get()->sortByDesc('posts_count'); 

        $rank = 1; 
        foreach ($users as $user) {
            $badges = json_decode($user->badge, true) ?? [];

            $badges = array_filter($badges, function ($badge) {
                return strpos($badge, 'Active User Rank') === false && strpos($badge, 'Its Quiet Here!') === false;
            });

            if ($user->posts_count > 0 && $rank <= 3) {
                $badge = $this->getUserRank($rank);
                $badges[] = $badge;
                $rank++; 
            }

            if ($user->posts_count == 0) {
                $badges[] = "Its Quiet Here! :p";
            }

            if (count($badges) > 0) {
                $user->badge = json_encode(array_values($badges));
                $user->save();
            }
        }

        $paginatedUsers = $query->orderByDesc('posts_count')->paginate(10);

        return response()->json([
            'users' => $paginatedUsers->map(function ($user) {
                return [
                    'user_name' => $user->fname . ' ' . $user->lname,
                    'total_posts' => $user->posts_count,
                    'total_likes' => $user->liked_posts_count,
                    'badges' => json_decode($user->badge, true) 
                ];
            })
        ], 200);
    }

    private function getUserRank($rank)
    {
        if ($rank == 1) {
            return 'Active User Rank 1';
        } elseif ($rank == 2) {
            return 'Active User Rank 2';
        } elseif ($rank == 3) {
            return 'Active User Rank 3';
        }
    }




    public function totalLike(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        $query = UserPost::withCount('usersWhoLiked')
                        ->whereHas('usersWhoLiked');

        $query->whereHas('user', function ($q) {
            $q->whereNotIn('role', ['admin', 'agri']);
        });

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $query->having('users_who_liked_count', '>', 0);

        $posts = $query->orderByDesc('users_who_liked_count')
                    ->take(5)
                    ->get();

        return response()->json([
            'posts' => $posts->map(function ($post) {
                return [
                    'post_id' => $post->id,
                    'title' => $post->title,
                    'user_name' => $post->user->fname . ' ' . $post->user->lname,
                    'likes_count' => $post->users_who_liked_count,
                    'created_at' => $post->created_at,
                ];
            })
        ], 200);
    }

    public function topliked(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);

        $query = UserPost::withCount('usersWhoLiked')
            ->whereHas('usersWhoLiked') 
            ->whereHas('user', function ($q) {
                $q->whereNotIn('role', ['admin', 'agri']);
            });

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $posts = $query->orderByDesc('users_who_liked_count')->take(3)->get();

        UserPost::query()->update(['badge' => null]);

        foreach ($posts as $index => $post) {
            $user = $post->user;

            if ($user) {
                $postBadge = "Top Liked - Rank " . ($index + 1); 

                if ($post->badge !== $postBadge) {
                    $post->badge = $postBadge;
                    $post->save();
                }
            }
        }

        return response()->json([
            'posts' => $posts->map(function ($post) {
                return [
                    'post_id' => $post->id,
                    'title' => $post->title,
                    'user_name' => $post->user->fname . ' ' . $post->user->lname,
                    'likes_count' => $post->users_who_liked_count,
                    'created_at' => $post->created_at,
                    'badge' => $post->badge,
                ];
            })
        ], 200);
    }

    // Function to fetch category data for the pie chart
    public function mostCategories(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $categories = [
            "Compost", "Plastic", "Rubber", "Wood and Paper", "Glass", "Boxes", "Mixed Waste", "Cloth", "Issues", "Miscellaneous Products", "Tips & tricks"
        ];

        // Get start and end date from the request, default to current month if not provided
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()));
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->endOfMonth()->toDateString()));

        $data = UserPost::where('status', 'approved')  // Filters for 'approved' posts
            ->whereIn('category', $categories)         // Filters posts by category
            ->whereBetween('created_at', [$startDate, $endDate])  // Filters posts within the given date range
            ->selectRaw('category, COUNT(*) as count')  // Groups and counts posts by category
            ->groupBy('category')  // Groups the posts by category
            ->orderByDesc('count')  // Orders categories by post count in descending order
            ->get();

        return response()->json($data);
    }


    // Function to fetch category data for the table
    public function tableCategories(Request $request)
{
    // Ensure the user is authenticated
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get start and end date from the request, default to current month if not provided
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

    // Validate date formats (YYYY-MM-DD)
    if (!Carbon::hasFormat($startDate, 'Y-m-d')) {
        return response()->json(['error' => 'Invalid start_date format. Use YYYY-MM-DD'], 400);
    }

    if (!Carbon::hasFormat($endDate, 'Y-m-d')) {
        return response()->json(['error' => 'Invalid end_date format. Use YYYY-MM-DD'], 400);
    }

    // Parse the dates using Carbon
    try {
        $startDate = Carbon::parse($startDate)->startOfDay(); // Ensure the start date includes all of the day
        $endDate = Carbon::parse($endDate)->endOfDay(); // Ensure the end date includes all of the day
    } catch (\Exception $e) {
        return response()->json(['error' => 'Invalid date format'], 400);
    }

    // Categories to track
    $categories = [
        "Compost", "Plastic", "Rubber", "Wood and Paper", "Glass", "Boxes", "Mixed Waste", "Cloth", "Issues", "Miscellaneous Products", "Tips & tricks"
    ];

    // Query the posts
    $data = UserPost::where('status', 'approved')
        ->whereIn('category', $categories)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('category', \DB::raw('count(*) as total'))
        ->groupBy('category')
        ->orderBy('category')
        ->get();

    // Return the data as a JSON response
    return response()->json($data);
}



}
