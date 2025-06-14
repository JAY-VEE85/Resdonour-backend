<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\User;
use App\Models\LandingPhotos;
use App\Models\BarangayPost;
use App\Models\ActivityLog;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // for dashboard tab
    public function dashboardStatistics(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'No authenticated user found.'
            ], 401);
        }

        if (!in_array($user->role, ['admin', 'agri', 'sangukab'])) {
            return response()->json([
                'message' => 'Access denied. Admins only. wag papansin'
            ], 403);
        }

        $totalPosts = UserPost::count();
        $totalReported = UserPost::where('status', 'reported')->count();
        $totalBarangayPosts = BarangayPost::count();
        $userCount = User::count();

        return response()->json([
            'total_user_posts' => $totalPosts,
            'total_reported_posts' => $totalReported,
            'total_barangay_posts' => $totalBarangayPosts,
            'total_users' => $userCount,
        ]);
    }

    // for all post tab
    public function allPost(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agri' , 'sangukab'])) {
            return response()->json([
                'message' => 'Access denied. Admins and Agri users only.'
            ], 403);
        }

        $posts = UserPost::with('user')->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'created_at' => $post->created_at,
                'category' => $post->category,
                'materials' => $post->materials,
                'title' => $post->title,
                'status' => $post->status,
                'fname' => $post->user->fname ?? 'N/A',
                'lname' => $post->user->lname ?? 'N/A',
            ];
        });

        return response()->json($posts);
    }

    // admin view post
    public function getPost($postId)
    {
        $user = auth()->user();
        $userId = $user->id;

        if (!in_array($user->role, ['admin', 'agri', 'sangukab'])) {
            return response()->json([
                'message' => 'Access denied. Admins and Agri users only.'
            ], 403);
        }

        $post = UserPost::withTrashed()->with('user')->find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->status === 'removed' && $post->user_id !== $userId) {
            return response()->json(['message' => 'You are not authorized to view this post'], 403);
        }

        return response()->json([
            'id' => $post->id,
            'user_id' => $post->user_id,
            'user_name' => $post->user ? $post->user->fname . ' ' . $post->user->lname : 'Unknown',
            'title' => $post->title,
            'content' => $post->content,
            'category' => $post->category,
            'materials' => $post->materials,
            'image' => $post->image ? asset('storage/' . $post->image) : null,
            'image_type' => $post->image ? (preg_match('/\.(mp4|mov|avi|wmv|flv)$/i', $post->image) ? 'video' : 'image') : null,
            'status' => $post->status,
            'remarks' => $post->remarks,
            'report_count' => $post->report_count,
            'report_reasons' => $post->report_reasons,
            'report_remarks' => $post->report_remarks,
            'total_likes' => $post->total_likes,
            'liked_by_user' => $post->likes()->where('user_id', $userId)->exists(),
            'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            'deleted_at' => $post->deleted_at ? $post->deleted_at->format('Y-m-d H:i:s') : null,
        ]);
    }

    // admin and environmental admin remove user posts
    public function softDeletePost(Request $request, $id)
    {
        try {
            $user = auth()->user();

            if (!$user || !in_array($user->role, ['admin', 'agri', 'sangukab'])) {
                return response()->json(['error' => 'Unauthorized. Only admin or agri users can delete posts.'], 403);
            }

            $request->validate([
                'remarks' => 'required|string'
            ]);

            $post = UserPost::findOrFail($id);

            // soft delete the post with remarks
            $post->remarks = $request->remarks;
            $post->report_remarks = "Admin Removed the Post";
            $post->status = 'removed';
            $post->save();

            $post->delete();

            // Get author firstname manually
            $author = \App\Models\User::find($post->user_id);
            $postAuthorFirstname = $author ? $author->fname : 'Unknown User';

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => "Removed a post by $postAuthorFirstname from the Community Feed",
                'details' => json_encode(['post_id' => $post->id]),
            ]);

            return response()->json(['message' => 'Post soft deleted successfully.'], 200);
        } catch (\Exception $e) {
            \Log::error('Soft Delete Post Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // reported posts
    public function allReportedPosts(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['admin', 'agri', 'sangukab'])) {
            return response()->json([
                'message' => 'Access denied. Admins and Agri users only.'
            ], 403);
        }

        $posts = UserPost::with('user')
            ->where('status', 'reported')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'created_at' => $post->created_at,
                    'category' => $post->category,
                    'materials' => $post->materials,
                    'status' => $post->status,
                    'report_count' => $post->report_count,
                    'report_reasons' => $post->report_reasons,
                    'report_remarks' => $post->report_remarks,
                    'fname' => $post->user->fname ?? 'N/A',
                    'lname' => $post->user->lname ?? 'N/A',
                ];
            });

        return response()->json($posts);
    }

    // for landing page photos
    public function addphotos(Request $request)
    {   
        try {
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

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => "Customized the landing page photos",
                'details' => json_encode(['landing_photos_id' => $landing_photos->id]),
            ]);

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
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found.',
                    'data' => []
                ], 200);
            }

            $baseURL = url('storage/');

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
            $photos = \App\Models\LandingPhotos::orderBy('created_at', 'desc')->get();

            if ($photos->count() <= 1) {
                return response()->json([
                    'message' => 'No previous photos found.',
                    'data' => []
                ], 200);
            }

            $photos = $photos->skip(1)->values();

            $baseURL = url('storage/');

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
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found to edit.',
                    'data' => []
                ], 200);
            }

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

            for ($i = 1; $i <= 5; $i++) {
                $imageKey = 'image' . $i;
                $contentKey = 'content' . $i;

                if ($request->hasFile($imageKey)) {
                    if ($latestPhoto->$imageKey) {
                        \Storage::disk('public')->delete($latestPhoto->$imageKey);
                    }
                    $latestPhoto->$imageKey = $request->file($imageKey)->store('landing_photos', 'public');
                }

                if ($request->filled($contentKey)) {
                    $latestPhoto->$contentKey = $validated[$contentKey];
                }
            }

            $latestPhoto->save();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => "Edited the landing page photos",
                'details' => json_encode(['landing_photos_id' => $latestPhoto->id]),
            ]);

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
            $photo = \App\Models\LandingPhotos::find($id);

            if (!$photo) {
                return response()->json([
                    'message' => 'Photo entry not found.',
                    'data' => null
                ], 404);
            }

            for ($i = 1; $i <= 5; $i++) {
                $imageKey = 'image' . $i;
                if ($photo->$imageKey) {
                    \Storage::disk('public')->delete($photo->$imageKey);
                }
            }

            $photo->delete();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => "Deleted landing page photos",
                'details' => json_encode(['landing_photos_id' => $photo->id]),
            ]);

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
            $latestPhoto = \App\Models\LandingPhotos::latest()->first();

            if (!$latestPhoto) {
                return response()->json([
                    'message' => 'No photos found.',
                    'data' => null
                ], 404);
            }

            $previousPhotos = \App\Models\LandingPhotos::where('id', '!=', $latestPhoto->id)->get();

            if ($previousPhotos->isEmpty()) {
                return response()->json([
                    'message' => 'No previous photos to delete.',
                    'data' => null
                ], 404);
            }

            $deletedIds = [];
            
            foreach ($previousPhotos as $photo) {
                for ($i = 1; $i <= 5; $i++) {
                    $imageKey = 'image' . $i;
                    if ($photo->$imageKey) {
                        \Storage::disk('public')->delete($photo->$imageKey);
                    }
                }
                $deletedIds[] = $photo->id;
                $photo->delete();
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => "Deleted all previous landing page photos",
                'details' => json_encode(['deleted_photo_ids' => $deletedIds]),
            ]);

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
