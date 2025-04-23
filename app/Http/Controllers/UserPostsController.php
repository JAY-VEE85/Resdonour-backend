<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPost;
use App\Models\Announcement;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class UserPostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createPost(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,MOV,avi,mkv|max:51200',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:11000',
            'category' => 'required|string|max:100',
            'materials' => 'nullable|array',
            'materials.*' => 'string|max:255'
        ]);

        $filePath = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();

            // Store in different directories based on file type
            if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif'])) {
                $filePath = $file->store('images', 'public');
            } elseif (in_array($extension, ['mp4', 'mov', 'MOV', 'avi', 'mkv'])) {
                $filePath = $file->store('videos', 'public');
            }
        }

        $post = new UserPost();
        $post->user_id = auth()->id();
        $post->image = $filePath; // Now supports both images & videos
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->category = $request->input('category');
        $post->materials = $request->has('materials') ? json_encode($request->input('materials')) : json_encode([]);
        $post->status = 'posted';

        $post->save();

        return response()->json(['message' => 'Post created successfully', 'post' => $post]);
    }

    public function updatePost(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $post = UserPost::find($id);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        if ($post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,MOV,avi,mkv|max:51200',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'materials' => 'nullable|array',
            'materials.*' => 'string|max:255'
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            // Delete the old file if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();

            // Store based on file type
            if (in_array($extension, ['jpeg', 'png', 'jpg', 'gif'])) {
                $filePath = $file->store('images', 'public');
            } elseif (in_array($extension, ['mp4', 'mov', 'MOV', 'avi', 'mkv'])) {
                $filePath = $file->store('videos', 'public');
            }

            $post->image = $filePath;
        }

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->category = $request->input('category');
        $post->materials = $request->has('materials') ? json_encode($request->input('materials')) : json_encode([]);

        $post->save();

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    // get post by id
    public function getPost($postId)
    {
        $userId = auth()->id(); // Get the logged-in user ID

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
            'liked_by_user' => DB::table('post_likes')
                ->where('user_id', $userId)
                ->where('post_id', $post->id)
                ->exists(),
            'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            'deleted_at' => $post->deleted_at ? $post->deleted_at->format('Y-m-d H:i:s') : null,
            // 'permanent_deletion_date' => $post->deleted_at ? $post->deleted_at->addDays(7)->format('Y-m-d H:i:s') : null,
        ]);
    }

    // final get logged in user posts
    public function getUserPosts()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $posts = $user->posts()
            ->withTrashed() // Include soft-deleted posts
            ->get()
            ->map(function ($post) {
                $post->image = $post->image ? url('storage/' . $post->image) : null;
                $post->image_type = $post->image ? (preg_match('/\.(mp4|mov|avi|wmv|flv)$/i', $post->image) ? 'video' : 'image') : null;
                return $post;
            });

        return response()->json(['posts' => $posts], 200);
    }

    // GET ALL USERS POSTS - hide the reported post from user who reports
    public function getAllPosts()
    {
        $userId = auth()->id(); // Get the logged-in user ID
        $currentDate = now();

        $posts = UserPost::orderBy('created_at', 'desc')->get()->filter(function ($post) use ($userId, $currentDate) {
            $reportedByUsers = json_decode($post->reported_by_users, true) ?? [];

            if (isset($reportedByUsers[$userId])) {
                $reportDate = Carbon::createFromFormat('Y-m-d', $reportedByUsers[$userId]);
                if ($reportDate->diffInDays($currentDate) < 30) {
                    return false; // Hide post if reported within the last 30 days
                }
            }

            return true;
        })->map(function ($post) use ($userId) {
            return [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'fname' => $post->user->fname ?? 'N/A',
                'lname' => $post->user->lname ?? 'N/A',
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category,
                'image' => $post->image ? asset('storage/' . $post->image) : null, // Convert image/video to full URL
                'image_type' => $post->image ? (preg_match('/\.(mp4|mov|avi|wmv|flv)$/i', $post->image) ? 'video' : 'image') : null, // Detect file type
                'status' => $post->status,
                'total_likes' => $post->total_likes,
                'liked_by_user' => DB::table('post_likes')
                    ->where('user_id', $userId)
                    ->where('post_id', $post->id)
                    ->exists(), // Check if the user liked the post
                'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json(array_values($posts->toArray()));

    }

    public function deletePost($id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the post, including soft-deleted ones
        $post = UserPost::withTrashed()->find($id);

        if (!$post || ($post->user_id !== $user->id && $user->role !== 'admin' && $user->role !== 'agri' && $user->role !== 'sangukab')) {
            return response()->json(['error' => 'Post not found or unauthorized access'], 404);
        }

        // Delete the post image if it exists
        if ($post->image && file_exists(storage_path('app/public/' . $post->image))) {
            unlink(storage_path('app/public/' . $post->image)); 
        }

        // **Force delete the post permanently**
        $post->forceDelete();

        return response()->json(['message' => 'Post permanently deleted'], 200);
    }

    // toggle like post
    public function toggleLike(Request $request, $postId)
    {
        $user = auth()->user();
        $post = UserPost::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        // Check if the user has already liked the post
        $liked = DB::table('post_likes')
            ->where('user_id', $user->id)
            ->where('post_id', $postId)
            ->exists();

        if ($liked) {
            // Unlike the post
            DB::table('post_likes')
                ->where('user_id', $user->id)
                ->where('post_id', $postId)
                ->delete();
        } else {
            // Like the post
            DB::table('post_likes')->insert([
                'user_id' => $user->id,
                'post_id' => $postId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $post->total_likes = DB::table('post_likes')->where('post_id', $postId)->count();
        $post->save();

        return response()->json(['total_likes' => $post->total_likes]);
    }

    // get total likes
    public function getTotalLikes($postId)
    {
        $post = UserPost::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json(['total_likes' => $post->total_likes]);
    }

    public function getLikedPosts()
    {
        $userId = Auth::id(); // Get logged-in user ID

        $likedPosts = \DB::table('post_likes')
            ->join('user_posts', 'post_likes.post_id', '=', 'user_posts.id')
            ->join('users', 'user_posts.user_id', '=', 'users.id')
            ->where('post_likes.user_id', $userId)
            ->orderBy('post_likes.created_at', 'desc') // Sort by liked time
            ->select(
                'user_posts.id',
                'user_posts.title',
                'user_posts.category',
                'user_posts.content',
                'user_posts.total_likes',
                'user_posts.created_at',
                'post_likes.created_at as liked_at',
                DB::raw("CONCAT(users.fname, ' ', users.lname) as user_name"),
                'user_posts.image'
            )
            ->get()
            ->map(function ($post) {
                $post->image = $post->image ? asset('storage/' . $post->image) : null;
                $post->image_type = $post->image 
                    ? (preg_match('/\.(mp4|mov|avi|wmv|flv)$/i', $post->image) ? 'video' : 'image') 
                    : null;

                return $post;
            });

        return response()->json([
            'liked_posts' => $likedPosts
        ]);
    }

    // posts reporting
    // public function reportPost(Request $request, $postId)
    // {
    //     $user = auth()->user();
    //     $post = UserPost::find($postId);

    //     if (!$post) {
    //         return response()->json(['error' => 'Post not found'], 404);
    //     }

    //     $request->validate([
    //         'reasons' => 'required|array|min:1',
    //         'reasons.*' => 'string|max:255'
    //     ]);

    //     $existingReasons = json_decode($post->report_reasons, true) ?? [];
    //     $post->report_reasons = json_encode(array_merge($existingReasons, $request->input('reasons')));
    //     $post->report_count += 1;
    //     $post->status = 'reported';

    //     $reportedByUsers = json_decode($post->reported_by_users, true) ?? [];
    //     $reportedByUsers[$user->id] = now()->format('Y-m-d'); // Store the date of report
    //     $post->reported_by_users = json_encode($reportedByUsers);

    //     $post->save();

    //     return response()->json([
    //         'message' => 'Post reported successfully',
    //         'report_count' => $post->report_count,
    //         'status' => $post->status
    //     ]);
    // }

    // final reporting post
    public function reportPost(Request $request, $postId)
    {
        $user = auth()->user();
        $post = UserPost::find($postId);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $request->validate([
            'reasons' => 'required|array|min:1',
            'reasons.*' => 'string|max:255'
        ]);

        $existingReasons = json_decode($post->report_reasons, true) ?? [];
        $post->report_reasons = json_encode(array_merge($existingReasons, $request->input('reasons')));

        $post->report_count += 1;
        $post->status = 'reported'; // change status to "reported"

        // update report_remarks based on report count
        if ($post->report_count >= 1 && $post->report_count <= 4) {
            $post->report_remarks = "Pending Admin Review";
        } elseif ($post->report_count >= 5 && $post->report_count <= 9) {
            $post->report_remarks = "Pending Admin Removal";
        } elseif ($post->report_count == 10) {
            $post->report_remarks = "Post Removed";
            $post->remarks = "Post removed due to excessive reports.";
            $post->status = "removed"; // change status to "removed"
            $post->delete(); // soft delete the post if reported 10 times
        }

        // store reported user details
        $reportedByUsers = json_decode($post->reported_by_users, true) ?? [];
        $reportedByUsers[$user->id] = now()->format('Y-m-d'); // date of report
        $post->reported_by_users = json_encode($reportedByUsers);

        $post->save();

        return response()->json([
            'message' => 'Post reported successfully',
            'report_count' => $post->report_count,
            'report_remarks' => $post->report_remarks,
            'status' => $post->status
        ]);
    }

    // view all announcements in community feed tab
    public function getAnnouncements(Request $request)
    {
        $announcements = Announcement::all();

        return response()->json(['announcements' => $announcements], 200);
    }
}
