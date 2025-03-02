<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;
use App\Models\LandingPhotos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function totalUsers(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only. wag papansin'
                ], 403);
            }

            $userCount = User::count();

            return response()->json([
                'Total users of resit app' => $userCount
            ], 200);
        }

    public function totalPosts(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $totalPosts = UserPost::count();

            return response()->json([
                'Total posts of resit' => $totalPosts
            ], 200);
        }

    public function allPost(Request $request)
        {
            $user = auth()->user();
        
            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        
            $query = UserPost::with(['user:id,fname,lname,role']);
        
            if ($request->has('start_date') && $request->has('end_date')) {
                try {
                    $start_date = Carbon::createFromFormat('m-d-y', $request->start_date)->startOfDay();
                    $end_date = Carbon::createFromFormat('m-d-y', $request->end_date)->endOfDay();
        
                    $query->whereBetween('created_at', [$start_date, $end_date]);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Invalid date format. Please use MM-DD-YY.',
                        'error' => $e->getMessage()
                    ], 400);
                }
            }
        
            if ($request->has('category') && in_array($request->category, ['Compost', 'Plastic', 'Rubber', 'Wood', 'Miscellaneous Products'])) {
                $query->where('category', $request->category);
            }
        
            $posts = $query->get()->map(function ($post) use ($user) {
                if (in_array($post->user->role, ['admin', 'agri'])) {
                    return null;
                }
        
                $post->liked_by_user = $post->usersWhoLiked->contains('id', $user->id);
                $post->fname = $post->user->fname ?? null;
                $post->lname = $post->user->lname ?? null;
                unset($post->user);
        
                $post->user_name = ($post->user->fname ?? '') . ' ' . ($post->user->lname ?? '');
        
                return $post;
            })->filter();
        
            return response()->json(['posts' => $posts], 200);
        }
        
    
    public function getPost($id, Request $request)
        {
            $user = auth()->user();
        
            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        
            $post = UserPost::with(['user:id,fname,lname,role'])
                            ->find($id);
        
            if (!$post) {
                return response()->json([
                    'message' => 'Post not found.'
                ], 404);
            }
        
            // if (in_array($post->user->role, ['admin', 'agri'])) {
            //     return response()->json([
            //         'message' => 'Access denied. Admins or Agri posts are not available for view.'
            //     ], 403);
            // }
        
            $post->liked_by_user = $post->usersWhoLiked->contains('id', $user->id);
            $post->fname = $post->user->fname ?? null;
            $post->lname = $post->user->lname ?? null;
            unset($post->user);
        
            $post->user_name = ($post->user->fname ?? '') . ' ' . ($post->user->lname ?? '');

            $post->image = asset('storage/' . $post->image);
        
            return response()->json(['post' => $post], 200);
        }
        

    public function deletePost($id)
        {
            $user = auth()->user();
    
            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }
        
            $post = UserPost::find($id);
        
            if (!$post) {
                return response()->json([
                    'message' => 'Post not found.'
                ], 404);
            }
        
            try {
                $post->delete();
        
                return response()->json([
                    'message' => 'Post deleted successfully.'
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to delete the post.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
    public function approvePost($id)
        {
            $post = UserPost::findOrFail($id);

            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'agri') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $post->status = 'approved';
            $post->save();

            return response()->json(['message' => 'Post approved successfully']);
        }

        public function declinePost(Request $request, $id)
{
    // Validate 'remarks' field
    $request->validate([
        'remarks' => 'required|string|max:255',
    ]);

    // Find the post by ID
    $post = UserPost::findOrFail($id);

    // Check user role for authorization
    if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'agri') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Update the post with new status and remarks
    $post->update([
        'status' => 'declined',
        'remarks' => $request->remarks,
    ]);

    return response()->json(['message' => 'Post declined successfully']);
}

        
    // pending count dito

    public function totalPendings(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'pending')->count();

            return response()->json([
                'Pending posts of resit' => $pendingCount
            ], 200);
        }

    public function totalApproved(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'approved')->count();

            return response()->json([
                'Approved posts of resit' => $pendingCount
            ], 200);
        }

    public function totalDeclined(Request $request)
        {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'No authenticated user found.'
                ], 401);
            }

            if (!in_array($user->role, ['admin', 'agri'])) {
                return response()->json([
                    'message' => 'Access denied. Admins only.'
                ], 403);
            }

            $pendingCount = UserPost::where('status', 'declined')->count();

            return response()->json([
                'Declined posts of resit' => $pendingCount
            ], 200);
        }

    // public function awardBadge(Request $request, $id)
    //     {
    //         if (!in_array(auth()->user()->role, ['admin', 'agri'])) {
    //             return response()->json(['message' => 'Unauthorized. Only admin and agri-admin can award badges.'], 403);
    //         }
        
    //         $user = User::findOrFail($id);
        
    //         $badge = $request->input('badge');
        
    //         $user->awardBadge($badge);
        
    //         return response()->json(['message' => 'Badge awarded successfully!', 'badges' => json_decode($user->badges)]);
    //     }
    
    // public function removeBadge(Request $request, $id)
    //     {
    //         if (!in_array(auth()->user()->role, ['admin', 'agri'])) {
    //             return response()->json(['message' => 'Unauthorized. Only admin and agri-admin can remove badges.'], 403);
    //         }

    //         $user = User::findOrFail($id);

    //         $badge = $request->input('badge');

    //         $currentBadges = json_decode($user->badges, true) ?? [];
    //         if (in_array($badge, $currentBadges)) {
    //             $currentBadges = array_filter($currentBadges, fn($b) => $b !== $badge);
    //             $user->badges = json_encode(array_values($currentBadges));
    //             $user->save();
    //         }

    //         return response()->json([
    //             'message' => 'Badge removed successfully!',
    //             'badges' => json_decode($user->badges)
    //         ]);
    //     }

    public function topliked(Request $request)
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
                    ->take(10)
                    ->get();

        // Automatically award "Top Liked" badge
        foreach ($posts as $post) {
            $user = $post->user;
            
            if ($user) {
                // Check if the user already has the "Top Liked" badge
                $badges = json_decode($user->badges, true) ?? [];
                if (!in_array('Top Liked', $badges)) {
                    $badges[] = 'Top Liked';
                    $user->badges = json_encode($badges);
                    $user->save();
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
                ];
            })
        ], 200);
    }

    // for landing page photos
    public function addphotos(Request $request)
    {   
        try {
            // Validate request
            $validated = $request->validate([
                'image1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content1' => 'nullable|string|max:255',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content2' => 'nullable|string|max:255',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content3' => 'nullable|string|max:255',
                'image4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content4' => 'nullable|string|max:255',
                'image5' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content5' => 'nullable|string|max:255'
            ]);

            // Upload images if present
            $images = [];
            for ($i = 1; $i <= 5; $i++) {
                $imageKey = 'image' . $i;
                if ($request->hasFile($imageKey)) {
                    $images[$imageKey] = $request->file($imageKey)->store('landing_photos', 'public');
                } else {
                    $images[$imageKey] = null;
                }
            }

            // Store data in database
            $landing_photos = \App\Models\LandingPhotos::create([
                'image1' => $images['image1'] ?? null,
                'content1' => $validated['content1'] ?? null,
                'image2' => $images['image2'] ?? null,
                'content2' => $validated['content2'] ?? null,
                'image3' => $images['image3'] ?? null,
                'content3' => $validated['content3'] ?? null,
                'image4' => $images['image4'] ?? null,
                'content4' => $validated['content4'] ?? null,
                'image5' => $images['image5'] ?? null,
                'content5' => $validated['content5'] ?? null,
            ]);

            // Return JSON response
            return response()->json([
                'message' => 'Photos uploaded successfully!',
                'data' => $landing_photos
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showlatestphoto()
    {
        try {
            // Retrieve the latest photo entry
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            // Check if there is any photo entry
            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found.',
                    'data' => []
                ], 200);
            }

            // Define the base URL for storage images
            $baseURL = url('storage/');

            // Return JSON response with full image URLs
            return response()->json([
                'message' => 'Latest photo retrieved successfully!',
                'data' => [
                    'id' => $latestPhoto->id,
                    'image1' => $latestPhoto->image1 ? $baseURL . '/' . $latestPhoto->image1 : null,
                    'content1' => $latestPhoto->content1 ?? null,
                    'image2' => $latestPhoto->image2 ? $baseURL . '/' . $latestPhoto->image2 : null,
                    'content2' => $latestPhoto->content2 ?? null,
                    'image3' => $latestPhoto->image3 ? $baseURL . '/' . $latestPhoto->image3 : null,
                    'content3' => $latestPhoto->content3 ?? null,
                    'image4' => $latestPhoto->image4 ? $baseURL . '/' . $latestPhoto->image4 : null,
                    'content4' => $latestPhoto->content4 ?? null,
                    'image5' => $latestPhoto->image5 ? $baseURL . '/' . $latestPhoto->image5 : null,
                    'content5' => $latestPhoto->content5 ?? null,
                    'created_at' => $latestPhoto->created_at
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showallphotos()
    {
        try {
            // Retrieve all photo entries sorted by latest first
            $photos = \App\Models\LandingPhotos::orderBy('created_at', 'desc')->get();

            // If no previous photos exist, return an empty array (not a 404 error)
            if ($photos->count() <= 1) {
                return response()->json([
                    'message' => 'No previous photos found.',
                    'data' => [] // Return empty data with 200 status
                ], 200);
            }

            // Remove the latest entry
            $photos = $photos->skip(1)->values();

            // Define the base URL for storage images
            $baseURL = url('storage/');

            // Format the response data
            $formattedPhotos = $photos->map(function ($photo) use ($baseURL) {
                return [
                    'id' => $photo->id,
                    'image1' => $photo->image1 ? $baseURL . '/' . $photo->image1 : null,
                    'content1' => $photo->content1 ?? null,
                    'image2' => $photo->image2 ? $baseURL . '/' . $photo->image2 : null,
                    'content2' => $photo->content2 ?? null,
                    'image3' => $photo->image3 ? $baseURL . '/' . $photo->image3 : null,
                    'content3' => $photo->content3 ?? null,
                    'image4' => $photo->image4 ? $baseURL . '/' . $photo->image4 : null,
                    'content4' => $photo->content4 ?? null,
                    'image5' => $photo->image5 ? $baseURL . '/' . $photo->image5 : null,
                    'content5' => $photo->content5 ?? null,
                    'created_at' => $photo->created_at
                ];
            });

            // Return JSON response
            return response()->json([
                'message' => 'All photos except the latest retrieved successfully!',
                'data' => $formattedPhotos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function editLatestPhoto(Request $request)
    {
        try {
            // Retrieve the latest photo entry
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            // Check if there is any photo entry
            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found to edit.',
                    'data' => []
                ], 200);
            }

            // Validate request
            $validated = $request->validate([
                'image1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content1' => 'nullable|string|max:255',
                'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content2' => 'nullable|string|max:255',
                'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content3' => 'nullable|string|max:255',
                'image4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content4' => 'nullable|string|max:255',
                'image5' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'content5' => 'nullable|string|max:255'
            ]);

            // Upload new images if present and replace the existing ones
            for ($i = 1; $i <= 5; $i++) {
                $imageKey = 'image' . $i;
                $contentKey = 'content' . $i;

                if ($request->hasFile($imageKey)) {
                    // Delete the old image if exists
                    if ($latestPhoto->$imageKey) {
                        \Storage::disk('public')->delete($latestPhoto->$imageKey);
                    }
                    // Store the new image
                    $latestPhoto->$imageKey = $request->file($imageKey)->store('landing_photos', 'public');
                }

                // Update content if provided
                if ($request->filled($contentKey)) {
                    $latestPhoto->$contentKey = $validated[$contentKey];
                }
            }

            // Save the updated entry
            $latestPhoto->save();

            return response()->json([
                'message' => 'Latest photo updated successfully!',
                'data' => $latestPhoto
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deletephoto($id)
    {
        try {
            // Find the photo entry by ID
            $photo = \App\Models\LandingPhotos::find($id);

            if (!$photo) {
                return response()->json([
                    'message' => 'Photo entry not found.',
                    'data' => null
                ], 404);
            }

            // Delete images from storage if they exist
            for ($i = 1; $i <= 5; $i++) {
                $imageKey = 'image' . $i;
                if ($photo->$imageKey) {
                    \Storage::disk('public')->delete($photo->$imageKey);
                }
            }

            // Delete the entry from the database
            $photo->delete();

            return response()->json([
                'message' => 'Highlight deleted successfully!',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAllPhotos()
    {
        try {
            // Get the latest uploaded entry
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found.',
                    'data' => null
                ], 404);
            }

            // Get all previous entries excluding the latest
            $previousPhotos = \App\Models\LandingPhotos::where('id', '!=', $latestPhoto->id)->get();

            if ($previousPhotos->isEmpty()) {
                return response()->json([
                    'message' => 'No previous photos to delete.',
                    'data' => null
                ], 404);
            }

            // Loop through and delete images from storage
            foreach ($previousPhotos as $photo) {
                for ($i = 1; $i <= 5; $i++) {
                    $imageKey = 'image' . $i;
                    if ($photo->$imageKey) {
                        \Storage::disk('public')->delete($photo->$imageKey);
                    }
                }
                // Delete the database entry
                $photo->delete();
            }

            return response()->json([
                'message' => 'All previous photo entries deleted successfully!',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
