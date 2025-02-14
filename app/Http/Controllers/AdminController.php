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
        
            if ($request->has('category') && in_array($request->category, ['Compost', 'Plastic', 'Rubber', 'Wood and Paper', 'Miscellaneous Products'])) {
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
        // dd([
        //     'file_count' => count($request->file('images')),
        //     'files' => $request->file('images')
        // ]);
        
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240',
        ]);

        $imagesPaths = []; // Array to store file paths

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagesPaths[] = $image->store('landingphotos', 'public'); // Store each image
            }
        }

        $landing_photos = LandingPhotos::create([
            'images' => json_encode($imagesPaths) // Store as JSON array
        ]);

        return response()->json($landing_photos, 201);
    }

    public function showallphotos()
    {
        $landingPhotos = LandingPhotos::orderBy('created_at', 'desc')->get();
        // Fetch all landing photos
        // $landingPhotos = LandingPhotos::all();

        // Decode the images JSON array and generate full URLs for each image
        foreach ($landingPhotos as $photo) {
            // Decode the images JSON array
            $images = json_decode($photo->images, true);

            // Generate full URLs for each image
            if (is_array($images)) {
                $photo->images = array_map(fn($img) => asset('storage/' . $img), $images);
            } else {
                $photo->images = [];
            }
        }

        // Return the data as a JSON response
        return response()->json($landingPhotos);
    }

    public function showphotos(Request $request)
    {
        $landingPhotos = LandingPhotos::orderBy('created_at', 'desc')->get();

        foreach ($landingPhotos as $photo) {
            // Decode the images JSON array
            $images = json_decode($photo->images, true);

            // Generate full URLs for each image
            if (is_array($images)) {
                $photo->images = array_map(fn($img) => asset('storage/' . $img), $images);
            } else {
                $photo->images = [];
            }
        }

        return response()->json($landingPhotos, 200);
    }

    public function deletephoto($id)
    {
        $landingPhoto = LandingPhotos::find($id);

        if (!$landingPhoto) {
            return response()->json(['message' => 'Photo not found.'], 404);
        }

        $images = json_decode($landingPhoto->images, true);

        // Delete the images from storage
        foreach ($images as $image) {
            Storage::disk('public')->delete($image);
        }

        // Delete the record from the database
        $landingPhoto->delete();

        return response()->json(['message' => 'Image deleted successfully.'], 200);
    }

    public function deleteAllPhotos(Request $request)
    {
        $ids = $request->input('ids');  // Get ids from request body

        // Check if ids are provided
        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }

        // Delete the selected photos based on the IDs (excluding the latest one)
        foreach ($ids as $id) {
            $photo = LandingPhotos::find($id);

            if ($photo) {
                $images = json_decode($photo->images, true);

                // Delete the images from storage
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }

                // Delete the record from the database
                $photo->delete();
            }
        }

        return response()->json(['message' => 'Selected images deleted successfully.'], 200);
    }


}
